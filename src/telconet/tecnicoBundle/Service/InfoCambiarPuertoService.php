<?php

namespace telconet\tecnicoBundle\Service;

use Doctrine\ORM\EntityManager;
use telconet\soporteBundle\Service\EnvioPlantillaService;
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
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
use telconet\schemaBundle\Entity\InfoRutaElemento;
use telconet\planificacionBundle\Service\RecursosDeRedService;
use telconet\schemaBundle\Service\UtilService;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class InfoCambiarPuertoService {

    private $emComercial;
    private $emInfraestructura;
    private $emSoporte;
    private $emComunicacion;
    private $emSeguridad;
    private $emGeneral;
    private $servicioGeneral;
    private $activar;
    private $cancelar;
    private $container;
    private $recursosRed;
    private $utilServicio;
    private $migracionHuawei;
    private $rdaBandEjecuta;
    private $rdaTipoEjecucion;
    private $networkingScripts;
    private $serviceUtilidades;
    private $rdaMiddleware;
    private $opcion = "CAMBIAR_PUERTO";
    private $ejecutaComando;
    private $strConfirmacionTNMiddleware;
    private $servicePromociones;
    private $servicioInfoServicio;
    //Variables staticas
    private static $strWsAppName          = 'APP.TELCOGRAPH';
    private static $strWsServiceCloudform = 'TecnicoWSController';
    private static $strWsGatewayCloudForm = 'Telcos';
    
    public function setDependencies(Container $objContainerParam)
    {
        $this->container          = $objContainerParam;
        $this->emSoporte          = $this->container->get('doctrine')->getManager('telconet_soporte');
        $this->emInfraestructura  = $this->container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emSeguridad        = $this->container->get('doctrine')->getManager('telconet_seguridad');
        $this->emComercial        = $this->container->get('doctrine')->getManager('telconet');
        $this->emComunicacion     = $this->container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emNaf              = $this->container->get('doctrine')->getManager('telconet_naf');
        $this->emGeneral          = $this->container->get('doctrine')->getManager('telconet_general');
        $this->correo             = $this->container->get('soporte.EnvioPlantilla');
        $this->serviceSoporte     = $this->container->get('soporte.soporteservice');
        $this->servicioGeneral    = $this->container->get('tecnico.InfoServicioTecnico');
        $this->activar            = $this->container->get('tecnico.InfoActivarPuerto');
        $this->cancelar           = $this->container->get('tecnico.InfoCancelarServicio');
        $this->migracionHuawei    = $this->container->get('tecnico.MigracionHuawei');
        $this->networkingScripts  = $this->container->get('tecnico.NetworkingScripts');
        $this->recursosRed        = $this->container->get('planificacion.RecursosDeRed');
        $this->utilServicio       = $this->container->get('schema.Util');
        $this->rdaMiddleware      = $this->container->get('tecnico.RedAccesoMiddleware');
        $this->serviceUtilidades  = $this->container->get('administracion.Utilidades');
        $this->servicePromociones = $this->container->get('tecnico.Promociones');
        $this->host               = $this->container->getParameter('host');
        $this->ejecutaComando     = $this->container->getParameter('ws_rda_ejecuta_scripts');
        $this->servicioInfoServicio = $this->container->get('comercial.InfoServicio');
        $this->rdaTipoEjecucion     = $this->container->getParameter('ws_rda_tipo_ejecucion');
        $this->rdaBandEjecuta       = $this->container->getParameter('ws_rda_band_ejecuta');
        $this->strConfirmacionTNMiddleware = $this->container->getParameter('ws_rda_opcion_confirmacion_middleware');
    }

    /**
     * cambiarPuerto
     * 
     * Funcion encargada de realizar el cambio de puerto/ultima milla segun la empresa enviada como parametro
     * 
     * @author Allan Suarez <arsuarez@telconete.ec>
     * @since 1.0 07-04-2016
     * @version 1.1
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2  06-05-2016    Se agrega parametro empresa en metodo cambiarPuerto por conflictos de 
     *                             producto INTERNET DEDICADO
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3  11-05-2016    Se agrega parametro empresa en metodo cambiarPuerto por conflictos de 
     *                             producto INTERNET DEDICADO
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.4  28-11-2018    Se agregan validaciones para gestionar los productos de la empresa TNP
     * @since 1.3
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 13-05-2021 Se modifica el envío de parámetros a la función cambiarPuertoMd
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.6 20-03-2023 Se Agrega bandera de prefijo empresa para que ingrese a realizar el cambio de puerto a la empresa Ecuanet.
     * 
     * @param Array $arrayPeticiones [ idEmpresa , prefijoEmpresa , idServicio , elementoId , interfaceElementoId , dslamId , elementoCajaId ,
     *                                 elementoConectorId , interfaceElementoConectorId , requiereScript , usrCreacion , ipCreacion ]
     * @return string $resultado
     */
    public function cambiarPuerto($arrayPeticiones)
    {
        $idEmpresa           = $arrayPeticiones['idEmpresa'];
        $prefijoEmpresa      = $arrayPeticiones['prefijoEmpresa'];
        $idServicio          = $arrayPeticiones['idServicio'];
        $elementoId          = $arrayPeticiones['elementoId'];
        $interfaceElementoId = $arrayPeticiones['interfaceElementoId'];
        $dslamId             = $arrayPeticiones['dslamId'];
        $elementoCajaId      = $arrayPeticiones['elementoCajaId'];
        $elementoConectorId  = $arrayPeticiones['elementoConectorId'];
        $interfaceElementoConectorId = $arrayPeticiones['interfaceElementoConectorId'];
        $usrCreacion         = $arrayPeticiones['usrCreacion'];
        $ipCreacion          = $arrayPeticiones['ipCreacion'];        
        $strIdEmpresaSesion  = $idEmpresa;
        $servicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
        
        $servicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                             ->findOneBy(array("servicioId" => $servicio->getId()));
        //migracion_ttco_md
        $arrayEmpresaMigra = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                               ->getEmpresaEquivalente($idServicio, $prefijoEmpresa);

        if($arrayEmpresaMigra)
        {
            if($arrayEmpresaMigra['prefijo'] == 'TTCO')
            {
                $idEmpresa      = $arrayEmpresaMigra['id'];
                $prefijoEmpresa = $arrayEmpresaMigra['prefijo'];
            }
        }
        if($prefijoEmpresa == "TTCO")
        {
            $resultado = $this->cambiarPuertoTtco( $servicio, 
                                                   $servicioTecnico, 
                                                   $dslamId, 
                                                   $interfaceElementoId, 
                                                   $usrCreacion, 
                                                   $ipCreacion, 
                                                   $strIdEmpresaSesion);
        }
        else if($prefijoEmpresa == "MD" || $prefijoEmpresa == "TNP" || $prefijoEmpresa == "EN")
        {
            $arrayResultadoCambioPuertoMd   = $this->cambiarPuertoMd(array( "objServicio"                   => $servicio,
                                                                            "objServicioTecnico"            => $servicioTecnico,
                                                                            "intElementoIdStNuevo"          => $elementoId,
                                                                            "intInterfaceElementoIdStNuevo" => $interfaceElementoId,
                                                                            "intContenedorIdNuevo"          => $elementoCajaId,
                                                                            "intConectorIdStNuevo"          => $elementoConectorId,
                                                                            "intInterfaceConectorIdStNuevo" => $interfaceElementoConectorId,
                                                                            "strUsrCreacion"                => $usrCreacion,
                                                                            "strIpCreacion"                 => $ipCreacion,
                                                                            "strCodEmpresa"                 => $idEmpresa));
            $strStatusCambioPuertoMd        = $arrayResultadoCambioPuertoMd["status"];
            $strMensajeCambioPuertoMd       = $arrayResultadoCambioPuertoMd["mensaje"];
            if($strStatusCambioPuertoMd === "OK")
            {
                $resultado = $strStatusCambioPuertoMd;
            }
            else
            {
                $resultado = $strMensajeCambioPuertoMd;
            }
        }
        else if($prefijoEmpresa == "TN")
        {            
            $resultado = $this->cambiarUltimaMillaTn($arrayPeticiones);            
        }

        return $resultado;
    }

    /**
     * Service que realiza el cambio de linea pon, con ejecucion de scripts
     * 
     * @author Creado: John Vera <javera@telconet.ec>
     * @version 1.0 20-05-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 11-05-2020 Se unifica las validaciones por marca y no por modelo de olt
     * 
     */
    public function cambiarPuertoScriptMd($arrayDatos)
    {
        $interfaceElementoNuevoId = $arrayDatos[0]['interfaceElementoId'];

        $interfaceElementoNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                          ->findOneById($interfaceElementoNuevoId);

        //obtengo el modelo del elemento nuevo
        $objElementoNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                    ->findOneById($interfaceElementoNuevo->getElementoId()->getId());

        $strMarcaOltNuevo   = $objElementoNuevo->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();
        if($strMarcaOltNuevo == "HUAWEI")
        {
            $arrayFinal = $this->cambiarPuertoScriptMdHuawei($arrayDatos);
        }
        else if($strMarcaOltNuevo == "TELLION")
        {
            $arrayFinal = $this->cambiarPuertoScriptMdTellion($arrayDatos);
        }
        else if($strMarcaOltNuevo == "ZTE")
        {
            $arrayFinal = $this->cambiarPuertoScriptMdZte($arrayDatos[0]);
        }
        else
        {
            $arrayFinal[] = array('status' => "ERROR", 'mensaje' => "La marca " . $strMarcaOltNuevo . " no está soportada.");
        }
        return $arrayFinal;
    }

    /**
     * Service que realiza el cambio de linea pon, con ejecucion de scripts
     * 
     * @author Creado:     John Vera         <javera@telconet.ec>
     * @author Modificado: Francisco Adum    <fdaum@telconet.ec>
     * @version 1.0 17-06-2014
     * @version 1.1 modificado:21-06-2014
     * @version 1.2 modificado:28-05-2015 javera
     * @version 1.3 modificado:08-07-2015 javera
     * @version 1.4 modificado:02-09-2015 javera
     * @version 1.5 modificado:09-05-2016 jbozada
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 04-06-2018 Se realizan validaciones para realizar cambios de línea pon de Servicios Small Business con o sin Ips adicionales
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.7 26-12-2018 Se agregan parámetros equipoOntDualBand y tipoOrden por cambio en envío al middleware al activar un servicio
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 25-02-2019 Se agrega cambio de línea pon para servicios TelcoHome con tecnología Tellion
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.9 04-09-2019 Se agrega proceso para validar promociones en servicios de clientes, en caso de 
     *                         que aplique a alguna promoción se le configurarán los anchos de bandas promocionales
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.10 04-05-2020 Se elimina la función obtenerInfoMapeoProdPrefYProdsAsociados y en su lugar se usa obtenerParametrosProductosTnGpon,
     *                          debido a los cambios realizados por la reestructuración de servicios Small Business
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.11 22-11-2020 Se agrega programación para ejecutar cambios de elementos de clientes pyme con IP FIJA WAN adicional
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.12 11-10-2021 Se agrega validación para los productos que tienen aprovisionamiento de ip privadas. 
     * 
     * @since 1.8
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.13 15-11-2021 Se construye el arreglo con la información que se enviará al invocar al web service para confirmación de 
     *                          opción de Tn a Middleware
     * 
     */
    public function cambiarPuertoScriptMdTellion($arrayDatos) {
        $servicioId                     = $arrayDatos[0]['idServicio'];
        $interfaceElementoNuevoId       = $arrayDatos[0]['interfaceElementoId']; 
        $empresa                        = $arrayDatos[0]['idEmpresa'];
        $elementoCajaId                 = $arrayDatos[0]['elementoCajaId'];
        $elementoSplitterId             = $arrayDatos[0]['elementoSplitterId'];
        $interfaceElementoSplitterId    = $arrayDatos[0]['interfaceElementoSplitterId'];
        $usrCreacion                    = $arrayDatos[0]['usrCreacion'];
        $ipCreacion                     = $arrayDatos[0]['ipCreacion'];
        $idSolicitud                    = $arrayDatos[0]['idSolicitud'];
        $strPrefijoEmpresa              = $arrayDatos[0]['prefijoEmpresa'];
        $strEsIsb                       = $arrayDatos[0]['esIsb'] ? $arrayDatos[0]['esIsb'] : "NO";
        $flagMiddleware                 = false;
        $strOntId                       = "";
        $intIdElementoOltNuevo          = 0;
        $flagProdViejo                  = 0;
        $strExisteIpWan                 = "NO";
        $arrayDatosIpWan                = array();
        $arrayProdIp                    = array();
        $objSession                     = $arrayDatos[0]['objSession'];
        $arrayDataConfirmacionTn        = array();
        
        $objSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                             ->findOneById($idSolicitud);

        $interfaceElementoNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                       ->findOneById($interfaceElementoNuevoId);
        $elementoNuevo          = $interfaceElementoNuevo->getElementoId();
        $intIdElementoOltNuevo  = $elementoNuevo->getId();
        $servicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                ->findOneById($servicioId);
        
        $planEdicionLimitada = 'NO';
        
        $servicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                ->findOneBy(array("servicioId" => $servicio->getId()));

        $interfaceElementoViejo = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                       ->find($servicioTecnico->getInterfaceElementoId());
        
        $objDetalleElemento = $this->emInfraestructura ->getRepository('schemaBundle:InfoDetalleElemento')
                                                       ->findOneBy(array('detalleNombre' => 'OLT MIGRADO CNR',
                                                                         'elementoId'    => $interfaceElementoViejo->getElementoId()->getId()));
        //backbone viejo
        $elementoViejo = $interfaceElementoViejo->getElementoId();
        $elementoContenedorViejo = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                        ->find($servicioTecnico->getElementoContenedorId());
        $interfaceElementoConectorViejo = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                               ->find($servicioTecnico->getInterfaceElementoConectorId());
        $elementoConectorViejo = $interfaceElementoConectorViejo->getElementoId();

        if($strEsIsb === "SI")
        {
            if(!is_object($servicio->getProductoId()))
            {
                $arrayRespuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe producto asociado al servicio');
                return $arrayRespuestaFinal;
            }
            $objProducto    = $servicio->getProductoId();
            
            $intIdProdPref              = $objProducto->getId();
            $strNombreTecnicoProdPref   = $objProducto->getNombreTecnico();            
            if($strNombreTecnicoProdPref === "TELCOHOME")
            {
                $flagProdViejo          = 0;
                $strTipoNegocio         = "HOME";
            }
            else
            {
                $flagProdViejo          = 1;
                $strTipoNegocio         = "PYME";
                $arrayParamsInfoProds   = array("strValor1ParamsProdsTnGpon"    => "PRODUCTOS_RELACIONADOS_INTERNET_IP",
                                                "strCodEmpresa"                 => $empresa,
                                                "intIdProductoInternet"         => $intIdProdPref);
                $arrayInfoMapeoProds    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->obtenerParametrosProductosTnGpon($arrayParamsInfoProds);
                if(isset($arrayInfoMapeoProds) && !empty($arrayInfoMapeoProds))
                {
                    foreach($arrayInfoMapeoProds as $arrayInfoProd)
                    {
                        $intIdProductoIp    = $arrayInfoProd["intIdProdIp"];
                        $objProdIPSB        = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProductoIp);
                        $arrayProdIp[]      = $objProdIPSB;
                    }
                }
                else
                {
                    $arrayRespuestaFinal[]  = array('status'    => 'ERROR', 
                                                    'mensaje'   => 'No se ha podido obtener el correcto mapeo del servicio con la ip respectiva');
                    return $arrayRespuestaFinal;
                }
            }
        }
        else
        {
            $objProducto    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                ->findOneBy(array("nombreTecnico" => "INTERNET",
                                                                  "empresaCod"    => $empresa,
                                                                  "estado"        => "Activo"));
            $arrayProdIp    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                ->findBy(array( "nombreTecnico"  => "IP",
                                                                "empresaCod"     => $empresa,
                                                                "estado"         => "Activo"));
            //OBTENER TIPO DE NEGOCIO
            $strTipoNegocio = $servicio->getPuntoId()->getTipoNegocioId()->getNombreTipoNegocio();

            $planCabNuevo = $this->emComercial->getRepository('schemaBundle:InfoPlanCab')
                                              ->find($servicio->getPlanId()->getId());
            //obtener caracteristica plan edicion limitada
            $caractEdicionLimitada  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array("descripcionCaracteristica"=>"EDICION LIMITADA",
                                                                          "estado"                   =>"Activo"));
            if(is_object($planCabNuevo))
            {
                $planCaractEdicionLimitada = $this->emComercial->getRepository('schemaBundle:InfoPlanCaracteristica')
                                                                ->findOneBy(array("planId"            => $planCabNuevo->getId(),
                                                                                  "caracteristicaId"  => $caractEdicionLimitada->getId(),
                                                                                  "estado"            => $planCabNuevo->getEstado()));
                if($planCaractEdicionLimitada)
                {
                    $planEdicionLimitada = $planCaractEdicionLimitada->getValor();
                }
            }
        }

        //obtengo el indice viejo del cliente
        $objIndiceClienteViejoSpc   = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $objProducto);        
        $objDetalleElementoMid  = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                        ->findOneBy(array(  "elementoId"   => $servicioTecnico->getElementoId(),
                                                            "detalleNombre"=> 'MIDDLEWARE',
                                                            "estado"       => 'Activo'));
        
        if($objDetalleElementoMid)
        {
            if($objDetalleElementoMid->getDetalleValor() == 'SI')
            {
                $flagMiddleware = true;
            }
        }

        $servProdCaractPerfil = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "PERFIL", $objProducto);
        if ($servProdCaractPerfil) 
        {
            $perfil = $servProdCaractPerfil->getValor();
            $arrayPerfil    = explode("_", $perfil);
            if($strEsIsb === "SI")
            {
                $perfil = $arrayPerfil[0]."_".$arrayPerfil[1]."_".$arrayPerfil[2];
            }
            else
            {
                $perfil = $arrayPerfil[0]."_".$arrayPerfil[1];
            }
        }
        else 
        {
            $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE PERFIL DEL SERVICIO,' . $servicio->getId());
            return $respuestaFinal;
        }

        //obtener mac ont
        $servProdCaractMacOnt = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC ONT", $objProducto);
        if ($servProdCaractMacOnt) 
        {
            $macOnt = $servProdCaractMacOnt->getValor();
        }
        else 
        {
            $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE MAC ONT DEL CLIENTE,' . $servicio->getId());
            return $respuestaFinal;
        }

        //obtener mac wifi
        $servProdCaracMacWifi = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC", $objProducto);
        if ($servProdCaracMacWifi) 
        {
            //cambiar formato de la mac
            $macWifi = $servProdCaracMacWifi->getValor();
        } 
        else 
        {
            //obtener mac wifi
            $servProdCaracMacWifi = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC WIFI", $objProducto);
          
            if (is_object($servProdCaracMacWifi))
            {
                //cambiar formato de la mac
                $macWifi = $servProdCaracMacWifi->getValor();
            } 
            else 
            {
                $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE MAC WIFI DEL CLIENTE,' . $servicio->getId());
                return $respuestaFinal;
            }
        }
        
        $servProdCaractIndiceCliente = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $objProducto);
        if(!$servProdCaractIndiceCliente)
        {
            $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe Caracteristica Indice, favor revisar!');
            return $respuestaFinal;
        }
        else
        {
            $strOntId = $servProdCaractIndiceCliente->getValor();
        }

        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/

        try {
            //verificar ip en el plan----------------------------------------------------------
            $planCabViejo  = $servicio->getPlanId();
            if(is_object($planCabViejo))
            {
                $planDetViejo = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                        ->findBy(array("planId" => $planCabViejo->getId()));
                for ($i = 0; $i < count($planDetViejo); $i++) 
                {
                    for ($j = 0; $j < count($arrayProdIp); $j++) 
                    {
                        if ($planDetViejo[$i]->getProductoId() == $arrayProdIp[$j]->getId()) 
                        {
                            $prodIpPlan = $arrayProdIp[$j];
                            $flagProdViejo  = 1;
                            break;
                        }
                    }
                }//for($i=0;$i<count($planDetViejo);$i++)
            }
            if ($strPrefijoEmpresa === "MD" && $strTipoNegocio === "PYME" && $flagProdViejo === 0)
            {
                $arrayParametrosIpWan = array('objPunto'       => $servicio->getPuntoId(),
                                              'strEmpresaCod'  => $empresa,
                                              'strUsrCreacion' => $usrCreacion,
                                              'strIpCreacion'  => $ipCreacion);
                $arrayDatosIpWan      = $this->servicioGeneral
                                             ->getIpFijaWan($arrayParametrosIpWan);
                if (isset($arrayDatosIpWan['strStatus']) && !empty($arrayDatosIpWan['strStatus']) && 
                    $arrayDatosIpWan['strStatus'] === 'OK' && isset($arrayDatosIpWan['strExisteIpWan']) &&
                    !empty($arrayDatosIpWan['strExisteIpWan']) &&  $arrayDatosIpWan['strExisteIpWan'] === 'SI')
                {
                    $strExisteIpWan = $arrayDatosIpWan['strExisteIpWan'];
                    $flagProdViejo  = 1;
                }
            }
            //----------------------------------------------------------------------------------
            //verificar si punto tiene ip adicional---------------------------------------------
            $serviciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                    ->findBy(array("puntoId" => $servicio->getPuntoId()->getId()));
            $x = 1;
            $contIpsFijas = 0;
            $arrayServicioIp[] = array("idServicio" => "");
            for ($i = 0; $i < count($serviciosPunto); $i++) {
                $servicioPunto = $serviciosPunto[$i];
                if (($servicioPunto->getEstado() == "Activo" || $servicioPunto->getEstado() == "In-Corte" ) &&
                     $servicioPunto->getId() != $servicio->getId()) 
                {
                    if ($servicioPunto->getPlanId()) {
                        $planCab = $this->emComercial->getRepository('schemaBundle:InfoPlanCab')
                                ->find($servicioPunto->getPlanId()->getId());
                        $planDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                ->findBy(array("planId" => $planCab->getId()));

                        for ($j = 0; $j < count($planDet); $j++) {
                            //contar las ip que estan en planes
                            foreach ($arrayProdIp as $productoIp) {
                                if ($productoIp->getId() == $planDet[$j]->getProductoId()) {
                                    $arrayServicioIp[] = array("idServicio" => $servicioPunto->getId());
                                    $contIpsFijas++;
                                }
                            }
                        }//for($j=0;$j<count($planDet);$j++)
                    }//if($servicioPunto->getPlanId())
                    else
                    {
                        $productoServicioPunto = $servicioPunto->getProductoId();
                        $arrayParametrosCaractIpWan = array( 'intIdProducto'         => $productoServicioPunto->getId(),
                                                             'strDescCaracteristica' => 'IP WAN',
                                                             'strEstado'             => 'Activo' );
                        $strValidaExisteIpWan = $this->serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaractIpWan);
                        if ($strValidaExisteIpWan === 'N')
                        {
                            //contar las ip que estan como productos
                            foreach ($arrayProdIp as $productoIp) {

                                if ($productoIp->getId() == $productoServicioPunto->getId()) {
                                    $arrayServicioIp[] = array("idServicio" => $servicioPunto->getId());
                                    $arrayServicioIpProducto[] = array("idServicio" => $servicioPunto->getId());
                                    $contIpsFijas++;
                                }
                            }
                        }
                    }//else
                }
            }//for($i=0; $i<count($serviciosPunto); $i++)

            //----------------------------------------------------------------------------------
            //solicitar las ips necesarias
            $totalIpPto = $contIpsFijas + $flagProdViejo;
            $flagIpsReservadas = 0;             
            
            //valido que plan 100 100 con CNR se ejecute como un plan sin ip
            if($planEdicionLimitada == "SI" && $objDetalleElemento)
            {
                $totalIpPto = 0;
            }                        
            
            if($flagMiddleware)
            {
                $arrayIpCancelar    = array();
                $arrayIpActivar     = array();
                $intIpsFijasActivas = 0;
                $strScopeNuevo      = '';
                $strIpFija          = '';
                $scope              = '';
                $strIpElementoNuevo = 0;
                $strInterfaceNuevo  = '';
                $spcScope           = null;
                
                //obtener la ip del olt anterior
                $objIpElementoViejo = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                            ->findOneBy(array("elementoId" => $elementoViejo->getId()));

                //OBTENER DATOS DEL CLIENTE (NOMBRES E IDENTIFICACION)
                $objPersona         = $servicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
                $strIdentificacion  = $objPersona->getIdentificacionCliente();
                $strNombreCliente   = $objPersona->__toString();
                
                //Si el producto es Internet Small Business y si la ip es publica, consultar si tiene producto adicional, caso contrario
                //agregar un servicio adicional (IP Small Business)
                $boolCrearServicio = false;
                $boolIsb      = false;
                if ($strPrefijoEmpresa === "TN")
                {
                    $arrayParametrosCaract    = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                     ->getOne("IP_PRIVADA_GPON_CARACTERISTICAS",
                                                              "COMERCIAL",
                                                              "",
                                                              "",
                                                              $servicio->getProductoId()->getDescripcionProducto(),
                                                              "",
                                                              "",
                                                              "",
                                                              "",
                                                              $empresa);
                    if(isset($arrayParametrosCaract['valor2']) && !empty($arrayParametrosCaract['valor2']))
                    {
                        $strCaractIsb = $arrayParametrosCaract['valor2'];
                        $boolIsb      = true;
                    }
                }
                                    
                if ($strNombreTecnicoProdPref === "INTERNET SMALL BUSINESS" 
                                                           && $strPrefijoEmpresa ==="TN"
                                                           && $boolIsb)
                {
                    $intIdServicioIp = $servicio->getId();
                    //Obtiene tipo de ip por el servicio
                    $objTipoIpOrigen = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                   ->findOneBy(array("servicioId"  =>  $intIdServicioIp,
                                                                                         "tipoIp"      =>  "FIJA",
                                                                                         "estado"      =>  "Activo"));
                    if (is_object($objTipoIpOrigen))
                    {
                        $strTipoIpOrigen = $objTipoIpOrigen->getTipoIp();
                    }
                                                
                    if ($strTipoIpOrigen === "FIJA")
                    {
                        $strTieneIps = "NO";
                            
                        $arrayProdIp                = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                ->findBy(array( "descripcionProducto" => "IP Small Business",
                                                                                "nombreTecnico"       => "IPSB", 
                                                                                "empresaCod"          => "10",
                                                                                "estado"              => "Activo"));
                        if(empty($arrayProdIp))
                        {
                            throw new \Exception("No existe el objeto del producto IP");
                        }
                        
                        //arreglo de los estados de los servicios permitidos
                        $arrayEstadosServiciosPermitidos = array();
                        //obtengo la cabecera de los estados de los servicios permitidos
                        $objAdmiParametroCabEstadosServ  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                                    array('nombreParametro' => 'ESTADOS_SERVICIOS_ISB_CAMBIO_PUERTO',
                                                                          'estado'          => 'Activo'));
                        if( is_object($objAdmiParametroCabEstadosServ) )
                        {
                            $arrayParametrosDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                                                    array(  "parametroId" => $objAdmiParametroCabEstadosServ->getId(),
                                                                            "estado"      => "Activo"));
                            foreach($arrayParametrosDet as $objParametro)
                            {
                                $arrayEstadosServiciosPermitidos[] = $objParametro->getValor1();
                            }
                        }
                            
                        $objProductoOrigen          = $servicio->getProductoId();
                        $arrayServiciosPuntoOrigen  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                ->findBy(array( "puntoId"=> $servicio->getPuntoId()->getId(), 
                                                                                "estado" => $arrayEstadosServiciosPermitidos));
                        
                        //Consultamos si tiene ips adicionales el servicio de origen
                        $arrayParametrosIsb = array("arrayServicios"                  => $arrayServiciosPuntoOrigen,
                                                    "arrayProdIp"                     => $arrayProdIp,
                                                    "servicio"                        => $objTipoIpOrigen,
                                                    "objProductoInternet"             => $objProductoOrigen,
                                                    "estadoIp"                        => 'Activo',
                                                    "arrayEstadosServiciosPermitidos" => $arrayEstadosServiciosPermitidos
                                                    );
                            
                        //Consultamos si tiene ips adicionales el servicio de origen
                        $arrayDatosIpPyme   = $this->servicioGeneral->getInfoIpsFijaPuntoIsb($arrayParametrosIsb);
                        //Obtener la cantidad de ips adicionales
                        $intIpsFijasActivasPyme = $arrayDatosIpPyme['ip_fijas_activas'];
                        if($intIpsFijasActivasPyme > 0)
                        {
                            $strTieneIps = "SI";
                        }
                            
                        if ($strTieneIps === "NO")
                        {
                            $objProductoIp = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                            ->findOneBy(array(  "descripcionProducto"   => "IP Small Business",
                                                                                "empresaCod"            => $empresa,
                                                                                "estado"                => "Activo"));
                            $intIdProdIp             = $objProductoIp->getId();
                            $strDescripcionProdIp    = $objProductoIp->getDescripcionProducto();
                            $strLoginVendedor        = $servicio->getUsrVendedor();
                                
                            $objInfoPersona          = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                                ->findOneBy(array('login'=>$strLoginVendedor));
                            $strVendedor             = "";

                            if(is_object($objInfoPersona))
                            {
                                $strNombres   = ucwords(strtolower($objInfoPersona->getNombres()));
                                $strApellidos = ucwords(strtolower($objInfoPersona->getApellidos()));
                                $strVendedor  = $strNombres.' '.$strApellidos;
                                $intIdPersona = $objInfoPersona->getId();
                            }
                                
                            $objPersonaEmpresaRol   = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                        ->findBy(array(
                                                                                "personaId" => $intIdPersona
                                                                            ));
                            
                            $intIdPersonaRol = '';                                                    
                            foreach($objPersonaEmpresaRol as $objParametroRol)
                            {
                                $intIdEmpresaRol    = $objParametroRol->getEmpresaRolId()->getId();
                                //Consultamos si el id de la empresa_rol es de TN
                                $objEmpresaRol   = $this->emComercial->getRepository('schemaBundle:InfoEmpresaRol')
                                                                   ->findOneBy(array(
                                                                                    "id"         => $intIdEmpresaRol,
                                                                                    "empresaCod" => $empresa
                                                                                ));
                                if (is_object($objEmpresaRol))
                                {
                                    $intIdPersonaRol = $intIdEmpresaRol;
                                    break;
                                }

                            }

                            if(empty($intIdPersonaRol))
                            {
                                throw new \Exception("el Id de la empresa rol no pertenece a la empresa TN");
                            }
                                
                            $objCaractVelocidad      = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                            ->findOneBy(array("descripcionCaracteristica" => 'VELOCIDAD', "estado" => "Activo"));
                            $objProdCaracVelocidad   = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                            ->findOneBy(array(  "productoId"        => $servicio->getProductoId(),
                                                                                "caracteristicaId"  => $objCaractVelocidad->getId(),
                                                                                "estado"            => "Activo"));
                            $objSpcServicioVelocidad = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                   ->findOneBy(array(   "servicioId"                => 
                                                                                        $servicio->getId(),
                                                                                        "productoCaracterisiticaId" =>
                                                                                        $objProdCaracVelocidad->getId(),
                                                                                        "estado"        => "Activo"));
                            $strVelocidad            = $objSpcServicioVelocidad->getValor();
                            $arrayProductoCaracteristicasValores['VELOCIDAD'] = $strVelocidad;
                            $strFuncionPrecio        = $objProductoIp->getFuncionPrecio();
                            $strPrecioVelocidad      = $this->evaluarFuncionPrecio($strFuncionPrecio, $arrayProductoCaracteristicasValores);
                               
                            $arrayPlantillaProductos  = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                               ->getResultadoComisionPlantilla( array('intIdProducto' => $intIdProdIp,
                                                                                                      'strCodEmpresa' => $empresa) );
                            if (isset($arrayPlantillaProductos['objRegistros']) && !empty($arrayPlantillaProductos['objRegistros']))
                            {
                                foreach($arrayPlantillaProductos['objRegistros'] as $arrayItem)
                                {
                                    if (isset($arrayItem['idComisionDet']) && !empty($arrayItem['idComisionDet']))
                                    {
                                        $intIdComisionDet = (isset($arrayItem['idComisionDet']) && !empty($arrayItem['idComisionDet']))
                                                                ? $arrayItem['idComisionDet'] : 0;
                                    }
                                }
                            }
                                
                            $strPlantillaComisionista = $intIdComisionDet.'---'.$intIdPersonaRol;
                                
                            //Se crea el servicio adicional para este producto
                            $arrayServicios = array();
                            $arrayServicios[0]['hijo']                          = 0;
                            $arrayServicios[0]['servicio']                      = 0;
                            $arrayServicios[0]['codigo']                        = $intIdProdIp;
                            $arrayServicios[0]['producto']                      = $strDescripcionProdIp.' '.$strVelocidad.' 0';
                            $arrayServicios[0]['cantidad']                      = '1';
                            $arrayServicios[0]['frecuencia']                    = '1';
                            $arrayServicios[0]['precio']                        = $strPrecioVelocidad;
                            $arrayServicios[0]['precio_total']                  = $strPrecioVelocidad;
                            $arrayServicios[0]['info']                          = 'C';
                            $arrayServicios[0]['caracteristicasProducto']       = $strCaractIsb;
                            $arrayServicios[0]['caractCodigoPromoIns']          = '';
                            $arrayServicios[0]['nombrePromoIns']                = '';
                            $arrayServicios[0]['idTipoPromoIns']                = '';
                            $arrayServicios[0]['caractCodigoPromo']             = '';
                            $arrayServicios[0]['nombrePromo']                   = '';
                            $arrayServicios[0]['idTipoPromo']                   = '';
                            $arrayServicios[0]['caractCodigoPromoBw']           = '';
                            $arrayServicios[0]['nombrePromoBw']                 = '';
                            $arrayServicios[0]['idTipoPromoBw']                 = '';
                            $arrayServicios[0]['strServiciosMix']               = '';
                            $arrayServicios[0]['tipoMedio']                     = '';
                            $arrayServicios[0]['backupDesc']                    = '';
                            $arrayServicios[0]['fecha']                         = '';
                            $arrayServicios[0]['precio_venta']                  = $strPrecioVelocidad;
                            $arrayServicios[0]['precio_instalacion']            = '0';
                            $arrayServicios[0]['descripcion_producto']          = $strDescripcionProdIp.' '.$strVelocidad.' 0';
                            $arrayServicios[0]['precio_instalacion_pactado']    = '0';
                            $arrayServicios[0]['ultimaMilla']                   = '107';
                            $arrayServicios[0]['um_desc']                       = 'FTTx';
                            $arrayServicios[0]['login_vendedor']                = $strLoginVendedor;
                            $arrayServicios[0]['nombre_vendedor']               = $strVendedor;
                            $arrayServicios[0]['strPlantillaComisionista']      = $strPlantillaComisionista;
                            $arrayServicios[0]['cotizacion']                    = '';
                            $arrayServicios[0]['cot_desc']                      = 'Ninguna';
                            $arrayServicios[0]['intIdPropuesta']                = '';
                            $arrayServicios[0]['strPropuesta']                  = '';
                             
                            $objPuntoDestino = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($servicio
                                                                                                              ->getPuntoId()->getId());
                            $objRol   = null;

                            if (is_object($objPuntoDestino))
                            {
                                $objRol = $this->emComercial->getRepository('schemaBundle:AdmiRol')
                                                          ->find($objPuntoDestino->getPersonaEmpresaRolId()->getEmpresaRolId()->getRolId());
                            }
                            $arrayParamsServicio = array(   "codEmpresa"            => $empresa,
                                                    "idOficina"             => $objSession->get('idOficina'),
                                                    "entityPunto"           => $objPuntoDestino,
                                                    "entityRol"             => $objRol,
                                                    "usrCreacion"           => $usrCreacion,
                                                    "clientIp"              => $ipCreacion,
                                                    "tipoOrden"             => 'N',
                                                    "ultimaMillaId"         => null,
                                                    "servicios"             => $arrayServicios,
                                                    "strPrefijoEmpresa"     => $strPrefijoEmpresa,
                                                    "session"               => $objSession,
                                                    "intIdSolFlujoPP"       => $objSession->get('idSolFlujoPrePlanificacion') 
                                                                               ? $objSession->get('idSolFlujoPrePlanificacion') : 0
                                            );
                                $boolCrearServicio = true;
                            }
                        }
                    }
                
                //DIFERENTES ELEMENTOS
                if($elementoNuevo->getId() != $elementoViejo->getId())
                {
                    //PUNTO TIENE IP
                    if($totalIpPto > 0)
                    {
                        $arrServiciosPunto      = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                       ->findBy(array("puntoId" => $servicio->getPuntoId(), "estado" => "Activo"));
                        
                        //OBTENER IPS ADICIONALES A CANCELAR
                        $arrayDatosIpCancelar   = $this->servicioGeneral
                                                       ->getInfoIpsFijaPunto($arrServiciosPunto, $arrayProdIp, $servicio, 'Activo', 
                                                                             'Activo', $objProducto);
                        $arrayIpCancelar        = $arrayDatosIpCancelar['valores'];

                        //OBTENER LA CANTIDAD DE IPS ADICIONALES ACTIVAS
                        $intIpsFijasActivas     = $arrayDatosIpCancelar['ip_fijas_activas'];
                        $intIdPlanIpsDisponibleScopeOlt = 0;
                        if(is_object($servicio->getPlanId()))
                        {
                            $intIdPlanIpsDisponibleScopeOlt = $servicio->getPlanId()->getId();
                        }
                        
                        //OBTENER IPS ADICIONALES A ACTIVAR---------------------------------------------------------------------------------
                        $arregloIps = $this->recursosRed->getIpsDisponibleScopeOlt( $totalIpPto, 
                                                                                    $elementoNuevo->getId(), 
                                                                                    $servicio->getId(), 
                                                                                    $servicio->getPuntoId()->getId(), 
                                                                                    "SI", 
                                                                                    $intIdPlanIpsDisponibleScopeOlt);
                        
                        if($arregloIps['error'])
                        {
                            $arrayFinal[] = array('status' => "ERROR", 'mensaje' => $arregloIps['error']);

                            $punto = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($servicio->getPuntoId());
                            //Envío de notificación de creación de errores
                            /* @var $envioPlantilla EnvioPlantilla */
                            $asunto     = "Notificación de errores al activar cambio de línea Pon";
                            $parametros = array('login' => $punto->getLogin(),
                                                'olt'   => $elementoNuevo->getNombreElemento(),
                                                'error' => $arregloIps['error']);
                            $this->correo->generarEnvioPlantilla($asunto, $to, 'ECLP', $parametros, '', '', '');
                            return $arrayFinal;
                        }
                        
                        //SI EL SERVICIO TIENE IP EN EL PLAN
                        if($flagProdViejo == 1)
                        {
                            if ($strExisteIpWan === "SI")
                            {
                                $arrayServicioIp[] = array("idServicio" => $arrayDatosIpWan['arrayInfoIp']['intIdServicioIp']);
                                $strIpFija = $arrayDatosIpWan['arrayInfoIp']['strIp'];
                                $scope     = $arrayDatosIpWan['arrayInfoIp']['strScope'];
                            }
                            else 
                            {
                                $arrayServicioIp[] = array("idServicio" => $servicio->getId());
                                
                                //OBTENER IP DEL PLAN
                                $ipFija     = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                   ->findOneBy(array("servicioId"=>$servicio->getId(),"estado"=>"Activo"));

                                $strIpFija  = $ipFija->getIp();
                                
                                if($strEsIsb === "SI")
                                {
                                    $prodIpPlan = $objProducto;
                                }
                                
                                //OBTENER SCOPE
                                $spcScope = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SCOPE", $prodIpPlan);

                                if(!$spcScope)
                                {
                                    //buscar scopes
                                    $arrayScopeOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                             ->getScopePorIpFija($ipFija->getIp(), $servicioTecnico->getElementoId());

                                    if (!$arrayScopeOlt)
                                    {   
                                        $arrayFinal[] = array('status'  => "ERROR",
                                                              'mensaje' => "Ip Fija no pertenece a un Scope! <br>".
                                                                           "Favor Comunicarse con el Dep. Gepon!");
                                        return $arrayFinal;
                                    }

                                    $scope = $arrayScopeOlt['NOMBRE_SCOPE'];
                                }
                                else
                                {
                                    $scope = $spcScope->getValor();
                                }
                            }
                        }
                        
                        $arrayIps       = $arregloIps['ips'];
                        
                        //CONSTRUIR ARREGLO PARA ACTIVAR IPS ADICIONALES
                        $i = 0;
                        foreach($arrayIps as $arrIpData)
                        {
                            if($i == 0 && $flagProdViejo == 1)
                            {
                                if ($strExisteIpWan === "NO")
                                {
                                    $objSpcMac = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC ONT", $objProducto);
                                    if(!is_object($objSpcMac))
                                    {
                                        $objSpcMac = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC", $objProducto);
                                        if(!is_object($objSpcMac))
                                        {
                                            $objSpcMac  = $this->servicioGeneral
                                                               ->getServicioProductoCaracteristica($servicio, "MAC WIFI", $objProducto);
                                            if(!is_object($objSpcMac))
                                            {
                                                $respuestaFinal[] = array(  'status' => 'ERROR', 
                                                                            'mensaje' => 'No existe Mac asociado a un Servicio, favor revisar!');
                                                return $respuestaFinal;
                                            }
                                        }
                                    }
                                }
                                $strIpNuevaPlan = $arrIpData['ip'];
                                $strScopeNuevo  = $arrIpData['scope'];
                            }
                            else
                            {
                                $objServicioIp = $this->emInfraestructura->getRepository('schemaBundle:InfoServicio')
                                                                         ->find($arrayServicioIp[$i]['idServicio']);
                                
                                $objSpcMac = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioIp, "MAC ONT", $objProducto);
                                if(!is_object($objSpcMac))
                                {
                                    $objSpcMac = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioIp, "MAC", $objProducto);
                                    if(!is_object($objSpcMac))
                                    {
                                        $objSpcMac  = $this->servicioGeneral
                                                           ->getServicioProductoCaracteristica($objServicioIp, "MAC WIFI", $objProducto);
                                        if(!is_object($objSpcMac))
                                        {
                                            $respuestaFinal[] = array(  'status' => 'ERROR', 
                                                                        'mensaje' => 'No existe Mac asociado a un Servicio, favor revisar!');
                                            return $respuestaFinal;
                                        }
                                    }
                                }

                                $strMac         = $objSpcMac->getValor();
                                $strIp          = $arrIpData['ip'];
                                $intIdservicio  = $arrayServicioIp[$i]['idServicio'];
                                
                                $arrayIpActivar[] = array(
                                                        'mac'           => $strMac,
                                                        'ip'            => $strIp,
                                                        'id_servicio'   => $intIdservicio
                                                       );
                            }
                            
                            $i++;
                        }
                        //---------------------------------------------------------------------------------------------------------------
                    }
                    
                    //OBTENER IP ELEMENTO NUEVO
                    $objIpElementoNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                ->findOneBy(array("elementoId" => $elementoNuevo->getId()));
                    $strIpElementoNuevo = $objIpElementoNuevo->getIp();
                    
                    //OBTENER MODELO DE ELEMENTO NUEVO
                    $strModeloNuevo = $elementoNuevo->getModeloElementoId()->getNombreModeloElemento();
                }
                else
                {
                    if ($strNombreTecnicoProdPref === "INTERNET SMALL BUSINESS" 
                                                        && $strPrefijoEmpresa ==="TN"
                                                        && $boolIsb)
                    {
                         if ($strTipoIpOrigen === "FIJA")
                         {
                            if(is_object($servicio->getPlanId()))
                            {
                                $intIdPlanIpsDisponibleScopeOlt = $servicio->getPlanId()->getId();
                            }

                            //OBTENER IPS ADICIONALES A ACTIVAR---------------------------------------------------------------------------------
                            $arregloIps = $this->recursosRed->getIpsDisponibleScopeOlt( $totalIpPto, 
                                                                                        $elementoViejo->getId(), 
                                                                                        $servicio->getId(), 
                                                                                        $servicio->getPuntoId()->getId(), 
                                                                                        "SI", 
                                                                                        $intIdPlanIpsDisponibleScopeOlt);

                            if($arregloIps['error'])
                            {
                                $arrayFinal[] = array('status' => "ERROR", 'mensaje' => $arregloIps['error']);

                                $objPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($servicio->getPuntoId());
                                //Envío de notificación de creación de errores
                                /* @var $envioPlantilla EnvioPlantilla */
                                $strAsunto        = "Notificación de errores al activar cambio de línea Pon";
                                $arrayParamCorreo = array('login' => $objPunto->getLogin(),
                                                    'olt'   => $elementoViejo->getNombreElemento(),
                                                    'error' => $arregloIps['error']);
                                $this->correo->generarEnvioPlantilla($strAsunto, $strTo, 'ECLP', $arrayParamCorreo, '', '', '');
                                return $arrayFinal;
                            }
                            
                            //SI EL SERVICIO TIENE IP EN EL PLAN
                            if($flagProdViejo == 1)
                            {
                                if ($strExisteIpWan === "SI")
                                {
                                    $arrayServicioIp[] = array("idServicio" => $arrayDatosIpWan['arrayInfoIp']['intIdServicioIp']);
                                    $strIpFija         = $arrayDatosIpWan['arrayInfoIp']['strIp'];
                                    $scope             = $arrayDatosIpWan['arrayInfoIp']['strScope'];
                                }
                                else 
                                {
                                    $arrayServicioIp[] = array("idServicio" => $servicio->getId());

                                    //OBTENER IP DEL PLAN
                                    $objIpFija  = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                       ->findOneBy(array("servicioId"=>$servicio->getId(),"estado"=>"Activo"));

                                    $strIpFija  = $objIpFija->getIp();

                                    if(isset($arrayDatos[0]['esIsb']) && !empty($arrayDatos[0]['esIsb']) && $arrayDatos[0]['esIsb'] === "SI")
                                    {
                                        $prodIpPlan = $objProducto;
                                    }
                                    //OBTENER SCOPE
                                    $spcScope = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SCOPE", $prodIpPlan);

                                    if(!$spcScope)
                                    {
                                        //buscar scopes
                                        $arrayScopeOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                              ->getScopePorIpFija($objIpFija->getIp(), $servicioTecnico->getElementoId());

                                        if (!$arrayScopeOlt)
                                        {   
                                            $arrayFinal[] = array('status'  => "ERROR",
                                                                  'mensaje' => "Ip Fija no pertenece a un Scope! <br>".
                                                                               "Favor Comunicarse con el Dep. Gepon!");
                                            return $arrayFinal;
                                        }

                                        $scope = $arrayScopeOlt['NOMBRE_SCOPE'];
                                    }
                                    else
                                    {
                                        $scope = $spcScope->getValor();
                                    }
                                }
                            }
                            
                            $arrayIps       = $arregloIps['ips'];
                        
                            //CONSTRUIR ARREGLO PARA ACTIVAR IPS ADICIONALES
                            $intI = 0;
                            foreach($arrayIps as $arrIpData)
                            {
                                if($intI == 0 && $flagProdViejo == 1)
                                {
                                    if ($strExisteIpWan === "NO")
                                    {
                                        $objSpcMac = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC ONT", $objProducto);
                                        if(!is_object($objSpcMac))
                                        {
                                            $objSpcMac = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC", $objProducto);
                                            if(!is_object($objSpcMac))
                                            {
                                                $objSpcMac = $this->servicioGeneral
                                                                  ->getServicioProductoCaracteristica($servicio, "MAC WIFI", $objProducto);
                                                if(!is_object($objSpcMac))
                                                {
                                                    $respuestaFinal[] = array(  'status' => 'ERROR', 
                                                                                'mensaje' => 'No existe Mac asociado a un Servicio, favor revisar!');
                                                    return $respuestaFinal;
                                                }
                                            }
                                        }
                                    }
                                    $strIpNuevaPlan = $arrIpData['ip'];
                                    $strScopeNuevo  = $arrIpData['scope'];
                                }
                                else
                                {
                                    $objServicioIp = $this->emInfraestructura->getRepository('schemaBundle:InfoServicio')
                                                                             ->find($arrayServicioIp[$intI]['idServicio']);

                                    $objSpcMacInterna = $this->servicioGeneral
                                                             ->getServicioProductoCaracteristica($objServicioIp, "MAC ONT", $objProducto);
                                    if(!is_object($objSpcMacInterna))
                                    {
                                        $objSpcMacInterna = $this->servicioGeneral
                                                                 ->getServicioProductoCaracteristica($objServicioIp, "MAC", $objProducto);
                                        if(!is_object($objSpcMacInterna))
                                        {
                                            $objSpcMacInterna = $this->servicioGeneral
                                                                     ->getServicioProductoCaracteristica($objServicioIp, "MAC WIFI", $objProducto);
                                            if(!is_object($objSpcMacInterna))
                                            {
                                                $respuestaFinal[] = array(  'status' => 'ERROR', 
                                                                            'mensaje' => 'No existe Mac asociado a un Servicio, favor revisar!');
                                                return $respuestaFinal;
                                            }
                                        }
                                    }

                                    $strMac         = $objSpcMacInterna->getValor();
                                    $strIp          = $arrIpData['ip'];
                                    $intIdservicio  = $arrayServicioIp[$intI]['idServicio'];

                                    $arrayIpActivar[] = array(
                                                            'mac'           => $strMac,
                                                            'ip'            => $strIp,
                                                            'id_servicio'   => $intIdservicio
                                                           );
                                }

                                $intI++;
                            }
                            
                            //OBTENER IP ELEMENTO NUEVO
                            $objIpElementoNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                        ->findOneBy(array("elementoId" => $elementoViejo->getId()));
                            $strIpElementoNuevo = $objIpElementoNuevo->getIp();
                            
                            //OBTENER INTERFACE NUEVO
                            $intIpsFijasActivas = $totalIpPto -1;

                            //OBTENER MODELO DE ELEMENTO NUEVO
                            $strModeloNuevo     = $elementoViejo->getModeloElementoId()->getNombreModeloElemento();
                         }
                         else
                         {
                            $strIpElementoNuevo = $objIpElementoViejo->getIp();
                    
                            //OBTENER INTERFACE NUEVO
                            $intIpsFijasActivas = $totalIpPto -1;

                            //OBTENER MODELO DE ELEMENTO NUEVO
                            $strModeloNuevo     = $elementoViejo->getModeloElementoId()->getNombreModeloElemento();
                         }
                    }
                    else
                    {
                        $strIpElementoNuevo = $objIpElementoViejo->getIp();
                    
                        //OBTENER INTERFACE NUEVO
                        $intIpsFijasActivas = $totalIpPto -1;
                    
                        //OBTENER MODELO DE ELEMENTO NUEVO
                        $strModeloNuevo     = $elementoViejo->getModeloElementoId()->getNombreModeloElemento();
                    }
                }
                
                //OBTENER INTERFACE NUEVO
                $strInterfaceNuevo  = $interfaceElementoNuevo->getNombreInterfaceElemento();
                
                //DATOS PARA EL MIDDLEWARE
                $arrayDatosParamsMid = array(
                                        'ont_id'                => $strOntId,
                                        'mac_ont'               => $macOnt,
                                        'mac_wifi'              => $macWifi,
                                        'nombre_olt'            => $elementoViejo->getNombreElemento(),
                                        'ip_olt'                => $objIpElementoViejo->getIp(),
                                        'puerto_olt'            => $interfaceElementoViejo->getNombreInterfaceElemento(),
                                        'modelo_olt'            => $elementoViejo->getModeloElementoId()->getNombreModeloElemento(),
                                        'line_profile'          => $perfil,
                                        'estado_servicio'       => $servicio->getEstado(),
                                        'ip'                    => $strIpFija,     //ip plan actual
                                        'scope'                 => $scope,         //scope actual
                                        'ip_olt_nuevo'          => $strIpElementoNuevo,
                                        'modelo_olt_nuevo'      => $strModeloNuevo,
                                        'puerto_olt_nuevo'      => $strInterfaceNuevo,
                                        'ip_fijas_activas'      => $intIpsFijasActivas,
                                        'tipo_negocio_actual'   => $strTipoNegocio,
                                        'ip_nueva'              => $strIpNuevaPlan,
                                        'scope_nuevo'           => $strScopeNuevo,
                                        'ip_cancelar'           => $arrayIpCancelar,
                                        'ip_activar'            => $arrayIpActivar,
                                        'equipoOntDualBand'     => "",
                                        'tipoOrden'             => ""
                                    );
                if ($strPrefijoEmpresa === 'MD')
                {
                    $arrayRespuestaSeteaInfo = $this->servicioGeneral
                                                    ->seteaInformacionPlanesPyme(array("intIdPlan"         => $servicio->getPlanId()->getId(),
                                                                                       "intIdPunto"        => $servicio->getPuntoId()->getId(),
                                                                                       "strConservarIp"    => "",
                                                                                       "strTipoNegocio"    => $strTipoNegocio,
                                                                                       "strPrefijoEmpresa" => $strPrefijoEmpresa,
                                                                                       "strUsrCreacion"    => $usrCreacion,
                                                                                       "strIpCreacion"     => $ipCreacion,
                                                                                       "strTipoProceso"    => 'CAMBIAR_PUERTO',
                                                                                       "arrayInformacion"  => $arrayDatosParamsMid));
                    if($arrayRespuestaSeteaInfo["strStatus"]  === "OK")
                    {
                        $arrayDatosParamsMid = $arrayRespuestaSeteaInfo["arrayInformacion"];
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
                                                'empresa'               => $arrayDatos[0]['prefijoEmpresa'],
                                                'nombre_cliente'        => $strNombreCliente,
                                                'login'                 => $servicio->getPuntoId()->getLogin(),
                                                'identificacion'        => $strIdentificacion,
                                                'datos'                 => $arrayDatosParamsMid,
                                                'opcion'                => $this->opcion,
                                                'ejecutaComando'        => $this->ejecutaComando,
                                                'usrCreacion'           => $usrCreacion,
                                                'ipCreacion'            => $ipCreacion
                                            );

                $arrayRespuesta = $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));
                
                $statusActivar  = $arrayRespuesta['status_activar'];
                $statusCancelar = $arrayRespuesta['status_cancelar'];
                
                $mensajeFinal   = '';
                
                //RESPUESTA ACTIVAR Y CANCELAR = OK
                if($statusActivar == 'OK' && $statusCancelar == 'OK')
                {
                    $strSerieElementoClienteOnt = "";
                    $intIdElementoCliente       = $servicioTecnico->getElementoClienteId();
                    if(isset($intIdElementoCliente) && !empty($intIdElementoCliente))
                    {
                        $objElementoClienteOnt  = $this->emComercial->getRepository('schemaBundle:InfoElemento')
                                                                    ->find($intIdElementoCliente);
                        if(is_object($objElementoClienteOnt))
                        {
                            $strSerieElementoClienteOnt = $objElementoClienteOnt->getSerieFisica();
                        }
                    }
                    
                    $arrayDatosConfirmacionTn                           = $arrayDatosParamsMid;
                    $arrayDatosConfirmacionTn['serial_ont']             = $strSerieElementoClienteOnt;
                    $arrayDatosConfirmacionTn['opcion_confirmacion']    = $this->opcion;
                    $arrayDatosConfirmacionTn['respuesta_confirmacion'] = 'ERROR';
                    $arrayDataConfirmacionTn    = array('nombre_cliente'    => $strNombreCliente,
                                                        'login'             => $servicio->getPuntoId()->getLogin(),
                                                        'identificacion'    => $strIdentificacion,
                                                        'datos'             => $arrayDatosConfirmacionTn,
                                                        'opcion'            => $this->strConfirmacionTNMiddleware,
                                                        'ejecutaComando'    => $this->ejecutaComando,
                                                        'usrCreacion'       => $usrCreacion,
                                                        'ipCreacion'        => $ipCreacion,
                                                        'empresa'           => $arrayDatos[0]['prefijoEmpresa'],
                                                        'statusMiddleware'  => 'OK');
                    $mensajeFinal = $mensajeFinal . $arrayRespuesta['mensaje_cancelar'];
                    $mensajeFinal = $mensajeFinal . $arrayRespuesta['mensaje_activar'];
                    
                    //CAMBIAR PUERTO LOGICO
                    $this->cambiarPuertoLogicoMd(   $servicio, 
                                                    $servicioTecnico, 
                                                    $elementoNuevo->getId(), 
                                                    $interfaceElementoNuevo->getId(), 
                                                    $elementoCajaId,
                                                    $elementoSplitterId, 
                                                    $interfaceElementoSplitterId, 
                                                    $usrCreacion, 
                                                    $ipCreacion,
                                                    $empresa);
                    
                    //IP DEL PLAN
                    if($strIpNuevaPlan != '')
                    {
                        if ($strExisteIpWan === "SI")
                        {
                            $intIdServicioIp        = $arrayDatosIpWan['arrayInfoIp']['intIdServicioIp'];
                            $objServicioIpAdicional = $this->emComercial
                                                           ->getRepository('schemaBundle:InfoServicio')
                                                           ->find($intIdServicioIp);
                            $prodIpPlan             = $objServicioIpAdicional->getProductoId();
                            $spcScope               = $this->servicioGeneral
                                                           ->getServicioProductoCaracteristica($objServicioIpAdicional, "SCOPE", $prodIpPlan);
                        }
                        else
                        {
                            $intIdServicioIp        = $servicio->getId();
                            $objServicioIpAdicional = $servicio;
                        }
                        
                        $objServicioIpPlan = $this->emInfraestructura
                                                  ->getRepository('schemaBundle:InfoIp')
                                                  ->findOneBy(array("servicioId" => $intIdServicioIp, "estado" => "Activo"));
                        
                        $arrayParametrosIp['intIdServicio'] = $servicioId;
                        $arrayParametrosIp['emComercial']   = $this->emComercial;
                        $arrayParametrosIp['emGeneral']     = $this->emGeneral;
                        
                        $strTipoIp = '';
                        if ($strPrefijoEmpresa === 'TN')
                        {
                            $strTipoIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                             ->getTipoIpServicio($arrayParametrosIp);
                        }   
                        
                        //Si esta vacía la variable $strIp por default es Fija
                        if(empty($strTipoIp))
                        {
                            $strTipoIp = 'FIJA';
                        }
                        else
                        {
                            $strTipoIp = strtoupper($strTipoIp);
                        }
                        
                        if($objServicioIpPlan)
                        {
                            //ELIMINA IP ANTERIOR
                            $objServicioIpPlan->setEstado("Eliminado");
                            $this->emInfraestructura->persist($objServicioIpPlan);
                            $this->emInfraestructura->flush();
                            
                            //GRABAR Y ACTIVAR IP NUEVA
                            $ipFija = new InfoIp();
                            $ipFija->setIp($strIpNuevaPlan);
                            $ipFija->setEstado("Activo");
                            $ipFija->setTipoIp($strTipoIp);
                            $ipFija->setVersionIp('IPV4');
                            $ipFija->setServicioId($intIdServicioIp);
                            $ipFija->setUsrCreacion($usrCreacion);
                            $ipFija->setFeCreacion(new \DateTime('now'));
                            $ipFija->setIpCreacion($ipCreacion);
                            $this->emInfraestructura->persist($ipFija);
                            $this->emInfraestructura->flush();
                            
                            //ELIMINAR CARACTERISTICA SCOPE ANTERIOR
                            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($spcScope, 'Eliminado');
                            
                            //CREAR NUEVA CARACTERISTICA SCOPE NUEVO
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicioIpAdicional, 
                                                                                            $prodIpPlan, 
                                                                                            'SCOPE', 
                                                                                            $strScopeNuevo, 
                                                                                            $usrCreacion);
                        }
                    }//if($strIpNuevaPlan != '')
                    
                    //IPS ADICIONALES
                    if(count($arrayIpActivar) > 0)
                    {
                        //ELIMINAR IPS ADICIONALES ANTERIORES
                        foreach($arrayRespuesta['ip_cancelar'] as $arrayRespuestaIpCancelar)
                        {
                            $statusIpCancelar = $arrayRespuestaIpCancelar['status'];
                            
                            if($statusIpCancelar == 'OK')
                            {
                                //ELIMINA IP ANTERIOR
                                $objIpAdicional    = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                            ->findOneBy(array(  "servicioId"    => $arrayRespuestaIpCancelar['id_servicio'], 
                                                                                "estado"        => "Activo"));
                                
                                $objIpAdicional->setEstado('Eliminado');
                                $this->emInfraestructura->persist($objIpAdicional);
                                $this->emInfraestructura->flush();
                                
                                $servicioIpAdicional    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->find($arrayRespuestaIpCancelar['id_servicio']);
                                $spcScopeAdicional      = $this->servicioGeneral
                                                       ->getServicioProductoCaracteristica($servicioIpAdicional, "SCOPE", $servicioIpAdicional->getProductoId());
                                
                                //ELIMINAR CARACTERISTICA SCOPE ANTERIOR
                                $this->servicioGeneral->setEstadoServicioProductoCaracteristica($spcScopeAdicional, 'Eliminado');
                            }
                            
                            $mensajeFinal = $mensajeFinal . $arrayRespuestaIpCancelar['mensaje'];
                        }//foreach($arrayRespuesta['ip_cancelar'] as $arrayRespuestaIpCancelar)
                        
                        //GRABAR Y ACTIVAR IPS ADICIONALES NUEVAS
                        foreach($arrayRespuesta['ip_activar'] as $arrayRespuestaIpActivar)
                        {
                            $statusIpActivar = $arrayRespuestaIpActivar['status'];
                            
                            if($statusIpActivar == 'OK')
                            {
                                //GRABAR Y ACTIVAR IP NUEVA
                                $ipFija = new InfoIp();
                                $ipFija->setIp($arrayRespuestaIpActivar['ip']);
                                $ipFija->setEstado("Activo");
                                $ipFija->setTipoIp('FIJA');
                                $ipFija->setVersionIp('IPV4');
                                $ipFija->setServicioId($arrayRespuestaIpActivar['id_servicio']);
                                $ipFija->setUsrCreacion($usrCreacion);
                                $ipFija->setFeCreacion(new \DateTime('now'));
                                $ipFija->setIpCreacion($ipCreacion);
                                $this->emInfraestructura->persist($ipFija);
                                $this->emInfraestructura->flush();

                                $servicioIpAdicional    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->find($arrayRespuestaIpActivar['id_servicio']);
                                
                                //CREAR NUEVA CARACTERISTICA SCOPE NUEVO
                                $this->servicioGeneral->ingresarServicioProductoCaracteristica( $servicioIpAdicional, 
                                                                                                $servicioIpAdicional->getProductoId(),
                                                                                                'SCOPE', 
                                                                                                $strScopeNuevo, 
                                                                                                $usrCreacion);
                                
                                $servicioTecnicoIp = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                               ->findOneByServicioId($arrayRespuestaIpActivar['id_servicio']);

                                if($servicioTecnicoIp)
                                {
                                    $servicioTecnicoIp->setElementoId($elementoNuevo->getId());
                                    $servicioTecnicoIp->setInterfaceElementoId($interfaceElementoNuevo->getId());
                                    $servicioTecnicoIp->setElementoContenedorId($elementoCajaId);
                                    $servicioTecnicoIp->setElementoConectorId($elementoSplitterId);
                                    $servicioTecnicoIp->setInterfaceElementoConectorId($interfaceElementoSplitterId);
                                    $this->emComercial->persist($servicioTecnicoIp);
                                    $this->emComercial->flush();
                                }
                            }
                            
                            $mensajeFinal = $mensajeFinal . $arrayRespuestaIpActivar['mensaje'];
                        }//foreach($arrayRespuesta['ip_activar'] as $arrayRespuestaIpActivar)
                    }//if(count($arrayIpActivar) > 0)
                    
                    //Consulta si se debe crear servicio adicional
                    if ($boolCrearServicio)
                    {
                        $this->servicioInfoServicio->crearServicio($arrayParamsServicio);
                    }
                    
                    //ELIMINAR INDICE_CLIENTE ANTERIOR
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objIndiceClienteViejoSpc, "Eliminado");
                    
                    //CREAR NUEVO INDICE_CLIENTE
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $servicio, 
                                                                                    $objProducto, 
                                                                                    "INDICE CLIENTE", 
                                                                                    $arrayRespuesta['ont_id'],
                                                                                    $usrCreacion);
                    
                    $mensajeFinal = "OK";
                }//if($statusActivar == 'OK' && $statusCancelar == 'OK')
                else
                {
                    if($statusCancelar == 'ERROR')
                    {
                        $mensajeFinal = $mensajeFinal . $arrayRespuesta['mensaje_cancelar'];
                    }
                    else if($statusActivar == 'ERROR' && $statusCancelar == 'OK')
                    {
                        $mensajeFinal = $mensajeFinal . $arrayRespuesta['mensaje_cancelar'];
                        
                        //validar si aplica ip adicional IP FIJA WAN
                        //IPS ADICIONALES
                        if(count($arrayIpActivar) > 0)
                        {
                            //ELIMINAR IPS ADICIONALES ANTERIORES
                            foreach($arrayRespuesta['ip_cancelar'] as $arrayRespuestaIpCancelar)
                            {
                                $statusIpCancelar = $arrayRespuestaIpCancelar['status'];

                                if($statusIpCancelar == 'OK')
                                {
                                    //ELIMINA IP ANTERIOR
                                    $objIpAdicional    = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                ->findOneBy(array(  "servicioId"    => $arrayRespuestaIpCancelar['id_servicio'], 
                                                                                    "estado"        => "Activo"));

                                    $objIpAdicional->setEstado('Eliminado');
                                    $this->emInfraestructura->persist($objIpAdicional);
                                    $this->emInfraestructura->flush();

                                    //ELIMINAR CARACTERISTICA SCOPE ANTERIOR
                                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($spcScope, 'Eliminado');
                                }

                                $mensajeFinal = $mensajeFinal . $arrayRespuestaIpCancelar['mensaje'];
                            }//foreach($arrayRespuesta['ip_cancelar'] as $arrayRespuestaIpCancelar)
                        }//if(count($arrayIpActivar) > 0)
                        
                        $mensajeFinal = $mensajeFinal . $arrayRespuesta['mensaje_activar'];
                    }//else if($statusActivar == 'ERROR' && $statusCancelar == 'OK')
                    
                    throw new \Exception($mensajeFinal);
                }//else
            }//if($flagMiddleware)
            else
            {
                if($strEsIsb === "SI")
                {
                    $arrayFinal[]   = array('status'    => "ERROR",
                                            'mensaje'   => "El OLT considerado no soporta el esquema del middleware"
                                                            . "Favor Comunicarse con Sistemas!");
                    return $arrayFinal;
                }
                if ($totalIpPto > 0) 
                {
                    //valida si el cambio se lo realiza en el mismo o en otro olt con la finalidad de mantener las mismas ips.

                    if($elementoNuevo->getId() != $elementoViejo->getId())
                    {
                        //validacion para que tome directamente las ips que ya tiene reservada
                        $ipReservadaServicio = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                       ->findOneBy(array("servicioId" => $servicioId,
                                                                                         "estado" => "Reservada"));
                        if($ipReservadaServicio)
                        {
                            $arrayIps[] = array("ip" => $ipReservadaServicio->getIp(), "tipo" => $ipReservadaServicio->getTipoIp());
                        }
                        //obtengo las ip de los servicios adicionales
                        for($i = 0; $i < count($arrayServicioIp); $i++)
                        {

                            if($arrayServicioIp[$i]['idServicio'])
                            {
                                $ipReservada = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                       ->findOneBy(array("servicioId" => $arrayServicioIp[$i]['idServicio'],
                                                                                         "estado" => "Reservada"));
                                if($ipReservada)
                                {
                                    $arrayIps[] = array("ip" => $ipReservada->getIp(), "tipo" => $ipReservada->getTipoIp());
                                }
                            }
                        }

                        //si es la misma cantidad de ips reservadas como solicitadas se tomaran las reservadas
                        if(count($arrayIps) == $totalIpPto)
                        {
                            $flagIpsReservadas = 1;
                        }
                        else
                        {
                            //si aprovisiona obtengo el scope
                            if($objDetalleElemento)
                            {
                                $arregloIps = $this->recursosRed->getIpsDisponibleScopeOlt( $totalIpPto, 
                                                                $elementoNuevo->getId(), 
                                                                $servicio->getId(), 
                                                                $servicio->getPuntoId()->getId(), 
                                                                "SI", 
                                                                $servicio->getPlanId()->getId());
                                $arrayIps = $arregloIps['ips'];
                            }
                            else
                            {
                                $arregloIps = $this->recursosRed->getIpsDisponiblePoolOlt(  $totalIpPto, 
                                                                                            $elementoNuevo->getId(), 
                                                                                            $servicio->getId(), 
                                                                                            $servicio->getPuntoId()->getId(), "SI", 
                                                                                            $servicio->getPlanId()->getId());
                                $arrayIps = $arregloIps['ips'];                        
                            }
                            if($arregloIps['error'])
                            {
                                //reversar cambio de puerto logico
                                $this->cambiarPuertoLogicoMd($servicio, $servicioTecnico, $elementoViejo->getId(), $interfaceElementoViejo->getId(), 
                                                             $elementoContenedorViejo->getId(), $elementoConectorViejo->getId(), 
                                                             $interfaceElementoConectorViejo->getId(), $usrCreacion, $ipCreacion, $empresa);
                                $arrayFinal[] = array('status' => "ERROR", 'mensaje' => $arregloIps['error']);

                                $punto = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($servicio->getPuntoId());
                                //Envío de notificación de creación de errores
                                /* @var $envioPlantilla EnvioPlantilla */
                                $asunto = "Notificación de errores al activar cambio de línea Pon";
                                $parametros = array('login' => $punto->getLogin(), 'olt' => $elementoNuevo->getNombreElemento(), 
                                                    'error' => $arregloIps['error']);
                                $this->correo->generarEnvioPlantilla($asunto, $to, 'ECLP', $parametros, '', '', '');
                                return $arrayFinal;
                            }
                            //si no tienen cnr que compruebe en el equipo tellion
                            if(!$objDetalleElemento)
                            {    
                                //if($arregloIps['error'])
                                //obtener script para verificar ips
                                $scriptArray = $this->servicioGeneral->obtenerArregloScript("obtenerPoolParaIpFija", $elementoNuevo->getModeloElementoId());
                                $idDocumentoPool = $scriptArray[0]->idDocumento;
                                $usuario = $scriptArray[0]->usuario;
                                $protocolo = $scriptArray[0]->protocolo;

                                //verificar si todas las ips estan o no configuradas en el olt
                                for($i = 0; $i < count($arrayIps); $i++)
                                {
                                    $comando = "java -jar -Djava.security.egd=file:/dev/./urandom /home/telcos/src/telconet/tecnicoBundle/batch/md_datos.jar '" . $this->host 
                                                . "' 'verificarIpConfigurada' '" . $servicioTecnico->getElementoId() . "' '" . $usuario . "' '" . $protocolo .
                                                "' '" . $idDocumentoPool . "' '" . $arrayIps[$i]['ip'] . "'";
                                    $salida = shell_exec($comando);
                                    $pos = strpos($salida, "{");
                                    $jsonObj = substr($salida, $pos);
                                    $resultadJsonPerfil = json_decode($jsonObj);

                                    $status = $resultadJsonPerfil->status;
                                    $macIpConf = $resultadJsonPerfil->mensaje;
                                    if($status == "ERROR")
                                    {
                                        //reversar cambio de puerto logico
                                        $this->cambiarPuertoLogicoMd($servicio, $servicioTecnico, $elementoViejo->getId(), $interfaceElementoViejo->getId(), 
                                                                     $elementoContenedorViejo->getId(), $elementoConectorViejo->getId(),
                                                                     $interfaceElementoConectorViejo->getId(), $usrCreacion, $ipCreacion, $empresa);

                                        $olt = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($servicioTecnico->getElementoId());
                                        $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'Ip: <b>' . $arrayIps[$i]['ip'] .
                                            '</b> ya se encuentra configurada en <b>' . $olt->getNombreElemento() .
                                            '</b> con la mac <b>' . $macIpConf .
                                            '</b>. Favor notificar a Sistemas.');
                                        return $respuestaFinal;
                                    }
                                }//for ($i=0; $i<count($arrayIps); $i++)
                            }
                        }
                    }
                    //el cambio es en el mismo olt
                    else 
                    {
                        //ingreso la ip del servicio al arreglo
                        $ipServicio = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                         ->findOneBy(array("servicioId" => $servicioId,
                                                               "estado" => "Activo"));
                        if($ipServicio)
                        {
                            $arrayIps[] = array("ip" => $ipServicio->getIp(), "tipo" => $ipServicio->getTipoIp());
                        }
                        //obtengo las ip de los servicios adicionales
                        for ($i = 0; $i < count($arrayServicioIp); $i++) {

                            if ($arrayServicioIp[$i]['idServicio']) 
                            {
                                $ipViejas = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                        ->findOneBy(array("servicioId" => $arrayServicioIp[$i]['idServicio'],
                                                          "estado" => "Activo"));
                                if ($ipViejas) 
                                {
                                    $arrayIps[] = array("ip" => $ipViejas->getIp(), "tipo" => $ipViejas->getTipoIp());

                                }
                            }
                        }
                    }
                    $proIpAdicional = "NO";
                    //aumento validacion para que de una vuelta mas para el caso de pro con ip adicional
                    if($arrayServicioIpProducto && $planCabNuevo->getTipo() == 'PRO')
                    {
                        $totalIpPto = $totalIpPto+1;
                        $proIpAdicional = "SI";
                    }
                    //cancelar servicio e ips adicionales

                    for ($i = 0; $i < $totalIpPto; $i++) {
                        $tmp = $i;
                        //si la ip esta dentro del plan de internet
                        if ($flagProdViejo == 1 && $planConIp ==0) 
                        {
                            $servicioIpPlan = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                    ->findOneBy(array("servicioId" => $servicio->getId(), "estado" => "Activo"));
                            //echo "servicioTecnico".$servicioTecnico->getId()." elementoViejo".$elementoViejo->getModeloElementoId()." interfaceElementoViejo".$interfaceElementoViejo->getId()." producto".$producto->getId()." Sloginservicio".$servicio->getPuntoId()->getLogin();    die();    
                            //cancelamos (script) servicio con ip
                            $arrayParametros = array(
                                            'servicioTecnico'   => $servicioTecnico,
                                            'interfaceElemento' => $interfaceElementoViejo,
                                            'modeloElemento'    => $elementoViejo->getModeloElementoId(),
                                            'producto'          => $objProducto,
                                            'spcIndiceCliente'  => $objIndiceClienteViejoSpc,
                                            'spcMacOnt'         => $servProdCaractMacOnt,
                                            'login'             => $servicio->getPuntoId()->getLogin(),
                                            'idEmpresa'         => $empresa,
                                            'ipCreacion'        => $ipCreacion,
                                            'usrCreacion'       => $usrCreacion
                                        );
                            $respuestaArrayCancel = $this->cancelar->cancelarServicioMdConIp($arrayParametros);
                            $statusCancel = $respuestaArrayCancel[0]['status'];
                            //$statusCancel = "OK";
                            if ($statusCancel == "OK") 
                            {
                                //eliminamos ip vieja
                                if ($servicioIpPlan) 
                                {
                                    $servicioIpPlan->setEstado("Eliminado");
                                    $this->emInfraestructura->persist($servicioIpPlan);
                                    $this->emInfraestructura->flush();
                                }

                                $planConIp = 1;
                            }//if($statusCancel=="OK")
                            else 
                            {
                                $arrayFinal[] = array('status' => "ERROR", 'mensaje' => 'No se pudo Cancelar el servicio con IP, <br>'
                                    . 'Favor verificar todos los datos!</br>'.$respuestaArrayCancel[0]['mensaje']);
                                return $arrayFinal;
                            }
                        }//if ($flagProdViejo==1)
                        else if ($flagProdViejo == 0 && $planConIp == 0) 
                        {
                            $servProdCaractIndiceCliente = $this->servicioGeneral
                                    ->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $objProducto);
                            $arrayParametros = array(
                                            'servicioTecnico'   => $servicioTecnico,
                                            'interfaceElemento' => $interfaceElementoViejo,
                                            'modeloElemento'    => $elementoViejo->getModeloElementoId(),
                                            'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                                            'login'             => $servicio->getPuntoId()->getLogin(),
                                            'spcSpid'           => "",
                                            'spcMacOnt'         => "",
                                            'idEmpresa'         => $empresa
                                        );
                            $respuestaArrayCancel = $this->cancelar->cancelarServicioMdSinIp($arrayParametros);

                            $statusCancel = $respuestaArrayCancel[0]['status'];
                            //$statusCancel = "OK";
                            if ($statusCancel == "ERROR")
                            {
                                $arrayFinal[] = array('status' => "ERROR", 'mensaje' => 'No se pudo Cancelar el servicio sin Ip, <br>'
                                    . 'Favor verificar todos los datos!</br>'.$respuestaArrayCancel[0]['mensaje']);
                                return $arrayFinal;
                            }

                            $planConIp = 1;
                        } 
                        else 
                        {   
                            //servicio adicional de ip
                            $servicioIpAdicional = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                        ->find($arrayServicioIp[$i]['idServicio']);

                            $arrParametrosCancel = array(
                                                            'servicioTecnico'   => $servicioTecnico,
                                                            'modeloElemento'    => $elementoViejo->getModeloElementoId(),
                                                            'interfaceElemento' => $interfaceElementoViejo,
                                                            'producto'          => $objProducto,
                                                            'servicio'          => $servicioIpAdicional,
                                                            'spcMac'            => "",
                                                            'scope'             => ""
                                                        );

                            //cancelar (script) ip adicional
                            $this->cancelar->cancelarServicioIp($arrParametrosCancel);

                            //eliminar (base) ip adicional
                            $ipAdicional = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                    ->findOneBy(array("servicioId" => $servicioIpAdicional->getId(),
                                "estado" => "Activo"));
                            if ($ipAdicional) 
                            {
                                $ipAdicional->setEstado("Eliminado");
                                $this->emInfraestructura->persist($ipAdicional);
                                $this->emInfraestructura->flush();
                            }
                        }


                    }//for ($i=0;$i<$totalIpPto;$i++)

                    //Estado eliminado a los indices de la tabla producto servicio caracteristica                
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objIndiceClienteViejoSpc,"Eliminado");

                    //realizar cambio de puerto logico para la activacion
                    $this->cambiarPuertoLogicoMd($servicio, $servicioTecnico, $elementoNuevo->getId(), $interfaceElementoNuevo->getId(), $elementoCajaId,
                                                 $elementoSplitterId, $interfaceElementoSplitterId, $usrCreacion, $ipCreacion, $empresa);

                    //activar servicio e ips adicional
                    $planConIp = 0;
                    for ($i = 0; $i < $totalIpPto; $i++){
                        $tmp = $i;
                        //si la ip esta dentro del plan de internet

                        if ($flagProdViejo == 1 && $planConIp ==0) 
                        {
                            //reservamos la ip nueva
                            if($flagIpsReservadas == 0)
                            {
                                $ipFija = new InfoIp();
                                $ipFija->setIp($arrayIps[$i]['ip']);
                                $ipFija->setEstado("Reservada");
                                $ipFija->setTipoIp($arrayIps[$i]['tipo']);
                                $ipFija->setServicioId($servicio->getId());
                                $ipFija->setUsrCreacion($usrCreacion);
                                $ipFija->setFeCreacion(new \DateTime('now'));
                                $ipFija->setIpCreacion($ipCreacion);
                                $this->emInfraestructura->persist($ipFija);
                                $this->emInfraestructura->flush();
                            }
                            else
                            {
                                $ipFija = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                  ->findOneBy(array("ip" => $arrayIps[$i]['ip'],
                                                                                    "estado" => "Reservada"));
                            }
                            $arrayParametros=array(
                                            'servicio'          => $servicio,
                                            'servicioTecnico'   => $servicioTecnico,
                                            'interfaceElemento' => $interfaceElementoNuevo,
                                            'modeloElemento'    => $elementoNuevo->getModeloElementoId(),
                                            'producto'          => $objProducto,
                                            'macOnt'            => $macOnt,
                                            'macWifi'           => $servProdCaracMacWifi->getValor(),
                                            'perfil'            => $perfil,
                                            'login'             => $servicio->getPuntoId()->getLogin(),
                                            'usrCreacion'       => $usrCreacion
                                          );
                            //activamos servicio con ip
                            $respuestaArrayActivar = $this->activar->activarClienteMdConIp($arrayParametros);
                            $statusActivar = $respuestaArrayActivar[0]['status'];


                            if ($statusActivar == "OK") 
                            {

                                $ipFija->setEstado("Activo");
                                $this->emInfraestructura->persist($ipFija);
                                $this->emInfraestructura->flush();

                                //guardamos el indice
                                $indiceCliente = $respuestaArrayActivar[0]['mensaje'];

                                $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, 
                                                        $objProducto, 
                                                        "INDICE CLIENTE", 
                                                        $indiceCliente, 
                                                        $usrCreacion);

                                if ($planCaractEdicionLimitada){
                                    if($planCaractEdicionLimitada->getValor()=="SI")
                                    {
                                        $comando = "java -jar -Djava.security.egd=file:/dev/./urandom /home/telcos/src/telconet/tecnicoBundle/batch/md_sce.jar '".$this->host."' '".$servicio->getId()."' 'activar' '".$ipFija->getIp()."'";
                                        error_log($comando);
                                        $salida= shell_exec($comando);
                                        $pos = strpos($salida, "{"); 
                                        $jsonObj= substr($salida, $pos);
                                        $resultadJson = json_decode($jsonObj);
                                        $statusSce = $resultadJson->status;
                                        if($statusSce=="ERROR")
                                        {
                                            $ipFija->setEstado("Eliminado");
                                            $this->emInfraestructura->persist($ipFija);
                                            $this->emInfraestructura->flush();

                                            $mensaje = $resultadJson->mensaje;
                                            $respuestaFinal[] = array('status'=>'ERROR', 'mensaje'=>$mensaje);
                                            return $respuestaFinal;
                                        }
                                    }
                                }
                                $planConIp = 1;
                            }//if($statusActivar=="OK")
                            else 
                            {
                                //reversar cambio de puerto logico
                                $this->cambiarPuertoLogicoMd($servicio, $servicioTecnico, $elementoViejo->getId(), $interfaceElementoViejo->getId(), 
                                                             $elementoContenedorViejo->getId(), $elementoConectorViejo->getId(), 
                                                             $interfaceElementoConectorViejo->getId(), $usrCreacion, $ipCreacion, $empresa);

                                $ipFija->setEstado("Eliminado");
                                $this->emInfraestructura->persist($ipFija);
                                $this->emInfraestructura->flush();

                                $arrayParametros=array(
                                            'servicio'          => $servicio,
                                            'servicioTecnico'   => $servicioTecnico,
                                            'interfaceElemento' => $interfaceElementoViejo,
                                            'modeloElemento'    => $elementoViejo->getModeloElementoId(),
                                            'producto'          => $objProducto,
                                            'macOnt'            => $macOnt,
                                            'macWifi'           => $macWifi,
                                            'perfil'            => $perfil,
                                            'login'             => $servicio->getPuntoId()->getLogin(),
                                            'usrCreacion'       => $usrCreacion
                                          );

                                //activamos servicio con ip del puerto anterior
                                $respuestaArrayActivarClie = $this->activar->activarClienteMdConIp($arrayParametros);
                                //activo el indice viejo del cliente 
                                $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objIndiceClienteViejoSpc,"Activo");
                                $arrayFinal[] = array('status' => "ERROR", 'mensaje' => 'No se pudo Activar cliente con Ip, <br>'
                                    . 'Favor verificar todos los datos!<br>'. $respuestaArrayActivar[0]['mensaje']);
                                return $arrayFinal;
                            }
                        }//if ($flagProdViejo==1)
                        else if ($flagProdViejo == 0 && $planConIp ==0) 
                        {
                            $arrayParametros=array(
                                            'servicioTecnico'   => $servicioTecnico,
                                            'interfaceElemento' => $interfaceElementoNuevo,
                                            'modeloElemento'    => $elementoNuevo->getModeloElementoId(),
                                            'macOnt'            => $macOnt,
                                            'perfil'            => $perfil,
                                            'login'             => $servicio->getPuntoId()->getLogin(),
                                            'ontLineProfile'    => "",
                                            'serviceProfile'    => "",
                                            'serieOnt'          => "",
                                            'vlan'              => "",
                                            'gemPort'           => "",
                                            'trafficTable'      => ""
                                          );
                            //activamos servicio sin ip
                            $respuestaArrayActivar = $this->activar->activarClienteMdSinIp($arrayParametros);
                            $statusActivar = $respuestaArrayActivar[0]['status'];
                            if ($statusActivar == "ERROR") 
                            {
                                //reversar cambio de puerto logico
                                $this->cambiarPuertoLogicoMd($servicio, $servicioTecnico, $elementoViejo->getId(), $interfaceElementoViejo->getId(), 
                                                             $elementoContenedorViejo->getId(), $elementoConectorViejo->getId(), 
                                                             $interfaceElementoConectorViejo->getId(), $usrCreacion, $ipCreacion, $empresa);

                                $arrayParametros=array(
                                            'servicioTecnico'   => $servicioTecnico,
                                            'interfaceElemento' => $interfaceElementoNuevo,
                                            'modeloElemento'    => $elementoViejo->getModeloElementoId(),
                                            'macOnt'            => $macOnt,
                                            'perfil'            => $perfil,
                                            'login'             => $servicio->getPuntoId()->getLogin(),
                                            'ontLineProfile'    => "",
                                            'serviceProfile'    => "",
                                            'serieOnt'          => "",
                                            'vlan'              => "",
                                            'gemPort'           => "",
                                            'trafficTable'      => ""
                                          );

                                //activamos servicio sin ip del puerto anterior
                                $respuestaArrayActivarClie = $this->activar->activarClienteMdSinIp($arrayParametros);
                                //activamos el indice anterior del cliente
                                $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objIndiceClienteViejoSpc,"Activo");
                                $arrayFinal[] = array('status' => "ERROR", 'mensaje' => 'No se pudo Activar el Puerto Nuevo, <br>'
                                    . 'Favor verificar todos los datos!<br>'.$respuestaArrayActivar[0]['mensaje']);
                                return $arrayFinal;
                            }
                            $indiceCliente = $respuestaArrayActivar[0]['mensaje'];
                            //creacion de indice en la tabla servicio producto caracteristica
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, 
                                                    $objProducto, 
                                                    "INDICE CLIENTE", 
                                                    $indiceCliente, 
                                                    $usrCreacion);

                            $planConIp = 1;
                        }//else if($flagProdViejo==0)
                        else 
                        {

                                if($proIpAdicional=='SI')
                                {
                                    $ipProdAdicional = $arrayIps[0]['ip'];
                                    $tipoIp = $arrayIps[0]['tipo'];
                                }else
                                {
                                                            //servicio adicional de ip
                                    $servicioIpAdicional = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                ->findOneById($arrayServicioIp[$i]['idServicio']);
                                                                //validamos pro con ip adicional
                                    $ipProdAdicional = $arrayIps[$i]['ip'];
                                    $tipoIp = $arrayIps[$i]['tipo'];
                                }


                            if($flagIpsReservadas == 0)
                            {

                            //reservamos la ip nueva
                                $ipFija = new InfoIp();
                                $ipFija->setIp($ipProdAdicional);
                                $ipFija->setEstado("Reservada");
                                $ipFija->setTipoIp($tipoIp);
                                $ipFija->setServicioId($servicioIpAdicional->getId());
                                $ipFija->setUsrCreacion($usrCreacion);
                                $ipFija->setFeCreacion(new \DateTime('now'));
                                $ipFija->setIpCreacion($ipCreacion);
                                $this->emInfraestructura->persist($ipFija);
                                $this->emInfraestructura->flush();
                            }
                            else
                            {
                                $ipFija = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                  ->findOneBy(array("ip" => $arrayIps[$i]['ip'],
                                                                                    "estado" => "Reservada"));
                            }
                            //activar (script y base) ip adicional
                            $arrayActivarServicioIp =$this->activar->activarServicioIp(  $servicioIpAdicional, $servicioTecnico, $objProducto, 
                                                                                    $interfaceElementoNuevo, $elementoNuevo->getModeloElementoId());
                            $arrayActivarServicioIpStatus   = $arrayActivarServicioIp['status'];
                            if ($arrayActivarServicioIpStatus=='ERROR')
                            {
                                $arrayFinal[] = array('status' => "ERROR", 
                                                      'mensaje' => 'No se pudo Activar la ip adicional, <br>'
                                                      .$arrayActivarServicioIp['mensaje']);
                                return $arrayFinal;
                            }
                        }
                    }
                    //for ($i=0;$i<$totalIpPto;$i++)
                    //actualizo la info tecnica de las ip productos
                    foreach ($arrayServicioIpProducto as $servicioProducto)
                    {
                        $servicioTecnicoIp = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                  ->findOneByServicioId($servicioProducto['idServicio']);

                        if ($servicioTecnicoIp)
                        {
                            $servicioTecnicoIp->setElementoId($elementoNuevo->getId());
                            $servicioTecnicoIp->setInterfaceElementoId($interfaceElementoNuevo->getId());
                            $servicioTecnicoIp->setElementoContenedorId($elementoCajaId);
                            $servicioTecnicoIp->setElementoConectorId($elementoSplitterId);
                            $servicioTecnicoIp->setInterfaceElementoConectorId($interfaceElementoSplitterId);
                            $this->emComercial->persist($servicioTecnicoIp);
                            $this->emComercial->flush();
                        }

                    }



                }//if($totalIpPto>0)
                else 
                {
                    //cancelar servicio
                    $servProdCaractIndiceCliente = $this->servicioGeneral
                            ->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $objProducto);
                    $arrayParametrosCancel = array(
                                                    'servicioTecnico'   => $servicioTecnico,
                                                    'interfaceElemento' => $interfaceElementoViejo,
                                                    'modeloElemento'    => $elementoViejo->getModeloElementoId(),
                                                    'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                                                    'login'             => $servicio->getPuntoId()->getLogin(),
                                                    'spcSpid'           => "",
                                                    'spcMacOnt'         => "",
                                                    'idEmpresa'         => $empresa
                                                  );
                    $respuestaArrayCancel = $this->cancelar->cancelarServicioMdSinIp($arrayParametrosCancel);
                    $statusCancel = $respuestaArrayCancel[0]['status'];
                    //$statusCancel = "OK";
                    if ($statusCancel == "ERROR") 
                    {
                        $arrayFinal[] = array('status' => "ERROR", 'mensaje' => 'No se pudo Cancelar el servicio sin Ip, <br>'
                            . 'Favor verificar todos los datos!<br>'.$respuestaArrayCancel[0]['mensaje']);
                        return $arrayFinal;
                    }

                    //poner estado eliminado a los indices de la tabla producto servicio caracteristica
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objIndiceClienteViejoSpc,"Eliminado");


                    //realizar cambio de puerto logico para la activacion
                    $resultado = $this->cambiarPuertoLogicoMd($servicio, $servicioTecnico, $elementoNuevo->getId(), $interfaceElementoNuevo->getId(), 
                                                              $elementoCajaId, $elementoSplitterId, $interfaceElementoSplitterId, $usrCreacion, 
                                                              $ipCreacion, $empresa);

                    $arrayParametros=array(
                                            'servicioTecnico'   => $servicioTecnico,
                                            'interfaceElemento' => $interfaceElementoNuevo,
                                            'modeloElemento'    => $elementoNuevo->getModeloElementoId(),
                                            'macOnt'            => $macOnt,
                                            'perfil'            => $perfil,
                                            'login'             => $servicio->getPuntoId()->getLogin(),
                                            'ontLineProfile'    => "",
                                            'serviceProfile'    => "",
                                            'serieOnt'          => "",
                                            'vlan'              => "",
                                            'gemPort'           => "",
                                            'trafficTable'      => ""
                                          );

                    //activamos servicio sin ip
                    $respuestaArrayActivar = $this->activar->activarClienteMdSinIp($arrayParametros);
                    $statusActivar = $respuestaArrayActivar[0]['status'];
                    //$statusActivar = "OK";
                    if ($statusActivar == "ERROR") 
                    {
                        //reversar cambio de puerto logico
                        $this->cambiarPuertoLogicoMd($servicio, $servicioTecnico, $elementoViejo->getId(), $interfaceElementoViejo->getId(), 
                                                     $elementoContenedorViejo->getId(), $elementoConectorViejo->getId(), 
                                                     $interfaceElementoConectorViejo->getId(), $usrCreacion, $ipCreacion, $empresa);

                        $arrayParametros=array(
                                            'servicioTecnico'   => $servicioTecnico,
                                            'interfaceElemento' => $interfaceElementoViejo,
                                            'modeloElemento'    => $elementoViejo->getModeloElementoId(),
                                            'macOnt'            => $macOnt,
                                            'perfil'            => $perfil,
                                            'login'             => $servicio->getPuntoId()->getLogin(),
                                            'ontLineProfile'    => "",
                                            'serviceProfile'    => "",
                                            'serieOnt'          => "",
                                            'vlan'              => "",
                                            'gemPort'           => "",
                                            'trafficTable'      => ""
                                          );

                        //activamos servicio sin ip del puerto anterior
                        $respuestaArrayActivarClie = $this->activar->activarClienteMdSinIp($arrayParametros);

                        //activamos el indice anterior del cliente
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objIndiceClienteViejoSpc,"Activo");
                        $arrayFinal[] = array('status' => "ERROR", 'mensaje' => 'No se pudo Activar el Puerto Nuevo, <br>'
                            . 'Favor verificar todos los datos!<br>'.$respuestaArrayActivar[0]['mensaje']);
                        return $arrayFinal;
                    }

                    $indiceCliente = $respuestaArrayActivar[0]['mensaje'];

                    $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, 
                                            $objProducto, 
                                            "INDICE CLIENTE", 
                                            $indiceCliente, 
                                            $usrCreacion);
                }//else
                
                $mensajeFinal = 'OK';
            }
            
            //finalizar solicitud
            $objSolicitud->setObservacion('Se finaliza Solicitud, por ejecucion de cambio de linea pon');
            $objSolicitud->setEstado('Finalizada');
            $this->emComercial->persist($objSolicitud);
            $this->emComercial->flush();

            //historial de la solicitud
            $InfoDetalleSolHist = new InfoDetalleSolHist();
            $InfoDetalleSolHist->setDetalleSolicitudId($objSolicitud);
            $InfoDetalleSolHist->setObservacion('Se finaliza Solicitud, por ejecucion de cambio de linea pon');
            $InfoDetalleSolHist->setUsrCreacion($usrCreacion);
            $InfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
            $InfoDetalleSolHist->setEstado('Finalizada');
            $this->emComercial->persist($InfoDetalleSolHist);
            $this->emComercial->flush();
            $arrayDataConfirmacionTn['datos']['respuesta_confirmacion'] = "OK";
        } 
        catch (\Exception $e) {
            if ($this->emInfraestructura->getConnection()->isTransactionActive()) 
            {
                $this->emInfraestructura->getConnection()->rollback();
            }

            if ($this->emComercial->getConnection()->isTransactionActive()) 
            {
                $this->emComercial->getConnection()->rollback();
            }
            $this->rdaMiddleware->ejecutaWsSinEsperarRespuestaMiddleware($arrayDataConfirmacionTn);
            $arrayFinal[] = array('status' => "ERROR", 'mensaje' => "ERROR, " . $e->getMessage());
            return $arrayFinal;
        }

        //*DECLARACION DE COMMITS*/
        if ($this->emInfraestructura->getConnection()->isTransactionActive()) 
        {
            $this->emInfraestructura->getConnection()->commit();
        }

        if ($this->emComercial->getConnection()->isTransactionActive()) 
        {
            $this->emComercial->getConnection()->commit();
        }

        
        //*----------------------------------------------------------------------*/

        //EJECUTAR VALIDACIÓN DE PROMOCIONES BW
        $arrayParametrosInfoBw = array();
        $arrayParametrosInfoBw['intIdServicio']     = $servicio->getId();
        $arrayParametrosInfoBw['intIdEmpresa']      = $empresa;
        $arrayParametrosInfoBw['strTipoProceso']    = "CAMBIO_LINEA_PON";
        $arrayParametrosInfoBw['strValor']          = $intIdElementoOltNuevo;
        $arrayParametrosInfoBw['strUsrCreacion']    = $usrCreacion;
        $arrayParametrosInfoBw['strIpCreacion']     = $ipCreacion;
        $arrayParametrosInfoBw['strPrefijoEmpresa'] = $strPrefijoEmpresa;
        $this->servicePromociones->configurarPromocionesBW($arrayParametrosInfoBw);
        
        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        $this->rdaMiddleware->ejecutaWsSinEsperarRespuestaMiddleware($arrayDataConfirmacionTn);
        $arrayFinal[] = array('status' => "OK", 'mensaje' => "Se realizó el cambio de línea Pon");
        return $arrayFinal;
    }

    /**
     * cambiarPuertoScriptMdHuawei
     * Service que realiza el cambio de linea pon en equipos Huawei, con ejecucion de scripts
     *
     * @author Creado: John Vera <javera@telconet.ec>
     * @version 1.0 7-05-2015
     * @version 1.1 modificado: 02-09-2015 John Vera
     * @version 1.2 modificado: 29-09-2015 John Vera
     * @version 1.3 modificado: 09-05-2016 Jesus Bozada
     * 
     * @author Francisco Adum <fadum@netlife.net.ec>
     * @version 1.4 12-06-2017  Se agrega bandera para que use el middleware de RDA
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 11-10-2017  Se regularizan cambios que fueron realizados en caliente,se agrega mensaje de exito en la linea 2232
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 20-02-2018 Se agregan validaciones para permitir el cambio de línea pon en servicios Internet Small Business
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.7 04-06-2018 Se modifican validaciones para servicios Internet Small Business con Ips adicionales
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 26-12-2018 Se agregan parámetros equipoOntDualBand y tipoOrden por cambio en envío al middleware al activar un servicio
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.9 25-02-2019 Se agrega cambio de línea pon para servicios TelcoHome con tecnología Huawei
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.10 20-06-2019 Se agrega método (generarSincronizacionExtenderDualBand) que genera las caracteristicas necesarias para poder Sincronizar los equipos extender dual band
     *                          al realizar cambio de linea pon de servicios de internet
     * @since 1.9
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.11 04-09-2019 Se agrega proceso para validar promociones en servicios de clientes, en caso de 
     *                          que aplique a alguna promoción se le configurarán los anchos de bandas promocionales
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.12 04-05-2020 Se elimina la función obtenerInfoMapeoProdPrefYProdsAsociados y en su lugar se usa obtenerParametrosProductosTnGpon,
     *                           debido a los cambios realizados por la reestructuración de servicios Small Business
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.13 22-11-2020 Se agrega programación para ejecutar cambios de elementos de clientes pyme con IP FIJA WAN adicional
     * 
     * @since 1.10
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.14 17-12-2020 Se elimina la creación de la característica SERVICE-PROFILE cuando ya existe esta característica, para evitar 
     *                          duplicación de características
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.15 11-10-2021 Se agrega validación para los productos que tienen aprovisionamiento de ip privadas. 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.16 15-11-2021 Se construye el arreglo con la información que se enviará al invocar al web service para confirmación de 
     *                          opción de Tn a Middleware
     * 
     * @author Manuel Carpio <mcarpio@telconet.ec>
     * @version 1.17 13-09-2022 Se construye el arreglo con la información que se enviará al invocar al web service para confirmación de 
     *                          opción de Tn a Middleware se incluye al cambio de linea pon para entre OLT multiplataformas
     *                          pra servicio Safe City
     * 
     * @author Joel Muñoz <jrmunoz@telconet.ec>
     * @version 1.18 08-12-2022 Se corrigen validaciones ya que estaban generando error al intentar obtener propiedades de un objeto no definido
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.19 02-05-2023 Se agrega try-catch para validar la confirmación TN en caso de status ERROR y OK
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.19 02-05-2023 Se agrega try-catch para validar la confirmación TN en caso de status ERROR y OK
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 2.0 06-01-2023 - Se realizan las validaciones para anadir el servicio SAFECITYWIFI al request del consumo
     * 
     */
    public function cambiarPuertoScriptMdHuawei($arrayDatos)
    {
        $servicioId                 = $arrayDatos[0]['idServicio'];
        $interfaceElementoNuevoId   = $arrayDatos[0]['interfaceElementoId'];
        $empresa                    = $arrayDatos[0]['idEmpresa'];
        $elementoCajaId             = $arrayDatos[0]['elementoCajaId'];
        $elementoSplitterId         = $arrayDatos[0]['elementoSplitterId'];
        $interfaceElementoSplitterId= $arrayDatos[0]['interfaceElementoSplitterId'];
        $usrCreacion                = $arrayDatos[0]['usrCreacion'];
        $ipCreacion                 = $arrayDatos[0]['ipCreacion'];
        $idSolicitud                = $arrayDatos[0]['idSolicitud'];
        $strPrefijoEmpresa          = $arrayDatos[0]['prefijoEmpresa'];
        $objSession                 = $arrayDatos[0]['objSession'];
        $strTipoRed                 = $arrayDatos[0]['tipoRed'];        
        $valorTraffic               = '';
        $valorGemport               = '';
        $valorVlan                  = '';
        $valorLineProfile           = '';
        $spcSpid                    = '';
        $spcMacOnt                  = '';
        $servProdCaractIndiceCliente= '';
        $strOntId                   = '';
        $strSpid                    = '';
        $prodIpPlan                 = '';
        $flagMiddleware             = false;
        $intIdElementoOltNuevo      = 0;
        $flagProdViejo              = 0;
        $intContador                = 0;
        $strReutilizable            = '';
        $strExisteIpWan             = "NO";
        $arrayDatosIpWan            = array();
        $arrayCorreo                = array();
        $arrayProdIp                = array();
        $strIsbConIps               = 'NO';
        $arrayDatosGpon             = array();
        $arrayDataNoc               = $arrayDatos[0]['arrayDatosNoc'];
        $arrayDataConfirmacionTn    = array();
        $boolRedGponMpls            = false;

        if(isset($strTipoRed) && !empty($strTipoRed) && $strTipoRed === "GPON_MPLS")
        {
            $boolRedGponMpls = true;
        }
        
        $objSolicitud       = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                          ->findOneById($idSolicitud);

        $interfaceElementoNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                          ->findOneById($interfaceElementoNuevoId);
        $elementoNuevo = $interfaceElementoNuevo->getElementoId();
        //obtengo el modelo del elemento nuevo
        $objElementoNuevo      = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                         ->findOneById($interfaceElementoNuevo->getElementoId()->getId());
                                                         
        $modeloElementoNuevo   = $objElementoNuevo->getModeloElementoId();
        $intIdElementoOltNuevo = $objElementoNuevo->getId();

        if($boolRedGponMpls)
        {
            $arrayPeNuevo["intIdElemento"]  = $elementoNuevo->getId();
            $arrayPeNuevo["strUsrCreacion"] = $usrCreacion;
            $objElementoPeNuevo             = $this->servicioGeneral->getPeByOlt($arrayPeNuevo);

        }    

        $servicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                      ->findOneById($servicioId);
        
        $flagProdViejo                  = 0;
        $intIdPlanIpsDisponibleScopeOlt = 0;
        if(isset($arrayDatos[0]['esIsb']) && !empty($arrayDatos[0]['esIsb']) && $arrayDatos[0]['esIsb'] === "SI")
        {
            if(!is_object($servicio->getProductoId()))
            {
                $arrayRespuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe producto asociado al servicio');
                return $arrayRespuestaFinal;
            }
            $objProducto                = $servicio->getProductoId();
            $intIdProdPref              = $objProducto->getId();
            $strNombreTecnicoProdPref   = $objProducto->getNombreTecnico();
            if($strNombreTecnicoProdPref === "TELCOHOME")
            {
                $flagProdViejo          = 0;
                $strTipoNegocio         = "HOME";
            }
            else
            {
                $flagProdViejo          = 1;
                $strTipoNegocio         = "PYME";
                $arrayParamsInfoProds   = array("strValor1ParamsProdsTnGpon"    => "PRODUCTOS_RELACIONADOS_INTERNET_IP",
                                                "strCodEmpresa"                 => $empresa,
                                                "intIdProductoInternet"         => $intIdProdPref);
                $arrayInfoMapeoProds    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->obtenerParametrosProductosTnGpon($arrayParamsInfoProds);
                
                if(isset($arrayInfoMapeoProds) && !empty($arrayInfoMapeoProds))
                {
                    foreach($arrayInfoMapeoProds as $arrayInfoProd)
                    {
                        $intIdProductoIp        = $arrayInfoProd["intIdProdIp"];
                        $objProdIPSB            = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProductoIp);
                        $arrayProdIp[]          = $objProdIPSB;
                        $strIsbConIps           = "SI";
                    }
                }
                else
                {
                    $arrayRespuestaFinal[]  = array('status'    => 'ERROR', 
                                                    'mensaje'   => 'No se ha podido obtener el correcto mapeo del servicio con la ip respectiva');
                    return $arrayRespuestaFinal;
                }
            }
        }
        else if($boolRedGponMpls &&
            (isset($strPrefijoEmpresa) && !empty($strPrefijoEmpresa) && $strPrefijoEmpresa === "TN"))
        {
            $strTipoNegocio = $servicio->getPuntoId()->getTipoNegocioId()->getNombreTipoNegocio();
            $objProducto    = $servicio->getProductoId();
            $arrayProdIp[]  = $objProducto;
        }
        else
        {
            //OBTENER TIPO DE NEGOCIO
            $strTipoNegocio = $servicio->getPuntoId()->getTipoNegocioId()->getNombreTipoNegocio();
            $objProducto    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->findOneBy(array(  "nombreTecnico" => "INTERNET",
                                                                                                                    "empresaCod"    => $empresa,
                                                                                                                    "estado"        => "Activo"));
            $arrayProdIp    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                ->findBy(array( "nombreTecnico" => "IP",
                                                                "empresaCod"    => $empresa,
                                                                "estado"        => "Activo"));
        }
        $servicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                              ->findOneBy(array("servicioId" => $servicio->getId()));
        $objDetalleElementoMid  = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                        ->findOneBy(array(  "elementoId"   => $servicioTecnico->getElementoId(),
                                                            "detalleNombre"=> 'MIDDLEWARE',
                                                            "estado"       => 'Activo'));
        if($objDetalleElementoMid)
        {
            if($objDetalleElementoMid->getDetalleValor() == 'SI')
            {
                $flagMiddleware = true;
            }
        }
        $interfaceElementoViejo = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                          ->find($servicioTecnico->getInterfaceElementoId());
        //backbone viejo
        $elementoViejo = $interfaceElementoViejo->getElementoId();
        $elementoContenedorViejo = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                           ->find($servicioTecnico->getElementoContenedorId());
        $interfaceElementoConectorViejo = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                  ->find($servicioTecnico->getInterfaceElementoConectorId());
        $elementoConectorViejo = $interfaceElementoConectorViejo->getElementoId();

        if($boolRedGponMpls)
        {
            $arrayPeViejo["intIdElemento"]  = $elementoViejo->getId();
            $arrayPeViejo["strUsrCreacion"] = $usrCreacion;
            $objElementoPeViejo             = $this->servicioGeneral->getPeByOlt($arrayPeViejo);

        }
        //buscar caracteristicas para olt huawei
        //obtener ont
        $elementoCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                   ->findOneById($servicioTecnico->getElementoClienteId());
        if($elementoCliente)
        {
            $modeloOnt = $elementoCliente->getModeloElementoId()->getNombreModeloElemento();
            $serieOnt = $elementoCliente->getSerieFisica();
        }
        else
        {
            $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe el ont del cliente, favor revisar!');
            return $respuestaFinal;
        }
        $objSpcLineProfile = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "LINE-PROFILE-NAME", $objProducto);
        if(is_object($objSpcLineProfile))
        {
            $valorLineProfile = $objSpcLineProfile->getValor();
        }
        else
        {
            $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe Caracteristica LINE-PROFILE-NAME, favor revisar!');
            return $respuestaFinal;
        }
        $objSpcVlan = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "VLAN", $objProducto);
        if(is_object($objSpcVlan))
        {
            $valorVlan = $objSpcVlan->getValor();
        }
        else
        {
            $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe Caracteristica VLAN, favor revisar!');
            return $respuestaFinal;
        }
        $objSpcGemPort = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "GEM-PORT", $objProducto);
        if(is_object($objSpcGemPort))
        {
            $valorGemport = $objSpcGemPort->getValor();
        }
        else
        {
            $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe Caracteristica GEM-PORT, favor revisar!');
            return $respuestaFinal;
        }
        $objSpcTraffic = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "TRAFFIC-TABLE", $objProducto);
        if(is_object($objSpcTraffic))
        {
            $valorTraffic = $objSpcTraffic->getValor();
        }
        else
        {
            $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe Caracteristica TRAFFIC-TABLE, favor revisar!');
            return $respuestaFinal;
        }
        $spcSpid = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SPID", $objProducto);
        if(!$spcSpid)
        {
            $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe Caracteristica Spid, favor revisar!');
            return $respuestaFinal;
        }
        else
        {
            $strSpid = $spcSpid->getValor();
        }
        $spcMacOnt = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC ONT", $objProducto);
        if(!$spcMacOnt)
        {
            $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe Mac del Ont , favor revisar!');
            return $respuestaFinal;
        }        
        $servProdCaractIndiceCliente = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $objProducto);
        if(!$servProdCaractIndiceCliente)
        {
            $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe Caracteristica Indice, favor revisar!');
            return $respuestaFinal;
        }
        else
        {
            $strOntId = $servProdCaractIndiceCliente->getValor();
        }

        //obtengo el indice viejo del cliente
        $objIndiceClienteViejoSpc = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $objProducto);

        //obtener mac ont
        $objServProdCaractMacOnt = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC ONT", $objProducto);
        if($objServProdCaractMacOnt)
        {
            $strMacOnt = $objServProdCaractMacOnt->getValor();
        }
        else
        {
            $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE MAC ONT DEL CLIENTE,' . $servicio->getId());
            return $respuestaFinal;
        }


        //Se valida las caracteristicas de olt servcios principal datos gpon
        if($boolRedGponMpls && $servicio->getProductoId()->getNombreTecnico() === "DATOS SAFECITY")
        {

            $arrayDatosGpon = array('servicio' => $servicio,
                                    'producto' => $objProducto,
                                    'vlan'     => $valorVlan);
            $respuestaFinal[] = $this->getCaracteristicasDatosGpon($arrayDatosGpon);
            
            if($respuestaFinal[0]['status'] === 200 && count($respuestaFinal[0]['result']) > 0)
            {
                $strTnCont              = $respuestaFinal[0]['result']['TnContDatos'];
                $strTnMappingMonitoreo  = $respuestaFinal[0]['result']['MaMonitoreo'];
                $strVlanOnt             = $respuestaFinal[0]['result']['vlanDatos'];
            }
            else
            {
                $respuestaFinal[] = array('status' => $respuestaFinal[0]['status'], 'mensaje' => $respuestaFinal[0]['mensaje'] . $servicio->getId());
                return $respuestaFinal;
            }
        }

        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        try
        {
            if(!$boolRedGponMpls)
            {
                //verificar ip en el plan----------------------------------------------------------
                $planCabViejo = $servicio->getPlanId();
                if(is_object($planCabViejo))
                {
                    $planDetViejo = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')->findBy(array("planId" => $planCabViejo->getId()));

                    for($i = 0; $i < count($planDetViejo); $i++)
                    {
                        for($intIndiceProdIp = 0; $intIndiceProdIp < count($arrayProdIp); $intIndiceProdIp++)
                        {
                            if($planDetViejo[$i]->getProductoId() == $arrayProdIp[$intIndiceProdIp]->getId())
                            {
                                $prodIpPlan = $arrayProdIp[$intIndiceProdIp];
                                $flagProdViejo = 1;
                                break;
                            }
                        }
                    }
                }
            }

            if ($strPrefijoEmpresa === "MD" && $strTipoNegocio === "PYME" && $flagProdViejo === 0)
            {
                //OBTENER IPS ADICIONALES
                $arrayParametrosIpWan = array('objPunto'       => $servicio->getPuntoId(),
                                              'strEmpresaCod'  => $empresa,
                                              'strUsrCreacion' => $usrCreacion,
                                              'strIpCreacion'  => $ipCreacion);
                $arrayDatosIpWan      = $this->servicioGeneral
                                             ->getIpFijaWan($arrayParametrosIpWan);
                //SI EL SERVICIO TIENE IP EN EL PLAN
                if (isset($arrayDatosIpWan['strStatus']) && !empty($arrayDatosIpWan['strStatus']) && 
                    $arrayDatosIpWan['strStatus'] === 'OK' && isset($arrayDatosIpWan['strExisteIpWan']) &&
                    !empty($arrayDatosIpWan['strExisteIpWan']) &&  $arrayDatosIpWan['strExisteIpWan'] === 'SI')
                {
                    $strExisteIpWan = $arrayDatosIpWan['strExisteIpWan'];
                    $flagProdViejo  = 1;
                }
            }
            //----------------------------------------------------------------------------------
            //verificar si punto tiene ip adicional---------------------------------------------
            $serviciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                ->findBy(array("puntoId" => $servicio->getPuntoId()->getId()));
            $contIpsFijas = 0;
            $arrayServicioIp[] = array("idServicio" => "");
            $arrayServiciValidate    = array();
            for($i = 0; $i < count($serviciosPunto); $i++)
            {
                $servicioPunto = $serviciosPunto[$i];
                if(($servicioPunto->getEstado() == "Activo" || $servicioPunto->getEstado() == "In-Corte" ) &&
                    $servicioPunto->getId() != $servicio->getId())
                {
                    if($servicioPunto->getPlanId())
                    {
                        $planCab = $this->emComercial->getRepository('schemaBundle:InfoPlanCab')->find($servicioPunto->getPlanId()->getId());
                        $planDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')->findBy(array("planId" => $planCab->getId()));

                        for($intIndicePlanDet = 0; $intIndicePlanDet < count($planDet); $intIndicePlanDet++)
                        {
                            //contar las ip que estan en planes
                            foreach($arrayProdIp as $productoIp)
                            {
                                if($productoIp->getId() == $planDet[$intIndicePlanDet]->getProductoId())
                                {
                                    $arrayServicioIp[] = array("idServicio" => $servicioPunto->getId());
                                    $contIpsFijas++;
                                }
                            }
                        }
                    }
                    else
                    {
                        //contar las ip que estan como productos
                        $productoServicioPunto = $servicioPunto->getProductoId();
                        $arrayParametrosCaractIpWan = array( 'intIdProducto'         => $productoServicioPunto->getId(),
                                                             'strDescCaracteristica' => 'IP WAN',
                                                             'strEstado'             => 'Activo' );
                        $strValidaExisteIpWan = $this->serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaractIpWan);
                        if ($strValidaExisteIpWan === 'N')
                        {   
                            foreach($arrayProdIp as $productoIp)
                            {
                                if($productoIp->getId() == $productoServicioPunto->getId())
                                {
                                    $arrayServicioIp[] = array("idServicio" => $servicioPunto->getId());
                                    $arrayServicioIpProducto[] = array("idServicio" => $servicioPunto->getId());
                                    $contIpsFijas++;
                                }

                                if($productoServicioPunto->getNombreTecnico() == "SAFECITYDATOS" && $servicioPunto->getEstado() == "Activo")  
                                {
                                    $arrayServicioCamaras[]    = $servicioPunto;
                                    $arrayServicioIpProducto[] = array("idServicio" => $servicioPunto->getId());
                                    array_push($arrayServiciValidate,$servicioPunto->getId());
                                    $contIpsFijas++;
                                }
                                elseif( $productoServicioPunto->getNombreTecnico = 'SAFECITYWIFI'  && $servicioPunto->getEstado() == "Activo")
                                {
                                    array_push($arrayServiciValidate,$servicioPunto->getId());
                                }  
                            }
                        }
                    }
                }
            }
            //----------------------------------------------------------------------------------
            //solicitar las ips necesarias
            $totalIpPto = $contIpsFijas + $flagProdViejo;
            
            if(!$boolRedGponMpls)
            {
                //consultar si el olt va a migrar sin ips
                $objDetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                    ->findOneBy(array( "elementoId"    => $servicioTecnico->getElementoId(),
                                                                        "detalleNombre" => "MIGRACION_NODO",
                                                                    "estado"        => "Activo"));   
            }         
            if($objDetalleElemento)
            {
                $totalIpPto = 0;
                
                    for($i = 0; $i < count($arrayServicioIpProducto); $i++)
                    {
                        $servicioTecnicoIpProd = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                                         ->findOneBy(array("servicioId" => $arrayServicioIpProducto[$i]));

                        if($servicioTecnicoIpProd->getInterfaceElementoClienteId())
                        {
                            $interfaceCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                        ->find($servicioTecnicoIpProd->getInterfaceElementoClienteId());
                        }
                        else
                        {
                            $arrayFinal[] = array('status' => "ERROR", 'mensaje' => "ERROR DATA: Asignar puerto de ont a la Ip Adicional. ");
                            return $arrayFinal;
                        }
                    }                
            }

            if($flagMiddleware)
            {
                $arrayIpCancelar    = array();
                $arrayIpActivar     = array();
                $arrayRespuestaNoc  = array();
                $intIpsFijasActivas = 0;
                $strScopeNuevo      = '';
                $strIpFija          = '';
                $scope              = '';
                $strIpElementoNuevo = 0;
                $strInterfaceNuevo  = '';
                $macWifi            = '';
                $strIpNuevaPlan     = '';
                $spcScope           = null;
                //OBTENER SERVICE-PROFILE
                $objSpcServiceProf = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SERVICE-PROFILE", $objProducto);
                if(!is_object($objSpcServiceProf))
                {
                    $elementoClienteAnterior = $this->emComercial->getRepository('schemaBundle:InfoElemento')
                                                    ->find($servicioTecnico->getElementoClienteId());
                    $this->servicioGeneral
                         ->ingresarServicioProductoCaracteristica($servicio, $objProducto, "SERVICE-PROFILE",
                                                                  $elementoClienteAnterior->getModeloElementoId()->getNombreModeloElemento(),
                                                                  $usrCreacion);
                    $strServiceProfile = $elementoClienteAnterior->getModeloElementoId()->getNombreModeloElemento();
                }
                else
                {
                    $strServiceProfile = $objSpcServiceProf->getValor();
                }
                //obtener la ip del olt anterior
                $objIpElementoViejo = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                            ->findOneBy(array("elementoId" => $elementoViejo->getId()));
                
                //OBTENER DATOS DEL CLIENTE (NOMBRES E IDENTIFICACION)
                $objPersona         = $servicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
                $strIdentificacion  = $objPersona->getIdentificacionCliente();
                $strNombreCliente   = ($objPersona->getRazonSocial() != "") ? $objPersona->getRazonSocial() : 
                                                        $objPersona->getNombres()." ".$objPersona->getApellidos();
                
                //Si el producto es Internet Small Business y si la ip es publica, consultar si tiene producto adicional, caso contrario
                //agregar un servicio adicional (IP Small Business)
                $boolCrearServicio = false;
                $boolIsb           = false;
                if ($strPrefijoEmpresa === "TN" && !$boolRedGponMpls)
                {
                    $arrayParametrosCaract    = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                            ->getOne("IP_PRIVADA_GPON_CARACTERISTICAS",
                                                                                     "COMERCIAL",
                                                                                     "",
                                                                                     "",
                                                                                     $servicio->getProductoId()->getDescripcionProducto(),
                                                                                     "",
                                                                                     "",
                                                                                     "",
                                                                                     "",
                                                                                     $empresa);
                }    
                if(isset($arrayParametrosCaract['valor2']) && !empty($arrayParametrosCaract['valor2']))
                {
                    $strCaractIsb = $arrayParametrosCaract['valor2'];
                    $boolIsb      = true;
                }
                    
                if ($strNombreTecnicoProdPref === "INTERNET SMALL BUSINESS" 
                                                        && $strPrefijoEmpresa ==="TN"
                                                        && $boolIsb)
                {
                    $intIdServicioIp = $servicio->getId();
                    //Obtiene tipo de ip por el servicio
                    $objTipoIpOrigen = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                   ->findOneBy(array("servicioId"  =>  $intIdServicioIp,
                                                                                         "tipoIp"      =>  "FIJA",
                                                                                         "estado"      =>  "Activo"));
                    if (is_object($objTipoIpOrigen))
                    {
                        $strTipoIpOrigen = $objTipoIpOrigen->getTipoIp();
                    }
                                                
                    if ($strTipoIpOrigen === "FIJA")
                    {
                        $strTieneIps = "NO";
                            
                        $arrayProdIp  = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                          ->findBy(array( "descripcionProducto" => "IP Small Business",
                                                                          "nombreTecnico"       => "IPSB", 
                                                                          "empresaCod"          => "10",
                                                                          "estado"              => "Activo"));
                        if(empty($arrayProdIp))
                        {
                            throw new \Exception("No existe el objeto del producto IP");
                        }
                        
                        //arreglo de los estados de los servicios permitidos
                        $arrayEstadosServiciosPermitidos = array();
                        //obtengo la cabecera de los estados de los servicios permitidos
                        $objAdmiParametroCabEstadosServ  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                                    array('nombreParametro' => 'ESTADOS_SERVICIOS_ISB_CAMBIO_PUERTO',
                                                                          'estado'          => 'Activo'));
                        if( is_object($objAdmiParametroCabEstadosServ) )
                        {
                            $arrayParametrosDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                                                    array(  "parametroId" => $objAdmiParametroCabEstadosServ->getId(),
                                                                            "estado"      => "Activo"));
                            foreach($arrayParametrosDet as $objParametro)
                            {
                                $arrayEstadosServiciosPermitidos[] = $objParametro->getValor1();
                            }
                        }                           
                        $objProductoOrigen          = $servicio->getProductoId();
                        $arrayServiciosPuntoOrigen  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                ->findBy(array( "puntoId" => $servicio->getPuntoId()->getId(), 
                                                                                "estado" => $arrayEstadosServiciosPermitidos));
                            
                        //Consultamos si tiene ips adicionales el servicio de origen
                        $arrayParametrosIsb = array("arrayServicios"                  => $arrayServiciosPuntoOrigen,
                                                    "arrayProdIp"                     => $arrayProdIp,
                                                    "servicio"                        => $objTipoIpOrigen,
                                                    "objProductoInternet"             => $objProductoOrigen,
                                                    "estadoIp"                        => 'Activo',
                                                    "arrayEstadosServiciosPermitidos" => $arrayEstadosServiciosPermitidos
                                                    );
                        
                        $arrayDatosIpPyme   = $this->servicioGeneral->getInfoIpsFijaPuntoIsb($arrayParametrosIsb);
                        //Obtener la cantidad de ips adicionales
                        $intIpsFijasActivasPyme = $arrayDatosIpPyme['ip_fijas_activas'];
                        if($intIpsFijasActivasPyme > 0)
                        {
                            $strTieneIps = "SI";
                        }
                            
                        if ($strTieneIps === "NO")
                        {
                            $objProductoIp = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                            ->findOneBy(array(  "descripcionProducto"   => "IP Small Business",
                                                                                "empresaCod"            => $empresa,
                                                                                "estado"                => "Activo"));
                            $intIdProdIp             = $objProductoIp->getId();
                            $strDescripcionProdIp    = $objProductoIp->getDescripcionProducto();
                            $strLoginVendedor        = $servicio->getUsrVendedor();
                                                                
                            $objInfoPersona          = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                                ->findOneBy(array('login'=>$strLoginVendedor));
                            $strVendedor             = "";

                            if(is_object($objInfoPersona))
                            {
                                $strNombres   = ucwords(strtolower($objInfoPersona->getNombres()));
                                $strApellidos = ucwords(strtolower($objInfoPersona->getApellidos()));
                                $strVendedor  = $strNombres.' '.$strApellidos;
                                $intIdPersona = $objInfoPersona->getId();
                            }
                                
                            $objPersonaEmpresaRol   = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                        ->findBy(array(
                                                                                "personaId" => $intIdPersona
                                                                            ));
                            
                            $intIdPersonaRol = '';                                                    
                            foreach($objPersonaEmpresaRol as $objParametroRol)
                            {
                                $intIdEmpresaRol    = $objParametroRol->getEmpresaRolId()->getId();
                                //Consultamos si el id de la empresa_rol es de TN
                                $objEmpresaRol   = $this->emComercial->getRepository('schemaBundle:InfoEmpresaRol')
                                                               ->findOneBy(array(
                                                                                "id"         => $intIdEmpresaRol,
                                                                                "empresaCod" => $empresa
                                                                            ));
                                if (is_object($objEmpresaRol))
                                {
                                    $intIdPersonaRol = $intIdEmpresaRol;
                                    break;
                                }
                                    
                            }
                            
                            if(empty($intIdPersonaRol))
                            {
                                throw new \Exception("el Id de la empresa rol no pertenece a la empresa TN");
                            }
                                                        
                            $objCaractVelocidad      = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                            ->findOneBy(array("descripcionCaracteristica" => 'VELOCIDAD', "estado" => "Activo"));
                            $objProdCaracVelocidad   = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                            ->findOneBy(array(  "productoId"        => $servicio->getProductoId(),
                                                                                "caracteristicaId"  => $objCaractVelocidad->getId(),
                                                                                "estado"            => "Activo"));
                            $objSpcServicioVelocidad = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                   ->findOneBy(array(   "servicioId"                => 
                                                                                        $servicio->getId(),
                                                                                        "productoCaracterisiticaId" =>
                                                                                        $objProdCaracVelocidad->getId(),
                                                                                        "estado"        => "Activo"));
                            $strVelocidad            = $objSpcServicioVelocidad->getValor();
                            $arrayProductoCaracteristicasValores['VELOCIDAD'] = $strVelocidad;
                            $strFuncionPrecio        = $objProductoIp->getFuncionPrecio();
                            $strPrecioVelocidad      = $this->evaluarFuncionPrecio($strFuncionPrecio, $arrayProductoCaracteristicasValores);
                                
                            $arrayPlantillaProductos  = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                             ->getResultadoComisionPlantilla( array('intIdProducto' => $intIdProdIp,
                                                                                                      'strCodEmpresa' => $empresa) );
                            if (isset($arrayPlantillaProductos['objRegistros']) && !empty($arrayPlantillaProductos['objRegistros']))
                            {
                                foreach($arrayPlantillaProductos['objRegistros'] as $arrayItem)
                                {
                                    if (isset($arrayItem['idComisionDet']) && !empty($arrayItem['idComisionDet']))
                                    {
                                        $intIdComisionDet = (isset($arrayItem['idComisionDet']) && !empty($arrayItem['idComisionDet']))
                                                                ? $arrayItem['idComisionDet'] : 0;
                                    }
                                }
                            }
                                
                            $strPlantillaComisionista = $intIdComisionDet.'---'.$intIdPersonaRol;
                                
                            //Se crea el servicio adicional para este producto
                            $arrayServicios = array();
                            $arrayServicios[0]['hijo']                          = 0;
                            $arrayServicios[0]['servicio']                      = 0;
                            $arrayServicios[0]['codigo']                        = $intIdProdIp;
                            $arrayServicios[0]['producto']                      = $strDescripcionProdIp.' '.$strVelocidad.' 0';
                            $arrayServicios[0]['cantidad']                      = '1';
                            $arrayServicios[0]['frecuencia']                    = '1';
                            $arrayServicios[0]['precio']                        = $strPrecioVelocidad;
                            $arrayServicios[0]['precio_total']                  = $strPrecioVelocidad;
                            $arrayServicios[0]['info']                          = 'C';
                            $arrayServicios[0]['caracteristicasProducto']       = $strCaractIsb;
                            $arrayServicios[0]['caractCodigoPromoIns']          = '';
                            $arrayServicios[0]['nombrePromoIns']                = '';
                            $arrayServicios[0]['idTipoPromoIns']                = '';
                            $arrayServicios[0]['caractCodigoPromo']             = '';
                            $arrayServicios[0]['nombrePromo']                   = '';
                            $arrayServicios[0]['idTipoPromo']                   = '';
                            $arrayServicios[0]['caractCodigoPromoBw']           = '';
                            $arrayServicios[0]['nombrePromoBw']                 = '';
                            $arrayServicios[0]['idTipoPromoBw']                 = '';
                            $arrayServicios[0]['strServiciosMix']               = '';
                            $arrayServicios[0]['tipoMedio']                     = '';
                            $arrayServicios[0]['backupDesc']                    = '';
                            $arrayServicios[0]['fecha']                         = '';
                            $arrayServicios[0]['precio_venta']                  = $strPrecioVelocidad;
                            $arrayServicios[0]['precio_instalacion']            = '0';
                            $arrayServicios[0]['descripcion_producto']          = $strDescripcionProdIp.' '.$strVelocidad.' 0';
                            $arrayServicios[0]['precio_instalacion_pactado']    = '0';
                            $arrayServicios[0]['ultimaMilla']                   = '107';
                            $arrayServicios[0]['um_desc']                       = 'FTTx';
                            $arrayServicios[0]['login_vendedor']                = $strLoginVendedor;
                            $arrayServicios[0]['nombre_vendedor']               = $strVendedor;
                            $arrayServicios[0]['strPlantillaComisionista']      = $strPlantillaComisionista;
                            $arrayServicios[0]['cotizacion']                    = '';
                            $arrayServicios[0]['cot_desc']                      = 'Ninguna';
                            $arrayServicios[0]['intIdPropuesta']                = '';
                            $arrayServicios[0]['strPropuesta']                  = '';
                                
                            $objPuntoDestino = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($servicio
                                                                                                          ->getPuntoId()->getId());
                            $objRol   = null;

                            if (is_object($objPuntoDestino))
                            {
                                $objRol = $this->emComercial->getRepository('schemaBundle:AdmiRol')
                                                          ->find($objPuntoDestino->getPersonaEmpresaRolId()->getEmpresaRolId()->getRolId());
                            }
                            $arrayParamsServicio = array(   "codEmpresa"            => $empresa,
                                                    "idOficina"             => $objSession->get('idOficina'),
                                                    "entityPunto"           => $objPuntoDestino,
                                                    "entityRol"             => $objRol,
                                                    "usrCreacion"           => $usrCreacion,
                                                    "clientIp"              => $ipCreacion,
                                                    "tipoOrden"             => 'N',
                                                    "ultimaMillaId"         => null,
                                                    "servicios"             => $arrayServicios,
                                                    "strPrefijoEmpresa"     => $strPrefijoEmpresa,
                                                    "session"               => $objSession,
                                                    "intIdSolFlujoPP"       => $objSession->get('idSolFlujoPrePlanificacion') 
                                                                               ? $objSession->get('idSolFlujoPrePlanificacion') : 0
                                            );
                            $boolCrearServicio = true;
                        }
                    }
                }
                
                //DIFERENTES ELEMENTOS
                if($elementoNuevo->getId() != $elementoViejo->getId())
                {
                    //PUNTO TIENE IP
                    if($totalIpPto > 0)
                    {

                        $arrServiciosPunto      = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                        ->findBy(array("puntoId" => $servicio->getPuntoId(), "estado" => "Activo"));

                        //OBTENER IPS ADICIONALES A CANCELAR
                        $arrayDatosIpCancelar   = $this->servicioGeneral
                                                    ->getInfoIpsFijaPunto(  $arrServiciosPunto, $arrayProdIp, 
                                                                            $servicio, 'Activo', 'Activo', $objProducto);

                        $arrayIpCancelar        = $arrayDatosIpCancelar['valores'];

                        //OBTENER LA CANTIDAD DE IPS ADICIONALES ACTIVAS
                        $intIpsFijasActivas     = $arrayDatosIpCancelar['ip_fijas_activas'];
                        if(!$boolRedGponMpls)
                        {                           
                            if(is_object($servicio->getPlanId()))
                            {
                                $intIdPlanIpsDisponibleScopeOlt = $servicio->getPlanId()->getId();
                            }
                           //OBTENER IPS ADICIONALES A ACTIVAR---------------------------------------------------------------------------------
                            $arregloIps = $this->recursosRed->getIpsDisponibleScopeOlt( $totalIpPto, 
                                                                                        $elementoNuevo->getId(), 
                                                                                        $servicio->getId(), 
                                                                                        $servicio->getPuntoId()->getId(), 
                                                                                        "SI", 
                                                                                        $intIdPlanIpsDisponibleScopeOlt);                            
                            if($arregloIps['error'])
                            {
                                $arrayFinal[] = array('status' => "ERROR", 'mensaje' => $arregloIps['error']);

                                $punto = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($servicio->getPuntoId());
                                //Envío de notificación de creación de errores
                                /* @var $envioPlantilla EnvioPlantilla */
                                $asunto     = "Notificación de errores al activar cambio de línea Pon";
                                $parametros = array('login' => $punto->getLogin(),
                                                    'olt'   => $elementoNuevo->getNombreElemento(),
                                                    'error' => $arregloIps['error']);
                                $this->correo->generarEnvioPlantilla($asunto, $to, 'ECLP', $parametros, '', '', '');
                                return $arrayFinal;
                            }
                        }                        
                        
                        //SI EL SERVICIO TIENE IP EN EL PLAN
                        if($flagProdViejo == 1)
                        {
                            if ($strExisteIpWan === "SI")
                            {
                                $arrayServicioIp[] = array("idServicio" => $arrayDatosIpWan['arrayInfoIp']['intIdServicioIp']);
                                $strIpFija         = $arrayDatosIpWan['arrayInfoIp']['strIp'];
                                $scope             = $arrayDatosIpWan['arrayInfoIp']['strScope'];
                            }
                            else 
                            {
                                $arrayServicioIp[] = array("idServicio" => $servicio->getId());
                                
                                //OBTENER IP DEL PLAN
                                $ipFija     = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                   ->findOneBy(array("servicioId"=>$servicio->getId(),"estado"=>"Activo"));

                                $strIpFija  = $ipFija->getIp();
                                
                                if(isset($arrayDatos[0]['esIsb']) && !empty($arrayDatos[0]['esIsb']) && $arrayDatos[0]['esIsb'] === "SI")
                                {
                                    $prodIpPlan = $objProducto;
                                }
                                //OBTENER SCOPE
                                $spcScope = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SCOPE", $prodIpPlan);

                                if(!$spcScope)
                                {
                                    //buscar scopes
                                    $arrayScopeOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                             ->getScopePorIpFija($ipFija->getIp(), $servicioTecnico->getElementoId());

                                    if (!$arrayScopeOlt)
                                    {   
                                        $arrayFinal[] = array('status'  => "ERROR",
                                                              'mensaje' => "Ip Fija no pertenece a un Scope! <br>".
                                                                           "Favor Comunicarse con el Dep. Gepon!");
                                        return $arrayFinal;
                                    }

                                    $scope = $arrayScopeOlt['NOMBRE_SCOPE'];
                                }
                                else
                                {
                                    $scope = $spcScope->getValor();
                                }
                            }
                        }                       
                        
                        $arrayIps       = $arregloIps['ips'];
                        //CONSTRUIR ARREGLO PARA ACTIVAR IPS ADICIONALES                       
                        if(!$boolRedGponMpls)
                        {
                            $i = 0;
                            foreach($arrayIps as $arrIpData)
                            {
                                if($i == 0 && $flagProdViejo == 1)
                                {
                                    if ($strExisteIpWan === "NO")
                                    {
                                        $objSpcMac = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC ONT", $objProducto);
                                        if(!is_object($objSpcMac))
                                        {
                                            $objSpcMac = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC", $objProducto);
                                            if(!is_object($objSpcMac))
                                            {
                                                $objSpcMac = $this->servicioGeneral
                                                                  ->getServicioProductoCaracteristica($servicio, "MAC WIFI", $objProducto);
                                                if(!is_object($objSpcMac))
                                                {
                                                    $respuestaFinal[] = array(  'status' => 'ERROR', 
                                                                                'mensaje' => 'No existe Mac asociado a un Servicio, favor revisar!');
                                                    return $respuestaFinal;
                                                }
                                            }
                                        }
                                    }
                                    $strIpNuevaPlan = $arrIpData['ip'];
                                    $strScopeNuevo  = $arrIpData['scope'];
                                } 
                                else
                                {
                                    $objServicioIp = $this->emInfraestructura->getRepository('schemaBundle:InfoServicio')
                                                                             ->find($arrayServicioIp[$i]['idServicio']);
                                    
                                    $objSpcMacInterna = $this->servicioGeneral
                                                             ->getServicioProductoCaracteristica($objServicioIp, "MAC ONT", $objProducto);
                                    if(!is_object($objSpcMacInterna))
                                    {
                                        $objSpcMacInterna = $this->servicioGeneral
                                                                 ->getServicioProductoCaracteristica($objServicioIp, "MAC", $objProducto);
                                        if(!is_object($objSpcMacInterna))
                                        {
                                            $objSpcMacInterna = $this->servicioGeneral
                                                                     ->getServicioProductoCaracteristica($objServicioIp, "MAC WIFI", $objProducto);
                                            if(!is_object($objSpcMacInterna))
                                            {
                                                $respuestaFinal[] = array(  'status' => 'ERROR', 
                                                                            'mensaje' => 'No existe Mac asociado a un Servicio, favor revisar!');
                                                return $respuestaFinal;
                                            }
                                        }
                                    }
    
                                    $strMac         = $objSpcMacInterna->getValor();
                                    $strIp          = $arrIpData['ip'];
                                    $intIdservicio  = $arrayServicioIp[$i]['idServicio'];
                                    
                                    $arrayIpActivar[] = array(
                                                            'mac'           => $strMac,
                                                            'ip'            => $strIp,
                                                            'id_servicio'   => $intIdservicio
                                                           );
                                }                               
                                $i++;
                            }
                        }                       
                        //---------------------------------------------------------------------------------------------------------------
                    }
                    
                    //OBTENER IP ELEMENTO NUEVO
                    $objIpElementoNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                ->findOneBy(array("elementoId" => $elementoNuevo->getId()));
                    $strIpElementoNuevo = $objIpElementoNuevo->getIp();
                    
                    //OBTENER MODELO DE ELEMENTO NUEVO
                    $strModeloNuevo = $elementoNuevo->getModeloElementoId()->getNombreModeloElemento();
                }
                else
                {
                    if ($strNombreTecnicoProdPref === "INTERNET SMALL BUSINESS" 
                                                        && $strPrefijoEmpresa ==="TN"
                                                        && $boolIsb)
                    {
                         if ($strTipoIpOrigen === "FIJA")
                         {
                            if(is_object($servicio->getPlanId()))
                            {
                                $intIdPlanIpsDisponibleScopeOlt = $servicio->getPlanId()->getId();
                            }

                            //OBTENER IPS ADICIONALES A ACTIVAR---------------------------------------------------------------------------------
                            $arregloIps = $this->recursosRed->getIpsDisponibleScopeOlt( $totalIpPto, 
                                                                                        $elementoViejo->getId(), 
                                                                                        $servicio->getId(), 
                                                                                        $servicio->getPuntoId()->getId(), 
                                                                                        "SI", 
                                                                                        $intIdPlanIpsDisponibleScopeOlt);

                            if($arregloIps['error'])
                            {
                                $arrayFinal[] = array('status' => "ERROR", 'mensaje' => $arregloIps['error']);

                                $objPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($servicio->getPuntoId());
                                //Envío de notificación de creación de errores
                                /* @var $envioPlantilla EnvioPlantilla */
                                $strAsunto        = "Notificación de errores al activar cambio de línea Pon";
                                $arrayParamCorreo = array('login' => $objPunto->getLogin(),
                                                    'olt'   => $elementoViejo->getNombreElemento(),
                                                    'error' => $arregloIps['error']);
                                $this->correo->generarEnvioPlantilla($strAsunto, $strTo, 'ECLP', $arrayParamCorreo, '', '', '');
                                return $arrayFinal;
                            }
                            
                            //SI EL SERVICIO TIENE IP EN EL PLAN
                            if($flagProdViejo == 1)
                            {
                                if ($strExisteIpWan === "SI")
                                {
                                    $arrayServicioIp[] = array("idServicio" => $arrayDatosIpWan['arrayInfoIp']['intIdServicioIp']);
                                    $strIpFija         = $arrayDatosIpWan['arrayInfoIp']['strIp'];
                                    $scope             = $arrayDatosIpWan['arrayInfoIp']['strScope'];
                                }
                                else 
                                {
                                    $arrayServicioIp[] = array("idServicio" => $servicio->getId());

                                    //OBTENER IP DEL PLAN
                                    $objIpFija  = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                       ->findOneBy(array("servicioId"=>$servicio->getId(),"estado"=>"Activo"));

                                    $strIpFija  = $objIpFija->getIp();

                                    if(isset($arrayDatos[0]['esIsb']) && !empty($arrayDatos[0]['esIsb']) && $arrayDatos[0]['esIsb'] === "SI")
                                    {
                                        $prodIpPlan = $objProducto;
                                    }
                                    //OBTENER SCOPE
                                    $spcScope = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SCOPE", $prodIpPlan);

                                    if(!$spcScope)
                                    {
                                        //buscar scopes
                                        $arrayScopeOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                              ->getScopePorIpFija($objIpFija->getIp(), $servicioTecnico->getElementoId());

                                        if (!$arrayScopeOlt)
                                        {   
                                            $arrayFinal[] = array('status'  => "ERROR",
                                                                  'mensaje' => "Ip Fija no pertenece a un Scope! <br>".
                                                                               "Favor Comunicarse con el Dep. Gepon!");
                                            return $arrayFinal;
                                        }

                                        $scope = $arrayScopeOlt['NOMBRE_SCOPE'];
                                    }
                                    else
                                    {
                                        $scope = $spcScope->getValor();
                                    }
                                }
                            }
                            
                            $arrayIps       = $arregloIps['ips'];
                        
                            //CONSTRUIR ARREGLO PARA ACTIVAR IPS ADICIONALES
                            $intI = 0;
                            foreach($arrayIps as $arrIpData)
                            {
                                if($intI == 0 && $flagProdViejo == 1)
                                {
                                    if ($strExisteIpWan === "NO")
                                    {
                                        $objSpcMac = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC ONT", $objProducto);
                                        if(!is_object($objSpcMac))
                                        {
                                            $objSpcMac = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC", $objProducto);
                                            if(!is_object($objSpcMac))
                                            {
                                                $objSpcMac = $this->servicioGeneral
                                                                  ->getServicioProductoCaracteristica($servicio, "MAC WIFI", $objProducto);
                                                if(!is_object($objSpcMac))
                                                {
                                                    $respuestaFinal[] = array(  'status' => 'ERROR', 
                                                                                'mensaje' => 'No existe Mac asociado a un Servicio, favor revisar!');
                                                    return $respuestaFinal;
                                                }
                                            }
                                        }
                                    }
                                    $strIpNuevaPlan = $arrIpData['ip'];
                                    $strScopeNuevo  = $arrIpData['scope'];
                                }
                                else
                                {
                                    $objServicioIp = $this->emInfraestructura->getRepository('schemaBundle:InfoServicio')
                                                                             ->find($arrayServicioIp[$intI]['idServicio']);

                                    $objSpcMacInterna = $this->servicioGeneral
                                                             ->getServicioProductoCaracteristica($objServicioIp, "MAC ONT", $objProducto);
                                    if(!is_object($objSpcMacInterna))
                                    {
                                        $objSpcMacInterna = $this->servicioGeneral
                                                                 ->getServicioProductoCaracteristica($objServicioIp, "MAC", $objProducto);
                                        if(!is_object($objSpcMacInterna))
                                        {
                                            $objSpcMacInterna = $this->servicioGeneral
                                                                     ->getServicioProductoCaracteristica($objServicioIp, "MAC WIFI", $objProducto);
                                            if(!is_object($objSpcMacInterna))
                                            {
                                                $respuestaFinal[] = array(  'status' => 'ERROR', 
                                                                            'mensaje' => 'No existe Mac asociado a un Servicio, favor revisar!');
                                                return $respuestaFinal;
                                            }
                                        }
                                    }

                                    $strMac         = $objSpcMacInterna->getValor();
                                    $strIp          = $arrIpData['ip'];
                                    $intIdservicio  = $arrayServicioIp[$intI]['idServicio'];

                                    $arrayIpActivar[] = array(
                                                            'mac'           => $strMac,
                                                            'ip'            => $strIp,
                                                            'id_servicio'   => $intIdservicio
                                                           );
                                }

                                $intI++;
                            }
                            
                            //OBTENER IP ELEMENTO NUEVO
                            $objIpElementoNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                        ->findOneBy(array("elementoId" => $elementoViejo->getId()));
                            $strIpElementoNuevo = $objIpElementoNuevo->getIp();
                            
                            //OBTENER INTERFACE NUEVO
                            $intIpsFijasActivas = $totalIpPto -1;

                            //OBTENER MODELO DE ELEMENTO NUEVO
                            $strModeloNuevo     = $elementoViejo->getModeloElementoId()->getNombreModeloElemento();
                         }
                         else
                         {
                            $strIpElementoNuevo = $objIpElementoViejo->getIp();
                    
                            //OBTENER INTERFACE NUEVO
                            $intIpsFijasActivas = $totalIpPto -1;
                    
                            //OBTENER MODELO DE ELEMENTO NUEVO
                            $strModeloNuevo     = $elementoViejo->getModeloElementoId()->getNombreModeloElemento();
                         }
                    }
                    else
                    {
                        $strIpElementoNuevo = $objIpElementoViejo->getIp();
                    
                        //OBTENER INTERFACE NUEVO
                        $intIpsFijasActivas = $totalIpPto -1;
                    
                        //OBTENER MODELO DE ELEMENTO NUEVO
                        $strModeloNuevo     = $elementoViejo->getModeloElementoId()->getNombreModeloElemento();
                    }
                }
                
                //OBTENER INTERFACE NUEVO
                $strInterfaceNuevo  = $interfaceElementoNuevo->getNombreInterfaceElemento();
                
                if ($strPrefijoEmpresa == 'TNP' && $strIsbConIps === 'NO')
                {
                    $strTipoNegocio = 'HOME';
                }

                if ($strPrefijoEmpresa == 'TN' && $boolRedGponMpls)
                {
                    $arrayResponseCan = array();
                    $strReutilizable  = ""; 
                    $arrayParametrosGdaDatos = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne("NUEVA_RED_GPON_MPLS_TN",
                                                            "TECNICO",
                                                            "",
                                                            "PARAMETROS PARA WS de GDA - Cambio de linea pon",
                                                            "CAMBIO_LINEA_PON_DATOS_SERVICIOS",
                                                            "",
                                                            "",
                                                            "",
                                                            "",
                                                            $empresa);

                    $arrayParametrosGdaDatosNw = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne("NUEVA_RED_GPON_MPLS_TN",
                                                                    "TECNICO",
                                                                    "",
                                                                    "PARAMETROS PARA WS de GDA - Cambio de linea pon",
                                                                    "CAMBIO_LINEA_PON_DATOS_NW",
                                                                    "",
                                                                    "",
                                                                    "",
                                                                    "",
                                                                    $empresa);


                    $arrayParametrosGdaDatosAdd = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne("NUEVA_RED_GPON_MPLS_TN",
                                                            "TECNICO",
                                                            "",
                                                            "PARAMETROS PARA WS de GDA - Cambio de linea pon",
                                                            "CAMBIO_LINEA_PON_DATOS_SERVICIOS_2",
                                                            "",
                                                            "",
                                                            "",
                                                            "",
                                                            $empresa);
                    foreach($serviciosPunto as $objServiciosPunto)
                    {

                        $objServicio = $objServiciosPunto;
                        $objProductoService = $objServicio->getProductoId();
                        
                        if(($objProductoService->getNombreTecnico() == "SAFECITYDATOS" 
                            || $objProductoService->getNombreTecnico()== 'SAFECITYWIFI')
                            && $objServicio->getEstado() == "Activo")
                        {
                            
                            if($elementoNuevo->getId() != $elementoViejo->getId() && $objProductoService->getNombreTecnico() == 'SAFECITYDATOS')
                            {
                                //Obtenemos la subred y gateway de elmento actula
                                $arrayIpViejo = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                ->getIpViejasPorServicio(array("idServicio" => $objServicio->getId()));

                                if(count($arrayIpViejo) > 0 && is_array($arrayIpViejo))
                                {
                                    $objIpActual = $arrayIpViejo['objSubred'];
                                }

                                $arrayIps = $this->servicioGeneral
                                                    ->getIpDisponiblePorServicio(array( "objServicio"    => $objServicio,
                                                                                        "strCodEmpresa"  => $empresa,
                                                                                        "strUsrCreacion" => $usrCreacion,
                                                                                        "strIpCreacion"  => $ipCreacion,
                                                                                        "objNuevoOlt"    => $objElementoNuevo,
                                                                                        "flag"           => "CLP"));

                                if($arrayIps['status'] != "OK")
                                {
                                    throw new \Exception($arrayIps['mensaje']);
                                }
                                $objSubredServicioAdd = $arrayIps['objSubred'];
                                $strIpServicioAdd     = $arrayIps['strIpServicio'];
                                                                        
                                //se graba la nuevas ips del servicio adicional de camaras
                                $objIpSerAdd = new InfoIp();
                                $objIpSerAdd->setElementoId($intIdElemento);
                                $objIpSerAdd->setIp($strIpServicioAdd);
                                $objIpSerAdd->setSubredId($objSubredServicioAdd->getId());
                                $objIpSerAdd->setServicioId($objServicio->getId());
                                $objIpSerAdd->setMascara($objSubredServicioAdd->getMascara());
                                $objIpSerAdd->setGateway($objSubredServicioAdd->getMascara());
                                $objIpSerAdd->setFeCreacion(new \DateTime('now'));
                                $objIpSerAdd->setUsrCreacion($usrCreacion);
                                $objIpSerAdd->setIpCreacion($ipCreacion);
                                $objIpSerAdd->setEstado("Reservada");
                                $objIpSerAdd->setTipoIp("LAN");
                                $objIpSerAdd->setVersionIp("IPV4");
                                $this->emInfraestructura->persist($objIpSerAdd);
                                $this->emInfraestructura->flush();

                                $arrayIpActivar[] = array( "subRedId"      => $objSubredServicioAdd->getId(),
                                                        "ip"            => $arrayIps["strIpServicio"],
                                                        "id_servicio"   => $objServicio->getId(),
                                                        "esatdo"        => $objServicio->getEstado(),
                                                        "mascara"       => $objSubredServicioAdd->getMascara(),
                                                        "intIdSpcScope" => 0,
                                                        "vlan_datos"    => $arrayResponseCan['vlan'],
                                                        "gateway"       => $objSubredServicioAdd->getGateway(),
                                                        "productoId"    => $objProductoService->getId());
                            }
                            if($elementoNuevo->getId() != $elementoViejo->getId() && $objProductoService->getNombreTecnico() == "SAFECITYWIFI")
                            {
                                //subred wifi
                                $strUsoSubredSsidServicio  = "";
                                $strUsoSubredAdminServicio = "";
                                $strMascaraSubred          = "";
                                $strEstadoSubredServicio   = "Activo";
                                $arrayParUsoSubredWifi     = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne("NUEVA_RED_GPON_TN",
                                                                        "COMERCIAL",
                                                                        "",
                                                                        "PARAMETRO USO SUBRED PARA SERVICIOS ADICIONALES SAFECITY",
                                                                        $objServicio->getProductoId()->getId(),
                                                                        "",
                                                                        "",
                                                                        "",
                                                                        "");
                                if(isset($arrayParUsoSubredWifi) && !empty($arrayParUsoSubredWifi)
                                   && isset($arrayParUsoSubredWifi['valor2']) && !empty($arrayParUsoSubredWifi['valor2'])
                                   && isset($arrayParUsoSubredWifi['valor3']) && !empty($arrayParUsoSubredWifi['valor3']))
                                {
                                    $strUsoSubredSsidServicio  = $arrayParUsoSubredWifi['valor2'];
                                    $strUsoSubredAdminServicio = $arrayParUsoSubredWifi['valor3'];
                                    $strMascaraSubred          = $arrayParUsoSubredWifi['valor4'];
                                    $strEstadoSubredServicio   = $arrayParUsoSubredWifi['valor5'] ? $arrayParUsoSubredWifi['valor5'] : "Activo";
                                }
                                else
                                {
                                    throw new \Exception("No se ha podido obtener el uso de subred del producto ".
                                                         $objProductoService->getDescripcionProducto().
                                                         ", por favor notificar a Sistemas.");
                                }
                                //obtengo la subred ssid
                                $objSubredSsidServicioAnt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                        ->findOneBy(array("elementoId" => $elementoViejo->getId(),
                                                                                          "uso"        => $strUsoSubredSsidServicio,
                                                                                          "mascara"    => $strMascaraSubred,
                                                                                          "estado"     => $strEstadoSubredServicio));
                                if(!is_object($objSubredSsidServicioAnt))
                                {
                                    throw new \Exception("No se encontró la subred SSID anterior del servicio ".
                                                         $objServicio->getLoginAux()." para tipo de red GPON, ".
                                                         "por favor notificar a Sistemas.");
                                }
                                //obtengo la subred admin
                                $objSubredAdminServicioAnt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                        ->findOneBy(array("elementoId" => $elementoViejo->getId(),
                                                                                          "uso"        => $strUsoSubredAdminServicio,
                                                                                          "mascara"    => $strMascaraSubred,
                                                                                          "estado"     => $strEstadoSubredServicio));
                                if(!is_object($objSubredAdminServicioAnt))
                                {
                                    throw new \Exception("No se encontró la subred ADMIN anterior del servicio ".
                                                         $objServicio->getLoginAux()." para tipo de red GPON, ".
                                                         "por favor notificar a Sistemas.");
                                }
                                //obtengo la subred ssid
                                $objSubredSsidServicio = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                        ->findOneBy(array("elementoId" => $elementoNuevo->getId(),
                                                                                          "uso"        => $strUsoSubredSsidServicio,
                                                                                          "mascara"    => $strMascaraSubred,
                                                                                          "estado"     => $strEstadoSubredServicio));
                                if(!is_object($objSubredSsidServicio))
                                {
                                    throw new \Exception("No se encontró la subred SSID nuevo del servicio ".
                                                         $objServicio->getLoginAux()." para tipo de red GPON, ".
                                                         "por favor notificar a Sistemas.");
                                }
                                //obtengo la subred admin
                                $objSubredAdminServicio = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                        ->findOneBy(array("elementoId" => $elementoNuevo->getId(),
                                                                                          "uso"        => $strUsoSubredAdminServicio,
                                                                                          "mascara"    => $strMascaraSubred,
                                                                                          "estado"     => $strEstadoSubredServicio));
                                if(!is_object($objSubredAdminServicio))
                                {
                                    throw new \Exception("No se encontró la subred ADMIN nuevo del servicio ".
                                                         $objServicio->getLoginAux()." para tipo de red GPON, ".
                                                         "por favor notificar a Sistemas.");
                                }
                            }

                            $arrayResponseCan  = $this->getCaracteristicasCamaras($objServicio,$objProductoService);

                            if($arrayResponseCan['status'] === "ERROR")
                            {   
                                throw new \Exception($arrayResponseCan['mensaje']);
                            }
                            else
                            {
                                $arrayResponseCan = $arrayResponseCan['result'];
                            }                         

                            if(is_object($objServicio))
                            {
                                $strPesonaEmpresaRolId     = $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getId();

                                $objResultadoValidateVrf  = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                ->getReutilizableRecursoRed(
                                                                    array("idPerosnaEmpresaRol" => $strPesonaEmpresaRolId,
                                                                        "arrayServicios"      => $arrayServiciValidate,
                                                                        "elementoId"          => $elementoViejo->getId(),
                                                                        "valor"               => $arrayResponseCan['vrfValor']));
                                if($objResultadoValidateVrf[0]['servicios'] > 1)
                                {
                                    $strReutilizable = "S";
                                }
                                else
                                {
                                    $strReutilizable = "N";
                                }
                                
                                $intContador = -1;
                                $intContador++;
                                //Reseteamos el array
                                $arrayDatosAactivar = null;

                                if($objProductoService->getNombreTecnico() == "SAFECITYWIFI")
                                {
                                    $arrayDatosAactivar[$intContador] = array(
                                        'vlan_datos'          => $arrayResponseCan['vlanAdmin'],
                                        'gemport_datos'       => $arrayResponseCan['genPortDatosAdmin'],
                                        't_cont_datos'        => $arrayResponseCan['tContDatosAdmin'],
                                        'traffic_table_datos' => $arrayResponseCan['TTDatosAdmin'],
                                        'id_mapping_datos'    => $arrayResponseCan['idMappingDatos']);
                                    $arrayDatosAactivar[$intContador+1] = array(
                                        'vlan_datos'          => $arrayResponseCan['vlan'],
                                        'gemport_datos'       => $arrayResponseCan['genPortDatos'],
                                        't_cont_datos'        => $arrayResponseCan['tContDatos'],
                                        'traffic_table_datos' => $arrayResponseCan['TTDatos'],
                                        'id_mapping_datos'    => $arrayResponseCan['idMappingDatos']);
                                }
                                else
                                {
                                    $arrayDatosAactivar[$intContador] = array(
                                        'vlan_datos'          => $arrayResponseCan['vlan'],
                                        'gemport_datos'       => $arrayResponseCan['genPortDatos'],
                                        't_cont_datos'        => $arrayResponseCan['tContDatos'],
                                        'traffic_table_datos' => $arrayResponseCan['TTDatos'],
                                        'id_mapping_datos'    => $arrayResponseCan['idMappingDatos']);
                                }

                                if($elementoNuevo->getId() != $elementoViejo->getId())
                                {
                                    //Reseteamos los array
                                    $arrayDatosNw = null;
                                    $arrayDatosNwCancelar = null;

                                    if($objProductoService->getNombreTecnico() == "SAFECITYWIFI")
                                    {
                                        $arrayDatosNw[$intContador] = array(
                                            'opcion_NW'           => $arrayParametrosGdaDatosNw['valor3'],
                                            'opcion'              => $arrayParametrosGdaDatosNw['valor4'],
                                            'accion'              => $arrayParametrosGdaDatosNw['valor5'],
                                            'modulo'              => $arrayParametrosGdaDatosNw['valor6'],
                                            'esquema'             => $arrayParametrosGdaDatosAdd['valor2'],
                                            'bandEjecuta'         => $this->rdaBandEjecuta,
                                            'servicio'            => $objServicio->getProductoId()->getNombreTecnico(),
                                            'ambiente'            => $this->rdaTipoEjecucion,
                                            'vrf'                 => $arrayResponseCan['vrfAdmin'],
                                            'rd'                  => $arrayResponseCan['rdAdmin'],
                                            'vlan'                => $arrayResponseCan['vlanAdmin'],
                                            'subred'              => is_object($objSubredAdminServicio)?$objSubredAdminServicio->getSubred():'',
                                            'gateway'             => is_object($objSubredAdminServicio)?$objSubredAdminServicio->getGateway():'');
                                        $arrayDatosNwCancelar[$intContador] = array(
                                            'opcion_NW'           => $arrayParametrosGdaDatosAdd['valor3'],
                                            'opcion'              => $arrayParametrosGdaDatosNw['valor4'],
                                            'accion'              => $arrayParametrosGdaDatosAdd['valor4'],
                                            'modulo'              => $arrayParametrosGdaDatosNw['valor6'],
                                            'esquema'             => $arrayParametrosGdaDatosAdd['valor2'],
                                            'bandEjecuta'         => $this->rdaBandEjecuta,
                                            'servicio'            => $objServicio->getProductoId()->getNombreTecnico(),
                                            'ambiente'            => $this->rdaTipoEjecucion,
                                            'vrf'                 => $arrayResponseCan['vrfAdmin'],
                                            'rd'                  => $arrayResponseCan['rdAdmin'],
                                            'vlan'                => $arrayResponseCan['vlanAdmin'],
                                            'subred'              => is_object($objSubredAdminServicioAnt)?
                                                                     $objSubredAdminServicioAnt->getSubred():'',
                                            'gateway'             => is_object($objSubredAdminServicioAnt)?
                                                                     $objSubredAdminServicioAnt->getGateway():'',
                                            'reutilizada'         => $strReutilizable);
                                        $arrayDatosNw[$intContador+1] = array(
                                            'opcion_NW'           => $arrayParametrosGdaDatosNw['valor3'],
                                            'opcion'              => $arrayParametrosGdaDatosNw['valor4'],
                                            'accion'              => $arrayParametrosGdaDatosNw['valor5'],
                                            'modulo'              => $arrayParametrosGdaDatosNw['valor6'],
                                            'esquema'             => $arrayParametrosGdaDatosAdd['valor2'],
                                            'bandEjecuta'         => $this->rdaBandEjecuta,
                                            'servicio'            => $objServicio->getProductoId()->getNombreTecnico(),
                                            'ambiente'            => $this->rdaTipoEjecucion,
                                            'vrf'                 => $arrayResponseCan['vrf'],
                                            'rd'                  => $arrayResponseCan['rd'],
                                            'vlan'                => $arrayResponseCan['vlan'],
                                            'subred'              => is_object($objSubredSsidServicio)?$objSubredSsidServicio->getSubred():'',
                                            'gateway'             => is_object($objSubredSsidServicio)?$objSubredSsidServicio->getGateway():'');
                                        $arrayDatosNwCancelar[$intContador+1] = array(
                                            'opcion_NW'           => $arrayParametrosGdaDatosAdd['valor3'],
                                            'opcion'              => $arrayParametrosGdaDatosNw['valor4'],
                                            'accion'              => $arrayParametrosGdaDatosAdd['valor4'],
                                            'modulo'              => $arrayParametrosGdaDatosNw['valor6'],
                                            'esquema'             => $arrayParametrosGdaDatosAdd['valor2'],
                                            'bandEjecuta'         => $this->rdaBandEjecuta,
                                            'servicio'            => $objServicio->getProductoId()->getNombreTecnico(),
                                            'ambiente'            => $this->rdaTipoEjecucion,
                                            'vrf'                 => $arrayResponseCan['vrf'],
                                            'rd'                  => $arrayResponseCan['rd'],
                                            'vlan'                => $arrayResponseCan['vlan'],
                                            'subred'              => is_object($objSubredSsidServicioAnt)?$objSubredSsidServicioAnt->getSubred():'',
                                            'gateway'             => is_object($objSubredSsidServicioAnt)?$objSubredSsidServicioAnt->getGateway():'',
                                            'reutilizada'         => $strReutilizable);
                                    }
                                    else
                                    {
                                        $arrayDatosNw[$intContador] = array(
                                            'opcion_NW'           => $arrayParametrosGdaDatosNw['valor3'],
                                            'opcion'              => $arrayParametrosGdaDatosNw['valor4'],
                                            'accion'              => $arrayParametrosGdaDatosNw['valor5'],
                                            'modulo'              => $arrayParametrosGdaDatosNw['valor6'],
                                            'esquema'             => $arrayParametrosGdaDatosAdd['valor2'],
                                            'bandEjecuta'         => $this->rdaBandEjecuta,
                                            'servicio'            => $objServicio->getProductoId()->getNombreTecnico(),
                                            'ambiente'            => $this->rdaTipoEjecucion,
                                            'vrf'                 => $arrayResponseCan['vrf'],
                                            'rd'                  => $arrayResponseCan['rd'],
                                            'vlan'                => $arrayResponseCan['vlan'],
                                            'subred'              => is_object($objSubredServicioAdd)?$objSubredServicioAdd->getSubRed():'',
                                            'gateway'             => is_object($objSubredServicioAdd)?$objSubredServicioAdd->getGateWay():'');
                                        $arrayDatosNwCancelar[$intContador] = array(
                                            'opcion_NW'           => $arrayParametrosGdaDatosAdd['valor3'],
                                            'opcion'              => $arrayParametrosGdaDatosNw['valor4'],
                                            'accion'              => $arrayParametrosGdaDatosAdd['valor4'],
                                            'modulo'              => $arrayParametrosGdaDatosNw['valor6'],
                                            'esquema'             => $arrayParametrosGdaDatosAdd['valor2'],
                                            'bandEjecuta'         => $this->rdaBandEjecuta,
                                            'servicio'            => $objServicio->getProductoId()->getNombreTecnico(),
                                            'ambiente'            => $this->rdaTipoEjecucion,
                                            'vrf'                 => $arrayResponseCan['vrf'],
                                            'rd'                  => $arrayResponseCan['rd'],
                                            'vlan'                => $arrayResponseCan['vlan'],
                                            'subred'              => is_object($objIpActual)?$objIpActual->getSubred():'',
                                            'gateway'             => is_object($objIpActual)?$objIpActual->getGateWay():'',
                                            'reutilizada'         => $strReutilizable);
                                    }
                                }
                                else
                                {
                                    $arrayDatosNw = [];
                                    $arrayDatosNwCancelar = [];
                                }
                                $arrayDatosServicios =  array( 
                                    'es_datos'            => $arrayParametrosGdaDatos['valor2'],
                                    'login_aux'           => $objServicio->getLoginAux(),
                                    'estado_servicio'     => $servicio->getEstado(),
                                    'tiene_cpe'           => $arrayParametrosGdaDatos['valor3'],
                                    'puerto_ethernet'     => $arrayResponseCan['puertoEthernet'],
                                    "numero_datos_activar"=> count($arrayDatosAactivar),
                                    'vlan_ethernet'       => $arrayResponseCan['vlan'],
                                    'service_port'        => $arrayResponseCan['spid'], 
                                    'tipo_negocio_actual' => $arrayParametrosGdaDatos['valor4'],
                                    'datos_activar'       => $arrayDatosAactivar,
                                    'datos_NW'            => $arrayDatosNw,
                                    'datos_NW_CANCELAR'   => $arrayDatosNwCancelar);
                                                                                            
                                $arrayDatosServiciosCan[] = $arrayDatosServicios;
                            }
                        }
                    }                               
                    
                    //DATOS OLT PARA EL MIDDLEWARE 
                    $arrayDatos = array(
                        'nombre_olt'            => $elementoViejo->getNombreElemento(),
                        'ip_olt'                => $objIpElementoViejo->getIp(),
                        'puerto_olt'            => $interfaceElementoViejo->getNombreInterfaceElemento(),
                        'nombre_olt_nuevo'      => $elementoNuevo->getNombreElemento(),//scope actual
                        'ip_olt_nuevo'          => $strIpElementoNuevo,
                        'puerto_olt_nuevo'      => $strInterfaceNuevo,
                        'line_profile'          => $valorLineProfile);

                    //DATOS ONT PARA EL MIDDLEWARE
                    $arrayDatosOnt[] = array(
                        'serial_ont'              => $serieOnt,
                        'mac_ont'                 => $strMacOnt,
                        'service_profile'         => $strServiceProfile,
                        'ont_id'                  => $strOntId,
                        'tiene_datos'             => $arrayParametrosGdaDatos['valor5'],
                        'tiene_internet'          => $arrayParametrosGdaDatos['valor6'],
                        'gemport_monitoreo'       => $valorGemport,
                        'traffic_table_monitoreo' => $valorTraffic,
                        't_cont_monitoreo'        => $strTnCont,
                        'id_mapping_monitoreo'    => $strTnMappingMonitoreo,
                        'vlan_monitoreo'          => $strVlanOnt,
                        'service_port_monitoreo'  => $strSpid, 
                        'datos_servicios'         => $arrayDatosServiciosCan);  
                }
                else
                {
                    //DATOS PARA EL MIDDLEWARE
                    $arrayDatos = array(
                        'serial_ont'            => $serieOnt,
                        'mac_ont'               => $strMacOnt,
                        'nombre_olt'            => $elementoViejo->getNombreElemento(),
                        'ip_olt'                => $objIpElementoViejo->getIp(),
                        'puerto_olt'            => $interfaceElementoViejo->getNombreInterfaceElemento(),
                        'modelo_olt'            => $elementoViejo->getModeloElementoId()->getNombreModeloElemento(),
                        'gemport'               => $valorGemport,
                        'service_profile'       => $strServiceProfile,
                        'line_profile'          => $valorLineProfile,
                        'traffic_table'         => $valorTraffic,
                        'ont_id'                => $strOntId,
                        'service_port'          => $strSpid,
                        'vlan'                  => $valorVlan,
                        'estado_servicio'       => $servicio->getEstado(),
                        'ip'                    => $strIpFija,     //ip plan actual
                        'scope'                 => $scope,         //scope actual
                        'ip_olt_nuevo'          => $strIpElementoNuevo,
                        'modelo_olt_nuevo'      => $strModeloNuevo,
                        'puerto_olt_nuevo'      => $strInterfaceNuevo,
                        'ip_fijas_activas'      => $intIpsFijasActivas,
                        'tipo_negocio_actual'   => $strTipoNegocio,
                        'ip_nueva'              => $strIpNuevaPlan,
                        'scope_nuevo'           => $strScopeNuevo,
                        'ip_cancelar'           => $arrayIpCancelar,
                        'ip_activar'            => $arrayIpActivar,
                        'equipoOntDualBand'     => "",
                        'tipoOrden'             => "");
                }

                if ($strPrefijoEmpresa === 'MD')
                {
                    $arrayRespuestaSeteaInfo = $this->servicioGeneral
                                                    ->seteaInformacionPlanesPyme(array("intIdPlan"         => $servicio->getPlanId()->getId(),
                                                                                       "intIdPunto"        => $servicio->getPuntoId()->getId(),
                                                                                       "strConservarIp"    => "",
                                                                                       "strTipoNegocio"    => $strTipoNegocio,
                                                                                       "strPrefijoEmpresa" => $strPrefijoEmpresa,
                                                                                       "strUsrCreacion"    => $usrCreacion,
                                                                                       "strIpCreacion"     => $ipCreacion,
                                                                                       "strTipoProceso"    => 'CAMBIAR_PUERTO',
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

                if (($strPrefijoEmpresa == 'TN' && $boolRedGponMpls)
                    && $servicio->getProductoId()->getNombreTecnico() === "DATOS SAFECITY")
                {
                    $arrayDatosMiddleware = array(
                        'nombre_cliente'        => $strNombreCliente,
                        'login'                 => $servicio->getPuntoId()->getLogin(),
                        'identificacion'        => $strIdentificacion,
                        'datos_generales'       => $arrayDatos,
                        'datos_por_ont'         => $arrayDatosOnt,
                        'opcion'                => $arrayParametrosGdaDatos['valor7'],
                        'ejecutaComando'        => $this->ejecutaComando,
                        'usrCreacion'           => $usrCreacion,
                        'ipCreacion'            => $ipCreacion,
                        'comandoConfiguracion'  => $arrayParametrosGdaDatosNw['valor7'],
                        'empresa'               => $strPrefijoEmpresa);
                   
                }
                else
                {
                    $arrayDatosMiddleware = array(
                        'empresa'               => $strPrefijoEmpresa,
                        'nombre_cliente'        => $strNombreCliente,
                        'login'                 => $servicio->getPuntoId()->getLogin(),
                        'identificacion'        => $strIdentificacion,
                        'datos'                 => $arrayDatos,
                        'opcion'                => $this->opcion,
                        'ejecutaComando'        => $this->ejecutaComando,
                        'usrCreacion'           => $usrCreacion,
                        'ipCreacion'            => $ipCreacion
                    );

                }

                if($boolRedGponMpls)
                {
                    $arrayRespuesta       = $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));
                    $strMensajeStatus     = $arrayRespuesta['status'];
                    $strMensajeOpcion     = $arrayRespuesta['opcion'];
                    $strMensajeCan        = $arrayRespuesta['mensaje'];
                    $strFlag = true;
                }
                else
                {
                    $arrayRespuesta   = $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));
                    $statusActivar    = $arrayRespuesta['status_activar'];
                    $statusCancelar   = $arrayRespuesta['status_cancelar'];
                    $strFlag = false;
                }
                
                $mensajeFinal   = '';
                
                //RESPUESTA ACTIVAR Y CANCELAR = OK
                if(($statusActivar == 'OK' && $statusCancelar == 'OK') || 
                   ($strMensajeStatus === 'OK' ||  $strMensajeStatus === 'WARNING'))
                {
                    if(!$strFlag)
                    {
                        $arrayDatosConfirmacionTn                           = $arrayDatos;
                        $arrayDatosConfirmacionTn['opcion_confirmacion']    = $this->opcion;
                        $arrayDatosConfirmacionTn['respuesta_confirmacion'] = 'ERROR';
                        $arrayDataConfirmacionTn    = array('nombre_cliente'    => $strNombreCliente,
                                                            'login'             => $servicio->getPuntoId()->getLogin(),
                                                            'identificacion'    => $strIdentificacion,
                                                            'datos'             => $arrayDatosConfirmacionTn,
                                                            'opcion'            => $this->strConfirmacionTNMiddleware,
                                                            'ejecutaComando'    => $this->ejecutaComando,
                                                            'usrCreacion'       => $usrCreacion,
                                                            'ipCreacion'        => $ipCreacion,
                                                            'empresa'           => $strPrefijoEmpresa,
                                                            'statusMiddleware'  => 'OK');
                        
                        $mensajeFinal = $arrayRespuesta['mensaje_cancelar'];
                        $mensajeFinal = $arrayRespuesta['mensaje_activar'];
                    }

                    if(($strMensajeStatus === 'OK' ||  $strMensajeStatus === 'WARNING') && $strFlag)
                    {
                        $arrayDatosPorOnt        = $arrayRespuesta['datos_por_ont'][0];
                        $strSerialOnt            = $arrayDatosPorOnt['serial_ont'];
                        $strMacOntMonitoreo      = $arrayDatosPorOnt['mac_ont'];
                        $strOntIdMonitorio       = $arrayDatosPorOnt['ont_id'];
                        $strSpIdMonitorio        = $arrayDatosPorOnt['spId_Monitoreo'];
                        $strLineProfileMonitoreo = $arrayRespuesta['LINE_PROFILE'];
                        $arrayDatosActivacion    = $arrayDatosPorOnt['datos_activacion'];
                        
                        if(is_object($objServProdCaractMacOnt))
                        {
                            //ELIMINAR MAC ONT ANTERIOR DATOS GPON SAFE CITY
                            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objServProdCaractMacOnt, "Eliminado");

                            //CREAR MAC ONT_CLIENTE DATOS GPON SAFE CITY
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $servicio, $objProducto, "MAC ONT", 
                                                                                                $strMacOntMonitoreo, $usrCreacion);
                        }
                                                
                        if(is_object($objIndiceClienteViejoSpc))
                        {
                             //ELIMINAR INDICE_CLIENTE ANTERIOR DATOS GPON SAFE CITY
                            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objIndiceClienteViejoSpc, "Eliminado");
                                                
                            //CREAR NUEVO INDICE_CLIENTE DATOS GPON SAFE CITY
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $servicio, 
                                                                                            $objProducto, 
                                                                                            "INDICE CLIENTE", 
                                                                                            $strOntIdMonitorio,
                                                                                            $usrCreacion);
                        }

                        if(is_object($spcSpid))
                        {
                            //ELIMINAR SERVICE PORT ID ANTERIOR DATOS GPON SAFE CITY
                            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($spcSpid, "Eliminado");
                                                
                            //CREAR NUEVO SERVICE PORT ID DATOS GPON SAFE CITY
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $servicio, 
                                                                                            $objProducto, 
                                                                                            "SPID", 
                                                                                            $strSpIdMonitorio,
                                                                                            $usrCreacion);
                        }

                        if(is_object($objSpcLineProfile))
                        {
                            //ELIMINAR LINE-PROFILE-NAME ANTERIOR DATOS GPON SAFE CITY
                            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcLineProfile, "Eliminado");
                                
                            //CREAR LINE-PROFILE-NAME ID DATOS GPON SAFE CITY
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $servicio, 
                                                                                            $objProducto, 
                                                                                            "LINE-PROFILE-NAME", 
                                                                                            $strLineProfileMonitoreo,
                                                                                            $usrCreacion);
                        }

                        if(is_array($arrayDatosActivacion))
                        {
                            foreach($arrayDatosActivacion as $arrayDatosActivacionCamaras)
                            {
                                $objServicioLoginAux = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                ->findOneByLoginAux($arrayDatosActivacionCamaras['login_aux']);

                                $objProductoLoginAux = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                       ->findOneBy(array("nombreTecnico" => $objServicioLoginAux->getProductoId()->getNombreTecnico(),
                                                                         "empresaCod"    => $empresa,
                                                                         "estado"        => "Activo"));

                                $objMacOntCan = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioLoginAux
                                                                                            , "MAC ONT", $objProductoLoginAux);

                                if(is_object($objMacOntCan))
                                {
                                    //ELIMINAR MAC ONT ANTERIOR DATOS GPON SAFE CITY
                                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objMacOntCan, "Eliminado");
                                                            
                                    //CREAR MAC ONT_CLIENTE DATOS GPON SAFE CITY
                                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicioLoginAux, 
                                                                                                    $objProductoLoginAux, 
                                                                                                    "MAC ONT", 
                                                                                                    $strMacOntMonitoreo,
                                                                                                    $usrCreacion);
                                }
                                else
                                {
                                    throw new \Exception('No se pudo obtener la caracteristica MAC ONT del 
                                                          producto '. $objProductoLoginAux->getDescripcionProducto() 
                                                          . ', favor notificar a sistemas.');
                                }

                                $objIndiceClienteCan = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioLoginAux,
                                                                                             "INDICE CLIENTE", $objProductoLoginAux);
                                
                                if(is_object($objIndiceClienteCan))
                                {
                                    //ELIMINAR INDICE_CLIENTE ANTERIOR DATOS GPON SAFE CITY
                                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objIndiceClienteCan, "Eliminado");
                                                        
                                    //CREAR NUEVO INDICE_CLIENTE DATOS GPON SAFE CITY
                                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicioLoginAux, 
                                                                                                    $objProductoLoginAux, 
                                                                                                    "INDICE CLIENTE", 
                                                                                                    $strOntIdMonitorio,
                                                                                                    $usrCreacion);
                                }
                                else
                                {
                                    throw new \Exception('No se pudo obtener la caracteristica INDICE CLIENTE del
                                                         producto '. $objProductoLoginAux->getDescripcionProducto() . 
                                                         ', favor notificar a sistemas.');
                                }


                                $objLineProfileNameCan = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioLoginAux,
                                                                                             "LINE-PROFILE-NAME", $objProductoLoginAux);

                                if(is_object($objLineProfileNameCan))
                                {
                                    //ELIMINAR LINE-PROFILE-NAME ANTERIOR DATOS GPON SAFE CITY
                                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objLineProfileNameCan, "Eliminado");
                                    
                                    //CREAR LINE-PROFILE-NAME ID DATOS GPON SAFE CITY
                                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicioLoginAux, 
                                                                                                    $objProductoLoginAux, 
                                                                                                    "LINE-PROFILE-NAME", 
                                                                                                    $strLineProfileMonitoreo,
                                                                                                    $usrCreacion);
                                }
                                else
                                {
                                    throw new \Exception('No se pudo obtener la caracteristica LINE-PROFILE-NAME del producto '
                                    . $objProductoLoginAux->getDescripcionProducto() . ', favor notificar a sistemas.');
                                }
                                
    

                                $objServicePidCan = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioLoginAux,
                                                                                                      "SPID", $objProductoLoginAux);

                                if(is_object($objServicePidCan))
                                {
                                    //ELIMINAR SERVICE PORT ID ANTERIOR SAFE VIDEO ANALYTICS CAM
                                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objServicePidCan, "Eliminado");
                                                        
                                    //CREAR NUEVO SERVICE PORT ID SAFE VIDEO ANALYTICS CAM
                                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicioLoginAux, 
                                                                                                    $objProductoLoginAux, 
                                                                                                    "SPID", 
                                                                                                    $arrayDatosActivacionCamaras['spId'][0],
                                                                                                    $usrCreacion);
                                }
                                else
                                {
                                    throw new \Exception('No se pudo obtener la caracteristica SPID del
                                          producto '. $objProductoLoginAux->getDescripcionProducto() . ', favor notificar a sistemas.');
                                }
                            }
                        }
                        else
                        {
                            throw new \Exception('No se recibieron la nuevas caracteristicas SPID de camaras, favor notificar a sistemas.');
                        }
                    } 
                    
                    //CAMBIAR PUERTO LOGICO
                    $this->cambiarPuertoLogicoMd(   $servicio, 
                                                    $servicioTecnico, 
                                                    $elementoNuevo->getId(), 
                                                    $interfaceElementoNuevo->getId(), 
                                                    $elementoCajaId,
                                                    $elementoSplitterId, 
                                                    $interfaceElementoSplitterId, 
                                                    $usrCreacion, 
                                                    $ipCreacion,
                                                    $empresa);
                    
                    //IP DEL PLAN
                    if($strIpNuevaPlan != '')
                    {
                        if ($strExisteIpWan === "SI")
                        {
                            $intIdServicioIp        = $arrayDatosIpWan['arrayInfoIp']['intIdServicioIp'];
                            $objServicioIpAdicional = $this->emComercial
                                                           ->getRepository('schemaBundle:InfoServicio')
                                                           ->find($intIdServicioIp);
                            $prodIpPlan             = $objServicioIpAdicional->getProductoId();
                            $spcScope               = $this->servicioGeneral
                                                           ->getServicioProductoCaracteristica($objServicioIpAdicional, "SCOPE", $prodIpPlan);
                        }
                        else
                        {
                            $intIdServicioIp        = $servicio->getId();
                            $objServicioIpAdicional = $servicio;
                        }
                        $servicioIpPlan = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                      ->findOneBy(array("servicioId" => $intIdServicioIp, "estado" => "Activo"));
                        
                        $arrayParametrosIp['intIdServicio'] = $servicioId;
                        $arrayParametrosIp['emComercial']   = $this->emComercial;
                        $arrayParametrosIp['emGeneral']     = $this->emGeneral;
                        
                        $strTipoIp = '';
                        if ($strPrefijoEmpresa === 'TN')
                        {
                            $strTipoIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                             ->getTipoIpServicio($arrayParametrosIp);
                        }
                        
                        //Si esta vacía la variable $strIp por default es Fija
                        if(empty($strTipoIp))
                        {
                            $strTipoIp = 'FIJA';
                        }
                        else
                        {
                            $strTipoIp = strtoupper($strTipoIp);
                        }
                        
                        if($servicioIpPlan)
                        {
                            //ELIMINA IP ANTERIOR
                            $servicioIpPlan->setEstado("Eliminado");
                            $this->emInfraestructura->persist($servicioIpPlan);
                            $this->emInfraestructura->flush();
                            
                            //GRABAR Y ACTIVAR IP NUEVA
                            $ipFija = new InfoIp();
                            $ipFija->setIp($strIpNuevaPlan);
                            $ipFija->setEstado("Activo");
                            $ipFija->setTipoIp($strTipoIp);
                            $ipFija->setVersionIp('IPV4');
                            $ipFija->setServicioId($intIdServicioIp);
                            $ipFija->setUsrCreacion($usrCreacion);
                            $ipFija->setFeCreacion(new \DateTime('now'));
                            $ipFija->setIpCreacion($ipCreacion);
                            $this->emInfraestructura->persist($ipFija);
                            $this->emInfraestructura->flush();
                            
                            //ELIMINAR CARACTERISTICA SCOPE ANTERIOR
                            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($spcScope, 'Eliminado');
                            
                            //CREAR NUEVA CARACTERISTICA SCOPE NUEVO
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicioIpAdicional, 
                                                                                            $prodIpPlan, 
                                                                                            'SCOPE', 
                                                                                            $strScopeNuevo, 
                                                                                            $usrCreacion);
                        }
                    }
                    
                    //Consulta si se debe crear servicio adicional
                    if ($boolCrearServicio)
                    {
                        $this->servicioInfoServicio->crearServicio($arrayParamsServicio);
                    }
                    
                    //IPS ADICIONALES
                    if(count($arrayIpActivar) > 0)
                    {
                        //ELIMINAR IPS ADICIONALES ANTERIORES
                        foreach($arrayRespuesta['ip_cancelar'] as $arrayRespuestaIpCancelar)
                        {
                            $statusIpCancelar = $arrayRespuestaIpCancelar['status'];
                            
                            if($statusIpCancelar == 'OK')
                            {
                                //ELIMINA IP ANTERIOR
                                $objIpAdicional    = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                            ->findOneBy(array(  "servicioId"    => $arrayRespuestaIpCancelar['id_servicio'], 
                                                                                "estado"        => "Activo"));
                                
                                $objIpAdicional->setEstado('Eliminado');
                                $this->emInfraestructura->persist($objIpAdicional);
                                $this->emInfraestructura->flush();
                                
                                $servicioIpAdicional    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->find($arrayRespuestaIpCancelar['id_servicio']);
                                $spcScopeAdicional      = $this->servicioGeneral
                                                    ->getServicioProductoCaracteristica($servicioIpAdicional, "SCOPE", 
                                                                                        $servicioIpAdicional->getProductoId());
                                
                                //ELIMINAR CARACTERISTICA SCOPE ANTERIOR
                                $this->servicioGeneral->setEstadoServicioProductoCaracteristica($spcScopeAdicional, 'Eliminado');
                            }
                            
                            $mensajeFinal = $mensajeFinal . $arrayRespuestaIpCancelar['mensaje'];
                        }
                        
                        //GRABAR Y ACTIVAR IPS ADICIONALES NUEVAS
                        foreach($arrayRespuesta['ip_activar'] as $arrayRespuestaIpActivar)
                        {
                            $statusIpActivar = $arrayRespuestaIpActivar['status'];
                            
                            if($statusIpActivar == 'OK')
                            {
                                //GRABAR Y ACTIVAR IP NUEVA
                                $ipFija = new InfoIp();
                                $ipFija->setIp($arrayRespuestaIpActivar['ip']);
                                $ipFija->setEstado("Activo");
                                $ipFija->setTipoIp('FIJA');
                                $ipFija->setVersionIp('IPV4');
                                $ipFija->setServicioId($arrayRespuestaIpActivar['id_servicio']);
                                $ipFija->setUsrCreacion($usrCreacion);
                                $ipFija->setFeCreacion(new \DateTime('now'));
                                $ipFija->setIpCreacion($ipCreacion);
                                $this->emInfraestructura->persist($ipFija);
                                $this->emInfraestructura->flush();

                                $servicioIpAdicional    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->find($arrayRespuestaIpActivar['id_servicio']);
                                
                                //CREAR NUEVA CARACTERISTICA SCOPE NUEVO
                                $this->servicioGeneral->ingresarServicioProductoCaracteristica( $servicioIpAdicional, 
                                                                                                $servicioIpAdicional->getProductoId(), 
                                                                                                'SCOPE', 
                                                                                                $strScopeNuevo, 
                                                                                                $usrCreacion);
                                
                                $servicioTecnicoIp = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->findOneByServicioId($arrayRespuestaIpActivar['id_servicio']);

                                if($servicioTecnicoIp)
                                {
                                    $servicioTecnicoIp->setElementoId($elementoNuevo->getId());
                                    $servicioTecnicoIp->setInterfaceElementoId($interfaceElementoNuevo->getId());
                                    $servicioTecnicoIp->setElementoContenedorId($elementoCajaId);
                                    $servicioTecnicoIp->setElementoConectorId($elementoSplitterId);
                                    $servicioTecnicoIp->setInterfaceElementoConectorId($interfaceElementoSplitterId);
                                    $this->emComercial->persist($servicioTecnicoIp);
                                    $this->emComercial->flush();
                                }
                            }
                            
                            $mensajeFinal = $mensajeFinal . $arrayRespuestaIpActivar['mensaje'];
                        }

                         if($boolRedGponMpls && ($elementoNuevo->getId() != $elementoViejo->getId()))
                        {
                            //ELIMINAR IPS ADICIONALES ANTERIORES  DE CAMARAS
                            foreach($arrayIpCancelar as $arrayRespuestaIpCancelar)
                            {                               
                                    //ELIMINA IP ANTERIOR
                                    $objIpAdicionalEliminar = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                ->findOneBy(array(  "servicioId"    => $arrayRespuestaIpCancelar['id_servicio'], 
                                                                                    "estado"        => "Activo"));

                                    $objServicioCanE = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                                    ->findOneById($objIpAdicionalEliminar->getServicioId());
                                    
                                    $objIpAdicionalEliminar->setEstado('Eliminado');
                                    $this->emInfraestructura->persist($objIpAdicionalEliminar);
                                    $this->emInfraestructura->flush();

                                    $strObservacion = " <b style = 'color: red'>Se elimino la Ip LAN de anterior OLT</b> "."<br>".
                                                      " <b>OLT anterior:</b> ".$elementoViejo->getNombreElemento()."<br>".
                                                      " <b>Ip LAN:</b> ".$objIpAdicionalEliminar->getIp()."<br>".
                                                      " <b>Login Aux:</b> ".$objServicioCanE->getLoginAux()."<br>".
                                                      " <b>Producto:</b> ".$objServicioCanE->getProductoId()->getDescripcionProducto();
                                    
                                    $objServicioHistorial = new InfoServicioHistorial();
                                    $objServicioHistorial->setServicioId($objServicioCanE);
                                    $objServicioHistorial->setEstado($objServicioCanE->getEstado());
                                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                                    $objServicioHistorial->setIpCreacion($ipCreacion);
                                    $objServicioHistorial->setAccion("cambio de linea pon");           
                                    $objServicioHistorial->setObservacion($strObservacion);
                                    $objServicioHistorial->setUsrCreacion($usrCreacion);
                                    $this->emComercial->persist($objServicioHistorial);
                                    $this->emComercial->flush();

                                    $arrayCorreo[] = array('loginAux'   => $objServicioCanE->getLoginAux(),
                                                           'ipAnterior' => $arrayRespuestaIpCancelar['ip'],
                                                           'ipNueva'    => $arrayRespuestaIpActivar['ip']);
                            }
                            
                            //ACTIVAR IPS ADICIONALES NUEVAS DE CAMARAS
                            foreach($arrayIpActivar as $arrayRespuestaIpActivar)
                            {                                    
                                    //Activar IP ANTERIOR
                                    $objIpAdicionalCan = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                    ->findOneBy(array(  "servicioId"    => $arrayRespuestaIpActivar['id_servicio'], 
                                                        "estado"        => "Reservada"));
                                    
                                    $objIpAdicionalCan->setEstado('Activo');
                                    $this->emInfraestructura->persist($objIpAdicionalCan);
                                    $this->emInfraestructura->flush();                                                                     
                                            
                                    $servicioTecnicoIp = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->findOneByServicioId($arrayRespuestaIpActivar['id_servicio']);

                                    if($servicioTecnicoIp)
                                    {
                                        $servicioTecnicoIp->setElementoId($elementoNuevo->getId());
                                        $servicioTecnicoIp->setInterfaceElementoId($interfaceElementoNuevo->getId());
                                        $servicioTecnicoIp->setElementoContenedorId($elementoCajaId);
                                        $servicioTecnicoIp->setElementoConectorId($elementoSplitterId);
                                        $servicioTecnicoIp->setInterfaceElementoConectorId($interfaceElementoSplitterId);
                                        $this->emComercial->persist($servicioTecnicoIp);
                                        $this->emComercial->flush();
                                    }

                                        //GUARDAR INFO SERVICIO HISTORIAL
                                        $strObservacion = " <b style = 'color: red'>Se activo la Ip LAN del nuevo OLT</b> "."<br>".
                                                            " <b>OLT nuevo:</b> ".$elementoNuevo->getNombreElemento()."<br>".
                                                            " <b>Ip LAN:</b> ".$arrayRespuestaIpActivar['ip']."<br>".
                                                            " <b>Login Aux:</b> ".$servicioTecnicoIp->getServicioId()->getLoginAux()."<br>".
                                                            " <b>Producto:</b> ".$servicioTecnicoIp->getServicioId()
                                                                             ->getProductoId()->getDescripcionProducto();
    
                                        $objServicioHistorial = new InfoServicioHistorial();
                                        $objServicioHistorial->setServicioId($servicioTecnicoIp->getServicioId());
                                        $objServicioHistorial->setEstado($servicioTecnicoIp->getServicioId()->getEstado());
                                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                                        $objServicioHistorial->setIpCreacion($ipCreacion);
                                        $objServicioHistorial->setAccion("cambio de linea pon");
                                        $objServicioHistorial->setObservacion($strObservacion);
                                        $objServicioHistorial->setUsrCreacion($usrCreacion);
                                        $this->emComercial->persist($objServicioHistorial);
                                        $this->emComercial->flush();
                            }
                        } 
                    }                
                    
                    //Servicios de camara 
                    if(count($arrayServicioCamaras) > 0 && is_array($arrayServicioCamaras) || count($arrayServiciValidate) > 0)
                    {
                        foreach($arrayServicioCamaras as $arrayCamaras)
                        {
                            $servicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                ->findOneBy(array("servicioId" => $arrayCamaras->getId()));
                                                
                            $servicioTecnico->setInterfaceElementoId($interfaceElementoNuevo->getId());
                            $servicioTecnico->setElementoContenedorId($elementoCajaId);
                            $servicioTecnico->setElementoConectorId($elementoSplitterId);
                            $servicioTecnico->setInterfaceElementoConectorId($interfaceElementoSplitterId);
                            $this->emComercial->persist($servicioTecnico);
                            $this->emComercial->flush();

                            $objCajaNuevaSafe = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                                ->find($elementoCajaId);

                            $objInterfaceElementoSplitterNuevoSafe = $this->emInfraestructura
                                                            ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                            ->find($interfaceElementoSplitterId);

                            $objServicioHistorialSafe = new InfoServicioHistorial();
                            $objServicioHistorialSafe->setServicioId($arrayCamaras);
                            $objServicioHistorialSafe->setEstado($arrayCamaras->getEstado());
                            $objServicioHistorialSafe->setFeCreacion(new \DateTime('now'));
                            $objServicioHistorialSafe->setIpCreacion($ipCreacion);
                            $objServicioHistorialSafe->setAccion("cambio de linea pon");           
                            $objServicioHistorialSafe->setObservacion(" <b>Se hizo cambio de linea pon:</b>"."<br>".
                                "<b style = 'color: red'>OLT Anterior</b>"."<br>".
                                "<b>Elemento anterior : </b>".$elementoViejo->getNombreElemento().
                                "<br> <b>Puerto anterior : </b> " . $interfaceElementoViejo->getNombreInterfaceElemento().
                                "<br> <b>Elemento conector anterior : </b> ".$elementoContenedorViejo->getNombreElemento().
                                "<br> <b>Interface elemento conector anterior : </b> ".
                                $interfaceElementoConectorViejo->getNombreInterfaceElemento()."<br>".
                                "<b style = 'color: red'>OLT Actual</b>"."<br>".
                                "<b>Elemento actual :</b>".$elementoNuevo->getNombreElemento().
                                "<br> <b>Puerto actual :</b> " . $strInterfaceNuevo.
                                "<br> <b>Elemento conector actual:</b> ".$objCajaNuevaSafe->getNombreElemento().
                                "<br> <b>Interface elemento conector actual:</b> ".
                                $objInterfaceElementoSplitterNuevoSafe->getNombreInterfaceElemento());
                            $objServicioHistorialSafe->setUsrCreacion($usrCreacion);
                            $this->emComercial->persist($objServicioHistorialSafe);
                            $this->emComercial->flush();
                        }

                        if($strPrefijoEmpresa == 'TN' && $boolRedGponMpls)
                        {
                            //Si el punto posee los siguientes servicios, se actualizara la informacion tecnica con el nuevo elemento
                            // y  se agrega al historial del servicio el cambio realizado
                            foreach($serviciosPunto as $objServicosSafeCity)
                            {
                                if(($objServicosSafeCity->getProductoId()->getNombreTecnico() === "SAFECITYWIFI" ||
                                    $objServicosSafeCity->getProductoId()->getNombreTecnico() === "SAFECITYSWPOE") &&
                                    $objServicosSafeCity->getEstado() == "Activo")
                                {
                                    $objServicioTecnicoSafeCity = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                            ->findOneBy(array("servicioId" => $objServicosSafeCity->getId()));
                                    
                                    $objServicioTecnicoSafeCity->setInterfaceElementoId($interfaceElementoNuevo->getId());
                                    $objServicioTecnicoSafeCity->setElementoContenedorId($elementoCajaId);
                                    $objServicioTecnicoSafeCity->setElementoConectorId($elementoSplitterId);
                                    $objServicioTecnicoSafeCity->setInterfaceElementoConectorId($interfaceElementoSplitterId);
                                    $this->emComercial->persist($objServicioTecnicoSafeCity);
                                    $this->emComercial->flush();

                                    $objCajaNuevaSafeCity = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                                    ->find($elementoCajaId);

                                    $objInterfaceElementoSplitterNuevoSafeCity = $this->emInfraestructura
                                                                    ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                    ->find($interfaceElementoSplitterId);

                                    $objServicioHistorialSafeCity = new InfoServicioHistorial();
                                    $objServicioHistorialSafeCity->setServicioId($objServicosSafeCity);
                                    $objServicioHistorialSafeCity->setEstado($objServicosSafeCity->getEstado());
                                    $objServicioHistorialSafeCity->setFeCreacion(new \DateTime('now'));
                                    $objServicioHistorialSafeCity->setIpCreacion($ipCreacion);
                                    $objServicioHistorialSafeCity->setAccion("cambio de linea pon");           
                                    $objServicioHistorialSafeCity->setObservacion(" <b>Se hizo cambio de linea pon:</b>"."<br>".
                                        "<b style = 'color: red'>OLT Anterior</b>"."<br>".
                                        "<b>Elemento anterior : </b>".$elementoViejo->getNombreElemento().
                                        "<br> <b>Puerto anterior : </b> " . $interfaceElementoViejo->getNombreInterfaceElemento().
                                        "<br> <b>Elemento conector anterior : </b> ".$elementoContenedorViejo->getNombreElemento().
                                        "<br> <b>Interface elemento conector anterior : </b> ".
                                        $interfaceElementoConectorViejo->getNombreInterfaceElemento()."<br>".
                                        "<b style = 'color: red'>OLT Actual</b>"."<br>".
                                        "<b>Elemento actual :</b>".$elementoNuevo->getNombreElemento().
                                        "<br> <b>Puerto actual :</b> " . $strInterfaceNuevo.
                                        "<br> <b>Elemento conector actual:</b> ".$objCajaNuevaSafeCity->getNombreElemento().
                                        "<br> <b>Interface elemento conector actual:</b> ".
                                        $objInterfaceElementoSplitterNuevoSafeCity->getNombreInterfaceElemento());
                                    $objServicioHistorialSafeCity->setUsrCreacion($usrCreacion);
                                    $this->emComercial->persist($objServicioHistorialSafeCity);
                                    $this->emComercial->flush();
                                }
                            }
                        }
                    } 

                    if((is_object($servicio) && $boolRedGponMpls) &&
                       ($elementoNuevo->getId() != $elementoViejo->getId()))
                    {
                        $arrayRespuestaNoc   = array();
                        $arrayDatosPantilla  = array();
                        $arrayDatosGenrales  = array();
                        $arrayDataGeneral    = array();
                        $arrayDataTarea      = array();
                        $arrayRespuestaTarea = array();

                        if(!isset($arrayIpActivar))
                        {
                            $arrayDatosGenrales = array('usrCreacion'    => $usrCreacion,
                                                        'ipCreacion'     => $ipCreacion,
                                                        'datosNoc'       => $arrayDataNoc,
                                                        'switch'         => $elementoNuevo->getNombreElemento(),
                                                        'servicioCan'    => $arrayIpActivar[0]['id_servicio'],
                                                        'ipServicioCan'  => $arrayIpActivar[0]['ip'],
                                                        'estadoServicio' => $servicio->getEstado(),
                                                        'ont'            => $servicio->getPuntoId()->getLogin(),
                                                        'modeloOnt'      => $modeloOnt,
                                                        'macOnt'         => $spcMacOnt->getValor(),
                                                        'puertoOnt'      => $strInterfaceNuevo);

                            $arrayRespuestaNoc  = $this->notificacionNocSafeCity($servicio,$arrayDatosGenrales);

                            if($arrayRespuestaNoc['status'] == 200 && count($arrayRespuestaNoc['mensaje']) > 0)
                            {

                                $objPuntoMonitoreo   = $servicio->getPuntoId();
                                $objCantonMonitoreo  = $objPuntoMonitoreo->getSectorId()->getParroquiaId()
                                                                        ->getCantonId()->getNombreCanton();    
                                $arrayDatosPantilla = array('cliente'  => $strNombreCliente,
                                                            'login'    => $servicio->getPuntoId()->getLogin(),
                                                            'ont'      => $servicio->getPuntoId()->getLogin().'-ont',
                                                            'olt'      => $elementoViejo->getNombreElemento(),
                                                            'ip_ant_1' => $arrayIpCancelar[0]['ip'],
                                                            'ip_ant_2' => $arrayIpCancelar[1]['ip'],
                                                            'ip_ant_3' => $arrayIpCancelar[2]['ip'],
                                                            'ip_ant_4' => $arrayIpCancelar[3]['ip'],
                                                            'ip_nuv_1' => $arrayIpActivar[0]['ip'],
                                                            'ip_nuv_2' => $arrayIpActivar[1]['ip'],
                                                            'ip_nuv_3' => $arrayIpActivar[2]['ip'],
                                                            'ip_nuv_4' => $arrayIpActivar[3]['ip']);


                                $arrayDataGeneral   = array('canton'  => $objCantonMonitoreo,
                                                            'empresa' => $empresa);
                                
                                $this->notificacionCorreoSafeCity($arrayDatosPantilla, $arrayDataGeneral);

                                $arrayDataTarea     = array('usrCreacion'    => $usrCreacion,
                                                            'ipCreacion'     => $ipCreacion,
                                                            'canton'         => $objCantonMonitoreo,
                                                            'cliente'        => $strNombreCliente,
                                                            'empresa'        => $empresa);
                                                            
                                $arrayRespuestaTarea = $this->creacionTareaAutomaticaSafeCity($arrayDataTarea, $objPuntoMonitoreo);

                                if($arrayRespuestaTarea['status'] != "OK")
                                {
                                    throw new \Exception($arrayRespuestaTarea['mensaje']);
                                }
                            }
                        }
                    }

                    if(!$boolRedGponMpls)
                    {
                        //ELIMINAR INDICE_CLIENTE ANTERIOR
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objIndiceClienteViejoSpc, "Eliminado");
                        
                        //CREAR NUEVO INDICE_CLIENTE
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica( $servicio, 
                                                                                        $objProducto, 
                                                                                        "INDICE CLIENTE", 
                                                                                        $arrayRespuesta['ont_id'],
                                                                                        $usrCreacion);

                        //ELIMINAR SERVICE-PORT ANTERIOR
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($spcSpid, "Eliminado");

                        //CREAR NUEVO SERVICE-PORT
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica( $servicio, 
                                                                                        $objProducto, 
                                                                                        "SPID",
                                                                                        $arrayRespuesta['spid'], 
                                                                                        $usrCreacion);

                            $mensajeFinal = "OK";
                        
                            $arrayParametrosSincronizarWDB = array();
                            $arrayParametrosSincronizarWDB['strProceso']     = 'CAMBIO_LINEA_PON';
                            $arrayParametrosSincronizarWDB['objServicio']    = $servicio;
                            $arrayParametrosSincronizarWDB['strCodEmpresa']  = $empresa;
                            $arrayParametrosSincronizarWDB['strUsrCreacion'] = $usrCreacion;
                            $arrayParametrosSincronizarWDB['strIpCreacion']  = $ipCreacion;
                            $arrayParametrosSincronizarWDB['objProductoInternet'] = $objProducto;
                            $this->servicioGeneral->generarSincronizacionExtenderDualBand($arrayParametrosSincronizarWDB);

                    }                                                                
                }
                else
                {
                    if($strMensajeStatus == "500" || $strMensajeStatus === "ERROR" 
                       || $strMensajeStatus == 0)
                    {
                            //IPS ADICIONALES
                            if(count($arrayIpActivar) > 0)
                            {
                                //ELIMINAR IPS ADICIONALES ANTERIORES
                                for($i = 0; $i < count($arrayIpActivar); $i++)
                                {
                                        $strIpsCancelar = $arrayIpActivar[$i];                          
                                        //ELIMINA IP ANTERIOR
                                        $objIpAdicional    = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                    ->findOneBy(array(  "servicioId"    => $strIpsCancelar['id_servicio'], 
                                                                                        "estado"        => "Reservada"));

                                        $objIpAdicional->setEstado('Eliminado');
                                        $this->emInfraestructura->persist($objIpAdicional);
                                        $this->emInfraestructura->flush();
                                }
                            }
                            if(isset($arrayRespuesta['error']))
                            {
                                throw new \Exception($arrayRespuesta['error']);
                            }
                            else
                            {
                                throw new \Exception($strMensajeCan);
                            }
                    }
                    else
                    {
                        if($statusCancelar == 'ERROR')
                        {
                            $mensajeFinal = $mensajeFinal . $arrayRespuesta['mensaje_cancelar'];
                        }
                        else if($statusActivar == 'ERROR' && $statusCancelar == 'OK')
                        {
                            $mensajeFinal = $mensajeFinal . $arrayRespuesta['mensaje_cancelar'];
                            
                            //IPS ADICIONALES
                            if(count($arrayIpActivar) > 0)
                            {
                                //ELIMINAR IPS ADICIONALES ANTERIORES
                                foreach($arrayRespuesta['ip_cancelar'] as $arrayRespuestaIpCancelar)
                                {
                                    $statusIpCancelar = $arrayRespuestaIpCancelar['status'];

                                    if($statusIpCancelar == 'OK')
                                    {
                                        //ELIMINA IP ANTERIOR
                                        $objIpAdicional    = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                    ->findOneBy(array(  "servicioId"    => $arrayRespuestaIpCancelar['id_servicio'], 
                                                                                        "estado"        => "Activo"));

                                        $objIpAdicional->setEstado('Eliminado');
                                        $this->emInfraestructura->persist($objIpAdicional);
                                        $this->emInfraestructura->flush();

                                        if (is_object($spcScope))
                                        {
                                            //ELIMINAR CARACTERISTICA SCOPE ANTERIOR
                                            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($spcScope, 'Eliminado');
                                        }
                                    }

                                    $mensajeFinal = $mensajeFinal . $arrayRespuestaIpCancelar['mensaje'];
                                }
                            }
                            
                            $mensajeFinal = $mensajeFinal . $arrayRespuesta['mensaje_activar'];
                        }
                        else
                        {
                            $mensajeFinal = "Cancelar: ".$arrayRespuesta['mensaje_cancelar']." Activar: ".$arrayRespuesta['mensaje_activar'];
                        }
                        
                        throw new \Exception($mensajeFinal);
                    }
                }
            }
            else
            {
                if(isset($arrayDatos[0]['esIsb']) && !empty($arrayDatos[0]['esIsb']) && $arrayDatos[0]['esIsb'] === "SI")
                {
                    $arrayFinal[]   = array('status'    => "ERROR",
                                            'mensaje'   => "El OLT considerado no soporta el esquema del middleware"
                                                            . "Favor Comunicarse con Sistemas!");
                    return $arrayFinal;
                }
                $planEdicionLimitada        = 'NO';
                //obtener caracteristica plan edicion limitada
                $caractEdicionLimitada      = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                ->findOneBy(array("descripcionCaracteristica"=>"EDICION LIMITADA",
                                                                                  "estado"                   =>"Activo"));
                $planCabNuevo               = $this->emComercial->getRepository('schemaBundle:InfoPlanCab')
                                                                ->find($servicio->getPlanId()->getId());
                $planCaractEdicionLimitada  = $this->emComercial->getRepository('schemaBundle:InfoPlanCaracteristica')
                                                                ->findOneBy(array("planId"            =>$planCabNuevo->getId(),
                                                                                  "caracteristicaId"  =>$caractEdicionLimitada->getId(),
                                                                                  "estado"            =>$planCabNuevo->getEstado()));
                if($planCaractEdicionLimitada)
                {
                    $planEdicionLimitada = $planCaractEdicionLimitada->getValor();
                }
                
                if($totalIpPto > 0 && $planEdicionLimitada == 'NO')
                {
                    //valida si el cambio se lo realiza en el mismo o en otro olt con la finalidad de mantener las mismas ips.
                    if($elementoNuevo->getId() != $elementoViejo->getId())
                    {
                        //solicito las ips segun el modelo

                        $arregloIps = $this->recursosRed->getIpsDisponibleScopeOlt( $totalIpPto, 
                                                                                    $elementoNuevo->getId(), 
                                                                                    $servicio->getId(), 
                                                                                    $servicio->getPuntoId()->getId(), 
                                                                                    "SI", 
                                                                                    $servicio->getPlanId()->getId());
                        $arrayIps = $arregloIps['ips'];

                        if($arregloIps['error'])
                        {
                            //reversar cambio de puerto logico
                            $this->cambiarPuertoLogicoMd(   $servicio, 
                                                            $servicioTecnico, 
                                                            $elementoViejo->getId(), 
                                                            $interfaceElementoViejo->getId(),
                                                            $elementoContenedorViejo->getId(), 
                                                            $elementoConectorViejo->getId(), 
                                                            $interfaceElementoConectorViejo->getId(),
                                                            $usrCreacion, 
                                                            $ipCreacion,
                                                            $empresa  );

                            $arrayFinal[] = array('status' => "ERROR", 'mensaje' => $arregloIps['error']);

                            $punto = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($servicio->getPuntoId());
                            //Envío de notificación de creación de errores
                            /* @var $envioPlantilla EnvioPlantilla */
                            $asunto     = "Notificación de errores al activar cambio de línea Pon";
                            $parametros = array('login' => $punto->getLogin(),
                                                'olt'   => $elementoNuevo->getNombreElemento(),
                                                'error' => $arregloIps['error']);
                            $this->correo->generarEnvioPlantilla($asunto, $to, 'ECLP', $parametros, '', '', '');
                            return $arrayFinal;
                        }
                    }
                     //el cambio es en el mismo olt
                    else
                    {
                        //ingreso la ip del servicio al arreglo
                        $ipServicio = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')->findOneBy(array(  "servicioId"  => $servicioId,
                                                                                                                        "estado"      => "Activo"));
                        if($ipServicio)
                        {
                            $arrayIps[] = array("ip" => $ipServicio->getIp(), "tipo" => $ipServicio->getTipoIp());
                        }
                        //obtengo las ip de los servicios adicionales
                        for($i = 0; $i < count($arrayServicioIp); $i++)
                        {

                            if($arrayServicioIp[$i]['idServicio'])
                            {
                                $ipViejas = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                    ->findOneBy(array(  "servicioId"    => $arrayServicioIp[$i]['idServicio'],
                                                                                        "estado"        => "Activo"));
                                if($ipViejas)
                                {
                                    $arrayIps[] = array("ip" => $ipViejas->getIp(), "tipo" => $ipViejas->getTipoIp());
                                }
                            }
                        }
                    }
                    //cancelar servicio e ips adicionales

                    for($i = 0; $i < $totalIpPto; $i++)
                    {
                        $tmp = $i;
                        //si la ip esta dentro del plan de internet
                        if($flagProdViejo == 1 && $planConIp == 0)
                        {
                            $servicioIpPlan = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                      ->findOneBy(array("servicioId" => $servicio->getId(), "estado" => "Activo"));

                            //obtener caracteristica scope
                            $spcScope = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SCOPE", $objProducto);
                            if(!$spcScope)
                            {
                                //obtener ip fija
                                $ipFija = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                  ->findOneBy(array("servicioId" => $servicio->getId(), "estado" => "Activo"));

                                //buscar scopes
                                $arrayScopeOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                         ->getScopePorIpFija($ipFija->getIp(), $servicioTecnico->getElementoId());

                                if(!$arrayScopeOlt)
                                {
                                    $arrayFinal[] = array('status' => "ERROR",
                                                          'mensaje' => "Ip Fija " . $ipFija->getIp() . " no pertenece a un Scope! <br>"
                                                                       . "Favor Comunicarse con el Dep. Gepon!");
                                    return $arrayFinal;
                                }

                                $scope = $arrayScopeOlt['NOMBRE_SCOPE'];
                            }
                            else
                            {
                                $scope = $spcScope->getValor();
                            }

                            //cancelamos (script) servicio con ip
                            $arrayParametros = array(
                                'servicioTecnico'   => $servicioTecnico,
                                'interfaceElemento' => $interfaceElementoViejo,
                                'modeloElemento'    => $elementoViejo->getModeloElementoId(),
                                'producto'          => $objProducto,
                                'login'             => $servicio->getPuntoId()->getLogin(),
                                'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                                'spcSpid'           => $spcSpid,
                                'spcMacOnt'         => $spcMacOnt,
                                'scope'             => $scope,
                                'idEmpresa'         => $empresa,
                                'ipCreacion'        => $ipCreacion,
                                'usrCreacion'       => $usrCreacion);

                            $respuestaArrayCancel = $this->cancelar->cancelarServicioMdConIp($arrayParametros);
                            $statusCancel = $respuestaArrayCancel[0]['status'];

                            if($statusCancel == "OK")
                            {
                                //eliminamos ip vieja
                                if($servicioIpPlan)
                                {
                                    $servicioIpPlan->setEstado("Eliminado");
                                    $this->emInfraestructura->persist($servicioIpPlan);
                                    $this->emInfraestructura->flush();
                                }

                                $planConIp = 1;
                            }
                            else
                            {
                                $arrayFinal[] = array('status' => "ERROR", 
                                                      'mensaje' => 'No se pudo Cancelar el servicio con IP, <br>'
                                                                   .'Favor verificar todos los datos!</br>' . $respuestaArrayCancel[0]['mensaje']);
                                return $arrayFinal;
                            }
                        }
                        else if($flagProdViejo == 0 && $planConIp == 0)
                        {

                            $arrayParametros = array(
                                'servicioTecnico'   => $servicioTecnico,
                                'interfaceElemento' => $interfaceElementoViejo,
                                'modeloElemento'    => $elementoViejo->getModeloElementoId(),
                                'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                                'login'             => $servicio->getPuntoId()->getLogin(),
                                'spcSpid'           => $spcSpid,
                                'spcMacOnt'         => $spcMacOnt,
                                'idEmpresa'         => $empresa
                            );
                            $respuestaArrayCancel = $this->cancelar->cancelarServicioMdSinIp($arrayParametros);

                            $statusCancel = $respuestaArrayCancel[0]['status'];

                            if($statusCancel == "ERROR")
                            {
                                $arrayFinal[] = array('status' => "ERROR", 
                                                      'mensaje'=> 'No se pudo Cancelar el servicio sin Ip, <br>'
                                                                  .'Favor verificar todos los datos!</br>' . $respuestaArrayCancel[0]['mensaje']);
                                return $arrayFinal;
                            }

                            //si entra por aqui significa es un plan sin ip pero con una ip adicional o fija.. aumento el  $totalIpPto
                            $proConIpAdi = "OK";
                            $totalIpPto++;
                            $planConIp = 1;
                        }
                        else
                        {
                            //servicio adicional de ip
                            $servicioIpAdicional = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                     ->find($arrayServicioIp[$i]['idServicio']);
                            $strScopeAdicional = '';

                            $objSpcMacInterno = $this->servicioGeneral->getServicioProductoCaracteristica($servicioIpAdicional, "MAC", $objProducto);

                            //obtener caracteristica scope
                            $objSpcScopeAdi = $this->servicioGeneral->getServicioProductoCaracteristica($servicioIpAdicional, "SCOPE", $objProducto);
                            if(!is_object($objSpcScopeAdi))
                            {
                                //obtener ip fija
                                $ipFija = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                    ->findOneBy(array("servicioId" => $servicioIpAdicional->getId(), "estado" => "Activo"));

                                //buscar scopes
                                $arrayScopeOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                    ->getScopePorIpFija($ipFija->getIp(), $servicioTecnico->getElementoId());

                                if(!$arrayScopeOlt)
                                {
                                    $arrayFinal[] = array('status' => "ERROR", 'mensaje' => "Ip Fija Adicional no pertenece a un Scope! <br>"
                                        . "Favor Comunicarse con el Dep. Gepon!");
                                    return $arrayFinal;
                                }

                                $strScopeAdicional = $arrayScopeOlt['NOMBRE_SCOPE'];
                            }
                            else
                            {
                                $strScopeAdicional = $objSpcScopeAdi->getValor();
                            }

                            $arrParametrosCancel = array(
                                'servicioTecnico'   => $servicioTecnico,
                                'modeloElemento'    => $elementoViejo->getModeloElementoId(),
                                'interfaceElemento' => $interfaceElementoViejo,
                                'producto'          => $objProducto,
                                'servicio'          => $servicioIpAdicional,
                                'spcIndiceCliente'  => $objIndiceClienteViejoSpc,
                                'spcMac'            => $objSpcMacInterno,
                                'scope'             => $strScopeAdicional
                            );

                            //cancelar (script) ip adicional
                            $this->cancelar->cancelarServicioIp($arrParametrosCancel);

                            //eliminar (base) ip adicional
                            $ipAdicional = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                   ->findOneBy(array("servicioId"   => $servicioIpAdicional->getId(),
                                                                                     "estado"       => "Activo"));
                            if($ipAdicional)
                            {
                                $ipAdicional->setEstado("Eliminado");
                                $this->emInfraestructura->persist($ipAdicional);
                                $this->emInfraestructura->flush();
                            }
                        }
                    }
                    //Estado eliminado a los indices de la tabla producto servicio caracteristica
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objIndiceClienteViejoSpc, "Eliminado");

                    //realizar cambio de puerto logico para la activacion
                    $this->cambiarPuertoLogicoMd(   $servicio, 
                                                    $servicioTecnico, 
                                                    $elementoNuevo->getId(), 
                                                    $interfaceElementoNuevo->getId(), 
                                                    $elementoCajaId,
                                                    $elementoSplitterId, 
                                                    $interfaceElementoSplitterId, 
                                                    $usrCreacion, 
                                                    $ipCreacion,
                                                    $empresa);

                    //activar servicio e ips adicional
                    $planConIp = 0;
                    for($i = 0; $i < $totalIpPto; $i++)
                    {
                        $tmp = $i;
                        //si la ip esta dentro del plan de internet

                        if($flagProdViejo == 1 && $planConIp == 0)
                        {
                            //reservamos la ip nueva
                            $ipFija = new InfoIp();
                            $ipFija->setIp($arrayIps[$i]['ip']);
                            $ipFija->setEstado("Reservada");
                            $ipFija->setTipoIp($arrayIps[$i]['tipo']);
                            $ipFija->setServicioId($servicio->getId());
                            $ipFija->setUsrCreacion($usrCreacion);
                            $ipFija->setFeCreacion(new \DateTime('now'));
                            $ipFija->setIpCreacion($ipCreacion);
                            $this->emInfraestructura->persist($ipFija);
                            $this->emInfraestructura->flush();

                            //buscar caracteristicas para olt huawei
                            //obtener service profile
                            $objSrvProfileProdCaract = $this->servicioGeneral
                                                            ->getServicioProductoCaracteristica($servicio, "SERVICE-PROFILE", $objProducto);
                            if(is_object($objSrvProfileProdCaract))
                            {
                                $serviceProfile = $objSrvProfileProdCaract->getValor();
                            }
                            else
                            {
                            //buscar el service profile en el elemento
                                $elemento = $interfaceElementoNuevo->getElementoId();
                                $detalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                           ->findOneBy(array("detalleNombre"=> "SERVICE-PROFILE-NAME",
                                                                                             "detalleValor" => $modeloOnt,
                                                                                             "elementoId"   => $elemento->getId()));
                                if($detalleElemento)
                                {
                                    $serviceProfile = $detalleElemento->getDetalleValor();

                                    //servicio prod caract service-profile
                                    $this->servicioGeneral
                                         ->ingresarServicioProductoCaracteristica($servicio, $objProducto, "SERVICE-PROFILE", 
                                                                                  $serviceProfile, $usrCreacion);
                                }
                                else
                                {
                                    $respuestaFinal[] = array('status' => 'ERROR',
                                                             'mensaje' => 'No existe Caracteristica SERVICE-PROFILE-NAME en el elemento, favor revisar!');
                                    return $respuestaFinal;
                                }
                            }

                            $arrayParametros = array(
                                'servicio'          => $servicio,
                                'servicioTecnico'   => $servicioTecnico,
                                'interfaceElemento' => $interfaceElementoNuevo,
                                'modeloElemento'    => $elementoNuevo->getModeloElementoId(),
                                'producto'          => $objProducto,
                                'macOnt'            => $strMacOnt,
                                'macWifi'           => $macWifi,
                                'perfil'            => $perfil,
                                'login'             => $servicio->getPuntoId()->getLogin(),
                                'ontLineProfile'    => $valorLineProfile,
                                'serviceProfile'    => $serviceProfile,
                                'serieOnt'          => $serieOnt,
                                'vlan'              => $valorVlan,
                                'gemPort'           => $valorGemport,
                                'trafficTable'      => $valorTraffic,
                                'usrCreacion'       => $usrCreacion
                            );
                            //activamos servicio con ip
                            $respuestaArrayActivar = $this->activar->activarClienteMdConIp($arrayParametros);
                            $statusActivar = $respuestaArrayActivar[0]['status'];

                            if($statusActivar == "OK")
                            {

                                $ipFija->setEstado("Activo");
                                $this->emInfraestructura->persist($ipFija);
                                $this->emInfraestructura->flush();

                                //guardamos el indice
                                $indiceCliente = $respuestaArrayActivar[0]['mensaje'];

                                $this->servicioGeneral
                                     ->ingresarServicioProductoCaracteristica($servicio, $objProducto, "INDICE CLIENTE", 
                                                                              $indiceCliente, $usrCreacion);

                                $arraySpid = array( 'modeloElemento'    => $modeloElementoNuevo,
                                                    'interfaceElemento' => $interfaceElementoNuevo,
                                                    'ontId'             => $indiceCliente,
                                                    'servicioTecnico'   => $servicioTecnico);

                                $resultArraySpid = $this->getSpidHuawei($arraySpid);

                                $spidStatus = $resultArraySpid[0]['status'];
                                if($spidStatus == 'ERROR')
                                {
                                    $arrayFinal[] = array('status' => "ERROR",
                                                          'mensaje' => 'No se pudo consultar el spid, <br>'. $resultArraySpid['mensaje']);
                                    return $arrayFinal;
                                }
                                else
                                {
                                    $objSpidViejoSpc = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SPID", $objProducto);
                                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpidViejoSpc, "Eliminado");

                                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $servicio, 
                                                                                                    $objProducto, 
                                                                                                    "SPID",
                                                                                                    $resultArraySpid[0]['mensaje'], 
                                                                                                    $usrCreacion);
                                }

                                $planConIp = 1;
                            }
                            else
                            {
                                //reversar cambio de puerto logico
                                $this->cambiarPuertoLogicoMd(   $servicio, 
                                                                $servicioTecnico, 
                                                                $elementoViejo->getId(), 
                                                                $interfaceElementoViejo->getId(),
                                                                $elementoContenedorViejo->getId(), 
                                                                $elementoConectorViejo->getId(), 
                                                                $interfaceElementoConectorViejo->getId(), 
                                                                $usrCreacion, 
                                                                $ipCreacion,
                                                                $empresa);

                                $ipFija->setEstado("Eliminado");
                                $this->emInfraestructura->persist($ipFija);
                                $this->emInfraestructura->flush();

                                $arrayParametros = array(
                                    'servicio'          => $servicio,
                                    'servicioTecnico'   => $servicioTecnico,
                                    'interfaceElemento' => $interfaceElementoViejo,
                                    'modeloElemento'    => $elementoViejo->getModeloElementoId(),
                                    'producto'          => $objProducto,
                                    'macOnt'            => $strMacOnt,
                                    'macWifi'           => $macWifi,
                                    'perfil'            => $perfil,
                                    'login'             => $servicio->getPuntoId()->getLogin(),
                                    'ontLineProfile'    => $valorLineProfile,
                                    'serviceProfile'    => $serviceProfile,
                                    'serieOnt'          => $serieOnt,
                                    'vlan'              => $valorVlan,
                                    'gemPort'           => $valorGemport,
                                    'trafficTable'      => $valorTraffic,
                                    'usrCreacion'       => $usrCreacion
                                );

                                //activamos servicio con ip del puerto anterior
                                $respuestaArrayActivarClie = $this->activar->activarClienteMdConIp($arrayParametros);
                                //activo el indice viejo del cliente
                                $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objIndiceClienteViejoSpc, "Activo");
                                $arrayFinal[] = array('status' => "ERROR", 
                                                      'mensaje' => 'No se pudo Activar cliente con Ip, <br>'
                                                                   . 'Favor verificar todos los datos!<br>' . $respuestaArrayActivar[0]['mensaje']);
                                return $arrayFinal;
                            }
                        }
                        else if($flagProdViejo == 0 && $planConIp == 0)
                        {
                            $arrayParametros = array(
                                'servicioTecnico'   => $servicioTecnico,
                                'interfaceElemento' => $interfaceElementoNuevo,
                                'modeloElemento'    => $elementoNuevo->getModeloElementoId(),
                                'macOnt'            => $strMacOnt,
                                'perfil'            => $perfil,
                                'login'             => $servicio->getPuntoId()->getLogin(),
                                'ontLineProfile'    => $valorLineProfile,
                                'serviceProfile'    => $modeloOnt,
                                'serieOnt'          => $serieOnt,
                                'vlan'              => $valorVlan, //VLAN
                                'gemPort'           => $valorGemport, //GEM-PORT
                                'trafficTable'      => $valorTraffic //TRAFFIC-TABLE
                            );
                            //activamos servicio sin ip
                            $respuestaArrayActivar = $this->activar->activarClienteMdSinIp($arrayParametros);
                            $statusActivar = $respuestaArrayActivar[0]['status'];
                            if($statusActivar == "ERROR")
                            {
                                //reversar cambio de puerto logico
                                $this->cambiarPuertoLogicoMd(   $servicio, 
                                                                $servicioTecnico, 
                                                                $elementoViejo->getId(), 
                                                                $interfaceElementoViejo->getId(), 
                                                                $elementoContenedorViejo->getId(), 
                                                                $elementoConectorViejo->getId(), 
                                                                $interfaceElementoConectorViejo->getId(),
                                                                $usrCreacion, 
                                                                $ipCreacion,
                                                                $empresa);

                                $arrayParametros = array(
                                    'servicioTecnico'   => $servicioTecnico,
                                    'interfaceElemento' => $interfaceElementoNuevo,
                                    'modeloElemento'    => $elementoViejo->getModeloElementoId(),
                                    'macOnt'            => $strMacOnt,
                                    'perfil'            => $perfil,
                                    'login'             => $servicio->getPuntoId()->getLogin(),
                                    'ontLineProfile'    => $valorLineProfile,
                                    'serviceProfile'    => $modeloOnt,
                                    'serieOnt'          => $serieOnt,
                                    'vlan'              => $valorVlan, //VLAN
                                    'gemPort'           => $valorGemport, //GEM-PORT
                                    'trafficTable'      => $valorTraffic //TRAFFIC-TABLE
                                );

                                //activamos servicio sin ip del puerto anterior
                                $respuestaArrayActivarClie = $this->activar->activarClienteMdSinIp($arrayParametros);
                                //activamos el indice anterior del cliente
                                $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objIndiceClienteViejoSpc, "Activo");
                                $arrayFinal[] = array('status' => "ERROR", 
                                                      'mensaje' => 'No se pudo Activar el Puerto Nuevo, <br>'
                                                                   .'Favor verificar todos los datos!<br>' . $respuestaArrayActivar[0]['mensaje']);
                                return $arrayFinal;
                            }
                            $indiceCliente = $respuestaArrayActivar[0]['mensaje'];
                            //creacion de indice en la tabla servicio producto caracteristica
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, $objProducto, "INDICE CLIENTE", $indiceCliente,
                                                                                           $usrCreacion);

                            $arraySpid = array( 'modeloElemento'    => $modeloElementoNuevo,
                                                'interfaceElemento' => $interfaceElementoNuevo,
                                                'ontId'             => $indiceCliente,
                                                'servicioTecnico'   => $servicioTecnico);

                            $resultArraySpid = $this->getSpidHuawei($arraySpid);

                            $spidStatus = $resultArraySpid[0]['status'];
                            if($spidStatus == 'ERROR')
                            {
                                $arrayFinal[] = array('status' => "ERROR",
                                                      'mensaje' => 'No se pudo consultar el spid, <br>' . $resultArraySpid['mensaje']);
                                return $arrayFinal;
                            }
                            else
                            {
                                $objSpcSpidViejo = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SPID", $objProducto);
                                $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcSpidViejo, "Eliminado");

                                $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, $objProducto, "SPID", 
                                                                                               $resultArraySpid[0]['mensaje'], $usrCreacion);
                            }


                            $planConIp = 1;
                        }
                        else
                        {
                            //servicio adicional de ip
                            $servicioIpAdicional = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                     ->findOneById($arrayServicioIp[$i]['idServicio']);

                            $objSpcMacIp = $this->servicioGeneral->getServicioProductoCaracteristica($servicioIpAdicional, "MAC", $objProducto);

                            if(is_object($objSpcMacIp))
                            {
                                $strMacIpInterna = $objSpcMacIp->getValor();
                            }


                            if($proConIpAdi == "OK")
                            {
                                $ipCliente      = $arrayIps[$i - 1]['ip'];
                                $tipoIpCliente  = $arrayIps[$i - 1]['tipo'];
                                $strMacIpInterna= $strMacOnt;
                            }
                            else
                            {
                                $ipCliente = $arrayIps[$i]['ip'];
                                $tipoIpCliente = $arrayIps[$i]['tipo'];
                            }

                            //reservamos la ip nueva
                            $ipFija = new InfoIp();
                            $ipFija->setIp($ipCliente);
                            $ipFija->setEstado("Reservada");
                            $ipFija->setTipoIp($tipoIpCliente);
                            $ipFija->setServicioId($servicioIpAdicional->getId());
                            $ipFija->setUsrCreacion($usrCreacion);
                            $ipFija->setFeCreacion(new \DateTime('now'));
                            $ipFija->setIpCreacion($ipCreacion);
                            $this->emInfraestructura->persist($ipFija);
                            $this->emInfraestructura->flush();

                            $arrayPeticiones['ipFija']      = $ipCliente;
                            $arrayPeticiones['mac']         = $strMacIpInterna;
                            $arrayPeticiones['idServicio']  = $servicioIpAdicional->getId();
                            $arrayPeticiones['idEmpresa']   = $empresa;
                            $arrayPeticiones['usrCreacion'] = $usrCreacion;
                            $arrayPeticiones['ipCreacion']  = $ipCreacion;

                            $activarServicioIp = $this->activarIpAdicionalHuawei($arrayPeticiones);

                            //activar (script y base) ip adicional
                            $activarServicioIpStatus = $activarServicioIp['status'];
                            if($activarServicioIpStatus == 'ERROR')
                            {
                                $arrayFinal[] = array('status' => "ERROR",
                                    'mensaje' => 'No se pudo Activar la ip adicional, <br>'
                                    . $activarServicioIp['mensaje']);
                                return $arrayFinal;
                            }
                        }
                    }
                    
                    //actualizo la info tecnica de las ip productos
                    foreach($arrayServicioIpProducto as $servicioProducto)
                    {
                        $servicioTecnicoIp = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                               ->findOneByServicioId($servicioProducto['idServicio']);

                        if($servicioTecnicoIp)
                        {
                            $servicioTecnicoIp->setElementoId($elementoNuevo->getId());
                            $servicioTecnicoIp->setInterfaceElementoId($interfaceElementoNuevo->getId());
                            $servicioTecnicoIp->setElementoContenedorId($elementoCajaId);
                            $servicioTecnicoIp->setElementoConectorId($elementoSplitterId);
                            $servicioTecnicoIp->setInterfaceElementoConectorId($interfaceElementoSplitterId);
                            $this->emComercial->persist($servicioTecnicoIp);
                            $this->emComercial->flush();
                        }
                    }
                }//planes sin ip
                else
                {
                    $spcSpid = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SPID", $objProducto);
                    $spcMacOnt = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC ONT", $objProducto);
                    //cancelar servicio
                    $servProdCaractIndiceCliente = $this->servicioGeneral
                                                        ->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $objProducto);
                    $arrayParametrosCancel = array(
                        'servicioTecnico'   => $servicioTecnico,
                        'interfaceElemento' => $interfaceElementoViejo,
                        'modeloElemento'    => $elementoViejo->getModeloElementoId(),
                        'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                        'login'             => $servicio->getPuntoId()->getLogin(),
                        'spcSpid'           => $spcSpid,
                        'spcMacOnt'         => $spcMacOnt,
                        'idEmpresa'         => $empresa
                    );
                    $respuestaArrayCancel = $this->cancelar->cancelarServicioMdSinIp($arrayParametrosCancel);
                    $statusCancel = $respuestaArrayCancel[0]['status'];

                    if($statusCancel == "ERROR")
                    {
                        $arrayFinal[] = array('status' => "ERROR", 
                                              'mensaje' => 'No se pudo Cancelar el servicio sin IP, <br>'
                                                           . 'Favor verificar todos los datos!<br>' . $respuestaArrayCancel[0]['mensaje']);
                        return $arrayFinal;
                    }

                    //poner estado eliminado a los indices de la tabla producto servicio caracteristica
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objIndiceClienteViejoSpc, "Eliminado");

                    //realizar cambio de puerto logico para la activacion
                    $resultado = $this->cambiarPuertoLogicoMd(  $servicio, 
                                                                $servicioTecnico, 
                                                                $elementoNuevo->getId(), 
                                                                $interfaceElementoNuevo->getId(), 
                                                                $elementoCajaId, 
                                                                $elementoSplitterId, 
                                                                $interfaceElementoSplitterId, 
                                                                $usrCreacion, 
                                                                $ipCreacion,
                                                                $empresa);

                    $arrayParametros = array(
                        'servicioTecnico'   => $servicioTecnico,
                        'interfaceElemento' => $interfaceElementoNuevo,
                        'modeloElemento'    => $elementoNuevo->getModeloElementoId(),
                        'macOnt'            => $strMacOnt,
                        'perfil'            => $perfil,
                        'login'             => $servicio->getPuntoId()->getLogin(),
                        'ontLineProfile'    => $valorLineProfile,
                        'serviceProfile'    => $modeloOnt,
                        'serieOnt'          => $serieOnt,
                        'vlan'              => $valorVlan, //VLAN
                        'gemPort'           => $valorGemport, //GEM-PORT
                        'trafficTable'      => $valorTraffic //TRAFFIC-TABLE
                    );

                    //activamos servicio sin ip
                    $respuestaArrayActivar = $this->activar->activarClienteMdSinIp($arrayParametros);
                    $statusActivar = $respuestaArrayActivar[0]['status'];
                    $indiceCliente = $respuestaArrayActivar[0]['mensaje'];
                    if($statusActivar == "ERROR")
                    {
                        //reversar cambio de puerto logico
                        $this->cambiarPuertoLogicoMd(   $servicio, 
                                                        $servicioTecnico,
                                                        $elementoViejo->getId(), 
                                                        $interfaceElementoViejo->getId(), 
                                                        $elementoContenedorViejo->getId(), 
                                                        $elementoConectorViejo->getId(), 
                                                        $interfaceElementoConectorViejo->getId(), 
                                                        $usrCreacion, 
                                                        $ipCreacion,
                                                        $empresa);

                        $arrayParametros = array(
                            'servicioTecnico'   => $servicioTecnico,
                            'interfaceElemento' => $interfaceElementoViejo,
                            'modeloElemento'    => $elementoViejo->getModeloElementoId(),
                            'macOnt'            => $strMacOnt,
                            'perfil'            => $perfil,
                            'login'             => $servicio->getPuntoId()->getLogin(),
                            'ontLineProfile'    => $valorLineProfile,
                            'serviceProfile'    => $modeloOnt,
                            'serieOnt'          => $serieOnt,
                            'vlan'              => $valorVlan, //VLAN
                            'gemPort'           => $valorGemport, //GEM-PORT
                            'trafficTable'      => $valorTraffic //TRAFFIC-TABLE
                        );

                        //activamos servicio sin ip del puerto anterior
                        $respuestaArrayActivarClie = $this->activar->activarClienteMdSinIp($arrayParametros);

                        //activamos el indice anterior del cliente
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objIndiceClienteViejoSpc, "Activo");
                        $arrayFinal[] = array('status' => "ERROR", 
                                              'mensaje' => 'No se pudo Activar el Puerto Nuevo, <br>'
                                                           . 'Favor verificar todos los datos!<br>' . $respuestaArrayActivar[0]['mensaje']);
                        return $arrayFinal;
                    }

                    //validacion que se ingresa para que los clientes de un olt sean migrados a otro y no se haga cambios en el CNR :/
                    if($objDetalleElemento)
                    {
                        /*CONFIGURAR IP FIJA --------------------------------------------------------*/
                        $scriptArrayIpFija  = $this->servicioGeneral->obtenerArregloScript("configurarIpFija", $modeloElementoNuevo);
                        $idDocumentoIpFja   = $scriptArrayIpFija[0]->idDocumento;
                        $usuario            = $scriptArrayIpFija[0]->usuario;

                        //*----------------------------------------------------------------------*/
                        //dividir interface para obtener tarjeta y puerto pon
                        list($tarjeta, $puertoPon) = split('/', $interfaceElementoNuevo->getNombreInterfaceElemento());

                        //activo las ips
                        for($i = 0; $i < count($arrayServicioIpProducto); $i++)
                        {
                            $servicioTecnicoIpProd = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                                             ->findOneBy(array("servicioId" => $arrayServicioIpProducto[$i]));

                            $interfaceCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                        ->find($servicioTecnicoIpProd->getInterfaceElementoClienteId());


                            $arrayParametrosIpFija = array(
                                'elementoId'    => $interfaceElementoNuevo->getElementoId()->getId(),
                                'idDocumento'   => $idDocumentoIpFja,
                                'usuario'       => $usuario,
                                'tarjeta'       => $tarjeta,
                                'puertoPon'     => $puertoPon,
                                'ontId'         => $indiceCliente,
                                'puertoOnt'     => $interfaceCliente->getNombreInterfaceElemento()
                            );
                            $resultadoJsonIpFija = $this->activar->activarIpFijaHuawei($arrayParametrosIpFija);

                            if($resultadoJsonIpFija->status != "OK")
                            {
                                $respuestaArray[] = array('status' => 'ERROR',
                                    'mensaje' => "Activacion Puerto Ont: " . $resultadoJsonIpFija->mensaje);
                                return $respuestaArray;
                            }
                            //actualizar info tecnica
                            $servicioTecnicoIpProd->setElementoId($elementoNuevo->getId());
                            $servicioTecnicoIpProd->setInterfaceElementoId($interfaceElementoNuevo->getId());
                            $servicioTecnicoIpProd->setElementoContenedorId($elementoCajaId);
                            $servicioTecnicoIpProd->setElementoConectorId($elementoSplitterId);
                            $servicioTecnicoIpProd->setInterfaceElementoConectorId($interfaceElementoSplitterId);
                            $this->emInfraestructura->persist($servicioTecnicoIpProd);
                            $this->emInfraestructura->flush();                     

                        }
                    }

                    $this->servicioGeneral
                         ->ingresarServicioProductoCaracteristica($servicio, $objProducto, "INDICE CLIENTE", $indiceCliente, $usrCreacion);

                    $arraySpid = array( 'modeloElemento'    => $modeloElementoNuevo,
                                        'interfaceElemento' => $interfaceElementoNuevo,
                                        'ontId'             => $indiceCliente,
                                        'servicioTecnico'   => $servicioTecnico);

                    $resultArraySpid = $this->getSpidHuawei($arraySpid);
                    $spidStatus = $resultArraySpid[0]['status'];
                    if($spidStatus != 'OK')
                    {
                        $arrayFinal[] = array('status' => "ERROR",
                                              'mensaje' => 'No se pudo consultar el spidddd, <br>'. $resultArraySpid['mensaje']);
                        return $arrayFinal;
                    }
                    else
                    {
                        $objSpidViejoSpcInterno = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SPID", $objProducto);
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpidViejoSpcInterno, "Eliminado");

                        $this->servicioGeneral
                             ->ingresarServicioProductoCaracteristica($servicio, $objProducto, "SPID", $resultArraySpid[0]['mensaje'], $usrCreacion);
                    }
                }//else
                
                $mensajeFinal = 'OK';
            }
            
            //finalizar solicitud
            $objSolicitud->setObservacion('Se finaliza Solicitud, por ejecucion de cambio de linea pon');
            $objSolicitud->setEstado('Finalizada');
            $this->emComercial->persist($objSolicitud);
            $this->emComercial->flush();

            //historial de la solicitud
            $InfoDetalleSolHist = new InfoDetalleSolHist();
            $InfoDetalleSolHist->setDetalleSolicitudId($objSolicitud);
            $InfoDetalleSolHist->setObservacion('Se finaliza Solicitud, por ejecucion de cambio de linea pon');
            $InfoDetalleSolHist->setUsrCreacion($usrCreacion);
            $InfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
            $InfoDetalleSolHist->setEstado('Finalizada');
            $this->emComercial->persist($InfoDetalleSolHist);
            $this->emComercial->flush();
            $arrayDataConfirmacionTn['datos']['respuesta_confirmacion'] = "OK";
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
            if(!$boolRedGponMpls)
            {
                $this->rdaMiddleware->ejecutaWsSinEsperarRespuestaMiddleware($arrayDataConfirmacionTn);
            }
            $arrayFinal[] = array('status' => "ERROR", 'mensaje' => $e->getMessage());
            return $arrayFinal;
        }

        //*DECLARACION DE COMMITS*/
        if($this->emInfraestructura->getConnection()->isTransactionActive())
        {
           $this->emInfraestructura->getConnection()->commit();
        }

        if($this->emComercial->getConnection()->isTransactionActive())
        {
           $this->emComercial->getConnection()->commit();
        }

        try 
        {
            if(!$boolRedGponMpls)
            {
                    //*----------------------------------------------------------------------*/
                //reconfiguro en el ldap
                $mixResultadoJsonLdap = $this->servicioGeneral->ejecutarComandoLdap("A", $servicioId, $strPrefijoEmpresa);
                if($mixResultadoJsonLdap->status != "OK")
                {
                    $this->rdaMiddleware->ejecutaWsSinEsperarRespuestaMiddleware($arrayDataConfirmacionTn);
                    $arrayFinal[] = array('status' => "OK",
                                        'mensaje' => "Se ejecuto el cambio de linea Pon, pero no se ejecuto en el ldap " . $mixResultadoJsonLdap->mensaje);
                    return $arrayFinal;
                }

                //EJECUTAR VALIDACIÓN DE PROMOCIONES BW
                $arrayParametrosInfoBw = array();
                $arrayParametrosInfoBw['intIdServicio']     = $servicio->getId();
                $arrayParametrosInfoBw['intIdEmpresa']      = $empresa;
                $arrayParametrosInfoBw['strTipoProceso']    = "CAMBIO_LINEA_PON";
                $arrayParametrosInfoBw['strValor']          = $intIdElementoOltNuevo;
                $arrayParametrosInfoBw['strUsrCreacion']    = $usrCreacion;
                $arrayParametrosInfoBw['strIpCreacion']     = $ipCreacion;
                $arrayParametrosInfoBw['strPrefijoEmpresa'] = $strPrefijoEmpresa;
                $this->servicePromociones->configurarPromocionesBW($arrayParametrosInfoBw);

                $this->rdaMiddleware->ejecutaWsSinEsperarRespuestaMiddleware($arrayDataConfirmacionTn);
            }

            $this->emInfraestructura->getConnection()->close();
            $this->emComercial->getConnection()->close();
            if($boolRedGponMpls)
            {
                $strMesajeFinal = $strMensajeCan;
            }
            else
            {
                $strMesajeFinal = "Se realizó el cambio de línea Pon";
            }
        } 
        catch (\Exception $e) 
        {
            $strMesajeFinal .= $e->getMessage();;
        }
        
        $arrayFinal[] = array('status' => "OK", 'mensaje' => $strMesajeFinal);
        return $arrayFinal;
    }

     /**
    * getVlanPorServicio
    * Obtener la vlan por servicio
    *
    * @author Creado: Manuel Carpio <mcarpio@telconet.ec>
    * @version 1.0 2-10-2022
    */
    public function getVlanPorServicio($objServicio)
    {
        try
        {
            //obtengo la vlan
            if($objServicio->getProductoId()->getNombreTecnico() === "SAFECITYWIFI")
            {
                $strTipoVlan          = "SSID";
                //obtengo el detalle de la vlan
                $strDetalleNombreVlan = "";
                $arrayParVlanProducto = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                'COMERCIAL',
                                                                                                '',
                                                                                                'PARAMETRO VLAN PARA SERVICIOS ADICIONALES SAFECITY',
                                                                                                '',
                                                                                                '',
                                                                                                $objServicio->getProductoId()->getId(),
                                                                                                $strTipoVlan,
                                                                                                '');
                if(isset($arrayParVlanProducto) && !empty($arrayParVlanProducto)
                    && isset($arrayParVlanProducto['valor1']) && !empty($arrayParVlanProducto['valor1']))
                {
                    $strDetalleNombreVlan = $arrayParVlanProducto['valor1'];
                }
                else
                {
                    throw new \Exception("No se ha podido obtener la vlan del producto ".$objServicio->getProductoId()->getDescripcionProducto().
                                            ", por favor notificar a Sistemas.");
                }
                //obtengo la vlan
                $objDetalleEleVlan  = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                            ->findONeBy(array("elementoId"    => $objServicioTecnico->getElementoId(),
                                                                                "detalleNombre" => $strDetalleNombreVlan,
                                                                                "estado"        => "Activo"));
                if(!is_object($objDetalleEleVlan))
                {
                    throw new \Exception("No se ha podido obtener la vlan del elemento, por favor notificar a Sistemas.");
                }
            }
            else
            {
                $objDetalleEleVlan = null;
                //verificar camara activa
                $objServCamActiva  = $this->emComercial->getRepository("schemaBundle:InfoServicio")
                                                ->findOneBy(array("puntoId"    => $objServicio->getPuntoId()->getId(),
                                                                    "productoId" => $objServicio->getProductoId()->getId(),
                                                                    "estado"     => array("Asignada","Activo")));
                if(is_object($objServCamActiva))
                {
                    //obtengo la vlan
                    $objSerProCaracVlan = $this->servicioGeneral->getServicioProductoCaracteristica($objServCamActiva,
                                                                            "VLAN",$objServCamActiva->getProductoId());
                    if(!is_object($objSerProCaracVlan))
                    {
                        throw new \Exception("No se ha podido obtener la vlan del elemento con relación una cámara activa, ".
                                                "por favor notificar a Sistemas.");
                    }
                    $objDetalleEleVlan = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                            ->find($objSerProCaracVlan->getValor());
                    //verificar vlan elemento
                    if(!is_object($objDetalleEleVlan))
                    {
                        throw new \Exception("No se ha podido obtener la vlan del elemento con relación una cámara ".
                                                "con recursos asignados, por favor notificar a Sistemas.");
                    }
                }
                else
                {
                    $objCaractTipoRed = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,"TIPO_RED",
                                                                                             $objServicio->getProductoId());
                    if(!is_object($objCaractTipoRed))
                    {
                        throw new \Exception("No se ha podido obtener el tipo de red del servicio, ".
                                                "por favor notificar a Sistemas.");
                    }
                    //obtener vlan por cliente
                    $arrayParametrosVlan = array('intIdPersonaEmpresaRol' => $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getId(),
                                                    'strEmpresaCod'          => '10',
                                                    'strCaractVlan'          => 'VLAN',
                                                    'strNombre'              => '$objElementoPe->getNombreElemento()',
                                                    'strTipoRed'             => $objCaractTipoRed->getValor());
                                                    
                    $arrayResultadoVlan  = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                            ->getVlansCliente($arrayParametrosVlan);

                    if(!empty($arrayResultadoVlan) && isset($arrayResultadoVlan["total"])
                        && isset($arrayResultadoVlan["data"]) && !empty($arrayResultadoVlan["data"])
                        && count($arrayResultadoVlan["data"]) == 1)
                    {
                        //obtener vlan
                        $objPerEmpCaractVlanGpon = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                            ->find($arrayResultadoVlan["data"][0]['id']);
                        if(is_object($objPerEmpCaractVlanGpon))
                        {
                            $objDetalleEleVlan   = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                            ->find($objPerEmpCaractVlanGpon->getValor());
                        }
                    }
                }
            }
            $arrayResultado = array(
                'status'                      => "OK",
                'mensaje'                     => "OK",
                'objDetalleElementoVlan'      => $objDetalleEleVlan,
                'objDetalleElementoVlanAdmin' => $objDetalleEleVlanAdmin
            );
        }
        catch (\Exception $ex)
        {
            $arrayResultado = array(
                'status'                      => "ERROR",
                'mensaje'                     => $ex->getMessage(),
                'objDetalleElementoVlan'      => null,
                'objDetalleElementoVlanAdmin' => null
            );
        }
        return $arrayResultado;
    }

    /**
    * notificacionNocSafeCity
    * Construir Json de envio de notificacion al ws de NOC
    *
    * @author Creado: Manuel Carpio <mcarpio@telconet.ec>
    * @version 1.0 2-10-2022
    */
    public function notificacionNocSafeCity($objDatosGpon, $arrayDatosGenrales)
    {
        $arrayDatosNoc           = array();
        $arrayDataNoc            = $arrayDatosGenrales['datosNoc'];
        $arrayRespuestaFinal     = array();
        $arrayRespToken          = array();
        $arrayObtenerInfoRequest = array();
        $arrayJsonToken          = array();
        $arrayRespuestaNoc       = array();
        $objPersona              = "";
        $objServicioTecnico      = "";
        $strCorreoTecnico        = "";
        $objPunto                = "";
        $strFlagEsVip            = "";
        $strMac                  = "";
        $strServicioCanId        = $arrayDatosGenrales['servicioCan'];
        $strServicioCanIp        = $arrayDatosGenrales['ipServicioCan'];
        $strEstadoServicio       = $arrayDatosGenrales['estadoServicio'];
        $strUsrCreacion          = $arrayDatosGenrales['usrCreacion'];
        $strIpCreacion           = $arrayDatosGenrales['ipCreacion'];
        $strOnt                  = $arrayDatosGenrales['ont'];
        $strModeloOnt            = $arrayDatosGenrales['modeloOnt'];
        $strMac                  = $arrayDatosGenrales['macOnt'];
        $strPuertoOnt            = $arrayDatosGenrales['puertoOnt'];
        $strEsVip                = $arrayDataNoc['esVIP'];
        //Datos servicos
        $strLoginAux             = "";
        $strTipoServicio         = "";
        $strSwitch               =  $arrayDatosGenrales['switch'];

        if($strEsVip === "Sí")
        {
            $strFlagEsVip = "S";
            $strCorreoVip = $arrayDataNoc['ingenierosVip'];
        }
        else
        {
            $strFlagEsVip = "N";
        }
                //Generación Token de seguridad        
                $arrayJsonToken = array(
                    'user'    => $strUsrCreacion,
                    'gateway' => static::$strWsGatewayCloudForm,
                    'service' => static::$strWsServiceCloudform,
                    'method'  => 'procesarAction',
                    'source'  => array(
                        'name'         => static::$strWsAppName,
                        'originID'     => $strIpCreacion,
                        'tipoOriginID' => 'IP'
                    )
                );

        $arrayRespToken = $this->rdaMiddleware->generateToken($arrayJsonToken);

        if($arrayRespToken['result']['message'] === "OK" && $arrayRespToken['status'] === 200)
        {                   
                $arrayObtenerInfoRequest['objInfoServicio'] = $objDatosGpon;
                $arrayObtenerInfoRequest['strUsrCreacion']  = $strUsrCreacion;
                $arrayObtenerInfoRequest['strIpCreacion']   = $strIpCreacion;
                $arrayObtenerInfoRequest['strProceso']      = "crear";


                $objPersona   = $objDatosGpon->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
                $objPunto     = $objDatosGpon->getPuntoId();
                $objCanton    = $objPunto->getSectorId()->getParroquiaId()->getCantonId()->getNombreCanton();    

                $arrayCorreoVipRequest['intIdPersonaRol'] = $objDatosGpon->getPuntoId()
                                                                         ->getPersonaEmpresaRolId()
                                                                         ->getId();

                $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                              ->findOneBy(array("servicioId" => $strServicioCanId));

                if(is_object($objServicioTecnico))
                {
                    $strTipoServicio = $objServicioTecnico->getServicioId()->getProductoId()->getNombreTecnico();

                    $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                      ->findOneById($objServicioTecnico->getServicioId());

                    $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                     ->findOneBy(array(  "nombreTecnico" => $strtipoServicio,
                                                                         "empresaCod"    => "10",
                                                                         "estado"        => "Activo"));
                }

                $arrayDatosNoc     = array( 'op'    => 'crear',
                    'data'  => array(
                        'datosCliente'  => array(
                            'ruc'             => $objPersona->getIdentificacionCliente(),
                            'razonSocial'     => $objPersona->getRazonSocial(),
                            'esvip'           => $strFlagEsVip,
                            'correoComercial' => '',
                            'correoTecnico'   => $strCorreoTecnico,
                            'telefonoTecnico' => '',
                            'correoVip'       => $strCorreoVip),
                        'datosPunto'    => array(
                            'login'           => $objPunto->getLogin(),
                            'latitud'         => $objPunto->getLatitud(),
                            'longitud'        => $objPunto->getLongitud(),
                            'canton'          => $objCanton,
                            'cobertura'       => $arrayDataNoc['cobertura'],
                            'direccion'       => $objPunto->getDireccion(),
                            'descripcion'     => $arrayDataNoc['descripcion'] != null ? $arrayDataNoc['descripcion'] : ""),
                        'datosServicio' => array(
                            'loginAux'        => $objDatosGpon->getLoginAux(),
                            'tipoServicio'    => $strTipoServicio,
                            'tipoEnlace'      => $arrayDataNoc['tipoEnlace'],
                            'bws'             => $arrayDataNoc['bws'],
                            'bwb'             => $arrayDataNoc['bwb'],
                            'ip'              => $strServicioCanIp,
                            'mac'             => $strMac,
                            'modeloCpe'       => $strModeloOnt,
                            'ultimaMilla'     => $arrayDataNoc['ultimaMilla'],
                            'switch'          => $strSwitch,
                            'puerto'          => $strPuertoOnt,
                            'estado'          => $strEstadoServicio,
                            'ont'             => $strOnt,
                            'puertoOnt'       => '',
                            'modeloCamara'    => ''
                        )),
                    'audit' => array(
                        'usrCreacion' => $strUsrCreacion,
                        'ipCreacion'  => $strIpCreacion,
                        'token'       => $arrayRespToken['result']['token']));

                $arrayRespuestaNoc = $this->rdaMiddleware->notificacionNoc($arrayDatosNoc);

            if($arrayRespuestaNoc['status'] === 200)
            { 
                $arrayRespuestaFinal = array('status' => $arrayRespuestaNoc['status'], 'mensaje' => $arrayRespuestaNoc['result']);
            }
            else
            {
                $arrayRespuestaFinal = array('status' => $arrayRespuestaNoc['status'], 'mensaje' => $arrayRespuestaNoc['message']);
            }
        }
        else
        {
             $arrayRespuestaFinal = array('status' => $arrayRespToken['status'], 'mensaje' => $arrayRespToken['message']);
        }
        return $arrayRespuestaFinal;
    }

    /**
    * notificacionCorreoSafeCity
    * Construir plantilla para envio de correo por cambio de linea pon safecity
    *
    * @author Creado: Manuel Carpio <mcarpio@telconet.ec>
    * @version 1.0 2-10-2022
    */
    public function notificacionCorreoSafeCity($arrayDatosCorreo, $arrayDataGeneral)
    {
        $arrayRespuestaFinal = array();
        $strCanton           = $arrayDataGeneral['canton'];
        $strAsunto           = "";
        $strValor2           = "";
        $strDescripcion      = "";
        $strValor1           = "";
        $strEmpresa          = $arrayDataGeneral['empresa'];

        if(!empty($strCanton) && isset($strCanton) && $strCanton === "GUAYAQUIL")
        {
            $strValor2           = "R1";
            $strDescripcion      = "PARAMETROS Notificacion correo - region costa";
            $strValor1           = "CAMBIO_LINEA_PON_R1_NOTIFICACION";
        }
        else
        {
            $strValor2           = "R2";
            $strDescripcion      = "PARAMETROS Notificacion correo - region sierra";
            $strValor1           = "CAMBIO_LINEA_PON_R2_NOTIFICACION";
        }



        $arrayParametrosGdaDatos = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne("NUEVA_RED_GPON_MPLS_TN",
                                                                "TECNICO",
                                                                "",
                                                                $srtDescripcion,
                                                                $strValor1,
                                                                $strValor2,
                                                                "",
                                                                "",
                                                                "",
                                                                $strEmpresa);

        if(is_array($arrayParametrosGdaDatos) && count($arrayParametrosGdaDatos) > 0)
        {
            $strAsunto = $arrayParametrosGdaDatos['valor4'];
            $arrayTo[] = $arrayParametrosGdaDatos['valor3'];
        }

        if(count($arrayDatosCorreo) > 0)
        {
            $this->correo->generarEnvioPlantilla($strAsunto, $arrayTo, 'SAFECITYCAN', $arrayDatosCorreo, $srtEmpresa, '', '');
            
            $arrayRespuestaFinal = array('status' => 200, 'mensaje' => 'Envio de notificación exitosa');
        }  
        else
        {
            $arrayRespuestaFinal = array('status' => 500, 'mensaje' => 'Envio de notificación fallida');
        }                                        
        return $arrayRespuestaFinal;
    }

    /**
    * creacionTareaAutomaticaSafeCity
    * Creacion de tarea automatica para cambio de linea pon safecity
    *
    * @author Creado: Manuel Carpio <mcarpio@telconet.ec>
    * @version 1.0 2-10-2022
    */
    public function creacionTareaAutomaticaSafeCity($arrayDataGeneral, $objPuntoMonitoreo)
    {
        $arrayRespuestaTarea    = array();
        $strUsuarioCreacion     = $arrayDataGeneral['usrCreacion'];
        $strIpCreacion          = $arrayDataGeneral['ipCreacion'];
        $strCanton              = $arrayDataGeneral['canton'];
        $strCodEmpresa          = $arrayDataGeneral['empresa'];
        $strCliente             = $arrayDataGeneral['cliente'];
        $arrayParametroDet      = array();
        $arrayParametrosTarea   = array();
        $strParametroTareaDepar = "";
        $strEmpleado            = "";

        //crear Tarea Rapida
                  
        //obtener datos parametrizados para la creacion de la tarea
        $arrayParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne("NUEVA_RED_GPON_MPLS_TN",
                                                                "TECNICO",
                                                                "",
                                                                "DATOS_CREAR_TAREA",
                                                                "Cambio de linea pon DATOS GPON SAFE CITY",
                                                                "Se realiza el cambio de linea pon forma correcta",
                                                                "",
                                                                "",
                                                                "",
                                                                "10");

        //obtengo el nombre del usuario
        $objCreationUser = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                               ->findOneBy(array('login'=>$strUsuarioCreacion));

        if(is_object($objCreationUser))
        {
        $strEmpleado        = $objCreationUser->getNombres().' '.$objCreationUser->getApellidos();
        }

           //creo la tarea llamando al service de soporte
        $arrayParametrosTarea = array(  'strIdEmpresa'          => $strCodEmpresa,
                                        'strPrefijoEmpresa'     => $arrayParametroDet['valor5'],
                                        'strNombreTarea'        => $arrayParametroDet['valor1'],
                                        'strObservacion'        => $arrayParametroDet['valor2'],
                                        'strNombreDepartamento' => $arrayParametroDet['valor6'],
                                        'strCiudad'             => $strCanton,
                                        'strEmpleado'           => $strEmpleado,
                                        'strUsrCreacion'        => $strUsuarioCreacion,
                                        'strIp'                 => $strIpCreacion,
                                        'strOrigen'             => 'WEB-TN',
                                        'strLogin'              => $objPuntoMonitoreo->getLogin(),
                                        'intPuntoId'            => $objPuntoMonitoreo->getId(),
                                        'strNombreCliente'      => $strCliente
                                        );     
        $arrayRespuestaTarea = $this->serviceSoporte->ingresarTareaInterna($arrayParametrosTarea);

        return $arrayRespuestaTarea;
    }

    /**
    * getCaracteristicasDatosGpon
    * Funcion que se encarga de valida que servicio DATOS GPON
    * cumpla con todas sus caracteristicas
    *
    * @author Creado: Manuel Carpio <mcarpio@telconet.ec>
    * @version 1.0 2-10-2022
    */
    public function getCaracteristicasDatosGpon($arrayObjeto)
    {
        $arrayRespuestaFinal = array();
        $arrayDatosGpon      = array();
        $objServicio         = $arrayObjeto['servicio'];
        $objProducto         = $arrayObjeto['producto'];

        $objServProdCaractCont = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,"T-CONT", $objProducto);
            if(!is_object($objServProdCaractCont))
            {
                $arrayRespuestaFinal = array('status' => 'ERROR', 'mensaje' => 'No existe Caracteristica T-CONT, favor revisar!');
                return $arrayRespuestaFinal;
            }
            else
            {
                $strTnCont = $objServProdCaractCont->getValor();
            }

            $objServProdCaractMm = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,"ID-MAPPING",$objProducto);
            if(!is_object($objServProdCaractMm))
            {
                $arrayRespuestaFinal = array('status' => 'ERROR', 'mensaje' => 'No existe Caracteristica ID-MAPPING, favor revisar!');
                return $arrayRespuestaFinal;
            }
            else
            {
                $strTnMappingMonitoreo = $objServProdCaractMm->getValor();
            }

            $objVlanSafeCity  = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                ->findOneBy(array(  "id"            => $arrayObjeto['vlan'],
                                                                                    "estado"        => 'Activo'));
            if(!is_object($objVlanSafeCity))
            {
                $arrayRespuestaFinal = array('status' => 'ERROR', 
                'mensaje' => 'No existe Caracteristica VLAN del  ont, favor revisar!');
                return $arrayRespuestaFinal; 
            }
            else
            {
                $strVlanOnt = $objVlanSafeCity->getDetalleValor();
            }

            $arrayDatosGpon      = array('vlanDatos'   => $strVlanOnt,
                                         'TnContDatos' => $strTnCont,
                                         'MaMonitoreo' => $strTnMappingMonitoreo);

            $arrayRespuestaFinal = array('status' => 200, 'mensaje' => 'TRANSACCIÓN EXITOSA', 'result' => $arrayDatosGpon);

            return $arrayRespuestaFinal;
    }

    /**
    * getCaracteristicasCamaras
    * Funcion que se encarga de valida que servicio SAFE VIDEO ANALYTICS CAM 4 - GPON
    * cumpla con todas sus caracteristicas
    *
    * @author Creado: Manuel Carpio <mcarpio@telconet.ec>
    * @version 1.0 2-10-2022
    *
    * @author Jenniffer Mujica <jmujica@telconet.ec> 
    * @version 1.2 3-03-202 Se agrega validación para los servicios safecitywifi,
    *           se valida envio de datos a activar y cancelar.
    */
    public function getCaracteristicasCamaras($objServicio, $objProducto)
    {
        $strVlan              = "";
        $strVlanAdmin         = "";
        $strSpidCan           = "";
        $strSpidAdminCan      = "";
        $strPuertoEthernetCan = "";
        $strTContDatos        = "";
        $strTContDatosAdmin   = "";
        $strGenPortDatos      = "";
        $strGenPortDatosAdmin = "";
        $strTTDatos           = "";
        $strTTDatosAdmin      = "";
        $strIdMappingDatos    = "";
        $strVrf               = "";
        $strVrfAdmin          = "";
        $strVpn               = "";
        $arrayRespuesta       = array();
        $arrayDatos           = array();

        $objSpcVlan = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 
                      $objProducto->getNombreTecnico() == 'SAFECITYDATOS' ? "VLAN" : 'VLAN SSID', $objProducto);


        if(is_object($objSpcVlan))
        {
            $objVlan = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')->find($objSpcVlan->getValor());
            if(is_object($objVlan))
            {
                $strVlan = $objVlan->getDetalleValor();
            }
            else
            {
                $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE VLAN, DEL SERVICIO ' 
                                                  . $objServicio->getProductoId()->getDescripcionProducto());
                return $arrayRespuesta;
            }

        }
        else
        {
            $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE VLAN, DEL SERVICIO ' 
                                                  . $objServicio->getProductoId()->getDescripcionProducto());
            return $arrayRespuesta;
        }
                            
        $objPuertoEthernetCan = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 
                                                                     "PUERTO_ONT", $objProducto);

        if(is_object($objPuertoEthernetCan))
        {
            $strPuertoEthernetCan = $objPuertoEthernetCan->getValor();
        }
        else
        {
            $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE PUERTO_ONT, DEL SERVICIO' 
                                                  . $objServicio->getProductoId()->getDescripcionProducto());
            return $arrayRespuesta;
        }

        $objSpidCan = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "SPID", $objProducto);

        if(is_object($objSpidCan))
        {
            $strSpidCan = $objSpidCan->getValor();
        }
        else
        {
            $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE SPID, DEL SERVICIO ' 
                                            . $objServicio->getProductoId()->getDescripcionProducto());
            return $arrayRespuesta;
        }

        $objGenPortDatos = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "GEM-PORT", $objProducto);

        if(is_object($objGenPortDatos))
        {
            $strGenPortDatos  = $objGenPortDatos->getValor();
        }
        else
        {
            $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE GEM-PORT, DEL SERVICIO' 
                                               . $objServicio->getProductoId()->getDescripcionProducto());
            return $arrayRespuesta;
        }

        $objTContDatos = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "T-CONT", $objProducto);

        if(is_object($objTContDatos))
        {
            $strTContDatos = $objTContDatos->getValor();
        }
        else
        {
            $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE T-CONT, DEL SERVICIO' 
                                             . $objServicio->getProductoId()->getDescripcionProducto());
            return $arrayRespuesta;
        }

        $objTTDatos = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "TRAFFIC-TABLE", $objProducto);

        if(is_object($objTTDatos))
        {
            $strTTDatos = $objTTDatos->getValor();
        }
        else
        {
            $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE TRAFFIC-TABLE, DEL SERVICIO' 
            . $objServicio->getProductoId()->getDescripcionProducto());
            return $arrayRespuesta;
        }

        $objIdMappingDatos = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "ID-MAPPING", 
                                                                                              $objProducto);
                            
        if(is_object($objIdMappingDatos))
        {
            $strIdMappingDatos  = $objIdMappingDatos->getValor();
        }
        else
        {
            $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE ID-MAPPING, DEL SERVICIO' 
                                                 . $objServicio->getProductoId()->getDescripcionProducto());
            return $arrayRespuesta;
        }

        $objVrfDatos = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 
                       $objProducto->getNombreTecnico() == 'SAFECITYDATOS' ? "VRF" : 'VRF SSID', $objProducto);

                            
        if(is_object($objVrfDatos))
        {
            $objVrf = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                ->find($objVrfDatos->getValor());
            if(is_object($objVrf))
            {
                $strVrf = $objVrf->getValor();
            }
            else
            {
                $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE VRF, DEL SERVICIO' 
                                                . $objServicio->getProductoId()->getDescripcionProducto());
               return $arrayRespuesta;
            }
        }
        else
        {
            $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE VRF, DEL SERVICIO' 
                                                . $objServicio->getProductoId()->getDescripcionProducto());
            return $arrayRespuesta;
        }

        if(is_object($objVrf))
        {
            //obtener vpn
            $objVpn  = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                 ->find($objVrf->getPersonaEmpresaRolCaracId());

            if(is_object($objVpn))
            {
                $strVpn = $objVpn->getValor();
            }
            else
            {
                $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE VPN, DEL SERVICIO.' 
                . $objServicio->getProductoId()->getDescripcionProducto());
                return $arrayRespuesta;
            }
        }

        if(is_object($objVpn))
        {
            $objCaractRdId  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                              ->findOneBy(array("descripcionCaracteristica" => "RD_ID"));

            $objRdId        = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                            ->findOneBy(array("caracteristicaId"        => $objCaractRdId,
                                                                            "estado"                    => "Activo",
                                                                            "personaEmpresaRolCaracId"  => $objVpn->getId()));
            if(is_object($objRdId))
            {
                $strRdId = $objRdId->getValor();
            }
            else
            {
                $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE RD, DEL SERVICIO.' 
                . $objServicio->getProductoId()->getDescripcionProducto());
                return $arrayRespuesta;
            }
        }
        else
        {
            $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE VRF, DEL SERVICIO.' 
            . $objServicio->getProductoId()->getDescripcionProducto());
            return $arrayRespuesta;
        }

        if($objProducto->getNombreTecnico() == 'SAFECITYWIFI')
        {
            //VLAN ADMIN
            $objSpcVlanAdmin = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 'VLAN ADMIN', $objProducto);
            if(is_object($objSpcVlanAdmin))
            {
                $objVlan = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')->find($objSpcVlanAdmin->getValor());
                if(is_object($objVlan))
                {
                    $strVlanAdmin = $objVlan->getDetalleValor();
                }
                else
                {
                    $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE VLAN ADMIN, DEL SERVICIO ' 
                                                      . $objServicio->getProductoId()->getDescripcionProducto());
                    return $arrayRespuesta;
                }
    
            }
            else
            {
                $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE VLAN ADMIN, DEL SERVICIO ' 
                                                      . $objServicio->getProductoId()->getDescripcionProducto());
                return $arrayRespuesta;
            }
            //VRF ADMIN
            $objVrfDatosAdmin = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 'VRF ADMIN', $objProducto);           
            if(is_object($objVrfDatosAdmin))
            {
                $objVrf = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                    ->find($objVrfDatosAdmin->getValor());
                if(is_object($objVrf))
                {
                    $strVrfAdmin = $objVrf->getValor();
                    //obtener vpn
                    $objVpn  = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                         ->find($objVrf->getPersonaEmpresaRolCaracId());
                    if(is_object($objVpn))
                    {
                        $strVpnAdmin = $objVpn->getValor();
                        $objCaractRdId  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                          ->findOneBy(array("descripcionCaracteristica" => "RD_ID"));
                        $objRdId        = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                        ->findOneBy(array("caracteristicaId"        => $objCaractRdId,
                                                                                        "estado"                    => "Activo",
                                                                                        "personaEmpresaRolCaracId"  => $objVpn->getId()));
                        if(is_object($objRdId))
                        {
                            $strRdIdAdmin = $objRdId->getValor();
                        }
                        else
                        {
                            $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE RD-ID ADMIN, DEL SERVICIO ' 
                            . $objServicio->getProductoId()->getDescripcionProducto());
                            return $arrayRespuesta;
                        }
                    }
                    else
                    {
                        $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE VPN ADMIN, DEL SERVICIO ' 
                        . $objServicio->getProductoId()->getDescripcionProducto());
                        return $arrayRespuesta;
                    }
                }
                else
                {
                    $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE VRF ADMIN, DEL SERVICIO ' 
                                                    . $objServicio->getProductoId()->getDescripcionProducto());
                    return $arrayRespuesta;
                }
            }
            else
            {
                $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE VRF ADMIN, DEL SERVICIO ' 
                                                    . $objServicio->getProductoId()->getDescripcionProducto());
                return $arrayRespuesta;
            }
            //TRAFFIC-TABLE-ADMIN
            $objTTDatos = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "TRAFFIC-TABLE-ADMIN", $objProducto);
            if(is_object($objTTDatos))
            {
                $strTTDatosAdmin = $objTTDatos->getValor();
            }
            else
            {
                $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE TRAFFIC-TABLE-ADMIN, DEL SERVICIO ' 
                . $objServicio->getProductoId()->getDescripcionProducto());
                return $arrayRespuesta;
            }
            //GEM-PORT-ADMIN
            $objGenPortDatos = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "GEM-PORT-ADMIN", $objProducto);
            if(is_object($objGenPortDatos))
            {
                $strGenPortDatosAdmin  = $objGenPortDatos->getValor();
            }
            else
            {
                $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE GEM-PORT-ADMIN, DEL SERVICIO ' 
                                                . $objServicio->getProductoId()->getDescripcionProducto());
                return $arrayRespuesta;
            }
            //SPID ADMIN
            $objSpidAdminCan = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "SPID ADMIN", $objProducto);
    
            if(is_object($objSpidAdminCan))
            {
                $strSpidAdminCan = $objSpidAdminCan->getValor();
            }
            else
            {
                $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE SPID ADMIN, DEL SERVICIO ' 
                                                . $objServicio->getProductoId()->getDescripcionProducto());
                return $arrayRespuesta;
            }
            //T-CONT-ADMIN
            $objTContAdminDatos = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "T-CONT-ADMIN", $objProducto);

            if(is_object($objTContAdminDatos))
            {
                $strTContDatosAdmin = $objTContAdminDatos->getValor();
            }
            else
            {
                $arrayRespuesta = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE T-CONT-ADMIN, DEL SERVICIO ' 
                                                . $objServicio->getProductoId()->getDescripcionProducto());
                return $arrayRespuesta;
            }
        }

        $arrayDatos = array('vlan'             => $strVlan != null ? $strVlan : "",
                            'valorVlan'        => $objSpcVlan->getValor(),
                            'vlanAdmin'        => $strVlanAdmin != null ? $strVlanAdmin : "",
                            'valorVlanAdmin'   => is_object($objSpcVlanAdmin)?$objSpcVlanAdmin->getValor():"",
                            'puertoEthernet'   => $strPuertoEthernetCan != null ? $strPuertoEthernetCan : "",
                            'spid'             => $strSpidCan != null ? $strSpidCan : "",
                            'spidAdmin'        => $strSpidAdminCan != null ? $strSpidAdminCan : "",
                            'genPortDatos'     => $strGenPortDatos != null ? $strGenPortDatos : "",
                            'genPortDatosAdmin' => $strGenPortDatosAdmin != null ? $strGenPortDatosAdmin : "",
                            'tContDatos'       => $strTContDatos != null ? $strTContDatos : "",
                            'tContDatosAdmin'  => $strTContDatosAdmin != null ? $strTContDatosAdmin : "",
                            'TTDatos'          => $strTTDatos != null ? $strTTDatos : "",
                            'TTDatosAdmin'     => $strTTDatosAdmin != null ? $strTTDatosAdmin : "",
                            'idMappingDatos'   => $strIdMappingDatos != null ? $strIdMappingDatos : "",
                            'vrf'              => $strVrf != null ? $strVrf : "",
                            'vrfValor'         => $objVrfDatos->getValor(), 
                            'vrfAdmin'         => $strVrfAdmin != null ? $strVrfAdmin : "",
                            'vrfValorAdmin'    => is_object($objVrfDatosAdmin)?$objVrfDatosAdmin->getValor():"",
                            'vpn'              => $strVpn != null ? $strVpn : "",
                            'rd'               => $strRdId != null ? $strRdId : "",
                            'vpnAdmin'         => isset($strVpnAdmin) && !empty($strVpnAdmin) ? $strVpnAdmin : "",
                            'rdAdmin'          => isset($strRdIdAdmin) && !empty($strRdIdAdmin) ? $strRdIdAdmin : "");

        $arrayRespuesta = array('status' => 200, 'mensaje' => 'VALIDACIÓN EXITOSA.', 'result' => $arrayDatos);
        return $arrayRespuesta;
    }

    /**
    * getSpidHuawei
    * Service que obtiene el SPID
    *
    * @author Creado: John Vera <javera@telconet.ec>
    * @version 1.0 7-05-2015
    */
    public function getSpidHuawei($arrayPeticiones)
    {

        $modeloElemento     = $arrayPeticiones['modeloElemento'];
        $interfaceElemento  = $arrayPeticiones['interfaceElemento'];
        $ontId              = $arrayPeticiones['ontId'];
        $servicioTecnico    = $arrayPeticiones['servicioTecnico'];

        //*OBTENER SCRIPT SPID --------------------------------------------------------*/
        $scriptArraySpid = $this->servicioGeneral->obtenerArregloScript("obtenerSpid", $modeloElemento);
        $idDocumentoSpid = $scriptArraySpid[0]->idDocumento;
        $usuario         = $scriptArraySpid[0]->usuario;
        //*----------------------------------------------------------------------*/
        //dividir interface para obtener tarjeta y puerto pon
        list($tarjeta, $puertoPon) = split('/', $interfaceElemento->getNombreInterfaceElemento());

        //variables datos
        $datos              = $tarjeta . "," . $puertoPon . "," . $ontId;
        $resultadoJsonSpid  = $this->activar->obtenerDatosPorAccion($servicioTecnico, $usuario, $datos, $idDocumentoSpid, "obtenerSpid");
        $statusSpid         = $resultadoJsonSpid->status;

        $respuestaFinal[] = array('status' => $statusSpid, 'mensaje' => $resultadoJsonSpid->mensaje);
        return $respuestaFinal;
    }
    
    /**
    * verificarSpidHuawei
    * Funcion que sirve para verificar si un Spid existe en un Olt para otro cliente, caso contrario 
    * configurarlo para los datos del cliente referenciado
    *
    * @author Creado: Allan Suarez <arsuarez@telconet.ec>
    * @version 1.0 15-10-2015
    */
    public function verificarSpidHuawei($arrayPeticiones)
    {
        $spid            = $arrayPeticiones['spid'];
        $vlan            = $arrayPeticiones['vlan'];
        $interfaceElemento = $arrayPeticiones['interfaceElemento'];
        $ontId           = $arrayPeticiones['ontId'];
        $gemPort         = $arrayPeticiones['gemPort'];
        $trafficTable    = $arrayPeticiones['trafficTable'];
        $servicioTecnico = $arrayPeticiones['servicioTecnico'];
        
        //dividir interface para obtener tarjeta y puerto pon
        list($tarjeta, $puertoPon) = split('/', $interfaceElemento->getNombreInterfaceElemento());

        //variables datos
        $datos              = $spid . "," . $vlan . "," . $tarjeta . "," . $puertoPon . "," . $ontId . "," . $gemPort . "," . $trafficTable . "," .
                              $trafficTable;
        $resultadoJsonSpid  = $this->activar->obtenerDatosPorAccion($servicioTecnico, 'sistemas', $datos, 0 , "verificarSpidOlt");
        $statusSpid         = $resultadoJsonSpid->status;

        $respuestaFinal[] = array('status' => $statusSpid, 'mensaje' => $resultadoJsonSpid->mensaje);
        return $respuestaFinal;
    }

    /**
     * activarIpAdicionalHuawei
     * Service que activa las ip adicionales en los equipos Huawei
     *
     * @author Creado: John Vera <javera@telconet.ec>
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 7-05-2015
     * @version 1.1 25-06-2015
     * @version 1.2 02-04-2016   Se cambia variable de tabla InfoServicioTecnico para obtener el id de la 
     *                           interface del elemento cliente 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 28-05-2018 Se agregan validaciones para IPs Small Business
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 05-05-2019 Se elimina código innecesario para servicios de Ips Small Busines, ya que dichos servicios no siguen este flujo
     */
    public function activarIpAdicionalHuawei($arrayPeticiones)
    {
        $ipFija         = $arrayPeticiones['ipFija'];
        $mac            = $arrayPeticiones['mac'];
        $idServicio     = $arrayPeticiones['idServicio'];
        $idEmpresa      = $arrayPeticiones['idEmpresa'];
        $usrCreacion    = $arrayPeticiones['usrCreacion'];
        $ipCreacion     = $arrayPeticiones['ipCreacion'];
        $puertoNuevoOnt = $arrayPeticiones['puertoNuevoOnt'];

        $servicio       = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
        $servicioTecnico= $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                             ->findOneBy(array("servicioId" => $servicio->getId()));
        $plan           = $servicio->getPlanId();
        $arrayProductoIp= $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                             ->findBy(array("nombreTecnico" => "IP", "estado" => "Activo"));
        //obtener producto ip
        if($plan)
        {
            for($i = 0; $i < count($arrayProductoIp); $i++)
            {
                $planDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')->findOneBy(array("planId" => $plan->getId(),
                                                                                                          "productoId" => $arrayProductoIp[$i]));
                if($planDet)
                {
                    $productoIpId = $planDet->getProductoId();
                    $productoIp = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($productoIpId);
                    break;
                }
            }
        }
        else
        {
            $productoIp = $servicio->getProductoId();
        }

        $objProductoInternet    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->findOneBy(array(  "esPreferencia" => "SI",
                                                                                                                    "nombreTecnico" => "INTERNET",
                                                                                                                    "empresaCod"    => $idEmpresa,
                                                                                                                    "estado"        => "Activo"));
        //obtener servicio internet
        $punto = $servicio->getPuntoId();
        $objServicioInternet    = $this->migracionHuawei->getServicioInternetEnPunto($punto, $objProductoInternet);
        $objSpcMacAnterior      = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC", $objProductoInternet);        

        //obtener objeto modelo cnr
        $modeloElementoCnr = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                     ->findOneBy(array( "nombreModeloElemento"  => "CNR UCS C220",
                                                                        "estado"                => "Activo"));

        //obtener elemento cnr
        $elementoCnr = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
            ->findOneBy(array("modeloElementoId" => $modeloElementoCnr->getId()));

        try
        {
            //datos del servicio de internet
            $servicioTecnicoInternet = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                         ->findOneBy(array("servicioId" => $objServicioInternet->getId()));
            $interfaceElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                         ->find($servicioTecnicoInternet->getInterfaceElementoId());
            $modeloElementoHuawei = $interfaceElemento->getElementoId()->getModeloElementoId();

            if($punto->getTipoNegocioId()->getNombreTipoNegocio() == "PRO")
            {
                $objSpcVlan = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioInternet, "VLAN", $objProductoInternet);

                if($objSpcVlan->getValor() == '301')
                {
                    $objSpcSpid = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioInternet, "SPID", $objProductoInternet);
                    //eliminar service port id
                    //*ELIMINAR SCRIPT SPID --------------------------------------------------------*/
                    $scriptArraySpid = $this->servicioGeneral->obtenerArregloScript("eliminarSpid", $modeloElementoHuawei);
                    $idDocumentoSpid = $scriptArraySpid[0]->idDocumento;
                    $usuario = $scriptArraySpid[0]->usuario;
                    $protocolo = $scriptArraySpid[0]->protocolo;
                    //*----------------------------------------------------------------------*/

                    $arrayParametrosEliminarServicePort = array(
                        'idDocumento'   => $idDocumentoSpid,
                        'usuario'       => $usuario,
                        'spid'          => $objSpcSpid->getValor(),
                        'elementoId'    => $interfaceElemento->getElementoId()->getId(),
                        'protocolo'     => $protocolo
                    );
                    $resultadoJsonEliminarSpid = $this->activar->eliminarSpidHuawei($arrayParametrosEliminarServicePort);
                    $statusEliminarSpid = $resultadoJsonEliminarSpid->status;

                    if($statusEliminarSpid == "OK")
                    {
                        $objGemPort     = $this->servicioGeneral
                                               ->getServicioProductoCaracteristica($objServicioInternet, "GEM-PORT", $objProductoInternet);
                        $objOntId       = $this->servicioGeneral
                                               ->getServicioProductoCaracteristica($objServicioInternet, "INDICE CLIENTE", $objProductoInternet);
                        $objTrafficTable= $this->servicioGeneral->getServicioProductoCaracteristica($objServicioInternet, "TRAFFIC-TABLE",
                                                                                                    $objProductoInternet);
                        //dividir interface para obtener tarjeta y puerto pon
                        list($tarjeta, $puertoPon) = split('/', $interfaceElemento->getNombreInterfaceElemento());

                        //*OBTENER SCRIPT--------------------------------------------------------*/
                        $scriptArray= $this->servicioGeneral->obtenerArregloScript("activarCliente", $modeloElementoHuawei);
                        $idDocumento= $scriptArray[0]->idDocumento;
                        $usuario    = $scriptArray[0]->usuario;
                        $protocolo  = $scriptArray[0]->protocolo;
                        //*----------------------------------------------------------------------*/
                        //activar service port id con vlan=302, unicamente para ips adicionales
                        $arrayParametrosConfigSpid = array(
                            'vlan'           => '302',
                            'gemPort'        => $objGemPort->getValor(),
                            'trafficTable'   => $objTrafficTable->getValor(),
                            'idDocumento'    => $idDocumento,
                            'usuario'        => $usuario,
                            'servicioTecnico'=> $servicioTecnicoInternet,
                            'protocolo'      => $protocolo,
                            'tarjeta'        => $tarjeta,
                            'puertoPon'      => $puertoPon,
                            'ontId'          => $objOntId->getValor()
                        );
                        $resultadoJsonActivarSpid = $this->activar->activarClienteOltHuawei($arrayParametrosConfigSpid);
                        $statusActivarSpid = $resultadoJsonActivarSpid->status;

                        if($statusActivarSpid != "OK")
                        {
                            $respuestaFinal[] = array('status' => 'ERROR',
                                                      'mensaje' => "No creó el Service-Port! <br>," . $resultadoJsonActivarSpid->mensaje);
                            return $respuestaFinal;
                        }
                        
                        //elimino e ingreso la nueva vlan con 302
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcVlan, "Eliminado");
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicioInternet, $objProductoInternet, "VLAN", '302', 
                                                                                        $usrCreacion);    
                        
                        //eliminar serv prod caract de spid anterior
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcSpid, "Eliminado");

                        $arraySpid = array( 'modeloElemento'    => $modeloElementoHuawei,
                                            'interfaceElemento' => $interfaceElemento,
                                            'ontId'             => $objOntId->getValor(),
                                            'servicioTecnico'   => $servicioTecnicoInternet);
                        $resultArraySpid = $this->getSpidHuawei($arraySpid);
                        $spid = $resultArraySpid[0]['mensaje'];

                        if($spid != "")
                        {
                            //servicio prod caract spid
                            $this->servicioGeneral
                                ->ingresarServicioProductoCaracteristica($objServicioInternet, $objProductoInternet, "SPID", $spid, $usrCreacion);
                        }
                    }
                    else
                    {
                        throw new \Exception("No se pudo eliminar el Service-Port! <br>," . $resultadoJsonEliminarSpid->mensaje);
                    }
                }
            }
            else if($punto->getTipoNegocioId()->getNombreTipoNegocio() == "PYME")
            {
                $interfaceCliente = null;
                if($puertoNuevoOnt)
                {
                    //se debe seleccionar el puerto ont (1-4)
                    $interfaceCliente = $this->migracionHuawei->getInterfaceClienteServicioIp($punto, $servicioTecnicoInternet, $arrayProductoIp);

                    if(!$interfaceCliente)
                    {
                        $strMensaje = "No Se puede migrar Ip, puesto que ya se encuentran ocupados los 4 Puertos del ONT";
                        throw new \Exception("No se pudo migrar la ip fija adicional! <br>" . $strMensaje);
                    }
                }
                if (!$interfaceCliente)
                {
                    //se debe enviar un comando para activar puerto del ont
                    $interfaceClienteId = $servicioTecnicoInternet->getInterfaceElementoClienteId();
                    $interfaceCliente   = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                               ->find($interfaceClienteId);
                }
                
                if($interfaceCliente)
                {
                    //*CONFIGURAR IP FIJA --------------------------------------------------------*/
                    $scriptArrayIpFija = $this->servicioGeneral->obtenerArregloScript("configurarIpFija", $modeloElementoHuawei);
                    $idDocumentoIpFja = $scriptArrayIpFija[0]->idDocumento;
                    $usuario = $scriptArrayIpFija[0]->usuario;
                    $protocolo = $scriptArrayIpFija[0]->protocolo;
                    //*----------------------------------------------------------------------*/
                    //dividir interface para obtener tarjeta y puerto pon
                    list($tarjeta, $puertoPon) = split('/', $interfaceElemento->getNombreInterfaceElemento());

                    //ont id
                    $spcIndice = $this->servicioGeneral
                        ->getServicioProductoCaracteristica($objServicioInternet, "INDICE CLIENTE", $objProductoInternet);

                    $arrayParametrosIpFija = array(
                        'elementoId'    => $interfaceElemento->getElementoId()->getId(),
                        'idDocumento'   => $idDocumentoIpFja,
                        'usuario'       => $usuario,
                        'tarjeta'       => $tarjeta,
                        'puertoPon'     => $puertoPon,
                        'ontId'         => $spcIndice->getValor(),
                        'puertoOnt'     => $interfaceCliente->getNombreInterfaceElemento()
                    );
                    $resultadoJsonIpFija = $this->activar->activarIpFijaHuawei($arrayParametrosIpFija);

                    if($resultadoJsonIpFija->status != "OK")
                    {
                        $strMensaje = "Activacion Puerto Ont: " . $resultadoJsonIpFija->mensaje;
                        throw new \Exception("No se pudo migrar la ip fija adicional! <br>" . $strMensaje);
                    }
                }
            }

            $arrayParametrosConfigIp = array(
                'ipFija'            => $ipFija,
                'modeloElementoCnr' => $modeloElementoCnr,
                'elementoCnr'       => $elementoCnr,
                'mac'               => $mac
            );

            //activar ip adicional
            $arrayResultadoConfigIp = $this->activar->activarIpFijaCnr($arrayParametrosConfigIp);

            $status = $arrayResultadoConfigIp[0]['status'];

            if($status == "OK")
            {

                $entityIpReservada = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')->findOneBy(array(
                                                                                                            "ip"            => $ipFija,
                                                                                                            "estado"        => 'Reservada',
                                                                                                            "servicioId"    => $servicio->getId()));
                if($entityIpReservada)
                {
                    $entityIpReservada->setEstado("Activo");
                    $this->emInfraestructura->persist($entityIpReservada);
                    $this->emInfraestructura->flush();
                }

                //eliminar mac anterior
                if(is_object($objSpcMacAnterior))
                {
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcMacAnterior, "Eliminado");
                }

                //ingresar mac nueva
                $this->servicioGeneral
                    ->ingresarServicioProductoCaracteristica($servicio, $objProductoInternet, "MAC", $mac, $usrCreacion);

                //historial del servicio
                $servicioHistorial = new InfoServicioHistorial();
                $servicioHistorial->setServicioId($servicio);
                $servicioHistorial->setObservacion("Se configuró Ip Fija:" . $ipFija . " con Mac:" . $mac);
                $servicioHistorial->setEstado($servicio->getEstado());
                $servicioHistorial->setUsrCreacion($usrCreacion);
                $servicioHistorial->setFeCreacion(new \DateTime('now'));
                $servicioHistorial->setIpCreacion($ipCreacion);
                $this->emComercial->persist($servicioHistorial);
                $this->emComercial->flush();

                $strMensaje = "OK";
            }
            else
            {
                $strMensaje = $arrayResultadoConfigIp[0]['mensaje'];
                throw new \Exception("No se pudo activar la ip fija adicional! <br>" . $strMensaje);
            }
        }
        catch(\Exception $e)
        {
            $status = "ERROR";
            $strMensaje = "ERROR: <br> " . $e->getMessage();
            $respuestaFinal[] = array('status' => $status, 'mensaje' => $strMensaje);
            return $respuestaFinal;
        }

        $respuestaFinal[] = array('status' => $status, 'mensaje' => $strMensaje);
        return $respuestaFinal;
    }

    /**
    * cambiarPuertoLogicoMd
    * función que efectúa el cambio de puerto lógico cuando se realiza un cambio de línea pon
    *
    * @author Modificado: John Vera <javera@telconet.ec>
    * @version 1.1 08-07-2015
    * 
    * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
    * @version 1.2 27-09-2017  Se cambia el mensaje registrado en el historial
    *
    * @author Modificado: Manuel Carpio <mcarpio@telconet.ec>
    * @version 1.2 23-09-2022  Se agrega datos tecnicos de nuevo OLT en resgistro de historial
    * @since 1.2
    */
    public function cambiarPuertoLogicoMd($servicio, $servicioTecnico, $elementoId, $interfaceElementoId, $elementoCajaId, $elementoSplitterId, 
                                          $interfaceElementoSplitterId, $usrCreacion, $ipCreacion, $idEmpresa) {
        try {
            $nombreElementoAnterior = '';
            $nombreInterfaceElementoAnterior = '';

            //ANTERIOR--------------------------------------------------------------
            $elementoAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                    ->find($servicioTecnico->getElementoId());
            $ultimaMillaObj = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                    ->find($servicioTecnico->getUltimaMillaId());
            $interfaceElementoAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                    ->find($servicioTecnico->getInterfaceElementoId());
            $elementoConectorAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                ->find($servicioTecnico->getElementoConectorId());
            if ($elementoConectorAnterior)
            {
                $nombreElementoAnterior = $elementoConectorAnterior->getNombreElemento();
            }
            
            $interfaceElementoConectorAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                         ->find($servicioTecnico->getInterfaceElementoConectorId());
            if ($interfaceElementoConectorAnterior)
            {
                $nombreInterfaceElementoAnterior = $interfaceElementoConectorAnterior->getNombreInterfaceElemento();
            }       

            //NUEVO------------------------------------------------------------------
            $elementoNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($elementoId);
            $interfaceElementoNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                    ->find($interfaceElementoId);
            $cajaNueva = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($elementoCajaId);
            $splitterNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($elementoSplitterId);
            $interfaceElementoSplitterNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                    ->find($interfaceElementoSplitterId);

            if ($servicioTecnico->getInterfaceElementoClienteId()) {
                $interfaceElementoCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                        ->find($servicioTecnico->getInterfaceElementoClienteId());

                if ($servicioTecnico->getInterfaceElementoConectorId() != 0) {
                    
                    $serviciosPorInterface = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                     ->getServiciosPorInterface($servicioTecnico->getInterfaceElementoConectorId(), 
                                                                                                $idEmpresa);
                    //si solo hay un servicio en el puerto se lo libera
                    if(count($serviciosPorInterface) == 1)
                    {
                        $objInterfaceViejo = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                            ->find($servicioTecnico->getInterfaceElementoConectorId());

                        $objInterfaceViejo->setEstado('not connect');
                        $this->emInfraestructura->persist($objInterfaceViejo);
                        $this->emInfraestructura->flush();
                    }

                    $enlace = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                            ->findOneBy(array("interfaceElementoIniId" => $servicioTecnico->getInterfaceElementoConectorId(),
                        "interfaceElementoFinId" => $servicioTecnico->getInterfaceElementoClienteId()));
                    if ($enlace) {
                        $enlace->setInterfaceElementoIniId($interfaceElementoSplitterNuevo);
                    } else {
                        $enlace = new InfoEnlace();
                        $enlace->setInterfaceElementoIniId($interfaceElementoSplitterNuevo);
                        $enlace->setInterfaceElementoFinId($interfaceElementoCliente);
                        $enlace->setEstado("Activo");
                        $enlace->setTipoMedioId($ultimaMillaObj);
                        $enlace->setTipoEnlace("PRINCIPAL");
                        $enlace->setUsrCreacion($usrCreacion);
                        $enlace->setFeCreacion(new \DateTime('now'));
                        $enlace->setIpCreacion($ipCreacion);
                    }
                } else {
                    $enlace = new InfoEnlace();
                    $enlace->setInterfaceElementoIniId($interfaceElementoSplitterNuevo);
                    $enlace->setInterfaceElementoFinId($interfaceElementoCliente);
                    $enlace->setEstado("Activo");
                    $enlace->setTipoMedioId($ultimaMillaObj);
                    $enlace->setTipoEnlace("PRINCIPAL");
                    $enlace->setUsrCreacion($usrCreacion);
                    $enlace->setFeCreacion(new \DateTime('now'));
                    $enlace->setIpCreacion($ipCreacion);
                }
                $this->emInfraestructura->persist($enlace);
                $this->emInfraestructura->flush();
            }

            //actualizo la interface nueva a ocupado 
            $objInterfaceNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                         ->find($interfaceElementoSplitterId);
            if($objInterfaceNuevo)
            {
                $objInterfaceNuevo->setEstado('connected');
                $this->emInfraestructura->persist($objInterfaceNuevo);
                $this->emInfraestructura->flush();
            }
            $servicioTecnico->setElementoId($elementoNuevo->getId());
            $servicioTecnico->setInterfaceElementoId($interfaceElementoNuevo->getId());
            $servicioTecnico->setElementoContenedorId($cajaNueva->getId());
            $servicioTecnico->setElementoConectorId($splitterNuevo->getId());
            $servicioTecnico->setInterfaceElementoConectorId($interfaceElementoSplitterNuevo->getId());
            $this->emComercial->persist($servicioTecnico);
            $this->emComercial->flush();

            //historial del servicio
            $servicioHistorial = new InfoServicioHistorial();
            $servicioHistorial->setServicioId($servicio);
            if(is_object($servicio->getProductoId()))
            {
                if($servicio->getProductoId()->getCodigoProducto() === "SAFECITY")
                {
                    $servicioHistorial->setObservacion(" <b>Se hizo cambio de linea pon:</b>"."<br>".
                                   "<b style = 'color: red'>OLT Anterior :</b>"."<br>".
                                   "<b>Elemento anterior:</b>".$elementoAnterior->getNombreElemento().
                                   "<br> <b>Puerto anterior:</b> " . $interfaceElementoAnterior->getNombreInterfaceElemento().
                                   "<br> <b>Elemento conector anterior:</b> ".$nombreElementoAnterior.
                                   "<br> <b>Interface elemento conector anterior:</b> ".$nombreInterfaceElementoAnterior."<br>".
                                   "<b style = 'color: red'>OLT Actual :</b>"."<br>".
                                   "<b>Elemento actual:</b>".$elementoNuevo->getNombreElemento().
                                   "<br> <b>Puerto actual:</b> " . $interfaceElementoNuevo->getNombreInterfaceElemento().
                                   "<br> <b>Elemento conector actual:</b> ".$splitterNuevo->getNombreElemento().
                                   "<br> <b>Interface elemento conector actual:</b> ".$interfaceElementoSplitterNuevo->getNombreInterfaceElemento());
                }
                else
                {
                    $servicioHistorial->setObservacion("Se hizo cambio de linea pon:<br>Elemento anterior:".
                                    $elementoAnterior->getNombreElemento().
                                   "<br> Puerto anterior:" . $interfaceElementoAnterior->getNombreInterfaceElemento().
                                   "<br> Elemento conector anterior: ".$nombreElementoAnterior.
                                   "<br> Interface elemento conector anterior: ".$nombreInterfaceElementoAnterior);  
                }
            }
            else
            {
                $servicioHistorial->setObservacion("Se hizo cambio de linea pon:<br>Elemento anterior:".
                                    $elementoAnterior->getNombreElemento().
                                   "<br> Puerto anterior:" . $interfaceElementoAnterior->getNombreInterfaceElemento().
                                   "<br> Elemento conector anterior: ".$nombreElementoAnterior.
                                   "<br> Interface elemento conector anterior: ".$nombreInterfaceElementoAnterior);
            }
            $servicioHistorial->setEstado($servicio->getEstado());
            $servicioHistorial->setUsrCreacion($usrCreacion);
            $servicioHistorial->setFeCreacion(new \DateTime('now'));
            $servicioHistorial->setIpCreacion($ipCreacion);
            $this->emComercial->persist($servicioHistorial);
            $this->emComercial->flush();
            $result = "OK";
        } catch (\Exception $e) {
            $result = "ERROR, " . $e->getMessage();
        }

        //*----------------------------------------------------------------------*/
        return $result;
    }

    /**
     * Documentación para el método 'cambiarPuertoMd'.
     *
     * Método utilizado para Cambio de Puerto Lógico
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 18-12-2017 Se modifica la función para servicios en estado ASIGNADA
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.2 06-08-2018 Se agrega parámetro en tabla ADMI_PARAMETRO_CAB para permitir ejecutar
     *                         proceso omitiendo ciertas validaciones en caso de tener valor1 "SI".
     *                         Esto se lo realiza para permitir realizar regularización de información de puertos de splitters
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 13-05-2021 Se modifica la función para envío de parámetros por medio de un arreglo y se actualizan de datos de campos de la
     *                         INFO_SERVICIO_TECNICO sólo si dichos campos eran diferentes de null.
     * 
     * @since 1.1
     * 
     * @since 1.0 
     */
    public function cambiarPuertoMd($arrayParametros)
    {
        $objServicio                    = $arrayParametros["objServicio"];
        $objServicioTecnico             = $arrayParametros["objServicioTecnico"];
        $intElementoIdStNuevo           = $arrayParametros["intElementoIdStNuevo"];
        $intInterfaceElementoIdStNuevo  = $arrayParametros["intInterfaceElementoIdStNuevo"];
        $intContenedorIdNuevo           = $arrayParametros["intContenedorIdNuevo"];
        $intConectorIdStNuevo           = $arrayParametros["intConectorIdStNuevo"];
        $intInterfaceConectorIdStNuevo  = $arrayParametros["intInterfaceConectorIdStNuevo"];
        $strUsrCreacion                 = $arrayParametros["strUsrCreacion"];
        $strIpCreacion                  = $arrayParametros["strIpCreacion"];
        $strCodEmpresa                  = $arrayParametros["strCodEmpresa"];
        $strMensaje                     = "";
        $this->emComercial->beginTransaction();
        $this->emInfraestructura->beginTransaction();
        
        try
        {
            $intElementoIdStActual              = $objServicioTecnico->getElementoId();
            $intIdUltimaMillaStActual           = $objServicioTecnico->getUltimaMillaId();
            $intInterfaceElementoIdStActual     = $objServicioTecnico->getInterfaceElementoId();
            $intConectorIdStActual              = $objServicioTecnico->getElementoConectorId();
            $intInterfaceConectorIdStActual     = $objServicioTecnico->getInterfaceElementoConectorId();
            $intIdInterfaceElemClienteActual    = $objServicioTecnico->getInterfaceElementoClienteId();
            $strNombreConectorActual            = "";
            $strNombreInterfaceConectorActual   = "";
            $strPermiteSaltarValidacion         = "NO";
            
            if(!isset($intInterfaceConectorIdStActual) || empty($intInterfaceConectorIdStActual))
            {
                throw new \Exception("Servicio no tiene puerto de splitter asignado, imposible realizar cambio de puerto lógico. "
                                     ."Por favor notificar a Sistemas");
            }
            $objElementoStActual    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intElementoIdStActual);
            if(!is_object($objElementoStActual))
            {
                throw new \Exception("Servicio no tiene elemento asignado, imposible realizar cambio de puerto lógico. "
                                     ."Por favor notificar a Sistemas");
            }
            
            $objInterfaceElementoStActual   = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                      ->find($intInterfaceElementoIdStActual);
            if(!is_object($objInterfaceElementoStActual))
            {
                throw new \Exception("Servicio no tiene interface de elemento asignado, imposible realizar cambio de puerto lógico. "
                                     ."Por favor notificar a Sistemas");
            }
            
            $objConectorStActual    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intConectorIdStActual);
            if(is_object($objConectorStActual))
            {
                $strNombreConectorActual = $objConectorStActual->getNombreElemento();
            }
            
            $objInterfaceConectorStActual = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                         ->find($intInterfaceConectorIdStActual);
            if(is_object($objInterfaceConectorStActual))
            {
                $strNombreInterfaceConectorActual = $objInterfaceConectorStActual->getNombreInterfaceElemento();
            }
            
            $objUltimaMillaStActual         = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')->find($intIdUltimaMillaStActual);
            $objElementoStNuevo             = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intElementoIdStNuevo);
            $objInterfaceElementoStNuevo    = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                      ->find($intInterfaceElementoIdStNuevo);
            $objContenedorStNuevo           = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intContenedorIdNuevo);
            $objConectorStNuevo             = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intConectorIdStNuevo);
            $objInterfaceConectorStNuevo    = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                      ->find($intInterfaceConectorIdStNuevo);
            $arrayServiciosPorInterfaceConectorActual   = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                                  ->getServiciosPorInterface($intInterfaceConectorIdStActual,
                                                                                                             $strCodEmpresa);
            //Se obtiene parámetro para controlar validación
            $arrayPermiteCambioLogico   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->getOne( 'VALIDACIONES TELCOS TECNICO',
                                                                    'TECNICO',
                                                                    'TECNICO',
                                                                    'CAMBIO PUERTO LOGICO',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    $strCodEmpresa);

            if(isset($arrayPermiteCambioLogico["valor1"]) && !empty($arrayPermiteCambioLogico["valor1"]))
            {
                $strPermiteSaltarValidacion = $arrayPermiteCambioLogico["valor1"];
            }  
            //si solo hay un servicio en el puerto se lo libera
            if(count($arrayServiciosPorInterfaceConectorActual) == 1 || $strPermiteSaltarValidacion === 'SI')
            {
                $objInterfaceConectorStActual->setEstado('not connect');
                $this->emInfraestructura->persist($objInterfaceConectorStActual);
                $this->emInfraestructura->flush();
            }
            else
            {
                throw new \Exception("Existe más de 1 servicio asociado al puerto del splitter, imposible realizar cambio de puerto lógico. "
                                     ."Por favor notificar a Sistemas");
            }

            if($intIdInterfaceElemClienteActual > 0)
            {
                $objEnlaceConectorClienteActual = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                                          ->findOneBy(array("interfaceElementoIniId"  => 
                                                                                            $intInterfaceConectorIdStActual,
                                                                                            "interfaceElementoFinId"  => 
                                                                                            $intIdInterfaceElemClienteActual,
                                                                                            "estado"                  => "Activo"));
                if(!is_object($objEnlaceConectorClienteActual))
                {
                    throw new \Exception("No existe enlace actual entre el splitter y el ONT, imposible realizar cambio de puerto lógico. "
                                         ."Por favor notificar a Sistemas");
                }
                $objEnlaceConectorClienteActual->setEstado("Eliminado");
                $this->emInfraestructura->persist($objEnlaceConectorClienteActual);

                $objInterfaceElemClienteActual  = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                          ->find($intIdInterfaceElemClienteActual);
                if(!is_object($objInterfaceElemClienteActual))
                {
                    throw new \Exception("No existe registro del ONT del cliente, imposible realizar cambio de puerto lógico. "
                                         ."Por favor notificar a Sistemas");
                }

                $objEnlaceConectorClienteNuevo = new InfoEnlace();
                $objEnlaceConectorClienteNuevo->setInterfaceElementoIniId($objInterfaceConectorStNuevo);
                $objEnlaceConectorClienteNuevo->setInterfaceElementoFinId($objInterfaceElemClienteActual);
                $objEnlaceConectorClienteNuevo->setEstado("Activo");
                $objEnlaceConectorClienteNuevo->setTipoMedioId($objUltimaMillaStActual);
                $objEnlaceConectorClienteNuevo->setTipoEnlace("PRINCIPAL");
                $objEnlaceConectorClienteNuevo->setUsrCreacion($strUsrCreacion);
                $objEnlaceConectorClienteNuevo->setFeCreacion(new \DateTime('now'));
                $objEnlaceConectorClienteNuevo->setIpCreacion($strIpCreacion);
                $this->emInfraestructura->persist($objEnlaceConectorClienteNuevo);
            }
            
            $strEstadoInterfaceConectorStNuevo = "connected";
            if($objServicio->getEstado() === "Asignada")
            {
                $strEstadoInterfaceConectorStNuevo = "reserved";
            }
            $objInterfaceConectorStNuevo->setEstado($strEstadoInterfaceConectorStNuevo);
            $this->emInfraestructura->persist($objInterfaceConectorStNuevo);
            $this->emInfraestructura->flush();
            
            $arrayServiciosPunto    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                        ->findServiciosPorEstadoPorPuntos($objServicio->getPuntoId()->getId(), 
                                                                                          $objServicio->getEstado());
            foreach($arrayServiciosPunto as $objServicioPunto)
            {
                $strEsServicioPrincipal             = "NO";
                $strActualizaServicioTecnico        = "NO";
                $objServicioTecnicoServicioPunto    = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                                              ->findOneByServicioId($objServicioPunto->getId());
                if(is_object($objServicioTecnicoServicioPunto))
                {
                    $intElementoIdStServicioPunto                   = $objServicioTecnicoServicioPunto->getElementoId();
                    $intInterfaceElementoIdStServicioPunto          = $objServicioTecnicoServicioPunto->getInterfaceElementoId();
                    $intElementoContenedorIdStServicioPunto         = $objServicioTecnicoServicioPunto->getElementoContenedorId();
                    $intElementoConectorIdStServicioPunto           = $objServicioTecnicoServicioPunto->getElementoConectorId();
                    $intInterfaceElementoConectorIdStServicioPunto  = $objServicioTecnicoServicioPunto->getInterfaceElementoConectorId();
                    if($objServicioPunto->getId() === $objServicio->getId())
                    {
                        $strEsServicioPrincipal = "SI";
                    }
                    
                    if($strEsServicioPrincipal === "SI")
                    {
                        $objServicioTecnicoServicioPunto->setElementoId($objElementoStNuevo->getId());
                        $objServicioTecnicoServicioPunto->setInterfaceElementoId($objInterfaceElementoStNuevo->getId());
                        $objServicioTecnicoServicioPunto->setElementoContenedorId($objContenedorStNuevo->getId());
                        $objServicioTecnicoServicioPunto->setElementoConectorId($objConectorStNuevo->getId());
                        $objServicioTecnicoServicioPunto->setInterfaceElementoConectorId($objInterfaceConectorStNuevo->getId());
                        $strActualizaServicioTecnico = "SI";
                    }
                    else
                    {
                        if(isset($intElementoIdStServicioPunto) && !empty($intElementoIdStServicioPunto))
                        {
                            $objServicioTecnicoServicioPunto->setElementoId($objElementoStNuevo->getId());
                            $strActualizaServicioTecnico = "SI";
                        }
                        
                        if(isset($intInterfaceElementoIdStServicioPunto) && !empty($intInterfaceElementoIdStServicioPunto))
                        {
                            $objServicioTecnicoServicioPunto->setInterfaceElementoId($objInterfaceElementoStNuevo->getId());
                            $strActualizaServicioTecnico = "SI";
                        }
                    
                        if(isset($intElementoContenedorIdStServicioPunto) && !empty($intElementoContenedorIdStServicioPunto))
                        {
                            $objServicioTecnicoServicioPunto->setElementoContenedorId($objContenedorStNuevo->getId());
                            $strActualizaServicioTecnico = "SI";
                        }
                        
                        if(isset($intElementoConectorIdStServicioPunto) && !empty($intElementoConectorIdStServicioPunto))
                        {
                            $objServicioTecnicoServicioPunto->setElementoConectorId($objConectorStNuevo->getId());
                            $strActualizaServicioTecnico = "SI";
                        }
                        
                        if(isset($intInterfaceElementoConectorIdStServicioPunto) && !empty($intInterfaceElementoConectorIdStServicioPunto))
                        {
                            $objServicioTecnicoServicioPunto->setInterfaceElementoConectorId($objInterfaceConectorStNuevo->getId());
                            $strActualizaServicioTecnico = "SI";
                        }
                    }
                    
                    if($strActualizaServicioTecnico === "SI")
                    {
                        $this->emInfraestructura->persist($objServicioTecnicoServicioPunto);
                        $this->emInfraestructura->flush();

                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($objServicioPunto);
                        $objServicioHistorial->setObservacion(  "Se hizo cambio de puerto logico:<br>Elemento anterior:".
                                                                $objElementoStActual->getNombreElemento().
                                                                "<br> Puerto anterior:" . $objInterfaceElementoStActual->getNombreInterfaceElemento().
                                                                "<br> Elemento conector anterior: ".$strNombreConectorActual.
                                                                "<br> Interface elemento conector anterior: ".$strNombreInterfaceConectorActual);
                        $objServicioHistorial->setEstado($objServicioPunto->getEstado());
                        $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setIpCreacion($strIpCreacion);
                        $this->emComercial->persist($objServicioHistorial);
                        $this->emComercial->flush();
                    }
                }
            }
            $this->emComercial->commit();
            $this->emInfraestructura->commit();
            $strStatus = "OK";
        }
        catch(\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = "ERROR, ".$e->getMessage();
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }
            
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            $this->emInfraestructura->getConnection()->close();
            $this->emComercial->getConnection()->close();
        }
        
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }

    /**
     * cambiarPuertoTtco
     * 
     * Funcion encargada de realizar el cambio de puerto/ultima milla para la empresa TTCCO
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1  06-05-2016    Se agrega parametro empresa en metodo cambiarPuertoTtco por conflictos de 
     *                             producto INTERNET DEDICADO
     * 
     * @since 1.0
     * 
     * @param Objeto $servicio
     * @param Objeto $servicioTecnico 
     * @param Integer $dslamId
     * @param Integer $interfaceId
     * @param String $usrCreacion
     * @param String $ipCreacion 
     * @param Integer $idEmpresa
     * @return string $result
     */
    public function cambiarPuertoTtco($servicio, $servicioTecnico, $dslamId, $interfaceId, $usrCreacion, $ipCreacion, $idEmpresa) 
    {
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/

        try 
        {
            $interfaceAnteriorId          = $servicioTecnico->getInterfaceElementoId();
            //objetos interfaces anterior
            $interfaceAnterior            = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->find($interfaceAnteriorId);
            $interfaceNueva               = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->find($interfaceId);
            $nombreInterfaceElemento      = $interfaceAnterior->getNombreInterfaceElemento();
            $elemento                     = $interfaceAnterior->getElementoId();
            $modeloElemento               = $elemento->getModeloElementoId();
            $nombreModeloElemento         = $modeloElemento->getNombreModeloElemento();
            $reqAprovisionamiento         = $modeloElemento->getReqAprovisionamiento();
            $nombreInterfaceElementoNuevo = $interfaceNueva->getNombreInterfaceElemento();
            $elementoNuevo                = $interfaceNueva->getElementoId();
            $elementoNuevoId              = $elementoNuevo->getId();
            $modeloElementoNuevo          = $elementoNuevo->getModeloElementoId();
            $nombreModeloElementoNuevo    = $modeloElementoNuevo->getNombreModeloElemento();
            $reqAprovisionamientoNuevo    = $modeloElementoNuevo->getReqAprovisionamiento();
            $ipElementoNuevo              = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                    ->findOneBy(array("elementoId" => $elementoNuevoId));

            //buscamos motivo
            $motivo                       = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                                            ->findOneBy(array("nombreMotivo" => "Cambio de puerto - TECNICO", "estado" => "Activo"));

            $caracteristica1              = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                              ->findOneBy(array("descripcionCaracteristica" => "CAPACIDAD1", "estado" => "Activo"));
            $caracteristica2              = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                              ->findOneBy(array("descripcionCaracteristica" => "CAPACIDAD2", "estado" => "Activo"));
            $producto                     = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                              ->findOneBy(array("descripcionProducto" => "INTERNET DEDICADO", 
                                                                                "estado"              => "Activo", 
                                                                                "empresaCod"          => $idEmpresa));
            $prodCaracteristica1          = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                              ->findOneBy(array("productoId"       => $producto->getId(), 
                                                                                "caracteristicaId" => $caracteristica1->getId()));
            $prodCaracteristica2          = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                              ->findOneBy(array("productoId"       => $producto->getId(), 
                                                                                "caracteristicaId" => $caracteristica2->getId()));

            $plan                         = $servicio->getPlanId();
            $planDet                      = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                              ->findOneBy(array("productoId" => $producto->getId(), "planId" => $plan->getId()));

            $capacidad1                   = $this->emComercial->getRepository('schemaBundle:InfoPlanProductoCaract')
                                                              ->findOneBy(array("planDetId"                 => $planDet->getId(), 
                                                                                "productoCaracterisiticaId" => $prodCaracteristica1->getId()));
            $capacidad2                   = $this->emComercial->getRepository('schemaBundle:InfoPlanProductoCaract')
                                                              ->findOneBy(array("planDetId"                 => $planDet->getId(), 
                                                                                "productoCaracterisiticaId" => $prodCaracteristica2->getId()));

            if ($servicio->getEstado() == "Activo" || $servicio->getEstado() == "EnVerificacion") 
            {
                $puntoId = $servicio->getPuntoId();
                $punto   = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($puntoId->getId());
                $login   = $punto->getLogin();

                if ($servicio->getEstado() == "Activo") 
                {
                    //buscamos el enlace
                    $enlace = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                      ->findOneBy(array("interfaceElementoIniId" => $interfaceAnterior->getId()));
                }

                //***********************************************************************************
                //***********************************************************************************
                //  CANCELAR PUERTO  *****************************************************************
                //***********************************************************************************
                //***********************************************************************************

                if ($nombreModeloElemento != 'TERCERIZADO') 
                {
                    //---cancelar puerto viejo
                    if ($reqAprovisionamiento == "SI") 
                    {//solo si tiene aprovisionamiento 
                        //*OBTENER SCRIPT--------------------------------------------------------*/
                        $scriptArray  = $this->servicioGeneral->obtenerArregloScript("cancelarCliente", $modeloElemento);
                        $idDocumento1 = $scriptArray[0]->idDocumento;
                        $usuario1     = $scriptArray[0]->usuario;
                        $protocolo1   = $scriptArray[0]->protocolo;
                        //*----------------------------------------------------------------------*/

                        if ($idDocumento1 == 0)
                        {
                            return "NO EXISTE TAREA";
                        }
                    }

                    $caracteristicaVci = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                           ->findBy(array("descripcionCaracteristica" => "VCI", "estado" => "Activo"));
                    $pcVci             = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                           ->findBy(array("productoId"       => $producto->getId(), 
                                                                          "caracteristicaId" => $caracteristicaVci[0]->getId()));
                    $ispcVci           = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                           ->findBy(array("servicioId"                => $servicio->getId(), 
                                                                          "productoCaracterisiticaId" => $pcVci[0]->getId()));

                    if (count($ispcVci) > 0) 
                    {
                        if ($ispcVci[0]->getValor() > 31 && $ispcVci[0]->getValor() <= 100)
                            $vciValor = "0/" . $ispcVci[0]->getValor();
                        else
                            $vciValor = "0/35";
                    }
                    else 
                    {
                        $vciValor = "0/35";
                    }

                    if ($reqAprovisionamiento == "SI") 
                    {//solo si tiene aprovisionamiento 
                        if ($nombreModeloElemento == "A2024") 
                        {
                            $datos        = $nombreInterfaceElemento . ",1";
                            $resultadJson = $this->cancelarClienteA2024($idDocumento1, $usuario1, $protocolo1, $elemento, $datos);
                        } 
                        else if ($nombreModeloElemento == "A2048") 
                        {
                            $datos        = $nombreInterfaceElemento . ",1";
                            $resultadJson = $this->cancelarClienteA2048($idDocumento1, $usuario1, $protocolo1, $elemento, $datos);
                        } 
                        else if ($nombreModeloElemento == "R1AD24A") 
                        {
                            $datos        = $nombreInterfaceElemento . "," . $nombreInterfaceElemento . "," . $nombreInterfaceElemento . ".1," . 
                                            $nombreInterfaceElemento . ".1," . $nombreInterfaceElemento . ".1," . $nombreInterfaceElemento;
                            $resultadJson = $this->cancelarClienteR1AD24A($idDocumento1, $usuario1, $protocolo1, $elemento, $datos);
                        } 
                        else if ($nombreModeloElemento == "R1AD48A") 
                        {
                            $datos        = $nombreInterfaceElemento . "," . $nombreInterfaceElemento . "," . $nombreInterfaceElemento . ".1," . 
                                            $nombreInterfaceElemento . ".1," . $nombreInterfaceElemento . ".1," . $nombreInterfaceElemento;
                            $resultadJson = $this->cancelarClienteR1AD48A($idDocumento1, $usuario1, $protocolo1, $elemento, $datos);
                        } 
                        else if ($nombreModeloElemento == "6524") 
                        {
                            $datos        = $nombreInterfaceElemento . "," . $nombreInterfaceElemento . "," . $nombreInterfaceElemento;
                            $resultadJson = $this->cancelarCliente6524($idDocumento1, $usuario1, $protocolo1, $elemento, $datos);
                        } 
                        else if ($nombreModeloElemento == "7224") 
                        {
                            $datos        = $nombreInterfaceElemento . "," . $nombreInterfaceElemento . "," . $nombreInterfaceElemento . "," . 
                                            $nombreInterfaceElemento . "," . $nombreInterfaceElemento;
                            $resultadJson = $this->cancelarCliente7224($idDocumento1, $usuario1, $protocolo1, $elemento, $datos);
                        } 
                        else if ($nombreModeloElemento == "MEA1") 
                        {  
                            $datos        = $nombreInterfaceElemento . "," . $vciValor . "," . $vciValor;
                            $resultadJson = $this->cancelarClienteMea1($idDocumento1, $usuario1, $protocolo1, $elemento, $datos);
                        } 
                        else if ($nombreModeloElemento == "MEA3") 
                        {
                            $datos        = $nombreInterfaceElemento . "," . $vciValor . "," . $vciValor;
                            $resultadJson = $this->cancelarClienteMea3($idDocumento1, $usuario1, $protocolo1, $elemento, $datos);
                        } 
                        else if ($nombreModeloElemento == "IPTECOM" || $nombreModeloElemento == "411AH" || $nombreModeloElemento == "433AH") 
                        {
                            $caracteristica     = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                       ->findBy(array("descripcionCaracteristica" => "MAC"));
                            $prodCaract         = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                       ->findBy(array("productoId"       => $producto->getId(), 
                                                                      "caracteristicaId" => $caracteristica[0]->getId()));
                            $servicioProdCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                       ->findBy(array("servicioId"                => $servicio->getId(), 
                                                                      "productoCaracterisiticaId" => $prodCaract[0]->getId()));
                            $mac                = $servicioProdCaract[0]->getValor();

                            //*OBTENER SCRIPT--------------------------------------------------------*/
                            $scriptArray   = $this->servicioGeneral->obtenerArregloScript("encontrarNumbersMac", $modeloElemento);
                            $idDocumento11 = $scriptArray[0]->idDocumento;
                            $usuario1      = $scriptArray[0]->usuario;
                            //*----------------------------------------------------------------------*/
                            //numbers de la mac
                            $datos2        = $mac;
                            $resultadJson2 = $this->cortarClienteIPTECOM($idDocumento11, $usuario1, "radio", $elemento, $datos2);
                            $resultado     = $resultadJson2->mensaje;

                            $numbers = explode("\n", $resultado);

                            $flag = 0;

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
                                return "ERROR ELEMENTO";
                            }

                            if ($nombreModeloElemento == "411AH") 
                            {
                                $datos = $numero[0];
                            } 
                            else 
                            {
                                $datos = $numero[1];
                            }

                            $resultadJson1    = $this->cancelarClienteIPTECOM($idDocumento1, $usuario1, "radio", $elemento, $datos);
                            error_log("resultado cancelar base:" . $resultadJson1->status);
                            $datos1           = $login;
                            $elementoIdRadius = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                        ->findOneBy(array("nombreElemento" => "ttcoradius"));

                            //*OBTENER SCRIPT--------------------------------------------------------*/
                            $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("cancelarClienteRadius", 
                                                                                               $elementoIdRadius->getModeloElementoId());
                            $idDocumento = $scriptArray[0]->idDocumento;
                            $usuario     = $scriptArray[0]->usuario;
                            $protocolo   = $scriptArray[0]->protocolo;
                            //*----------------------------------------------------------------------*/

                            $resultadJson = $this->cancelarClienteRADIUS($idDocumento, $usuario, "servidor", $elementoIdRadius->getId(), $datos1);
                            error_log("resultado cancelar radius:" . $resultadJson->status);
                            
                            if ($resultadJson->status == "OK" && $resultadJson1->status == "OK") 
                            {
                                $status = "OK";
                            }
                        }

                        //cuando es dslam - se setea el status
                        if ($nombreModeloElemento != "IPTECOM" || $nombreModeloElemento != "411AH" || $nombreModeloElemento != "433AH") 
                        {
                            $status = "OK";
                        }
                    }// fin si requiere aprovisionamiento
                    else 
                    {// si no requiere aprovisionamiento
                        $status = "OK";
                    }
                } 
                else 
                {// si es tercelizado 
                    $status = "OK";
                }

                //***********************************************************************************
                //***********************************************************************************
                //  ACTIVAR PUERTO  *****************************************************************
                //***********************************************************************************
                //***********************************************************************************

                $tipoMedio      = $servicioTecnico->getUltimaMillaId();
                $ultimaMillaObj = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')->find($tipoMedio);
                $ultimaMilla    = $ultimaMillaObj->getNombreTipoMedio();

                //---activar puerto nuevo

                if ($nombreModeloElementoNuevo != 'TERCERIZADO') 
                {
                    if ($reqAprovisionamientoNuevo == "SI") 
                    {   
                        //solo si tiene aprovisionamiento 
                        //*OBTENER SCRIPT--------------------------------------------------------*/
                        $scriptArray = $this->servicioGeneral->obtenerArregloScript("activarCliente", $modeloElementoNuevo);
                        $idDocumento = $scriptArray[0]->idDocumento;
                        $usuario     = $scriptArray[0]->usuario;
                        $protocolo   = $scriptArray[0]->protocolo;
                        //*----------------------------------------------------------------------*/

                        if ($idDocumento == 0) 
                        {
                            return "NO EXISTE TAREA";
                        }
                    }

                    if ($ultimaMilla == "Radio") 
                    {
                        if ($reqAprovisionamientoNuevo == "SI") 
                        {//solo si tiene aprovisionamiento 
                            $caracMac          = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                   ->findOneBy(array("descripcionCaracteristica" => "MAC"));
                            $prodCaractMac     = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                   ->findOneBy(array("productoId"       => $producto->getId(), 
                                                                                     "caracteristicaId" => $caracMac->getId()));
                            $servProdCaractMac = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                   ->findOneBy(array("servicioId"                => $servicio->getId(), 
                                                                                     "productoCaracterisiticaId" => $prodCaractMac->getId()));
                            $mac               = $servProdCaractMac->getValor();

                            //cpe radio
                            $infoIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                              ->findOneBy(array("servicioId" => $servicio->getId(), 
                                                                                "tipoIp"     => "RADIO"));

                            if ($infoIp) 
                            {
                                $ipCpeRadio = $infoIp->getIp();
                            } 
                            else 
                            {
                                $infoIp     = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                      ->findOneBy(array("servicioId" => $servicio->getId(), 
                                                                                        "tipoIp"     => "WAN"));
                                $ipCpeRadio = $infoIp->getIp();
                            }

                            //comando - base 
                            $datos        = $mac . "," . $nombreInterfaceElementoNuevo;
                            $resultadJson = $this->activarClienteIPTECOM($idDocumento, $usuario, "radio", $elementoNuevo, $datos);

                            //radius
                            $elementoIdRadius = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                        ->findOneBy(array("nombreElemento" => "ttcoradius"));
                            //*OBTENER SCRIPT--------------------------------------------------------*/
                            $scriptArray      = $this->servicioGeneral->obtenerArregloScriptGeneral("activarClienteRADIUS", 
                                                                        $elementoIdRadius->getModeloElementoId());
                            $idDocumento1     = $scriptArray[0]->idDocumento;
                            $usuario1         = $scriptArray[0]->usuario;
                            $protocolo1       = $scriptArray[0]->protocolo;
                            //*----------------------------------------------------------------------*/
                            //comando - servidor radius
                            $datos1           = $login . "," . $login . "123," . $ipCpeRadio . "," . $capacidad1->getValor() . "," . 
                                                $capacidad2->getValor();
                            $resultadJson1    = $this->activarClienteRADIUS($idDocumento1, $usuario1, "servidor", $elementoIdRadius, $datos1);
                            $status           = $resultadJson->status;
                            $status1          = $resultadJson1->status;

                            if ($status == "OK" && $status1 == "OK") 
                            {
                                $status = "OK";
                            }
                        }
                    } 
                    else 
                    {
                        if ($reqAprovisionamiento == "SI") 
                        {
                            //*OBTENER SCRIPT--------------------------------------------------------*/
                            $scriptArray  = $this->servicioGeneral->obtenerArregloScript("activarCliente", $modeloElementoNuevo);
                            $idDocumento1 = $scriptArray[0]->idDocumento;
                            $usuario1     = $scriptArray[0]->usuario;
                            $protocolo1   = $scriptArray[0]->protocolo;
                            //*----------------------------------------------------------------------*/

                            if ($nombreModeloElementoNuevo == "A2024" || $nombreModeloElementoNuevo == "A2048" || 
                                $nombreModeloElementoNuevo == "MEA1" || $nombreModeloElementoNuevo == "MEA3") 
                            {
                                $flag            = 0;
                                $detalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                        ->findBy(array("elementoId" => $elementoNuevoId, "detalleNombre" => "PERFIL"));
                                for ($i = 0; $i < count($detalleElemento); $i++) 
                                {
                                    $detalleCaracteristica1 = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleCaracteristica')
                                                                   ->findOneBy(array( "detalleElementoId"         => $detalleElemento[$i],
                                                                                      "caracteristicaId"          => $caracteristica1->getId(),
                                                                                      "descripcionCaracteristica" => $capacidad1->getValor()));
                                    $detalleCaracteristica2 = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleCaracteristica')
                                                                   ->findOneBy(array( "detalleElementoId"         => $detalleElemento[$i],
                                                                                      "caracteristicaId"          => $caracteristica2->getId(),
                                                                                      "descripcionCaracteristica" => $capacidad2->getValor()));

                                    if ($detalleCaracteristica1 != null && $detalleCaracteristica2 != null) 
                                    {
                                        if ($detalleCaracteristica1->getDetalleElementoId() == $detalleCaracteristica2->getDetalleElementoId()) 
                                        {
                                            $valor  = $detalleCaracteristica1->getValorCaracteristica();
                                            $valor1 = explode("\r", $valor);
                                            $flag   = 1;
                                            break;
                                        }
                                    }
                                }
                                if (isset($valor1)) 
                                {
                                    $perfil = $valor1[0];
                                } 
                                else 
                                {
                                    return "NO EXISTE PERFIL";
                                }
                            }

                            if ($nombreModeloElementoNuevo == "A2024")
                            {
                                $caracteristicaVlan = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                        ->findBy(array("descripcionCaracteristica" => "VLAN", "estado" => "Activo"));
                                $pcVlan             = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                        ->findBy(array("productoId"       => $producto->getId(), 
                                                                                       "caracteristicaId" => $caracteristicaVlan[0]->getId()));
                                $ispcVlan           = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                        ->findBy(array("servicioId"                => $servicio->getId(), 
                                                                                       "productoCaracterisiticaId" => $pcVlan[0]->getId()));

                                $datos              = $nombreInterfaceElementoNuevo . "," . $perfil . "," . $login . "," . $vciValor . ", 1";

                                $resultadJson = $this->activarClienteA2024($idDocumento, $usuario, $protocolo, $interfaceNueva->getElementoId(), $datos);
                            } 
                            else if ($nombreModeloElementoNuevo == "A2048") 
                            {
                                $caracteristicaVlan = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                        ->findBy(array("descripcionCaracteristica" => "VLAN", "estado" => "Activo"));
                                $pcVlan             = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                        ->findBy(array("productoId"       => $producto->getId(), 
                                                                                       "caracteristicaId" => $caracteristicaVlan[0]->getId()));
                                $ispcVlan           = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                        ->findBy(array("servicioId"                => $servicio->getId(), 
                                                                                       "productoCaracterisiticaId" => $pcVlan[0]->getId()));

                                $datos              = $nombreInterfaceElementoNuevo . "," . $perfil . "," . $login . "," . $vciValor . ", 1";

                                $resultadJson       = $this->activarClienteA2048( $idDocumento, 
                                                                                  $usuario,
                                                                                  $protocolo, 
                                                                                  $interfaceNueva->getElementoId(), 
                                                                                  $datos);
                            }
                            else if ($nombreModeloElementoNuevo == "R1AD24A") 
                            {
                                $ptoFormateado = $nombreInterfaceElementoNuevo . ".1";
                                $datos         = $nombreInterfaceElementoNuevo . "," . $nombreInterfaceElementoNuevo . "," . $login . "," . 
                                                 $ptoFormateado . "," . $ptoFormateado . "," .$vciValor . "," . $ptoFormateado . "," . 
                                                 $nombreInterfaceElementoNuevo . "," . $capacidad2->getValor() . "," . $capacidad1->getValor();

                                $resultadJson  = $this->activarClienteR1AD24A( $idDocumento, 
                                                                               $usuario, 
                                                                               $protocolo, 
                                                                               $interfaceNueva->getElementoId(), 
                                                                               $datos );
                            } 
                            else if ($nombreModeloElementoNuevo == "R1AD48A") 
                            {
                                $ptoFormateado = $nombreInterfaceElementoNuevo . ".1";
                                $datos         = $nombreInterfaceElementoNuevo . "," . $nombreInterfaceElementoNuevo . "," . $login . "," . 
                                                 $ptoFormateado . "," . $ptoFormateado . "," .$vciValor . "," . $ptoFormateado . "," . 
                                                 $nombreInterfaceElementoNuevo . "," . $capacidad2->getValor() . "," . $capacidad1->getValor();

                                $resultadJson  = $this->activarClienteR1AD48A( $idDocumento, 
                                                                               $usuario, 
                                                                               $protocolo, 
                                                                               $interfaceNueva->getElementoId(), 
                                                                               $datos);
                            } 
                            else if ($nombreModeloElementoNuevo == "6524") 
                            {
                                $datos = $nombreInterfaceElementoNuevo . "," . $nombreInterfaceElementoNuevo . "," . $capacidad2->getValor() . "," . 
                                         $capacidad1->getValor() . "," . $nombreInterfaceElementoNuevo . "," . $login . "," . 
                                         $nombreInterfaceElementoNuevo . "," . $vciValor;

                                $resultadJson = $this->activarCliente6524($idDocumento, 
                                                                          $usuario, 
                                                                          $protocolo, 
                                                                          $interfaceNueva->getElementoId(), 
                                                                          $datos);
                            } 
                            else if ($nombreModeloElementoNuevo == "7224") 
                            {
                                $datos = $nombreInterfaceElementoNuevo . "," . $nombreInterfaceElementoNuevo . "," . $vciValor . "," . 
                                         $nombreInterfaceElementoNuevo . "," . $nombreInterfaceElementoNuevo . "," . $nombreInterfaceElementoNuevo . 
                                         "," . $nombreInterfaceElementoNuevo . "," . $vciValor . "," . $nombreInterfaceElementoNuevo . "," . 
                                         $capacidad2->getValor() . "," . $capacidad1->getValor() . "," . $nombreInterfaceElementoNuevo . "," . $login;

                                $resultadJson = $this->activarCliente7224( $idDocumento, 
                                                                           $usuario,
                                                                           $protocolo, 
                                                                           $interfaceNueva->getElementoId(), 
                                                                           $datos);
                            } 
                            else if ($nombreModeloElementoNuevo == "MEA1") 
                            {
                                $datos        = $nombreInterfaceElementoNuevo . "," . $perfil . "," . $vciValor . "," . $vciValor . "," . $login;

                                $resultadJson = $this->activarClienteMea1( $idDocumento, 
                                                                           $usuario, 
                                                                           $protocolo, 
                                                                           $interfaceNueva->getElementoId(), 
                                                                           $datos);
                            } 
                            else if ($nombreModeloElementoNuevo == "MEA3") 
                            {
                                $datos        = $nombreInterfaceElementoNuevo . "," . $perfil . "," . $vciValor . "," . $vciValor . "," . $login;
                                $resultadJson = $this->activarClienteMea3( $idDocumento, 
                                                                           $usuario, 
                                                                           $protocolo, 
                                                                           $interfaceNueva->getElementoId(), 
                                                                           $datos );
                            }
                        } //fin si requiere aprovisionamiento
                        else 
                        {
                            $status = "OK";
                        }

                        if ($reqAprovisionamientoNuevo == "SI" && $servicio->getEstado() == "In-Corte") 
                        {
                            $caracteristicaVci = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                   ->findBy(array("descripcionCaracteristica" => "VCI", 
                                                                                  "estado"                    => "Activo"));
                            $pcVci             = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                   ->findBy(array("productoId"       => $producto->getId(), 
                                                                                  "caracteristicaId" => $caracteristicaVci[0]->getId()));
                            $ispcVci           = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                   ->findBy(array("servicioId"                => $servicio->getId(), 
                                                                                  "productoCaracterisiticaId" => $pcVci[0]->getId()));


                            if ($ispcVci[0]->getValor() > 31 && $ispcVci[0]->getValor() <= 100)
                                $vciValor = "0/" . $ispcVci[0]->getValor();
                            else
                                $vciValor = "0/35";
                            //*OBTENER SCRIPT--------------------------------------------------------*/
                            $scriptArray      = $this->servicioGeneral->obtenerArregloScript("cortarCliente", $modeloElementoNuevo);
                            $idDocumentoCorte = $scriptArray[0]->idDocumento;
                            $usuarioCorte     = $scriptArray[0]->usuario;
                            $protocoloCorte   = $scriptArray[0]->protocolo;
                            //*----------------------------------------------------------------------*/
                            if ($nombreModeloElementoNuevo == "6524") 
                            {
                                $resultadJson = $this->cortarCliente6524( $idDocumentoCorte, 
                                                                          $usuarioCorte, 
                                                                          $protocoloCorte, 
                                                                          $interfaceNueva->getElementoId(), 
                                                                          $interfaceNueva->getNombreInterfaceElemento());
                            } 
                            else if ($nombreModeloElementoNuevo == "7224") 
                            {
                                $resultadJson = $this->cortarCliente7224( $idDocumentoCorte, 
                                                                          $usuarioCorte, 
                                                                          $protocoloCorte, 
                                                                          $interfaceNueva->getElementoId(), 
                                                                          $interfaceNueva->getNombreInterfaceElemento());
                            } 
                            else if ($nombreModeloElementoNuevo == "R1AD24A") 
                            {
                                $resultadJson = $this->cortarClienteR1AD24A($idDocumentoCorte, 
                                                                            $usuarioCorte, 
                                                                            $protocoloCorte, 
                                                                            $interfaceNueva->getElementoId(), 
                                                                            $interfaceNueva->getNombreInterfaceElemento());
                            } 
                            else if ($nombreModeloElementoNuevo == "R1AD48A") 
                            {
                                $resultadJson = $this->cortarClienteR1AD48A($idDocumentoCorte, 
                                                                            $usuarioCorte, 
                                                                            $protocoloCorte, 
                                                                            $interfaceNueva->getElementoId(), 
                                                                            $interfaceNueva->getNombreInterfaceElemento());
                            } 
                            else if ($nombreModeloElementoNuevo == "A2024") 
                            {
                                $resultadJson = $this->cortarClienteA2024( $idDocumentoCorte, 
                                                                           $usuarioCorte, 
                                                                           $protocoloCorte, 
                                                                           $interfaceNueva->getElementoId(), 
                                                                           $interfaceNueva->getNombreInterfaceElemento());
                            } 
                            else if ($nombreModeloElementoNuevo == "A2048") 
                            {
                                $resultadJson = $this->cortarClienteA2048( $idDocumentoCorte, 
                                                                           $usuarioCorte, 
                                                                           $protocoloCorte, 
                                                                           $interfaceNueva->getElementoId(), 
                                                                           $interfaceNueva->getNombreInterfaceElemento());
                            } 
                            else if ($nombreModeloElementoNuevo == "MEA1") 
                            {
                                $resultadJson = $this->cortarClienteMea1($idDocumentoCorte, 
                                                                         $usuarioCorte, 
                                                                         $protocoloCorte, 
                                                                         $interfaceNueva->getElementoId(), 
                                                                         $interfaceNueva->getNombreInterfaceElemento());
                            } 
                            else if ($nombreModeloElementoNuevo == "MEA3") 
                            {
                                $resultadJson = $this->cortarClienteMea3($idDocumentoCorte, 
                                                                         $usuarioCorte, 
                                                                         $protocoloCorte, 
                                                                         $interfaceNueva->getElementoId(), 
                                                                         $interfaceNueva->getNombreInterfaceElemento());
                            }

                            $status = $resultadJson->status;
                        }//fin si requiere aprovisionamiento
                        else 
                        {
                            $status = "OK";
                        }
                    }
                } 
                else
                {// sino es tercerizado
                    $status = "OK";
                }

                if ($servicio->getEstado() == "Activo" || $servicio->getEstado() == "In-Corte") 
                {
                    //actualizar el enlace
                    $enlace->setInterfaceElementoIniId($interfaceNueva);
                    $this->emInfraestructura->persist($enlace);
                    $this->emInfraestructura->flush();
                }
            }

            //actualizar estado del puerto viejo
            $interfaceAnterior->setEstado("not connect");
            $this->emInfraestructura->persist($interfaceAnterior);
            $this->emInfraestructura->flush();

            //actualizar estado del puerto nuevo
            $interfaceNueva->setEstado("connected");
            $this->emInfraestructura->persist($interfaceNueva);
            $this->emInfraestructura->flush();

            //eliminar perfil del puerto anterior
            $detalleInterfaceAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleInterface')
                                                                ->findOneBy(array("interfaceElementoId" => $interfaceAnterior->getId()));
            if ($detalleInterfaceAnterior != null) 
            {
                $this->emInfraestructura->remove($detalleInterfaceAnterior);
                $this->emInfraestructura->flush();
            }

            //agregar perfil en el nuevo puerto
            if ($nombreModeloElementoNuevo == "A2024" || $nombreModeloElementoNuevo == "A2048" || $nombreModeloElementoNuevo == "MEA1" || 
                $nombreModeloElementoNuevo == "MEA3") 
            {
                $flag            = 0;
                $detalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                           ->findBy(array("elementoId"    => $elementoNuevoId, 
                                                                          "detalleNombre" => "PERFIL"));
                for ($i = 0; $i < count($detalleElemento); $i++) 
                {
                    $detalleCaracteristica1 = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleCaracteristica')
                                                                      ->findOneBy(array("detalleElementoId"         => $detalleElemento[$i], 
                                                                                        "caracteristicaId"          => $caracteristica1->getId(), 
                                                                                        "descripcionCaracteristica" => $capacidad1->getValor()));
                    $detalleCaracteristica2 = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleCaracteristica')
                                                                      ->findOneBy(array("detalleElementoId"         => $detalleElemento[$i], 
                                                                                        "caracteristicaId"          => $caracteristica2->getId(), 
                                                                                        "descripcionCaracteristica" => $capacidad2->getValor()));

                    if ($detalleCaracteristica1 != null && $detalleCaracteristica2 != null) 
                    {
                        if ($detalleCaracteristica1->getDetalleElementoId() == $detalleCaracteristica2->getDetalleElementoId()) 
                        {
                            $valor  = $detalleCaracteristica1->getValorCaracteristica();
                            $valor1 = explode("\r", $valor);
                            $flag   = 1;
                            break;
                        }
                    }
                }

                if (isset($valor1))
                {
                    $perfil = $valor1[0];
                } 
                else 
                {
                    return "NO EXISTE PERFIL";
                }

                //AGREGAR EL PERFIL EN LA INFO_SERVICIO_PROD_CARACT

                $caracteristicaPerfil = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                          ->findBy(array("descripcionCaracteristica" => "PERFIL", "estado" => "Activo"));
                $pcPerfil             = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                          ->findBy(array("productoId"       => $producto->getId(), 
                                                                         "caracteristicaId" => $caracteristicaPerfil[0]->getId()));
                $ispcPerfil           = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                          ->findOneBy(array("servicioId"                => $servicio->getId(), 
                                                                            "productoCaracterisiticaId" => $pcPerfil[0]->getId()));

                if ($ispcPerfil != null || $ispcPerfil != "") 
                {
                    $ispcPerfil->setValor($perfil);
                    $this->emComercial->persist($ispcPerfil);
                    $this->emComercial->flush();
                } 
                else 
                {
                    $spc = new InfoServicioProdCaract();
                    $spc->setEstado("Activo");
                    $spc->setProductoCaracterisiticaId($pcPerfil[0]->getId());
                    $spc->setValor($perfil);
                    $spc->setFeCreacion(new \DateTime('now'));
                    $spc->setUsrCreacion($usrCreacion);
                    $spc->setServicioId($servicio->getId());
                    $this->emComercial->persist($spc);
                    $this->emComercial->flush();
                }
            }

            //actualizar el puerto y el elemnto del servicio tecnico
            $servicioTecnico->setInterfaceElementoId($interfaceNueva->getId());
            $servicioTecnico->setElementoId($interfaceNueva->getElementoId()->getId());
            $this->emComercial->persist($servicioTecnico);
            $this->emComercial->flush();

            if ($servicio->getEstado() == "Activo" || $servicio->getEstado() == "EnVerificacion" || $servicio->getEstado("In-Corte"))
            {
                //historial del servicio - para cancelar el puerto viejo
                $servicioHistorial = new InfoServicioHistorial();
                $servicioHistorial->setServicioId($servicio);
                $servicioHistorial->setObservacion("Se cancelo el puerto " . $nombreInterfaceElemento);
                $servicioHistorial->setEstado($servicio->getEstado());
                $servicioHistorial->setMotivoId($motivo->getId());
                $servicioHistorial->setUsrCreacion($usrCreacion);
                $servicioHistorial->setFeCreacion(new \DateTime('now'));
                $servicioHistorial->setIpCreacion($ipCreacion);
                $this->emComercial->persist($servicioHistorial);
                $this->emComercial->flush();

                //historial del servicio - para activar el puerto nuevo
                $servicioHistorial1 = new InfoServicioHistorial();
                $servicioHistorial1->setServicioId($servicio);
                $servicioHistorial1->setObservacion("Se activo el puerto nuevo " . $nombreInterfaceElementoNuevo);
                $servicioHistorial1->setEstado($servicio->getEstado());
                $servicioHistorial1->setMotivoId($motivo->getId());
                $servicioHistorial1->setUsrCreacion($usrCreacion);
                $servicioHistorial1->setFeCreacion(new \DateTime('now'));
                $servicioHistorial1->setIpCreacion($ipCreacion);
                $this->emComercial->persist($servicioHistorial1);
                $this->emComercial->flush();
            }

            $result = "OK";
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
            $result = "ERROR EN LA LOGICA DE NEGOCIO, " . $e->getMessage();
            return $result;
        }

        //*DECLARACION DE COMMITS*/
        if ($this->emInfraestructura->getConnection()->isTransactionActive()) 
        {
            $this->emInfraestructura->getConnection()->commit();
        }

        if ($this->emComercial->getConnection()->isTransactionActive()) 
        {
            $this->emComercial->getConnection()->commit();
        }

        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        //*----------------------------------------------------------------------*/

        return $result;
    }

    /**
     * Funcion que sirve para crear la solicitud de cambio de um
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-06-2016
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 25-10-2016     Se agrega programación para poder generar solicitudes de cambio
     *                             de um para los servicios que utilicen la misma um del servicio
     *                             que gestiona el cambio
     * 
     * @param array $arrayParametros [idServicio, usrCreacion, ipCreacion]
     */
    public function crearSolicitudCambioUM($arrayParametros)
    {
        $resultado              = "No se creó la Solicitud de Cambio de UM";
        $arrayParametrosMismaUm = array();
        $strLoginesAux          = "";
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/        
        $this->emComercial->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        //
        //variables para conexion a la base de datos mediante conexion OCI
        $arrayOciCon                     = array();
        $arrayOciCon['user_comercial']   = $this->container->getParameter('user_comercial');
        $arrayOciCon['passwd_comercial'] = $this->container->getParameter('passwd_comercial');
        $arrayOciCon['dsn']              = $this->container->getParameter('database_dsn');
        
        try
        {
            $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                    ->findOneBy(array("servicioId" => $arrayParametros['idServicio']));
            if (!is_object($objServicioTecnico))
            {
                throw new \Exception("No existe información técnica para el servicio seleccionado.");
            }
            
            $objServicio = $this->emComercial
                                ->getRepository('schemaBundle:InfoServicio')
                                ->find($arrayParametros['idServicio']);
            if (!is_object($objServicio))
            {
                throw new \Exception("No existe información para el servicio seleccionado.");
            }
            
            $arrayParametrosMismaUm['intPuntoId']                     = $objServicio->getPuntoId()->getId();
            $arrayParametrosMismaUm['intElementoId']                  = $objServicioTecnico->getElementoId();
            $arrayParametrosMismaUm['intInterfaceElementoId']         = $objServicioTecnico->getInterfaceElementoId();
            $arrayParametrosMismaUm['intElementoClienteId']           = $objServicioTecnico->getElementoClienteId();
            $arrayParametrosMismaUm['intInterfaceElementoClienteId']  = $objServicioTecnico->getInterfaceElementoClienteId();
            $arrayParametrosMismaUm['intUltimaMillaId']               = $objServicioTecnico->getUltimaMillaId();
            $arrayParametrosMismaUm['intTercerizadoraId']             = $objServicioTecnico->getTercerizadoraId();
            $arrayParametrosMismaUm['intElementoContenedorId']        = $objServicioTecnico->getElementoContenedorId();
            $arrayParametrosMismaUm['intElementoConectorId']          = $objServicioTecnico->getElementoConectorId();
            $arrayParametrosMismaUm['intInterfaceElementoConectorId'] = $objServicioTecnico->getInterfaceElementoConectorId();
            $arrayParametrosMismaUm['strTipoEnlace']                  = $objServicioTecnico->getTipoEnlace();
            $arrayParametrosMismaUm['ociCon']                         = $arrayOciCon;
            
            $arrayParametrosRespuesta = $this->emComercial
                                             ->getRepository('schemaBundle:InfoServicioTecnico')
                                             ->getServiciosMismaUm($arrayParametrosMismaUm);
            if ($arrayParametrosRespuesta['strStatus'] == "ERROR")
            {
                $this->utilServicio->insertError('Telcos+', 
                                                 'InfoCambiarPuertoService.crearSolicitudCambioUM', 
                                                  $arrayParametrosRespuesta['strMensaje'],
                                                  $arrayParametros['usrCreacion'],
                                                  $arrayParametros['ipCreacion']);
                throw new \Exception("Error al recuperar servicios con misma UM.");
            }
            
            $objTipoSolicitud = $this->emComercial
                                     ->getRepository('schemaBundle:AdmiTipoSolicitud')
                                     ->findOneBy(array("descripcionSolicitud" => "SOLICITUD CAMBIO ULTIMA MILLA", "estado" => "Activo"));
            
            $objCaracteristicaUm  = $this->emComercial
                                         ->getRepository('schemaBundle:AdmiCaracteristica')
                                         ->findOneBy(array("descripcionCaracteristica" => "SERVICIO_MISMA_ULTIMA_MILLA",
                                                           "estado"                    => "Activo"));
            
            $arrayRegistrosServicios       = $arrayParametrosRespuesta['arrayRegistros'];
            $arrayRegistrosServiciosCaract = $arrayParametrosRespuesta['arrayRegistros'];
            
            //recupera logines auxiliares de los servicios a crear solicitud para setear historiales de servicios
            foreach($arrayRegistrosServicios as $strIdServicio):
                $objServicio = $this->emComercial
                                    ->getRepository('schemaBundle:InfoServicio')
                                    ->find($strIdServicio);
                if (is_object($objServicio))
                {
                    $strLoginesAux = $strLoginesAux . $objServicio->getLoginAux() . ' ';
                }
            endforeach;
            
            foreach($arrayRegistrosServicios as $strIdServicio):
                $objServicio = $this->emComercial
                                    ->getRepository('schemaBundle:InfoServicio')
                                    ->find($strIdServicio);
                //crear solicitud
                $objDetalleSolicitud = new InfoDetalleSolicitud();
                $objDetalleSolicitud->setServicioId($objServicio);
                $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
                $objDetalleSolicitud->setUsrCreacion($arrayParametros['usrCreacion']);
                $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitud->setEstado("FactibilidadEnProceso");
                $this->emComercial->persist($objDetalleSolicitud);
                $this->emComercial->flush();
                
                foreach($arrayRegistrosServiciosCaract as $strServicioMismaUm):
                    $objDetSolCaracteristicaUm= new InfoDetalleSolCaract();
                    $objDetSolCaracteristicaUm->setCaracteristicaId($objCaracteristicaUm);
                    $objDetSolCaracteristicaUm->setDetalleSolicitudId($objDetalleSolicitud);
                    $objDetSolCaracteristicaUm->setValor($strServicioMismaUm);
                    $objDetSolCaracteristicaUm->setEstado("FactibilidadEnProceso");
                    $objDetSolCaracteristicaUm->setUsrCreacion($arrayParametros['usrCreacion']);
                    $objDetSolCaracteristicaUm->setFeCreacion(new \DateTime('now'));
                    $this->emComercial->persist($objDetSolCaracteristicaUm);
                    $this->emComercial->flush();
                endforeach;
                //agregar historial a la solicitud
                $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
                $objDetalleSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolicitudHistorial->setIpCreacion($arrayParametros['ipCreacion']);
                $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitudHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
                $objDetalleSolicitudHistorial->setEstado("FactibilidadEnProceso");
                $this->emComercial->persist($objDetalleSolicitudHistorial);
                $this->emComercial->flush();

                //agregar servicio historial
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setIpCreacion($arrayParametros['ipCreacion']);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
                $objServicioHistorial->setEstado($objServicio->getEstado());
                $objServicioHistorial->setObservacion('Se creo solicitud de cambio de UM en estado: FactibilidadEnProceso, '.
                                                      ' para los servicios: '.$strLoginesAux);
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();

            endforeach;
            
            //*DECLARACION DE COMMITS*/
            if ($this->emComercial->getConnection()->isTransactionActive()) 
            {
                $this->emComercial->getConnection()->commit();
            }

            $resultado = "OK"."|".$strLoginesAux;
        }
        catch (\Exception $e) 
        {
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
                        
            $resultado = $resultado. " <br> ERROR: ".$e->getMessage();                                    
        }

        $this->emComercial->getConnection()->close();
        
        return $resultado;
    }
    
    /**
     * cambiarUltimaMillaTn
     * 
     * Funcion que se encarga de realizar el cambio de ultima milla de una solicitud
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @since 30-06-2016
     * @version 1.0          
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 05-07-2016 Se cambia envio de vlan->mac al WS y que sea solo del cliente a configurar
     * 
     * @param Array $arrayParametros [ idEmpresa , prefijoEmpresa , idServicio , elementoId , interfaceElementoId , dslamId , elementoCajaId ,
     *                                 elementoConectorId , interfaceElementoConectorId , requiereScript , usrCreacion , ipCreacion , producto ]
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @since 28-07-2016 - Se discrimina tipo de ultima milla y backbone para que segun el caso tome la interface elemento conector solo para 
     *                     FIBRA RUTA
     * @version 1.2
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @since 24-11-2016 - Se corrige parametro URL en ejecución de WS de networking para configuración de servicio
     * @version 1.3
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @since 11-07-2017 - Se corrige envio de BW en switch nuevo o switch viejo de acuerdo a sumatoria realizada por puerto para envio de bw
     * @version 1.4
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 21-02-2018 Se regulariza cambios realizados en caliente 
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @since 24-05-2018
     * @version 1.6 - Se envia descripcion de acuerdo a la Ultima milla del servicio para identificacion de NW
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.7 17-07-2018 - Se enviá nuevo parámetro al método getPeBySwitch
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.8 25-10-2019 - Se agregan nuevos parametros:
     *                           'booleanEjeWsNetworking' => para ejecutar los web servicios de networking
     *                           'booleanSetearConexion'  => para habilitar el seteo de las conexiones de la base de este metodo
     *                           desde los parametros 'emComercial' y 'emInfraestructura'.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.9 05-12-2019 - Se realizan ajustes en el cambio de ultima milla en los ws de networking
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.10 31-03-2020 - Se realiza la configuración del BGP en el cambio de última milla con diferente PE o anillo
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.0 01-06-2020 - Se agrega el id del servicio a la url 'cambio_um' y 'migracion_anillo'
     *                           del ws de networking para la validación del BW
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.1 22-01-2021 - Se actualiza el estado de la interface nueva a 'connected' cuando la um es Fibra Ruta.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.2 15-04-2021 - Se abre la programacion tambien para productos L3MPLS SDWAN
     *
     * @author Rafael Vera <rsvera@telconet.ec>
     * @version 2.3 31-05-2023 - Se implemento la suma de las capacidad masivas en última milla
     * 
     * @param Array $arrayParametros [ idEmpresa , prefijoEmpresa , idServicio ]
     * 
     * @return string $result
     */
    public function cambiarUltimaMillaTn($arrayParametros)
    {
        //este parametro me identifica si debo setear las conexiones de la base
        $booleanSetearConexion           = false;
        //este parametro me identifica si debo ejecutar los web servicios de networking
        $booleanEjeWsNetworking          = true;
        $interfaceElementoCassetteNuevo  = null;                
        $nombreElementoAnterior          = '';
        $nombreInterfaceElementoAnterior = '';

        $status = "ERROR";
        
        //verifico si existe el parametro 'booleanSetearConexion' y seteo la variable booleanSetearConexion
        if( isset($arrayParametros['booleanSetearConexion']) && !empty($arrayParametros['booleanSetearConexion']) &&
            isset($arrayParametros['emComercial']) && !empty($arrayParametros['emComercial']) &&
            isset($arrayParametros['emInfraestructura']) && !empty($arrayParametros['emInfraestructura']) )
        {
            $booleanSetearConexion  = $arrayParametros['booleanSetearConexion'];
        }

        //verifico si existe el parametro 'booleanEjeWsNetworking' y seteo la variable booleanEjeWsNetworking
        if( isset($arrayParametros['booleanEjeWsNetworking']) )
        {
            $booleanEjeWsNetworking = $arrayParametros['booleanEjeWsNetworking'];
        }

        try
        {
            if( $booleanSetearConexion )
            {
                $this->emComercial          = $arrayParametros['emComercial'];
                $this->emInfraestructura    = $arrayParametros['emInfraestructura'];
            }
            else
            {
                $this->emComercial->getConnection()->beginTransaction();
                $this->emInfraestructura->getConnection()->beginTransaction();
            }
            
            $objServicio        = $this->emComercial->getRepository("schemaBundle:InfoServicio")
                                                    ->find($arrayParametros['idServicio']);
            $objProducto        = $objServicio->getProductoId();
            $objServicioTecnico = $this->emComercial->getRepository("schemaBundle:InfoServicioTecnico")
                                                    ->findOneBy(array("servicioId" => $objServicio->getId()));
            $objTipoSolicitud   = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                    ->findOneBy(array("descripcionSolicitud" => "SOLICITUD CAMBIO ULTIMA MILLA","estado"=>"Activo"));
            $objSolicitud       = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                    ->findOneBy(array("tipoSolicitudId"   => $objTipoSolicitud->getId(), 
                                                                      "servicioId"        => $objServicio->getId(),
                                                                      "estado"            => "Asignada"));           
            $ultimaMillaObj     = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                      ->find($objServicioTecnico->getUltimaMillaId());
            $objDetSolTipoCambioUM = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "TIPO_CAMBIO_ULTIMA_MILLA");
            
            //Obtener la caracteristica TIPO_FACTIBILIDAD para discriminar que sea FIBRA DIRECTA o RUTA
            $objServProdCaractTipoFact = $this->servicioGeneral
                                              ->getServicioProductoCaracteristica($objServicio,'TIPO_FACTIBILIDAD',$objProducto);
            
            //Verificar si un servicio es pseudoPe
            $boolEsPseudoPe  = $this->emComercial->getRepository("schemaBundle:InfoServicio")->esServicioPseudoPe($objServicio);
            
            $boolEsFibraRuta = false;
            
            if($objServProdCaractTipoFact)
            {
                if($ultimaMillaObj->getNombreTipoMedio() == "Fibra Optica" && $objServProdCaractTipoFact->getValor() == "RUTA")
                {
                    $boolEsFibraRuta = true;
                }
            }
            else
            {
                if($ultimaMillaObj->getNombreTipoMedio() == "Fibra Optica")
                {
                    $boolEsFibraRuta = true;
                }                
            }

            // --------------------- DATOS ANTERIORES ---------------------- //
            
            // Switch
            $objDetSolElementoId = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "ELEMENTO_ID");
            
            if(is_object($objDetSolElementoId))
            {
                $objElementoAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objDetSolElementoId->getValor());
                
                $objDetEleAnillo = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                        ->findOneBy(array("elementoId" => $objElementoAnterior->getId(),
                                                                          "detalleNombre" => "ANILLO",
                                                                          "estado"  => "Activo"));
            }
            else
            {
                throw new \Exception("No Existe Elemento Anterior,Favor Notificar a Sistemas!");
            }
            
            //puerto switch anterior
            $objDetSolInterfaceElementoId = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "INTERFACE_ELEMENTO_ID");
            
            if(is_object($objDetSolInterfaceElementoId))
            {
                $objInterfaceElementoAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                        ->find($objDetSolInterfaceElementoId->getValor());
            }
            else
            {
                throw new \Exception("No Existe Interface Elemento Anterior,Favor Notificar a Sistemas!");
            }                        
            
            //caja anterior
            $objDetSolElementoContenedorId = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "ELEMENTO_CONTENEDOR_ID");
            
            if(is_object($objDetSolElementoContenedorId))
            {
                $objElementoContenedorAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                            ->find($objDetSolElementoContenedorId->getValor());
            }
            
            //cassette anterior
            $objDetSolElementoConectorId = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "ELEMENTO_CONECTOR_ID");
            
            if(is_object($objDetSolElementoConectorId))
            {
                $objElementoConectorAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                ->find($objDetSolElementoConectorId->getValor());
            }
            
            //puerto cassette anterior ( FIBRA RUTA )
            $objDetSolInterfaceElementoConectorId = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "INTERFACE_ELEMENTO_CONECTOR_ID");
            
            if(is_object($objDetSolInterfaceElementoConectorId))
            {
                $objInterfaceElementoConectorAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                        ->find($objDetSolInterfaceElementoConectorId->getValor());
            }      
            
            //vlan anterior
            $objDetSolVlan = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "VLAN");
            
            $strVlanAnterior = $objDetSolVlan->getValor();
            
            if($objProducto->getNombreTecnico()=="L3MPLS" || $objProducto->getNombreTecnico()=="L3MPLS SDWAN")
            {   
                //vrf anterior
                $objDetSolVrf = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "VRF");
                
                if($objDetSolVrf)
                {
                    $strVrfAnterior = $objDetSolVrf->getValor();
                }
                
                //protocolo anterior
                $objDetSolProtocolo = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "PROTOCOLO_ENRUTAMIENTO");
                
                if($objDetSolProtocolo)
                {
                    $strProtocoloAnterior = $objDetSolProtocolo->getValor();
                }
            }
            
            // -------------------------------------- FIN DATOS ANTERIORES //
            
            // ----------------- DATOS NUEVOS -------------------- //
            
            //Switch Nuevo
            $elementoNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objServicioTecnico->getElementoId());
            
            //Puerto Switch Nuevo
            $interfaceElementoNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                              ->find($objServicioTecnico->getInterfaceElementoId());         
            
            if($boolEsFibraRuta)
            {
                //Caja nueva
                $cajaNueva = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objServicioTecnico->getElementoContenedorId());
                
                //Cassette Nuevo
                $cassetteNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objServicioTecnico->getElementoConectorId());
                
                //puerto cassette nuevo FIBRA OPTICA - RUTA
                $interfaceElementoCassetteNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                          ->find($objServicioTecnico->getInterfaceElementoConectorId());
            }
            
            //Se obtiene VLAN de acuerdo a si el proceso es pseudoPE
            $strVlanNueva = '';
            
            if(!$boolEsPseudoPe)
            {
                //vlan nueva
                $objSolCaracVlan = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "VLAN", $objServicio->getProductoId());

                if($objProducto->getNombreTecnico()=="L3MPLS" || $objProducto->getNombreTecnico()=="L3MPLS SDWAN")
                {  
                    $objPerEmpRolCarVlan = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                            ->find($objSolCaracVlan->getValor());

                    $objDetalleElementoVlan = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                    ->find($objPerEmpRolCarVlan->getValor());
                }
                else
                {
                    $objDetalleElementoVlan = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                    ->find($objSolCaracVlan->getValor());
                }

                $strVlanNueva = $objDetalleElementoVlan->getDetalleValor();
            }
            else
            {
                $objServProdCaractVlanPseudoPe   = $this->servicioGeneral
                                                        ->getServicioProductoCaracteristica($objServicio,
                                                                                            'VLAN_PROVEEDOR',
                                                                                            $objServicio->getProductoId());
                if(is_object($objServProdCaractVlanPseudoPe))
                {
                    $strVlanNueva = $objServProdCaractVlanPseudoPe->getValor();
                }
            }
            
            // --------------------------------------FIN DATOS NUEVOS //
            
            // EJECUTAR CAMBIO ULTIMA MILLA //
            //verifico si se debe ejecutar los web servicios de networking
            if( $booleanEjeWsNetworking )
            {
                //Capacidades totales de los servicios activos ligados a un puerto
                $arrayCapacidades = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                            ->getResultadoCapacidadesPorInterface($objInterfaceElementoAnterior->getId());

                //mac actual del servicio para realizar validacion y no incluirla
                $macServicio        = $arrayParametros['mac'];

                $arrayMacServicio[] = $macServicio;
                $arrayMacVlan = array($strVlanAnterior=>$arrayMacServicio);

                $strDescripcion = '';
                $strUltimaMilla = $ultimaMillaObj->getNombreTipoMedio();

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

                //arreglo para ejecutar el script de cambio de ultima milla en networking
                $arrayPeticiones = array();
                $arrayPeticiones['anillo']       = '';
                $arrayPeticiones['user_name']    = $arrayParametros['usrCreacion'];
                $arrayPeticiones['user_ip']      = $arrayParametros['ipCreacion'];
                $arrayPeticiones['servicio']     = $objProducto->getNombreTecnico();
                $arrayPeticiones['id_servicio']  = $objServicio->getId();
                $arrayPeticiones['nombreMetodo'] = 'InfoCambiarPuertoService.cambiarUltimaMillaTn';
                $arrayPeticiones['login_aux']    = $objServicio->getLoginAux();
                $arrayPeticiones['descripcion']  = 'cce_'.$objServicio->getLoginAux().$strDescripcion;

                $arrayPeticiones['sw_anterior']  = $objElementoAnterior->getNombreElemento();
                $arrayPeticiones['pt_anterior']  = $objInterfaceElementoAnterior->getNombreInterfaceElemento();
                $arrayPeticiones['bw_anterior']  = array('up'   => intval($arrayCapacidades['totalCapacidad1']),
                                                         'down' => intval($arrayCapacidades['totalCapacidad2']));
                $arrayPeticiones['cpe_anterior'] = array();
                foreach( $arrayMacVlan as $strValueVlanCPE => $arrayValueMacCPE )
                {
                    foreach( $arrayValueMacCPE as $strValueMacCPE )
                    {
                        $arrayPeticiones['cpe_anterior'][] = array(
                                    'vlan' => $strValueVlanCPE,
                                    'mac'  => $strValueMacCPE,
                                );
                    }
                }

                //Capacidades totales de los servicios activos ligados al puerto nuevo
                $arrayCapacidadesNueva           = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                            ->getResultadoCapacidadesPorInterface($interfaceElementoNuevo->getId());
                $this->utilServicio->validaObjeto($arrayCapacidadesNueva, "Capacidades del elemento nuevo con errores, favor notificar a Sistemas.");
                $arrayMacVlanNueva               = array($strVlanNueva=>$arrayMacServicio);
                $intCapacidadUnoNuevo = 0;
                $intCapacidadDosNuevo = 0;
                if(isset($arrayParametros['booleanProcesoMasivo']) && $arrayParametros['booleanProcesoMasivo'])
                {
                    $intCapacidadUnoNuevo = $arrayParametros['capacidad1'];
                    $intCapacidadDosNuevo = $arrayParametros['capacidad2'];
                }
                $arrayPeticiones['sw_nuevo']     = $elementoNuevo->getNombreElemento();
                $arrayPeticiones['pt_nuevo']     = $interfaceElementoNuevo->getNombreInterfaceElemento();
                $arrayPeticiones['bw_nueva']     = array('up'   => intval($arrayCapacidadesNueva['totalCapacidad1']) + intval($intCapacidadUnoNuevo),
                                                         'down' => intval($arrayCapacidadesNueva['totalCapacidad2']) + intval($intCapacidadDosNuevo));
                $arrayPeticiones['cpe_nuevo']    = array();
                foreach( $arrayMacVlanNueva as $strValueVlanCPE => $arrayValueMacCPE )
                {
                    foreach( $arrayValueMacCPE as $strValueMacCPE )
                    {
                        $arrayPeticiones['cpe_nuevo'][] = array(
                                    'vlan' => $strValueVlanCPE,
                                    'mac'  => $strValueMacCPE,
                                );
                    }
                }

                if( $objDetSolTipoCambioUM->getValor() == "DIFERENTE_PE" || $objDetSolTipoCambioUM->getValor() == "MISMO_PE_DIFERENTE_ANILLO" )
                {
                    $strVlanAnterior      = "";
                    if( !$boolEsPseudoPe )
                    {
                        $objSolCaracVlan     = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "VLAN");
                        if( $objProducto->getNombreTecnico() == "L3MPLS" || $objProducto->getNombreTecnico() == "L3MPLS SDWAN")
                        {
                            $objPerEmpRolCarVlan                = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                    ->find($objSolCaracVlan->getValor());
                            if( is_object($objPerEmpRolCarVlan) )
                            {
                                $objDetalleElementoVlanAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->find($objPerEmpRolCarVlan->getValor());
                            }
                        }
                        else
                        {
                            $objDetalleElementoVlanAnterior     = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->find($objSolCaracVlan->getValor());
                        }
                        if( isset($objDetalleElementoVlanAnterior) && is_object($objDetalleElementoVlanAnterior) )
                        {
                            $strVlanAnterior = $objDetalleElementoVlanAnterior->getDetalleValor();
                        }
                    }
                    else
                    {
                        $objServProdCaractVlanPseudoPe  = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                                    'VLAN_PROVEEDOR',
                                                                                                                    $objServicio->getProductoId());
                        if( is_object($objServProdCaractVlanPseudoPe) )
                        {
                            $strVlanAnterior = $objServProdCaractVlanPseudoPe->getValor();
                        }
                    }

                    if( $strVlanAnterior != "" )
                    {
                        $arrayMacVlan = array($strVlanAnterior=>$arrayMacServicio);
                        $arrayPeticiones['cpe_anterior'] = array();
                        foreach( $arrayMacVlan as $strValueVlanCPE => $arrayValueMacCPE )
                        {
                            foreach( $arrayValueMacCPE as $strValueMacCPE )
                            {
                                $arrayPeticiones['cpe_anterior'][] = array(
                                            'vlan' => $strValueVlanCPE,
                                            'mac'  => $strValueMacCPE,
                                        );
                            }
                        }
                    }

                    $arrayPeticiones['url']                  = 'migracion_anillo';
                    $arrayPeticiones['accion']               = 'migracion_anillo';
                    $arrayPeticiones['servicio']             = $objProducto->getNombreTecnico();

                    $arrayPeticiones['login']                = $objServicio->getPuntoId()->getLogin();
                    $arrayPeticiones['razon_social']         = "";
                    $arrayPeticiones['vrf']                  = "";
                    $arrayPeticiones['rutas_anteriores']     = array();
                    $arrayPeticiones['rutas_nuevas']         = array();
                    $arrayPeticiones['interfaz_pe_anterior'] = "";
                    $arrayPeticiones['interfaz_pe_nueva']    = "";
                    $arrayPeticiones['protocolo']            = "STANDARD";
                    $arrayPeticiones['ip_bgp_anterior']      = "";
                    $arrayPeticiones['ip_bgp_nueva']         = "";

                    if( $objProducto->getNombreTecnico() != "INTERNET" )
                    {
                        //obtener el nombre de la vrf
                        $objSolCaracVrf         = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "VRF");
                        $this->utilServicio->validaObjeto($objSolCaracVrf, "No se encontraron datos de la Vrf, favor notificar a Sistemas.");
                        $objPerEmpRolCarVrf     = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                        ->find($objSolCaracVrf->getValor());
                        $this->utilServicio->validaObjeto($objPerEmpRolCarVrf, "No se encontraron datos de la Vrf, favor notificar a Sistemas.");

                        //obtener el rd de la vrf
                        $strRdVrf               = "";
                        $objCaractRdId          = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy(array("descripcionCaracteristica" => "RD_ID", "estado" => "Activo"));
                        if( $objProducto->getNombreTecnico() == "L3MPLS" || $objProducto->getNombreTecnico() == "L3MPLS SDWAN")
                        {
                            $objPerEmpRolCarRdId    = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                ->findOneBy(array("personaEmpresaRolCaracId" => $objPerEmpRolCarVrf->getPersonaEmpresaRolCaracId(),
                                                                  "caracteristicaId"         => $objCaractRdId->getId(),
                                                                  "estado"                   => "Activo"));
                            $this->utilServicio->validaObjeto($objPerEmpRolCarRdId,"No se encontraron datos del Rd-Id, favor notificar a Sistemas.");
                            $strRdVrf               = $objPerEmpRolCarRdId->getValor();
                        }

                        //obtengo el valor de la VRF
                        $strValorVrf            = $objPerEmpRolCarVrf->getValor();

                        //obtengo el parametro para la reemplazar la VRF si es necesario
                        $objParametroCabVrf     = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                                            array(  'nombreParametro'   => 'VALORES_VRF_TELCONET',
                                                                                    'estado'            => 'Activo'));
                        if( is_object($objParametroCabVrf) )
                        {
                            $objParametroDetVrf = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy(
                                                                            array(  "parametroId"   => $objParametroCabVrf->getId(),
                                                                                    "valor1"        => $strValorVrf,
                                                                                    "estado"        => "Activo"));
                            //si existe el parametro reemplazo el valor de la VRF
                            if( is_object($objParametroDetVrf) )
                            {
                                $strValorVrf    = $objParametroDetVrf->getValor2();
                            }
                        }

                        //ingreso el name y el rd en la llave 'vrf' de la variable arrayPeticiones
                        $arrayPeticiones['vrf'] = array(
                                                    'name'      => $strValorVrf,
                                                    'rd'        => $strRdVrf,
                                                    'rt_export' => '',
                                                    'rt_import' => ''
                                                  );

                        //obtener la razon social
                        $strRazonSocial     = "";
                        $objInfoPersona     = $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
                        if( is_object($objInfoPersona) )
                        {
                            $strRazonSocial = $objInfoPersona->getRazonSocial();
                        }
                        if( !empty($strRazonSocial) )
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
                            if( isset($arrayRazonesSociales["valor1"]) && !empty($arrayRazonesSociales["valor1"]) )
                            {
                                $strRazonSocial                        = $arrayRazonesSociales["valor4"];
                                //ingreso el rt_export y el rt_import en la llave 'vrf' de la variable arrayPeticiones
                                $arrayPeticiones['vrf']['rt_export']   = $arrayRazonesSociales["valor2"];
                                $arrayPeticiones['vrf']['rt_import']   = $arrayRazonesSociales["valor3"];
                            }
                            else
                            {
                                $strRazonSocial = "";
                            }
                        }
                        $arrayPeticiones['razon_social'] = $strRazonSocial;

                        //arreglo de todas las rutas anteriores del elemento
                        $arrayRutas     = $this->emInfraestructura->getRepository("schemaBundle:InfoRutaElemento")
                                                            ->findBy(array( "servicioId"    => $objServicio->getId(),
                                                                            "estado"        => "Activo"));
                        //obtengo la ip que contiene la subred
                        $objDetSolIpId  = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "IP_ID");
                        $objIp          = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')->find($objDetSolIpId->getValor());
                        $arrayRutasAnteriores = array();
                        foreach( $arrayRutas as $objRuta )
                        {
                            $strRedLan            = $objRuta->getRedLan();
                            $strMascaraRedLan     = $objRuta->getMascaraRedLan();
                            if( $strRedLan == null || $strMascaraRedLan == null )
                            {
                                $objSubred        = $objRuta->getSubredId();
                                $this->utilServicio->validaObjeto($objSubred,
                                                                  "No se encontraron sub redes en las rutas, favor notificar a Sistemas.");
                                $strMascaraRedLan = $objSubred->getMascara();
                                $arrayRedLan      = explode('/', $objSubred->getSubred());
                                $strRedLan        = $arrayRedLan[0];
                            }
                            $arrayRutasAnteriores[] = array(
                                                            'subred'      => $strRedLan,
                                                            'mask'        => $strMascaraRedLan,
                                                            'gateway'     => $objIp->getIp(),
                                                            'description' => $objRuta->getNombre()
                                                        );
                            //cambio el estado de la ruta a Eliminado
                            $objRuta->setEstado("Eliminado");
                            $this->emInfraestructura->persist($objRuta);
                            $this->emInfraestructura->flush();
                        }
                        $arrayPeticiones['rutas_anteriores'] = $arrayRutasAnteriores;
                    }

                    if( $objProducto->getNombreTecnico() == "L3MPLS" || $objProducto->getNombreTecnico() == "L3MPLS SDWAN")
                    {
                        //obtengo los datos del PE anterior
                        $arrayDesconfig     = array(
                                                'objServicio'           => $objServicio,
                                                'objDetalleSolicitud'   => $objSolicitud,
                                                'booleanEsPseudoPe'     => $boolEsPseudoPe
                                            );
                        $arrayPeticiones['interfaz_pe_anterior'] = $this->getDatosPePorSolicitud($arrayDesconfig);

                        //obtengo los datos del PE nuevo
                        $arrayConfigurar    = array(
                                                'objServicio'       => $objServicio,
                                                'booleanEsPseudoPe' => $boolEsPseudoPe
                                            );
                        $arrayPeticiones['interfaz_pe_nueva']    = $this->getDatosPePorServicio($arrayConfigurar);
                    }

                    //obtengo el protocolo de enrutamiento
                    $objServCaractProtocolo = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                        "PROTOCOLO_ENRUTAMIENTO",
                                                                                                        $objProducto);
                    if(is_object($objServCaractProtocolo))
                    {
                        //agrego el valor del protocolo de enrutamiento
                        $arrayPeticiones['protocolo']    = $objServCaractProtocolo->getValor();
                        //verifico que el protocolo de enrutamiento sea BGP para agregar las Ips
                        if( $arrayPeticiones['protocolo'] == 'BGP' )
                        {
                            $objDetSolIpId  = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "IP_ID");
                            $this->utilServicio->validaObjeto($objDetSolIpId,
                                                            "El servicio no tiene ingresada la Ip Anterior en la característica de la solicitud, ".
                                                            "favor notificar a Sistemas.");
                            $objIpAnterior  = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')->find($objDetSolIpId->getValor());
                            if(is_object($objIpAnterior) )
                            {
                                //agrego la ip del BGP anterior
                                $arrayPeticiones['ip_bgp_anterior'] = $objIpAnterior->getIp();
                            }
                            $objIpNueva     = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                             ->findOneBy(array("servicioId"    => $objServicio->getId(),
                                                                               "estado"        => "Activo"));
                            if(is_object($objIpNueva) )
                            {
                                //agrego la ip del BGP nueva
                                $arrayPeticiones['ip_bgp_nueva']    = $objIpNueva->getIp();
                            }
                        }
                    }
                }
                else
                {
                    $arrayPeticiones['url']          = 'cambio_um';
                    $arrayPeticiones['accion']       = 'cambio_um';
                }

                //Ejecución del método vía WS para realizar la configuración del SW
                $arrayRespuesta = $this->networkingScripts->callNetworkingWebService($arrayPeticiones);

                $status  = $arrayRespuesta['status'];
                $mensaje = $arrayRespuesta['mensaje'];
            }
            else
            {
                $status  = "OK";
                $mensaje = "OK";
            }
                        
            //Si ejecuta script de valida con respuesta de WS para continuar, si no requiere script
            //Solo ejecuta cambio ultima milla a nivel logico
            if ($status == "OK") 
            {
                //ver servicios del puerto anterior del sw
                $serviciosPorInterface = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                               ->getServiciosPorInterfaceProducto($objInterfaceElementoAnterior,
                                                                                  $arrayParametros['producto'],
                                                                                  $arrayParametros['idEmpresa']
                                                                                  );

                //si solo hay un servicio en el puerto se lo libera
                if(count($serviciosPorInterface) == 1)
                {
                    $objInterfaceElementoAnterior->setEstado('not connect');
                    $this->emInfraestructura->persist($objInterfaceElementoAnterior);
                    $this->emInfraestructura->flush();

                    //Se obtiene el enlace final del cliente anterior
                    $objInfoEnlace = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                      ->findOneBy(array(
                                                          "interfaceElementoIniId" => $objInterfaceElementoAnterior)
                                                      );                                                                        
                    //Se elimina en enlace anterior entre cassette y roseta
                    if (is_object($objInfoEnlace)) 
                    {
                        $objInfoEnlace->setEstado("Eliminado");
                        $this->emInfraestructura->persist($objInfoEnlace);
                        $this->emInfraestructura->flush();
                    }
                }
                
                //verificar datos del cassette
                if($objDetSolTipoCambioUM->getValor() != "MISMO_SW")
                {
                    if(is_object($objInterfaceElementoConectorAnterior))
                    {
                        //ver servicios del puerto anterior del cassette
                        $serviciosPorInterface = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                       ->getServiciosPorInterfaceProducto($objInterfaceElementoConectorAnterior,
                                                                                          $arrayParametros['producto'],
                                                                                          $arrayParametros['idEmpresa']
                                                                                          );

                        //si solo hay un servicio en el puerto se lo libera
                        if(count($serviciosPorInterface) == 1)
                        {
                            $objInterfaceElementoAnterior->setEstado('not connect');
                            $this->emInfraestructura->persist($objInterfaceElementoAnterior);
                            $this->emInfraestructura->flush();

                            //Se obtiene el enlace final del cliente anterior
                            $objInfoEnlace = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                              ->findOneBy(array(
                                                                  "interfaceElementoIniId" => $objInterfaceElementoAnterior)
                                                              );                                                                        
                            //Se elimina en enlace anterior entre cassette y roseta
                            if (is_object($objInfoEnlace)) 
                            {
                                $objInfoEnlace->setEstado("Eliminado");
                                $this->emInfraestructura->persist($objInfoEnlace);
                                $this->emInfraestructura->flush();
                            }
                        }
                    }//if(is_object($objInterfaceElementoConectorAnterior))
                }//if($objDetSolTipoCambioUM->getValor() != "MISMO_SW")                
                
                //Si es Fibra RUTA actualizo a conectado la interface del cassette
                if($boolEsFibraRuta)
                {
                    //actualizo la interface nueva a ocupado 
                    $interfaceElementoCassetteNuevo->setEstado('connected');
                    $this->emInfraestructura->persist($interfaceElementoCassetteNuevo);
                    $this->emInfraestructura->flush();
                }
                $interfaceElementoNuevo->setEstado('connected');
                $this->emInfraestructura->persist($interfaceElementoNuevo);
                $this->emInfraestructura->flush();

                //historial del servicio
                $servicioHistorial = new InfoServicioHistorial();
                $servicioHistorial->setServicioId($objServicio);
                $servicioHistorial->setObservacion("Se realizó el Cambio de Ultima Milla con éxito");
                $servicioHistorial->setEstado($objServicio->getEstado());
                $servicioHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
                $servicioHistorial->setFeCreacion(new \DateTime('now'));
                $servicioHistorial->setIpCreacion($arrayParametros['ipCreacion']);
                $this->emComercial->persist($servicioHistorial);
                $this->emComercial->flush(); 
                
                $objSolicitud->setEstado("Finalizada");
                $this->emComercial->persist($objSolicitud);
                $this->emComercial->flush();

                //agregar historial a la solicitud
                $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
                $objDetalleSolicitudHistorial->setDetalleSolicitudId($objSolicitud);
                $objDetalleSolicitudHistorial->setIpCreacion($arrayParametros['ipCreacion']);
                $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitudHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
                $objDetalleSolicitudHistorial->setEstado("Finalizada");
                $this->emComercial->persist($objDetalleSolicitudHistorial);
                $this->emComercial->flush();
                //actualizar las solicitudes caract
                $arrayDetalleSolicitudCarac = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                               ->findBy(array("detalleSolicitudId"  => $objSolicitud->getId(), 
                                                              "estado"              => "Asignada"));
                foreach($arrayDetalleSolicitudCarac as $objDetalleSolCarac)
                {
                    $objDetalleSolCarac->setEstado("Finalizada");
                    $this->emComercial->persist($objDetalleSolCarac);
                    $this->emComercial->flush();
                }
                
                $result = "OK";
            }
            else
            {
                throw new \Exception($mensaje);
            }
            
            if( $result == 'OK' )
            {
                $this->emComercial->flush();
                $this->emInfraestructura->flush();
                
                if( !$booleanSetearConexion )
                {
                    $this->emComercial->getConnection()->commit();
                    $this->emComercial->getConnection()->close();
                    
                    $this->emInfraestructura->getConnection()->commit();
                    $this->emInfraestructura->getConnection()->close();
                }
            }
        }
        catch (\Exception $e)
        {
            if ($this->emInfraestructura->getConnection()->isTransactionActive()){
                $this->emInfraestructura->getConnection()->rollback();
                if( !$booleanSetearConexion )
                {
                    $this->emInfraestructura->getConnection()->close();
                }
            }

            if ($this->emComercial->getConnection()->isTransactionActive()){
                $this->emComercial->getConnection()->rollback();
                if( !$booleanSetearConexion )
                {
                    $this->emComercial->getConnection()->close();
                }
            }
            
            $result = "ERROR ".$e->getMessage();
        }
        
        return $result;
    }
    
    /**
     * cambiarUltimaMillaRadioTn
     * 
     * Funcion que se encarga de realizar el cambio de ultima milla Radio de una solicitud
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 05-12-2019 - Se realizan ajustes en el cambio de ultima milla en los ws de networking
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 31-03-2020 - Se realiza la configuración del BGP en el cambio de última milla con diferente PE o anillo
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 01-06-2020 - Se agrega el id del servicio a la url 'cambio_um' y 'migracion_anillo'
     *                           del ws de networking para la validación del BW
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.4 22-01-2021 - Se actualiza el estado de la interface nueva a 'connected' si el estado es diferente a 'connected'.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 15-04-2021 - Se abre la programacion tambien para productos L3MPLS SDWAN
     *
     * @param Array $arrayParametros [ idEmpresa , prefijoEmpresa , idServicio ]
     * @return string $result
     * 
     * @since  01-07-2016
     */
    public function cambiarUltimaMillaRadioTn($arrayParametros)
    {
        $status                       = "ERROR";
        $mensaje                      = "";
        $strValorTipoCambioUm         = "";      
        $objServicio                  = null;
        $objProducto                  = null;
        $objServicioTecnico           = null;
        $objTipoSolicitud             = null;
        $objSolicitud                 = null;
        $objDetSolTipoCambioUM        = null;
        $objDetSolElementoId          = null;
        $objElementoAnterior          = null;
        $objDetSolInterfaceElementoId = null;
        $objInterfaceElementoAnterior = null;
        $objDetSolVlan                = null;
        $objElementoNuevo             = null;
        $objInterfaceElementoNuevo    = null;
        $objSolCaracVlan              = null;
        $objPerEmpRolCarVlan          = null;
        $objDetalleElementoVlan       = null;
        $strVlanAnterior              = "";
        $strVlanNueva                 = "";
        $arrayPeticiones              = array();
                
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
                
        try
        {
            $this->utilServicio->validaObjeto($arrayParametros['idServicio'], "Información incompleta para ejecutar esta operación, favor notificar a Sistemas.");

            $objServicio        = $this->emComercial->getRepository("schemaBundle:InfoServicio")
                                                    ->find($arrayParametros['idServicio']);
            $this->utilServicio->validaObjeto($objServicio, "El servicio no existe, favor notificar a Sistemas.");
            
            $objProducto        = $objServicio->getProductoId();
            $this->utilServicio->validaObjeto($objProducto, "El servicio no tiene registrado un producto, favor notificar a Sistemas.");
            
            $objServicioTecnico = $this->emComercial->getRepository("schemaBundle:InfoServicioTecnico")
                                                    ->findOneBy(array("servicioId" => $objServicio->getId()));
            $this->utilServicio->validaObjeto($objServicioTecnico, "El servicio no tiene registrada información técnica, favor notificar a Sistemas.");
            
            $objTipoSolicitud   = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                    ->findOneBy(array("descripcionSolicitud" => "SOLICITUD CAMBIO ULTIMA MILLA","estado"=>"Activo"));
            $objSolicitud       = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                    ->findOneBy(array("tipoSolicitudId"   => $objTipoSolicitud->getId(), 
                                                                      "servicioId"        => $objServicio->getId(),
                                                                      "estado"            => "Asignada"));
            
            $objDetSolTipoCambioUM = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "TIPO_CAMBIO_ULTIMA_MILLA");
            $this->utilServicio->validaObjeto($objDetSolTipoCambioUM, "El servicio no tiene registrado caracteristica TIPO_CAMBIO_ULTIMA_MILLA,".
                                                                      " favor notificar a Sistemas.");
            $strValorTipoCambioUm  = $objDetSolTipoCambioUM->getValor();
            
            // DATOS ANTERIORES //
            
            // Switch
            $objDetSolElementoId = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "ELEMENTO_ID");
            $this->utilServicio->validaObjeto($objDetSolElementoId, "No existe caracteristica de Elemento Anterior,Favor Notificar a Sistemas!");
            $objElementoAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objDetSolElementoId->getValor());
            $this->utilServicio->validaObjeto($objElementoAnterior, "No existe elemento anterior, favor notificar a Sistemas.");

            //puerto switch anterior
            $objDetSolInterfaceElementoId = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "INTERFACE_ELEMENTO_ID");
            $this->utilServicio->validaObjeto($objDetSolInterfaceElementoId, "No existe caracteristica de interface elemento anterior,".
                                                                             " favor notificar a Sistemas.");
            $objInterfaceElementoAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                 ->find($objDetSolInterfaceElementoId->getValor());
            $this->utilServicio->validaObjeto($objInterfaceElementoAnterior, "No existe interface elemento anterior, favor notificar a Sistemas.");
            
            //vlan anterior
            $objDetSolVlan = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "VLAN");
            $this->utilServicio->validaObjeto($objDetSolVlan, "No existe caracteristica de VLAN anterior, favor notificar a Sistemas.");
            
            $strVlanAnterior = $objDetSolVlan->getValor();
            
            // FIN DATOS ANTERIORES //
            
            // DATOS NUEVOS //
            
            //Switch Nuevo
            $objElementoNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objServicioTecnico->getElementoId());
            $this->utilServicio->validaObjeto($objElementoNuevo, "No existe elemento de backbone nuevo en el servicio, favor notificar a Sistemas.");
            
            //Puerto Switch Nuevo
            $objInterfaceElementoNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                ->find($objServicioTecnico->getInterfaceElementoId());
            $this->utilServicio->validaObjeto($objInterfaceElementoNuevo, "No existe interface de elemento de backbone nuevo en el servicio, ".
                                                                          "favor notificar a Sistemas.");

            //vlan nueva
            $objSolCaracVlan = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "VLAN", $objServicio->getProductoId());
            $this->utilServicio->validaObjeto($objSolCaracVlan, "No existe caracteristica VLAN para el servicio, favor notificar a Sistemas.");
            
            if($objProducto->getNombreTecnico()=="L3MPLS" || $objProducto->getNombreTecnico()=="L3MPLS SDWAN" )
            {  
                $objPerEmpRolCarVlan = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                        ->find($objSolCaracVlan->getValor());
                $this->utilServicio->validaObjeto($objPerEmpRolCarVlan, "No existe relacion entre el cliente y la caracteristica VLAN del servicio, ".
                                                                        "favor notificar a Sistemas.");
                                                        
                $objDetalleElementoVlan = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                ->find($objPerEmpRolCarVlan->getValor());
            }
            else
            {
                $objDetalleElementoVlan = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                ->find($objSolCaracVlan->getValor());
            }
            $this->utilServicio->validaObjeto($objDetalleElementoVlan, "No existe VLAN asociada a algun PE, favor notificar a Sistemas.");
            $strVlanNueva = $objDetalleElementoVlan->getDetalleValor();
            
            // FIN DATOS NUEVOS //
            
            //Capacidades totales de los servicios activos ligados a un puerto
            $arrayCapacidades = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                        ->getResultadoCapacidadesPorInterface($objInterfaceElementoAnterior->getId());
            $this->utilServicio->validaObjeto($arrayCapacidades, "Capacidades del elemento anterior con errores, favor notificar a Sistemas.");
            
            //mac actual del servicio para realizar validacion y no incluirla
            $macServicio        = $arrayParametros['mac'];
            
            $arrayMacServicio[] = $macServicio;
            $arrayMacVlan = array($strVlanAnterior=>$arrayMacServicio);
            $this->utilServicio->validaObjeto($arrayMacVlan, "Información de MAC y VLAN anterior del servicio con errores, favor notificar a Sistemas.");
            
            $this->utilServicio->validaObjeto($arrayParametros['usrCreacion'], "Información incompleta para ejecutar el cambio de um radio, ".
                                                                               "favor notificar a Sistemas.");
            $this->utilServicio->validaObjeto($arrayParametros['ipCreacion'], "Información incompleta para ejecutar el cambio de um radio, ".
                                                                              "favor notificar a Sistemas.");
            
            //arreglo para ejecutar el script de cambio de ultima milla en networking
            $arrayPeticiones['anillo']       = '';
            $arrayPeticiones['user_name']    = $arrayParametros['usrCreacion'];
            $arrayPeticiones['user_ip']      = $arrayParametros['ipCreacion'];
            $arrayPeticiones['servicio']     = $objProducto->getNombreTecnico();
            $arrayPeticiones['id_servicio']  = $objServicio->getId();
            $arrayPeticiones['nombreMetodo'] = 'InfoCambiarPuertoService.cambiarUltimaMillaRadioTn';
            $arrayPeticiones['login_aux']    = $objServicio->getLoginAux();
            $arrayPeticiones['descripcion']  = 'cce_'.$objServicio->getLoginAux().'_rad';

            $arrayPeticiones['sw_anterior']  = $objElementoAnterior->getNombreElemento();
            $arrayPeticiones['pt_anterior']  = $objInterfaceElementoAnterior->getNombreInterfaceElemento();
            $arrayPeticiones['bw_anterior']  = array(
                    'up'   => intval($arrayCapacidades['totalCapacidad1']) - intval($arrayParametros['capacidad1']),
                    'down' => intval($arrayCapacidades['totalCapacidad2']) - intval($arrayParametros['capacidad2'])
                );
            $arrayPeticiones['cpe_anterior'] = array();

            //Capacidades totales de los servicios activos ligados al puerto nuevo
            $arrayCapacidadesNueva           = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                        ->getResultadoCapacidadesPorInterface($objInterfaceElementoNuevo->getId());
            $this->utilServicio->validaObjeto($arrayCapacidadesNueva, "Capacidades del elemento nuevo con errores, favor notificar a Sistemas.");
            
            $arrayMacVlanNueva               = array($strVlanNueva=>$arrayMacServicio);
            $this->utilServicio->validaObjeto($arrayMacVlanNueva, "Información de MAC y VLAN nueva del servicio con errores, ".
                                                                  "favor notificar a Sistemas.");
            $arrayMacVlanNuevoRad = $this->servicioGeneral->getArrayMacVlansAConfigurar( $arrayMacVlanNueva,
                                                                                         $strVlanNueva,
                                                                                         $arrayParametros['macRadio'],
                                                                                         "+" );
            $this->utilServicio->validaObjeto($arrayMacVlanNuevoRad, "Información de MAC de radio de cliente y VLAN nueva del ".
                                                                     "servicio con errores, favor notificar a Sistemas.");
            foreach( $arrayMacVlanNuevoRad as $strValueVlanCPE => $arrayValueMacCPE )
            {
                if( isset($arrayMacVlanNueva[$strValueVlanCPE]) && !empty($arrayMacVlanNueva[$strValueVlanCPE]) )
                {
                    foreach( $arrayValueMacCPE as $strValueMacCPE )
                    {
                        if( !in_array($strValueMacCPE, $arrayMacVlanNueva[$strValueVlanCPE]) )
                        {
                            $arrayMacVlanNueva[$strValueVlanCPE][] = $strValueMacCPE;
                        }
                    }
                }
                else
                {
                    $arrayMacVlanNueva[$strValueVlanCPE] = $arrayValueMacCPE;
                }
            }

            $arrayPeticiones['sw_nuevo']     = $objElementoNuevo->getNombreElemento();
            $arrayPeticiones['pt_nuevo']     = $objInterfaceElementoNuevo->getNombreInterfaceElemento();
            $arrayPeticiones['bw_nueva']     = array(
                    'up'   => intval($arrayCapacidadesNueva['totalCapacidad1']) + intval($arrayParametros['capacidad1']),
                    'down' => intval($arrayCapacidadesNueva['totalCapacidad2']) + intval($arrayParametros['capacidad2'])
                );
            $arrayPeticiones['cpe_nuevo']    = array();
            foreach( $arrayMacVlanNueva as $strValueVlanCPE => $arrayValueMacCPE )
            {
                foreach( $arrayValueMacCPE as $strValueMacCPE )
                {
                    $arrayPeticiones['cpe_nuevo'][] = array(
                                'vlan' => $strValueVlanCPE,
                                'mac'  => $strValueMacCPE,
                            );
                }
            }
            
            if( $strValorTipoCambioUm == "DIFERENTE_PE" || $strValorTipoCambioUm == "MISMO_PE_DIFERENTE_ANILLO" )
            {
                $arrayPeticiones['url']                  = 'migracion_anillo';
                $arrayPeticiones['accion']               = 'migracion_anillo';
                $arrayPeticiones['servicio']             = $objProducto->getNombreTecnico();
                
                $arrayPeticiones['login']                = $objServicio->getPuntoId()->getLogin();
                $arrayPeticiones['razon_social']         = "";
                $arrayPeticiones['vrf']                  = "";
                $arrayPeticiones['rutas_anteriores']     = array();
                $arrayPeticiones['rutas_nuevas']         = array();
                $arrayPeticiones['interfaz_pe_anterior'] = "";
                $arrayPeticiones['interfaz_pe_nueva']    = "";
                $arrayPeticiones['protocolo']            = "STANDARD";
                $arrayPeticiones['ip_bgp_anterior']      = "";
                $arrayPeticiones['ip_bgp_nueva']         = "";
                
                //verifico los datos del cpe anterior
                $objSolCaracVlan         = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "VLAN");
                if( $objProducto->getNombreTecnico() == "L3MPLS" || $objProducto->getNombreTecnico() == "L3MPLS SDWAN")
                {
                    $objPerEmpRolCarVlan = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                        ->find($objSolCaracVlan->getValor());
                    if( is_object($objPerEmpRolCarVlan) )
                    {
                        $objDetalleElementoVlanAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                            ->find($objPerEmpRolCarVlan->getValor());
                        if( is_object($objDetalleElementoVlanAnterior) )
                        {
                            $strVlanAnterior = $objDetalleElementoVlanAnterior->getDetalleValor();
                            $arrayMacVlan    = array($strVlanAnterior => $arrayMacServicio);
                        }
                    }
                }
                else
                {
                    $objDetalleElementoVlanAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                        ->find($objSolCaracVlan->getValor());
                    if( is_object($objDetalleElementoVlanAnterior) )
                    {
                        $strVlanAnterior = $objDetalleElementoVlanAnterior->getDetalleValor();
                        $arrayMacVlan    = array($strVlanAnterior => $arrayMacServicio);
                    }
                }
                
                //obtengo los datos de la mac de la radio anterior
                $arrayMacVlanNuevoRadAnterior    = $this->servicioGeneral
                                                     ->getArrayMacVlansAConfigurar($arrayMacVlan,
                                                                                   $strVlanAnterior,
                                                                                   $arrayParametros['macRadio'],
                                                                                   "+");
                $this->utilServicio->validaObjeto($arrayMacVlanNuevoRadAnterior, "Información de MAC de radio de cliente y VLAN , ".
                                                                                 "del servicio con errores favor notificar a Sistemas.");
                //ingreso los datos del cpe anterior
                foreach( $arrayMacVlanNuevoRadAnterior as $strValueVlanCPE => $arrayValueMacCPE )
                {
                    if( isset($arrayMacVlan[$strValueVlanCPE]) && !empty($arrayMacVlan[$strValueVlanCPE]) )
                    {
                        foreach( $arrayValueMacCPE as $strValueMacCPE )
                        {
                            if( !in_array($strValueMacCPE, $arrayMacVlan[$strValueVlanCPE]) )
                            {
                                $arrayMacVlan[$strValueVlanCPE][] = $strValueMacCPE;
                            }
                        }
                    }
                    else
                    {
                        $arrayMacVlan[$strValueVlanCPE] = $arrayValueMacCPE;
                    }
                }
                foreach( $arrayMacVlan as $strValueVlanCPE => $arrayValueMacCPE )
                {
                    foreach( $arrayValueMacCPE as $strValueMacCPE )
                    {
                        $arrayPeticiones['cpe_anterior'][] = array(
                                    'vlan' => $strValueVlanCPE,
                                    'mac'  => $strValueMacCPE,
                                );
                    }
                }
                
                if( $objProducto->getNombreTecnico() != "INTERNET" )
                {
                    //obtener el nombre de la vrf
                    $objSolCaracVrf         = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "VRF");
                    $this->utilServicio->validaObjeto($objSolCaracVrf, "No se encontraron datos de la Vrf, favor notificar a Sistemas.");
                    $objPerEmpRolCarVrf     = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                    ->find($objSolCaracVrf->getValor());
                    $this->utilServicio->validaObjeto($objPerEmpRolCarVrf, "No se encontraron datos de la Vrf, favor notificar a Sistemas.");
                    
                    //obtener el rd de la vrf
                    $strRdVrf               = "";
                    $objCaractRdId          = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array("descripcionCaracteristica" => "RD_ID", "estado" => "Activo"));
                    if( $objProducto->getNombreTecnico() == "L3MPLS" || $objProducto->getNombreTecnico() == "L3MPLS SDWAN")
                    {
                        $objPerEmpRolCarRdId    = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                ->findOneBy(array("personaEmpresaRolCaracId" => $objPerEmpRolCarVrf->getPersonaEmpresaRolCaracId(),
                                                                  "caracteristicaId"         => $objCaractRdId->getId(),
                                                                  "estado"                   => "Activo"));
                        $this->utilServicio->validaObjeto($objPerEmpRolCarRdId, "No se encontraron datos del Rd-Id, favor notificar a Sistemas.");
                        $strRdVrf               = $objPerEmpRolCarRdId->getValor();
                    }
                    
                    //obtengo el valor de la VRF
                    $strValorVrf            = $objPerEmpRolCarVrf->getValor();
                    
                    //obtengo el parametro para la reemplazar la VRF si es necesario
                    $objParametroCabVrf     = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                                        array(  'nombreParametro'   => 'VALORES_VRF_TELCONET',
                                                                                'estado'            => 'Activo'));
                    if( is_object($objParametroCabVrf) )
                    {
                        $objParametroDetVrf = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy(
                                                                        array(  "parametroId"   => $objParametroCabVrf->getId(),
                                                                                "valor1"        => $strValorVrf,
                                                                                "estado"        => "Activo"));
                        //si existe el parametro reemplazo el valor de la VRF
                        if( is_object($objParametroDetVrf) )
                        {
                            $strValorVrf    = $objParametroDetVrf->getValor2();
                        }
                    }
                    
                    //ingreso el name y el rd en la llave 'vrf' de la variable arrayPeticiones
                    $arrayPeticiones['vrf'] = array(
                                                'name'      => $strValorVrf,
                                                'rd'        => $strRdVrf,
                                                'rt_export' => '',
                                                'rt_import' => ''
                                              );

                    //obtener la razon social
                    $strRazonSocial     = "";
                    $objInfoPersona     = $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
                    if( is_object($objInfoPersona) )
                    {
                        $strRazonSocial = $objInfoPersona->getRazonSocial();
                    }
                    if( !empty($strRazonSocial) )
                    {
                        $arrayRazonesSociales = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                                  ->getOne('PROYECTO MONITOREO CLIENTES GRUPO BRAVCO',
                                                                           'INFRAESTRUCTURA','ACTIVAR SERVICIO',
                                                                           'RAZON SOCIAL GRUPO BRAVCO',
                                                                           $strRazonSocial,
                                                                           '',
                                                                           '',
                                                                           '',
                                                                           '',
                                                                           '');
                        if( isset($arrayRazonesSociales["valor1"]) && !empty($arrayRazonesSociales["valor1"]) )
                        {
                            $arrayPeticiones['razon_social']       = $arrayRazonesSociales["valor4"];
                            //ingreso el rt_export y el rt_import en la llave 'vrf' de la variable arrayPeticiones
                            $arrayPeticiones['vrf']['rt_export']   = $arrayRazonesSociales["valor2"];
                            $arrayPeticiones['vrf']['rt_import']   = $arrayRazonesSociales["valor3"];
                        }
                        else
                        {
                            $strRazonSocial = "";
                        }
                    }
                    $arrayPeticiones['razon_social'] = $strRazonSocial;
                    
                    //arreglo de todas las rutas anteriores del elemento
                    $arrayRutas     = $this->emInfraestructura->getRepository("schemaBundle:InfoRutaElemento")
                                                        ->findBy(array( "servicioId"    => $objServicio->getId(),
                                                                        "estado"        => "Activo"));
                    foreach($arrayRutas as $objRuta)
                    {
                        //cambio el estado de la ruta a Eliminado
                        $objRuta->setEstado("Eliminado");
                        $this->emInfraestructura->persist($objRuta);
                        $this->emInfraestructura->flush();
                    }
                }

                if( $objProducto->getNombreTecnico() == "L3MPLS"  || $objProducto->getNombreTecnico() == "L3MPLS SDWAN")
                {
                    //obtengo los datos del PE anterior
                    $arrayDesconfig     = array(
                                            'objServicio'           => $objServicio,
                                            'objDetalleSolicitud'   => $objSolicitud,
                                        );
                    $arrayPeticiones['interfaz_pe_anterior'] = $this->getDatosPePorSolicitud($arrayDesconfig);
                    
                    //obtengo los datos del PE nuevo
                    $arrayConfigurar    = array(
                                            'objServicio' => $objServicio
                                        );
                    $arrayPeticiones['interfaz_pe_nueva']    = $this->getDatosPePorServicio($arrayConfigurar);
                }

                //obtengo el protocolo de enrutamiento
                $objServCaractProtocolo = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                    "PROTOCOLO_ENRUTAMIENTO",
                                                                                                    $objProducto);
                if(is_object($objServCaractProtocolo))
                {
                    //agrego el valor del protocolo de enrutamiento
                    $arrayPeticiones['protocolo']    = $objServCaractProtocolo->getValor();
                    //verifico que el protocolo de enrutamiento sea BGP para agregar las Ips
                    if( $arrayPeticiones['protocolo'] == 'BGP' )
                    {
                        $objDetSolIpId  = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "IP_ID");
                        $this->utilServicio->validaObjeto($objDetSolIpId,
                                                        "El servicio no tiene ingresada la Ip Anterior en la característica de la solicitud, ".
                                                        "favor notificar a Sistemas.");
                        $objIpAnterior  = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')->find($objDetSolIpId->getValor());
                        if(is_object($objIpAnterior) )
                        {
                            //agrego la ip del BGP anterior
                            $arrayPeticiones['ip_bgp_anterior'] = $objIpAnterior->getIp();
                        }
                        $objIpNueva     = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                         ->findOneBy(array("servicioId"    => $objServicio->getId(),
                                                                           "estado"        => "Activo"));
                        if(is_object($objIpNueva) )
                        {
                            //agrego la ip del BGP nueva
                            $arrayPeticiones['ip_bgp_nueva']    = $objIpNueva->getIp();
                        }
                    }
                }
            }
            else
            {
                //obtengo los datos de la mac de la radio anterior
                $arrayMacVlanNuevoRadAnterior    = $this->servicioGeneral
                                                     ->getArrayMacVlansAConfigurar($arrayMacVlan,
                                                                                   $strVlanAnterior,
                                                                                   $arrayParametros['macRadio'],
                                                                                   "+");
                $this->utilServicio->validaObjeto($arrayMacVlanNuevoRadAnterior, "Información de MAC de radio de cliente y VLAN , ".
                                                                                 "del servicio con errores favor notificar a Sistemas.");
                //ingreso los datos del cpe anterior
                foreach( $arrayMacVlanNuevoRadAnterior as $strValueVlanCPE => $arrayValueMacCPE )
                {
                    if( isset($arrayMacVlan[$strValueVlanCPE]) && !empty($arrayMacVlan[$strValueVlanCPE]) )
                    {
                        foreach( $arrayValueMacCPE as $strValueMacCPE )
                        {
                            if( !in_array($strValueMacCPE, $arrayMacVlan[$strValueVlanCPE]) )
                            {
                                $arrayMacVlan[$strValueVlanCPE][] = $strValueMacCPE;
                            }
                        }
                    }
                    else
                    {
                        $arrayMacVlan[$strValueVlanCPE] = $arrayValueMacCPE;
                    }
                }
                foreach( $arrayMacVlan as $strValueVlanCPE => $arrayValueMacCPE )
                {
                    foreach( $arrayValueMacCPE as $strValueMacCPE )
                    {
                        $arrayPeticiones['cpe_anterior'][] = array(
                                    'vlan' => $strValueVlanCPE,
                                    'mac'  => $strValueMacCPE,
                                );
                    }
                }
                
                $arrayPeticiones['url']          = 'cambio_um';
                $arrayPeticiones['accion']       = 'cambio_um';
            }
            
            //Ejecucion del metodo via WS para realizar la configuracion del SW
            $arrayRespuesta = $this->networkingScripts->callNetworkingWebService($arrayPeticiones);

            $status  = $arrayRespuesta['status'];
            $mensaje = $arrayRespuesta['mensaje'];
                        
            //Si ejecuta script de valida con respuesta de WS para continuar, si no requiere script
            //Solo ejecuta cambio ultima milla a nivel logico
            if ($status == "OK") 
            {
                //verificar si el estado de la interface nueva es diferente de 'connected'
                if($objInterfaceElementoNuevo->getEstado() != 'connected')
                {
                    //se actualiza el estado de la interface
                    $objInterfaceElementoNuevo->setEstado('connected');
                    $this->emInfraestructura->persist($objInterfaceElementoNuevo);
                    $this->emInfraestructura->flush();
                }
                //historial del servicio
                $servicioHistorial = new InfoServicioHistorial();
                $servicioHistorial->setServicioId($objServicio);
                $servicioHistorial->setObservacion("Se realizó el Cambio de Ultima Milla con éxito");
                $servicioHistorial->setEstado($objServicio->getEstado());
                $servicioHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
                $servicioHistorial->setFeCreacion(new \DateTime('now'));
                $servicioHistorial->setIpCreacion($arrayParametros['ipCreacion']);
                $this->emComercial->persist($servicioHistorial);
                $this->emComercial->flush(); 
                
                $objSolicitud->setEstado("Finalizada");
                $this->emComercial->persist($objSolicitud);
                $this->emComercial->flush();

                //agregar historial a la solicitud
                $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
                $objDetalleSolicitudHistorial->setDetalleSolicitudId($objSolicitud);
                $objDetalleSolicitudHistorial->setIpCreacion($arrayParametros['ipCreacion']);
                $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitudHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
                $objDetalleSolicitudHistorial->setEstado("Finalizada");
                $this->emComercial->persist($objDetalleSolicitudHistorial);
                $this->emComercial->flush();
                //actualizar las solicitudes caract
                $arrayDetalleSolicitudCarac = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                               ->findBy(array("detalleSolicitudId"  => $objSolicitud->getId(), 
                                                              "estado"              => "Asignada"));
                foreach($arrayDetalleSolicitudCarac as $objDetalleSolCarac)
                {
                    $objDetalleSolCarac->setEstado("Finalizada");
                    $this->emComercial->persist($objDetalleSolCarac);
                    $this->emComercial->flush();
                }
                
                $result = "OK";
            }
            else
            {
                throw new \Exception($mensaje);
            }
        } 
        catch (\Exception $e) 
        {
            if ($this->emInfraestructura->getConnection()->isTransactionActive()){
                $this->emInfraestructura->getConnection()->rollback();
            }
            
            if ($this->emComercial->getConnection()->isTransactionActive()){
                $this->emComercial->getConnection()->rollback();
            }
                        
            $result = "ERROR : ".$e->getMessage();                                    
        }
        
        //*DECLARACION DE COMMITS*/
        if ($this->emInfraestructura->getConnection()->isTransactionActive()) 
        {
            $this->emInfraestructura->getConnection()->commit();
        }

        if ($this->emComercial->getConnection()->isTransactionActive()) 
        {
            $this->emComercial->getConnection()->commit();
        }

        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        
        return $result;
    }
    
    /**
     * Funcion que sirve para configurar al cliente en el PE
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 01-07-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 27-07-2016 - Se modifica arreglo para configurar PE para que reciba correctamente el SW nuevo a configurar
     * 
     * @author $arrayParametros [objProducto, objServicio, objElemento, objDetalleSolicitud]
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.2 26-10-2016  Se cambio la forma de obtener el as privado
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 28-01-2017  Se cambia forma de obtener la vlan de acuerdo a si es pseudope o no
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 17-07-2018 - Se enviá nuevo parámetro al método getPeBySwitch
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 19-06-2019 - Se agregan parámetros de razon_social y route_target export e import en el método configPE, con el objetivo de
     *                           enviar a configurar una lineas adicionales que permitan al cliente el monitoreo sus enlaces de datos
     *
     */
    public function configurarPePorServicio($arrayParametros)
    {
        $objServicio     = $arrayParametros['objServicio'];
        $objElemento     = $arrayParametros['objElemento'];
        $objProducto     = $arrayParametros['objProducto'];

        $strBanderaLineasBravco = "N";
        $strRouteTargetExport   = "";
        $strRouteTargetImport   = "";
        $strRazonSocial         = "";
        $boolEsPseudoPe         = false;
        $arrayParametrosWs      = array();
        //Boleano que me trae si el servicio a desconfigurar es pseudoPe o no
        if(isset($arrayParametros['boolEsPseudoPe']))
        {
            $boolEsPseudoPe = $arrayParametros['boolEsPseudoPe'];
        }
        
        $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                ->findOneBy(array("servicioId" => $objServicio->getId()));
        
        $objIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                         ->findOneBy(array("servicioId"    => $objServicio->getId(),
                                                           "estado"        => "Activo"));
            
        //subred
        $objSubred = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                        ->find($objIp->getSubredId());
        
        //obtener el anillo del elemento 
        $objDetalleElementoAnillo = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                            ->findOneBy(array(  "elementoId"    => $objElemento->getId(),
                                                                "detalleNombre" => "ANILLO",
                                                                "estado"        => "Activo"));
        //------------------------------------------------------------------
        $arrayParametrosWs["intIdElemento"] = $objElemento->getId();
        $arrayParametrosWs["intIdServicio"] = $objServicio->getId();

        //obtener el elemento padre del elemento anterior
        $objElementoPadre = $this->servicioGeneral->getPeBySwitch($arrayParametrosWs);

        if(is_object($objElementoPadre))
        {
            $nombreElementoPadre = $objElementoPadre->getNombreElemento();
        }
        else
        {
            throw new \Exception("Mensaje:".$objElementoPadre);
        }
        //------------------------------------------------------------------
        
        $strVlan = '';
        
        if(!$boolEsPseudoPe)
        {
            //obtener la vlan 
            $objSolCaracVlan = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "VLAN", $objServicio->getProductoId());

            $objPerEmpRolCarVlan = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                     ->find($objSolCaracVlan->getValor());

            $objDetalleElementoVlan = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                           ->find($objPerEmpRolCarVlan->getValor());
            
            if(is_object($objDetalleElementoVlan))
            {
                $strVlan = $objDetalleElementoVlan->getDetalleValor();
            }
        }
        else
        {
            $objServProdCaractVlanPseudoPe   = $this->servicioGeneral
                                                    ->getServicioProductoCaracteristica($objServicio,
                                                                                        'VLAN_PROVEEDOR',
                                                                                        $objServicio->getProductoId());
            if(is_object($objServProdCaractVlanPseudoPe))
            {
                $strVlan = $objServProdCaractVlanPseudoPe->getValor();
            }
        }
        
        //------------------------------------------------------------------

        //obtener la vrf 
        $objSolCaracVrf = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "VRF", $objServicio->getProductoId());

        $objPerEmpRolCarVrf = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                            ->find($objSolCaracVrf->getValor());
        //------------------------------------------------------------------

        //obtener el protocolo 
        $objSolCaracProtocolo = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "PROTOCOLO_ENRUTAMIENTO", 
                                                            $objServicio->getProductoId());
        //------------------------------------------------------------------

        //obtener el rd_id 
        $objCaractRdId = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array("descripcionCaracteristica" => "RD_ID", 
                                                              "estado" => "Activo"));

        $objPerEmpRolCarRdId = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                 ->findOneBy(array("personaEmpresaRolCaracId" => $objPerEmpRolCarVrf->getPersonaEmpresaRolCaracId(),
                                                                   "caracteristicaId"         => $objCaractRdId->getId(),
                                                                   "estado"                   => "Activo"));
        //------------------------------------------------------------------

        //obtener el default gateway 
        $objSolCaracDefaultGw = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "DEFAULT_GATEWAY", 
                                                            $objServicio->getProductoId());
        //------------------------------------------------------------------

        //obtener el as privado
        if($objPerEmpRolCarVrf)
        {
            $objSolCaracAsPrivado = $this->emComercial
                                         ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                         ->getOneByCaracteristica($objPerEmpRolCarVrf->getPersonaEmpresaRolId(),"AS_PRIVADO");
        }
        //------------------------------------------------------------------

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

        //CONFIGURAR NUEVOS DATOS EN EL PE
        $arrayPeticiones = array();
        //accion a ejecutar
        $arrayPeticiones['url']                   = 'configPE';
        $arrayPeticiones['accion']                = 'Activar';        
        $arrayPeticiones['sw']                    = $objElemento->getNombreElemento();
        $arrayPeticiones['clase_servicio']        = $objProducto->getNombreTecnico();
        $arrayPeticiones['vrf']                   = $objPerEmpRolCarVrf->getValor();
        $arrayPeticiones['pe']                    = $nombreElementoPadre;
        $arrayPeticiones['anillo']                = $objDetalleElementoAnillo->getDetalleValor();
        $arrayPeticiones['vlan']                  = $strVlan;
        $arrayPeticiones['subred']                = $objSubred->getSubred();
        $arrayPeticiones['mascara']               = $objSubred->getMascara();
        $arrayPeticiones['gateway']               = $objSubred->getGateway();
        $arrayPeticiones['rd_id']                 = $objPerEmpRolCarRdId->getValor();
        $arrayPeticiones['descripcion_interface'] = $objServicio->getLoginAux();
        $arrayPeticiones['ip_bgp']                = $objIp->getIp();
        $arrayPeticiones['asprivado']             = ($objSolCaracAsPrivado) ? $objSolCaracAsPrivado->getValor(): "";
        $arrayPeticiones['nombre_sesion_bgp']     = $objServicio->getLoginAux();
        $arrayPeticiones['default_gw']            = ($objSolCaracDefaultGw) ? $objSolCaracDefaultGw->getValor(): "NO";
        $arrayPeticiones['protocolo']             = $objSolCaracProtocolo->getValor();
        $arrayPeticiones['servicio']              = $objProducto->getNombreTecnico();
        $arrayPeticiones['login_aux']             = $objServicio->getLoginAux();
        $arrayPeticiones['tipo_enlace']           = $objServicioTecnico->getTipoEnlace();
        $arrayPeticiones['banderaBravco']         = 'NO';
        $arrayPeticiones['weight']                = null;

        $arrayPeticiones['user_name']             = $arrayParametros['usrCreacion'];
        $arrayPeticiones['user_ip']               = $arrayParametros['ipCreacion'];

        if($strBanderaLineasBravco === "S")
        {
            $arrayPeticiones['razon_social'] = $strRazonSocial;
            $arrayPeticiones['rt_export']    = $strRouteTargetExport;
            $arrayPeticiones['rt_import']    = $strRouteTargetImport;
        }

        //Ejecucion del metodo via WS para realizar la configuracion en el Pe
        $arrayRespuesta = $this->networkingScripts->callNetworkingWebService($arrayPeticiones);
        
        $status     = $arrayRespuesta['status'];
        $mensaje    = $arrayRespuesta['mensaje'];
        $statusCode = $arrayRespuesta['statusCode'];
            
        $respuestaArray = array('status' => $status, 'mensaje' => $mensaje , 'statusCode' => $statusCode);
        return $respuestaArray;
    }
    
    /**
     * Funcion que sirve para desconfigurar al cliente en el PE
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 01-07-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 28-01-2017 Se ajusta para que obtenga la vlan de acuerdo a si es pseudope o no
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 17-07-2018 - Se enviá nuevo parámetro al método getPeBySwitch
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 19-06-2019 - Se agregan parámetros de razon_social y route_target export e import en el método configPE, con el objetivo de
     *                           enviar a configurar una lineas adicionales que permitan al cliente el monitoreo sus enlaces de datos
     *
     * @author $arrayParametros [objProducto, objServicio, objElemento, objDetalleSolicitud]
     */
    public function desconfigurarPePorSolicitud($arrayParametros)
    {
        $objServicio  = $arrayParametros['objServicio'];
        $objElemento  = $arrayParametros['objElemento'];
        $objProducto  = $arrayParametros['objProducto'];
        $objSolicitud = $arrayParametros['objDetalleSolicitud'];

        $strBanderaLineasBravco = "N";
        $strRouteTargetExport   = "";
        $strRouteTargetImport   = "";
        $strRazonSocial         = "";
        $boolEsPseudoPe         = false;
        $arrayParametrosWs      = array();
        //Boleano que me trae si el servicio a desconfigurar es pseudoPe o no
        if(isset($arrayParametros['boolEsPseudoPe']))
        {
            $boolEsPseudoPe = $arrayParametros['boolEsPseudoPe'];
        }
        
        $status       = "ERROR";
        $mensaje      = "ERROR";
        $statusCode   = 500;
        
        $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                ->findOneBy(array("servicioId" => $objServicio->getId()));
        
        //se buscan cuantas ips tiene la subred
        $objDetSolIpId = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "IP_ID");
        
        $objIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')->find($objDetSolIpId->getValor());
        
        $arrayIpsSubred = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                            ->findBy(array( 'subredId'  => $objIp->getSubredId(),
                                                            'estado'    => 'Activo'));
        $numIpsSubred = count($arrayIpsSubred);
        
        //ROUTER - cancelar en el pe
        if($numIpsSubred>0 && $numIpsSubred<2)
        {
            //obtener datos de la subred
            $objSubredAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                        ->find($objIp->getSubredId());
            //------------------------------------------------------------------

            //obtener el anillo del elemento 
            $objDetalleElementoAnilloAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                ->findOneBy(array(  "elementoId"    => $objElemento->getId(),
                                                                    "detalleNombre" => "ANILLO",
                                                                    "estado"        => "Activo"));
            //------------------------------------------------------------------

            $arrayParametrosWs["intIdElemento"] = $objElemento->getId();
            $arrayParametrosWs["intIdServicio"] = $objServicio->getId();

            //obtener el elemento padre del elemento anterior
            $objElementoPadre = $this->servicioGeneral->getPeBySwitch($arrayParametrosWs);

            if(is_object($objElementoPadre))
            {
                $nombreElementoPadre = $objElementoPadre->getNombreElemento();
            }
            else
            {
                throw new \Exception("Mensaje:".$objElementoPadre);
            }
            
            $strVlanAnterior = '';
            //------------------------------------------------------------------
            if(!$boolEsPseudoPe)
            {
                //obtener la vlan 
                $objSolCaracVlan = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "VLAN");

                $objPerEmpRolCarVlan = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                    ->find($objSolCaracVlan->getValor());

                $objDetalleElementoVlanAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                    ->find($objPerEmpRolCarVlan->getValor());
                
                if(is_object($objDetalleElementoVlanAnterior))
                {
                    $strVlanAnterior = $objDetalleElementoVlanAnterior->getDetalleValor();
                }
            }
            else
            {
                $objServProdCaractVlanPseudoPe   = $this->servicioGeneral
                                                        ->getServicioProductoCaracteristica($objServicio,
                                                                                            'VLAN_PROVEEDOR',
                                                                                            $objServicio->getProductoId());
                if(is_object($objServProdCaractVlanPseudoPe))
                {
                    $strVlanAnterior = $objServProdCaractVlanPseudoPe->getValor();
                }
            }
            
            //------------------------------------------------------------------

            //obtener la vrf 
            $objSolCaracVrf = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "VRF");

            $objPerEmpRolCarVrf = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                ->find($objSolCaracVrf->getValor());
            //------------------------------------------------------------------

            //obtener el protocolo 
            $objSolCaracProtocolo = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "PROTOCOLO_ENRUTAMIENTO");
            //------------------------------------------------------------------

            //obtener el rd_id 
            $objCaractRdId = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array("descripcionCaracteristica" => "RD_ID", 
                                                                  "estado" => "Activo"));

            $objPerEmpRolCarRdId = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                        ->findOneBy(array("personaEmpresaRolCaracId" => $objPerEmpRolCarVrf->getPersonaEmpresaRolCaracId(),
                                                          "caracteristicaId"         => $objCaractRdId->getId(),
                                                          "estado"                   => "Activo"));
            //------------------------------------------------------------------

            //obtener el default gateway 
            $objSolCaracDefaultGw = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "DEFAULT_GATEWAY");
            //------------------------------------------------------------------
            
            //obtener el as privado
            $objSolCaracAsPrivado = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "AS_PRIVADO");
            //------------------------------------------------------------------

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

            $arrayPeticiones = array();
            //accion a ejecutar
            $arrayPeticiones['url']                   = 'configPE';
            $arrayPeticiones['accion']                = 'Cancelar';        
            $arrayPeticiones['sw']                    = $objElemento->getNombreElemento();
            $arrayPeticiones['clase_servicio']        = $objProducto->getNombreTecnico();
            $arrayPeticiones['vrf']                   = $objPerEmpRolCarVrf->getValor();
            $arrayPeticiones['pe']                    = $nombreElementoPadre;
            $arrayPeticiones['anillo']                = $objDetalleElementoAnilloAnterior->getDetalleValor();
            $arrayPeticiones['vlan']                  = $strVlanAnterior;
            $arrayPeticiones['subred']                = $objSubredAnterior->getSubred();
            $arrayPeticiones['mascara']               = $objSubredAnterior->getMascara();
            $arrayPeticiones['gateway']               = $objSubredAnterior->getGateway();
            $arrayPeticiones['rd_id']                 = $objPerEmpRolCarRdId->getValor();
            $arrayPeticiones['descripcion_interface'] = $objServicio->getLoginAux();
            $arrayPeticiones['ip_bgp']                = $objIp->getIp();
            $arrayPeticiones['asprivado']             = ($objSolCaracAsPrivado) ? $objSolCaracAsPrivado->getValor(): "";
            $arrayPeticiones['nombre_sesion_bgp']     = $objServicio->getLoginAux();
            $arrayPeticiones['default_gw']            = ($objSolCaracDefaultGw) ? $objSolCaracDefaultGw->getValor(): "NO";
            $arrayPeticiones['protocolo']             = $objSolCaracProtocolo->getValor();
            $arrayPeticiones['servicio']              = $objProducto->getNombreTecnico();
            $arrayPeticiones['login_aux']             = $objServicio->getLoginAux();
            $arrayPeticiones['tipo_enlace']           = $objServicioTecnico->getTipoEnlace();
            $arrayPeticiones['banderaBravco']         = 'NO';
            $arrayPeticiones['weight']                = null;

            $arrayPeticiones['user_name']             = $arrayParametros['usrCreacion'];
            $arrayPeticiones['user_ip']               = $arrayParametros['ipCreacion'];

            if($strBanderaLineasBravco === "S")
            {
                $arrayPeticiones['razon_social'] = $strRazonSocial;
                $arrayPeticiones['rt_export']    = $strRouteTargetExport;
                $arrayPeticiones['rt_import']    = $strRouteTargetImport;
            }

            //ROUTER - Ejecucion del metodo via WS para realizar la configuracion en el Pe
            $arrayRespuesta = $this->networkingScripts->callNetworkingWebService($arrayPeticiones);

            $status     = $arrayRespuesta['status'];
            $mensaje    = $arrayRespuesta['mensaje'];
            $statusCode = $arrayRespuesta['statusCode'];
        }
        else
        {
            $status     = "OK";
            $mensaje    = "OK";
            $statusCode = 200;
        }
        
        $respuestaArray = array('status' => $status, 'mensaje' => $mensaje , 'statusCode' => $statusCode);
        return $respuestaArray;
    }
    
    /**
     * Funcion que sirve para ejecutar scripts para la migracion a anillo
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 10-05-2016
     *
     * @author Eduardo Plua <eplua@telconet.ec>
     * @version 1.1 26-05-2016 - Se recupera elementoPe desde ws networking
     *    
     * @author Juan Lafuente <jlafuente@telconet.ec>     
     * @version 1.2 07-07-2016 - Se mejora la funcion para soportar INTMPLS y L3MPLS 
     *
     * @author Duval Medina C <dmedina@telconet.ec>
     * @version 1.3 2016-08-05 - Se valida la existencia de la IP asociada a la Solicitud y
     *                            si el IdSubRed  de la IP está en NULL no eliminar Rutas
     *                            elimnación de idServicio y idProducto de enrutamientoEstaticoPe, por no uso
     * @version 1.4 15-08-2016 - Se mejora la funcion para soportar INTMPLS y L3MPLS      
     * 
     * @author Allan Suarez C <arsuarez@telconet.ec>
     * @version 1.5 15-03-2017 - Se cambia ejecucion de configMAC enviando accion requerida correcta para estas opciones migracion-add/migracion-del
     *                         - Se elimina correctamente y se agrega las rutas estaticas en nuevo pe
     * 
     * @author Allan Suarez C <arsuarez@telconet.ec>
     * @version 1.6 05-05-2017 - Se cambia el orden de las ejecuciones realizadas con el WS de NW para el proceso, el orden establecido es:
     *                           1. Eliminar configuracion de MAC Anterior en SW
     *                           2. Eliminar Interface del PE Anterior
     *                           3. Eliminar Ruta del PE Anterior
     *                           4. Configuracion de MAC nueva en SW
     *                           5. Configuracion Interface del PE Nuevo
     *                           6. Configuracion de Rutas en PE Nuevo
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.7 11-10-2017 - Se regularizan cambios realizados en caliente,se agrega validacion en la linea 6140
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.8 09-05-2018 - Se realizan ajustes para obtener el PE anterior , en servicios Internet MPLS
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.9 17-07-2018 - Se enviá nuevo parámetro al método getPeBySwitch
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.0 19-06-2019 - Se agregan parámetros de razon_social y route_target export e import en el método configPE, con el objetivo de
     *                           enviar a configurar una lineas adicionales que permitan al cliente el monitoreo sus enlaces de datos
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.1 28-08-2019 - Se agrega el parametro strMigracionVlan con el objetivo de reutilizar esta funcionalidad en la herramienta
     *                           individual de migración de vlan.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.2 22-03-2021 Se abre la programacion para servicios Internet SDWAN
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6 15-04-2021 - Se abre la programacion tambien para productos L3MPLS SDWAN
     * 
     * @author Bryan Perez <baperezm@telconet.ec>
     * @version 1.7 14-09-2022 - Se envia parámetro servicio NETVOICE-L3MPLS para servicio con descripcionpresentafactura CANAL TELEFONIA
     *
     */
    public function ejecutaMigracionAnillo($arrayParametros)
    {
        $arrayParametrosWs      = array();
        $strBanderaLineasBravco = "N";
        $strRouteTargetExport   = "";
        $strRouteTargetImport   = "";
        $strRazonSocial         = "";
        $strNombreSolicitud     = "SOLICITUD MIGRACION ANILLO";
        $boolMigracionVlan      = false;

        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        try
        {
            if($arrayParametros['tipoEnlace'] == null)
            {
                throw new \Exception("No existe Tipo de Enlace, Favor Revisar!");
            }
            // =====================================================================================================
            // RECUPERACION DE INFORMACION PARA LA MIGRACION 
            // ...
            $objServicio        = $this->emComercial->getRepository("schemaBundle:InfoServicio")
                                                        ->find($arrayParametros['idServicio']);
            $objServicioTecnico = $this->emComercial->getRepository("schemaBundle:InfoServicioTecnico")
                                                        ->findOneByServicioId($objServicio->getId());
            $objProducto      = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                    ->find($objServicio->getProductoId());

            if($arrayParametros['strMigracionVlan'] === "S")
            {
                $boolMigracionVlan  = true;
                $strNombreSolicitud = "SOLICITUD MIGRACION DE VLAN";
            }

            $objTipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                  ->findOneBy(array("descripcionSolicitud" => $strNombreSolicitud,
                                                                    "estado"               => "Activo"));

            $objSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                 ->findOneBy(array( "tipoSolicitudId"   => $objTipoSolicitud->getId(), 
                                                    "servicioId"        => $objServicio->getId(),
                                                    "estado"            => "Asignada"));
            //---------------------------------------------------------------------------------------------------------------
            // Interface del elemento SWITCH
            $objInterfaceElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                 ->find($objServicioTecnico->getInterfaceElementoId());
            // Elemento SWITCH          
            $objElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                ->find($objServicioTecnico->getElementoId());
            //---------------------------------------------------------------------------------------------------------------

            //mac actual del servicio para realizar validacion y no incluirla
            $macActual  = $this->servicioGeneral->getMacPorServicio($objServicio->getId());

            // Obtener la vlan anterior
            $objCaractVlan = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array("descripcionCaracteristica" => "VLAN", "estado" => "Activo"));

            $objSolCaracVlan = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array(  "detalleSolicitudId"    => $objSolicitud->getId(), 
                                                                    "caracteristicaId"      => $objCaractVlan->getId(),
                                                                    "estado"                => "Asignada"));

            if($objProducto->getNombreTecnico() === 'L3MPLS' || $objProducto->getNombreTecnico() === 'L3MPLS SDWAN')
            {
                $objPerEmpRolCarVlan = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                         ->find($objSolCaracVlan->getValor());
                $objDetalleElementoVlanAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                       ->find($objPerEmpRolCarVlan->getValor());
            }
            else
            {
                $objDetalleElementoVlanAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                       ->find($objSolCaracVlan->getValor());
            }
            //------------------------------------------------------------------
            //obtener la ip anterior
            $objCaractIp = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                             ->findOneBy(array("descripcionCaracteristica" => "IP_ID", "estado" => "Activo"));
            if(!$objCaractIp)
            {
                return array('status'  => "ERROR",
                             'mensaje' => "Error con la IP de la Solicitud: <br>\tNo existe o No está Activa. ");
            }

            $objSolCaracIp = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array(  "detalleSolicitudId"    => $objSolicitud->getId(), 
                                                                    "caracteristicaId"      => $objCaractIp->getId(),
                                                                    "estado"                => "Asignada"));            
                            
            $objIpAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                     ->find($objSolCaracIp->getValor());
            //------------------------------------------------------------------

            $arrayMacNueva[] = $macActual;
            $arrayMacVlan    = array($objDetalleElementoVlanAnterior->getDetalleValor()=>$arrayMacNueva);

            //====================================================================================================
            //                    ELIMINACION CONFIGURACION ANTERIOR PARA MIGRACION DE ANILLO
            //====================================================================================================
            
            $objPeAnterior     = null;
            $objSubredAnterior = null;
            $boolEnrutar       = false;
            
            if(is_object($objIpAnterior) && $objIpAnterior->getSubredId())
            {
                $objSubredAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                             ->find($objIpAnterior->getSubredId());
                if(is_object($objSubredAnterior))
                {
                    $objPeAnterior = $objSubredAnterior->getElementoId();
                }
            }
            else if(is_object($objElemento) && ($objProducto->getNombreTecnico() === 'INTMPLS' || 
                                                $objProducto->getNombreTecnico() === 'INTERNET SDWAN'))
            {
                $arrayParametrosWs["intIdElemento"] = $objElemento->getId();
                $arrayParametrosWs["intIdServicio"] = $arrayParametros['idServicio'];

                $objPeAnterior = $this->servicioGeneral->getPeBySwitch($arrayParametrosWs);
            }
            else
            {
                $objPeAnterior = null;
            }

            // ===================================================================================================
            // CALL WS "NetworkingScriptsService" >>> SE CANCELA LA MAC DEL SWITCH
            // ===================================================================================================
            $arrayRequestWS                = array();
            $arrayRequestWS['url']         = 'configMAC';
            $arrayRequestWS['accion']      = 'migracion-del';
            $arrayRequestWS['sw']          = $objElemento->getNombreElemento();
            $arrayRequestWS['anillo']      = $arrayParametros['anillo'];
            $arrayRequestWS['pto']         = $objInterfaceElemento->getNombreInterfaceElemento();
            $arrayRequestWS['macVlan']     = $arrayMacVlan;
            $arrayRequestWS['descripcion'] = 'cce_'.$objServicio->getLoginAux().'_fib';
            $arrayRequestWS['servicio']    = $objProducto->getNombreTecnico();
            $arrayRequestWS['login_aux']   = $objServicio->getLoginAux();
            $arrayRequestWS['user_name']   = $arrayParametros['usrCreacion'];
            $arrayRequestWS['user_ip']     = $arrayParametros['ipCreacion'];
            // Ejecucion del metodo via WS para realizar la configuracion del SW
            $arrayRespuesta = $this->networkingScripts->callNetworkingWebService($arrayRequestWS);
            if($arrayRespuesta['status'] != 'OK')
            {
                throw new \Exception("PROBLEMAS DE COMUNICACION: ".$arrayRespuesta['mensaje']);
            }
            
            // ===================================================================================================
            // SE EJECUTAN PETICIONES A LOS PE PARA LOS PRODUCTOS CON NOMBRE TECNICO L3MPLS - ELIMINACION DEL PE
            // ===================================================================================================
            if($objProducto->getNombreTecnico() === 'L3MPLS' || $objProducto->getNombreTecnico() === 'L3MPLS SDWAN')
            {
                if(!$objSubredAnterior)
                {
                    return array('status'  => "ERROR", 
                                 'mensaje' => "Error con la SubRed Actual: <br>\tEl Servicio L3MPLS lo requiere y NO existe.");
                }

                $arrayIpsSubred = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                            ->findBy(array( 'subredId'  => $objIpAnterior->getSubredId(),
                                                            'estado'    => 'Activo'));

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

                //ROUTER - cancelar en el pe
                if(count($arrayIpsSubred)==0)
                {
                    //------------------------------------------------------------------
                    //obtener la vlan anterior
                    $objCaractVlan = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array("descripcionCaracteristica" => "VLAN", "estado" => "Activo"));

                    $objSolCaracVlan = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array(  "detalleSolicitudId"    => $objSolicitud->getId(), 
                                                                            "caracteristicaId"      => $objCaractVlan->getId(),
                                                                            "estado"                => "Asignada"));

                    $objPerEmpRolCarVlan = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                        ->find($objSolCaracVlan->getValor());
                    
                    $objDetalleElementoVlanAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                        ->find($objPerEmpRolCarVlan->getValor());
                    //------------------------------------------------------------------
                    //obtener la vrf anterior
                    $objCaractVrf = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array("descripcionCaracteristica" => "VRF", "estado" => "Activo"));

                    $objSolCaracVrf = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array(  "detalleSolicitudId"    => $objSolicitud->getId(), 
                                                                            "caracteristicaId"      => $objCaractVrf->getId(),
                                                                            "estado"                => "Asignada"));

                    $objPerEmpRolCarVrf = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                        ->find($objSolCaracVrf->getValor());
                    //------------------------------------------------------------------
                    //obtener el protocolo anterior
                    $objCaractProtocolo = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array("descripcionCaracteristica" => "PROTOCOLO_ENRUTAMIENTO", 
                                                                        "estado" => "Activo"));

                    $objSolCaracProtocolo = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array(  "detalleSolicitudId"    => $objSolicitud->getId(), 
                                                                            "caracteristicaId"      => $objCaractProtocolo->getId(),
                                                                            "estado"                => "Asignada"));
                    //------------------------------------------------------------------
                    //obtener el default gateway anterior
                    $objCaractDefaultGw = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array("descripcionCaracteristica" => "DEFAULT_GATEWAY", 
                                                                        "estado" => "Activo"));

                    $objSolCaracDefaultGw = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array(  "detalleSolicitudId"    => $objSolicitud->getId(), 
                                                                            "caracteristicaId"      => $objCaractDefaultGw->getId(),
                                                                            "estado"                => "Asignada"));
                    //------------------------------------------------------------------
                    //obtener el rd_id anterior
                    $objCaractRdId = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array("descripcionCaracteristica" => "RD_ID", 
                                                                        "estado" => "Activo"));

                    $objPerEmpRolCarRdId = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                ->findOneBy(array("personaEmpresaRolCaracId" => $objPerEmpRolCarVrf->getPersonaEmpresaRolCaracId(),
                                                                  "caracteristicaId"         => $objCaractRdId->getId(),
                                                                  "estado"                   => "Activo"));
                    // ===================================================================================================
                    // CALL WS "NetworkingScriptsService" >>> SE CANCELA EN EL PE
                    // ===================================================================================================
                    $arrayPeticionesPe                          = array();
                    $arrayPeticionesPe['url']                   = 'configPE';
                    $arrayPeticionesPe['accion']                = 'Cancelar';        
                    $arrayPeticionesPe['sw']                    = $objElemento->getNombreElemento();
                    $arrayPeticionesPe['clase_servicio']        = $objProducto->getNombreTecnico();
                    $arrayPeticionesPe['vrf']                   = $objPerEmpRolCarVrf->getValor();
                    $arrayPeticionesPe['pe']                    = is_object($objPeAnterior)?$objPeAnterior->getNombreElemento():'';
                    $arrayPeticionesPe['anillo']                = $arrayParametros['anillo'];
                    $arrayPeticionesPe['vlan']                  = $objDetalleElementoVlanAnterior->getDetalleValor();
                    $arrayPeticionesPe['subred']                = $objSubredAnterior->getSubred();
                    $arrayPeticionesPe['mascara']               = $objSubredAnterior->getMascara();
                    $arrayPeticionesPe['gateway']               = $objSubredAnterior->getGateway();
                    $arrayPeticionesPe['rd_id']                 = $objPerEmpRolCarRdId->getValor();
                    $arrayPeticionesPe['descripcion_interface'] = $objServicio->getLoginAux();
                    $arrayPeticionesPe['ip_bgp']                = $objIpAnterior->getIp();
                    $arrayPeticionesPe['asprivado']             = $arrayParametros['asPrivado'];
                    $arrayPeticionesPe['nombre_sesion_bgp']     = $objServicio->getLoginAux();
                    $arrayPeticionesPe['default_gw']            = ($objSolCaracDefaultGw) ? $objSolCaracDefaultGw->getValor(): "NO";
                    $arrayPeticionesPe['protocolo']             = $objSolCaracProtocolo->getValor();
                    $arrayPeticionesPe['servicio']              = $objProducto->getNombreTecnico();
                    $arrayPeticionesPe['login_aux']             = $objServicio->getLoginAux();
                    $arrayPeticionesPe['tipo_enlace']           = $objServicioTecnico->getTipoEnlace();
                    $arrayPeticionesPe['banderaBravco']         = 'NO';
                    $arrayPeticionesPe['weight']                = null;
                    $arrayPeticionesPe['user_name']             = $arrayParametros['usrCreacion'];
                    $arrayPeticionesPe['user_ip']               = $arrayParametros['ipCreacion'];

                    //Se envian a configurar lineas de monitoreo de enlaces de datos
                    if($strBanderaLineasBravco === "S")
                    {
                        $arrayPeticionesPe['razon_social'] = $strRazonSocial;
                        $arrayPeticionesPe['rt_export']    = $strRouteTargetExport;
                        $arrayPeticionesPe['rt_import']    = $strRouteTargetImport;
                    }

                    //ROUTER - Ejecucion del metodo via WS para realizar la configuracion en el Pe
                    $arrayRespuesta = $this->networkingScripts->callNetworkingWebService($arrayPeticionesPe);
                    if($arrayRespuesta['status'] != 'OK')
                    {
                        throw new \Exception("PROBLEMAS DE COMUNICACION: ".$arrayRespuesta['mensaje']);
                    }
                }
            }
            
            // ===================================================================================================
            // CALL WS "NetworkingScriptsService" >>> Eliminar rutas del servicio
            // ===================================================================================================
            $arrayRutas = array();

            if(is_object($objPeAnterior))
            {
                $arrayRutas = $this->emInfraestructura->getRepository("schemaBundle:InfoRutaElemento")
                                                      ->findBy(array( "servicioId" => $objServicio->getId(),
                                                                      "estado"     => "Activo"));
                foreach($arrayRutas as $objRuta)
                {
                    $arrayPeticionesRutas                    = array();
                    $arrayPeticionesRutas['url']             = 'enrutamientoEstaticoPe';
                    $arrayPeticionesRutas['accion']          = 'eliminar';                        
                    $arrayPeticionesRutas['clase_servicio']  = $objProducto->getNombreTecnico();
                    $arrayPeticionesRutas['vrf']             = $arrayParametros['vrf'];
                    $arrayPeticionesRutas['pe']              = $objPeAnterior->getNombreElemento();
                    $arrayPeticionesRutas['sw']              = $objElemento->getNombreElemento();
                    $arrayPeticionesRutas['name_route']      = $objRuta->getNombre();
                    $arrayPeticionesRutas['net_lan']         = $objRuta->getRedLan();
                    $arrayPeticionesRutas['mask_lan']        = $objRuta->getMascaraRedLan();
                    $arrayPeticionesRutas['ip_destino']      = $objIpAnterior->getIp();
                    $arrayPeticionesRutas['distance_admin']  = '1';
                    $arrayPeticionesRutas['servicio']        = $objProducto->getNombreTecnico();
                    $arrayPeticionesRutas['login_aux']       = $objServicio->getLoginAux();
                    $arrayPeticionesRutas['user_name']       = $arrayParametros['usrCreacion'];
                    $arrayPeticionesRutas['user_ip']         = $arrayParametros['ipCreacion'];
                    $arrayRespuesta = $this->networkingScripts->callNetworkingWebService($arrayPeticionesRutas);

                    $strStatus = $arrayRespuesta['status'];

                    if($strStatus == 'OK')
                    {
                        $strObservacion = "Anillo";

                        if($boolMigracionVlan)
                        {
                            $strObservacion = "de VLAN";
                        }

                        $objRuta->setEstado("Eliminado");
                        $objRuta->setUsrUltMod($arrayParametros['usrCreacion']); 	
                        $objRuta->setFeUltMod(new \DateTime('now'));
                        $this->emInfraestructura->persist($objRuta);
                        $this->emInfraestructura->flush();

                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($objServicio);
                        $objServicioHistorial->setObservacion('Se Eliminó la ruta <b>'.$objRuta->getNombre().'</b>'.
                                                              ' ligada a la IP : <b>'.$objIpAnterior->getIp().'</b> '
                                                              . 'por Migración '.$strObservacion.' del Servicio');
                        $objServicioHistorial->setIpCreacion($arrayParametros['ipCreacion']);
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
                        $objServicioHistorial->setEstado($objServicio->getEstado());
                        $this->emComercial->persist($objServicioHistorial);
                        $this->emComercial->flush();

                        $boolEnrutar = true;
                    }
                    else
                    {
                        throw new \Exception("PROBLEMAS DE COMUNICACION: ".$arrayRespuesta['mensaje']);
                    }                    
                }
            }

            //====================================================================================================
            //                          CONFIGURACION NUEVA PARA MIGRACION DE ANILLO
            //====================================================================================================
            
            // ===================================================================================================
            // CALL WS "NetworkingScriptsService" >>> SE ACTIVA LA MAC DEL SWITCH EN ANILLO
            // ===================================================================================================
            $arrayRequestWS['accion']      = 'migracion-add';
            $arrayRequestWS['macVlan']     = array($arrayParametros['vlan']=>$arrayMacNueva);
            if($objServicio->getdescripcionpresentafactura() == 'CANAL TELEFONIA')
            {
                $arrayRequestWS['servicio']    = 'NETVOICE-L3MPLS';      
            }
            // Ejecucion del metodo via WS para realizar la configuracion del SW
            $arrayRespuesta = $this->networkingScripts->callNetworkingWebService($arrayRequestWS);
            if($arrayRespuesta['status'] != 'OK')
            {
                throw new \Exception("PROBLEMAS DE COMUNICACION: ".$arrayRespuesta['mensaje']);
            }
            
            // ===================================================================================================
            // CALL WS "NetworkingScriptsService" >>> SE ACTIVAR EN EL PE
            // ===================================================================================================
            if($objProducto->getNombreTecnico() === 'L3MPLS' || $objProducto->getNombreTecnico() === 'L3MPLS SDWAN')
            {
                $arrayPeticionesPe                          = array();
                if($objServicio->getdescripcionpresentafactura() == 'CANAL TELEFONIA')
                {
                    $arrayPeticionesPe['servicio']          = 'NETVOICE-L3MPLS';
                    $arrayPeticionesPe['clase_servicio']    = 'NETVOICE-L3MPLS';      
                }
                else
                {             
                    $arrayPeticionesPe['servicio']          = $objProducto->getNombreTecnico();
                    $arrayPeticionesPe['clase_servicio']    = $objProducto->getNombreTecnico();

                }
                $arrayPeticionesPe['url']                   = 'configPE';
                $arrayPeticionesPe['accion']                = 'Activar';        
                $arrayPeticionesPe['sw']                    = $objElemento->getNombreElemento();
                $arrayPeticionesPe['vrf']                   = $arrayParametros['vrf'];
                $arrayPeticionesPe['pe']                    = $arrayParametros['nombreElementoPadre'];
                $arrayPeticionesPe['anillo']                = $arrayParametros['anillo'];
                $arrayPeticionesPe['vlan']                  = $arrayParametros['vlan'];
                $arrayPeticionesPe['subred']                = $arrayParametros['subredServicio'];
                $arrayPeticionesPe['mascara']               = $arrayParametros['mascaraSubredServicio'];
                $arrayPeticionesPe['gateway']               = $arrayParametros['gwSubredServicio'];
                $arrayPeticionesPe['rd_id']                 = $arrayParametros['rdId'];
                $arrayPeticionesPe['descripcion_interface'] = $objServicio->getLoginAux();
                $arrayPeticionesPe['ip_bgp']                = $arrayParametros['ipServicio'];
                $arrayPeticionesPe['asprivado']             = $arrayParametros['asPrivado'];
                $arrayPeticionesPe['nombre_sesion_bgp']     = $objServicio->getLoginAux();
                $arrayPeticionesPe['default_gw']            = $arrayParametros['defaultGateway'];
                $arrayPeticionesPe['protocolo']             = $arrayParametros['protocolo'];
                $arrayPeticionesPe['login_aux']             = $objServicio->getLoginAux();
                $arrayPeticionesPe['tipo_enlace']           = $objServicioTecnico->getTipoEnlace();
                $arrayPeticionesPe['banderaBravco']         = 'NO';
                $arrayPeticionesPe['weight']                = null;
                $arrayPeticionesPe['user_name']             = $arrayParametros['usrCreacion'];
                $arrayPeticionesPe['user_ip']               = $arrayParametros['ipCreacion'];

                //Se envian a configurar lineas de monitoreo de enlaces de datos
                if($strBanderaLineasBravco === "S")
                {
                    $arrayPeticionesPe['razon_social'] = $strRazonSocial;
                    $arrayPeticionesPe['rt_export']    = $strRouteTargetExport;
                    $arrayPeticionesPe['rt_import']    = $strRouteTargetImport;
                }

                //Ejecucion del metodo via WS para realizar la configuracion en el Pe
                $arrayRespuesta = $this->networkingScripts->callNetworkingWebService($arrayPeticionesPe);
                if($arrayRespuesta['status'] != 'OK')
                {
                    throw new \Exception("PROBLEMAS DE COMUNICACION: ".$arrayRespuesta['mensaje']);
                }
            }
            
            // ===================================================================================================
            // CALL WS "NetworkingScriptsService" >>> Agregar rutas del servicio
            // ===================================================================================================
            
            if($boolEnrutar && !empty($arrayRutas))
            {
                foreach($arrayRutas as $objRuta)
                {
                    $arrayPeticionesRutas                    = array();
                    $arrayPeticionesRutas['url']             = 'enrutamientoEstaticoPe';
                    $arrayPeticionesRutas['accion']          = 'agregar';                        
                    $arrayPeticionesRutas['clase_servicio']  = $objProducto->getNombreTecnico();
                    $arrayPeticionesRutas['vrf']             = $arrayParametros['vrf'];
                    $arrayPeticionesRutas['pe']              = $arrayParametros['nombreElementoPadre'];
                    $arrayPeticionesRutas['sw']              = $objElemento->getNombreElemento();
                    $arrayPeticionesRutas['name_route']      = $objRuta->getNombre();
                    $arrayPeticionesRutas['net_lan']         = $objRuta->getRedLan();
                    $arrayPeticionesRutas['mask_lan']        = $objRuta->getMascaraRedLan();
                    $arrayPeticionesRutas['ip_destino']      = $arrayParametros['ipServicio'];
                    $arrayPeticionesRutas['distance_admin']  = '1';
                    $arrayPeticionesRutas['servicio']        = $objProducto->getNombreTecnico();
                    $arrayPeticionesRutas['login_aux']       = $objServicio->getLoginAux();
                    $arrayPeticionesRutas['user_name']       = $arrayParametros['usrCreacion'];
                    $arrayPeticionesRutas['user_ip']         = $arrayParametros['ipCreacion'];
                    
                    $arrayRespuesta = $this->networkingScripts->callNetworkingWebService($arrayPeticionesRutas);

                    $strStatus = $arrayRespuesta['status'];

                    if($strStatus == 'OK')
                    {
                        //Se obtiene el PE
                        $objPeNuevo = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                              ->findOneBy(array('nombreElemento' => $arrayParametros['nombreElementoPadre'],
                                                                                'estado'         => 'Activo'));
                        if(!is_object($objPeNuevo))
                        {
                            throw new \Exception("No existe referencia en Telcos ligado al Pe : ".$arrayParametros['nombreElementoPadre']);
                        }

                        $objIpNueva = $this->emInfraestructura->getRepository("schemaBundle:InfoIp")
                                                              ->findOneBy(array('servicioId' => $objServicio->getId(),
                                                                                'estado'     => 'Activo',
                                                                                'ip'         => $arrayParametros['ipServicio']));                                
                        if(!is_object($objIpNueva))
                        {
                            throw new \Exception('No existe referencia de la Ip '.$arrayParametros['ipServicio'].' '
                                                 .'configurada en Telcos, notificar a Sistemas');
                        }

                        $objRutaElementoNuevo = new InfoRutaElemento();
                        $objRutaElementoNuevo->setServicioId($objServicio);
                        $objRutaElementoNuevo->setElementoId($objPeNuevo);
                        $objRutaElementoNuevo->setIpId($objIpNueva);
                        $objRutaElementoNuevo->setNombre($objRuta->getNombre());
                        $objRutaElementoNuevo->setEstado("Activo");
                        $objRutaElementoNuevo->setRedLan($objRuta->getRedLan());
                        $objRutaElementoNuevo->setMascaraRedLan($objRuta->getMascaraRedLan());
                        $objRutaElementoNuevo->setDistanciaAdmin($objRuta->getDistanciaAdmin());
                        $objRutaElementoNuevo->setFeCreacion(new \DateTime('now'));
                        $objRutaElementoNuevo->setUsrCreacion($arrayParametros['usrCreacion']);
                        $objRutaElementoNuevo->setIpCreacion($arrayParametros['ipCreacion']);
                        $this->emInfraestructura->merge($objRutaElementoNuevo);
                        $this->emInfraestructura->flush();

                        $strObservacion = "Anillo";

                        if($boolMigracionVlan)
                        {
                            $strObservacion = "de VLAN";
                        }

                        //Historial de creacion de ruta en proceso de migracion a anillo
                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($objServicio);
                        $objServicioHistorial->setObservacion('Se Creó la ruta <b>'.$objRuta->getNombre().'</b>'
                                                              .' ligada a la Nueva IP : <b>'.$arrayParametros['ipServicio'].'</b> '
                                                              . 'por Migración '.$strObservacion.' del Servicio');
                        $objServicioHistorial->setIpCreacion($arrayParametros['ipCreacion']);
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
                        $objServicioHistorial->setEstado($objServicio->getEstado());
                        $this->emComercial->persist($objServicioHistorial);
                        $this->emComercial->flush();
                    }
                    else
                    {
                        throw new \Exception("PROBLEMAS DE COMUNICACION: ".$arrayRespuesta['mensaje']);
                    }
                }
            }
            
            // ===================================================================================================
            // FINALIZACION DE LA SOLICITUD DE MIGRACION DE ANILLOS
            // ===================================================================================================
            $objSolicitud->setEstado("Finalizada");
            $this->emComercial->persist($objSolicitud);
            $this->emComercial->flush();
            
            // Agregar historial a la solicitud
            $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
            $objDetalleSolicitudHistorial->setDetalleSolicitudId($objSolicitud);
            $objDetalleSolicitudHistorial->setIpCreacion($arrayParametros['ipCreacion']);
            $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
            $objDetalleSolicitudHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
            $objDetalleSolicitudHistorial->setEstado("Finalizada");
            $this->emComercial->persist($objDetalleSolicitudHistorial);
            $this->emComercial->flush();

            $strObservacion = "a anillo";

            if($boolMigracionVlan)
            {
                $strObservacion = "de VLAN";
            }

            // Agregar servicio historial
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setIpCreacion($arrayParametros['ipCreacion']);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
            $objServicioHistorial->setEstado($objServicio->getEstado());
            $objServicioHistorial->setObservacion('Se finalizo la solicitud de migración '.$strObservacion);
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();

            // Actualizar las solicitudes caract
            $arrayDetalleSolicitudCarac = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                               ->findBy(array("detalleSolicitudId"  => $objSolicitud->getId(), 
                                                             "estado"              => "Asignada"));
            foreach($arrayDetalleSolicitudCarac as $objDetalleSolCarac)
            {
                $objDetalleSolCarac->setEstado("Finalizada");
                $this->emComercial->persist($objDetalleSolCarac);
                $this->emComercial->flush();
            }
            
            // ===================================================================================================
            // COMMITS DE LAS TRANSACCIONES
            // ===================================================================================================
            if ($this->emInfraestructura->getConnection()->isTransactionActive()) 
            {
                $this->emInfraestructura->getConnection()->commit();
            }
            
            if ($this->emComercial->getConnection()->isTransactionActive()) 
            {
                $this->emComercial->getConnection()->commit();
            }
            
            $status  = "OK";
            $mensaje = "OK";
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
                        
            $status             = "ERROR";
            $mensaje            = "ERROR EN LA LOGICA DE NEGOCIO: <br> ".$e->getMessage();
        }
        
        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        
        return array('status' => $status, 'mensaje' => $mensaje);
    }
    
    /**
     * Funcion que sirve para crear la solicitud de migracion de anillo con sus caracteristicas, y por ultimo 
     * asignar los nuevos recursos de red de l3mpls
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-06-2016
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 23-04-2019 - Se agrega el parámetro empresaCod en la llamada a la función: asignarRecursosRedL3mpls, con el objetivo de que
     *                           valide correctamente la asignación de subredes para el proyecto de Interconexion
     * 
     * @author David Leon       <mdleon@telconet.ec>
     * @version 1.2 05-08-2019 - Se agrega el producto Internet Sdwan en la validacion de asignar recursos.
     * 
     */
    public function solicitarMigracionAnilloTN($arrayParametros)
    {
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/        
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        try
        {
            $arrayObjIp  = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                ->findBy(array('servicioId' => $arrayParametros['idServicio'], 
                                               'estado'     => 'Activo'));

            $objServicio         = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayParametros['idServicio']);

            if(count($arrayObjIp)>1 && (is_object($objServicio) && $objServicio->getdescripcionpresentafactura() != 'CANAL TELEFONIA'))
            {
                throw new \Exception("Servicio posee más de una ip Activa, Favor Regularizar!");
            }
            
            // Crear solicitud y sus caracteristicas
            $objDetalleSolicitud = $this->crearSolicitudYCaracteristicas($arrayParametros);
            
            // Eliminar caracteristicas anteriores 
            $this->servicioGeneral->eliminarDatosCaracteristicas($arrayParametros);
            
            $arrayParametros['idDetalleSolicitud']  = $objDetalleSolicitud->getId();
            $arrayParametros['flagTransaccion']     = false;
            $arrayParametros['flagServicio']        = false;            
            $arrayParametros['objDetalleSolicitud'] = $objDetalleSolicitud;
            $arrayParametros['subred']              = $arrayParametros['idSubred'];
            $arrayParametros['empresaCod']          = $arrayParametros['idEmpresa'];

            if($objServicio->getProductoId()->getNombreTecnico() === 'INTERNET' ||
               $objServicio->getProductoId()->getNombreTecnico() === 'INTERNET SDWAN')
            {
                $arrayRespuestaRecursosRed = $this->recursosRed->asignarRecursosRedInternetMPLS($arrayParametros);
            }
            else
            {
                $arrayRespuestaRecursosRed = $this->recursosRed->asignarRecursosRedL3mpls($arrayParametros);
            }

            if($arrayRespuestaRecursosRed['status'] != "OK")
            {
                throw new \Exception($arrayRespuestaRecursosRed['mensaje']);
            }
            
            // Agregar servicio historial
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setIpCreacion($arrayParametros['ipCreacion']);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
            $objServicioHistorial->setEstado($objServicio->getEstado());
            $objServicioHistorial->setObservacion($this->setObservacionCambioUmProgramada($arrayParametros));
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();
            
            if ($this->emComercial->getConnection()->isTransactionActive()) 
            {
                $this->emComercial->getConnection()->commit();
            }
            
            if ($this->emInfraestructura->getConnection()->isTransactionActive()) 
            {
                $this->emInfraestructura->getConnection()->commit();
            }
            
            $status  = "OK";
            $mensaje = "OK";
        }
        catch (\Exception $e) 
        {
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }
                        
            $status             = "ERROR";
            $mensaje            = "ERROR EN LA LOGICA DE NEGOCIO: <br> ".$e->getMessage();
            
            error_log($mensaje);
        }
        
        $this->emComercial->getConnection()->close();
        $this->emInfraestructura->getConnection()->close();
        
        return array('status' => $status, 'mensaje' => $mensaje);
    }
    
    /**
     * Funcion que sirve para crear la observacion para el cambio de ultima milla programada
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 10-05-2016
     *
     * @author Juan Lafuente <jlafuente@telconet.ec> - Se agrega validaciones para el producto
     * @version 1.1 05-07-2016
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 01-10-2018 - Para servicios port channel, SI NO POSE LA CARACTERISTICA 'DEFAULT GATEWAY', NO SE MIGRA
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 30-18-2019 - Se realizan ajustes en la observacion del historial del servicio, para agregar el concepto de la herramienta de
     *                           migracion de vlan
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 22-03-2021 Se abre la programacion para servicios Internet SDWAN
     */
    public function setObservacionCambioUmProgramada($arrayPeticiones)
    {
        $objServicio         = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayPeticiones['idServicio']);
        $objDetalleSolicitud = $arrayPeticiones['objDetalleSolicitud'];
        // =================================================================
        // >>> Datos Anteriores
        // =================================================================
        $vlanAnterior       = '';
        $vrfAnterior        = '';
        $protocoloAnterior  = '';
        $defaultGwAnterior  = '';
        //...
        if($objServicio->getProductoId()->getNombreTecnico() === 'INTMPLS' || $objServicio->getProductoId()->getNombreTecnico() === 'INTERNET SDWAN')
        {
            //vlan anterior
            $objDetalleSolicitudVlan      = $this->servicioGeneral->getInfoDetalleSolCaract($objDetalleSolicitud, "VLAN");
            $objVlanAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                       ->find($objDetalleSolicitudVlan->getValor());
            $vrfAnterior        = 'Ninguna';
            $protocoloAnterior  = 'Ninguna';                          
            $defaultGwAnterior  = 'Ninguna';
        }
        else
        {
            //vlan anterior
            $objDetalleSolicitudVlan      = $this->servicioGeneral->getInfoDetalleSolCaract($objDetalleSolicitud, "VLAN");
            $objPerEmpRolCarVlanAnterior  = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                              ->find($objDetalleSolicitudVlan->getValor());
            $objVlanAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                       ->find($objPerEmpRolCarVlanAnterior->getValor());
            $vlanAnterior = $objVlanAnterior->getDetalleValor();

            //vrf anterior
            $objDetalleSolicitudVrf       = $this->servicioGeneral->getInfoDetalleSolCaract($objDetalleSolicitud, "VRF");
            $objPerEmpRolCarVrfAnterior   = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                              ->find($objDetalleSolicitudVrf->getValor());
            $vrfAnterior = $objPerEmpRolCarVrfAnterior->getValor();

            //protocolo de enrutamiento anterior
            $objDetalleSolicitudProtocolo = $this->servicioGeneral->getInfoDetalleSolCaract($objDetalleSolicitud, "PROTOCOLO_ENRUTAMIENTO");
            $protocoloAnterior = $objDetalleSolicitudProtocolo->getValor();
            
            //default gateway anterior
            $objDetalleSolicitudDefault   = $this->servicioGeneral->getInfoDetalleSolCaract($objDetalleSolicitud, "DEFAULT_GATEWAY");
            if(is_object($objDetalleSolicitudDefault)) {
                $defaultGwAnterior = $objDetalleSolicitudDefault->getValor();
            }
        }
                        
        // =================================================================
        // >>> Datos nuevos
        // =================================================================
        $vlanNuevo          = '';
        $vrfNuevo           = '';
        $protocoloNUevo     = '';
        $defaultGwNuevo     = '';
        //...
        //obtener la vrf
	if($objServicio->getProductoId()->getNombreTecnico() != 'INTMPLS' && $objServicio->getProductoId()->getNombreTecnico() != 'INTERNET SDWAN')
        {
		$objSpcVrf          = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "VRF", $objServicio->getProductoId());
		$objPerEmpRolCarVrf = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
		                                        ->find($objSpcVrf->getValor());
		$vrfNuevo = $objPerEmpRolCarVrf->getValor();
	}
        //obtener la vlan 
        $objSpcVlan         = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "VLAN", $objServicio->getProductoId());
        if($objServicio->getProductoId()->getNombreTecnico() === 'INTMPLS' || $objServicio->getProductoId()->getNombreTecnico() === 'INTERNET SDWAN')
        {
            $objVlan = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                               ->find($objSpcVlan->getValor());
            $vlanNuevo = $objVlan->getDetalleValor();
            $protocoloNUevo     = 'Ninguna';
            $defaultGwNuevo     = 'Ninguna';
        }
        else
        {
            $objPerEmpRolCarVlan = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                     ->find($objSpcVlan->getValor());
            $objVlan = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                               ->find($objPerEmpRolCarVlan->getValor());
            $vlanNuevo = $objVlan->getDetalleValor();

            // Obtener el protocolo enrutamiento
            $objSpcProtocolo= $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "PROTOCOLO_ENRUTAMIENTO", 
                                                                                        $objServicio->getProductoId());
            $protocoloNUevo = $objSpcProtocolo->getValor();

            // Obtener el default gw
            $objSpcDefault  = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "DEFAULT_GATEWAY", $objServicio->getProductoId());
            if(is_object($objSpcDefault)) {
                $defaultGwNuevo = $objSpcDefault->getValor();
            }
        }

        $strObservacion = "a Anillo";

        if($arrayPeticiones["tipoSolicitud"] === "SOLICITUD MIGRACION DE VLAN")
        {
            $strObservacion = "de VLAN";
        }

        if($objServicio->getProductoId()->getNombreTecnico() === 'INTMPLS' || $objServicio->getProductoId()->getNombreTecnico() === 'INTERNET SDWAN')
        {
            $observacion =      "Se creo una Solicitud de Migracion a Anillo <br>"
                            .   "con los siguientes datos: <br>"
                            .   "<b>Vrf Anterior</b>:".$vrfAnterior."<br>"
                            .   "<b>Vlan Anterior</b>:".$vlanAnterior."<br>"
                            .   "---------------------------------------------------------------------------------------------<br>"
                            .   "<b>Vrf Nuevo</b>:".$vrfNuevo."<br>"
                            .   "<b>Vlan Nuevo</b>:".$vlanNuevo."<br>";
        }
        else
        {
            $observacion =      "Se creo una Solicitud de Migracion ".$strObservacion." <br>"
                            .   "con los siguientes datos: <br>"
                            .   "<b>Default Gw Anterior</b>:".$defaultGwAnterior."<br>"
                            .   "<b>Protocolo Anterior</b>:".$protocoloAnterior."<br>"
                            .   "<b>Vrf Anterior</b>:".$vrfAnterior."<br>"
                            .   "<b>Vlan Anterior</b>:".$vlanAnterior."<br>"
                            .   "---------------------------------------------------------------------------------------------<br>"
                            .   "<b>Default Gw Nuevo</b>:".$defaultGwNuevo."<br>"
                            .   "<b>Protocolo Nuevo</b>:".$protocoloNUevo."<br>"
                            .   "<b>Vrf Nuevo</b>:".$vrfNuevo."<br>"
                            .   "<b>Vlan Nuevo</b>:".$vlanNuevo."<br>";
        }
        
        
        
        return $observacion;
    }
    
    /**
     * Función que sirve para crear la solicitud y sus caracteristicas
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 8-05-2016
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.1 24-06-2016
     * Se actualiza funcion para que tipo de solicitud sea parametrizada
     * 
     * @param  array $arrayPeticiones [idServicio, tipoSolicitud, usrCreacion, ipCreacion]
     */
    public function crearSolicitudYCaracteristicas($arrayPeticiones)
    {
        $objServicio      = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayPeticiones['idServicio']);
        $objServicioTec   = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                 ->findOneBy(array('servicioId' => $arrayPeticiones['idServicio']));
        $objTipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                 ->findOneBy(array("descripcionSolicitud" => $arrayPeticiones['tipoSolicitud'], "estado" => "Activo"));
                
        //crear solicitud
        $objDetalleSolicitud = new InfoDetalleSolicitud();
        $objDetalleSolicitud->setServicioId($objServicio);
        $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
        $objDetalleSolicitud->setUsrCreacion($arrayPeticiones['usrCreacion']);
        $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
        $objDetalleSolicitud->setEstado("Asignada");
        $this->emComercial->persist($objDetalleSolicitud);
        $this->emComercial->flush();
        
        //agregar historial a la solicitud
        $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
        $objDetalleSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
        $objDetalleSolicitudHistorial->setIpCreacion($arrayPeticiones['ipCreacion']);
        $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
        $objDetalleSolicitudHistorial->setUsrCreacion($arrayPeticiones['usrCreacion']);
        $objDetalleSolicitudHistorial->setEstado("Asignada");
        $this->emComercial->persist($objDetalleSolicitudHistorial);
        $this->emComercial->flush();
        
        //obtener los servicios prod caract
        $arrServicioProdCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                      ->findBy(array("servicioId" => $objServicio->getId(), "estado" => "Activo"));
        
        //grabar las caracteristicas en la solicitud
        foreach($arrServicioProdCaract as $objServicioProdCaract)
        {
            $objCaracteristica = $this->servicioGeneral->getCaracteristicaByInfoServicioProdCaract($objServicioProdCaract);
            
            $arrayParametros = array(
                                        'objDetalleSolicitudId' => $objDetalleSolicitud,
                                        'objCaracteristica'     => $objCaracteristica,
                                        'estado'                => "Asignada",
                                        'valor'                 => $objServicioProdCaract->getValor(),
                                        'usrCreacion'           => $arrayPeticiones['usrCreacion']
                                    );
            
            $this->servicioGeneral->insertarInfoDetalleSolCaract($arrayParametros);
        }
        
        // Se valida que el servicio tenga la descripcion de canal telefonia
        if($objServicio->getdescripcionpresentafactura() == 'CANAL TELEFONIA')
        {
            //grabar detalles tecnicos en la solicitud - IP_ID    
            $objIp  = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                ->findOneBy(array('servicioId' => $arrayPeticiones['idServicio'], 
                                                                'tipoIp'     => 'WAN',
                                                                'estado'     => 'Activo'));
        }
        else
        {
            //grabar detalles tecnicos en la solicitud - IP_ID    
            $objIp  = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                ->findOneBy(array('servicioId' => $arrayPeticiones['idServicio'], 
                                                                'estado'     => 'Activo'));
        }
        
        
        $objCaracIp  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array('descripcionCaracteristica' => 'IP_ID'));
        $arrayParametrosIp = array(
                                    'objDetalleSolicitudId' => $objDetalleSolicitud,
                                    'objCaracteristica'     => $objCaracIp,
                                    'estado'                => "Asignada",
                                    'valor'                 => $objIp->getId(),
                                    'usrCreacion'           => $arrayPeticiones['usrCreacion']
                                    );  
        $this->servicioGeneral->insertarInfoDetalleSolCaract($arrayParametrosIp);
        
        //eliminar ip anterior
        $objIp->setEstado('Eliminado');
        $this->emInfraestructura->persist($objIp);
        $this->emInfraestructura->flush();
        
        return $objDetalleSolicitud;
    }
    
    /**
     * Funcion que sirve para activar clientes vdsl en el sce
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function activarClienteSCE($idServicio, $accion)
    {
        $comando = "java -jar -Djava.security.egd=file:/dev/./urandom " . $this->pathTelcos . "telcos/src/telconet/tecnicoBundle/batch/ttco_sce.jar '" . $this->host . "' '" .
            $idServicio . "' '" . $accion . "' '" . $this->pathParameters . "'";
        $salida = shell_exec($comando);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para activar clientes que se encuentran
     * en un dslam modelo A2024
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function activarClienteA2024($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para activar clientes que se encuentran
     * en un dslam modelo A2048
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function activarClienteA2048($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para activar clientes que se encuentran
     * en un dslam modelo R1AD24A
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function activarClienteR1AD24A($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para activar clientes que se encuentran
     * en un dslam modelo R1AD48A
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function activarClienteR1AD48A($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para activar clientes que se encuentran
     * en un dslam modelo 7224
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function activarCliente7224($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para activar clientes que se encuentran
     * en un dslam modelo 6524
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function activarCliente6524($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para activar clientes que se encuentran
     * en un radio de marca IPTECOM
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function activarClienteIPTECOM($idDocumento, $usuario, $tipo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoRadio($idDocumento, $usuario, $tipo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para activar clientes que se encuentran
     * en un dslam modelo MEA1
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function activarClienteMea1($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para activar clientes que se encuentran
     * en un dslam modelo MEA3
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function activarClienteMea3($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para cancelar un cliente
     * que se encuentra configurado en un dslam de modelo A2024
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cancelarClienteA2024($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para cancelar un cliente
     * que se encuentra configurado en un dslam de modelo A2048
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cancelarClienteA2048($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para cancelar un cliente
     * que se encuentra configurado en un dslam de modelo R1AD24A
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cancelarClienteR1AD24A($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para cancelar un cliente
     * que se encuentra configurado en un dslam de modelo R1AD48A
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cancelarClienteR1AD48A($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para cancelar un cliente
     * que se encuentra configurado en un dslam de modelo 6524
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cancelarCliente6524($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para cancelar un cliente
     * que se encuentra configurado en un dslam de modelo 7224
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cancelarCliente7224($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para cancelar un cliente
     * que se encuentra configurado en un dslam de modelo MEA1
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cancelarClienteMea1($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para cancelar un cliente
     * que se encuentra configurado en un dslam de modelo MEA3  
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cancelarClienteMea3($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para cancelar un cliente
     * que se encuentra configurado en un radio IPTECOM
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cancelarClienteIPTECOM($idDocumento, $usuario, $tipo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoRadio($idDocumento, $usuario, $tipo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para cortar un cliente
     * que se encuentra configurado en un servidor RADIUS
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cancelarClienteRADIUS($idDocumento, $usuario, $tipo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoRadio($idDocumento, $usuario, $tipo, $elementoId, $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

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
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $nombreInterfaceElemento);        $pos = strpos($salida, "{");
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
     * Función que sirve para el cambio de línea pon cuando el olt del servicio sea ZTE
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 03-08-2018
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 26-12-2018 Se agregan parámetros equipoOntDualBand y tipoOrden por cambio en envío al middleware al activar un servicio
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.2 16-09-2019 Se agrega proceso para validar promociones en servicios de clientes, en caso de 
     *                         que aplique a alguna promoción se le configurarán los anchos de bandas promocionales
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.3 22-11-2020 Se agrega programación para ejecutar cambios de elementos de clientes pyme con IP FIJA WAN adicional
     * 
     * @author Jonathan Montece <jmontece@telconet.ec>
     * @version 1.4 14-06-2021 Se agrega nuevo parametro en objProductoInternet para ejecutar cambio de linea pon ZTE en TN
     *                          que tienen como nombre tecnico Internet Small Business, Telcohome
     * 
     * @author Jonathan Montece <jmontece@telconet.ec>
     * @version 1.5 21-07-2021 Corrección de error en cambio de linea PON, se agrega un "=" en validación de prefijoEmpresa 
     *     
     * @author Jonathan Montece <jmontece@telconet.ec>
     * @version 1.6 29-10-2021 se agrega validación para obtener el tipo de negocio correctamente
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.6 11-10-2021 Se agrega validación para los productos que tienen aprovisionamiento de ip privadas. 
     * 
     * @since 1.1
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.7 15-11-2021 Se construye el arreglo con la información que se enviará al invocar al web service para confirmación de 
     *                         opción de Tn a Middleware
     * 
     * @author Manuel Carpio <mcarpio@telconet.ec>
     * @version 1.8 5-1-2023 Se corrige problemas en consulta obtenerParametrosProductosTnGpon ya que se envia en id del la ip adicional 
     *                  y se corrigion para en enviar el id del servicio principal ISMB 
     * 
     * @author Jenniffer Mujica <jmujica@telconet.ec>
     * @version 1.9 6-02-2023 Se valida cambio de olt zte para tipo de red GPON MPLS
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 2.0 06-01-2023 Se valida la llave de numeros de datos a activar en funcion del arreglo datos para los servicios  GponMpls
     * 
     * @param array $arrayParametros [
     *                                  'idServicio'                    => id del servicio
     *                                  'interfaceElementoId'           => id de la nueva interface del olt
     *                                  'idSolicitud'                   => id de la solicitud de cambio de línea pon
     *                                  'idEmpresa'                     => código de la empresa
     *                                  'prefijoEmpresa'                => prefijo de la empresa
     *                                  'usrCreacion'                   => usuario de creación
     *                                  'ipCreacion'                    => ip de creación
     *                                  'elementoCajaId'                => id de la caja nueva
     *                                  'elementoSplitterId'            => id del splitter nuevo
     *                                  'interfaceElementoSplitterId'   => id de la nueva interface del splitter
     *                                ]
     * 
     * @return array $arrayResultado [
     *                                  'status'    => OK o ERROR
     *                                  'mensaje'   => mensaje de proceso
     *                               ]
     * 
     */
    public function cambiarPuertoScriptMdZte($arrayParametros)
    {
        $intNumIpEnPlan                 = 0;
        $intIdPlanIpsDisponibleScopeOlt = 0;
        $strScopeActual                 = "";
        $strIpNuevaPlan                 = "";
        $strScopeNuevo                  = "";
        $strIpActualPlan                = "";
        $arrayIpCancelar                = array();
        $arrayIpActivar                 = array();
        $intIpsFijasActivas             = 0;
        $objSpcScopeActual              = null;
        $intIdServicio                  = $arrayParametros['idServicio'];
        $intIdInterfaceOltNuevo         = $arrayParametros['interfaceElementoId'];
        $intIdSolicitudCambioLineaPon   = $arrayParametros['idSolicitud'];
        $strCodEmpresa                  = $arrayParametros['idEmpresa'];
        $strPrefijoEmpresa              = $arrayParametros['prefijoEmpresa'];
        $strUsrCreacion                 = $arrayParametros['usrCreacion'];
        $strIpCreacion                  = $arrayParametros['ipCreacion'];
        $intIdCajaNueva                 = $arrayParametros['elementoCajaId'];
        $intIdSplitterNuevo             = $arrayParametros['elementoSplitterId'];
        $intIdInterfaceSplitterNuevo    = $arrayParametros['interfaceElementoSplitterId'];
        $strTipoRed                     = $arrayParametros['tipoRed'];   
        $strEsIsb                       = $arrayParametros['esIsb'];
        $intProductoId                  = $arrayParametros['productoId'];
        $objSession                     = $arrayParametros['objSession'];
        $arrayDataNoc                   = $arrayParametros['arrayDatosNoc'];
        $intIdElementoOltNuevo          = 0;
        $strExisteIpWan                 = "NO";
        $arrayDatosIpWan                = array();
        $arrayDataConfirmacionTn        = array();
        $boolRedGponMpls            = false;
        
        $this->emComercial->beginTransaction();
        $this->emInfraestructura->beginTransaction();
        try
        {
            if(isset($strTipoRed) && !empty($strTipoRed) && $strTipoRed === "GPON_MPLS")
            {
                $boolRedGponMpls = true;
            }
            
            $objServicio                = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findOneById($intIdServicio);
            if(!is_object($objServicio))
            {
                throw new \Exception("No existe el servicio.");
            }
            $boolIsb           = false;
            if ($strPrefijoEmpresa === "TN")
            {
                $arrayParametrosCaract    = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                            ->getOne("IP_PRIVADA_GPON_CARACTERISTICAS",
                                                                                     "COMERCIAL",
                                                                                     "",
                                                                                     "",
                                                                                     $objServicio->getProductoId()->getDescripcionProducto(),
                                                                                     "",
                                                                                     "",
                                                                                     "",
                                                                                     "",
                                                                                     $strCodEmpresa);
                if(isset($arrayParametrosCaract['valor2']) && !empty($arrayParametrosCaract['valor2']))
                {
                    $strCaractIsb = $arrayParametrosCaract['valor2'];
                    $boolIsb      = true;
                }
            }
                        
            if($strEsIsb == "SI" && $strPrefijoEmpresa == "TN" && !empty($intProductoId))
            {
                $objProductoInternet    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                        ->findOneBy(array(  "id" => $intProductoId,
                                                                            "empresaCod"    => $strCodEmpresa,
                                                                            "estado"        => "Activo"));
                 if(!is_object($objProductoInternet))
                {
                    throw new \Exception("No existen los productos del servicio.");
                }
            }
            else if($boolRedGponMpls && $strPrefijoEmpresa == "TN")
            {
                $objProductoInternet = $objServicio->getProductoId();
                //$arrayProdIp   
                if(!is_object($objProductoInternet) || empty($objProductoInternet))
                {
                    throw new \Exception("No existen los productos del servicio.");
                }

                $arrayProdIp[]  = $objProductoInternet;
            }
            else
            {
                $objProductoInternet    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                        ->findOneBy(array(  "nombreTecnico" => "INTERNET",
                                                                            "empresaCod"    => $strCodEmpresa,
                                                                            "estado"        => "Activo"));
                 $arrayProdIp            = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                        ->findBy(array( "nombreTecnico" => "IP",
                                                                        "empresaCod"    => $strCodEmpresa,
                                                                        "estado"        => "Activo"));
                if(!is_object($objProductoInternet) || empty($arrayProdIp)) 
                {
                    throw new \Exception("No existen los productos del servicio.");
                }            
            }
                    
            $objSolicitudCambioLineaPon = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                            ->findOneById($intIdSolicitudCambioLineaPon);
            if(!is_object($objSolicitudCambioLineaPon))
            {
                throw new \Exception("No existe solicitud de cambio de línea Pon.");
            }
            $objPersona                 = $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
            $strIdentificacion          = $objPersona->getIdentificacionCliente();
            $strNombreCliente           = ($objPersona->getRazonSocial()) ? $objPersona->getRazonSocial() :
                                          $objPersona->getNombres()." ".$objPersona->getApellidos();
            $objPunto                   = $objServicio->getPuntoId();
            $objServicioTecnico         = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->findOneBy(array("servicioId" => $objServicio->getId()));
            $objDetalleOltMiddleware    = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                  ->findOneBy(array("elementoId"    => $objServicioTecnico->getElementoId(),
                                                                                    "detalleNombre" => 'MIDDLEWARE',
                                                                                    "detalleValor"  => 'SI',
                                                                                    "estado"        => 'Activo'));
            //Validación para enviar el tipo de negocio cuando el producto sea telcohome
            if($strEsIsb == "SI")
            {
                if(!is_object($objServicio->getProductoId()))
                {
                    throw new \Exception("No existe producto asociado al servicio.");
                }
                $objProducto                = $objServicio->getProductoId();
                $strNombreTecnicoProdPref   = $objProducto->getNombreTecnico();
                
                    if($strNombreTecnicoProdPref === "TELCOHOME")
                    {
                        $strTipoNegocio         = "HOME";
                    }
                    else
                    {
                        $strTipoNegocio         = "PYME";
                    }
            }
            else
            {
                $strTipoNegocio = $objPunto->getTipoNegocioId()->getNombreTipoNegocio();
            }
            
            //Fin de validación para enviar el tipo de negocio cuando el producto sea telcohome

            if(!is_object($objDetalleOltMiddleware))
            {
                throw new \Exception("El olt actual no posee el detalle MIDDLEWARE.");
            }
            $intIdOltAnterior           = $objServicioTecnico->getElementoId();
            $intIdInterfaceOltAnterior  = $objServicioTecnico->getInterfaceElementoId();
            $objInterfaceOltAnterior    = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                  ->find($intIdInterfaceOltAnterior);

            $objElContenedorAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                ->find($objServicioTecnico->getElementoContenedorId());

            $objInterfaceElConectorAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                    ->find($objServicioTecnico->getInterfaceElementoConectorId());

            $objOltAnterior             = $objInterfaceOltAnterior->getElementoId();
            if($objOltAnterior->getId() !== $intIdOltAnterior)
            {
                throw new \Exception("La interface no pertenece al olt actual.");
            }
            
            $objInterfaceOltNuevo   = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                              ->findOneById($intIdInterfaceOltNuevo);
            $objOltNuevo            = $objInterfaceOltNuevo->getElementoId();
            
            $intIdElementoOltNuevo  = $objOltNuevo->getId();

            $strInterfaceOltNuevo  = $objInterfaceOltNuevo->getNombreInterfaceElemento();

            $objElementoClienteOnt  = $this->emComercial->getRepository('schemaBundle:InfoElemento')
                                                        ->find($objServicioTecnico->getElementoClienteId());
            if(!is_object($objElementoClienteOnt))
            {
                throw new \Exception("No existe el ont del cliente.");
            }
            $strSerieOnt    = $objElementoClienteOnt->getSerieFisica();

            $objSpcMacOnt = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "MAC ONT", $objProductoInternet);            
            if(!is_object($objSpcMacOnt))
            {
                throw new \Exception("No existe mac del ont.");
            }
            
            //Se verifican todas las características asociadas al servicio con olt ZTE
            $objSpcIndiceClienteAnterior    = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "INDICE CLIENTE",
                                                                                                        $objProductoInternet);
            if(!is_object($objSpcIndiceClienteAnterior))
            {
                throw new \Exception("No existe Característica INDICE CLIENTE.");
            }

            $strOntId = $objSpcIndiceClienteAnterior->getValor();

            $objSpcSpidAnterior = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "SPID", $objProductoInternet);
            if(!is_object($objSpcSpidAnterior))
            {
                throw new \Exception("No existe característica SPID.");
            }
            
            $objSpcVlanAnterior = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "VLAN",
                                                                                            $objProductoInternet);
            if(!is_object($objSpcVlanAnterior))
            {
                throw new \Exception("No existe Característica VLAN.");
            }

            $strSpid = $objSpcVlanAnterior->getValor();

            $objSpcClientClassAnterior  = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "CLIENT CLASS",
                                                                                                    $objProductoInternet);
            if(!is_object($objSpcClientClassAnterior) && !$boolRedGponMpls)
            {
                throw new \Exception("No existe Característica CLIENT CLASS.");
            }
            
            $objSpcPackageIdAnterior    = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "PACKAGE ID",
                                                                                                    $objProductoInternet);
            if(!is_object($objSpcPackageIdAnterior) && !$boolRedGponMpls)
            {
                throw new \Exception("No existe Característica PACKAGE ID.");
            }

            $objSpcLineProfileNameAnterior  = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "LINE-PROFILE-NAME",
                                                                                                        $objProductoInternet);
            if(!is_object($objSpcLineProfileNameAnterior))
            {
                throw new \Exception("No existe Característica LINE-PROFILE-NAME.");
            }

            $intLineProfile = $objSpcLineProfileNameAnterior->getValor();
            
            $objSpcCapacidad1 = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "CAPACIDAD1", $objProductoInternet);
            if(!is_object($objSpcCapacidad1) && !$boolRedGponMpls)
            {
                throw new \Exception("No existe Característica CAPACIDAD1.");
            }
            
            $objSpcCapacidad2 = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "CAPACIDAD2", $objProductoInternet);
            if(!is_object($objSpcCapacidad2) && !$boolRedGponMpls)
            {
                throw new \Exception("No existe Característica CAPACIDAD2.");
            }

            //Se valida otras caracteristicas de olt servcios principal datos gpon
            if($boolRedGponMpls && $objServicio->getProductoId()->getNombreTecnico() === "DATOS SAFECITY")
            {
                $objSpcGemPort = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "GEM-PORT", $objProductoInternet);
                if(!is_object($objSpcGemPort))
                {
                    throw new \Exception("No existe Característica GEM-PORT.");
                }
                $intGemport = $objSpcGemPort->getValor();

                $objSpcTraffic = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "TRAFFIC-TABLE", $objProductoInternet);
                if(!is_object($objSpcTraffic))
                {
                    throw new \Exception("No existe Característica TRAFFIC-TABLE.");
                }
                $intTraffic = $objSpcTraffic->getValor();

                $arrayDatosGpon = array('servicio' => $objServicio,
                                        'producto' => $objProductoInternet,
                                        'vlan'     => $objSpcVlanAnterior->getValor());
                $arrayRespCaract[] = $this->getCaracteristicasDatosGpon($arrayDatosGpon);
                
                if($arrayRespCaract[0]['status'] === 200 && count($arrayRespCaract[0]['result']) > 0)
                {
                    $strTnCont              = $arrayRespCaract[0]['result']['TnContDatos'];
                    $strTnMappingMonitoreo  = $arrayRespCaract[0]['result']['MaMonitoreo'];
                    $strVlanOnt             = $arrayRespCaract[0]['result']['vlanDatos'];
                }
                else
                {
                    $arrayRespCaract[] = array('status' => $arrayRespCaract[0]['status'], 
                    'mensaje' => $arrayRespCaract[0]['mensaje'].$servicio->getId());
                    
                    return $arrayRespCaract;
                }
            }
            
            //Se verifica si existe el producto ip dentro del plan
            $objPlanCabAnterior = $objServicio->getPlanId();
            if(is_object($objPlanCabAnterior))
            {
                $objPlanDetAnterior = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                        ->findBy(array("planId" => $objPlanCabAnterior->getId()));

                for($intIndex = 0; $intIndex < count($objPlanDetAnterior); $intIndex++)
                {
                    for($intIndexJ = 0; $intIndexJ < count($arrayProdIp); $intIndexJ++)
                    {
                        if($objPlanDetAnterior[$intIndex]->getProductoId() == $arrayProdIp[$intIndexJ]->getId())
                        {
                            $objProdIpEnPlan    = $arrayProdIp[$intIndexJ];
                            $intNumIpEnPlan     = 1;
                            break;
                        }
                    }
                }
            }
            if ($strPrefijoEmpresa === "MD" && $strTipoNegocio === "PYME" && $intNumIpEnPlan === 0)
            {
                //OBTENER IPS ADICIONALES
                $arrayParametrosIpWan = array('objPunto'       => $objServicio->getPuntoId(),
                                              'strEmpresaCod'  => $strCodEmpresa,
                                              'strUsrCreacion' => $strUsrCreacion,
                                              'strIpCreacion'  => $strIpCreacion);
                $arrayDatosIpWan      = $this->servicioGeneral
                                             ->getIpFijaWan($arrayParametrosIpWan);
                if (isset($arrayDatosIpWan['strStatus']) && !empty($arrayDatosIpWan['strStatus']) && 
                    $arrayDatosIpWan['strStatus'] === 'OK' && isset($arrayDatosIpWan['strExisteIpWan']) &&
                    !empty($arrayDatosIpWan['strExisteIpWan']) &&  $arrayDatosIpWan['strExisteIpWan'] === 'SI')
                {
                    $strExisteIpWan = $arrayDatosIpWan['strExisteIpWan'];
                    $intNumIpEnPlan = 1;
                }
            }
            
            //Se verifica si existen ips adicionales en el punto que no pertenezcan al servicio actual
            $arrayServiciosPunto    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                        ->findBy(array("puntoId" => $objPunto->getId()));
            $intContadorIpsFijas        = 0;
            $arrayServicioIpAdicionales = array();
            $arrayServiciValidate    = array();
            for($intIndex = 0; $intIndex < count($arrayServiciosPunto); $intIndex++)
            {
                $objServicioPunto = $arrayServiciosPunto[$intIndex];
                if( is_object($objServicioPunto) && ($objServicioPunto->getEstado() == "Activo" || $objServicioPunto->getEstado() == "In-Corte" ) 
                    && $objServicioPunto->getId() != $objServicio->getId())
                {
                    if(is_object($objServicioPunto->getPlanId()))
                    {
                        $objPlanCabServPunto    = $this->emComercial->getRepository('schemaBundle:InfoPlanCab')
                                                                    ->find($objServicioPunto->getPlanId()->getId());
                        $objPlanDetServPunto    = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                                    ->findBy(array("planId" => $objPlanCabServPunto->getId()));

                        for($intIndexJ = 0; $intIndexJ < count($objPlanDetServPunto); $intIndexJ++)
                        {
                            foreach($arrayProdIp as $objProductoIp)
                            {
                                if($objProductoIp->getId() == $objPlanDetServPunto[$intIndexJ]->getProductoId())
                                {
                                    $arrayServicioIpAdicionales[] = array("idServicio" => $objServicioPunto->getId());
                                    $intContadorIpsFijas++;
                                }
                            }
                        }
                    }
                    else
                    {
                        $objProductoServicioPunto = $objServicioPunto->getProductoId();
                        $intIdProdPref            = $objProductoServicioPunto->getId();
                        $arrayParametrosCaractIpWan = array( 'intIdProducto'         => $objProductoServicioPunto->getId(),
                                                             'strDescCaracteristica' => 'IP WAN',
                                                             'strEstado'             => 'Activo' );
                        $strValidaExisteIpWan = $this->serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaractIpWan);
                       
                        if($strValidaExisteIpWan === 'N' && $strEsIsb == "" && $strPrefijoEmpresa == "MD" && !empty($intProductoId)) 
                        {
                            foreach($arrayProdIp as $objProductoIp)
                            {
                                if($objProductoIp->getId() === $objProductoServicioPunto->getId())
                                {
                                    $arrayServicioIpAdicionales[]   = array("idServicio" => $objServicioPunto->getId());
                                    $intContadorIpsFijas++;
                                }
                            }
                        }
                        else if($strValidaExisteIpWan === 'N' && $strEsIsb == "" && $strPrefijoEmpresa == "MD")
                        {   
                            $arrayServicioCamaras[]    = $objServicioPunto;
                            $arrayServicioIpProducto[] = array("idServicio" => $objServicioPunto->getId());
                            array_push($arrayServiciValidate,$objServicioPunto->getId());
                            $intContadorIpsFijas++;
                            
                        }
                        else
                        {
                            if($strValidaExisteIpWan === 'N' && $strEsIsb == "SI" && $strPrefijoEmpresa == "TN" && !empty($intProductoId)) 
                            {
                                $strTipoNegocio         = "PYME";
                                $arrayParamsInfoProds   = array("strValor1ParamsProdsTnGpon"    => "PRODUCTOS_RELACIONADOS_INTERNET_IP",
                                                                "strCodEmpresa"                 => $strCodEmpresa,
                                                                "intIdProductoInternet"         => $intProductoId);
                                $arrayInfoMapeoProds    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                            ->obtenerParametrosProductosTnGpon($arrayParamsInfoProds);

                                if(isset($arrayInfoMapeoProds) && !empty($arrayInfoMapeoProds))
                                {
                                    foreach($arrayInfoMapeoProds as $arrayInfoProd)
                                    {
                                        $intIdProductoIp        = $arrayInfoProd["intIdProdIp"];
                                        $objProdIPSB            = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                       ->find($intIdProductoIp);
                                        $arrayProdIp[]          = $objProdIPSB;
                                    }
                                }
                                else
                                {
                                    $arrayRespuestaFinal[]  = array('status'    => 'ERROR', 
                                                 'mensaje'   => 'No se ha podido obtener el correcto mapeo del servicio con la ip respectiva');
                                    return $arrayRespuestaFinal;
                                }
                                
                                foreach($arrayProdIp as $objProductoIp)
                                {
                                    if($objProductoIp->getId() == $objProductoServicioPunto->getId())
                                    {
                                        $arrayServicioIpAdicionales[]   = array("idServicio" => $objServicioPunto->getId());
                                        $intContadorIpsFijas++;
                                    }
                                }
                                $intNumIpEnPlan = 1;
                            }
                        }

                        if($objProductoServicioPunto->getNombreTecnico() == "SAFECITYDATOS")  
                        {
                            $arrayServicioCamaras[]    = $objServicioPunto;
                            $arrayServicioIpProducto[] = array("idServicio" => $objServicioPunto->getId());
                            array_push($arrayServiciValidate,$objServicioPunto->getId());
                            $intContadorIpsFijas++;
                        }
                        else if($objProductoServicioPunto->getNombreTecnico() == 'SAFECITYWIFI')
                        {
                            array_push($arrayServiciValidate,$objServicioPunto->getId());
                        }
                    }
                }
            }
            
            if ($intNumIpEnPlan == 0 && $boolIsb)
            {
                $intNumIpEnPlan = 1;
            }

            //Se obtiene el número total de ips, tomando en cuenta la del plan y las adicionales
            $intNumTotalIpsPto = $intNumIpEnPlan + $intContadorIpsFijas;
            
            $objSpcServiceProfile = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "SERVICE-PROFILE", $objProductoInternet);
            if(!is_object($objSpcServiceProfile))
            {
                $objElementoClienteAnterior = $this->emComercial->getRepository('schemaBundle:InfoElemento')
                                                                ->find($objServicioTecnico->getElementoClienteId());
                if(is_object($objElementoClienteAnterior))
                {
                    $strServiceProfile = $objElementoClienteAnterior->getModeloElementoId()->getNombreModeloElemento();
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, $objProductoInternet, "SERVICE-PROFILE",
                                                                                    $strServiceProfile, $strUsrCreacion);
                }
                else
                {
                    throw new \Exception("No existe la característica SERVICE-PROFILE.");
                }
            }
            else
            {
                $strServiceProfile = $objSpcServiceProfile->getValor();
            }
            
            //Si el producto es Internet Small Business y si la ip es publica, consultar si tiene producto adicional, caso contrario
            //agregar un servicio adicional (IP Small Business)
            $boolCrearServicio = false;
                         
            if ($strNombreTecnicoProdPref === "INTERNET SMALL BUSINESS" 
                                                           && $strPrefijoEmpresa ==="TN"
                                                           && $boolIsb)
            {
                $intIdServicioIp = $objServicio->getId();
                //Obtiene tipo de ip por el servicio
                $objTipoIpOrigen = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                   ->findOneBy(array("servicioId"  =>  $intIdServicioIp,
                                                                                         "tipoIp"      =>  "FIJA",
                                                                                         "estado"      =>  "Activo"));
                if (is_object($objTipoIpOrigen))
                {
                    $strTipoIpOrigen = $objTipoIpOrigen->getTipoIp();
                }
                                              
                if ($strTipoIpOrigen === "FIJA")
                {
                    //Aprovisionamos con ip privada para el servicio Internet Small Business
                            
                            
                    $strTieneIps = "NO";
                            
                    $arrayProdIpIsb                = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                ->findBy(array( "descripcionProducto" => "IP Small Business",
                                                                                "nombreTecnico"       => "IPSB", 
                                                                                "empresaCod"          => "10",
                                                                                "estado"              => "Activo"));
                    if(empty($arrayProdIpIsb))
                    {
                        throw new \Exception("No existe el objeto del producto IP");
                    }
                    
                    //arreglo de los estados de los servicios permitidos
                    $arrayEstadosServiciosPermitidos = array();
                    //obtengo la cabecera de los estados de los servicios permitidos
                    $objAdmiParametroCabEstadosServ  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                                    array('nombreParametro' => 'ESTADOS_SERVICIOS_ISB_CAMBIO_PUERTO',
                                                                          'estado'          => 'Activo'));
                    if( is_object($objAdmiParametroCabEstadosServ) )
                    {
                        $arrayParametrosDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                                                    array(  "parametroId" => $objAdmiParametroCabEstadosServ->getId(),
                                                                            "estado"      => "Activo"));
                        foreach($arrayParametrosDet as $objParametro)
                        {
                            $arrayEstadosServiciosPermitidos[] = $objParametro->getValor1();
                        }
                    }
                            
                    $objProductoOrigen          = $objServicio->getProductoId();
                    $arrayServiciosPuntoOrigen  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                ->findBy(array( "puntoId" => $objServicio->getPuntoId()->getId(), 
                                                                                "estado"  => $arrayEstadosServiciosPermitidos));
                    
                    //Consultamos si tiene ips adicionales el servicio de origen
                    $arrayParametrosIsb = array("arrayServicios"                  => $arrayServiciosPuntoOrigen,
                                                "arrayProdIp"                     => $arrayProdIpIsb,
                                                "servicio"                        => $objTipoIpOrigen,
                                                "objProductoInternet"             => $objProductoOrigen,
                                                "estadoIp"                        => 'Activo',
                                                "arrayEstadosServiciosPermitidos" => $arrayEstadosServiciosPermitidos
                                                );
                            
                    //Consultamos si tiene ips adicionales el servicio de origen
                    $arrayDatosIpPyme   = $this->servicioGeneral->getInfoIpsFijaPuntoIsb($arrayParametrosIsb);
                    //Obtener la cantidad de ips adicionales
                    $intIpsFijasActivasPyme = $arrayDatosIpPyme['ip_fijas_activas'];
                    if($intIpsFijasActivasPyme > 0)
                    {
                        $strTieneIps = "SI";
                    }
                            
                    if ($strTieneIps === "NO")
                    {
                        $objProductoIp = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                            ->findOneBy(array(  "descripcionProducto"   => "IP Small Business",
                                                                                "empresaCod"            => $strCodEmpresa,
                                                                                "estado"                => "Activo"));
                        $intIdProdIp             = $objProductoIp->getId();
                        $strDescripcionProdIp    = $objProductoIp->getDescripcionProducto();
                        $strLoginVendedor        = $objServicio->getUsrVendedor();
                                
                        $objInfoPersona          = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                               ->findOneBy(array('login'=>$strLoginVendedor));
                        $strVendedor             = "";

                        if(is_object($objInfoPersona))
                        {
                            $strNombres   = ucwords(strtolower($objInfoPersona->getNombres()));
                            $strApellidos = ucwords(strtolower($objInfoPersona->getApellidos()));
                            $strVendedor  = $strNombres.' '.$strApellidos;
                            $intIdPersona = $objInfoPersona->getId();
                        }
                                
                        $objPersonaEmpresaRol   = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                        ->findBy(array(
                                                                                "personaId" => $intIdPersona
                                                                            ));
                            
                        $intIdPersonaRol = '';                                                    
                        foreach($objPersonaEmpresaRol as $objParametroRol)
                        {
                            $intIdEmpresaRol    = $objParametroRol->getEmpresaRolId()->getId();
                            //Consultamos si el id de la empresa_rol es de TN
                            $objEmpresaRol   = $this->emComercial->getRepository('schemaBundle:InfoEmpresaRol')
                                                               ->findOneBy(array(
                                                                                "id"         => $intIdEmpresaRol,
                                                                                "empresaCod" => $strCodEmpresa
                                                                            ));
                            if (is_object($objEmpresaRol))
                            {
                                $intIdPersonaRol = $intIdEmpresaRol;
                                break;
                            }
                                    
                        }
                            
                        if(empty($intIdPersonaRol))
                        {
                            throw new \Exception("el Id de la empresa rol no pertenece a la empresa TN");
                        }
                                
                        $objCaractVelocidad      = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                            ->findOneBy(array("descripcionCaracteristica" => 'VELOCIDAD', "estado" => "Activo"));
                        $objProdCaracVelocidad   = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                            ->findOneBy(array(  "productoId"        => $objServicio->getProductoId(),
                                                                                "caracteristicaId"  => $objCaractVelocidad->getId(),
                                                                                "estado"            => "Activo"));
                        $objSpcServicioVelocidad = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                   ->findOneBy(array(   "servicioId"                => 
                                                                                        $objServicio->getId(),
                                                                                        "productoCaracterisiticaId" =>
                                                                                        $objProdCaracVelocidad->getId(),
                                                                                        "estado"        => "Activo"));
                        $strVelocidad             = $objSpcServicioVelocidad->getValor();
                        $arrayProductoCaracteristicasValores['VELOCIDAD'] = $strVelocidad;
                        $strFuncionPrecio        = $objProductoIp->getFuncionPrecio();
                        $strPrecioVelocidad      = $this->evaluarFuncionPrecio($strFuncionPrecio, $arrayProductoCaracteristicasValores);
                                
                        $arrayPlantillaProductos  = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                               ->getResultadoComisionPlantilla( array('intIdProducto' => $intIdProdIp,
                                                                                                      'strCodEmpresa' => $strCodEmpresa) );
                        if (isset($arrayPlantillaProductos['objRegistros']) && !empty($arrayPlantillaProductos['objRegistros']))
                        {
                            foreach($arrayPlantillaProductos['objRegistros'] as $arrayItem)
                            {
                                if (isset($arrayItem['idComisionDet']) && !empty($arrayItem['idComisionDet']))
                                {
                                    $intIdComisionDet = (isset($arrayItem['idComisionDet']) && !empty($arrayItem['idComisionDet']))
                                                                ? $arrayItem['idComisionDet'] : 0;
                                }
                            }
                        }
                                
                        $strPlantillaComisionista = $intIdComisionDet.'---'.$intIdPersonaRol;
                                
                        //Se crea el servicio adicional para este producto
                        $arrayServicios = array();
                        $arrayServicios[0]['hijo']                          = 0;
                        $arrayServicios[0]['servicio']                      = 0;
                        $arrayServicios[0]['codigo']                        = $intIdProdIp;
                        $arrayServicios[0]['producto']                      = $strDescripcionProdIp.' '.$strVelocidad.' 0';
                        $arrayServicios[0]['cantidad']                      = '1';
                        $arrayServicios[0]['frecuencia']                    = '1';
                        $arrayServicios[0]['precio']                        = $strPrecioVelocidad;
                        $arrayServicios[0]['precio_total']                  = $strPrecioVelocidad;
                        $arrayServicios[0]['info']                          = 'C';
                        $arrayServicios[0]['caracteristicasProducto']       = $strCaractIsb;
                        $arrayServicios[0]['caractCodigoPromoIns']          = '';
                        $arrayServicios[0]['nombrePromoIns']                = '';
                        $arrayServicios[0]['idTipoPromoIns']                = '';
                        $arrayServicios[0]['caractCodigoPromo']             = '';
                        $arrayServicios[0]['nombrePromo']                   = '';
                        $arrayServicios[0]['idTipoPromo']                   = '';
                        $arrayServicios[0]['caractCodigoPromoBw']           = '';
                        $arrayServicios[0]['nombrePromoBw']                 = '';
                        $arrayServicios[0]['idTipoPromoBw']                 = '';
                        $arrayServicios[0]['strServiciosMix']               = '';
                        $arrayServicios[0]['tipoMedio']                     = '';
                        $arrayServicios[0]['backupDesc']                    = '';
                        $arrayServicios[0]['fecha']                         = '';
                        $arrayServicios[0]['precio_venta']                  = $strPrecioVelocidad;
                        $arrayServicios[0]['precio_instalacion']            = '0';
                        $arrayServicios[0]['descripcion_producto']          = $strDescripcionProdIp.' '.$strVelocidad.' 0';
                        $arrayServicios[0]['precio_instalacion_pactado']    = '0';
                        $arrayServicios[0]['ultimaMilla']                   = '107';
                        $arrayServicios[0]['um_desc']                       = 'FTTx';
                        $arrayServicios[0]['login_vendedor']                = $strLoginVendedor;
                        $arrayServicios[0]['nombre_vendedor']               = $strVendedor;
                        $arrayServicios[0]['strPlantillaComisionista']      = $strPlantillaComisionista;
                        $arrayServicios[0]['cotizacion']                    = '';
                        $arrayServicios[0]['cot_desc']                      = 'Ninguna';
                        $arrayServicios[0]['intIdPropuesta']                = '';
                        $arrayServicios[0]['strPropuesta']                  = '';
                                
                        $objPuntoDestino = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($objServicio
                                                                                                              ->getPuntoId()->getId());
                        $objRol   = null;

                        if (is_object($objPuntoDestino))
                        {
                            $objRol = $this->emComercial->getRepository('schemaBundle:AdmiRol')
                                                          ->find($objPuntoDestino->getPersonaEmpresaRolId()->getEmpresaRolId()->getRolId());
                        }
                        $arrayParamsServicio = array(   "codEmpresa"            => $strCodEmpresa,
                                                    "idOficina"             => $objSession->get('idOficina'),
                                                    "entityPunto"           => $objPuntoDestino,
                                                    "entityRol"             => $objRol,
                                                    "usrCreacion"           => $strUsrCreacion,
                                                    "clientIp"              => $strIpCreacion,
                                                    "tipoOrden"             => 'N',
                                                    "ultimaMillaId"         => null,
                                                    "servicios"             => $arrayServicios,
                                                    "strPrefijoEmpresa"     => $strPrefijoEmpresa,
                                                    "session"               => $objSession,
                                                    "intIdSolFlujoPP"       => $objSession->get('idSolFlujoPrePlanificacion') 
                                                                               ? $objSession->get('idSolFlujoPrePlanificacion') : 0
                                            );
                        $boolCrearServicio = true;
                    }
                }
            }

            //obtener la ip del olt anterior
            $objIpOltAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                        ->findOneBy(array("elementoId" => $objOltAnterior->getId()));


            //Se verifica si el cambio de línea pon se realiza entre diferentes olts
            if($objOltNuevo->getId() !== $objOltAnterior->getId())
            {
                $objIpOltNuevo      = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                              ->findOneBy(array("elementoId" => $objOltNuevo->getId()));
                $strIpOltNuevo      = $objIpOltNuevo->getIp();
                
                //Se verifica si el punto tiene ips ya sea del plan o adicionales para obtener nuevas ips con el scope del nuevo olt
                if($intNumTotalIpsPto > 0)
                {
                    $arrayServiciosPuntoActivo  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                    ->findBy(array("puntoId" => $objServicio->getPuntoId(), "estado" => "Activo"));
                    $arrayDatosIpCancelar       = $this->servicioGeneral->getInfoIpsFijaPunto($arrayServiciosPuntoActivo, $arrayProdIp, $objServicio,
                                                                                              'Activo', 'Activo', $objProductoInternet);
                    $arrayIpCancelar            = $arrayDatosIpCancelar['valores'];
                    $intIpsFijasActivas         = $arrayDatosIpCancelar['ip_fijas_activas'];
                    
                    if(!$boolRedGponMpls)
                    {
                        if(is_object($objServicio->getPlanId()))
                        {
                            $intIdPlanIpsDisponibleScopeOlt = $objServicio->getPlanId()->getId();
                        }
    
                        //Se obtienen ips nuevas con el scope del nuevo olt
                        $arrayIpsDisponibles    = $this->recursosRed->getIpsDisponibleScopeOlt( $intNumTotalIpsPto, 
                                                                                                $objOltNuevo->getId(), 
                                                                                                $objServicio->getId(), 
                                                                                                $objServicio->getPuntoId()->getId(), 
                                                                                                "SI", 
                                                                                                $intIdPlanIpsDisponibleScopeOlt);
                        //Si existe algún error al obtener las nuevas ips, se envía la respectiva notificación
                        if(!empty($arrayIpsDisponibles['error']))
                        {
                            $strAsunto      = "Notificación de errores al activar cambio de línea Pon";
                            $arrayParams    = array('login' => $objPunto->getLogin(),
                                                    'olt'   => $objOltNuevo->getNombreElemento(),
                                                    'error' => $arrayIpsDisponibles['error']);
                            $this->correo->generarEnvioPlantilla($strAsunto, null, 'ECLP', $arrayParams, '', '', '');
                            throw new \Exception($arrayIpsDisponibles['error']);
                        }
    
                        //Si el servicio tiene ip dentro del plan, se obtiene el scope actual y la ip del servicio
                        if($intNumIpEnPlan === 1)
                        {
                            if ($strExisteIpWan === "SI")
                            {
                                $arrayServicioIp[] = array("idServicio" => $arrayDatosIpWan['arrayInfoIp']['intIdServicioIp']);
                                $strIpActualPlan = $arrayDatosIpWan['arrayInfoIp']['strIp'];
                                $strScopeActual  = $arrayDatosIpWan['arrayInfoIp']['strScope'];
                            }
                            else 
                            {
                                $objIpFijaPlan      = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                              ->findOneBy(array("servicioId"    => $objServicio->getId(), 
                                                                                                "estado"        => "Activo"));
                                $strIpActualPlan    = $objIpFijaPlan->getIp();
                                $objSpcScopeActual  = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "SCOPE", $objProdIpEnPlan);
                                if(!is_object($objSpcScopeActual))
                                {
                                    $arrayScopeOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                             ->getScopePorIpFija($objIpFijaPlan->getIp(), 
                                                                                                 $objServicioTecnico->getElementoId());
                                    if(empty($arrayScopeOlt))
                                    {
                                        throw new \Exception("Ip Fija no pertenece a un Scope! <br>Favor Comunicarse con el Dep. Gepon!");
                                    }
                                    $strScopeActual = $arrayScopeOlt['NOMBRE_SCOPE'];
                                }
                                else
                                {
                                    $strScopeActual = $objSpcScopeActual->getValor();
                                }
                            }
                        }
                        $arrayIpsNuevas = $arrayIpsDisponibles['ips'];
                                            
                        //Se construye el arreglo de las nuevas ips que se desea activar
                        $intContadorIps = 0;
                        foreach($arrayIpsNuevas as $arrayIpDataNueva)
                        {
                            if($intContadorIps === 0 && $intNumIpEnPlan === 1)
                            {
                                if ($strExisteIpWan === "NO")
                                {
                                    //Se obtiene la ip nueva y el scope nuevo en caso de tener ip dentro del plan
                                    $objSpcMac = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "MAC ONT", $objProductoInternet);
                                    if(!is_object($objSpcMac))
                                    {
                                        $objSpcMac = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "MAC", $objProductoInternet);
                                        if(!is_object($objSpcMac))
                                        {
                                            $objSpcMac = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "MAC WIFI", 
                                                                                                                $objProductoInternet);
                                            if(!is_object($objSpcMac))
                                            {
                                                throw new \Exception("No existe Mac asociado a un Servicio, favor revisar!");
                                            }
                                        }
                                    }
                                }
                                $strIpNuevaPlan = $arrayIpDataNueva['ip'];
                                $strScopeNuevo  = $arrayIpDataNueva['scope'];
                            }
                            else
                            {
                                //Se obtiene la ip, mac y id servicio para las nuevas ips adicionales
                                $intContadorIpsAdicionales  = $intContadorIps - $intNumIpEnPlan;
                                $objServicioIp = $this->emInfraestructura->getRepository('schemaBundle:InfoServicio')
                                                                 ->find($arrayServicioIpAdicionales[$intContadorIpsAdicionales]['idServicio']);
                                $objSpcMac = $this->servicioGeneral
                                                  ->getServicioProductoCaracteristica($objServicioIp, "MAC ONT", $objProductoInternet);
                                if(!is_object($objSpcMac))
                                {
                                    $objSpcMac = $this->servicioGeneral
                                                      ->getServicioProductoCaracteristica($objServicioIp, "MAC", $objProductoInternet);
                                    if(!is_object($objSpcMac))
                                    {
                                        $objSpcMac = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioIp, "MAC WIFI", 
                                                                                                                   $objProductoInternet);
                                        if(!is_object($objSpcMac))
                                        {
                                            throw new \Exception("No existe Mac asociado a un Servicio, favor revisar!");
                                        }
                                    }
                                }
                                $strMacIpAdicional          = $objSpcMac->getValor();
                                $strIpAdicional             = $arrayIpDataNueva['ip'];
                                $intIdservicioIpAdicional   = $arrayServicioIpAdicionales[$intContadorIpsAdicionales]['idServicio'];
    
                                $arrayIpActivar[]           = array(
                                                                    'mac'           => $strMacIpAdicional,
                                                                    'ip'            => $strIpAdicional,
                                                                    'id_servicio'   => $intIdservicioIpAdicional
                                                                    );
                            }
                            $intContadorIps++;
                        }
                    }
                    
                }
            }
            else
            {
                if ($strNombreTecnicoProdPref === "INTERNET SMALL BUSINESS" 
                                                        && $strPrefijoEmpresa ==="TN"
                                                        && $boolIsb)
                {
                    if ($strTipoIpOrigen === "FIJA")
                    {
                        $objIpOltNuevo      = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                              ->findOneBy(array("elementoId" => $objOltNuevo->getId()));
                        $strIpOltNuevo      = $objIpOltNuevo->getIp();

                        if(is_object($objServicio->getPlanId()))
                        {
                            $intIdPlanIpsDisponibleScopeOlt = $objServicio->getPlanId()->getId();
                        }

                        //OBTENER IPS ADICIONALES A ACTIVAR---------------------------------------------------------------------------------
                        $arrayIpsDisponibles = $this->recursosRed->getIpsDisponibleScopeOlt( $intNumTotalIpsPto, 
                                                                                            $objOltNuevo->getId(), 
                                                                                            $objServicio->getId(), 
                                                                                            $objServicio->getPuntoId()->getId(), 
                                                                                            "SI", 
                                                                                            $intIdPlanIpsDisponibleScopeOlt);

                        //Si existe algún error al obtener las nuevas ips, se envía la respectiva notificación
                        if(!empty($arrayIpsDisponibles['error']))
                        {
                            $strAsunto      = "Notificación de errores al activar cambio de línea Pon";
                            $arrayParams    = array('login' => $objPunto->getLogin(),
                                                'olt'   => $objOltNuevo->getNombreElemento(),
                                                'error' => $arrayIpsDisponibles['error']);
                            $this->correo->generarEnvioPlantilla($strAsunto, null, 'ECLP', $arrayParams, '', '', '');
                            throw new \Exception($arrayIpsDisponibles['error']);
                        }
                            
                        //Si el servicio tiene ip dentro del plan, se obtiene el scope actual y la ip del servicio
                        if($intNumIpEnPlan === 1)
                        {
                            if ($strExisteIpWan === "SI")
                            {
                                $arrayServicioIp[] = array("idServicio" => $arrayDatosIpWan['arrayInfoIp']['intIdServicioIp']);
                                $strIpActualPlan = $arrayDatosIpWan['arrayInfoIp']['strIp'];
                                $strScopeActual  = $arrayDatosIpWan['arrayInfoIp']['strScope'];
                            }
                            else 
                            {
                                $objIpFijaPlan      = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                              ->findOneBy(array("servicioId"    => $objServicio->getId(), 
                                                                                                "estado"        => "Activo"));
                                $strIpActualPlan    = $objIpFijaPlan->getIp();
                                $objSpcScopeActual  = $this->servicioGeneral
                                                           ->getServicioProductoCaracteristica($objServicio, "SCOPE", $objProdIpEnPlan);
                                if(!is_object($objSpcScopeActual))
                                {
                                    $arrayScopeOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                             ->getScopePorIpFija($objIpFijaPlan->getIp(), 
                                                                                                 $objServicioTecnico->getElementoId());
                                    if(empty($arrayScopeOlt))
                                    {
                                        throw new \Exception("Ip Fija no pertenece a un Scope! <br>Favor Comunicarse con el Dep. Gepon!");
                                    }
                                    $strScopeActual = $arrayScopeOlt['NOMBRE_SCOPE'];
                                }
                                else
                                {
                                    $strScopeActual = $objSpcScopeActual->getValor();
                                }
                            }
                        }
                        $arrayIpsNuevas = $arrayIpsDisponibles['ips'];
                        
                        //Se construye el arreglo de las nuevas ips que se desea activar
                        $intContadorIps = 0;
                        foreach($arrayIpsNuevas as $arrayIpDataNueva)
                        {
                            if($intContadorIps === 0 && $intNumIpEnPlan === 1)
                            {
                                if ($strExisteIpWan === "NO")
                                {
                                    //Se obtiene la ip nueva y el scope nuevo en caso de tener ip dentro del plan
                                    $objSpcMac = $this->servicioGeneral
                                                      ->getServicioProductoCaracteristica($objServicio, "MAC ONT", $objProductoInternet);
                                    if(!is_object($objSpcMac))
                                    {
                                        $objSpcMac = $this->servicioGeneral
                                                          ->getServicioProductoCaracteristica($objServicio, "MAC", $objProductoInternet);
                                        if(!is_object($objSpcMac))
                                        {
                                            $objSpcMac = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "MAC WIFI", 
                                                                                                                $objProductoInternet);
                                            if(!is_object($objSpcMac))
                                            {
                                                throw new \Exception("No existe Mac asociado a un Servicio, favor revisar!");
                                            }
                                        }
                                    }
                                }
                                $strIpNuevaPlan = $arrayIpDataNueva['ip'];
                                $strScopeNuevo  = $arrayIpDataNueva['scope'];
                            }
                            else
                            {
                                //Se obtiene la ip, mac y id servicio para las nuevas ips adicionales
                                $intContadorIpsAdicionales  = $intContadorIps - $intNumIpEnPlan;
                                $objServicioIp = $this->emInfraestructura->getRepository('schemaBundle:InfoServicio')
                                                                   ->find($arrayServicioIpAdicionales[$intContadorIpsAdicionales]['idServicio']);
                                $objSpcMac = $this->servicioGeneral
                                                  ->getServicioProductoCaracteristica($objServicioIp, "MAC ONT", $objProductoInternet);
                                if(!is_object($objSpcMac))
                                {
                                    $objSpcMac = $this->servicioGeneral
                                                      ->getServicioProductoCaracteristica($objServicioIp, "MAC", $objProductoInternet);
                                    if(!is_object($objSpcMac))
                                    {
                                        $objSpcMac = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioIp, "MAC WIFI", 
                                                                                                               $objProductoInternet);
                                        if(!is_object($objSpcMac))
                                        {
                                            throw new \Exception("No existe Mac asociado a un Servicio, favor revisar!");
                                        }
                                    }
                                }
                                $strMacIpAdicional          = $objSpcMac->getValor();
                                $strIpAdicional             = $arrayIpDataNueva['ip'];
                                $intIdservicioIpAdicional   = $arrayServicioIpAdicionales[$intContadorIpsAdicionales]['idServicio'];

                                $arrayIpActivar[]           = array(
                                                                    'mac'           => $strMacIpAdicional,
                                                                    'ip'            => $strIpAdicional,
                                                                    'id_servicio'   => $intIdservicioIpAdicional
                                                                   );
                            }
                            $intContadorIps++;
                        }
                        $intIpsFijasActivas = $intNumTotalIpsPto - 1;
                    }
                    else
                    {
                        $strIpOltNuevo      = $objIpOltAnterior->getIp();
                        $intIpsFijasActivas = $intNumTotalIpsPto - 1;
                    }
                }
                else
                {
                    $strIpOltNuevo      = $objIpOltAnterior->getIp();
                	$intIpsFijasActivas = $intNumTotalIpsPto - 1;
                }
            }

            //Se realiza la petición al middleware
            if ($strPrefijoEmpresa == 'TN' && $boolRedGponMpls)
            {
                $arrayResponseCan = array();
                $strReutilizable  = ""; 
                $arrayParametrosGdaDatos = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne("NUEVA_RED_GPON_MPLS_TN",
                                                        "TECNICO",
                                                        "",
                                                        "PARAMETROS PARA WS de GDA - Cambio de linea pon",
                                                        "CAMBIO_LINEA_PON_DATOS_SERVICIOS",
                                                        "",
                                                        "",
                                                        "",
                                                        "",
                                                        $strCodEmpresa);

                $arrayParametrosGdaDatosNw = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne("NUEVA_RED_GPON_MPLS_TN",
                                                                "TECNICO",
                                                                "",
                                                                "PARAMETROS PARA WS de GDA - Cambio de linea pon",
                                                                "CAMBIO_LINEA_PON_DATOS_NW",
                                                                "",
                                                                "",
                                                                "",
                                                                "",
                                                                $strCodEmpresa);


                $arrayParametrosGdaDatosAdd = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne("NUEVA_RED_GPON_MPLS_TN",
                                                            "TECNICO",
                                                            "",
                                                            "PARAMETROS PARA WS de GDA - Cambio de linea pon",
                                                            "CAMBIO_LINEA_PON_DATOS_SERVICIOS_2",
                                                            "",
                                                            "",
                                                            "",
                                                            "",
                                                            $strCodEmpresa);
                foreach($arrayServiciosPunto as $objServiciosPunto)
                {

                    $objServicioEl = $objServiciosPunto;
                    $objProductoService = $objServicioEl->getProductoId();

                    if(($objProductoService->getNombreTecnico() == "SAFECITYDATOS"
                        || $objProductoService->getNombreTecnico() == "SAFECITYWIFI")
                       && ($objServicioEl->getEstado() == "Activo" || $objServicioEl->getEstado() == "In-Corte" ))
                    {
                        $objIpActual = null;
                        if($objProductoService->getNombreTecnico() == "SAFECITYDATOS")
                        {
                            //Obtenemos la subred y gateway de elmento actula
                            $arrayIpViejo = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                            ->getIpViejasPorServicio(array("idServicio" => $objServicioEl->getId()));
    
                            if(count($arrayIpViejo) > 0 && is_array($arrayIpViejo))
                            {
                                $objIpActual = $arrayIpViejo['objSubred'];
                            }
    
                            if($objOltNuevo->getId() != $objOltAnterior->getId())
                            {
                                
                                $arrayIps = $this->servicioGeneral
                                                    ->getIpDisponiblePorServicio(array( "objServicio"    => $objServicioEl,
                                                                                        "strCodEmpresa"  => $strCodEmpresa,
                                                                                        "strUsrCreacion" => $strUsrCreacion,
                                                                                        "strIpCreacion"  => $strIpCreacion,
                                                                                        "objNuevoOlt"    => $objOltNuevo,
                                                                                        "flag"           => "CLP"));
    
                                if($arrayIps['status'] != "OK")
                                {
                                    throw new \Exception($arrayIps['mensaje']);
                                }
                                $objSubredServicioAdd = $arrayIps['objSubred'];
                                $strIpServicioAdd     = $arrayIps['strIpServicio'];
                                                                        
                                //se graba la nuevas ips del servicio adicional de camaras
                                $objIpSerAdd = new InfoIp();
                                $objIpSerAdd->setElementoId($intIdElemento);
                                $objIpSerAdd->setIp($strIpServicioAdd);
                                $objIpSerAdd->setSubredId($objSubredServicioAdd->getId());
                                $objIpSerAdd->setServicioId($objServicioEl->getId());
                                $objIpSerAdd->setMascara($objSubredServicioAdd->getMascara());
                                $objIpSerAdd->setGateway($objSubredServicioAdd->getMascara());
                                $objIpSerAdd->setFeCreacion(new \DateTime('now'));
                                $objIpSerAdd->setUsrCreacion($strUsrCreacion);
                                $objIpSerAdd->setIpCreacion($strIpCreacion);
                                $objIpSerAdd->setEstado("Reservada");
                                $objIpSerAdd->setTipoIp("LAN");
                                $objIpSerAdd->setVersionIp("IPV4");
                                $this->emInfraestructura->persist($objIpSerAdd);
                                $this->emInfraestructura->flush();
    
                                $arrayIpActivar[] = array( "subRedId"      => $objSubredServicioAdd->getId(),
                                                        "ip"            => $arrayIps["strIpServicio"],
                                                        "id_servicio"   => $objServicioEl->getId(),
                                                        "esatdo"        => $objServicioEl->getEstado(),
                                                        "mascara"       => $objSubredServicioAdd->getMascara(),
                                                        "intIdSpcScope" => 0,
                                                        "vlan_datos"    => $arrayResponseCan['vlan'],
                                                        "gateway"       => $objSubredServicioAdd->getGateway(),
                                                        "productoId"    => $objProductoService->getId());
                            }
                        }
                        else if($objProductoService->getNombreTecnico() == "SAFECITYWIFI"
                                && $objOltNuevo->getId() != $objOltAnterior->getId())
                        {
                            //subred wifi
                            $strUsoSubredSsidServicio  = "";
                            $strUsoSubredAdminServicio = "";
                            $strMascaraSubred          = "";
                            $strEstadoSubredServicio   = "Activo";
                            $arrayParUsoSubredWifi     = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne("NUEVA_RED_GPON_TN",
                                                                    "COMERCIAL",
                                                                    "",
                                                                    "PARAMETRO USO SUBRED PARA SERVICIOS ADICIONALES SAFECITY",
                                                                    $objServicioEl->getProductoId()->getId(),
                                                                    "",
                                                                    "",
                                                                    "",
                                                                    "");
                            if(isset($arrayParUsoSubredWifi) && !empty($arrayParUsoSubredWifi)
                               && isset($arrayParUsoSubredWifi['valor2']) && !empty($arrayParUsoSubredWifi['valor2'])
                               && isset($arrayParUsoSubredWifi['valor3']) && !empty($arrayParUsoSubredWifi['valor3']))
                            {
                                $strUsoSubredSsidServicio  = $arrayParUsoSubredWifi['valor2'];
                                $strUsoSubredAdminServicio = $arrayParUsoSubredWifi['valor3'];
                                $strMascaraSubred          = $arrayParUsoSubredWifi['valor4'];
                                $strEstadoSubredServicio   = $arrayParUsoSubredWifi['valor5'] ? $arrayParUsoSubredWifi['valor5'] : "Activo";
                            }
                            else
                            {
                                throw new \Exception("No se ha podido obtener el uso de subred del producto ".
                                                     $objProductoService->getDescripcionProducto().
                                                     ", por favor notificar a Sistemas.");
                            }
                            //obtengo la subred ssid
                            $objSubredSsidServicioAnt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                    ->findOneBy(array("elementoId" => $objOltAnterior->getId(),
                                                                                      "uso"        => $strUsoSubredSsidServicio,
                                                                                      "mascara"    => $strMascaraSubred,
                                                                                      "estado"     => $strEstadoSubredServicio));
                            if(!is_object($objSubredSsidServicioAnt))
                            {
                                throw new \Exception("No se encontró la subred SSID anterior del servicio ".
                                                     $objServicioEl->getLoginAux()." para tipo de red GPON, ".
                                                     "por favor notificar a Sistemas.");
                            }
                            //obtengo la subred admin
                            $objSubredAdminServicioAnt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                    ->findOneBy(array("elementoId" => $objOltAnterior->getId(),
                                                                                      "uso"        => $strUsoSubredAdminServicio,
                                                                                      "mascara"    => $strMascaraSubred,
                                                                                      "estado"     => $strEstadoSubredServicio));
                            if(!is_object($objSubredAdminServicioAnt))
                            {
                                throw new \Exception("No se encontró la subred ADMIN anterior del servicio ".
                                                     $objServicioEl->getLoginAux()." para tipo de red GPON, ".
                                                     "por favor notificar a Sistemas.");
                            }
                            //obtengo la subred ssid
                            $objSubredSsidServicio = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                    ->findOneBy(array("elementoId" => $objOltNuevo->getId(),
                                                                                      "uso"        => $strUsoSubredSsidServicio,
                                                                                      "mascara"    => $strMascaraSubred,
                                                                                      "estado"     => $strEstadoSubredServicio));
                            if(!is_object($objSubredSsidServicio))
                            {
                                throw new \Exception("No se encontró la subred SSID nuevo del servicio ".
                                                     $objServicioEl->getLoginAux()." para tipo de red GPON, ".
                                                     "por favor notificar a Sistemas.");
                            }
                            //obtengo la subred admin
                            $objSubredAdminServicio = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                    ->findOneBy(array("elementoId" => $objOltNuevo->getId(),
                                                                                      "uso"        => $strUsoSubredAdminServicio,
                                                                                      "mascara"    => $strMascaraSubred,
                                                                                      "estado"     => $strEstadoSubredServicio));
                            if(!is_object($objSubredAdminServicio))
                            {
                                throw new \Exception("No se encontró la subred ADMIN nuevo del servicio ".
                                                     $objServicioEl->getLoginAux()." para tipo de red GPON, ".
                                                     "por favor notificar a Sistemas.");
                            }
                        }

                        $arrayResponseCan  = $this->getCaracteristicasCamaras($objServicioEl,$objProductoService);

                        if($arrayResponseCan['status'] == 'ERROR')
                        {
                            $arrayRespFinalCart = array();
                            $arrayRespFinalCart[] = array('status' => $arrayResponseCan['status'], 
                                                    'mensaje' => $arrayResponseCan['mensaje']);
                             return $arrayRespFinalCart;
                        }
                        else
                        {
                            $arrayResponseCan = $arrayResponseCan['result'];
                        }                         

                        if(is_object($objServicioEl))
                        {
                            $strPesonaEmpresaRolId     = $objServicioEl->getPuntoId()->getPersonaEmpresaRolId()->getId();

                            $objResultadoValidateVrf  = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                            ->getReutilizableRecursoRed(
                                                                array("idPerosnaEmpresaRol" => $strPesonaEmpresaRolId,
                                                                    "arrayServicios"      => $arrayServiciValidate,
                                                                    "elementoId"          => $objOltAnterior->getId(),
                                                                    "valor"               =>  $arrayResponseCan['vrfValor']));
                            if($objResultadoValidateVrf[0]['servicios'] > 1)
                            {
                                $strReutilizable = "S";
                            }
                            else
                            {
                                $strReutilizable = "N";
                            }
                            
                            $intContador = -1;
                            $intContador++;

                            $arrayDatosActivar = [];
                            $arrayDatosNw = [];
                            $arrayDatosNwCancelar = [];

                            if($objProductoService->getNombreTecnico() == "SAFECITYWIFI")
                            {
                                $arrayDatosActivar[$intContador] = array(
                                    'vlan_datos'          => $arrayResponseCan['vlanAdmin'],
                                    'gemport_datos'       => $arrayResponseCan['genPortDatosAdmin'],
                                    't_cont_datos'        => $arrayResponseCan['tContDatosAdmin'],
                                    'traffic_table_datos' => $arrayResponseCan['TTDatosAdmin'],
                                    'id_mapping_datos'    => $arrayResponseCan['idMappingDatos']);
                                $arrayDatosActivar[$intContador+1] = array(
                                    'vlan_datos'          => $arrayResponseCan['vlan'],
                                    'gemport_datos'       => $arrayResponseCan['genPortDatos'],
                                    't_cont_datos'        => $arrayResponseCan['tContDatos'],
                                    'traffic_table_datos' => $arrayResponseCan['TTDatos'],
                                    'id_mapping_datos'    => $arrayResponseCan['idMappingDatos']);
                            }
                            else
                            {
                                $arrayDatosActivar[$intContador] = array(
                                    'vlan_datos'          => $arrayResponseCan['vlan'],
                                    'gemport_datos'       => $arrayResponseCan['genPortDatos'],
                                    't_cont_datos'        => $arrayResponseCan['tContDatos'],
                                    'traffic_table_datos' => $arrayResponseCan['TTDatos'],
                                    'id_mapping_datos'    => $arrayResponseCan['idMappingDatos']);
                            }

                            if($objOltNuevo->getId() != $objOltAnterior->getId())
                            {
                                if($objProductoService->getNombreTecnico() == "SAFECITYWIFI")
                                {
                                    $arrayDatosNw[$intContador] = array(
                                        'opcion_NW'           => $arrayParametrosGdaDatosNw['valor3'],
                                        'opcion'              => $arrayParametrosGdaDatosNw['valor4'],
                                        'accion'              => $arrayParametrosGdaDatosNw['valor5'],
                                        'modulo'              => $arrayParametrosGdaDatosNw['valor6'],
                                        'esquema'             => $arrayParametrosGdaDatosAdd['valor2'],
                                        'bandEjecuta'         => $this->rdaBandEjecuta,
                                        'servicio'            => $arrayParametrosGdaDatosNw['valor2'],
                                        'ambiente'            => $this->rdaTipoEjecucion,
                                        'vrf'                 => $arrayResponseCan['vrfAdmin'],
                                        'rd'                  => $arrayResponseCan['rdAdmin'],
                                        'vlan'                => $arrayResponseCan['vlanAdmin'],
                                        'subred'              => is_object($objSubredAdminServicio)?$objSubredAdminServicio->getSubred():'',
                                        'gateway'             => is_object($objSubredAdminServicio)?$objSubredAdminServicio->getGateway():'');
                                    $arrayDatosNwCancelar[$intContador] = array(
                                        'opcion_NW'           => $arrayParametrosGdaDatosAdd['valor3'],
                                        'opcion'              => $arrayParametrosGdaDatosNw['valor4'],
                                        'accion'              => $arrayParametrosGdaDatosAdd['valor4'],
                                        'modulo'              => $arrayParametrosGdaDatosNw['valor6'],
                                        'esquema'             => $arrayParametrosGdaDatosAdd['valor2'],
                                        'bandEjecuta'         => $this->rdaBandEjecuta,
                                        'servicio'            => $arrayParametrosGdaDatosNw['valor2'],
                                        'ambiente'            => $this->rdaTipoEjecucion,
                                        'vrf'                 => $arrayResponseCan['vrfAdmin'],
                                        'rd'                  => $arrayResponseCan['rdAdmin'],
                                        'vlan'                => $arrayResponseCan['vlanAdmin'],
                                        'subred'              => is_object($objSubredAdminServicioAnt)?$objSubredAdminServicioAnt->getSubred():'',
                                        'gateway'             => is_object($objSubredAdminServicioAnt)?$objSubredAdminServicioAnt->getGateway():'',
                                        'reutilizada'         => $strReutilizable);
                                    $arrayDatosNw[$intContador+1] = array(
                                        'opcion_NW'           => $arrayParametrosGdaDatosNw['valor3'],
                                        'opcion'              => $arrayParametrosGdaDatosNw['valor4'],
                                        'accion'              => $arrayParametrosGdaDatosNw['valor5'],
                                        'modulo'              => $arrayParametrosGdaDatosNw['valor6'],
                                        'esquema'             => $arrayParametrosGdaDatosAdd['valor2'],
                                        'bandEjecuta'         => $this->rdaBandEjecuta,
                                        'servicio'            => $arrayParametrosGdaDatosNw['valor2'],
                                        'ambiente'            => $this->rdaTipoEjecucion,
                                        'vrf'                 => $arrayResponseCan['vrf'],
                                        'rd'                  => $arrayResponseCan['rd'],
                                        'vlan'                => $arrayResponseCan['vlan'],
                                        'subred'              => is_object($objSubredSsidServicio)?$objSubredSsidServicio->getSubred():'',
                                        'gateway'             => is_object($objSubredSsidServicio)?$objSubredSsidServicio->getGateway():'');
                                    $arrayDatosNwCancelar[$intContador+1] = array(
                                        'opcion_NW'           => $arrayParametrosGdaDatosAdd['valor3'],
                                        'opcion'              => $arrayParametrosGdaDatosNw['valor4'],
                                        'accion'              => $arrayParametrosGdaDatosAdd['valor4'],
                                        'modulo'              => $arrayParametrosGdaDatosNw['valor6'],
                                        'esquema'             => $arrayParametrosGdaDatosAdd['valor2'],
                                        'bandEjecuta'         => $this->rdaBandEjecuta,
                                        'servicio'            => $arrayParametrosGdaDatosNw['valor2'],
                                        'ambiente'            => $this->rdaTipoEjecucion,
                                        'vrf'                 => $arrayResponseCan['vrf'],
                                        'rd'                  => $arrayResponseCan['rd'],
                                        'vlan'                => $arrayResponseCan['vlan'],
                                        'subred'              => is_object($objSubredSsidServicioAnt)?$objSubredSsidServicioAnt->getSubred():'',
                                        'gateway'             => is_object($objSubredSsidServicioAnt)?$objSubredSsidServicioAnt->getGateway():'',
                                        'reutilizada'         => $strReutilizable);
                                }
                                else
                                {
                                    $arrayDatosNw[$intContador] = array(
                                        'opcion_NW'           => $arrayParametrosGdaDatosNw['valor3'],
                                        'opcion'              => $arrayParametrosGdaDatosNw['valor4'],
                                        'accion'              => $arrayParametrosGdaDatosNw['valor5'],
                                        'modulo'              => $arrayParametrosGdaDatosNw['valor6'],
                                        'esquema'             => $arrayParametrosGdaDatosAdd['valor2'],
                                        'bandEjecuta'         => $this->rdaBandEjecuta,
                                        'servicio'            => $arrayParametrosGdaDatosNw['valor2'],
                                        'ambiente'            => $this->rdaTipoEjecucion,
                                        'vrf'                 => $arrayResponseCan['vrf'],
                                        'rd'                  => $arrayResponseCan['rd'],
                                        'vlan'                => $arrayResponseCan['vlan'],
                                        'subred'              => is_object($objSubredServicioAdd)?$objSubredServicioAdd->getSubred():'',
                                        'gateway'             => is_object($objSubredServicioAdd)?$objSubredServicioAdd->getGateway():'');
                                    $arrayDatosNwCancelar[$intContador] = array(
                                        'opcion_NW'           => $arrayParametrosGdaDatosAdd['valor3'],
                                        'opcion'              => $arrayParametrosGdaDatosNw['valor4'],
                                        'accion'              => $arrayParametrosGdaDatosAdd['valor4'],
                                        'modulo'              => $arrayParametrosGdaDatosNw['valor6'],
                                        'esquema'             => $arrayParametrosGdaDatosAdd['valor2'],
                                        'bandEjecuta'         => $this->rdaBandEjecuta,
                                        'servicio'            => $arrayParametrosGdaDatosNw['valor2'],
                                        'ambiente'            => $this->rdaTipoEjecucion,
                                        'vrf'                 => $arrayResponseCan['vrf'],
                                        'rd'                  => $arrayResponseCan['rd'],
                                        'vlan'                => $arrayResponseCan['vlan'],
                                        'subred'              => is_object($objIpActual) ? $objIpActual->getSubred():'',
                                        'gateway'             => is_object($objIpActual) ? $objIpActual->getGateway():'',
                                        'reutilizada'         => $strReutilizable);
                                }
                            }
                            else
                            {
                                $arrayDatosNw = [];
                                $arrayDatosNwCancelar = [];
                            }

                            $arrayDatosServicios =  array( 
                                        'es_datos'            => $arrayParametrosGdaDatos['valor2'],
                                        'login_aux'           => $objServicioEl->getLoginAux(),
                                        'estado_servicio'     => $objServicioEl->getEstado(),
                                        'tiene_cpe'           => $arrayParametrosGdaDatos['valor3'],
                                        'puerto_ethernet'     => preg_replace('/[^0-9]/', '', $arrayResponseCan['puertoEthernet']),
                                        "numero_datos_activar"=> count($arrayDatosActivar),
                                        'vlan_ethernet'       => $arrayResponseCan['vlan'],
                                        'service_port'        => $arrayResponseCan['spid'], 
                                        'tipo_negocio_actual' => $arrayParametrosGdaDatos['valor4'],
                                        'datos_activar'       => $arrayDatosActivar,
                                        'datos_NW'            => $arrayDatosNw,
                                        'datos_NW_CANCELAR'   => $arrayDatosNwCancelar);
                                                                                        
                            $arrayDatosServiciosCan[] = $arrayDatosServicios;
                        }
                    }
                }                               
                
                //DATOS OLT PARA EL MIDDLEWARE 
                $arrayDatos = array(
                    'nombre_olt'            => $objOltAnterior->getNombreElemento(),
                    'ip_olt'                => $objIpOltAnterior->getIp(),
                    'puerto_olt'            => $objInterfaceOltAnterior->getNombreInterfaceElemento(),
                    'nombre_olt_nuevo'      => $objOltNuevo->getNombreElemento(),
                    'ip_olt_nuevo'          => $strIpOltNuevo,
                    'puerto_olt_nuevo'      => $strInterfaceOltNuevo,
                    'line_profile'          => $intLineProfile);

                //DATOS ONT PARA EL MIDDLEWARE
                $arrayDatosOnt[] = array(
                    'serial_ont'              => $strSerieOnt,
                    'mac_ont'                 => $objSpcMacOnt->getValor(),
                    'service_profile'         => $strServiceProfile,
                    'ont_id'                  => $strOntId,
                    'tiene_datos'             => $arrayParametrosGdaDatos['valor5'],
                    'tiene_internet'          => $arrayParametrosGdaDatos['valor6'],
                    'gemport_monitoreo'       => $intGemport,
                    'traffic_table_monitoreo' => $intTraffic,
                    't_cont_monitoreo'        => $strTnCont,
                    'id_mapping_monitoreo'    => $strTnMappingMonitoreo,
                    'vlan_monitoreo'          => $strVlanOnt,
                    'service_port_monitoreo'  => $strSpid, 
                    'datos_servicios'         => $arrayDatosServiciosCan);  
            }
            else
            {
                $arrayDatos             = array(
                                        'serial_ont'            => $strSerieOnt,
                                        'mac_ont'               => $objSpcMacOnt->getValor(),
                                        'nombre_olt'            => $objOltAnterior->getNombreElemento(),
                                        'ip_olt'                => $objIpOltAnterior->getIp(),
                                        'puerto_olt'            => $objInterfaceOltAnterior->getNombreInterfaceElemento(),
                                        'modelo_olt'            => $objOltAnterior->getModeloElementoId()->getNombreModeloElemento(),
                                        'gemport'               => '',
                                        'service_profile'       => $strServiceProfile,
                                        'line_profile'          => '',
                                        'traffic_table'         => '',
                                        'capacidad_up'          => $objSpcCapacidad1->getValor(),
                                        'capacidad_down'        => $objSpcCapacidad2->getValor(),
                                        'ont_id'                => $objSpcIndiceClienteAnterior->getValor(),
                                        'service_port'          => $objSpcSpidAnterior->getValor(),
                                        'estado_servicio'       => $objServicio->getEstado(),
                                        'ip_fijas_activas'      => $intIpsFijasActivas,
                                        'tipo_negocio_actual'   => $strTipoNegocio,
                                        'ip_olt_nuevo'          => $strIpOltNuevo,
                                        'modelo_olt_nuevo'      => $objOltNuevo->getModeloElementoId()->getNombreModeloElemento(),
                                        'puerto_olt_nuevo'      => $objInterfaceOltNuevo->getNombreInterfaceElemento(),
                                        'ip'                    => $strIpActualPlan,
                                        'scope'                 => $strScopeActual,
                                        'ip_nueva'              => $strIpNuevaPlan,
                                        'scope_nuevo'           => $strScopeNuevo,
                                        'ip_cancelar'           => $arrayIpCancelar,
                                        'ip_activar'            => $arrayIpActivar,
                                        'equipoOntDualBand'     => "",
                                        'tipoOrden'             => ""
                                    );
            }

            if ($strPrefijoEmpresa === 'MD')
            {
                $arrayRespuestaSeteaInfo = $this->servicioGeneral
                                                ->seteaInformacionPlanesPyme(array("intIdPlan"         => $objServicio->getPlanId()->getId(),
                                                                                   "intIdPunto"        => $objServicio->getPuntoId()->getId(),
                                                                                   "strConservarIp"    => "",
                                                                                   "strTipoNegocio"    => $strTipoNegocio,
                                                                                   "strPrefijoEmpresa" => $strPrefijoEmpresa,
                                                                                   "strUsrCreacion"    => $strUsrCreacion,
                                                                                   "strIpCreacion"     => $strIpCreacion,
                                                                                   "strTipoProceso"    => 'CAMBIAR_PUERTO',
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

            if (($strPrefijoEmpresa == 'TN' && $boolRedGponMpls)
                    && $objServicio->getProductoId()->getNombreTecnico() === "DATOS SAFECITY")
            {
                    $arrayDatosMiddleware = array(
                        'nombre_cliente'        => $strNombreCliente,
                        'login'                 => $objServicio->getPuntoId()->getLogin(),
                        'identificacion'        => $strIdentificacion,
                        'datos_generales'       => $arrayDatos,
                        'datos_por_ont'         => $arrayDatosOnt,
                        'opcion'                => $arrayParametrosGdaDatos['valor7'],
                        'ejecutaComando'        => $this->ejecutaComando,
                        'usrCreacion'           => $strUsrCreacion,
                        'ipCreacion'            => $strIpCreacion,
                        'comandoConfiguracion'  => $arrayParametrosGdaDatosNw['valor7'],
                        'empresa'               => $strPrefijoEmpresa);
                
            }
            else
            {
                $arrayDatosMiddleware   = array(
                    'empresa'               => $strPrefijoEmpresa,
                    'nombre_cliente'        => $strNombreCliente,
                    'login'                 => $objPunto->getLogin(),
                    'identificacion'        => $strIdentificacion,
                    'datos'                 => $arrayDatos,
                    'opcion'                => $this->opcion,
                    'ejecutaComando'        => $this->ejecutaComando,
                    'usrCreacion'           => $strUsrCreacion,
                    'ipCreacion'            => $strIpCreacion
                );
            }
                                            
            $arrayRespuesta         = $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));
            
            if($boolRedGponMpls)
            {
                $strMensajeStatus     = $arrayRespuesta['status'];
                $strMensajeOpcion     = $arrayRespuesta['opcion'];
                $strMensajeCan        = $arrayRespuesta['mensaje'];
                $strFlag = true;
            }
            else
            {
                $strStatusActivar       = $arrayRespuesta['status_activar'];
                $strStatusCancelar      = $arrayRespuesta['status_cancelar'];
                $strMensajeMiddleware   = '';
                $strFlag = false;
            }

            
            if(($strStatusActivar === 'OK' && $strStatusCancelar === 'OK') ||
                 ($strMensajeStatus === 'OK' ||  $strMensajeStatus === 'WARNING'))
            {
                if(!$strFlag)
                {
                    $arrayDatosConfirmacionTn                           = $arrayDatos;
                    $arrayDatosConfirmacionTn['line_profile']           = $objSpcLineProfileNameAnterior->getValor();
                    $arrayDatosConfirmacionTn['vlan']                   = $objSpcVlanAnterior->getValor();
                    $arrayDatosConfirmacionTn['opcion_confirmacion']    = $this->opcion;
                    $arrayDatosConfirmacionTn['respuesta_confirmacion'] = 'ERROR'; 
                    $arrayDataConfirmacionTn    = array('nombre_cliente'    => $strNombreCliente,
                                                        'login'             => $objPunto->getLogin(),
                                                        'identificacion'    => $strIdentificacion,
                                                        'datos'             => $arrayDatosConfirmacionTn,
                                                        'opcion'            => $this->strConfirmacionTNMiddleware,
                                                        'ejecutaComando'    => $this->ejecutaComando,
                                                        'usrCreacion'       => $strUsrCreacion,
                                                        'ipCreacion'        => $strIpCreacion,
                                                        'empresa'           => $strPrefijoEmpresa,
                                                        'statusMiddleware'  => 'OK');
                }
                
                if(($strMensajeStatus === 'OK' ||  $strMensajeStatus === 'WARNING') && $strFlag)
                {
                    $arrayDatosPorOnt        = $arrayRespuesta['datos_por_ont'][0];
                    $strSerialOnt            = $arrayDatosPorOnt['serial_ont'];
                    $strMacOntMonitoreo      = $arrayDatosPorOnt['mac_ont'];
                    $strOntIdMonitorio       = $arrayDatosPorOnt['ont_id'];
                    $strSpIdMonitorio        = $arrayDatosPorOnt['spId_Monitoreo'];
                    $strLineProfileMonitoreo = $arrayRespuesta['LINE_PROFILE'];
                    $arrayDatosActivacion    = $arrayDatosPorOnt['datos_activacion'];

                    //actualizacion de caracteristicas
                    //mac ont
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcMacOnt, "Eliminado");

                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, $objProductoInternet, "MAC ONT", 
                                                                                    $strMacOntMonitoreo, $strUsrCreacion);

                    //indice_cliente
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcIndiceClienteAnterior, "Eliminado");

                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, $objProductoInternet, "INDICE CLIENTE", 
                                                                                    $strOntIdMonitorio,$strUsrCreacion);

                    //spid
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcSpidAnterior, "Eliminado");
                                            
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, $objProductoInternet, "SPID", 
                                                                                    $strSpIdMonitorio,$strUsrCreacion);
                    //line_profile
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcLineProfileNameAnterior, "Eliminado");
                            
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, $objProductoInternet, "LINE-PROFILE-NAME", 
                                                                                    $strLineProfileMonitoreo, $strUsrCreacion);
                
                    if(is_array($arrayDatosActivacion))
                    {
                        foreach($arrayDatosActivacion as $arrayDatosActivacionSafecity)
                        {
                            $objServicioLoginAux = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                        ->findOneBy(array("loginAux" => $arrayDatosActivacionSafecity['login_aux'],
                                                                                          "estado"   => array("Activo","In-Corte")));

                            $objProductoLoginAux = $objServicioLoginAux->getProductoId();

                            //mac ont cam
                            $objMacOntCan = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioLoginAux, 
                                                                                            "MAC ONT", $objProductoLoginAux);
                            if(is_object($objMacOntCan))
                            {
                                $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objMacOntCan, "Eliminado");
                            }                                                  
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicioLoginAux, $objProductoLoginAux, 
                                                                                            "MAC ONT", $strMacOntMonitoreo, $strUsrCreacion);
                            //indice_cliente
                            $objIndiceClienteCan = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioLoginAux,
                                                                                            "INDICE CLIENTE", $objProductoLoginAux);
                        
                            if(is_object($objIndiceClienteCan))
                            {
                                $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objIndiceClienteCan, "Eliminado"); 
                            }                 
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicioLoginAux, $objProductoLoginAux, 
                                                                                            "INDICE CLIENTE", $strOntIdMonitorio, $strUsrCreacion);
                            //line_profile
                            $objLineProfileNameCan = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioLoginAux,
                                                                                            "LINE-PROFILE-NAME", $objProductoLoginAux);

                            if(is_object($objLineProfileNameCan))
                            {
                                $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objLineProfileNameCan, "Eliminado");
                            }                          
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicioLoginAux, $objProductoLoginAux, 
                                                                                            "LINE-PROFILE-NAME", $strLineProfileMonitoreo, 
                                                                                            $strUsrCreacion);
                            //service port
                            $objServicePidCan = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioLoginAux,
                                                                                                    "SPID", $objProductoLoginAux);

                            if(is_object($objServicePidCan))
                            {
                                $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objServicePidCan, "Eliminado");
                            }
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicioLoginAux, $objProductoLoginAux, 
                                                                                            "SPID",$arrayDatosActivacionSafecity['spId'][0],
                                                                                            $strUsrCreacion);
                            //service port admin
                            if($objProductoLoginAux->getNombreTecnico() == "SAFECITYWIFI")
                            {
                                $objServicePidAdminCan = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioLoginAux,
                                                                                                        "SPID ADMIN", $objProductoLoginAux);
                                if(is_object($objServicePidAdminCan))
                                {
                                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objServicePidAdminCan, "Eliminado");
                                }
                                $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicioLoginAux, $objProductoLoginAux, 
                                                                                                "SPID ADMIN",$arrayDatosActivacionSafecity['spId'][1],
                                                                                                $strUsrCreacion);
                            }
                        }
                    }
                    else
                    {
                        throw new \Exception('No se recibieron la nuevas caracteristicas SPID de camaras, favor notificar a sistemas.');
                    }
                }
                
                $this->cambiarPuertoLogicoMd(   $objServicio, 
                                                $objServicioTecnico, 
                                                $objOltNuevo->getId(), 
                                                $objInterfaceOltNuevo->getId(), 
                                                $intIdCajaNueva,
                                                $intIdSplitterNuevo, 
                                                $intIdInterfaceSplitterNuevo, 
                                                $strUsrCreacion, 
                                                $strIpCreacion,
                                                $strCodEmpresa);

                //Se elimina la ip anterior y se crea nueva ip en caso de que el cambio de línea pon se haya realizado en diferentes olt
                if(!empty($strIpNuevaPlan))
                {
                    if ($strExisteIpWan === "SI")
                    {
                        
                        $intIdServicioIp        = $arrayDatosIpWan['arrayInfoIp']['intIdServicioIp'];
                        
                        $objServicioIpAdicional = $this->emComercial
                                                       ->getRepository('schemaBundle:InfoServicio')
                                                       ->find($intIdServicioIp);
                        $objProdIpEnPlan        = $objServicioIpAdicional->getProductoId();
                        $objSpcScopeActual      = $this->servicioGeneral
                                                       ->getServicioProductoCaracteristica($objServicioIpAdicional, "SCOPE", $objProdIpEnPlan);
                        
                    }
                    else
                    {
                        $intIdServicioIp        = $objServicio->getId();
                        $objServicioIpAdicional = $objServicio;
                    }
                    $objIpPlanServicio = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                 ->findOneBy(array("servicioId" => $intIdServicioIp, "estado" => "Activo"));

                    
                    $arrayParametrosIp['intIdServicio'] = $intIdServicio;
                    $arrayParametrosIp['emComercial']   = $this->emComercial;
                    $arrayParametrosIp['emGeneral']     = $this->emGeneral;
                        
                    $strTipoIp = '';
                    if ($strPrefijoEmpresa === 'TN')
                    {
                        $strTipoIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                             ->getTipoIpServicio($arrayParametrosIp);
                    }
                    
                    //Si esta vacía la variable $strIp por default es Fija
                    if(empty($strTipoIp))
                    {
                        $strTipoIp = 'FIJA';
                    }
                    else
                    {
                        $strTipoIp = strtoupper($strTipoIp);
                    }
                    
                    if(is_object($objIpPlanServicio))
                    {
                        $objIpPlanServicio->setEstado("Eliminado");
                        $this->emInfraestructura->persist($objIpPlanServicio);
                        $this->emInfraestructura->flush();

                        $objIpNuevaPlan = new InfoIp();
                        $objIpNuevaPlan->setIp($strIpNuevaPlan);
                        $objIpNuevaPlan->setEstado("Activo");
                        $objIpNuevaPlan->setTipoIp($strTipoIp);
                        $objIpNuevaPlan->setVersionIp('IPV4');
                        $objIpNuevaPlan->setServicioId($intIdServicioIp);
                        $objIpNuevaPlan->setUsrCreacion($strUsrCreacion);
                        $objIpNuevaPlan->setFeCreacion(new \DateTime('now'));
                        $objIpNuevaPlan->setIpCreacion($strIpCreacion);
                        $this->emInfraestructura->persist($objIpNuevaPlan);
                        $this->emInfraestructura->flush();

                        if(is_object($objSpcScopeActual))
                        {
                            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcScopeActual, 'Eliminado');
                        }

                        $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicioIpAdicional, 
                                                                                        $objProdIpEnPlan, 
                                                                                        'SCOPE', 
                                                                                        $strScopeNuevo, 
                                                                                        $strUsrCreacion);
                    }
                }
                
                //Consulta si se debe crear servicio adicional
                if ($boolCrearServicio)
                {
                    $this->servicioInfoServicio->crearServicio($arrayParamsServicio);
                }

                /**
                 * Si existen ips adicionales nuevas se procede a eliminar las ips adicionales anteriores y la característica scope del servicio
                 * y además se crean las nuevas ips con su respectivo scope y se procede con la actualización del servicio técnico de cada 
                 * servicio ip
                 */
                if(count($arrayIpActivar) > 0)
                {
                    foreach($arrayRespuesta['ip_cancelar'] as $arrayRespuestaIpCancelar)
                    {
                        $strStatusIpCancelar = $arrayRespuestaIpCancelar['status'];

                        if($strStatusIpCancelar === 'OK')
                        {
                            $objIpAdicional = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                      ->findOneBy(array("servicioId"    => $arrayRespuestaIpCancelar['id_servicio'],
                                                                                        "estado"        => "Activo"));

                            $objIpAdicional->setEstado('Eliminado');
                            $this->emInfraestructura->persist($objIpAdicional);
                            $this->emInfraestructura->flush();

                            $objServicioIpAdicional = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                        ->find($arrayRespuestaIpCancelar['id_servicio']);
                            $objSpcScopeAdicional   = $this->servicioGeneral
                                                           ->getServicioProductoCaracteristica( $objServicioIpAdicional, "SCOPE", 
                                                                                                $objServicioIpAdicional->getProductoId());
                            if(is_object($objSpcScopeAdicional))
                            {
                                $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcScopeAdicional, 'Eliminado');
                            }
                        }
                    }
                    foreach($arrayRespuesta['ip_activar'] as $arrayRespuestaIpActivar)
                    {
                        $strStatusIpActivar = $arrayRespuestaIpActivar['status'];
                        if($strStatusIpActivar === 'OK')
                        {
                            $objIpNuevaAdicional = new InfoIp();
                            $objIpNuevaAdicional->setIp($arrayRespuestaIpActivar['ip']);
                            $objIpNuevaAdicional->setEstado("Activo");
                            $objIpNuevaAdicional->setTipoIp('FIJA');
                            $objIpNuevaAdicional->setVersionIp('IPV4');
                            $objIpNuevaAdicional->setServicioId($arrayRespuestaIpActivar['id_servicio']);
                            $objIpNuevaAdicional->setUsrCreacion($strUsrCreacion);
                            $objIpNuevaAdicional->setFeCreacion(new \DateTime('now'));
                            $objIpNuevaAdicional->setIpCreacion($strIpCreacion);
                            $this->emInfraestructura->persist($objIpNuevaAdicional);
                            $this->emInfraestructura->flush();

                            $objServicioIpAdicional = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                        ->find($arrayRespuestaIpActivar['id_servicio']);

                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicioIpAdicional, 
                                                                                            $objServicioIpAdicional->getProductoId(), 
                                                                                            'SCOPE', 
                                                                                            $strScopeNuevo, 
                                                                                            $strUsrCreacion);

                            $objServicioTecnicoIp   = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                        ->findOneByServicioId($arrayRespuestaIpActivar['id_servicio']);

                            if(is_object($objServicioTecnicoIp))
                            {
                                $objServicioTecnicoIp->setElementoId($objOltNuevo->getId());
                                $objServicioTecnicoIp->setInterfaceElementoId($objInterfaceOltNuevo->getId());
                                $objServicioTecnicoIp->setElementoContenedorId($intIdCajaNueva);
                                $objServicioTecnicoIp->setElementoConectorId($intIdSplitterNuevo);
                                $objServicioTecnicoIp->setInterfaceElementoConectorId($intIdInterfaceSplitterNuevo);
                                $this->emComercial->persist($objServicioTecnicoIp);
                                $this->emComercial->flush();
                            }
                        }
                    }

                    if($boolRedGponMpls && ($objOltNuevo->getId() != $objOltAnterior->getId()))
                    {
                        //ELIMINAR IPS ADICIONALES ANTERIORES  DE CAMARAS
                        foreach($arrayIpCancelar as $arrayRespuestaIpCancelar)
                        {                               
                            //ELIMINA IP ANTERIOR
                            $objIpAdicionalEliminar = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                        ->findOneBy(array(  "servicioId"    => $arrayRespuestaIpCancelar['id_servicio'], 
                                                                            "estado"        => "Activo"));

                            $objServicioCanE = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                            ->findOneById($objIpAdicionalEliminar->getServicioId());
                            
                            $objIpAdicionalEliminar->setEstado('Eliminado');
                            $this->emInfraestructura->persist($objIpAdicionalEliminar);
                            $this->emInfraestructura->flush();

                            $strObservacion = " <b style = 'color: red'>Se elimino la Ip LAN de anterior OLT</b> "."<br>".
                                                " <b>OLT anterior:</b> ".$objOltAnterior->getNombreElemento()."<br>".
                                                " <b>Ip LAN:</b> ".$objIpAdicionalEliminar->getIp()."<br>".
                                                " <b>Login Aux:</b> ".$objServicioCanE->getLoginAux()."<br>".
                                                " <b>Producto:</b> ".$objServicioCanE->getProductoId()->getDescripcionProducto();
                            
                            $objServicioHistorial = new InfoServicioHistorial();
                            $objServicioHistorial->setServicioId($objServicioCanE);
                            $objServicioHistorial->setEstado($objServicioCanE->getEstado());
                            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                            $objServicioHistorial->setIpCreacion($strIpCreacion);
                            $objServicioHistorial->setAccion("cambio de linea pon");           
                            $objServicioHistorial->setObservacion($strObservacion);
                            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                            $this->emComercial->persist($objServicioHistorial);
                            $this->emComercial->flush();

                            $arrayCorreo[] = array('loginAux'   => $objServicioCanE->getLoginAux(),
                                                    'ipAnterior' => $arrayRespuestaIpCancelar['ip'],
                                                    'ipNueva'    => $arrayRespuestaIpActivar['ip']);
                        }
                        
                        //ACTIVAR IPS ADICIONALES NUEVAS DE CAMARAS
                        foreach($arrayIpActivar as $arrayRespuestaIpActivar)
                        {                                    
                                //Activar IP ANTERIOR
                                $objIpAdicionalCan = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                ->findOneBy(array(  "servicioId"    => $arrayRespuestaIpActivar['id_servicio'], 
                                                    "estado"        => "Reservada"));
                                
                                $objIpAdicionalCan->setEstado('Activo');
                                $this->emInfraestructura->persist($objIpAdicionalCan);
                                $this->emInfraestructura->flush();                                                                     
                                        
                                $objServicioTecnicoIp = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                        ->findOneByServicioId($arrayRespuestaIpActivar['id_servicio']);

                                if($objServicioTecnicoIp)
                                {
                                    $objServicioTecnicoIp->setElementoId($objOltNuevo->getId());
                                    $objServicioTecnicoIp->setInterfaceElementoId($objInterfaceOltNuevo->getId());
                                    $objServicioTecnicoIp->setElementoContenedorId($intIdCajaNueva);
                                    $objServicioTecnicoIp->setElementoConectorId($intIdSplitterNuevo);
                                    $objServicioTecnicoIp->setInterfaceElementoConectorId($intIdInterfaceSplitterNuevo);
                                    $this->emComercial->persist($objServicioTecnicoIp);
                                    $this->emComercial->flush();
                                }

                                    //GUARDAR INFO SERVICIO HISTORIAL
                                    $strObservacion = " <b style = 'color: red'>Se activo la Ip LAN del nuevo OLT</b> "."<br>".
                                                        " <b>OLT nuevo:</b> ".$objOltNuevo->getNombreElemento()."<br>".
                                                        " <b>Ip LAN:</b> ".$arrayRespuestaIpActivar['ip']."<br>".
                                                        " <b>Login Aux:</b> ".$objServicioTecnicoIp->getServicioId()->getLoginAux()."<br>".
                                                        " <b>Producto:</b> ".$objServicioTecnicoIp->getServicioId()
                                                                         ->getProductoId()->getDescripcionProducto();

                                    $objServicioHistorial = new InfoServicioHistorial();
                                    $objServicioHistorial->setServicioId($objServicioTecnicoIp->getServicioId());
                                    $objServicioHistorial->setEstado($objServicioTecnicoIp->getServicioId()->getEstado());
                                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                                    $objServicioHistorial->setAccion("cambio de linea pon");
                                    $objServicioHistorial->setObservacion($strObservacion);
                                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                                    $this->emComercial->persist($objServicioHistorial);
                                    $this->emComercial->flush();
                        }
                    } 
                }
        
                //servicios de camara 
                if(count($arrayServicioCamaras) > 0 && is_array($arrayServicioCamaras))
                {
                    foreach($arrayServicioCamaras as $arrayCamaras)
                    {
                        $objServTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                            ->findOneBy(array("servicioId" => $arrayCamaras->getId()));
                                            
                        $objServTecnico->setInterfaceElementoId($objInterfaceOltNuevo->getId());
                        $objServTecnico->setElementoContenedorId($intIdCajaNueva);
                        $objServTecnico->setElementoConectorId($intIdSplitterNuevo);
                        $objServTecnico->setInterfaceElementoConectorId($intIdInterfaceSplitterNuevo);
                        $this->emComercial->persist($objServTecnico);
                        $this->emComercial->flush();

                        $objCajaNuevaSafe = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                            ->find($intIdCajaNueva);

                        $objInterfaceElementoSplitterNuevoSafe = $this->emInfraestructura
                                                        ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                        ->find($intIdInterfaceSplitterNuevo);

                        $objServicioHistorialSafe = new InfoServicioHistorial();
                        $objServicioHistorialSafe->setServicioId($arrayCamaras);
                        $objServicioHistorialSafe->setEstado($arrayCamaras->getEstado());
                        $objServicioHistorialSafe->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorialSafe->setIpCreacion($strIpCreacion);
                        $objServicioHistorialSafe->setAccion("cambio de linea pon");           
                        $objServicioHistorialSafe->setObservacion(" <b>Se hizo cambio de linea pon:</b>"."<br>".
                            "<b style = 'color: red'>OLT Anterior</b>"."<br>".
                            "<b>Elemento anterior : </b>".$objOltAnterior->getNombreElemento().
                            "<br> <b>Puerto anterior : </b> " . $objInterfaceOltAnterior->getNombreInterfaceElemento().
                            "<br> <b>Elemento conector anterior : </b> ".$objElContenedorAnterior->getNombreElemento().
                            "<br> <b>Interface elemento conector anterior : </b> ".
                            $objInterfaceElConectorAnterior->getNombreInterfaceElemento()."<br>".
                            "<b style = 'color: red'>OLT Actual</b>"."<br>".
                            "<b>Elemento actual :</b>".$objOltNuevo->getNombreElemento().
                            "<br> <b>Puerto actual :</b> " . $objInterfaceOltNuevo->getNombreInterfaceElemento().
                            "<br> <b>Elemento conector actual:</b> ".$objCajaNuevaSafe->getNombreElemento().
                            "<br> <b>Interface elemento conector actual:</b> ".
                            $objInterfaceElementoSplitterNuevoSafe->getNombreInterfaceElemento());
                        $objServicioHistorialSafe->setUsrCreacion($strUsrCreacion);
                        $this->emComercial->persist($objServicioHistorialSafe);
                        $this->emComercial->flush();
                    }

                    foreach($arrayServiciosPunto as $objServicosSafeCity)
                    {
                        if(($objServicosSafeCity->getProductoId()->getNombreTecnico() === "SAFECITYWIFI" ||
                            $objServicosSafeCity->getProductoId()->getNombreTecnico() === "SAFECITYSWPOE") &&
                            $objServicosSafeCity->getEstado() == "Activo")
                        {
                            $objServicioTecnicoSafeCity = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                    ->findOneBy(array("servicioId" => $objServicosSafeCity->getId()));
                            
                            $objServicioTecnicoSafeCity->setElementoId($objOltNuevo->getId());
                            $objServicioTecnicoSafeCity->setInterfaceElementoId($objInterfaceOltNuevo->getId());
                            $objServicioTecnicoSafeCity->setElementoContenedorId($intIdCajaNueva);
                            $objServicioTecnicoSafeCity->setElementoConectorId($intIdSplitterNuevo);
                            $objServicioTecnicoSafeCity->setInterfaceElementoConectorId($intIdInterfaceSplitterNuevo);
                            $this->emComercial->persist($objServicioTecnicoSafeCity);
                            $this->emComercial->flush();

                            $objCajaNuevaSafeCity = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                            ->find($intIdCajaNueva);

                            $objInterfaceElementoSplitterNuevoSafeCity = $this->emInfraestructura
                                                            ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                            ->find($intIdInterfaceSplitterNuevo);

                            $objServicioHistorialSafeCity = new InfoServicioHistorial();
                            $objServicioHistorialSafeCity->setServicioId($objServicosSafeCity);
                            $objServicioHistorialSafeCity->setEstado($objServicosSafeCity->getEstado());
                            $objServicioHistorialSafeCity->setFeCreacion(new \DateTime('now'));
                            $objServicioHistorialSafeCity->setIpCreacion($strIpCreacion);
                            $objServicioHistorialSafeCity->setAccion("cambio de linea pon");           
                            $objServicioHistorialSafeCity->setObservacion(" <b>Se hizo cambio de linea pon:</b>"."<br>".
                                "<b style = 'color: red'>OLT Anterior</b>"."<br>".
                                "<b>Elemento anterior : </b>".$objOltAnterior->getNombreElemento().
                                "<br> <b>Puerto anterior : </b> " . $objInterfaceOltAnterior->getNombreInterfaceElemento().
                                "<br> <b>Elemento conector anterior : </b> ".$objElContenedorAnterior->getNombreElemento().
                                "<br> <b>Interface elemento conector anterior : </b> ".
                                $objInterfaceElConectorAnterior->getNombreInterfaceElemento()."<br>".
                                "<b style = 'color: red'>OLT Actual</b>"."<br>".
                                "<b>Elemento actual :</b>".$objOltNuevo->getNombreElemento().
                                "<br> <b>Puerto actual :</b> " . $objInterfaceOltNuevo.
                                "<br> <b>Elemento conector actual:</b> ".$objCajaNuevaSafeCity->getNombreElemento().
                                "<br> <b>Interface elemento conector actual:</b> ".
                                $objInterfaceElementoSplitterNuevoSafeCity->getNombreInterfaceElemento());
                            $objServicioHistorialSafeCity->setUsrCreacion($strUsrCreacion);
                            $this->emComercial->persist($objServicioHistorialSafeCity);
                            $this->emComercial->flush();
                        }
                    }
                }

                if((is_object($objServicio) && $boolRedGponMpls) &&
                    ($objOltNuevo->getId() != $objOltAnterior->getId()) &&
                    (isset($arrayIpActivar) && !empty($arrayIpActivar)))
                {
                    $arrayRespuestaNoc   = array();
                    $arrayDatosPantilla  = array();
                    $arrayDatosGenrales  = array();
                    $arrayDataGeneral    = array();
                    $arrayDataTarea      = array();
                    $arrayRespuestaTarea = array();

                    $arrayDatosGenrales = array('usrCreacion'    => $strUsrCreacion,
                                                'ipCreacion'     => $strIpCreacion,
                                                'datosNoc'       => $arrayDataNoc,
                                                'switch'         => $objOltNuevo->getNombreElemento(),
                                                'servicioCan'    => $arrayIpActivar[0]['id_servicio'],
                                                'ipServicioCan'  => $arrayIpActivar[0]['ip'],
                                                'estadoServicio' => $objServicio->getEstado(),
                                                'ont'            => $objServicio->getPuntoId()->getLogin(),
                                                'modeloOnt'      => $strModeloOnt,
                                                'macOnt'         => $objSpcMacOnt->getValor(),
                                                'puertoOnt'      => $objInterfaceOltNuevo->getNombreInterfaceElemento());

                    $arrayRespuestaNoc  = $this->notificacionNocSafeCity($objServicio,$arrayDatosGenrales);

                    if($arrayRespuestaNoc['status'] == 200 && count($arrayRespuestaNoc['mensaje']) > 0)
                    {

                        $objPuntoMonitoreo   = $objServicio->getPuntoId();
                        $objCantonMonitoreo  = $objPuntoMonitoreo->getSectorId()->getParroquiaId()
                                                                ->getCantonId()->getNombreCanton();    
                        $arrayDatosPantilla = array('cliente'  => $strNombreCliente,
                                                    'login'    => $objServicio->getPuntoId()->getLogin(),
                                                    'ont'      => $objServicio->getPuntoId()->getLogin().'-ont',
                                                    'olt'      => $objOltAnterior->getNombreElemento(),
                                                    'ip_ant_1' => $arrayIpCancelar[0]['ip'],
                                                    'ip_ant_2' => $arrayIpCancelar[1]['ip'],
                                                    'ip_ant_3' => $arrayIpCancelar[2]['ip'],
                                                    'ip_ant_4' => $arrayIpCancelar[3]['ip'],
                                                    'ip_nuv_1' => $arrayIpActivar[0]['ip'],
                                                    'ip_nuv_2' => $arrayIpActivar[1]['ip'],
                                                    'ip_nuv_3' => $arrayIpActivar[2]['ip'],
                                                    'ip_nuv_4' => $arrayIpActivar[3]['ip']);


                        $arrayDataGeneral   = array('canton'  => $objCantonMonitoreo,
                                                    'empresa' => $strCodEmpresa);
                        
                        $this->notificacionCorreoSafeCity($arrayDatosPantilla, $arrayDataGeneral);

                        $arrayDataTarea     = array('usrCreacion'    => $strUsrCreacion,
                                                    'ipCreacion'     => $strIpCreacion,
                                                    'canton'         => $objCantonMonitoreo,
                                                    'cliente'        => $strNombreCliente,
                                                    'empresa'        => $strCodEmpresa);
                                                    
                        $arrayRespuestaTarea = $this->creacionTareaAutomaticaSafeCity($arrayDataTarea, $objPuntoMonitoreo);

                        if($arrayRespuestaTarea['status'] != "OK")
                        {
                            throw new \Exception($arrayRespuestaTarea['mensaje']);
                        }
                    }
                }

                if(!$boolRedGponMpls)
                {
                    //Se elimina característica INDICE CLIENTE anterior
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcIndiceClienteAnterior, "Eliminado");
                    //Se crea nueva característica INDICE CLIENTE
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                                    $objProductoInternet, 
                                                                                    "INDICE CLIENTE", 
                                                                                    $arrayRespuesta['ont_id'],
                                                                                    $strUsrCreacion);

                    //Se elimina característica SPID anterior
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcSpidAnterior, "Eliminado");
                    //Se crea nueva característica SPID
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                                    $objProductoInternet, 
                                                                                    "SPID",
                                                                                    $arrayRespuesta['spid'], 
                                                                                    $strUsrCreacion);
                }
      
            }
            else
            {
                if($strMensajeStatus == "500" || $strMensajeStatus === "ERROR" 
                    || $strMensajeStatus == 0)
                {
                        //IPS ADICIONALES
                        if(count($arrayIpActivar) > 0)
                        {
                            //ELIMINAR IPS ADICIONALES ANTERIORES
                            for($intIp = 0; $intIp < count($arrayIpActivar); $intIp++)
                            {
                                    $strIpsCancelar = $arrayIpActivar[$intIp];                          
                                    //ELIMINA IP ANTERIOR
                                    $objIpAdicional    = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                ->findOneBy(array(  "servicioId"    => $strIpsCancelar['id_servicio'], 
                                                                                    "estado"        => "Reservada"));

                                    $objIpAdicional->setEstado('Eliminado');
                                    $this->emInfraestructura->persist($objIpAdicional);
                                    $this->emInfraestructura->flush();
                            }
                        }
                        if(isset($arrayRespuesta['error']))
                        {
                            throw new \Exception($arrayRespuesta['error']);
                        }
                        else
                        {
                            throw new \Exception($strMensajeCan);
                        }
                }
                else
                {
                    $strMensajeMiddleware = "Cancelar: ".$arrayRespuesta['mensaje_cancelar'];
                    if($strStatusActivar === 'ERROR' && $strStatusCancelar === 'OK' && count($arrayIpActivar) > 0)
                    {
                        $strMensajeMiddleware = $strMensajeMiddleware." Ips Cancelar: ";
                        foreach($arrayRespuesta['ip_cancelar'] as $arrayRespuestaIpCancelar)
                        {
                            $strMensajeMiddleware = $strMensajeMiddleware . $arrayRespuestaIpCancelar['mensaje'] ." ";
                        }
                    }
                    $strMensajeMiddleware = $strMensajeMiddleware." Activar: ".$arrayRespuesta['mensaje_activar'];
                    throw new \Exception($strMensajeMiddleware);
                }                
            }
            //Se finaliza la solicitud de cambio de línea pon
            $objSolicitudCambioLineaPon->setObservacion('Se finaliza la solicitud por ejecución de cambio de línea pon');
            $objSolicitudCambioLineaPon->setEstado('Finalizada');
            $this->emComercial->persist($objSolicitudCambioLineaPon);
            $this->emComercial->flush();

            //Se agrega historial con la finalización de la solicitud
            $objDetalleSolHist = new InfoDetalleSolHist();
            $objDetalleSolHist->setDetalleSolicitudId($objSolicitudCambioLineaPon);
            $objDetalleSolHist->setObservacion('Se finaliza la solicitud por ejecución de cambio de línea pon');
            $objDetalleSolHist->setUsrCreacion($strUsrCreacion);
            $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
            $objDetalleSolHist->setEstado('Finalizada');
            $this->emComercial->persist($objDetalleSolHist);
            $this->emComercial->flush();
            
            $strStatus  = "OK";
            $strMensaje = "Se realizó el cambio de línea Pon";
            
            $this->emInfraestructura->commit();
            $this->emComercial->commit();
            $this->emInfraestructura->close();
            $this->emComercial->close();

            $arrayDataConfirmacionTn['datos']['respuesta_confirmacion'] = "OK";
        }
        catch(\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage()." Por favor comunicarse con el Dpto de Sistemas.";
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            $this->emInfraestructura->close();
            $this->emComercial->close();
            $this->utilServicio->insertError(   "Telcos+",
                                                "InfoCambiarPuertoService->cambiarPuertoScriptMdZte",
                                                $e->getMessage(),
                                                $strUsrCreacion,
                                                $strIpCreacion);
        }
        
        if ($strStatus == "OK" && !$boolRedGponMpls)
        {
            //EJECUTAR VALIDACIÓN DE PROMOCIONES BW
            $arrayParametrosInfoBw = array();
            $arrayParametrosInfoBw['intIdServicio']     = $objServicio->getId();
            $arrayParametrosInfoBw['intIdEmpresa']      = $strCodEmpresa;
            $arrayParametrosInfoBw['strTipoProceso']    = "CAMBIO_LINEA_PON";
            $arrayParametrosInfoBw['strValor']          = $intIdElementoOltNuevo;
            $arrayParametrosInfoBw['strUsrCreacion']    = $strUsrCreacion;
            $arrayParametrosInfoBw['strIpCreacion']     = $strIpCreacion;
            $arrayParametrosInfoBw['strPrefijoEmpresa'] = $strPrefijoEmpresa;
            $this->servicePromociones->configurarPromocionesBW($arrayParametrosInfoBw);
        }

        if(!$boolRedGponMpls)
        {
            $this->rdaMiddleware->ejecutaWsSinEsperarRespuestaMiddleware($arrayDataConfirmacionTn);
            $arrayResultado[] = array('status' => $strStatus, 'mensaje' => $strMensaje);
        }
        if($boolRedGponMpls)
        {
            $strMesajeFinal = $strMensajeCan;
            $arrayResultado[] = array('status' => "OK", 'mensaje' => $strMesajeFinal);
        }
        
        return $arrayResultado;
        
    }
    
    /**
     * Método que sirve para obtener los datos del PE del cliente por servicio y detalle de solicitud
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 06-12-2019
     *
     * @param array $arrayParametros [
     *                                  objServicio,           //objeto del servicio
     *                                  objDetalleSolicitud    //objeto del detalle de solicitud
     *                                  booleanEsPseudoPe,     //TRUE o FALSE
     *                                ]
     * @return array $arrayResultado [ vlan, mask, ip ]
     */
    public function getDatosPePorSolicitud($arrayParametros)
    {
        $objServicio            = $arrayParametros['objServicio'];
        $objSolicitud           = $arrayParametros['objDetalleSolicitud'];
        $booleanEsPseudoPe      = false;
        if( isset($arrayParametros['booleanEsPseudoPe']) && !empty($arrayParametros['booleanEsPseudoPe']) )
        {
            $booleanEsPseudoPe  = $arrayParametros['booleanEsPseudoPe'];
        }
        
        $objDetSolIpId  = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "IP_ID");
        $objIp          = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')->find($objDetSolIpId->getValor());
        if( is_object($objIp) )
        {
            $objSubredAnterior    = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                          ->find($objIp->getSubredId());
            $strVlanAnterior      = '';
            if( !$booleanEsPseudoPe )
            {
                $objSolCaracVlan     = $this->servicioGeneral->getInfoDetalleSolCaract($objSolicitud, "VLAN");
                $objPerEmpRolCarVlan = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                    ->find($objSolCaracVlan->getValor());
                $objDetalleElementoVlanAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                    ->find($objPerEmpRolCarVlan->getValor());
                if( is_object($objDetalleElementoVlanAnterior) )
                {
                    $strVlanAnterior = $objDetalleElementoVlanAnterior->getDetalleValor();
                }
            }
            else
            {
                $objServProdCaractVlanPseudoPe  = $this->servicioGeneral
                                                        ->getServicioProductoCaracteristica($objServicio,
                                                                                            'VLAN_PROVEEDOR',
                                                                                            $objServicio->getProductoId());
                if( is_object($objServProdCaractVlanPseudoPe) )
                {
                    $strVlanAnterior = $objServProdCaractVlanPseudoPe->getValor();
                }
            }
            
            return array(
                'vlan' => $strVlanAnterior,
                'mask' => $objSubredAnterior->getMascara(),
                'ip'   => $objSubredAnterior->getGateway()
            );
        }
        else
        {
            return array();
        }
    }
    
    /**
     * Método que sirve para obtener los datos del PE del cliente por servicio
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 06-12-2019
     *
     * @param array $arrayParametros [
     *                                  objServicio,           //objeto del servicio
     *                                  booleanEsPseudoPe,     //TRUE o FALSE
     *                                ]
     * @return array $arrayResultado [ vlan, mask, ip ]
     */
    public function getDatosPePorServicio($arrayParametros)
    {
        $objServicio            = $arrayParametros['objServicio'];
        $booleanEsPseudoPe      = false;
        if( isset($arrayParametros['booleanEsPseudoPe']) && !empty($arrayParametros['booleanEsPseudoPe']) )
        {
            $booleanEsPseudoPe  = $arrayParametros['booleanEsPseudoPe'];
        }
        $objIp      = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                         ->findOneBy(array("servicioId"    => $objServicio->getId(),
                                                           "estado"        => "Activo"));
        if( is_object($objIp) )
        {
            $objSubred  = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                            ->find($objIp->getSubredId());
            $strVlan    = '';
            if( !$booleanEsPseudoPe )
            {
                $objSolCaracVlan        = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "VLAN", 
                                                                                                    $objServicio->getProductoId());
                $objPerEmpRolCarVlan    = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                         ->find($objSolCaracVlan->getValor());
                $objDetalleElementoVlan = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                               ->find($objPerEmpRolCarVlan->getValor());
                if( is_object($objDetalleElementoVlan) )
                {
                    $strVlan     = $objDetalleElementoVlan->getDetalleValor();
                }
            }
            else
            {
                $objServProdCaractVlanPseudoPe  = $this->servicioGeneral
                                                        ->getServicioProductoCaracteristica($objServicio,'VLAN_PROVEEDOR',
                                                                                            $objServicio->getProductoId());
                if( is_object($objServProdCaractVlanPseudoPe) )
                {
                    $strVlan = $objServProdCaractVlanPseudoPe->getValor();
                }
            }
            return array(
                'vlan' => $strVlan,
                'mask' => $objSubred->getMascara(),
                'ip'   => $objSubred->getGateway()
            );
        }
        else
        {
            return array();
        }
    }

    /**
     * Documentación para el método 'reversarSolicitudCambioUM'.
     *
     * Método que sirve para reversar la solicitud de cambio de um
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 18-03-2020
     *
     * @param Array $arrayParametros [
     *                                  intIdServicio,    //id del servicio
     *                                  strObservacion,   //observación
     *                                  strUsrCreacion,   //nombre de usuario
     *                                  strIpCreacion     //ip de creación
     *                               ]
     *
     * @return String $strResultado
     */
    public function reversarSolicitudCambioUM($arrayParametros)
    {
        $intIdServicio          = $arrayParametros['intIdServicio'];
        $strObservacion         = $arrayParametros['strObservacion'];
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];
        $strIpCreacion          = $arrayParametros['strIpCreacion'];
        $strResultado           = "No se reverso la Solicitud de Cambio de UM";
        $arrayParametrosMismaUm = array();
        $strLoginesAux          = "";

        try
        {
            $this->emComercial->getConnection()->beginTransaction();
            $this->emInfraestructura->getConnection()->beginTransaction();

            //verifico si existe el servicio técnico
            $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                    ->findOneBy(array("servicioId" => $intIdServicio));
            if (!is_object($objServicioTecnico))
            {
                throw new \Exception("No existe información técnica para el servicio seleccionado, por favor notificar a Sistemas.");
            }

            //verifico si existe el servicio
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            if (!is_object($objServicio))
            {
                throw new \Exception("No existe información para el servicio seleccionado, por favor notificar a Sistemas.");
            }

            //variables para conexion a la base de datos mediante conexion OCI
            $arrayOciCon                     = array();
            $arrayOciCon['user_comercial']   = $this->container->getParameter('user_comercial');
            $arrayOciCon['passwd_comercial'] = $this->container->getParameter('passwd_comercial');
            $arrayOciCon['dsn']              = $this->container->getParameter('database_dsn');

            $arrayParametrosMismaUm['intPuntoId']                     = $objServicio->getPuntoId()->getId();
            $arrayParametrosMismaUm['intElementoId']                  = $objServicioTecnico->getElementoId();
            $arrayParametrosMismaUm['intInterfaceElementoId']         = $objServicioTecnico->getInterfaceElementoId();
            $arrayParametrosMismaUm['intElementoClienteId']           = $objServicioTecnico->getElementoClienteId();
            $arrayParametrosMismaUm['intInterfaceElementoClienteId']  = $objServicioTecnico->getInterfaceElementoClienteId();
            $arrayParametrosMismaUm['intUltimaMillaId']               = $objServicioTecnico->getUltimaMillaId();
            $arrayParametrosMismaUm['intTercerizadoraId']             = $objServicioTecnico->getTercerizadoraId();
            $arrayParametrosMismaUm['intElementoContenedorId']        = $objServicioTecnico->getElementoContenedorId();
            $arrayParametrosMismaUm['intElementoConectorId']          = $objServicioTecnico->getElementoConectorId();
            $arrayParametrosMismaUm['intInterfaceElementoConectorId'] = $objServicioTecnico->getInterfaceElementoConectorId();
            $arrayParametrosMismaUm['strTipoEnlace']                  = $objServicioTecnico->getTipoEnlace();
            $arrayParametrosMismaUm['ociCon']                         = $arrayOciCon;

            //verifico los servicios con la misma UM.
            $arrayParametrosRespuesta = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->getServiciosMismaUm($arrayParametrosMismaUm);
            if ($arrayParametrosRespuesta['strStatus'] == "ERROR")
            {
                $this->utilServicio->insertError('Telcos+',
                                                 'InfoCambiarPuertoService.reversarSolicitudCambioUM',
                                                  $arrayParametrosRespuesta['strMensaje'],
                                                  $strUsrCreacion,
                                                  $strIpCreacion);
                throw new \Exception("Error al recuperar servicios con la misma UM, por favor notificar a Sistemas.");
            }

            $objAdmiCaracTerce  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica' => 'TERCERIZADORA'));
            $objAdmiCaracFact   = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica' => 'TIPO_FACTIBILIDAD'));
            $objAdmiCaracTipo   = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica' => 'TIPO_CAMBIO_ULTIMA_MILLA'));
            $objAdmiCaracEle    = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica' => 'ELEMENTO_ID'));
            $objAdmiCaracInter  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica' => 'INTERFACE_ELEMENTO_ID'));
            $objAdmiCaracConte  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica' => 'ELEMENTO_CONTENEDOR_ID'));
            $objAdmiCaracEleCon = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica' => 'ELEMENTO_CONECTOR_ID'));
            $objAdmiCaracIntCon = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica' => 'INTERFACE_ELEMENTO_CONECTOR_ID'));
            $objAdmiCarEleClien = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica' => 'ELEMENTO_CLIENTE_ID'));
            $objAdmiCarIntClien = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica' => 'INTERFACE_ELEMENTO_CLIENTE_ID'));
            $objTipoSolicitud   = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                     ->findOneBy(array("descripcionSolicitud" => "SOLICITUD CAMBIO ULTIMA MILLA", "estado" => "Activo"));
            $objAdmiTipoRecurso = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica' => 'TIPO_RECURSO'));
            $arrayRegistrosServicios       = $arrayParametrosRespuesta['arrayRegistros'];

            //logins de los servicios con la misma UM.
            foreach($arrayRegistrosServicios as $strIdServicio)
            {
                $objInfoServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($strIdServicio);
                if( is_object($objInfoServicio) )
                {
                    $strLoginesAux = $strLoginesAux.$objInfoServicio->getLoginAux().' ';
                }
            }

            //seteo la observación
            if(!empty($strObservacion))
            {
                $strObservacion = 'Se reversa la solicitud de cambio de UM para los servicios: '.$strLoginesAux.
                                  '<br><b>Obs: </b>'.$strObservacion;
            }
            else
            {
                $strObservacion = 'Se reversa la solicitud de cambio de UM para los servicios: '.$strLoginesAux;
            }

            //reversar la solicitud de cambio de UM
            foreach($arrayRegistrosServicios as $strIdServicio)
            {
                $objInfoServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($strIdServicio);
                if( is_object($objInfoServicio) )
                {
                    $strTipoFactibilidad    = "";
                    $objProducto            = $objInfoServicio->getProductoId();
                    $objInfoServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->findOneBy(array("servicioId" => $objInfoServicio->getId()));
                    $objTipoMedio           = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                ->find($objInfoServicioTecnico->getUltimaMillaId());
                    //verifico si no existe el tipo de medio
                    if(!is_object($objTipoMedio))
                    {
                        throw new \Exception("No existe ultima milla en la información técnica de un servicio, por favor notificar a Sistemas.");
                    }
                    $strUltimaMilla         = $objTipoMedio->getNombreTipoMedio();
                    //si la ultima milla es Fibra Optica por default es RUTA
                    if($strUltimaMilla == "Fibra Optica")
                    {
                        $strTipoFactibilidad = "RUTA";
                    }

                    $objAdmiCaractTipoFact  = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                ->findOneBy(array("productoId"       => $objProducto->getId(),
                                                                  "caracteristicaId" => $objAdmiCaracFact->getId(),
                                                                  "estado"           => "Activo"));
                    //Si no existe la caracteristica mencionada se setea por default a Fibra Ruta
                    if(is_object($objAdmiCaractTipoFact))
                    {
                        $objServCarTipoFact = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                            ->findOneBy(array("servicioId"                => $objInfoServicio->getId(),
                                                              "productoCaracterisiticaId" => $objAdmiCaractTipoFact->getId(),
                                                              "estado"                    => 'Activo'));
                        if(is_object($objServCarTipoFact))
                        {
                            $strTipoFactibilidad = $objServCarTipoFact->getValor();
                        }
                    }

                    //obtengo la solicitud
                    $objDetalleSolicitud    = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                ->createQueryBuilder('p')
                                                                ->where('p.servicioId = :servicioId')
                                                                ->andWhere('p.tipoSolicitudId = :tipoSolicitudId')
                                                                ->andWhere("p.estado = :estadoOne OR ".
                                                                           "p.estado = :estadoTwo OR ".
                                                                           "p.estado = :estadoThree")
                                                                ->setParameter('servicioId', $objInfoServicio->getId())
                                                                ->setParameter('tipoSolicitudId', $objTipoSolicitud->getId())
                                                                ->setParameter('estadoOne','FactibilidadEnProceso')
                                                                ->setParameter('estadoTwo','AsignadoTarea')
                                                                ->setParameter('estadoThree','Asignada')
                                                                ->getQuery()
                                                                ->getOneOrNullResult();
                    if( is_object($objDetalleSolicitud) )
                    {
                        $strEstadoSolicitud = $objDetalleSolicitud->getEstado();
                        if( $strEstadoSolicitud == 'AsignadoTarea' || $strEstadoSolicitud == 'Asignada' )
                        {
                            $intIdInterfaceNew  = $objInfoServicioTecnico->getInterfaceElementoId();
                            $intIdCassetteNew   = $objInfoServicioTecnico->getElementoConectorId();
                            $intIdInterOutNew   = $objInfoServicioTecnico->getInterfaceElementoConectorId();
                            $intIdCajaNew       = $objInfoServicioTecnico->getElementoContenedorId();
                            $intEleClienteId    = $objInfoServicioTecnico->getElementoClienteId();
                            $intIdIntClienteNew = $objInfoServicioTecnico->getInterfaceElementoClienteId();

                            $objCaractElemento  = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                                      'caracteristicaId'   => $objAdmiCaracEle->getId()));
                            if(!is_object($objCaractElemento))
                            {
                                throw new \Exception("No existe la característica del elemento de la solicitud, ".
                                                     "por favor notificar a Sistemas.");
                            }
                            $objCaractInterface = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                                      'caracteristicaId'   => $objAdmiCaracInter->getId()));
                            if(!is_object($objCaractInterface))
                            {
                                throw new \Exception("No existe la característica de la interface del elemento de la solicitud, ".
                                                     "por favor notificar a Sistemas.");
                            }
                            $objCarTipoCambio   = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                                      'caracteristicaId'   => $objAdmiCaracTipo->getId()));
                            if(!is_object($objCarTipoCambio))
                            {
                                throw new \Exception("No existe la característica del tipo de cambio de la solicitud, ".
                                                     "por favor notificar a Sistemas.");
                            }
                            $objCarContenedor   = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                                      'caracteristicaId'   => $objAdmiCaracConte->getId()));
                            $objCaractConector  = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                                      'caracteristicaId'   => $objAdmiCaracEleCon->getId()));
                            $objCarIntConector  = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                                      'caracteristicaId'   => $objAdmiCaracIntCon->getId()));
                            $objCaractCliente   = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                                      'caracteristicaId'   => $objAdmiCarEleClien->getId()));
                            $objCaracIntCliente = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                                      'caracteristicaId'   => $objAdmiCarIntClien->getId()));

                            $strTipoCambio      = $objCarTipoCambio->getValor();
                            $intIdSwitch        = $objCaractElemento->getValor();
                            $intIdInterface     = $objCaractInterface->getValor();
                            if(is_object($objCarContenedor))
                            {
                                $intIdContenedor  = $objCarContenedor->getValor();
                            }
                            else
                            {
                                $intIdContenedor  = $intIdCajaNew;
                            }
                            if(is_object($objCaractConector))
                            {
                                $intIdConector  = $objCaractConector->getValor();
                            }
                            else
                            {
                                $intIdConector  = $intIdCassetteNew;
                            }
                            if(is_object($objCarIntConector))
                            {
                                $intIdIntConector = $objCarIntConector->getValor();
                            }
                            else
                            {
                                $intIdIntConector = $intIdInterOutNew;
                            }
                            if(is_object($objCaractCliente))
                            {
                                $intIdClienteAnt = $objCaractCliente->getValor();
                            }
                            else
                            {
                                $intIdClienteAnt = $intEleClienteId;
                            }
                            if(is_object($objCaracIntCliente))
                            {
                                $intIdIntCliente = $objCaracIntCliente->getValor();
                            }
                            else
                            {
                                $intIdIntCliente = $intIdIntClienteNew;
                            }

                            if($strUltimaMilla == "Radio")
                            {
                                //obtengo la característica de la tercerizadora
                                $objCaractTercerizadora = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                            ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                                              'caracteristicaId'   => $objAdmiCaracTerce->getId(),
                                                                              'estado'             => 'Asignada'));
                                if(is_object($objCaractTercerizadora))
                                {
                                    //actualiza el id tercerizadora del servicio técnico
                                    $objInfoServicioTecnico->setTercerizadoraId($objCaractTercerizadora->getValor());
                                    $this->emComercial->persist($objInfoServicioTecnico);
                                    $this->emComercial->flush();
                                }
                                else
                                {
                                    //verifico si no esta vació el id tercerizadora para setear a null
                                    $intIdTercerizadoraAnt = $objInfoServicioTecnico->getTercerizadoraId();
                                    if(!empty($intIdTercerizadoraAnt))
                                    {
                                        //seteo a null el id tercerizadora del servicio técnico
                                        $objInfoServicioTecnico->setTercerizadoraId(null);
                                        $this->emComercial->persist($objInfoServicioTecnico);
                                        $this->emComercial->flush();
                                    }
                                }
                                if($strTipoCambio == "MISMO_SWITCH" || $strTipoCambio == "MISMO_PE_MISMO_ANILLO")
                                {
                                    //recuperar interface esp del elemento conector antiguo
                                    $objIdInterfaceOutAnt  = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                 ->findOneBy((array('elementoId'              => $intIdConector,
                                                                                    'nombreInterfaceElemento' => 'esp1')));
                                    //recuperar interface esp del elemento cliente
                                    $objIdInterfaceOut     = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                ->findOneBy((array('elementoId'              => $intEleClienteId,
                                                                                   'nombreInterfaceElemento' => 'esp1')));
                                    if(!is_object($objIdInterfaceOutAnt))
                                    {
                                        throw new \Exception("No existe la interface esp del elemento del conector anterior, ".
                                                             "por favor notificar a Sistemas.");
                                    }
                                    if(!is_object($objIdInterfaceOut))
                                    {
                                        throw new \Exception("No existe la interface esp del elemento del cliente, ".
                                                             "por favor notificar a Sistemas.");
                                    }
                                    //activar enlace anterior
                                    $objEnlaceAnt   = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                         ->findOneBy(array("interfaceElementoIniId" => $objIdInterfaceOutAnt->getId(),
                                                                           "interfaceElementoFinId" => $objIdInterfaceOut->getId(),
                                                                           "estado"                 => "Eliminado"));
                                    if(is_object($objEnlaceAnt))
                                    {
                                        $objEnlaceAnt->setEstado("Activo");
                                        $this->emInfraestructura->persist($objEnlaceAnt);
                                        $this->emInfraestructura->flush();
                                    }
                                    //recuperar interface esp del elemento conector nuevo
                                    $objIdInterfaceOutNew   = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                   ->findOneBy((array('elementoId'              => $intIdCassetteNew,
                                                                                      'nombreInterfaceElemento' => 'esp1')));
                                    if(!is_object($objIdInterfaceOutNew))
                                    {
                                        throw new \Exception("No existe la interface esp del elemento del conector nuevo, ".
                                                             "por favor notificar a Sistemas.");
                                    }
                                    //eliminar enlace nuevo
                                    $objEnlaceNew   = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                        ->findOneBy(array("interfaceElementoIniId" => $objIdInterfaceOutNew->getId(),
                                                                          "interfaceElementoFinId" => $objIdInterfaceOut->getId(),
                                                                          "estado"                 => "Activo"));
                                    if(is_object($objEnlaceNew) && $intIdCassetteNew != $intIdConector)
                                    {
                                        $objEnlaceNew->setEstado("Eliminado");
                                        $this->emInfraestructura->persist($objEnlaceNew);
                                        $this->emInfraestructura->flush();
                                    }
                                    //actualizar el estado del nuevo pto del cassette a 'not connect'
                                    if($intIdCassetteNew != $intIdConector)
                                    {
                                        $objIdInterfaceOutNew->setEstado("not connect");
                                        $this->emInfraestructura->persist($objIdInterfaceOutNew);
                                        $this->emInfraestructura->flush();
                                    }
                                }
                                //actualizar los datos anteriores del servicio tecnico
                                if($strEstadoSolicitud == 'Asignada')
                                {
                                    $objInfoServicioTecnico->setElementoId($intIdSwitch);
                                    $objInfoServicioTecnico->setInterfaceElementoId($intIdInterface);
                                    $objInfoServicioTecnico->setElementoConectorId($intIdConector);
                                    $this->emComercial->persist($objInfoServicioTecnico);
                                    $this->emComercial->flush();
                                }
                            }
                            elseif($strUltimaMilla == "Fibra Optica" && $strTipoFactibilidad == "RUTA")
                            {
                                //verifico el estado de la solicitud y actualizo los valores
                                if($strEstadoSolicitud == 'AsignadoTarea')
                                {
                                    $intIdInterfaceNew  = $intIdInterface;
                                    $intIdCajaNew       = $intIdContenedor;
                                    $intIdCassetteNew   = $intIdConector;
                                    $intIdInterOutNew   = $intIdIntConector;
                                    $intIdSwitch        = $objInfoServicioTecnico->getElementoId();
                                    $intIdInterface     = $objInfoServicioTecnico->getInterfaceElementoId();
                                    $intIdContenedor    = $objInfoServicioTecnico->getElementoContenedorId();
                                    $intIdConector      = $objInfoServicioTecnico->getElementoConectorId();
                                    $intIdIntConector   = $objInfoServicioTecnico->getInterfaceElementoConectorId();
                                }
                                //actualizar el estado del nuevo pto del cassette a 'not connect'
                                $objInterfaceElementoConectorNuevo = $this->emInfraestructura
                                                                        ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                        ->find($intIdInterOutNew);
                                if(is_object($objInterfaceElementoConectorNuevo) && $intIdInterOutNew != $intIdIntConector)
                                {
                                    $objInterfaceElementoConectorNuevo->setEstado("not connect");
                                    $this->emInfraestructura->persist($objInterfaceElementoConectorNuevo);
                                    $this->emInfraestructura->flush();
                                }
                                if(!empty($intEleClienteId))
                                {
                                    $objElementoCliente = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                                ->find($intEleClienteId);
                                    if(is_object($objElementoCliente))
                                    {
                                        $strTipoElementoCli = $objElementoCliente->getModeloElementoId()->getTipoElementoId()
                                                                                                    ->getNombreTipoElemento();
                                    }
                                }
                                //Si no existe informacion de GIS ademas es fibra ruta y elemento cliente
                                //no es roseta se completa la data de bb de cliente
                                if( empty($intIdConector) && 
                                    (!empty($intEleClienteId) && isset($strTipoElementoCli) && $strTipoElementoCli != 'ROSETA') )
                                {
                                    //activar enlace anterior
                                    $objEnlaceAnt   = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                            ->findOneBy(array("interfaceElementoIniId" => $intIdInterface,
                                                                              "estado"                 => "Eliminado"));
                                    if(is_object($objEnlaceAnt))
                                    {
                                        $objEnlaceAnt->setEstado("Activo");
                                        $this->emInfraestructura->persist($objEnlaceAnt);
                                        $this->emInfraestructura->flush();
                                    }
                                    //conectar la interface del elemento anterior
                                    $objInterfaceElementoConectorAnterior = $this->emInfraestructura
                                                                                ->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                                ->find($intIdInterface);
                                    if(is_object($objInterfaceElementoConectorAnterior))
                                    {
                                        $objInterfaceElementoConectorAnterior->setEstado('connected');
                                        $this->emInfraestructura->persist($objInterfaceElementoConectorAnterior);
                                        $this->emInfraestructura->flush();
                                    }
                                }
                                elseif( ( $strEstadoSolicitud == 'Asignada' || $strTipoCambio == "MISMO_SWITCH" || 
                                          $strTipoCambio == "MISMO_PE_MISMO_ANILLO" ) && !empty($intIdConector)  )
                                {
                                    $objEnlaceAnt   = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                            ->findOneBy(array("interfaceElementoIniId" => $intIdIntConector,
                                                                              "estado"                 => "Eliminado"));
                                    $objEnlaceCrear = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                            ->findOneBy(array("interfaceElementoIniId" => $intIdInterOutNew,
                                                                              "estado"                 => "Activo"));
                                    if(is_object($objEnlaceAnt))
                                    {
                                        $objEnlaceAnt->setEstado("Activo");
                                        $this->emInfraestructura->persist($objEnlaceAnt);
                                        $this->emInfraestructura->flush();
                                    }
                                    if(is_object($objEnlaceCrear) && $intIdInterOutNew != $intIdIntConector)
                                    {
                                        $objEnlaceCrear->setEstado("Eliminado");
                                        $this->emInfraestructura->persist($objEnlaceCrear);
                                        $this->emInfraestructura->flush();
                                    }
                                    //conectar la interface del elemento anterior
                                    $objInterfaceElementoConectorAnterior = $this->emInfraestructura
                                                                         ->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                         ->find($intIdIntConector);
                                    if(is_object($objInterfaceElementoConectorAnterior))
                                    {
                                        $objInterfaceElementoConectorAnterior->setEstado('connected');
                                        $this->emInfraestructura->persist($objInterfaceElementoConectorAnterior);
                                        $this->emInfraestructura->flush();
                                    }
                                }
                                //actualizar los datos anteriores del servicio tecnico
                                if($strEstadoSolicitud == 'Asignada')
                                {
                                    $objInfoServicioTecnico->setElementoId($intIdSwitch);
                                    $objInfoServicioTecnico->setInterfaceElementoId($intIdInterface);
                                    $objInfoServicioTecnico->setElementoContenedorId($intIdContenedor);
                                    $objInfoServicioTecnico->setElementoConectorId($intIdConector);
                                    $objInfoServicioTecnico->setInterfaceElementoConectorId($intIdIntConector);
                                    if(!empty($intIdClienteAnt))
                                    {
                                        $objInfoServicioTecnico->setElementoClienteId($intIdClienteAnt);
                                    }
                                    if(!empty($intIdIntCliente))
                                    {
                                        $objInfoServicioTecnico->setInterfaceElementoClienteId($intIdIntCliente);
                                    }
                                    $this->emComercial->persist($objInfoServicioTecnico);
                                    $this->emComercial->flush();
                                }
                            }
                            else //Para UTP y FIBRA DIRECTA
                            {
                                if($strTipoCambio == "MISMO_SWITCH" || $strTipoCambio == "MISMO_PE_MISMO_ANILLO")
                                {
                                    //enlace anterior
                                    $objEnlaceAnt   = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                            ->findOneBy(array("interfaceElementoIniId" => $intIdInterface,
                                                                              "estado"                 => "Eliminado"));
                                    //enlace nuevo
                                    $objEnlaceCrear = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                            ->findOneBy(array("interfaceElementoIniId" => $intIdInterfaceNew,
                                                                              "estado"                 => "Activo"));
                                    //activar enlace anterior
                                    if(is_object($objEnlaceAnt))
                                    {
                                        $objEnlaceAnt->setEstado("Activo");
                                        $this->emInfraestructura->persist($objEnlaceAnt);
                                        $this->emInfraestructura->flush();
                                    }
                                    //eliminar enlace nuevo
                                    if(is_object($objEnlaceCrear) && $intIdInterfaceNew != $intIdInterface)
                                    {
                                        $objEnlaceCrear->setEstado("Eliminado");
                                        $this->emInfraestructura->persist($objEnlaceCrear);
                                        $this->emInfraestructura->flush();
                                    }
                                    //conectar la interface del elemento anterior
                                    $objInterfaceElementoConectorAnterior = $this->emInfraestructura
                                                                                ->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                                ->find($intIdInterface);
                                    if(is_object($objInterfaceElementoConectorAnterior))
                                    {
                                        $objInterfaceElementoConectorAnterior->setEstado('connected');
                                        $this->emInfraestructura->persist($objInterfaceElementoConectorAnterior);
                                        $this->emInfraestructura->flush();
                                    }
                                }
                                //verifico el estado de la solicitud y actualizo los valores
                                if($strEstadoSolicitud == 'AsignadoTarea')
                                {
                                    $intIdInterfaceNew  = $intIdInterface;
                                    $intIdInterface     = $objInfoServicioTecnico->getInterfaceElementoId();
                                }
                                //actualizar el estado del nuevo pto del cassette a 'not connect'
                                $objInterfaceElementoConectorNuevo = $this->emInfraestructura
                                                                        ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                        ->find($intIdInterfaceNew);
                                if(is_object($objInterfaceElementoConectorNuevo) && $intIdInterfaceNew != $intIdInterface)
                                {
                                    $objInterfaceElementoConectorNuevo->setEstado("not connect");
                                    $this->emInfraestructura->persist($objInterfaceElementoConectorNuevo);
                                    $this->emInfraestructura->flush();
                                }
                                //actualizar los datos anteriores del servicio tecnico
                                if($strEstadoSolicitud == 'Asignada')
                                {
                                    $objInfoServicioTecnico->setElementoId($intIdSwitch);
                                    $objInfoServicioTecnico->setInterfaceElementoId($intIdInterface);
                                    $this->emComercial->persist($objInfoServicioTecnico);
                                    $this->emComercial->flush();
                                }
                            }

                            //reversar asignación recursos de red
                            if($strEstadoSolicitud == 'Asignada' && $strTipoCambio != "MISMO_SWITCH" && $strTipoCambio != "MISMO_PE_MISMO_ANILLO")
                            {
                                //verificar si la asignación de recurso es nueva o existente
                                $strRecursosNuevos = 'N';
                                if( $objProducto->getNombreTecnico() == "L3MPLS" && is_object($objAdmiTipoRecurso) )
                                {
                                    $objCaractTipoRecurso = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                                                  'caracteristicaId'   => $objAdmiTipoRecurso->getId(),
                                                                                  'estado'             => 'Asignada'));
                                    if(is_object($objCaractTipoRecurso) && $objCaractTipoRecurso->getValor() == 'nuevos')
                                    {
                                        $strRecursosNuevos = 'S';
                                    }
                                }

                                //reversar recursos de red
                                $arrayParametrosReversar = array(
                                    'objInfoServicio'     => $objInfoServicio,
                                    'objProducto'         => $objProducto,
                                    'objDetalleSolicitud' => $objDetalleSolicitud,
                                    'strRecursosNuevos'   => $strRecursosNuevos,
                                    'strUsrCreacion'      => $strUsrCreacion,
                                    'strIpCreacion'       => $strIpCreacion,
                                );
                                $arrayResultadoReversar = $this->reversarAsignacionRecursosRedPorServicioSolicitud($arrayParametrosReversar);
                                if( $arrayResultadoReversar['status'] == 'ERROR' )
                                {
                                    throw new \Exception($arrayResultadoReversar['mensaje']);
                                }
                            }
                        }

                        //procedo a finalizar la solicitud
                        $objDetalleSolicitud->setEstado("Finalizada");
                        $this->emComercial->persist($objDetalleSolicitud);
                        $this->emComercial->flush();

                        //agregar historial a la solicitud
                        $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
                        $objDetalleSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
                        $objDetalleSolicitudHistorial->setIpCreacion($strIpCreacion);
                        $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                        $objDetalleSolicitudHistorial->setUsrCreacion($strUsrCreacion);
                        $objDetalleSolicitudHistorial->setObservacion($strObservacion);
                        $objDetalleSolicitudHistorial->setEstado("Finalizada");
                        $this->emComercial->persist($objDetalleSolicitudHistorial);
                        $this->emComercial->flush();
                    }

                    //agregar servicio historial
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objInfoServicio);
                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setEstado($objInfoServicio->getEstado());
                    $objServicioHistorial->setObservacion($strObservacion);
                    $this->emComercial->persist($objServicioHistorial);
                    $this->emComercial->flush();
                }
            }

            $this->emComercial->flush();
            $this->emInfraestructura->flush();
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->commit();
                $this->emComercial->getConnection()->close();
            }
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->commit();
                $this->emInfraestructura->getConnection()->close();
            }
            $strResultado = 'Se reversa la solicitud de cambio de UM para los servicios: <b>'.$strLoginesAux.'</b>';
        }
        catch (\Exception $e)
        {
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();
            }
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
                $this->emInfraestructura->getConnection()->close();
            }
            $this->utilServicio->insertError('Telcos+',
                                             'InfoCambiarPuertoService.reversarSolicitudCambioUM',
                                             $e->getMessage(),
                                             $strUsrCreacion,
                                             $strIpCreacion);
            $strResultado = $strResultado."<br>ERROR: ".$e->getMessage();
        }
        return $strResultado;
    }

    /**
     * Documentación para el método 'reversarSolicitudMigracionAnillo'.
     *
     * Método que sirve para reversar la solicitud de migración de anillo o vlan
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 23-03-2020
     *
     * @param Array $arrayParametros [
     *                                  intIdServicio,    //id del servicio
     *                                  strObservacion,   //observación
     *                                  strUsrCreacion,   //nombre de usuario
     *                                  strIpCreacion     //ip de creación
     *                               ]
     *
     * @return String $strResultado
     */
    public function reversarSolicitudMigracionAnillo($arrayParametros)
    {
        $intIdServicio          = $arrayParametros['intIdServicio'];
        $strObservacion         = $arrayParametros['strObservacion'];
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];
        $strIpCreacion          = $arrayParametros['strIpCreacion'];
        $strResultado           = "No se reverso la Solicitud de Migración Anillo";

        try
        {
            $this->emComercial->getConnection()->beginTransaction();
            $this->emInfraestructura->getConnection()->beginTransaction();

            //verifico si existe el servicio técnico
            $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                    ->findOneBy(array("servicioId" => $intIdServicio));
            if (!is_object($objServicioTecnico))
            {
                throw new \Exception("No existe información técnica para el servicio seleccionado, por favor notificar a Sistemas.");
            }

            //verifico si existe el servicio
            $objInfoServicio    = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            if (!is_object($objInfoServicio))
            {
                throw new \Exception("No existe información para el servicio seleccionado, por favor notificar a Sistemas.");
            }

            //obtengo el producto del servicio
            $objProducto            = $objInfoServicio->getProductoId();
            //obtengo el login aux del servicio
            $strLoginAux            = $objInfoServicio->getLoginAux();
            //obtengo las características
            $objTipoSolicitudMA     = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                        ->findOneBy(array("descripcionSolicitud" => "SOLICITUD MIGRACION ANILLO", "estado" => "Activo"));
            $objTipoSolicitudVlan   = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                        ->findOneBy(array("descripcionSolicitud" => "SOLICITUD MIGRACION DE VLAN", "estado" => "Activo"));
            //obtengo la solicitud
            $objDetalleSolicitud    = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                        ->createQueryBuilder('p')
                                                        ->where('p.servicioId = :servicioId')
                                                        ->andWhere('p.tipoSolicitudId = :tipoSolicitudIdOne OR '.
                                                                   'p.tipoSolicitudId = :tipoSolicitudIdTwo')
                                                        ->andWhere('p.estado = :estado')
                                                        ->setParameter('servicioId', $objInfoServicio->getId())
                                                        ->setParameter('tipoSolicitudIdOne', $objTipoSolicitudMA->getId())
                                                        ->setParameter('tipoSolicitudIdTwo', $objTipoSolicitudVlan->getId())
                                                        ->setParameter('estado','Asignada')
                                                        ->getQuery()
                                                        ->getOneOrNullResult();
            if( is_object($objDetalleSolicitud) )
            {
                $strNombreSolicitud = $objDetalleSolicitud->getTipoSolicitudId()->getDescripcionSolicitud();
                if( $strNombreSolicitud == "SOLICITUD MIGRACION ANILLO" )
                {
                    $strTipoObservacion = "Se reverso la Solicitud Migración a Anillo";
                }
                else
                {
                    $strTipoObservacion = "Se reverso la Solicitud Migración a Vlan";
                }
                if(!empty($strObservacion))
                {
                    $strObservacion = $strTipoObservacion."<br><b>Obs:</b>".$strObservacion;
                }
                else
                {
                    $strObservacion = $strTipoObservacion;
                }

                //verificar si la asignación de recurso es nueva o existente
                $strRecursosNuevos = 'N';
                if( $objProducto->getNombreTecnico() == "L3MPLS" )
                {
                    $objAdmiTipoRecurso   = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array('descripcionCaracteristica' => 'TIPO_RECURSO'));
                    if(is_object($objAdmiTipoRecurso))
                    {
                        $objCaractTipoRecurso = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                                      'caracteristicaId'   => $objAdmiTipoRecurso->getId(),
                                                                      'estado'             => 'Asignada'));
                        if(is_object($objCaractTipoRecurso) && $objCaractTipoRecurso->getValor() == 'nuevos')
                        {
                            $strRecursosNuevos = 'S';
                        }
                    }
                }

                //reversar recursos de red
                $arrayParametrosReversar = array(
                    'objInfoServicio'     => $objInfoServicio,
                    'objProducto'         => $objProducto,
                    'objDetalleSolicitud' => $objDetalleSolicitud,
                    'strRecursosNuevos'   => $strRecursosNuevos,
                    'strUsrCreacion'      => $strUsrCreacion,
                    'strIpCreacion'       => $strIpCreacion,
                );
                $arrayResultadoReversar = $this->reversarAsignacionRecursosRedPorServicioSolicitud($arrayParametrosReversar);
                if( $arrayResultadoReversar['status'] == 'ERROR' )
                {
                    throw new \Exception($arrayResultadoReversar['mensaje']);
                }

                //procedo a finalizar la solicitud
                $objDetalleSolicitud->setEstado("Finalizada");
                $this->emComercial->persist($objDetalleSolicitud);
                $this->emComercial->flush();

                //agregar historial a la solicitud
                $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
                $objDetalleSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolicitudHistorial->setIpCreacion($strIpCreacion);
                $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitudHistorial->setUsrCreacion($strUsrCreacion);
                $objDetalleSolicitudHistorial->setObservacion($strObservacion);
                $objDetalleSolicitudHistorial->setEstado("Finalizada");
                $this->emComercial->persist($objDetalleSolicitudHistorial);
                $this->emComercial->flush();

                //agregar servicio historial
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objInfoServicio);
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setEstado($objInfoServicio->getEstado());
                $objServicioHistorial->setObservacion($strObservacion);
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();
            }
            else
            {
                throw new \Exception("No existe una solicitud de migración de anillo para el servicio, por favor notificar a Sistemas.");
            }

            $this->emComercial->flush();
            $this->emInfraestructura->flush();
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->commit();
                $this->emComercial->getConnection()->close();
            }
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->commit();
                $this->emInfraestructura->getConnection()->close();
            }
            $strResultado = $strTipoObservacion." para el servicio: <b>".$strLoginAux."</b>";
        }
        catch (\Exception $e)
        {
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();
            }
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
                $this->emInfraestructura->getConnection()->close();
            }
            $this->utilServicio->insertError('Telcos+',
                                             'InfoCambiarPuertoService.reversarSolicitudMigracionAnillo',
                                             $e->getMessage(),
                                             $strUsrCreacion,
                                             $strIpCreacion);
            $strResultado = $strResultado."<br>ERROR: ".$e->getMessage();
        }
        return $strResultado;
    }

    /**
     * Documentación para el método 'reversarAsignacionRecursosRedPorServicioSolicitud'.
     *
     * Método que sirve para reversar la asignación de recursos de red por servicio y solicitud
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 18-03-2020
     *
     * @param Array $arrayParametros [
     *                                  objInfoServicio,       //objeto del servicio
     *                                  objProducto,           //objeto del producto
     *                                  objDetalleSolicitud,   //objeto de la solicitud
     *                                  strRecursosNuevos,     //recursos nuevos 'S' o 'N'
     *                                  strUsrCreacion,        //nombre de usuario
     *                                  strIpCreacion          //ip de creación
     *                               ]
     *
     * @return Array $arrayResultado [
     *                                  status,        //estado de la operación 'OK' o 'ERROR'
     *                                  mensaje        //mensaje de la operación
     *                               ]
     */
    public function reversarAsignacionRecursosRedPorServicioSolicitud($arrayParametros)
    {
        $objInfoServicio        = $arrayParametros['objInfoServicio'];
        $objProducto            = $arrayParametros['objProducto'];
        $objDetalleSolicitud    = $arrayParametros['objDetalleSolicitud'];
        $strRecursosNuevos      = $arrayParametros['strRecursosNuevos'];
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];
        $strIpCreacion          = $arrayParametros['strIpCreacion'];
        try
        {
            $objAdmiCaracIp     = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica' => 'IP_ID'));
            $objAdmiCaracVlan   = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica' => 'VLAN'));
            $objAdmiCaraVlanPro = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica' => 'VLAN_PROVEEDOR'));
            $objAdmiCaracVrf    = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica' => 'VRF'));
            $objAdmiCaracProt   = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica' => 'PROTOCOLO_ENRUTAMIENTO'));
            $objAdmiCaracGatway = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica' => 'DEFAULT_GATEWAY'));
            $objAdmiCarPerAsPri = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica' => 'ID_PERSONA_EMPRESA_ROL_CARAC_AS_PRIVADO'));
            $objAdmiProductoId  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica' => 'PRODUCTO_ID'));

            $objCaractIp        = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                    ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                      'caracteristicaId'   => $objAdmiCaracIp->getId(),
                                                      'estado'             => 'Asignada'));
            $objCaractVlan      = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                    ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                      'caracteristicaId'   => $objAdmiCaracVlan->getId(),
                                                      'estado'             => 'Asignada'));
            $objCaractVlanPro   = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                    ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                      'caracteristicaId'   => $objAdmiCaraVlanPro->getId(),
                                                      'estado'             => 'Asignada'));
            $objCaractVrf       = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                    ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                      'caracteristicaId'   => $objAdmiCaracVrf->getId(),
                                                      'estado'             => 'Asignada'));
            $objCaractProEnru   = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                    ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                      'caracteristicaId'   => $objAdmiCaracProt->getId(),
                                                      'estado'             => 'Asignada'));
            $objCaractGateway   = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                    ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                      'caracteristicaId'   => $objAdmiCaracGatway->getId(),
                                                      'estado'             => 'Asignada'));
            $objCarPerEmpAsPri  = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                    ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                      'caracteristicaId'   => $objAdmiCarPerAsPri->getId(),
                                                      'estado'             => 'Asignada'));
            $objCaracProductoId = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                    ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                      'caracteristicaId'   => $objAdmiProductoId->getId(),
                                                      'estado'             => 'Asignada'));

            //verifico si existe el producto en la característica de la solicitud
            if(is_object($objCaracProductoId))
            {
                $objProductoAnt = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                        ->find($objCaracProductoId->getValor());
                if( is_object($objProductoAnt) && ( $objProductoAnt->getNombreTecnico() == 'INTERNET' || 
                    $objProductoAnt->getNombreTecnico() == 'INTERNET SDWAN' ) )
                {
                    //actualizo el producto nuevo con el anterior
                    $objProducto = $objProductoAnt;
                    //actualizo el producto anterior
                    $objInfoServicio->setProductoId($objProductoAnt);
                    $this->emComercial->persist($objInfoServicio);
                    $this->emComercial->flush();
                    //actualizo las características de la persona empresa rol al producto anterior
                    $arrayServicioProductoCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                     ->findBy(array("servicioId" => $objInfoServicio->getId(), 
                                                                                    "estado"     => 'Activo'));
                    foreach($arrayServicioProductoCaract as $objServProdCarac)
                    {
                        $objProdCaract  = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                ->find($objServProdCarac->getProductoCaracterisiticaId());
                        $objProdNuevo   = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                ->findOneBy(array("productoId"       => $objProductoAnt->getId(), 
                                                                  "caracteristicaId" => $objProdCaract->getCaracteristicaId()->getId(), 
                                                                  "estado"           => "Activo"));
                        if(is_object($objProdNuevo))
                        {
                            $objServProdCarac->setProductoCaracterisiticaId($objProdNuevo->getId());
                            $this->emComercial->persist($objServProdCarac);
                            $this->emComercial->flush();
                        }
                    }
                }
            }

            $objProdCaracVlan   = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                        ->findOneBy(array("productoId"       => $objProducto->getId(),
                                                          "caracteristicaId" => $objAdmiCaracVlan->getId(),
                                                          "estado"           => "Activo"));
            if(is_object($objProdCaracVlan) && is_object($objCaractVlan))
            {
                $objServProdCarac = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                        ->findOneBy(array("servicioId"                => $objInfoServicio->getId(),
                                                          "productoCaracterisiticaId" => $objProdCaracVlan->getId(),
                                                          "valor"                     => $objCaractVlan->getValor(),
                                                          "estado"                    => 'Eliminado'));
                if(is_object($objServProdCarac))
                {
                    //eliminar el producto caracteristica de la Vlan nueva
                    $objServProdCaracNew = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                        ->findOneBy(array("servicioId"                => $objInfoServicio->getId(),
                                                          "productoCaracterisiticaId" => $objProdCaracVlan->getId(),
                                                          "estado"                    => 'Activo'));
                    if(is_object($objServProdCaracNew))
                    {
                        $objServProdCaracNew->setEstado("Eliminado");
                        $this->emComercial->persist($objServProdCaracNew);
                        $this->emComercial->flush();
                    }
                    //activar el producto caracteristica de la Vlan anterior
                    $objServProdCarac->setEstado("Activo");
                    $this->emComercial->persist($objServProdCarac);
                    $this->emComercial->flush();
                }
            }
            $objProdCaraVlanPro = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                        ->findOneBy(array("productoId"       => $objProducto->getId(),
                                                          "caracteristicaId" => $objAdmiCaraVlanPro->getId(),
                                                          "estado"           => "Activo"));
            if(is_object($objProdCaraVlanPro) && is_object($objCaractVlanPro))
            {
                $objServProdCarac = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                        ->findOneBy(array("servicioId"                => $objInfoServicio->getId(),
                                                          "productoCaracterisiticaId" => $objProdCaraVlanPro->getId(),
                                                          "valor"                     => $objCaractVlanPro->getValor(),
                                                          "estado"                    => 'Eliminado'));
                if(is_object($objServProdCarac))
                {
                    //eliminar el producto caracteristica de la Vlan Proveedor nueva
                    $objServProdCaracNew = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                        ->findOneBy(array("servicioId"                => $objInfoServicio->getId(),
                                                          "productoCaracterisiticaId" => $objProdCaraVlanPro->getId(),
                                                          "estado"                    => 'Activo'));
                    if(is_object($objServProdCaracNew))
                    {
                        $objServProdCaracNew->setEstado("Eliminado");
                        $this->emComercial->persist($objServProdCaracNew);
                        $this->emComercial->flush();
                    }
                    //activar el producto caracteristica de la Vlan Proveedor anterior
                    $objServProdCarac->setEstado("Activo");
                    $this->emComercial->persist($objServProdCarac);
                    $this->emComercial->flush();
                }
            }
            $objProdCaracVrf    = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                        ->findOneBy(array("productoId"       => $objProducto->getId(),
                                                          "caracteristicaId" => $objAdmiCaracVrf->getId(),
                                                          "estado"           => "Activo"));
            if(is_object($objProdCaracVrf) && is_object($objCaractVrf))
            {
                $objServProdCarac = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                        ->findOneBy(array("servicioId"                => $objInfoServicio->getId(),
                                                          "productoCaracterisiticaId" => $objProdCaracVrf->getId(),
                                                          "valor"                     => $objCaractVrf->getValor(),
                                                          "estado"                    => 'Eliminado'));
                if(is_object($objServProdCarac))
                {
                    //eliminar el producto caracteristica de la Vrf nueva
                    $objServProdCaracNew = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                        ->findOneBy(array("servicioId"                => $objInfoServicio->getId(),
                                                          "productoCaracterisiticaId" => $objProdCaracVrf->getId(),
                                                          "estado"                    => 'Activo'));
                    if(is_object($objServProdCaracNew))
                    {
                        $objServProdCaracNew->setEstado("Eliminado");
                        $this->emComercial->persist($objServProdCaracNew);
                        $this->emComercial->flush();
                    }
                    //activar el producto caracteristica de la Vrf anterior
                    $objServProdCarac->setEstado("Activo");
                    $this->emComercial->persist($objServProdCarac);
                    $this->emComercial->flush();
                }
            }
            $objProdCaracProEnr = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                        ->findOneBy(array("productoId"       => $objProducto->getId(),
                                                          "caracteristicaId" => $objAdmiCaracProt->getId(),
                                                          "estado"           => "Activo"));
            if(is_object($objProdCaracProEnr) && is_object($objCaractProEnru))
            {
                $objServProdCarac = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                        ->findOneBy(array("servicioId"                => $objInfoServicio->getId(),
                                                          "productoCaracterisiticaId" => $objProdCaracProEnr->getId(),
                                                          "valor"                     => $objCaractProEnru->getValor(),
                                                          "estado"                    => 'Eliminado'));
                if(is_object($objServProdCarac))
                {
                    //eliminar el producto caracteristica del protocolo enrutamiento nueva
                    $objServProdCaracNew = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                        ->findOneBy(array("servicioId"                => $objInfoServicio->getId(),
                                                          "productoCaracterisiticaId" => $objProdCaracProEnr->getId(),
                                                          "estado"                    => 'Activo'));
                    if(is_object($objServProdCaracNew))
                    {
                        $objServProdCaracNew->setEstado("Eliminado");
                        $this->emComercial->persist($objServProdCaracNew);
                        $this->emComercial->flush();
                    }
                    //activar el producto caracteristica del protocolo enrutamiento anterior
                    $objServProdCarac->setEstado("Activo");
                    $this->emComercial->persist($objServProdCarac);
                    $this->emComercial->flush();
                }
            }
            $objProdCaracGatway = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                        ->findOneBy(array("productoId"       => $objProducto->getId(),
                                                          "caracteristicaId" => $objAdmiCaracGatway->getId(),
                                                          "estado"           => "Activo"));
            if(is_object($objProdCaracGatway) && is_object($objCaractGateway))
            {
                $objServProdCarac = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                        ->findOneBy(array("servicioId"                => $objInfoServicio->getId(),
                                                          "productoCaracterisiticaId" => $objProdCaracGatway->getId(),
                                                          "valor"                     => $objCaractGateway->getValor(),
                                                          "estado"                    => 'Eliminado'));
                if(is_object($objServProdCarac))
                {
                    //eliminar el producto caracteristica de la Gateway nueva
                    $objServProdCaracNew = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                        ->findOneBy(array("servicioId"                => $objInfoServicio->getId(),
                                                          "productoCaracterisiticaId" => $objProdCaracGatway->getId(),
                                                          "estado"                    => 'Activo'));
                    if(is_object($objServProdCaracNew))
                    {
                        $objServProdCaracNew->setEstado("Eliminado");
                        $this->emComercial->persist($objServProdCaracNew);
                        $this->emComercial->flush();
                    }
                    //activar el producto caracteristica de la Gateway anterior
                    $objServProdCarac->setEstado("Activo");
                    $this->emComercial->persist($objServProdCarac);
                    $this->emComercial->flush();
                }
            }
            //eliminar la persona empresa rol caracteristica del As Privado
            if(is_object($objCarPerEmpAsPri))
            {
                $objPerEmpRolCarac  = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                        ->find($objCarPerEmpAsPri->getValor());
                if(is_object($objPerEmpRolCarac))
                {
                    //eliminar la persona empresa rol caracteristica del As Privado
                    $objPerEmpRolCarac->setEstado("Eliminado");
                    $this->emComercial->persist($objPerEmpRolCarac);
                    $this->emComercial->flush();
                }
            }
            //activar la ip anterior
            $objInfoIp          = $this->emInfraestructura->getRepository("schemaBundle:InfoIp")->find($objCaractIp->getValor());
            if(is_object($objInfoIp))
            {
                //eliminar la ip nueva
                $objInfoIpNew   = $this->emInfraestructura->getRepository("schemaBundle:InfoIp")
                                        ->findOneBy(array('servicioId' => $objInfoServicio->getId(),
                                                          "estado"     => "Activo"));
                if(is_object($objInfoIpNew))
                {
                    //eliminar la ip nueva
                    $objInfoIpNew->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objInfoIpNew);
                    $this->emInfraestructura->flush();
                    //verificar si la asignación de recursos es nueva
                    if($strRecursosNuevos === 'S' && $objProducto->getNombreTecnico() === "L3MPLS")
                    {
                        //liberar la subred y sus hijas
                        $objInfoSubred = $this->emInfraestructura->getRepository("schemaBundle:InfoSubred")->find($objInfoIpNew->getSubredId());
                        if(is_object($objInfoSubred))
                        {
                            $arrayParametrosLiberarSubred               = array();
                            $arrayParametrosLiberarSubred['tipoAccion'] = 'liberar';
                            $arrayParametrosLiberarSubred['uso']        = $objInfoSubred->getUso();
                            $arrayParametrosLiberarSubred['subredId']   = $objInfoSubred->getId();
                            $arrayRespuestaLiberar  = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                       ->provisioningSubred($arrayParametrosLiberarSubred);
                            if($arrayRespuestaLiberar['msg'] != 'OK')
                            {
                                throw new \Exception("No se pudo liberar la subred de la Ip, favor notificar a Sistemas.");
                            }
                        }
                    }
                }
                //activar la ip anterior
                $objInfoIp->setEstado("Activo");
                $this->emInfraestructura->persist($objInfoIp);
                $this->emInfraestructura->flush();
            }
            //resultado de la operación
            $arrayResultado = array(
                'status'  => 'OK',
                'mensaje' => 'Se reverso la asignación de recursos de red.',
            );
        }
        catch(Exception $e)
        {
            $this->utilServicio->insertError('Telcos+',
                                             'InfoCambiarPuertoService.reversarAsignacionRecursosRedPorServicioSolicitud',
                                             $e->getMessage(),
                                             $strUsrCreacion,
                                             $strIpCreacion);
            //resultado de la operación
            $arrayResultado = array(
                'status'  => 'ERROR',
                'mensaje' => $e->getMessage(),
            );
        }
        return $arrayResultado;
    }
    
    /**
     * evaluarFuncionPrecio, Evalua la funcion de precio en base a unos parametros dados y retorna el precio
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 04-11-2020
     * 
     * @param string $strFuncionPrecio Funcion de precio a evaluar
     * @param array $arrayProductoCaracteristicasValores Arreglo con los valores a ser reemplazados
     * @return int Retorna el precio obtenido de la evaluacion
     * 
     */
    private function evaluarFuncionPrecio($strFuncionPrecio, $arrayProductoCaracteristicasValores)
    {
        $floatPrecio        = 0;        
        $arrayFunctionJs    = array('Math.ceil','Math.floor','Math.pow',"}");
        $arrayFunctionPhp   = array('ceil','floor','pow',';}');
        $strFuncionPrecio   = str_replace($arrayFunctionJs, $arrayFunctionPhp, $strFuncionPrecio);
        $strFuncionPrecio   = str_replace('"[', '[', $strFuncionPrecio);
        $strFuncionPrecio   = str_replace(']"', ']', $strFuncionPrecio);
        foreach($arrayProductoCaracteristicasValores as $strClave => $strValor)
        {
            $strFuncionPrecio = str_replace("[" . $strClave . "]", '"'. $strValor . '"', $strFuncionPrecio);
        }
        $strFuncionPrecio      = str_replace('PRECIO', '$floatPrecio', $strFuncionPrecio);
        $strDigitoVerificacion = substr($strFuncionPrecio, -1, 1);
        if(is_numeric($strDigitoVerificacion))
        {
            $strFuncionPrecio = $strFuncionPrecio . ";";
        }
        
        
        eval($strFuncionPrecio);
        return $floatPrecio;
    }

    /**
     * Método utilizado para realizar cambio de puerto de servicios SAFECITY, o TN-GPON.
     *
     * @param array $arrayParams [
     *                              idServicio -> id derl serivio
     *                              strUsrCreacion -> usuario de creacion
     *                              strIpCreacion -> ip de creacion
     *                              intInterfaceOnt -> id de la interface
     *                              strSerieOnt -> serie del ont
     *                              strMacOnt -> mac del ont
     *                              intIdEmpresa -> id de la empresa
     *                              prefijoEmpresa -> prefijo de la empresa
     *                              intIdElemento -> id del elemento
     *                              strPuertoOnt -> nombre del puerto del ont
     *                              strPuertoSwPoe -> nombre del puerto del switch
     *                              strNombreProducto -> nombre del producto
     *                              strNombreInterfaz -> nombre de la interfaz seleccionada
     *                          ]
     *
     * @return array|string[] $arrayResponse [
     *                                  status, //estado de la operación 'OK' o 'ERROR'
     *                                  msg //mensaje de la operación ]
     *
     *@author Pablo Pin <ppin@telconet.ec>
     * @version 01-12-2021 | Version Inicial
     *
     */
    public function cambiarPuertoTnGpon($arrayParams)
    {
        $intIdServicio        = $arrayParams['idServicio'];
        $strUsrCreacion       = $arrayParams["strUsrCreacion"];
        $strIpCreacion        = $arrayParams["strIpCreacion"];
        $intInterfaceOnt      = $arrayParams["intInterfaceOnt"];
        $strSerieOnt          = $arrayParams["strSerieOnt"];
        $strMacOnt            = $arrayParams["strMacOnt"];
        $intIdEmpresa         = $arrayParams["intIdEmpresa"];
        $strPrefijoEmpresa    = $arrayParams["prefijoEmpresa"];
        $intIdElemento        = $arrayParams["intIdElemento"];
        $strPuertoOnt         = $arrayParams["strPuertoOnt"];
        $strPuertoSwPoe       = $arrayParams["strPuertoSwPoe"];
        $strNombreProducto    = $arrayParams["strNombreProducto"];
        $strNombreInterfaz    = $arrayParams["strNombreInterfaz"];

        $arrayResponse = array('status' => 'ERROR', 'msg' => 'Se ha producido un error durante el proceso de cambio de puerto.');

        $objServicio    = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);

        if ($strPuertoOnt == '-Seleccione-')
        {
            $strNombreInterfaz = $strPuertoSwPoe;
        }
        else if ($strPuertoSwPoe == '-Seleccione-')
        {
            $strNombreInterfaz = $strPuertoOnt;
        }

        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        try
        {
            $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                    ->findOneByServicioId($objServicio->getId());

            /*Obtener elemento actual.*/
            $objElementoCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                          ->findOneById($intIdElemento);
            $objInterfaceActual = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                          ->find($intInterfaceOnt);
            $objInterfaceNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                        ->findOneBy(array(
                                                            "elementoId" => $objElementoCliente->getId(),
                                                            "nombreInterfaceElemento" => $strNombreInterfaz
                                                        ));

            if ($objServicio->getProductoId()->getNombreTecnico() == 'SAFECITYSWPOE')
            {
                $objServCaractCamPrincipal = $this->servicioGeneral->getServicioProductoCaracteristica( $objServicio,
                    'RELACION_CAMARA_PRINCIPAL',
                    $objServicio->getProductoId());

                if (!is_object($objServCaractCamPrincipal))
                {
                    throw new \Exception("No existe caracteristica RELACION_CAMARA_PRINCIPAL.");
                }

                $objServicioConf = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($objServCaractCamPrincipal->getValor());

                if (!is_object($objServicioConf))
                {
                    throw new \Exception("No existe el servicio en RELACION_CAMARA_PRINCIPAL.");
                }

                $objServCaractMigracion = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioConf,
                                                                                                    'PUERTO_ONT',
                                                                                                    $objServicioConf->getProductoId());

                $objInterfaceActual = $this->emInfraestructura
                    ->getRepository('schemaBundle:InfoInterfaceElemento')
                    ->findOneBy(array(
                        "elementoId" => $objElementoCliente->getId(),
                        "nombreInterfaceElemento" => $objServCaractMigracion->getValor()
                    ));

            }

            /*Se valida si se necesita liberar recursos.*/
            $objServCaractMigracion = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                'SERVICIO_EN_SWITCH_POE',
                                                                                                $objServicio->getProductoId());

            if (is_object($objServCaractMigracion))
            {
                $objEnlaceActual = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                             ->findOneBy(array(
                                                                'interfaceElementoFinId' => $objServicioTecnico->getInterfaceElementoClienteId(),
                                                                'estado' => 'Activo'));

                $objInterfaceActual = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                              ->find($objEnlaceActual->getInterfaceElementoIniId()->getId());

            }

            if (!is_object($objServCaractMigracion))
            {
                $objServicioConf = $objServicio->getProductoId()->getNombreTecnico() == 'SAFECITYSWPOE' ? $objServicioConf : $objServicio;


                if (!is_object($objElementoCliente) || !is_object($objInterfaceActual) || !is_object($objInterfaceNuevo))
                {
                    throw new \Exception("No se enviaron los datos necesarios, por favor validar.");
                }

                /*TODO: Liberar recursos.*/
                $arrayRespuestaLiberar = $this->servicioGeneral->liberarPuertoOntGponTN(
                    array(
                        'objServicio' => $objServicioConf,
                        'arrayPuertosLiberar' => '',
                        'strCodEmpresa' => $strPrefijoEmpresa,
                        'strUsrCreacion' => $strUsrCreacion,
                        'strIpCreacion' => $strIpCreacion
                    ));

                $arrayRespuestaLiberar['status'] =  'OK';

                if ($arrayRespuestaLiberar['status'] == 'OK')
                {
                    $objServCaractPuerto = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                    'PUERTO_ONT',
                                                                                                    $objServicio->getProductoId());

                    if (is_object($objServCaractPuerto))
                    {
                        $objServCaractPuerto->setEstado("Eliminado");
                        $this->emComercial->persist($objServCaractPuerto);
                        $this->emComercial->flush();
                    }

                    $objServCaractPuertoConf = $this->servicioGeneral->getServicioProductoCaracteristica(  $objServicioConf,
                                                                                                            'PUERTO_ONT',
                                                                                                            $objServicioConf->getProductoId()   );
                    if (is_object($objServCaractPuertoConf))
                    {
                        $objServCaractPuertoConf->setEstado("Eliminado");
                        $this->emComercial->persist($objServCaractPuertoConf);
                        $this->emComercial->flush();
                    }

                    /*TODO: Activar nuevamente.*/
                    $arrayResultadoReactivar = $this->activar->reactivarServiciosSafeCityTNGpon(
                        array(
                        'objServicio' => $objServicioConf,
                        'idInterfaceOnt' => $intInterfaceOnt,
                        'serieOnt' => $strSerieOnt,
                        'macOnt' => $strMacOnt,
                        'prefijoEmpresa' => $strPrefijoEmpresa,
                        'strCodEmpresa' => $intIdEmpresa,
                        'strUsrCreacion' => $strUsrCreacion,
                        'strIpCreacion' => $strIpCreacion
                    ));

                    $arrayResultadoReactivar['status'] =  'OK';

                    /*Validamos que la respuesta sea ERROR para poder sacarlo del flujo.*/
                    if ($arrayResultadoReactivar['status'] == 'ERROR')
                    {
                        throw new \Exception("Se produjo un error al activar nuevamente.");
                    }
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicioConf,
                                                                                    $objServicioConf->getProductoId(),
                                                                                    "PUERTO_ONT",
                                                                                    $objInterfaceNuevo->getNombreInterfaceElemento(),
                                                                                    $strUsrCreacion);

                    if ($objServicio->getProductoId()->getNombreTecnico() == 'SAFECITYSWPOE')
                    {
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                            $objServicio->getProductoId(),
                            "PUERTO_ONT",
                            $objInterfaceNuevo->getNombreInterfaceElemento(),
                            $strUsrCreacion);
                    }
                }
                else
                {
                    throw new \Exception("Se ha producido un error durante la liberacion del recurso.");
                }
            }

            /*Recorrer enlaces anteriores de INICIO.*/
            $arrayEnlaceAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                         ->findBy(array(
                                                            'interfaceElementoIniId' => $objInterfaceActual->getId(),
                                                            'estado' => 'Activo'));

            foreach ($arrayEnlaceAnterior as $objEnlaceAnterior)
            {
                //guardar nuevo enlace
                $objEnlaceNuevo = new InfoEnlace();
                $objEnlaceNuevo->setInterfaceElementoIniId($objInterfaceNuevo);
                $objEnlaceNuevo->setInterfaceElementoFinId($objEnlaceAnterior->getInterfaceElementoFinId());
                $objEnlaceNuevo->setTipoMedioId($objEnlaceAnterior->getTipoMedioId());
                $objEnlaceNuevo->setTipoEnlace($objEnlaceAnterior->getTipoEnlace());
                $objEnlaceNuevo->setEstado("Activo");
                $objEnlaceNuevo->setUsrCreacion($strUsrCreacion);
                $objEnlaceNuevo->setFeCreacion(new \DateTime('now'));
                $objEnlaceNuevo->setIpCreacion($strIpCreacion);
                $this->emInfraestructura->persist($objEnlaceNuevo);
                $this->emInfraestructura->flush();
                //eliminar enlace anterior
                $objEnlaceAnterior->setEstado('Eliminado');
                $this->emInfraestructura->persist($objEnlaceAnterior);
                $this->emInfraestructura->flush();
            }

            /*Recorrer enlaces anteriores de FIN.*/
            $arrayEnlaceAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                            ->findBy(array(
                                                                'interfaceElementoFinId' => $objInterfaceActual->getId(),
                                                                'estado' => 'Activo'));

            foreach ($arrayEnlaceAnterior as $objEnlaceAnterior)
            {
                //guardar nuevo enlace
                $objEnlaceNuevo = new InfoEnlace();
                $objEnlaceNuevo->setInterfaceElementoIniId($objEnlaceAnterior->getInterfaceElementoIniId());
                $objEnlaceNuevo->setInterfaceElementoFinId($objInterfaceNuevo);
                $objEnlaceNuevo->setTipoMedioId($objEnlaceAnterior->getTipoMedioId());
                $objEnlaceNuevo->setTipoEnlace($objEnlaceAnterior->getTipoEnlace());
                $objEnlaceNuevo->setEstado("Activo");
                $objEnlaceNuevo->setUsrCreacion($strUsrCreacion);
                $objEnlaceNuevo->setFeCreacion(new \DateTime('now'));
                $objEnlaceNuevo->setIpCreacion($strIpCreacion);
                $this->emInfraestructura->persist($objEnlaceNuevo);
                $this->emInfraestructura->flush();
                //eliminar enlace anterior
                $objEnlaceAnterior->setEstado('Eliminado');
                $this->emInfraestructura->persist($objEnlaceAnterior);
                $this->emInfraestructura->flush();
            }

            //actualizar estado de la interface
            $objInterfaceNuevo->setEstado('connected');
            $objInterfaceNuevo->setMacInterfaceElemento($objInterfaceActual->getMacInterfaceElemento());

            $this->emInfraestructura->persist($objInterfaceNuevo);
            $this->emInfraestructura->flush();

            $objInterfaceActual->setEstado('err-disabled');
            $this->emInfraestructura->persist($objInterfaceActual);
            $this->emInfraestructura->flush();

            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objElementoCliente);
            $objHistorialElemento->setObservacion("El puerto se encuentra dañado.");
            $objHistorialElemento->setEstadoElemento($objElementoCliente->getEstado());
            $objHistorialElemento->setUsrCreacion($strUsrCreacion);
            $objHistorialElemento->setIpCreacion($strIpCreacion);
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));

            $this->emInfraestructura->persist($objHistorialElemento);
            $this->emInfraestructura->flush();

            /*Registrar en info historial elemento.*/
            $strObservacionHistEle = "Se realizó un cambio de puerto: "
                . " **Elemento Cliente**:"
                . " - Nombre: " . $objElementoCliente->getNombreElemento()
                . " - Puerto Anterior: " . $objInterfaceActual->getNombreInterfaceElemento()
                . " - Puerto Nuevo: " . $objInterfaceNuevo->getNombreInterfaceElemento();

            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objElementoCliente);
            $objHistorialElemento->setObservacion($strObservacionHistEle);
            $objHistorialElemento->setEstadoElemento($objElementoCliente->getEstado());
            $objHistorialElemento->setUsrCreacion($strUsrCreacion);
            $objHistorialElemento->setIpCreacion($strIpCreacion);
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $this->emInfraestructura->persist($objHistorialElemento);
            $this->emInfraestructura->flush();

            /*Registramos en el historial del servicio.*/
            $strObservacionHistSer = "<b>Se realizó un cambio de puerto:</b><br>"
                . "<b style='color:blue'>Elemento Cliente:</b><br>"
                . "<b>Nombre:</b> " . $objElementoCliente->getNombreElemento() . "<br>"
                . "<b>Puerto Anterior:</b>  " . $objInterfaceActual->getNombreInterfaceElemento() . "<br>"
                . "<b>Puerto Nuevo:</b> " . $objInterfaceNuevo->getNombreInterfaceElemento();

            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion($strObservacionHistSer);
            $objServicioHistorial->setEstado($objServicio->getEstado());
            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
            $objServicioHistorial->setIpCreacion($strIpCreacion);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));

            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();

            /*TODO: Crear solicitud.*/
            //obtengo el tipo de solicitud
            $objTipoSolicitud = $this->emComercial
                                    ->getRepository('schemaBundle:AdmiTipoSolicitud')
                                    ->findOneBy(array(
                                        "descripcionSolicitud" => 'SOLICITUD CAMBIO PUERTO',
                                        "estado" => "Activo"
                                    ));

            //ingreso el detalle de la solicitud
            $objDetalleSolicitud = new InfoDetalleSolicitud();
            $objDetalleSolicitud->setServicioId($objServicio);
            $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
            $objDetalleSolicitud->setEstado("AsignadoTarea");
            $objDetalleSolicitud->setUsrCreacion($strUsrCreacion);
            $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
            $objDetalleSolicitud->setObservacion("Se genera la solicitud de cambio de puerto GPON.");

            $this->emComercial->persist($objDetalleSolicitud);
            $this->emComercial->flush();

            /*TODO: Registrar seguimientos solicitud.*/
            $objHistorialSolicitud = new InfoDetalleSolHist();
            $objHistorialSolicitud->setDetalleSolicitudId($objDetalleSolicitud);
            $objHistorialSolicitud->setEstado($objDetalleSolicitud->getEstado());
            $objHistorialSolicitud->setObservacion("Se ingresa la solicitud automáticamente luego del cambio de puerto GPON.");
            $objHistorialSolicitud->setUsrCreacion($strUsrCreacion);
            $objHistorialSolicitud->setFeCreacion(new \DateTime('now'));
            $objHistorialSolicitud->setIpCreacion($strIpCreacion);
            $this->emComercial->persist($objHistorialSolicitud);
            $this->emComercial->flush();

            /*TODO: Finalizar la solicitud previamente creada.*/
            $objDetalleSolicitud->setEstado("Finalizada");
            $this->emComercial->persist($objDetalleSolicitud);
            $this->emComercial->flush();

            /*TODO: Registrar seguimientos solicitud.*/
            $objHistorialSolicitud = new InfoDetalleSolHist();
            $objHistorialSolicitud->setDetalleSolicitudId($objDetalleSolicitud);
            $objHistorialSolicitud->setEstado($objDetalleSolicitud->getEstado());
            $objHistorialSolicitud->setObservacion("Se finaliza la solicitud automáticamente luego del cambio de puerto GPON.");
            $objHistorialSolicitud->setUsrCreacion($strUsrCreacion);
            $objHistorialSolicitud->setFeCreacion(new \DateTime('now'));
            $objHistorialSolicitud->setIpCreacion($strIpCreacion);
            $this->emComercial->persist($objHistorialSolicitud);
            $this->emComercial->flush();

            //se guardan los cambios
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->commit();
                $this->emInfraestructura->getConnection()->close();
            }
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->commit();
                $this->emComercial->getConnection()->close();
            }

            //resultado de la operación
            $arrayResponse = array(
                'status'  => 'OK',
                'mensaje' => 'Se realizó el cambio de puerto exitosamente.',
            );

        }
        catch (\Exception $e)
        {
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
                $this->emInfraestructura->getConnection()->close();
            }
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();
            }
            $this->utilServicio->insertError('Telcos+',
                                            'InfoCambiarPuertoService.cambiarPuertoTnGpon',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);

            //resultado de la operación
            $arrayResponse = array(
                'status'  => 'ERROR',
                'mensaje' => $e->getMessage(),
            );

        }

        return $arrayResponse;

    }

}


