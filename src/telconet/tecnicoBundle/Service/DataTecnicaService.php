<?php

namespace telconet\tecnicoBundle\Service;

use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoElementoTrazabilidad;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Clase que sirve para cargar la data tecnica de un servicio
 * 
 * @author Francisco ADum <fadum@telconet.ec>
 * @version 1.0 30-07-2015
 */
class DataTecnicaService
{
    private $emComercial;
    private $emInfraestructura;
    private $emSoporte;
    private $emGeneral;
    private $emComunicacion;
    private $emSeguridad;
    private $emNaf;
    private $serviceTecnico;
    private $container;
    private $host;
    private $pathTelcos;
    private $pathParameters;
    private $serviceUtil;
    
    public function __construct(Container $container) 
    {
        $this->container            = $container;
        
        $this->emSoporte            = $this->container->get('doctrine')->getManager('telconet_soporte');
        $this->emGeneral            = $this->container->get('doctrine')->getManager('telconet_general');
        $this->emInfraestructura    = $this->container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emSeguridad          = $this->container->get('doctrine')->getManager('telconet_seguridad');
        $this->emComercial          = $this->container->get('doctrine')->getManager('telconet');
        $this->emComunicacion       = $this->container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emNaf                = $this->container->get('doctrine')->getManager('telconet_naf');
        $this->host                 = $this->container->getParameter('host');
        $this->pathTelcos           = $this->container->getParameter('path_telcos');
        $this->pathParameters       = $this->container->getParameter('path_parameters');
    }    
    
    public function setDependencies(InfoServicioTecnicoService $serviceTecnico) 
    {
        $this->serviceTecnico = $serviceTecnico;
        $this->serviceInfoElemento  = $this->container->get('tecnico.InfoElemento');
        $this->serviceUtil          = $this->container->get('schema.Util');
    }
    
    /**
     * getDataTecnica
     * 
     * Funcion que sirve para obtener de la base de datos la 
     * data tecnica de un servicio
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 30-07-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 28-01-2015 Se agrega campo con aprovisionamiento de ip del olt
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 06-05-2016 Se agrega parametro empresa en metodo generarJsonIpPublicaPorServicio por conflictos de 
     *                         producto INTERNET DEDICADO
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3 24-05-2016 Se agregan validaciones para obtener información de servicios Radio Tn
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 20-05-2016
     * Se agrega funcionalidad para mostrar la data técnica para empresa TN
     * 
     * @author Eduardo Plua <eplua@telconet.ec>
     * @version 1.3 26-05-2016 - Se recupera elementoPe desde ws networking
     *
     * @author Ana Arias <acarias@telconet.ec>
     * @version 1.4 15-06-2016 - Depuracion de variables no inicializadas.
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 18-06-2016 - Se verifica que si no tiene VLAN retorne información y no ocurra un error.
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.6 20-06-2016 - mostrar info de wifi
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.7 20-06-2016 - Se agregan nuevas validaciones para obtener información de servicios Radio Tn debido que cambios realizados generan
     *                           error al visualizar la info
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.8 20-06-2016 - ver info de macs e ips del ap wifi y del router
     * 
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.9 2016-06-22 - Se valida la no existencia de Servicio Tecnico y se inicializa las variables
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 2.0 27-06-2016 Se valida la no existencia de Servicio Tecnico y se inicializa las variables     * 
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 2.1 18-07-2016 ver info tecnica de servicio wifi
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.2 26-07-2016 ver info tecnica de servicio UTP y Fibra DIRECTA
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.3 04-08-2016 Se valida que la MAC del CPE se obtenga de la interface del CPE que contenga el servicio
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.4 08-08-2016 Se valida que la MAC de la Radio se obtenga del detalle elemento
     * 
     * @author Jesus Bozada<jbozada@telconet.ec>
     * @version 2.5 23-08-2016 se agrega recuperación de objeto InfoElemento del elementoId de BackBone del servicio consultado
     *
     * @author Jesus Bozada<jbozada@telconet.ec>
     * @version 2.6 23-08-2016 se agrega recuperación de objeto InfoElemento del elementoId de BackBone del servicio consultado
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.7 08-09-2016 - Se realiza alcance para que muestre mac de router que se encuentran como equipo de cliente
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 2.8 08-09-2016 ver info validacion de cpe en utp
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 2.9 12-09-2016 Recuperacion de Mac de Radio de cliente para servicios TN con ultima milla Radio
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 2.9 15-09-2016 se estableció que se debe comparar por nombre tecnico del producto wifi
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 3.0 28-09-2016 Se aumentó los parámetros para que traiga las caracteristicas del producto wifi alquiler equipos
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 3.1 11-10-2016 Se controla excepcion de metodo que obtiene el PE de WebService de Networking para que pueda mostrar
     *                         Data Tecnica
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 3.2 10-10-2016 se agrega filtro de estado al recuperar enlace de servicios md 
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 3.3 10-11-2016 Se envia informacion de propiedad y administración de CPEs para servicios de TN
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 3.4 23-11-2016 se valida si el servicio es pseudoPe obtener informacion de vlan, pe en el escenario dado
     *                         Inicializació de variables
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 3.5 25-01-2017 Se agregan validaciones para recuperación de información de elementos SmartWifi
     * @since 3.4
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 3.6 21-02-2017 Se agrega bloque de codigo que permite obtener protocolo de enrutamiento, vrf y asprivado para servicios INTMPLS
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 3.7 23-02-2017 Se agregan validaciones para recuperación de información de elementos SmartWifi II
     * @since 3.6
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 3.8 20-04-2017 Se realiza validacion de existencia de elemento cliente para poder mostrar data tecnica
     * @since 3.7
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 3.9 10-05-2017 Se muestra informacion de tercerizadora cuando el servicio posee ultima milla TERCERIZADA
     *                         Se muestra informacion completa para ultima milla SATELITAL y TERCERIZADA
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 4.0 25-05-2017 Se agrega informacion acerca si un SW es virtual para visualizacion del usuario
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 4.1 25-05-2017 Se agrega validacion para cuando exista servicio concentrador BACKUP obtenga las capacidades del PRINCIPAL
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 4.2 08-06-2017 Se envia informacion indicando si un elemento es hub virtual cuando se trate de UM SATELITAL
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 4.3 14-07-2017 Se agrega información de cámaras por el Plan de Netlifecam y que debe ser mostrada en la data técnica
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 4.4 28-09-2017 Se agrega información de recursos de red del flujo de servicios de DATACENTER - INTERNET DC
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 4.5 14-12-2017 Se agrega validaciones para los servicios con Internet Small Business
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 4.6 19-02-2018 Se muestra la mac en la información tecnica cuando es un cambio de tipo medio.
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 4.7 26-02-2018 Se valida para que soporte productos con nombre tecnico CONCINTER ( Concentrador de Interconexiones entre clientes )
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 4.8 19-04-2018 Se valida para que soporte productos con nombre tecnico DATOSDC y L2MPLS
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 4.9 17-07-2018 - Se enviá nuevo parámetro al método getPeBySwitch
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 5.0 08-08-2018 - Se obtiene la mac y la interface para los productos DC
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 5.1 28-08-2018 Se agrega programación para recuperar información completa de servicios ZTE
     * @since 5.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 5.2 26-09-2018 se valida que exista el objeto $interfaceElementoCliente y evitar que la consulta de la data técnica tenga problemas
     * @since 5.1
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 5.3 28-11-2018 Se agregan validaciones para gestionar los productos de la empresa TNP
     * @since 5.2
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 5.3 12-09-2018 Se agregan validaciones para mostrar informacinó de elementos AP WIFI
     * @since 5.1
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 5.3 26-09-2018 Se agrega programación para recuperar información completa de servicios NETHOME
     * @since 5.2
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 5.4 29-11-2018 Se obtiene la información de los productos extender dual band
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 5.5 10-02-2019 Se agrega validación para servicios TelcoHome
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 5.6 07-08-2019 Se agrega validación para obtener equipos en productos SMALL BUSINESS y TELCOHOME
     * 
     * @author David León <mdleon@telconet.ec>
     * @version 5.7 18-10-2019 Se obtiene las capacidades para equipos ZTE.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 5.8 29-11-2019 Se obtiene el tipo de red: GPON o MPLS para ser enviado al twig de data tecnica.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 5.8 26-02-2020 Se elimina la obtención de capacidades por medio de la velocidad, ya que estos valores ya están siendo guardados 
     *                          como valores de características asociados al servicio
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 5.8 20-02-2020 Se añade indice caracteristica del servicio para MD y se retorna información de los elementos 
     *                         con sus respectiva localización georeferencial.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 5.9 16-05-2020 Se vuelve a eliminar la obtención de capacidades por medio de la velocidad para servicios Small Business debido a
     *                          que se volvió a ingresar dicho cambio por MR reversado y que se volvió a subir
     * 
     * @author Jean Pierre Nazareno Martinez <jnazareno@telconet.ec>
     * @version 6.0 01-03-2021 Validación de datos de ubicación del elemento
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 6.1 19-05-2021 - Se obtiene los datos del servicio por el tipo de red GPON.
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 6.2 21-07-2021 Se agrega consulta para obtener fecha de caducidad y numero de licencia de un producto secure cpe
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 6.3 01-09-2021 Se valida si la fecha de expiracion es nula indicar el motivo
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 6.4 10-02-2022 Se agrega validacion de servicios FTTx de TN para cambiar empresa en sesion y mostrar Data Tecnica Completa
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 6.5 15-02-2022 - Se obtiene la vlan y vrf para los servicios WIFI para la red GPON_MPLS
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 6.7 03-10-2022 - Se agrega validación para obtener los elementos del producto Seg Vehiculo.
     * 
     * @author Manuel Carpio <mcarpio@telconet.ec>
     * @version 6.8 23-11-2022 - Se agrega validación para obtener la vrf y vlan admin de wifi gpon.
     * 
     * @author Manuel Carpio <mcarpio@telconet.ec>
     * @version 6.9 27-2-2023 - Correccion al visualizar info tecnica servicios l3mpls.
     * 
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 7.0 28-02-2023 Se agrega validacion por prefijo empresa Ecuanet para seguir el flujo correctamente. 
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 7.1 09-12-2022 - Se agrega la validacion para el producto SAFE ENTRY
     * 
     * @author Axel Auza <aauza@telconet.ec>
     * @version 7.2 07-06-2023 - Se agrega validación para obtener los elementos por clientes en el producto SEG_VEHICULO
     * 
     * @param array $arrayPeticiones
     * @return array $arrayResultado
     * 
     * 
     */
    public function getDataTecnica($arrayPeticiones)
    {
        $idEmpresa                  = $arrayPeticiones['idEmpresa'];
        $prefijoEmpresa             = $arrayPeticiones['prefijoEmpresa'];
        $idServicio                 = $arrayPeticiones['idServicio'];
        
        $servicioProdCaractIndice   = null;
        $servicioProdCaractSpid     = null;
        $servicioProdCaractPerfil   = null;
        $spcLineProfileName         = null;
        $spcGemPort                 = null;
        $spcVlan                    = null;
        $spcScope                   = null;
        $spcTrafficTable            = null;
        $servicioProdCaractMac      = null;
        $servicioProdCaractMacWifi  = null;
        $spcVci                     = null;
        $spcCapacidad1              = null;
        $spcCapacidad2              = null;
        $spcCapacidadProm1          = null;
        $spcCapacidadProm2          = null;
        $servicioProdCaractMacCpe   = null;
        $servicioProdCaractMacRadio = null;
        $bufferHilo                 = null;
        $objProtocolo               = null;
        $spcZona                    = null;
        $spcDefaultGateway          = null;
        $objVrf                     = null;
        $objAsPrivado               = null;
        $objVpn                     = null;
        $objRdId                    = null;
        $objTCont                   = null;
        $objElementoRouter          = null;
        $spcAnillo                  = null;
        $objInfoIpEleConector       = null;
        $objInterfaceCpe            = null;
        $objElementoTrans           = null;
        $objElementoRos             = null;
        $objInterfaceTrans          = null;
        $objIpConector              = null;
        $detalleMacConector         = null;
        $objIpeCpe                  = null;
        $elementoContenedor         = null;
        $interfaceElementoConector  = null;
        $entityDetalleElemento      = null;
        $interfaceElementoBackbone  = null;
        $interfaceElementoCliente   = null;
        $objElementoCpe             = null;
        $objElementoOnt             = null;
        $objInterfaceOnt            = null;
        $elementoCliente            = null;
        $ip                         = null;
        $strMacRadio                = null;
        $boolEsFibraRuta            = false;   
        $objPerEmpRolCarVlan        = null;
        $objInfoElementoBackbone    = null;
        $datosWifi                  = array();
        $vlanWifi                   = null;
        $arrayParametrosWs          = array();

        $nombreProducto               = "";
        $interfaceElementoClienteWifi = "";
        $elementoClienteWifi          = "";
        $producto                     = "";
        $spcPassword                  = "";
        $spcModoOperacion             = "";
        $tipoMedio                    = "";
        $spcSsid                      = "";
        $spcNumeroPc                  = "";
        $strMacCpe                    = "";
        $strNombrePe                  = "";
        $strAdministraCpe             = "";
        $strPropiedadCpe              = "";
        $strMacSmartWifi              = "";
        $strVrf                       = "";
        $strEsElementoAdicional       = "NO";
        $strNombreElementoAdicional   = "";
        $arrayParams                  = array();
        $arrayElementosSmartWifi      = array();
        $arrayElementosApWifi         = array();
        $arrayElementosCamaras        = array();
        $strCapacidadUno              = "";
        $strCapacidadDos              = "";
        $arrayElementoSmall           = null;
        $intNumLicencia               = "";
        $strFechaCaducidad            = "";
        $boolPermisoFTTx              = isset($arrayPeticiones['boolPermisoFTTx']) ? $arrayPeticiones['boolPermisoFTTx'] : false;
        
        $servicio           = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
        if (!is_object($servicio))
        {
          throw new \Exception("No se encontró información  del servicio");
        }
       
        $servicioTecnico    = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                ->findOneBy(array( "servicioId" =>$servicio->getId()));
        $arrayElementosExtenderDualBand = array();
        $arrayElementoUbicacion;

        $arrayServiciosfttx = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('SERVICIOS DE TELCONET',
                                                         'TECNICO',
                                                         '',
                                                         'PARAMETRO_DE_SERVICIOS_DE_TELCONET',
                                                         '',
                                                         '',
                                                         '',
                                                         '',
                                                         '',
                                                         10);

        if(($servicioTecnico) && ($prefijoEmpresa == "MD") && ($boolPermisoFTTx))
        {
            $arrayServiciosfttxS = explode(",", $arrayServiciosfttx['valor1']);
            for ($intCount=0; $intCount < count($arrayServiciosfttxS); $intCount++)
            {
                if ($arrayServiciosfttxS[$intCount] == $servicioTecnico->getUltimaMillaId())
                {
                    $objEmpresaCod = $this->emComercial->getRepository("schemaBundle:InfoEmpresaGrupo")->findOneByPrefijo('TN');
                    if (is_object($objEmpresaCod))
                    {
                        $idEmpresa = $objEmpresaCod->getId();
                        $prefijoEmpresa = $objEmpresaCod->getPrefijo();
                    }
                }
            }
        }
                                                
        //Obtener la caracteristica TIPO_FACTIBILIDAD para discriminar que sea FIBRA DIRECTA o RUTA
        $objServProdCaractTipoFact = $this->serviceTecnico
                                          ->getServicioProductoCaracteristica($servicio,'TIPO_FACTIBILIDAD',$servicio->getProductoId());   
        
        //Validar si es pseudoPe para obtener la VLAN del Servicio involucrado
        $boolEsPseudoPe     = $this->emComercial->getRepository('schemaBundle:InfoServicio')->esServicioPseudoPe($servicio);
        
        if($servicioTecnico)
        {
            //verifico el tipo de red
            $strTipoRed         = "MPLS";
            $booleanTipoRedGpon = false;
            if(($prefijoEmpresa == "TN" || empty($prefijoEmpresa)) && is_object($servicio->getProductoId()))
            {
                //Se obtiene el tipo de red: GPON o MPLS
                $objTipoRed = $this->serviceTecnico->getServicioProductoCaracteristica($servicio,"TIPO_RED", $servicio->getProductoId());
                if(is_object($objTipoRed))
                {
                    $strTipoRed = $objTipoRed->getValor();
                }
            }
            //verificar si es GPON el servicio
            $arrayParVerTipoRed = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('NUEVA_RED_GPON_TN',
                                                                                'COMERCIAL',
                                                                                '',
                                                                                'VERIFICAR TIPO RED',
                                                                                'VERIFICAR_GPON',
                                                                                $strTipoRed,
                                                                                '',
                                                                                '',
                                                                                '');
            if(isset($arrayParVerTipoRed) && !empty($arrayParVerTipoRed))
            {
                $booleanTipoRedGpon = true;
            }
            if($servicioTecnico->getElementoId())
            {
                //Se agrega recuperación de objeto InfoElemento del elementoId de BackBone del servicio consultado
                $objInfoElementoBackbone    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                   ->find($servicioTecnico->getElementoId());
                
                if($prefijoEmpresa == "TN")
                {
                    if(is_object($objInfoElementoBackbone))
                    {
                        $objDetalleElemento =   $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                        ->findOneBy(array('detalleNombre' => 'ES_SWITCH_VIRTUAL',
                                                                                          'estado'        => 'Activo',
                                                                                          'elementoId'    => $objInfoElementoBackbone->getId()
                                                                                         ));
                        if(is_object($objDetalleElemento))
                        {
                            $strEsElementoAdicional     = $objDetalleElemento->getDetalleValor();
                            $strNombreElementoAdicional = "(virtual)";
                        }
                        
                        //Si es DC se obtiene el elemento PADRE ( NExus5k ) a ser mostrado para servicios de InternetDC
                        if(strpos($servicio->getProductoId()->getGrupo(),'DATACENTER')!==false)
                        {
                            $objRelacionElementoDC = $this->emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                                             ->findOneBy(array('elementoIdB' => $objInfoElementoBackbone->getId(),
                                                                                               'estado'      => 'Activo')
                                                                                              );
                            if(is_object($objRelacionElementoDC))
                            {
                                $objInfoElementoBackbone = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                                   ->find($objRelacionElementoDC->getElementoIdA());
                            }
                        }
                    }
                }
            }

            $booleanTipoMedio = true;
            if($prefijoEmpresa == "TN" && is_object($servicio->getProductoId()) &&
              ($servicio->getProductoId()->getNombreTecnico() == "SEG_VEHICULO"
               || $servicio->getProductoId()->getNombreTecnico() == "SAFE ENTRY") )
            {
                $booleanTipoMedio = false;
            }
        if($booleanTipoMedio)
        {
            $tipoMedio          = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')->find($servicioTecnico->getUltimaMillaId());
            $producto           = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                       ->findOneBy(array( "empresaCod"          => $idEmpresa, 
                                                          "descripcionProducto" => "INTERNET DEDICADO",
                                                          "estado"              => 'Activo'));

            //Si no existe la caracteristica mencionada se setea por default a Fibra Ruta                                             
            if($tipoMedio->getNombreTipoMedio() == "Fibra Optica" || $booleanTipoRedGpon)
            {
                if($objServProdCaractTipoFact)
                {
                    if($objServProdCaractTipoFact->getValor() == 'RUTA')
                    {
                        $boolEsFibraRuta = true;
                    }                   
                }
                else
                {                    
                    if($servicioTecnico->getInterfaceElementoConectorId())
                    {
                        $boolEsFibraRuta = true;
                    }                             
                }
            }
        }
            
            //migracion_ttco_md
            $arrayEmpresaMigra = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                      ->getEmpresaEquivalente($idServicio, $prefijoEmpresa);

            if($arrayEmpresaMigra)
            {
                if($arrayEmpresaMigra['prefijo'] == 'TTCO')
                {
                     $idEmpresa         = $arrayEmpresaMigra['id'];
                     $prefijoEmpresa    = $arrayEmpresaMigra['prefijo'];
                }
                else
                {
                    $objProductoServicioValidar = $servicio->getProductoId();
                    if(is_object($objProductoServicioValidar))
                    {
                        $strNombreTecnicoProductoServicio = $objProductoServicioValidar->getNombreTecnico();
                        if($strNombreTecnicoProductoServicio === "INTERNET SMALL BUSINESS" || $strNombreTecnicoProductoServicio === "TELCOHOME")
                        {
                            $idEmpresa         = $arrayEmpresaMigra['id'];
                            $prefijoEmpresa    = $arrayEmpresaMigra['prefijo'];
                            $producto          = $objProductoServicioValidar;
                            $nombreProducto     = $producto->getNombreTecnico();
                            $descripcionProducto= $producto->getDescripcionProducto();
                        }
                    }
                }
            }

            if($prefijoEmpresa == "TN")
            {
                $producto           = $servicio->getProductoId();
                $nombreProducto     = $producto->getNombreTecnico();
                $descripcionProducto= $producto->getDescripcionProducto();
            }

            if($booleanTipoMedio && $prefijoEmpresa == "MD"
               && ($tipoMedio->getNombreTipoMedio()=="Cobre" || $tipoMedio->getNombreTipoMedio()=="Radio"))
            {
                //vci----------------------------------------------------------------------------------------------------------------------------
                $spcVci = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "VCI", $producto);

                //capacidad1----------------------------------------------------------------------------------------------------------------------------
                $spcCapacidad1 = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "CAPACIDAD1", $producto);
                
                if(is_object($spcCapacidad1))
                {
                    $strCapacidadUno  = $spcCapacidad1->getValor();
                }

                //capacidad2----------------------------------------------------------------------------------------------------------------------------
                $spcCapacidad2 = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "CAPACIDAD2", $producto);
                
                if(is_object($spcCapacidad2))
                {
                    $strCapacidadDos  = $spcCapacidad2->getValor();
                }

                //capacidad-prom1------------------------------------------------------------------------------------------------------------------------
                $spcCapacidadProm1 = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "CAPACIDAD-PROM1", $producto);

                //capacidad-prom2------------------------------------------------------------------------------------------------------------------------
                $spcCapacidadProm2 = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "CAPACIDAD-PROM2", $producto);

                //mac----------------------------------------------------------------------------------------------------------------------------
                $servicioProdCaractMacCpe = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "MAC", $producto);
                
                if($servicioProdCaractMacCpe)
                {
                    $strMacCpe = $servicioProdCaractMacCpe->getValor();
                }
            }
            
            if($prefijoEmpresa == "MD" || $prefijoEmpresa == "EN" || $prefijoEmpresa == "TNP")
            {
                //indice cliente------------------------------------------------------------------------------------------------------------------------
                $servicioProdCaractIndice = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $producto);

                //service port------------------------------------------------------------------------------------------------------------------------
                $servicioProdCaractSpid = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "SPID", $producto);

                //perfil----------------------------------------------------------------------------------------------------------------------------
                $servicioProdCaractPerfil = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "PERFIL", $producto);

                //line profile name------------------------------------------------------------------------------------------------------------------
                $spcLineProfileName = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "LINE-PROFILE-NAME", $producto);

                //gem port------------------------------------------------------------------------------------------------------------------
                $spcGemPort = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "GEM-PORT", $producto);

                //vlan------------------------------------------------------------------------------------------------------------------
                $spcVlan = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "VLAN", $producto);

                //capacidad1----------------------------------------------------------------------------------------------------------------------------
                $spcCapacidad1 = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "CAPACIDAD1", $producto);   
                
                if(is_object($spcCapacidad1))
                {
                    $strCapacidadUno  = $spcCapacidad1->getValor();
                }
                
                //capacidad1-------------------------------------------------------------------------------------------------------------------------
                $objSpcCapacidad2 = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "CAPACIDAD2", $producto);   
                
                if(is_object($objSpcCapacidad2))
                {
                    $strCapacidadDos = $objSpcCapacidad2->getValor();
                }

                //scope------------------------------------------------------------------------------------------------------------------
                $spcScope = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "SCOPE", $producto);

                //traffic table------------------------------------------------------------------------------------------------------------------
                $spcTrafficTable = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "TRAFFIC-TABLE", $producto);

                //mac ont----------------------------------------------------------------------------------------------------------------------------
                $servicioProdCaractMac = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "MAC ONT", $producto);

                //mac wifi----------------------------------------------------------------------------------------------------------------------------
                $servicioProdCaractMacWifi = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "MAC WIFI", $producto);
                
                //ssid----------------------------------------------------------------------------------------------------------------------------
                $spcSsid = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "SSID", $producto);

                //numero pc----------------------------------------------------------------------------------------------------------------------------
                $spcNumeroPc = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "NUMERO PC", $producto);

                //password----------------------------------------------------------------------------------------------------------------------------
                $spcPassword = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "PASSWORD SSID", $producto);

                //modo Operacion----------------------------------------------------------------------------------------------------------------------------
                $spcModoOperacion = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "MODO OPERACION", $producto);
                
                if(is_object($spcVlan))
                {
                    $strVlan = $spcVlan->getValor();
                }
                
                //CAMARAS POR EL PLAN NETLIFECAM
                $objPuntoServicio   = $servicio->getPuntoId();
                if(is_object($objPuntoServicio))
                {
                    $intIdPunto             = $objPuntoServicio->getId();
                    $arrayRespuestaCamaras  = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                  ->getResultadoCamarasPortal(array("idPunto"                   => $intIdPunto,
                                                                                                    "strSoloDataPrincipalCam"   => "SI",
                                                                                                    "estadoServ"                => "Activo"));
                    $arrayElementosCamaras  = $arrayRespuestaCamaras['resultInfo']; 
                }
            }

            if($booleanTipoRedGpon)
            {
                //indice cliente
                $servicioProdCaractIndice = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $producto);
                //service port
                $servicioProdCaractSpid = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "SPID", $producto);
                //traffic table
                $spcTrafficTable = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "TRAFFIC-TABLE", $producto);
                //mac ont
                $servicioProdCaractMac = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "MAC ONT", $producto);
                //line profile name
                $spcLineProfileName = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "LINE-PROFILE-NAME", $producto);
                //gem port
                $spcGemPort = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "GEM-PORT", $producto);
                //ssid
                $spcSsid = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "SSID", $producto);
                //numero pc
                $spcNumeroPc = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "NUMERO PC", $producto);
                //password
                $spcPassword = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "PASSWORD SSID", $producto);
                //modo Operacion
                $spcModoOperacion = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "MODO OPERACION", $producto);
            }

            if($booleanTipoMedio && (($tipoMedio->getNombreTipoMedio()=="UTP" || 
                $tipoMedio->getNombreTipoMedio()=="Fibra Optica"  || 
                $tipoMedio->getNombreTipoMedio()=="Radio"         ||
                $tipoMedio->getNombreTipoMedio()=="TERCERIZADA"   ||
                $tipoMedio->getNombreTipoMedio()=="SATELITAL")    ||
                ($tipoMedio->getNombreTipoMedio()=="FTTx" && $booleanTipoRedGpon) ) &&
                $prefijoEmpresa == "TN")
            {
                
                try
                {
                    $arrayParametrosWs["intIdElemento"] = $servicioTecnico->getElementoId();
                    $arrayParametrosWs["intIdServicio"] = $idServicio;

                    //PE ELEMENTO-----------------------------------------------------------------------------------------
                    if($booleanTipoRedGpon)
                    {
                        $objElementoRouter = $this->serviceTecnico->getPeByOlt($arrayParametrosWs);
                    }
                    else
                    {
                        $objElementoRouter = $this->serviceTecnico->getPeBySwitch($arrayParametrosWs);
                    }

                    if(is_object($objElementoRouter))
                    {
                        $strNombrePe = $objElementoRouter->getNombreElemento();
                    }
                }
                catch(\Exception $e)
                {
                    $strNombrePe = "";
                }

                //ANILLO------------------------------------------------------------------------------------------
                    $spcAnillo  = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                    ->findOneBy(array(  "elementoId"    => $servicioTecnico->getElementoId(),
                                                                        "detalleNombre" => "ANILLO",
                                                                        "estado"        => "Activo"));

                    //elementos clientes------------------------------------------------------------------------------------------------
                    if($boolEsFibraRuta || $tipoMedio->getNombreTipoMedio()=="Radio")
                    {
                        $intInterfaceElementoClienteId = $servicioTecnico->getInterfaceElementoClienteId();

                        if($intInterfaceElementoClienteId > 0)
                        {
                            //se verifica si es GPON
                            if($booleanTipoRedGpon)
                            {
                                //obtener cpe o ont
                                $objElementoCpe = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                            ->find($servicioTecnico->getElementoClienteId());
                                //buscar ont
                                if($nombreProducto != "DATOS SAFECITY")
                                {
                                    $objInfoEnlaceOnt = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                        ->findOneBy(array( "interfaceElementoFinId" => $intInterfaceElementoClienteId, 
                                                                           "estado"                 => "Activo"));
                                    if(is_object($objInfoEnlaceOnt))
                                    {
                                        $objInterfaceOnt = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                                    ->find($objInfoEnlaceOnt->getInterfaceElementoIniId());
                                        if(is_object($objInterfaceOnt))
                                        {
                                            $objElementoOnt = $objInterfaceOnt->getElementoId();
                                        }
                                    }
                                }
                            }
                            else if ($servicioTecnico->getInterfaceElementoConectorId())
                            {                                
                                //buscar roseta
                                $arrayParametrosRos = array('interfaceElementoConectorId'   => $servicioTecnico->getInterfaceElementoConectorId(),
                                                            'tipoElemento'                  => "ROSETA");
                                $arrayRespuestaRos = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                        ->getElementoClienteByTipoElemento($arrayParametrosRos);
                                if($arrayRespuestaRos['msg'] == "FOUND")
                                {
                                    $objElementoRos  = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                            ->find($arrayRespuestaRos['idElemento']);
                                }

                                //buscar transceiver
                                $arrayParametrosTrans = array('interfaceElementoConectorId'   => $intInterfaceElementoClienteId,
                                                            'tipoElemento'                  => "TRANSCEIVER");
                                $arrayRespuestaTrans = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                        ->getElementoClienteByTipoElemento($arrayParametrosTrans);
                                if($arrayRespuestaTrans['msg'] == "FOUND")
                                {
                                    $objElementoTrans  = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                            ->find($arrayRespuestaTrans['idElemento']);
                                }

                                //buscar cpe
                                $arrayParametrosCpe = array('interfaceElementoConectorId'   => $intInterfaceElementoClienteId,
                                                            'tipoElemento'                  => "CPE");
                                $arrayRespuestaCpe = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                          ->getElementoClienteByTipoElemento($arrayParametrosCpe);
                                
                                if($arrayRespuestaCpe['msg'] == "FOUND")
                                {
                                    $objElementoCpe  = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                            ->find($arrayRespuestaCpe['idElemento']);                                   
                                }                                                                                                  
                                else //Si no encuentra CPE va a buscar el ROUTER-WIFI relacionado por NODO WIFI
                                {
                                    $arrayParametrosCpe = array('interfaceElementoConectorId'   => $intInterfaceElementoClienteId,
                                                                'tipoElemento'                  => "ROUTER");

                                    $arrayRespuestaCpe = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                              ->getElementoClienteByTipoElemento($arrayParametrosCpe);
                                    
                                    if($arrayRespuestaCpe['msg'] == "FOUND")
                                    {
                                        $objElementoCpe  = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                ->find($arrayRespuestaCpe['idElemento']);

                                    }
                                }
                            }
                            else
                            {
                                //Si es Fibra Optica ( MIgrado )
                                if($tipoMedio->getNombreTipoMedio()=="Fibra Optica")
                                {                                
                                    $objElementoCpe  = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                            ->find($servicioTecnico->getElementoClienteId());                                                                          
                                }
                                else //Si es Radio
                                {
                                    $objInterfaceRad  = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                             ->find($intInterfaceElementoClienteId);

                                    $objElementoRos   = $objInterfaceRad->getElementoId(); //Radio


                                    $objEnlaceRosTrans  = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                               ->findOneBy(array(  "interfaceElementoIniId"    => $intInterfaceElementoClienteId,
                                                                                   "estado"                    => "Activo"));                    
                                    
                                    //Para obtener la MAC de la Radio
                                    $objDetalleElementoRa = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                 ->findOneBy(array('elementoId'    => $objElementoRos->getId(),
                                                                                   'detalleNombre' => 'MAC',
                                                                                   'estado'        => 'Activo')
                                                                             );
                                    if($objDetalleElementoRa)
                                    {
                                        $strMacRadio = $objDetalleElementoRa->getDetalleValor();
                                    }
                                    
                                    if(is_object($objEnlaceRosTrans))
                                    {
                                        $objInterfaceTrans  = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                ->find($objEnlaceRosTrans->getInterfaceElementoFinId());

                                        if($objInterfaceTrans->getElementoId()->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento()=="TRANSCEIVER")
                                        {
                                            $objElementoTrans   = $objInterfaceTrans->getElementoId();

                                            $objInterfaceTransFin  = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                        ->findOneBy(array(  "nombreInterfaceElemento"   => "eth1",
                                                                                            "elementoId"                => $objElementoTrans->getId()));

                                            $objEnlaceTransCpe  = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                                        ->findOneBy(array(  "interfaceElementoIniId"    => $objInterfaceTransFin->getId(),
                                                                                            "estado"                    => "Activo"));

                                            $objInterfaceCpe  = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                        ->find($objEnlaceTransCpe->getInterfaceElementoFinId());
                                            $objElementoCpe   = $objInterfaceCpe->getElementoId();
                                        }
                                        else                        
                                        { 
                                            $objElementoCpe = $objInterfaceTrans->getElementoId();
                                            $objInterfaceTrans = null;
                                        }                                        
                                    }
                                    else
                                    {
                                        $servicioProdCaractMacCpe  = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                          ->findOneBy(array(  "elementoId"    => $objElementoRos->getId(),
                                                                                              "detalleNombre" => "MAC",
                                                                                              "estado"        => "Activo"));
                                        
                                        if($servicioProdCaractMacCpe)
                                        {
                                            $strMacCpe = $servicioProdCaractMacCpe->getDetalleValor();
                                        }
                                    }                                    
                                }
                            }
                        }                        
                    }
                    else
                    {
                        //Se obtiene el CPE dependiendo del tipo medio de UM                        
                        //UTP
                        if($tipoMedio->getNombreTipoMedio()=="UTP"         || 
                           $tipoMedio->getNombreTipoMedio()=="TERCERIZADA" ||
                           $tipoMedio->getNombreTipoMedio()=="SATELITAL" )                            
                        {
                            if($servicioTecnico->getElementoClienteId())
                            {
                                $objElementoCpe  = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                         ->find($servicioTecnico->getElementoClienteId());
                            }
                        }
                        else //Fibra - DIRECTA ( Se busca el CPE a partir de la ROSETA )
                        {
                             //buscar roseta
                            $arrayParametrosRos = array('interfaceElementoConectorId'   => $servicioTecnico->getInterfaceElementoId(),
                                                        'tipoElemento'                  => "ROSETA");
                            $arrayRespuestaRos = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                    ->getElementoClienteByTipoElemento($arrayParametrosRos);
                            if($arrayRespuestaRos['msg'] == "FOUND")
                            {
                                $objElementoRos  = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                        ->find($arrayRespuestaRos['idElemento']);
                            }

                            //buscar transceiver
                            $arrayParametrosTrans = array('interfaceElementoConectorId'   => $servicioTecnico->getInterfaceElementoClienteId(),
                                                        'tipoElemento'                  => "TRANSCEIVER");
                            $arrayRespuestaTrans = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                    ->getElementoClienteByTipoElemento($arrayParametrosTrans);
                            if($arrayRespuestaTrans['msg'] == "FOUND")
                            {
                                $objElementoTrans  = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                        ->find($arrayRespuestaTrans['idElemento']);
                            }
                            
                            $arrayParametrosCpe = array('interfaceElementoConectorId'   => $servicioTecnico->getInterfaceElementoClienteId(),
                                                        'tipoElemento'                  => "CPE");
                            
                            $arrayRespuestaCpe = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                      ->getElementoClienteByTipoElemento($arrayParametrosCpe);
                            
                            if($arrayRespuestaCpe['msg'] == "FOUND")
                            {
                                $objElementoCpe  = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                        ->find($arrayRespuestaCpe['idElemento']);
                            }
                            else //Si no encuentre conectado el CPE busca el ROUTER ( nodo WIFI )
                            {                      
                                $arrayParametrosCpe = array('interfaceElementoConectorId'   => $servicioTecnico->getInterfaceElementoClienteId(),
                                                            'tipoElemento'                  => "ROUTER");
                            
                                $arrayRespuestaCpe = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                          ->getElementoClienteByTipoElemento($arrayParametrosCpe);

                                if($arrayRespuestaCpe['msg'] == "FOUND")
                                {
                                    $objElementoCpe  = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                            ->find($arrayRespuestaCpe['idElemento']);
                                }
                                else //Si no encuentra CPE va a buscar el ROUTER o CPE directo ( MIGRADOS )
                                {
                                    if($servicioTecnico->getElementoClienteId())
                                    {
                                        $objElementoCpe  = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                ->find($servicioTecnico->getElementoClienteId());    
                                    }
                                }
                            }
                        }                                                                       
                    }                                        

                    //----------------------MAC DEL CPE----------------------//
                    
                    $strNombreInterfaceCpeCliente = 'N/A';
                    $boolBusquedaMacDirecto       = false;
                    
                    //SI es Datacenter con propiedad cliente se obtiene la mac directamento desde la interface sin 
                    //preguntar por enlaces ya que no existen
                    if(is_object($objElementoCpe) && strpos($servicio->getProductoId()->getGrupo(),'DATACENTER')!==false)
                    {
                        $objDetalleElemento = $this->emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                                      ->findOneBy(array('elementoId'    => $objElementoCpe->getId(),
                                                                                        'estado'        => 'Activo',
                                                                                        'detalleNombre' => 'PROPIEDAD',
                                                                                        'detalleValor'  => 'CLIENTE'));
                        if(is_object($objDetalleElemento))
                        {
                            $boolBusquedaMacDirecto = true; 
                        }
                    }
                    else//para todos los casos normales
                    {
                        $boolBusquedaMacDirecto = $boolEsPseudoPe;
                    }
                    
                    if($servicio->getTipoOrden() == 'C')
                    {
                        $arrayParametrosMac = array(
                                                'intIdServicio'     => $idServicio,
                                                'boolEsPseudoPe'    => false,
                                                'strTipoOrden'      => $servicio->getTipoOrden(),
                                                'intIdTipoMedio'    => $servicioTecnico->getUltimaMillaId()
                                                );
                        $arrayResultadoInterface = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                           ->getMacCpeCambioTipoMedioInterface($arrayParametrosMac);
                    }
                    else
                    {
                        $arrayResultadoInterface = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                        ->getMacCpePorServicioInterface($idServicio,$boolBusquedaMacDirecto);
                    }
                    if($arrayResultadoInterface)
                    {
                        $strNombreInterfaceCpeCliente = $arrayResultadoInterface['nombreInterface'];
                        $strMacCpe                    = $arrayResultadoInterface['mac'];
                    }

                    //capacidad1--------------------------------------------------------------------------------------------------------------------
                    $spcCapacidad1 = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "CAPACIDAD1", $producto);
                    
                    if(is_object($spcCapacidad1))
                    {
                        $strCapacidadUno  = $spcCapacidad1->getValor();
                    }

                    //capacidad2------------------------------------------------------------------------------------------------------------------------
                    $spcCapacidad2 = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "CAPACIDAD2", $producto);
                    
                    if(is_object($spcCapacidad2))
                    {
                        $strCapacidadDos  = $spcCapacidad2->getValor();
                    }

                    //zona------------------------------------------------------------------------------------------------------------------------
                    $spcZona = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "ZONA", $producto);

                    if($producto->getNombreTecnico() == "L3MPLS" || $producto->getNombreTecnico() == "CONCINTER" ||
                       $producto->getNombreTecnico() == "DATOSDC" || $producto->getNombreTecnico() == "L3MPLS SDWAN"
                       || $producto->getNombreTecnico() == "SAFECITYDATOS" || $producto->getNombreTecnico() == "SAFECITYWIFI")
                    {
                        //Si el Servicio es Concentrador Backup obtiene sus capacidades del Concentrador Principal
                        if($producto->getEsConcentrador() == 'SI' && $servicioTecnico->getTipoEnlace() == 'BACKUP')
                        {
                            $arrayCapacidades = $this->serviceTecnico->getArrayCapacidadesConcentradorBackup($servicio);
                            
                            if(isset($arrayCapacidades['intCapacidadUno']))
                            {
                                $strCapacidadUno = $arrayCapacidades['intCapacidadUno'];
                            }

                            if(isset($arrayCapacidades['intCapacidadDos']))
                            {
                                $strCapacidadDos = $arrayCapacidades['intCapacidadDos'];
                            }
                        }
                        
                        //default gateway-------------------------------------------------------------------------------------------------------------------
                        $spcDefaultGateway = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "DEFAULT_GATEWAY", $producto);
                    
                        //protocolo------------------------------------------------------------------------------------------------------------------------
                        $objProtocolo = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "PROTOCOLO_ENRUTAMIENTO", $producto);
                    
                        //vlan------------------------------------------------------------------------------------------------------------------
                        $strTipoVlan = "VLAN";
                        if($producto->getNombreTecnico() == 'SAFECITYWIFI')
                        {
                            $strTipoVlan  = "VLAN SSID";

                            $strVlanAdmin = "VLAN ADMIN";
                        }
                        $spcVlan      = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, $strTipoVlan, $producto);

                        $objVlanAdmin = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, $strVlanAdmin, $producto);

                        if(is_object($spcVlan))
                        {
                            if($producto->getNombreTecnico() == 'SAFECITYDATOS' || $producto->getNombreTecnico() == 'SAFECITYWIFI')
                            {
                                $objPerEmpRolCarVlan = null;
                                $spcVlan = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                ->find($spcVlan->getValor());


                                if(is_object($objVlanAdmin))
                                {
                                    $objVlanAd = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                    ->find($objVlanAdmin->getValor());

                                    if(is_object($objVlanAd))
                                    {            
                                        $strVlanAd = $objVlanAd->getDetalleValor();
                                    }
                                }
                            }
                            else
                            {
                                $objPerEmpRolCarVlan  = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                            ->find($spcVlan->getValor());

                                $spcVlan  = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->find($objPerEmpRolCarVlan->getValor());
                            }
                        }
                        if(is_object($spcVlan))
                        {
                            $strVlan      = $spcVlan->getDetalleValor();
                        }

                        //vrf------------------------------------------------------------------------------------------------------------------
                        $strTipoVrf = "VRF";
                        if($producto->getNombreTecnico() == 'SAFECITYWIFI')
                        {
                            $strTipoVrf  = "VRF SSID";
                            $strVrfAdmin = "VRF ADMIN";
                        }
                        $objSpcVrf   = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, $strTipoVrf, $producto);

                        $objVrfAdmin = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, $strVrfAdmin, $producto);


                        if(is_object($objSpcVrf))
                        {
                            $objVrf    = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                    ->find($objSpcVrf->getValor());
                            
                            
                            if(is_object($objVrf) )
                            {
                                $strVrf = $objVrf->getValor();
                            }
                        }

                        if(is_object($objVrfAdmin))
                        {
                            $objVrfAd  = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                           ->find($objVrfAdmin->getValor());

                            if(is_object($objVrfAd))
                            {
                                $strVrfAd = $objVrfAd->getValor();
                            }                              
                        }

                        //Consulta de ip controladora para wifi gpon
                        if($producto->getNombreTecnico() == 'SAFECITYWIFI')
                        {
                            $strIpControladora = "IP CONTROLADORA";

                            $objIpControladora   = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, $strIpControladora, $producto);

                            if(is_object($objIpControladora))
                            {
                                $strIpControladora = $objIpControladora->getValor();
                            }
                        }

                        //AS PRIVADO--------------------------------------------------------------------------------------------------------------
                        if(is_object($objPerEmpRolCarVlan))
                        {
                            $caractAsPrivado  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array("descripcionCaracteristica" => "AS_PRIVADO"));

                            $objAsPrivado  = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                        ->findOneBy(array("caracteristicaId"    => $caractAsPrivado,
                                                                          "estado"              => "Activo",
                                                                          "personaEmpresaRolId" => $objPerEmpRolCarVlan->getPersonaEmpresaRolId()));
                        }

                        //VPN--------------------------------------------------------------------------------------------------------------
                        if(is_object($objVrf))
                        {
                            $strCaractVpn = "VPN";
                            if($booleanTipoRedGpon && $producto->getNombreTecnico() == "L3MPLS")
                            {
                                $strCaractVpn = "VPN_GPON";
                            }
                            $objCaractVpn  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array("descripcionCaracteristica" => $strCaractVpn));

                            $objVpn = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                        ->findOneBy(array("caracteristicaId"          => $objCaractVpn,
                                                                          "estado"                    => "Activo",
                                                                          "id"                        => $objVrf->getPersonaEmpresaRolCaracId()));
                        }

                        //RD_ID--------------------------------------------------------------------------------------------------------------
                        if(is_object($objVpn))
                        {
                            $caractRdId  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array("descripcionCaracteristica" => "RD_ID"));

                            $objRdId  = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                        ->findOneBy(array("caracteristicaId"          => $caractRdId,
                                                                          "estado"                    => "Activo",
                                                                          "personaEmpresaRolCaracId"  => $objVpn->getId()));
                        }
                        //obtengo t cont
                        $objTCont = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "T-CONT", $producto);
                    }
                    else if($producto->getNombreTecnico() == "DATOS SAFECITY")
                    {
                        //vlan------------------------------------------------------------------------------------------------------------------
                        $objVlan = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "VLAN", $producto);
                        if( is_object($objVlan) )
                        {
                            $objVlan = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')->find($objVlan->getValor());
                            if(is_object($objVlan))
                            {
                                $strVlan = $objVlan->getDetalleValor();
                            }
                        }
                        //vrf------------------------------------------------------------------------------------------------------------------
                        $objVrf = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "VRF", $producto);
                        if(is_object($objVrf))
                        {
                            $objVrf  = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->find($objVrf->getValor());
                            if(is_object($objVrf))
                            {
                                $strVrf = $objVrf->getValor();
                            }
                        }
                        //vpn------------------------------------------------------------------------------------------------------------------
                        if(is_object($objVrf))
                        {
                            $objCaractVpn  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array("descripcionCaracteristica" => "VPN"));
                            $objVpn = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                        ->findOneBy(array("caracteristicaId"          => $objCaractVpn,
                                                                          "estado"                    => "Activo",
                                                                          "id"                        => $objVrf->getPersonaEmpresaRolCaracId()));
                        }
                        //rd-id----------------------------------------------------------------------------------------------------------------
                        if(is_object($objVpn))
                        {
                            $objCaractRdId  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array("descripcionCaracteristica" => "RD_ID"));
                            $objRdId  = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                        ->findOneBy(array("caracteristicaId"          => $objCaractRdId,
                                                                          "estado"                    => "Activo",
                                                                          "personaEmpresaRolCaracId"  => $objVpn->getId()));
                        }
                        //obtengo t cont
                        $objTCont = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "T-CONT", $producto);
                    }
                    else
                    {
                        //vlan------------------------------------------------------------------------------------------------------------------
                        $spcVlan = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "VLAN", $producto);

                        if( $spcVlan )
                        {
                            $spcVlan  = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                             ->find($spcVlan->getValor());
                            
                            if(is_object($spcVlan))
                            {
                                $strVlan = $spcVlan->getDetalleValor();
                            }
                        }

                        //Vrf para INTMPLS
                        $arrayParametrosResultado = $this->emGeneral->getRepository("schemaBundle:AdmiParametroDet")
                                                         ->getOne('VRF-INTERNET',
                                                                  'TECNICO',
                                                                  '',
                                                                  'VRF-INTERNET',
                                                                  '','','','','',
                                                                  $idEmpresa,
                                                                  null
                                                                  );

                        $strVrf = isset($arrayParametrosResultado['valor1'])?$arrayParametrosResultado['valor1']:'';
                        
                        //AsPrivado para INTMPLS siempre y cuando posea protocolo de enrutamiento BGP
                        $objProtocolo = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "PROTOCOLO_ENRUTAMIENTO", $producto);
                        
                        if(is_object($objProtocolo))
                        {
                            if($objProtocolo->getValor() == 'BGP')
                            {
                                $objAsPrivado = $this->serviceTecnico
                                                     ->getServicioProductoCaracteristica($servicio, "AS_PRIVADO", $producto);
                            }
                        }
                        //obtengo t cont
                        $objTCont = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "T-CONT", $producto);
                    }

                    //si es wifi la mac del cliente es la del wifi
                    if($nombreProducto == 'INTERNET WIFI')
                    {
                        $objElementoCpe = '';
                        if($servicioTecnico->getElementoClienteId())
                        {
                            $objElementoCpe = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                      ->find($servicioTecnico->getElementoClienteId());
                        }
                        
                        $servicioProdCaractMacCpe = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                            ->findOneBy(array("elementoId" => $servicioTecnico->getElementoClienteId(),
                                                                                              "detalleNombre" => "MAC",
                                                                                              "estado" => "Activo"));
                        if($servicioProdCaractMacCpe)
                        {
                            $strMacCpe = $servicioProdCaractMacCpe->getDetalleValor();
                        }
                        
                        $servicioProdCaractMac = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "MAC WIFI", $producto);

                        $objIpeCpe = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                             ->findOneByElementoId($servicioTecnico->getElementoClienteId());

                        //elemento conector
                        $objIpConector = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                 ->findOneByElementoId($servicioTecnico->getElementoConectorId());
                        $detalleMacConector = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                      ->findOneBy(array("elementoId" => $servicioTecnico->getElementoConectorId(),
                                                                                        "detalleNombre" => "MAC",
                                                                                        "estado" => "Activo"));
                        //datos internos del cpe
                        $spcSsid            = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "SSID", $producto);
                        $spcNumeroPc        = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "NUMERO PC", $producto);
                        $spcPassword        = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "PASSWORD SSID", $producto);
                        $spcModoOperacion   = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "MODO OPERACION", $producto);
                        $vlanWifi           = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "VLAN", $producto);

                        //consulto si el servicio tiene los datos de la interface del router wifi
                        $servicioProdCaractInterface = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, 
                                                                                                                "INTERFACE_ELEMENTO_ID", 
                                                                                                                $producto);
                        if($servicioProdCaractInterface)
                        {
                            $objInterfaceWifi = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                        ->find($servicioProdCaractInterface->getValor());
                            if($objInterfaceWifi)
                            {
                                $strElementoNodo = '';
                                $strModeloElementoNodo = '';
                                $strNombreElementoPadre = '';
                                
                                $objElementoWifi = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                           ->find($objInterfaceWifi->getElementoId());
                                //consulto para verificar si es un SWITCH o un ROUTER
                                if($objElementoWifi)
                                {
                                    $strTipoElemento = $objElementoWifi->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
                                    
                                    $strSwWifi              = 'N/A';
                                    $strInterfaceSwWifi     = 'N/A';
                                    $strModeloSwWifi        = 'N/A';
                                    $strRouterWifi          = $objInterfaceWifi->getElementoId()->getNombreElemento();
                                    $strInterfaceRouterWifi = $objInterfaceWifi->getNombreInterfaceElemento();
                                    $strModeloRouter        = $objInterfaceWifi->getElementoId()->getModeloElementoId();
                                    $idElementoRelacion     = $objInterfaceWifi->getElementoId();
                                    
                                    //si es switch busco el enlace hasta el router
                                    if($strTipoElemento == 'SWITCH')
                                    {
                                                                                //obtengo el sw de backbone y lo relaciono al odf
                                        $idInterfaceElementoWifi = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                        ->getInterfaceElementoPadre($objInterfaceWifi->getElementoId()->getId(), 
                                                                                                    'ELEMENTO', 
                                                                                                    'ROUTER');

                                        if($idInterfaceElementoWifi)
                                        {
                                            $objInterfacePadre = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                                         ->find($idInterfaceElementoWifi);
                                            if($objInterfacePadre)
                                            {
                                                $strSwWifi              = $strRouterWifi;
                                                $strInterfaceSwWifi     = $strInterfaceRouterWifi;
                                                $strModeloSwWifi        = $strModeloRouter;
                                                $strRouterWifi          = $objInterfacePadre->getElementoId()->getNombreElemento();
                                                $strInterfaceRouterWifi = $objInterfacePadre->getNombreInterfaceElemento();
                                                $strModeloRouter        = $objInterfacePadre->getElementoId()->getModeloElementoId();
                                                $idElementoRelacion     = $objInterfacePadre->getElementoId()->getId();
                                                
                                            }
                                            
                                        }                                        
                                    }

                                }
                                
                                $objRelacionWifi =  $this->emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                                            ->findOneBy(array('elementoIdB' => $idElementoRelacion,
                                                                                              'estado'      => 'Activo'));
                                if($objRelacionWifi)
                                {
                                    $objNodo =  $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                        ->find($objRelacionWifi->getElementoIdA());
                                    if($objNodo)
                                    {
                                        $strElementoNodo = $objNodo->getNombreElemento();
                                        $strModeloElementoNodo = $objNodo->getModeloElementoId();
                                    }
                                }
                                
                                $datosWifi = array(
                                        'strModeloElementoNodo' => $strModeloElementoNodo,
                                        'strElementoNodo'       => $strElementoNodo,
                                        'strModeloRouter'       => $strModeloRouter,
                                        'strRouterWifi'         => $strRouterWifi,
                                        'strInterfaceRouterWifi'=> $strInterfaceRouterWifi,
                                        'strModeloSwWifi'       => $strModeloSwWifi,
                                        'strSwWifi'             => $strSwWifi,
                                        'strInterfaceSwWifi'    => $strInterfaceSwWifi);
                               
                            }
                        }                        
                    }
                    
                    if($descripcionProducto =='WIFI Alquiler Equipos')
                    {
                        //datos internos del cpe
                        $spcSsid            = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "SSID", $producto);
                        $spcNumeroPc        = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "NUMERO PC", $producto);
                        $spcPassword        = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "PASSWORD SSID", $producto);
                        $spcModoOperacion   = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "MODO OPERACION", $producto);
                        $vlanWifi           = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "VLAN", $producto);
                    }
                    
                    //Información CPE ( PROPIEDAD y ADMINISTRA )
                    if(is_object($objElementoCpe))
                    {
                        $objDetalleElementoPropiedadCpe = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                               ->findOneBy(array('elementoId'    => $objElementoCpe->getId(),
                                                                                 'detalleNombre' => 'PROPIEDAD',
                                                                                 'estado'        => 'Activo')
                                                                          );
                        if(is_object($objDetalleElementoPropiedadCpe))
                        {
                            $strPropiedadCpe = $objDetalleElementoPropiedadCpe->getDetalleValor();
                        }
                        
                        $objDetalleElementoAdministraCpe = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                ->findOneBy(array('elementoId'    => $objElementoCpe->getId(),
                                                                                  'detalleNombre' => 'ADMINISTRA',
                                                                                  'estado'        => 'Activo')
                                                                           );
                        if(is_object($objDetalleElementoAdministraCpe))
                        {
                            $strAdministraCpe = $objDetalleElementoAdministraCpe->getDetalleValor();
                        }
                    }
            }


            //backbone----------------------------------------------------------------------------------------------------------------------------
            if($servicioTecnico->getInterfaceElementoId())
            {
                $interfaceElementoBackbone  = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                   ->find($servicioTecnico->getInterfaceElementoId());
            }
            $ip                         = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                               ->findOneBy( array ("elementoId"=>$servicioTecnico->getElementoId()));

            //verificar si el servicio tiene Elemento Contenedor (caja)
            if($servicioTecnico->getElementoContenedorId()!=null
               || ($booleanTipoMedio && $tipoMedio->getNombreTipoMedio()=="Radio" && $prefijoEmpresa == "TN"))
            {
                if ($servicioTecnico->getInterfaceElementoConectorId()!= null)
                {
                    //obtener el elemento contenedor (caja)
                    $elementoContenedor             = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                          ->find($servicioTecnico->getElementoContenedorId());
                    //obtener elemento ubica
                    $objElementoUbica       = $this->emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                                      ->findOneBy( array (
                                                                          "elementoId"=>$servicioTecnico->getElementoContenedorId()
                                                                        ));
                    //obtener la interface del elemento conector (splitter)
                    $interfaceElementoConector  = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                       ->find($servicioTecnico->getInterfaceElementoConectorId());
                    $enlaceSegundoNivel         = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                       ->findOneBy(array('interfaceElementoFinId'=>$interfaceElementoConector->getId()));

                    //verificar si existe el enlace de segundo nivel
                    if($enlaceSegundoNivel)
                    {
                        $enlaceSplitter             = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                           ->findOneBy(array('interfaceElementoFinId'=>$enlaceSegundoNivel->getInterfaceElementoIniId()));
                        if($enlaceSplitter)
                        {
                            $bufferHilo = $enlaceSplitter->getBufferHiloId();
                        }
                    }

                    if($objElementoUbica != null && is_object($objElementoUbica) && $objElementoUbica->getUbicacionId() != null
                                                 && is_object($objElementoUbica->getUbicacionId()))
                    {
                     //obtener datos de ubicación del elemento
                        $arrayElementoUbicacionTmp  = $this->emInfraestructura->getRepository('schemaBundle:InfoUbicacion')
                                                     ->findOneBy(array ("id"=> $objElementoUbica->getUbicacionId()->getId()));  
                        if($arrayElementoUbicacionTmp != null)
                        {
                            $arrayElementoUbicacion = array(
                                "direccion"  => $arrayElementoUbicacionTmp->getDireccionUbicacion(),
                                "longitud"   => $arrayElementoUbicacionTmp->getLongitudUbicacion(),
                                "latitud"    => $arrayElementoUbicacionTmp->getLatitudUbicacion()
                            );
                        }
                    }
                }
                else
                {
                    //obtener la interface del elemento conector (splitter)
                    $interfaceElementoConector  = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                       ->findOneBy(array('elementoId'              => $servicioTecnico->getElementoConectorId(),
                                                                         'nombreInterfaceElemento' => 'wlan1'));
                    $objInfoIpEleConector       = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                       ->findOneBy(array('elementoId'              => $servicioTecnico->getElementoConectorId()));
                }
            }
            else
            {
                $elementoContenedor         = null;
                $interfaceElementoConector  = null;
                $bufferHilo                 = null;
            }            

            $interfaceElementoClienteId = $servicioTecnico->getInterfaceElementoClienteId();
            if($interfaceElementoClienteId)
            {
                //cliente-ont----------------------------------------------------------------------------------------------------------------------------
                $interfaceElementoCliente   = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                   ->find($interfaceElementoClienteId);
                $elementoCliente            = $interfaceElementoCliente->getElementoId();

                //cliente-wifi--------------------------------------------------------------------------------------------------------------------------
                // se agrega filtro de estado para recuperar enlace del servicio
                $enlaceCliente              = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                   ->findOneBy(array("interfaceElementoIniId" => $interfaceElementoCliente->getId(),
                                                                     "estado"                 => 'Activo'
                                                                    )
                                                              );
            }
            
            if($prefijoEmpresa == "MD")
            {
                $arrayServiciosNh    = array();
                $arrayServiciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                            ->findByPuntoId($servicio->getPuntoId()->getId());
                foreach($arrayServiciosPunto as $objServicioPunto)
                {
                    if ($objServicioPunto->getEstado() == 'Activo' || 
                        $objServicioPunto->getEstado() == 'In-Corte' ||
                        $objServicioPunto->getEstado() == 'Cancel')
                    {
                        $objProductoServicio = $objServicioPunto->getProductoId();
                        if (is_object($objProductoServicio) && $objProductoServicio->getNombreTecnico() == 'NETHOME')
                        {
                            $arrayElementosNh = array();
                            
                            $objServicioTecnicoNh = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                         ->findOneBy(array( "servicioId" =>$objServicioPunto->getId()));
                            if (is_object($objServicioTecnicoNh))
                            {
                                $intIdElementoCliente          = $objServicioTecnicoNh->getElementoClienteId();
                                $intIdInterfaceElementoCliente = $objServicioTecnicoNh->getInterfaceElementoClienteId();
                                if (!empty($intIdElementoCliente) && !empty($intIdInterfaceElementoCliente))
                                {
                                    $objElementoNh      = $this->emInfraestructura
                                                               ->getRepository('schemaBundle:InfoElemento')
                                                               ->find($intIdElementoCliente);
                                    $arrayElementosNh[] = $objElementoNh;
                                    $arrayParams        = array();
                                    $arrayParams['intInterfaceElementoConectorId'] = $intIdInterfaceElementoCliente;
                                    $arrayParams['arrayData']                      = $arrayElementosNh;
                                    $arrayElementosNetHome                         = $this->emInfraestructura
                                                                                          ->getRepository('schemaBundle:InfoElemento')
                                                                                          ->getElementosNetHomeFiberByInterface($arrayParams);

                                    $arrayServiciosNh[]= array ("strProductoNetHome"    => $objProductoServicio->getDescripcionProducto(),
                                                                "arrayElementosNetHome" => $arrayElementosNetHome);
                                }
                            }
                        }
                        
                        if (is_object($objProductoServicio) && $objProductoServicio->getNombreTecnico() == 'NETFIBER')
                        {
                            $arrayElementosNh = array();
                            
                            $objServicioTecnicoNh = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                         ->findOneBy(array( "servicioId" =>$objServicioPunto->getId()));
                            if (is_object($objServicioTecnicoNh))
                            {
                                $intIdElementoCliente          = $objServicioTecnicoNh->getElementoClienteId();
                                $intIdInterfaceElementoCliente = $objServicioTecnicoNh->getInterfaceElementoClienteId();
                                if (!empty($intIdElementoCliente) && !empty($intIdInterfaceElementoCliente))
                                {
                                    $objElementoNf      = $this->emInfraestructura
                                                               ->getRepository('schemaBundle:InfoElemento')
                                                               ->find($intIdElementoCliente);
                                    $arrayElementosNf[] = $objElementoNf;
                                    $arrayParams        = array();
                                    $arrayParams['intInterfaceElementoConectorId'] = $intIdInterfaceElementoCliente;
                                    $arrayParams['arrayData']                      = $arrayElementosNf;
                                    $arrayElementosNetFiber                        = $this->emInfraestructura
                                                                                          ->getRepository('schemaBundle:InfoElemento')
                                                                                          ->getElementosNetHomeFiberByInterface($arrayParams);

                                    $arrayServiciosNf[]= array ("strProductoNetFiber"    => $objProductoServicio->getDescripcionProducto(),
                                                                "arrayElementosNetFiber" => $arrayElementosNetFiber);
                                }
                            }
                        }
                        
                        if (is_object($objProductoServicio) && 
                           (($objProductoServicio->getNombreTecnico() == 'INTERNET SMALL BUSINESS') ||
                            ($objProductoServicio->getNombreTecnico() == 'TELCOHOME')))
                        {
                            $objServicioTecnicoNh = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                         ->findOneBy(array( "servicioId" =>$objServicioPunto->getId()));
                            $intIdMarcaTellion    = 4461;
                            if (is_object($objServicioTecnicoNh))
                            {
                                $intIdElementoCliente          = $objServicioTecnicoNh->getElementoClienteId();
                                $intIdInterfaceElementoCliente = $objServicioTecnicoNh->getInterfaceElementoClienteId();
                                if (!empty($intIdElementoCliente) && !empty($intIdInterfaceElementoCliente))
                                {
                                    $objElementoProducto      = $this->emInfraestructura
                                                                     ->getRepository('schemaBundle:InfoElemento')
                                                                     ->find($intIdElementoCliente);
                                    $objElementoCpe           = $objElementoProducto;                                    
                                    if($objElementoCpe->getModeloElementoId()->getMarcaElementoId()->getId() == $intIdMarcaTellion)
                                    {
                                        $arrayParams['intInterfaceElementoClienteId'] = $intIdInterfaceElementoCliente;
                                        $arrayElementoSmall                           = $this->emInfraestructura
                                                                                             ->getRepository('schemaBundle:InfoElemento')
                                                                                             ->getElementosSmallByInterface($arrayParams);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if($enlaceCliente!=null)
            {
                $interfaceElementoClienteWifi   = $enlaceCliente->getInterfaceElementoFinId();
                $elementoClienteWifi            = $interfaceElementoClienteWifi->getElementoId();
                //se agregan validaciones para recuperar información de elemento Smart Wifi
                $objElementoClienteSmartWifi    = $elementoClienteWifi;
                $enlaceClienteSegundoNivel      = $this->emInfraestructura
                                                       ->getRepository('schemaBundle:InfoEnlace')
                                                       ->findOneBy(array("interfaceElementoIniId" => $interfaceElementoClienteWifi->getId(),
                                                                         "estado"                 => 'Activo'
                                                                         )
                                                                  );
                if (is_object($enlaceClienteSegundoNivel))
                {
                    $objElementoClienteSmartWifi = $enlaceClienteSegundoNivel->getInterfaceElementoFinId()->getElementoId();
                }
                if (
                    (strpos($objElementoClienteSmartWifi->getNombreElemento(), 'SmartWifi') !== false) ||
                    (strpos($objElementoClienteSmartWifi->getNombreElemento(), 'ApWifi') !== false) ||
                    (strpos($objElementoClienteSmartWifi->getNombreElemento(), 'ExtenderDualBand') !== false)
                   )
                {
                    if (!is_object($enlaceClienteSegundoNivel))
                    {
                        $elementoClienteWifi      = null;                            
                    }
                    if (is_object($elementoClienteWifi) &&
                        (
                             (strpos($elementoClienteWifi->getNombreElemento(), 'SmartWifi') !== false) ||
                             (strpos($elementoClienteWifi->getNombreElemento(), 'ApWifi') !== false) ||
                             (strpos($elementoClienteWifi->getNombreElemento(), 'ExtenderDualBand') !== false) 
                        )
                       )
                    {
                            $elementoClienteWifi      = null;
                    }
                }
                $arrayParams['intInterfaceElementoConectorId'] = $interfaceElementoClienteId;
                $arrayParams['strTipoSmartWifi']               = 'SmartWifi';
                $arrayParams['arrayData']                      = array();
                $arrayElementosSmartWifi                       = $this->emInfraestructura
                                                                      ->getRepository('schemaBundle:InfoElemento')
                                                                      ->getElementosSmartWifiByInterface($arrayParams);
                
                $arrayParams['intInterfaceElementoConectorId'] = $interfaceElementoClienteId;
                $arrayParams['strTipoApWifi']                  = 'ApWifi';
                $arrayParams['arrayData']                      = array();
                $arrayElementosApWifi                          = $this->emInfraestructura
                                                                      ->getRepository('schemaBundle:InfoElemento')
                                                                      ->getElementosApWifiByInterface($arrayParams);
                $arrayParams['strTipoSmartWifi']               = 'ExtenderDualBand';
                $arrayParams['arrayData']                      = array();
                $arrayElementosExtenderDualBand                = $this->emInfraestructura
                                                                      ->getRepository('schemaBundle:InfoElemento')
                                                                      ->getElementosSmartWifiByInterface($arrayParams);
            }
            else
            {
                $interfaceElementoClienteWifi   = "";
                $elementoClienteWifi            = "";
            }
            
            //se agrega recuperación del tipo de aprovisionamiento del Olt en el cual se encuentra configurado el cliente
            $entityDetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                          ->findOneBy(array( "elementoId"    => $servicioTecnico->getElementoId(), 
                                                             "detalleNombre" => "APROVISIONAMIENTO_IP"));
        }
        if ($entityDetalleElemento)
        {
            $strAprovisionamiento = $entityDetalleElemento->getDetalleValor();
        }
        else
        {
            $strAprovisionamiento = "POOL";
        }
        
        $strNombreEdificio = 'N/A';      
        $strTercerizadora  = 'N/A';
        
        if($boolEsPseudoPe)
        {   
            $objServProdCaractVlanPseudoPe   = $this->serviceTecnico
                                                    ->getServicioProductoCaracteristica($servicio,'VLAN_PROVEEDOR',$producto);

             if(is_object($objServProdCaractVlanPseudoPe))
             {
                 $strVlan = $objServProdCaractVlanPseudoPe->getValor();
             }
             
             $arrayParametrosPseudoPe      = array ('idServicio' => $idServicio);
             $arrayDatosTecnicosPseudoPe   = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                               ->getDatosFactibilidadPseudoPe($arrayParametrosPseudoPe);
            
             if(isset($arrayDatosTecnicosPseudoPe['data']))
             {                
                if($strNombrePe == '')
                {
                    $strNombrePe    = $arrayDatosTecnicosPseudoPe['data']['nombrePe'];
                }
                         
                $strNombreEdificio  = $arrayDatosTecnicosPseudoPe['data']['nombreEdificio']; 
             }
             
             if(is_object($tipoMedio))                 
             {    
                 $strNombreTipoMedio = $tipoMedio->getNombreTipoMedio();
                 
                 if($strNombreTipoMedio == 'TERCERIZADA')
                 {
                    $objServProdCaractTercerizada = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, 
                                                                                                              'TERCERIZADORA', 
                                                                                                              $producto
                                                                                                              );
                    if(is_object($objServProdCaractTercerizada))
                    {
                        $objPersona = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                                        ->find(intval($objServProdCaractTercerizada->getValor()));
                        if(is_object($objPersona))
                        {
                            $strTercerizadora = $objPersona->getInformacionPersona();
                        }
                    }
                 }
                 else if($strNombreTipoMedio == 'SATELITAL')
                 {
                     $strEsElementoAdicional     = 'SI';
                     $strNombreElementoAdicional = "(hub satelital)";
                 }
             }                         
        }
        
        $strEsDataCenter = 'NO';
        $strVlanLan      = 'N/A';
        $strVlanWan      = 'N/A';
        $strFirewallDC   = 'N/A';
        $intVConnect     = 0;
        
        //Mostrar  informacion de DATACENTER
        if($prefijoEmpresa == "TN" && strpos($producto->getGrupo(),'DATACENTER')!==false)
        {
            $strEsDataCenter = 'SI';
            $strTipoRecursos = 'N/A';
            
            $objServProdCaractRecursos = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, 
                                                                                                  'TIPO_RECURSO_DC', 
                                                                                                  $producto
                                                                                                 );
            if(is_object($objServProdCaractRecursos))
            {
                $strTipoRecursos = $objServProdCaractRecursos->getValor();
            }
            
            $objServProdCaractVlanLan = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, 
                                                                                                 'VLAN_LAN', 
                                                                                                 $producto
                                                                                                );
            if(is_object($objServProdCaractVlanLan))
            {
                $objPersonaEmpresaRolCaract = $this->emComercial
                                                   ->getRepository("schemaBundle:InfoPersonaEmpresaRolCarac")
                                                   ->find($objServProdCaractVlanLan->getValor());
                if(is_object($objPersonaEmpresaRolCaract))
                {
                    $objDetalleElemento     = $this->emInfraestructura
                                                   ->getRepository("schemaBundle:InfoDetalleElemento")
                                                   ->find($objPersonaEmpresaRolCaract->getValor());
                    if(is_object($objDetalleElemento))
                    {
                        $strVlanLan = $objDetalleElemento->getDetalleValor();
                    }
                }
            }
            
            //Obtener la informacion dedicado
            if($strTipoRecursos == 'dedicado' || $producto->getNombreTecnico() == 'DATOSDC')
            {
                $objServProdCaractVlanWan = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, 
                                                                                                     'VLAN_WAN', 
                                                                                                     $producto
                                                                                                    );
                if(is_object($objServProdCaractVlanWan))
                {
                    $objPersonaEmpresaRolCaract = $this->emComercial
                                                       ->getRepository("schemaBundle:InfoPersonaEmpresaRolCarac")
                                                       ->find($objServProdCaractVlanWan->getValor());
                    
                    if(is_object($objPersonaEmpresaRolCaract))
                    {
                        $objDetalleElemento     = $this->emInfraestructura
                                                       ->getRepository("schemaBundle:InfoDetalleElemento")
                                                       ->find($objPersonaEmpresaRolCaract->getValor());
                        if(is_object($objDetalleElemento))
                        {
                            $strVlanWan = $objDetalleElemento->getDetalleValor();
                        }
                    }
                }
                
                $objServProdCaractFirewall = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, 
                                                                                                      'FIREWALL_DC', 
                                                                                                      $producto
                                                                                                     );
            
                if(is_object($objServProdCaractFirewall))
                {
                    $strFirewallDC = $objServProdCaractFirewall->getValor();
                }
            }
                       
            if(empty($strNombrePe) && ($producto->getNombreTecnico() == 'DATOSDC' || $producto->getNombreTecnico() == 'INTERNETDC'))
            {
                $intIdOficina = $servicio->getPuntoId()->getPuntoCoberturaId()->getOficinaId();

                $objOficina   = $this->emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficina);

                if(is_object($objOficina))
                {
                    $objCanton = $this->emComercial->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());

                    if(is_object($objCanton))
                    {
                        $strRegion = $objCanton->getProvinciaId()->getRegionId()->getNombreRegion();
                    }
                }

                //Obtener el Pe parametrizado dado que no existe factibilidad a nivel de backbone
                $arrayInfoPe   =  $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->getOne('ROUTERS DC - HOSTING', 
                                                          'TECNICO', 
                                                          '',
                                                          $strRegion,
                                                          '',
                                                          '',
                                                          '',
                                                          '', 
                                                          '', 
                                                          $idEmpresa);
                if(!empty($arrayInfoPe))
                {
                    $strPe         = $arrayInfoPe['valor1'];
                    $objElementoPe = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                             ->findOneByNombreElemento($strPe);
                    
                    if(is_object($objElementoPe))
                    {
                        $strNombrePe = $objElementoPe->getNombreElemento();
                    }
                }
            }
            
            //Si es L2
            if($producto->getNombreTecnico() == 'L2MPLS')
            {
                $objServProdVConnect = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, 
                                                                                                'VIRTUAL_CONNECT', 
                                                                                                $producto
                                                                                               );
                if(is_object($objServProdVConnect))
                {
                    $intVConnect = $objServProdVConnect->getValor();
                }
            }

            //Se obtiene la MAC Cpe y la Interface
            if(is_object($interfaceElementoCliente))
            {
                $strMacCpe                    = $interfaceElementoCliente->getMacInterfaceElemento();
                $strNombreInterfaceCpeCliente = $interfaceElementoCliente->getNombreInterfaceElemento();
            }
        }
        
        $objProducto = $producto;

        if($idEmpresa == 10)
        {
            $arrayRespuesta         = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                        ->getIdProductoPorServicio($idServicio);
            
            $strDescripcionProducto = $arrayRespuesta[0]['strDescipcionProducto'];

            $objProducto           = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                    ->findOneBy(array( "empresaCod"          => $idEmpresa, 
                                                                       "descripcionProducto" => $strDescripcionProducto));
            $producto = $objProducto;
        }
        
        //Consultamos si el servicio tiene relacionado a un producto secure cpe para visualizar el número de licencia y la fecha de caducidad
        $arrayParamsProdRelSecureCpe  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('PRODUCTO_RELACIONADO_SECURE_CPE',
                                                                 '',
                                                                 '',
                                                                 '',
                                                                 $strDescripcionProducto,
                                                                 '',
                                                                 '',
                                                                 '',
                                                                 '',
                                                                 $idEmpresa);
        if(isset($arrayParamsProdRelSecureCpe) && !empty($arrayParamsProdRelSecureCpe))
        {
            $intValor4                  = $arrayParamsProdRelSecureCpe["valor4"];
            $objCaracteristicaSecureCpe = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array("descripcionCaracteristica" => "ID_SERVICIO_SECURE_CPE", 
                                                                          "estado"                    => "Activo"));
        
            $objProdCaractSecureCpe     = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                            ->findOneBy(array("productoId"       => $intValor4, 
                                                                              "caracteristicaId" => $objCaracteristicaSecureCpe->getId()));

            $objServicioProductoCaracteristica = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                      ->findOneBy(array("productoCaracterisiticaId" => $objProdCaractSecureCpe->getId(),
                                                                        "valor"                     => $idServicio));
            
            if (is_object($objServicioProductoCaracteristica))
            {
                $intIdServicio                = $objServicioProductoCaracteristica->getServicioId();
                $objCaracteristicaNumLicencia = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array("descripcionCaracteristica" => "NUM LICENCIAS", 
                                                                          "estado"                    => "Activo"));
                
                $objProdCaractNumLicencia     = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                        ->findOneBy(array("productoId"       => $intValor4, 
                                                                          "caracteristicaId" => $objCaracteristicaNumLicencia->getId()));
                
                $objSerProdCaracLicencia      = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                     ->findOneBy(array("productoCaracterisiticaId" => $objProdCaractNumLicencia->getId(),
                                                                        "servicioId"               => $intIdServicio));
                
                if (is_object($objSerProdCaracLicencia))
                {
                    $intNumLicencia    = $objSerProdCaracLicencia->getValor();
                }
                
                $objCaracteristicaFecha = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array("descripcionCaracteristica" => "FECHA_EXPIRACION_SEGURIDAD_CPE", 
                                                                          "estado"                    => "Activo"));
                
                $objProdCaractFecha     = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                        ->findOneBy(array("productoId"       => $intValor4, 
                                                                          "caracteristicaId" => $objCaracteristicaFecha->getId()));
                
                $objSerProdCaracFecha   = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                        ->findOneBy(array("productoCaracterisiticaId" => $objProdCaractFecha->getId(),
                                                                          "servicioId"                => $intIdServicio));
                
                if (is_object($objSerProdCaracFecha))
                {
                    $strFechaCaducidad = $objSerProdCaracFecha->getValor();
                    if ($strFechaCaducidad == null || $strFechaCaducidad == '')
                    {
                        $strFechaCaducidad = 'Licencia de equipo no está activada';
                    }//*/
                }
                
            }
            
        }

        $arrayParDetClear= $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
        ->getOne('ESTADO_CLEAR_CHANNEL','COMERCIAL','','ESTADO_CLEAR_CHANNEL','','','','','',$idEmpresa);
        if(count($arrayParDetClear)>0)
        {
            $strDescripProducto = $arrayParDetClear["valor1"];
            if( $descripcionProducto==$strDescripProducto)
            {
                 $objServicioProductoCaracIp= $this->serviceTecnico->getServicioProductoCaracteristica($servicio,"IP_EQUIPO",$objProducto);

                 $objServicioProductoCaracInterface= $this->serviceTecnico->
                 getServicioProductoCaracteristica($servicio,"INTERFACE_EQUIPO",$objProducto);

                $objServicioProductoCaracMac = $this->serviceTecnico->getServicioProductoCaracteristica($servicio,"MAC",  $objProducto);
                
                if($objServicioProductoCaracMac==null)
                {
                    $objServicioPrincipalTecnico = $this->emComercial
                    ->getRepository('schemaBundle:InfoServicioTecnico')
                    ->findOneBy(array( "servicioId" => $servicio->getId())); 

                    if($objServicioPrincipalTecnico!=null&&$objServicioPrincipalTecnico->getTipoEnlace()=="BACKUP") 
                        {
                            $objServicioProductoCaracPrincipal= $this->serviceTecnico
                                ->getServicioProductoCaracteristica($servicio, 
                                "ES_BACKUP", 
                                $objProducto);

                            $objServicioPrincipal = $this->emComercial
                                ->getRepository('schemaBundle:InfoServicioTecnico')
                                ->findOneBy(array( "servicioId" => $objServicioProductoCaracPrincipal->getValor()));

                            $objServicioProductoCaracMac = $this->serviceTecnico
                                ->getServicioProductoCaracteristica(
                                $objServicioPrincipal->getServicioId(),"MAC",  $objProducto);
                        }
                }

                
                if($objServicioProductoCaracInterface!=null)
                {
                    $objInterfaceConsulta=!is_numeric($objServicioProductoCaracInterface->getValor())?null:
                    $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoInterface')
                    ->findOneBy(['id'=> $objServicioProductoCaracInterface->getValor()]);

                    $strNombreInterfaceCpeCliente=$objInterfaceConsulta==null?null:$objInterfaceConsulta->getNombreTipoInterface();
                  if($objInterfaceConsulta==null)
                  {
                    $strNombreInterfaceCpeCliente=!is_numeric($objServicioProductoCaracInterface
                        ->getValor())?$objServicioProductoCaracInterface->getValor():null;
                  }
                   
                }
            
                $objIpeCpe->getIp=$objServicioProductoCaracIp==null?null:$objServicioProductoCaracIp->getValor();
                $strMacCpe=$objServicioProductoCaracMac==null?null:$objServicioProductoCaracMac->getValor();

               //vrf------------------------------------------------------------------------------------------------------------------
               $objVrf = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "VRF", $objProducto);
               $strVrf = '';
               if(is_object($objVrf))
               {
                   $objVrf  = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->find($objVrf->getValor());
                   if(is_object($objVrf))
                   {
                       $strVrf = $objVrf->getValor();
                   }
               }
             
            }
        }
        
        $servicioProdCaractIndice = $this->serviceTecnico->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $producto);

        $arrayResultado = array('servicio'                      => $servicio,
                                'strTipoRed'                    => $strTipoRed,
                                'booleanTipoRedGpon'            => $booleanTipoRedGpon,
                                'booleanSegVehiculo'            => false,
                                'booleanSafeEntry'              => false,
                                'objTCont'                      => $objTCont,
                                'elemento'                      => $objInfoElementoBackbone,
                                'interfaceElemento'             => $interfaceElementoBackbone,
                                'prefijoEmpresa'                => $prefijoEmpresa,
                                'producto'                      => $producto,
                                'ip'                            => $ip,
                                'elementoContenedor'            => $elementoContenedor,
                                'interfaceElementoConector'     => $interfaceElementoConector,
                                'ipRadioBackbone'               => $objInfoIpEleConector?$objInfoIpEleConector->getIp():"",
                                'interfaceElementoCliente'      => $interfaceElementoCliente,
                                'elementoCliente'               => $elementoCliente,
                                'macCliente'                    => $servicioProdCaractMac,
                                'indiceCliente'                 => $servicioProdCaractIndice,
                                'spid'                          => $servicioProdCaractSpid,
                                'perfilCliente'                 => $servicioProdCaractPerfil,
                                'macClienteWifi'                => $servicioProdCaractMacWifi,
                                'lineProfileName'               => $spcLineProfileName,
                                'gemPort'                       => $spcGemPort,
                                'vlan'                          => $strVlan,
                                'vlanAdmin'                     => $strVlanAd,
                                'trafficTable'                  => $spcTrafficTable,
                                'scope'                         => $spcScope,
                                'tipoMedio'                     => $tipoMedio,                                
                                'macCpe'                        => $strMacCpe,
                                'interfaceCpeNombre'            => $strNombreInterfaceCpeCliente,
                                'capacidad1'                    => $strCapacidadUno,
                                'IpCpe'                         => $objIpeCpe,
                                'ipElementoConector'            => $objIpConector,
                                'detalleElementoMac'            => $detalleMacConector,            
                                'capacidad2'                    => $strCapacidadDos,
                                'capacidadProm1'                => $spcCapacidadProm1,
                                'capacidadProm2'                => $spcCapacidadProm2,
                                'vpn'                           => $objVpn,
                                'rdId'                          => $objRdId,
                                'asPrivado'                     => $objAsPrivado,
                                'vrf'                           => $strVrf,
                                'vrfAdmin'                      => $strVrfAd,
                                'ipControladoraWifiGpon'        => $strIpControladora,
                                'protocoloEnrutamiento'         => $objProtocolo,
                                'zona'                          => $spcZona,
                                'defaultGateway'                => $spcDefaultGateway,
                                'vci'                           => $spcVci,
                                'ssid'                          => $spcSsid,
                                'numeroPc'                      => $spcNumeroPc,
                                'passwordSsid'                  => $spcPassword,
                                'modoOperacion'                 => $spcModoOperacion,
                                'interfaceElementoClienteWifi'  => $interfaceElementoClienteWifi,
                                'bufferHilo'                    => $bufferHilo,
                                'elementoClienteWifi'           => $elementoClienteWifi,
                                'aprovisionamientoIp'           => $strAprovisionamiento,
                                'elementoCpe'                   => $objElementoCpe,
                                'elementoOnt'                   => $objElementoOnt,
                                'interfaceOnt'                  => $objInterfaceOnt,
                                'elementoTransceiver'           => $objElementoTrans,
                                'elementoRoseta'                => $objElementoRos,
                                'elementoRouter'                => $strNombrePe,
                                'interfaceTrans'                => $objInterfaceTrans,
                                'interfaceCpe'                  => $objInterfaceCpe,
                                'anillo'                        => $spcAnillo,
                                'datosWifi'                     => $datosWifi,
                                'vlanWifi'                      => $vlanWifi,
                                'macRadio'                      => $strMacRadio,
                                'propiedadCpe'                  => $strPropiedadCpe,
                                'administraCpe'                 => $strAdministraCpe,
                                'arrayElementosSmartWifi'       => $arrayElementosSmartWifi,
                                'arrayElementosApWifi'          => $arrayElementosApWifi,
                                'arrayElementosExtenderDualBand'=> $arrayElementosExtenderDualBand,
                                'strMacSmartWifi'               => $strMacSmartWifi,
                                'nombreEdificio'                => $strNombreEdificio,
                                'arrayServiciosNetHome'         => $arrayServiciosNh,
                                'arrayServiciosNetFiber'        => $arrayServiciosNf,
                                'tercerizadora'                 => $strTercerizadora,
                                'esElementoAdicional'           => $strEsElementoAdicional,
                                'nombreElementoAdicional'       => $strNombreElementoAdicional,
                                "arrayElementosCamaras"         => $arrayElementosCamaras,
                                'esDataCenter'                  => $strEsDataCenter,
                                'vlanLan'                       => $strVlanLan,
                                'vlanWan'                       => $strVlanWan,
                                'tipoRecursos'                  => $strTipoRecursos,
                                'firewallDC'                    => $strFirewallDC,
                                'virtualConnect'                => $intVConnect,
                                'arrayElementoSmall'            => $arrayElementoSmall,
                                'arrayElementoUbicacion'        => $arrayElementoUbicacion,
                                'intNumLicencia'                => $intNumLicencia,
                                'strFechaCaducidad'             => $strFechaCaducidad,
                                'cooperativa'                   => "",
                                'tipoTransporte'                => "",
                                'placa'                         => ""
                                );
        //validar producto Seg Vehiculo
        if($prefijoEmpresa == "TN" && is_object($servicio) && is_object($servicio->getProductoId()) 
            && $servicio->getProductoId()->getNombreTecnico() == "SEG_VEHICULO")
        {
            $arrayResultado['booleanSegVehiculo'] = true;
            //obtener caracteristicas del servicio
            $objSerCarcCooperativa = $this->serviceTecnico->getServicioProductoCaracteristica($servicio,
                                                                                              "COOPERATIVA",
                                                                                              $servicio->getProductoId());
            if(is_object($objSerCarcCooperativa))
            {
                $arrayResultado['cooperativa'] = $objSerCarcCooperativa->getValor();
            }
            $objSerCarcTipoTransporte = $this->serviceTecnico->getServicioProductoCaracteristica($servicio,
                                                                                                 "TIPO TRANSPORTE",
                                                                                                 $servicio->getProductoId());
            if(is_object($objSerCarcTipoTransporte))
            {
                $arrayResultado['tipoTransporte'] = $objSerCarcTipoTransporte->getValor();
            }
            $objSerCarcPlaca = $this->serviceTecnico->getServicioProductoCaracteristica($servicio,
                                                                                        "PLACA",
                                                                                        $servicio->getProductoId());
            if(is_object($objSerCarcPlaca))
            {
                $arrayResultado['placa'] = $objSerCarcPlaca->getValor();
            }
            //obtener los elementos del producto Seg Vehiculo
            $arrayResultado['arrayEquipos'] = array();
            $arrayParElementos  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('PARAMETROS_SEG_VEHICULOS',
                                                              'TECNICO',
                                                              '',
                                                              'ELEMENTOS_PRODUCTO',
                                                              $servicio->getProductoId()->getId(),
                                                              '',
                                                              '',
                                                              '',
                                                              '',
                                                              $idEmpresa,
                                                              'valor5',
                                                              '',
                                                              '',
                                                              '',
                                                              'GENERAL');
            foreach($arrayParElementos as $arrayItemParEle)
            {
                $strNombreElemento = strtolower(str_replace(" ","-",$arrayItemParEle['valor3']))."-".$servicio->getLoginAux();
                $objElementoDisp   = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                  ->findOneBy(array("nombreElemento" => $strNombreElemento,
                                                                                    "estado"         => "Activo"));
                if(is_object($objElementoDisp))
                {
                    $strMacEle = null;
                    $strIntEle = null;
                    $objInterfaceEleDisp = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                        ->findOneBy(array("elementoId" => $objElementoDisp->getId(),
                                                                          "estado"     => "connected"));
                    if(is_object($objInterfaceEleDisp))
                    {
                        $strMacEle = $objInterfaceEleDisp->getMacInterfaceElemento();
                        $strIntEle = $objInterfaceEleDisp->getNombreInterfaceElemento();
                    }
                    $objModeloElemento = $objElementoDisp->getModeloElementoId();
                    $arrayResultado['arrayEquipos'][] = array(
                        "titulo" => ucwords(strtolower($arrayItemParEle['valor3'])),
                        "nombre" => $objElementoDisp->getNombreElemento(),
                        "serie"  => $objElementoDisp->getSerieFisica(),
                        "marca"  => $objModeloElemento->getMarcaElementoId()->getNombreMarcaElemento(),
                        "modelo" => $objModeloElemento->getNombreModeloElemento(),
                        "tipo"   => $objModeloElemento->getTipoElementoId()->getNombreTipoElemento(),
                        "mac"       => $strMacEle,
                        "interface" => $strIntEle
                    );
                }
            }
        }

        if($prefijoEmpresa == "TN" && is_object($servicio) && is_object($servicio->getProductoId()) 
            && $servicio->getProductoId()->getNombreTecnico() == "SAFE ENTRY")
        {
            //Se obtienen los elementos del producto Safe entry
            $arrayResultado['arrayEquipos']     = array();
            $arrayResultado['booleanSafeEntry'] = true;
            //obtenner los elementos del servicio
            $objCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array("descripcionCaracteristica" => "ELEMENTO_CLIENTE_ID",
                                                      "estado"                    => "Activo"));
            $objProdCarac      = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                    ->findOneBy(array("productoId"       => $servicio->getProductoId()->getId(),
                                                      "caracteristicaId" => $objCaracteristica->getId(),
                                                      "estado"           => "Activo"));
            if(is_object($objProdCarac))
            {
                $arrayServCaracElementos = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                    ->findBy(array("servicioId"                => $servicio->getId(),
                                                   "productoCaracterisiticaId" => $objProdCarac->getId(),
                                                   "estado"                    => "Activo"));
                foreach($arrayServCaracElementos as $objItemSerCarEle)
                {
                    $objElementoDisp   = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                 ->find($objItemSerCarEle->getValor());
                    if (is_object($objElementoDisp))
                    {
                        $strMacEle = null;
                        $strIntEle = null;
                        $objInterfaceEleDisp = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                    ->findOneBy(array("elementoId" => $objElementoDisp->getId(),
                                                                                      "estado"     => "connected"));
                        if (is_object($objInterfaceEleDisp))
                        {
                            $strMacEle = $objInterfaceEleDisp->getMacInterfaceElemento();
                            $strIntEle = $objInterfaceEleDisp->getNombreInterfaceElemento();
                        }
                        
                        $objModeloElemento = $objElementoDisp->getModeloElementoId();
                        $arrayResultado['arrayEquipos'][] = array(
                            "titulo" => ucwords(strtolower(explode('-', $objElementoDisp->getNombreElemento())[0])),
                            "nombre" => $objElementoDisp->getNombreElemento(),
                            "serie" => $objElementoDisp->getSerieFisica(),
                            "marca" => $objModeloElemento->getMarcaElementoId()->getNombreMarcaElemento(),
                            "modelo" => $objModeloElemento->getNombreModeloElemento(),
                            "tipo" => $objModeloElemento->getTipoElementoId()->getNombreTipoElemento(),
                            "mac" => $strMacEle,
                            "interface" => $strIntEle
                        );
                    }
                }
            }
        }
        
        return $arrayResultado;
    }

    /***
     * Función que retorna la información de un Nodo a partir de su ID Elemento relación
     * @author Wilmer Vera wvera@telconet.ec
     * @since 29/10/2020
     * @version 1.0
     */
    public function getDataNodoRelacionElemento($intIdElemento)
    {

        $intElementoA = $this->getInfoRelacionRecursivo($intIdElemento);

        if($intElementoA)
        {
            $objNodo =  $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                ->find($intElementoA);
            if($objNodo != null && is_object($objNodo))
            {
                $strElementoNodo       = $objNodo->getNombreElemento();
                $objElementoUbica      = $this->emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                                    ->findOneBy( array (
                                                                        "elementoId" => $objNodo->getId()
                                                                    ));
                if($objElementoUbica != null && is_object($objElementoUbica))
                {
                    $arrayElementoUbicacionTmp  = $this->emInfraestructura->getRepository('schemaBundle:InfoUbicacion')
                                                                    ->findOneBy(array 
                                                                    ("id"=> $objElementoUbica->getUbicacionId()->getId()));  
                    if(is_object($arrayElementoUbicacionTmp) && $arrayElementoUbicacionTmp != null)
                    {
                        $arrayNodo = array(
                            "tipoElemento"   => "NODO",
                            "direccion"      => $arrayElementoUbicacionTmp->getDireccionUbicacion(),
                            "longitud"       => $arrayElementoUbicacionTmp->getLongitudUbicacion(),
                            "latitud"        => $arrayElementoUbicacionTmp->getLatitudUbicacion(),
                            'nombreElemento' => $strElementoNodo);
                    }else
                    {
                        $arrayNodo = array(
                            "tipoElemento"   => "NODO",
                            "direccion"      => "",
                            "longitud"       => 0.0,
                            "latitud"        => 0.0,
                            'nombreElemento' => $strElementoNodo);
                    }
                }else
                {
                    $arrayNodo = array(
                        "tipoElemento"      => "NODO",
                        "direccion"         => "",
                        "longitud"          => 0.0,
                        "latitud"           => 0.0,
                        'nombreElemento'    => $strElementoNodo);
                }
            }else 
            {
                    $arrayNodo = array(
                        "tipoElemento"      => "NODO",
                        "direccion"         => "",
                        "longitud"          => 0.0,
                        "latitud"           => 0.0,
                        'nombreElemento'    => $strElementoNodo);
            }
        }else
        {
        
                $arrayNodo = array(
                    "tipoElemento"      => "NODO",
                    "direccion"         => "",
                    "longitud"          => 0.0,
                    "latitud"           => 0.0,
                    'nombreElemento'    => $strElementoNodo);

        }
                
        return $arrayNodo;
    }

    public function getInfoRelacionRecursivo($intIdElemento)
    {
        $objElementoRelacion =  $this->emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                ->findOneBy(array('elementoIdB' => $intIdElemento,
                                                    'estado'    => 'Activo'));
        if($objElementoRelacion)
        {
            return $this->getInfoRelacionRecursivo($objElementoRelacion->getElementoIdA());  
        }
        else
        {
            return $intIdElemento;                    
        }
    }
    
    /**
     * 
     * Metoto que devuelve la Mac del Cpe del cliente, busca tanto en la info detalle elemento ( data antigua ) y en la interface del cpe ( nueva
     * figura )
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 04-07-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 - Se envia bool indicando si se trata de un servicio pseudope, se envia por default "false" para no afectar el funcionamiento
     *                normal de la funcion por parte de los metodos que lo invocan
     * @since 18-11-2016
     * 
     * @param integer $intIdElementoId
     * @return string macCpe
     */
    public function getMacCpe($intIdServicio , $boolEsPseudoPe = false)
    {
        $strMacCpe = "";
               
        $arrayResultado = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                  ->getMacCpePorServicioInterface($intIdServicio , $boolEsPseudoPe);
       
        if($arrayResultado)
        {
            $strMacCpe = $arrayResultado['mac'];
        }
               
        return $strMacCpe;
    }

    /**
     * Funcion  que realiza la actualizacion de data tecnica del cpe 
     * 
     * @author Jenniffer Mujica <jmujica@telconet.ec>
     * @version 1.0
     * @since 10-10-2022
     * 
     * @param Array $arrayParametros [
    *                                 intIdEmpresa              : Id de la empresa
    *                                 intIdServicio             : Id del servicio
    *                                 intIdElemento             : Id del elemento
    *                                 intIdInterface            : Id de la interface
    *                                 strModeloCpe              : Modelo del Cpe
    *                                 strIpCpe                  : Ip del Cpe
    *                                 strNombreCpe              : Nombre del Cpe
    *                                 intIdResponsable          : Id del responsable
    *                                 strMacCpe                 : Mac del Cpe
    *                                 strMacNueva               : Mac Nueva
    *                                 strEstadoTelcos           : Estado em Telcos
    *                                 strNafAnterior            : Estado Naf actual
    *                                 strNafNueva               : Estado Naf nuevo
    *                                 strSerieCpe               : Serie Cpe
    *                                 strTipoElementoCpe        : Tipo Elemento Cpe
    *                                 strUsrCreacion            : Login del usuario quien realiza la transacción
    *                                 strIpCreacion             : Ip del usuario quien realiza la transacción
    *                                 strEmpleadoSesion         : Empleado en sesion
    *                               ]
    *                                 
    * @return Array $arrayRespuesta
    */
    public function cambioDataTecnica($arrayParametros)
    {
        $intIdServicio             = $arrayParametros[0]['idServicio'];
        $intIdEmpresa              = $arrayParametros[0]['idEmpresa'];
        $intIdElemento             = $arrayParametros[0]['idElemento'];
        $intIdInterface            = $arrayParametros[0]['idInterface'];
        $strModeloCpe              = $arrayParametros[0]['modeloCpe'];
        $strIpCpe                  = $arrayParametros[0]['ipCpe'];
        $intIdResponsable          = $arrayParametros[0]['idResponsable'];
        $strNombreCpe              = $arrayParametros[0]['nombreCpe'];
        $strMacCpe                 = $arrayParametros[0]['macCpe'];
        $strMacNueva               = $arrayParametros[0]['macCpeNUeva'];
        $strEstadoTelcos           = $arrayParametros[0]['estadoTelcos'];
        $strNafAnterior            = $arrayParametros[0]['estadoNaf'];
        $strNafNueva               = $arrayParametros[0]['estadoNafNUevo'];
        $strSerieCpe               = trim(strtoupper($arrayParametros[0]['serieCpe']));
        $strSerieCpeNueva          = trim(strtoupper($arrayParametros[0]['serieCpeNueva']));
        $strTipoElementoCpe        = $arrayParametros[0]['tipoElementoCpe'];
        $strUsrCreacion            = $arrayParametros[0]['usrCreacion'];
        $strIpCreacion             = $arrayParametros[0]['ipCreacion'];
        $strEmpleadoSesion         = $arrayParametros[0]['empleadoSesion'];
        
        $objServicio       = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                 ->find($intIdServicio);

        $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                 ->findOneBy(array( "servicioId" => $objServicio->getId()));
                                                 
        $intIdProducto        = $objServicio->getProductoId();

        $objCpe          = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento');

        /* -------------------DECLARACION DE TRANSACCIONES------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        $this->emNaf->getConnection()->beginTransaction();
        /*-----------------------------------------------------------------------*/
        
        try 
        {
            //Obtenemos la información del elemento actual.
            $objInfoElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                    ->find($intIdElemento);

            if (!is_object($objInfoElemento))
            {
                throw new \Exception('Error : No se logro obtener los datos del elemento actual.');
            }

            if (empty($intIdServicio))
            {
                throw new \Exception('Error : El id del servicio se encuentra vacio.');
            }

            //Obtenemos los datos del servicio.
            $objInfoServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                    ->find($intIdServicio);

            if (!is_object($objInfoServicio))
            {
                throw new \Exception('Error : No existe el servicio del cliente con id '.$intIdServicio);
            } 
            else 
            {
                $intIdCustodio  = $objInfoServicio->getPuntoId()->getPersonaEmpresaRolId()->getId();
                $intIdServicio  = $objInfoServicio->getId();
            }

            //Obtenemos la tarea desde el servicio y solicitud.
            $arrayTarea     = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                            ->obtenerTareaSolicitudServicio(array('serviceUtil' => $serviceUtil,
                                        'strUsuario'              => $strUsrCreacion,
                                        'strIpUsuario'            => $strIpCreacion,
                                        'intIdServicio'           => $intIdServicio,
                                        'strEstadoSolicitud'      => 'Asignada',
                                        'strDescripcionSolicitud' => 'Actualizacion'));
            
            $intNumeroTarea    = $arrayTarea["result"][0]['idComunicacion'];
            $intIdComunicacion = empty($intNumeroTarea) || $intNumeroTarea === null ? 0 : $intNumeroTarea;

            //Se obtiene id Solicitud de cambio de modem inmediato
            $arraySolicitudCambio = $this->emSoporte->getRepository('schemaBundle:InfoDetalleSolicitud')
                                ->getSolicitudCambio($intIdServicio);

            if ($arraySolicitudCambio['status'])
            {  
                $intIdSolicitud   = $arraySolicitudCambio['result'][0]['idSolicitudCambio'];
            }


            //Obtenemos los datos del punto
            $objInfoPunto = $objInfoServicio->getPuntoId();

            if (isset($objInfoPunto))
            {  
                $strLogin   = $objInfoPunto->getLogin();
                $intIdPunto = $objInfoPunto->getId();
            }

            //**************************CAMBIO DE MAC************************/
            if($strMacNueva != '')
            {
                //se valida si no siendo utilizada
                $objInterface   =  $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                        ->findOneByMacInterfaceElemento($strMacNueva);

                if(isset($objInterface))
                {
                    $objInfoDetInterf =  $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleInterface')
                                                                    ->findOneByInterfaceElementoId($objInterface);
    
                    $strLoginAux = "";
    
                    if($objInfoDetInterf)
                    {
                        $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($objInfoDetInterf->getDetalleValor());
                        
                        if($objServicio)
                        {
                            $strLoginAux = ": ".$objServicio->getLoginAux();
                        }
                    }
    
                    $arrayRespuesta = array(
                        'status' => "ERROR" , 
                        'message'=> 'La MAC ingresada ya se encuentra configurada en otro Servicio <b>'.$strLoginAux.'</b><br>'
                                    . 'Por favor, ingresa una MAC diferente ');
                    
                    return $arrayRespuesta;
                } 
                
                if($strMacCpe && $strMacCpe!='')
                {
                    //Se obtiene la MAC vinculado al servicio
                    

                    $arrayResultado = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                         ->getMacCpePorServicioInterface($intIdServicio);

                    $objInterface = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                        ->find($arrayResultado['idInterface']);
                    
                    if($objInterface)
                    {
                        //Se realiza el cambio de la MAC ligada a la interface y al servicio
                        $objInterface->setMacInterfaceElemento($strMacNueva);
                        $this->emInfraestructura->persist($objInterface);
                        $this->emInfraestructura->flush();

                        $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);

                        //Se realiza actualizacion de mac naf 
                        $arrayRespuesta = $this->emNaf->getRepository('schemaBundle:InfoDetalleMaterial')
                        ->updateMacSerieFisica(array('serviceUtil' => $serviceUtil,
                                                   'strNumeroSerie' => $strSerieCpe,
                                                   'strMacNueva' => $strMacNueva,
                                                   'strUsrModif' => $strUsrCreacion));

                    }
                    

                    if($arrayRespuesta['status'])
                    {
                        $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);

                        if($objServicio)
                        {  
                            $strMensaje = "<b>Se actualizó Mac Correctamente del elemento.</b><br>"
                            . "<b>Serie:</b>  ".$strSerieCpe.  "<br>"
                            . "<b style='color:red'>Mac Anterior:</b> ".$strMacCpe."<br>"
                            . "<b style='color:blue'>Mac Nueva:</b> ".$strMacNueva ."<br>"
                            . "<b>previo a la ejecución de </b><br>"
                            . "<b>Solicitud de cambio de equipo número:</b> ".$intIdSolicitud."<br>";

                            $objServicioHistorial = new InfoServicioHistorial();
                            $objServicioHistorial->setServicioId($objServicio);
                            $objServicioHistorial->setObservacion($strMensaje);
                            $objServicioHistorial->setEstado($objServicio->getEstado());
                            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                            $objServicioHistorial->setIpCreacion($strIpCreacion);                    
                            $this->emComercial->persist($objServicioHistorial);               
                            $this->emComercial->flush();                            

                            $arrayRespuesta = array(
                                                    'status' => "OK" , 
                                                    'message'=> $strMensaje); 
                        }
                        else
                        {
                            $arrayRespuesta = array(
                                                    'status' => "ERROR" , 
                                                    'message'=> 'No existe el Servicio requerido');
                        }  
                    }         
                    else
                    {
                        $arrayRespuesta = array(
                                            'status' => "ERROR" , 
                                            'message'=> $arrayRespuesta['message']);    
                    }      
                }
                else
                {
                    $arrayRespuesta = array(
                                            'status' => "ERROR" , 
                                            'message'=> 'No se encuentra registro de MAC anterior, por favor verificar');
                    
                    return $arrayRespuesta;
                }
            }
     
            //**************************CAMBIO DE ESTADO NAF********************/
            if($strNafNueva != '')
            {
                if($strNafNueva == $strNafAnterior )
                {
                    $arrayRespuesta = array(
                        'status' => "ERROR" , 
                        'message'=> 'Registro no actualizaco');

                    return $arrayRespuesta;
                }

                if(empty($strNafAnterior) || $strNafAnterior == '' )
                {
                    $arrayRespuesta = array(
                        'status' => "ERROR" , 
                        'message'=> 'Registro no actualizaco');

                    return $arrayRespuesta;
                }

                //se obtiene ultimo elemento registrado en el naf
                $objElementoNaf = $this->emNaf->getRepository('schemaBundle:InArticulosInstalacion')
                            ->findOneBy(array("numeroSerie" => $strSerieCpe), array('fecha' => 'DESC','idInstalacion' => 'DESC'));
                
                //Se realiza actualizacion de estado
                $arrayRespuesta = $this->emNaf->getRepository('schemaBundle:InfoDetalleMaterial')
                                ->updateEstadoNafSerieFisica(array('serviceUtil' => $serviceUtil,
                                                                   'strNumeroSerie' => $strSerieCpe,
                                                                   'strIdInstalacion' => $objElementoNaf->getIdInstalacion(),
                                                                   'strEstadoNaf' => $strNafNueva,
                                                                   'strUsrModif' => $strUsrCreacion));

                if(($arrayRespuesta['status']))
                {

                    if($objServicio)
                    {
                        $strEstadoNafNuevo = $objCpe->getEstadosNaf($strNafNueva);

                        $strMensaje = "<b>Se actualizó el Estado Naf Correctamente del elemento.</b><br>"
                        . "<b>Serie:</b>  ".$strSerieCpe.  "<br>"
                        . "<b style='color:red'>Estado Naf Actual:</b> ".$strNafAnterior."<br>"
                        . "<b style='color:blue'>Estado Naf Nueva:</b> ".$strEstadoNafNuevo ."<br>"
                        . "<b>previo a la ejecución de </b><br>"
                        . "<b>Solicitud de cambio de equipo número:</b> ".$intIdSolicitud."<br>";

                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($objServicio);
                        $objServicioHistorial->setObservacion($strMensaje);
                        $objServicioHistorial->setEstado($objServicio->getEstado());
                        $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setIpCreacion($strIpCreacion);                    
                        $this->emComercial->persist($objServicioHistorial);               
                        $this->emComercial->flush();                            
    
                        $arrayRespuesta = array(
                                            'status' => "OK" , 
                                                        'message'=> $strMensaje);
                    }
                    else
                    {
                        $arrayRespuesta = array(
                            'status' => "ERROR" , 
                            'message'=> 'Registro no actualizaco');

                        return $arrayRespuesta;
                    }
                    
                    if(is_object($objInfoPunto))
                    {
                        //Se ingresa el tracking del elemento
                        $arrayParametrosAuditoria = array();
                        $arrayParametrosAuditoria["strLogin"]        =  $objInfoPunto->getLogin();
                        $arrayParametrosAuditoria["strUsrCreacion"]  =  $strUsrCreacion;
                        $arrayParametrosAuditoria["strNumeroSerie"]  =  $strSerieCpe;
                        $arrayParametrosAuditoria["strEstadoTelcos"] =  $strEstadoTelcos;
                        $arrayParametrosAuditoria["strEstadoNaf"]    =  $strEstadoNafNuevo;
                        $arrayParametrosAuditoria["strEstadoActivo"] = 'Activo';
                        $arrayParametrosAuditoria["strUbicacion"]    = 'Cliente';
                        $arrayParametrosAuditoria["strCodEmpresa"]   =  $intIdEmpresa;
                        $arrayParametrosAuditoria["strTransaccion"]  = 'Actualizacion Estado Naf';
                        $arrayParametrosAuditoria["intOficinaId"]    =  0;
                        $arrayParametrosAuditoria["strObservacion"]    = 'Modificado por: '. $strUsrCreacion 
                        . '. Solicitud de cambio de equipo número: '.$intIdSolicitud;
                        $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);
                    }
                    
                }
                else
                {
                    $arrayRespuesta = array(
                        'status' => "ERROR" , 
                        'message'=> 'No se pudo actualizar el registro, favor verificar');

                    return $arrayRespuesta;
                }
            }
            
            //**************************CAMBIO DE CUSTODIO*********************/
            if($intIdResponsable !='')
            {
                //VALIDA TIPO SERIE 
                $arrayModeloSerie = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                            ->getTipoModeloSerie(array( 'strNumeroSerie' => $strSerieCpeNueva,
                                                                             'strEstadoElemento' => 'Activo'));

                if($arrayModeloSerie['total'] != 0)
                {
                    $objModeloSerieEl = $arrayModeloSerie["encontrados"];
                    if ($objModeloSerieEl[0]['nombreTipoElem'] == 'CPE' || $objModeloSerieEl[0]['nombreTipoElem'] == 'ROUTER')
                    {
                        //VALIDA ESTADO EN TELCOS
                        $arrayElementoTelcos = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                ->getTipoElemetoTelcos(array( 'strNumeroSerie' => $strSerieCpeNueva,
                                                                            'strEstadoElemento' => 'Activo'));

                        if($arrayElementoTelcos['total'] != 0)
                        {
                            $objElemTelcos = $arrayElementoTelcos["encontrados"];
                            if($objElemTelcos[0]['estadoTelcos'] == 'Activo')
                            {
                                $arrayRespuesta = array(
                                    'status' => "ERROR" , 
                                    'message'=> 'La serie ingresada ya se encuentra configurada en otro Servicio <br><b>'
                                                . 'Por favor, ingresa una serie diferente ');
                                
                                return $arrayRespuesta;
                            }
                        }
                    }
                    else
                    {
                        $arrayRespuesta = array(
                            'status' => "ERROR" , 
                            'message'=> 'La serie ingresada no puede ser modificada, debido al tipo de elemento<br><b>'
                                        . 'Por favor, ingresa una serie diferente ');
                        
                        return $arrayRespuesta;
                    }
                }
                else
                {
                    $arrayRespuesta = array(
                        'status' => "ERROR" , 
                        'message'=> 'La serie ingresada no puede ser modificada<br><b>'
                                    . 'Por favor, ingresa una serie diferente ');
                    return $arrayRespuesta ;
                }

                //Se obtiene informacion del nuevo responsable
                if (empty($intIdResponsable))
                {
                    throw new \Exception('Error : El id del responsable se encuentra vacio.');
                }
    
                $objInfoPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdResponsable);
                if (!is_object($objInfoPersonaEmpresaRol))
                {
                    throw new \Exception('Error : No se logro obtener los datos del empleado.');
                }
    
                $intIdEmpleado           = $objInfoPersonaEmpresaRol->getPersonaId()->getId();
                $intIdEmpleadoEmpresaRol = $objInfoPersonaEmpresaRol->getId();
    
                //Obtenemos los datos del empleado.
                $arrayInfoPersona = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                        ->getInfoDatosPersona(array ('strRol'                     => 'Empleado',
                                                     'strCodEmpresa'              =>  '10',
                                                     'intIdPersona'               =>  $intIdEmpleado,
                                                     'strEstadoPersona'           =>  array('Activo','Pendiente','Modificado'),
                                                     'strEstadoPersonaEmpresaRol' => 'Activo'));
    
                $arrayDatosPersona = $arrayInfoPersona['result'][0];

                //Verificamos quien recibe y entrega el activo.
                //recibe
                $intIdPersonaEmpresaRolRecibe  = $arrayDatosPersona['idPersonaEmpresaRol'];
                $strLoginRecibe                = $arrayDatosPersona['loginEmpleado'];
                $strNombreEmpleado  = !empty($arrayDatosPersona['razonSocial'])
                                        ? $arrayDatosPersona['razonSocial'] :$arrayDatosPersona['nombres'] . ' ' . $arrayDatosPersona['apellidos'];
                
                
                //CONSULTAR DE LA SERIE NUEVA QUIEN TIENE EL EQUIPO                                                           
 
                //Se obtiene info de la serie en articulo instalacion
                $arrayCustodioInArt = $this->emNaf->getRepository('schemaBundle:InfoDetalleMaterial')
                                ->getResponsableCpe(array('serviceUtil' => $serviceUtil,
                                                            'strSerie' => $strSerieCpeNueva, 
                                                            'strTipoParametro' => 'buscarInArtInst'));
                
                //Se consulta info de serie en control_custodio
                $arrayCustodioArCont = $this->emNaf->getRepository('schemaBundle:InfoDetalleMaterial')
                                ->getResponsableCpe(array('serviceUtil' => $serviceUtil,
                                                            'strSerie' => $strSerieCpeNueva));
                
                // verificamos si serie existe en la control custodio
                $objNaf = $this->emNaf->getRepository('schemaBundle:InfoElemento');
                $objNafExiste = $objNaf->getElementoSerieNaf($strSerieCpeNueva);

                // 1 - Si existe en Naf
                if($objNafExiste[0]['custodio_id'])
                {
                  // lleno datos para llamado a procedimiento
                  $arrayParametrosCambio['numeroSerie']         = $strSerieCpeNueva; 
                  $arrayParametrosCambio['intidPersonaEntrega'] = (int)$objNafExiste[0]['custodio_id'];
                  $arrayParametrosCambio['cantidadEnt']         = (int)$objNafExiste[0]['cantidad'];
                  $arrayParametrosCambio['intidPersonaRecibe']  = $intIdPersonaEmpresaRolRecibe;
                  $arrayParametrosCambio['cantidadRec']         = (int)$objNafExiste[0]['cantidad'];
                  $arrayParametrosCambio['loginEmpleado']       = $strUsrCreacion;
                  $arrayParametrosCambio['idControl']           = (int)$objNafExiste[0]['id_control'];
                  $strTipoCustodio = $objNafExiste[0]['tipo_custodio'];

                  //Obtenemos los datos del empleado.

                  $objInfoPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                    ->find((int)$objNafExiste[0]['custodio_id']);
                  if (!is_object($objInfoPersonaEmpresaRol))
                  {
                      throw new \Exception('Error : No se logro obtener los datos del empleado Custodio Anterior.');
                  }
      
                  $intIdEmpleadoAnt           = $objInfoPersonaEmpresaRol->getPersonaId()->getId();
      
                  //Obtenemos los datos del empleado.
                  $arrayInfoPersonaAnt = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                          ->getInfoDatosPersona(array ('strRol'                     => 'Empleado',
                                                       'strCodEmpresa'              =>  '10',
                                                       'intIdPersona'               =>  $intIdEmpleadoAnt,
                                                       'strEstadoPersona'           =>  array('Activo','Pendiente','Modificado'),
                                                       'strEstadoPersonaEmpresaRol' => 'Activo'));
      
                  $arrayDatosPersona = $arrayInfoPersonaAnt['result'][0];

                  $strNombreEmpleadoEntrega  = !empty($arrayDatosPersona['razonSocial'])
                                          ? $arrayDatosPersona['razonSocial'] :$arrayDatosPersona['nombres'] . ' ' . $arrayDatosPersona['apellidos'];

                  if(empty($strNombreEmpleadoEntrega) || $strNombreEmpleadoEntrega == null)
                  {
                    $strNombreEmpleadoEntrega = 'Sin custodio Anterior';
                  }
                  
                }
                else
                {
                    // lleno datos para llamado a procedimiento
                    $arrayParametrosCambio['numeroSerie']         = $strSerieCpeNueva;
                    $arrayParametrosCambio['intidPersonaEntrega'] = null;
                    $arrayParametrosCambio['cantidadEnt']         = 0;
                    $arrayParametrosCambio['intidPersonaRecibe']  = $intIdPersonaEmpresaRolRecibe;
                    $arrayParametrosCambio['cantidadRec']         = 1;
                    $arrayParametrosCambio['loginEmpleado']       = $strUsrCreacion;
                    $arrayParametrosCambio['idControl']           = 0;

                    $strNombreEmpleadoEntrega = 'Sin custodio Anterior';
                }

                // 1 - llamado a procedimiento
                $arrayParametrosCambio['strUser']             = $this->container->getParameter('user_naf');
                $arrayParametrosCambio['strPass']             = $this->container->getParameter('passwd_naf');
                $arrayParametrosCambio['objDb']               = $this->container->getParameter('database_dsn_naf');
                

                if($strTipoCustodio != 'Nodo')
                {
                   //se registra el cambio
                   $arrayRespuesta = $this->emNaf->getRepository('schemaBundle:InfoDetalleMaterial')
                                      ->cambioCustodio($arrayParametrosCambio);

                    if($arrayRespuesta['status'] != "OK")
                    {
                        return $arrayRespuesta;
                    }

                }
        
                //----------------------------------------
            
                if($objServicio)
                {
                    $strMensajeResp = "<b>Se actualiza información del elemento:</b><br>"
                    . "<b>Serie:</b>  ".$strSerieCpeNueva.  "<br>"
                    . "<b style='color:blue'>Nuevo responsable o custodio asignado: :</b><br>"
                    . "<b>Custodio Actual:</b> ".$strNombreEmpleado."<br>"
                    . "<b style='color:blue'>Responsable o Custodio anterior:</b><br>"
                    . "<b>Custodio Anterior:</b> ".$strNombreEmpleadoEntrega ."<br>"
                    . "<b style='color:blue'>previo a la ejecución de </b><br>"
                    . "<b>Solicitud de cambio de equipo número:</b> ".$intIdSolicitud."<br>";

                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicio);
                    $objServicioHistorial->setObservacion($strMensajeResp);
                    $objServicioHistorial->setEstado($objServicio->getEstado());
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setIpCreacion($strIpCreacion);                    
                    $this->emComercial->persist($objServicioHistorial);               
                    $this->emComercial->flush();                            

                    $arrayRespuesta = array(
                                            'status' => "OK" , 
                                                        'message'=> $strMensajeResp);

                    
                    
                    //Se ingresa el tracking del elemento
                    $arrayParametrosAuditoria = array();
                    $arrayParametrosAuditoria["strLogin"]        =  'N/A';
                    $arrayParametrosAuditoria["intIdPersona"]    =  $intIdEmpleado;
                    $arrayParametrosAuditoria["strUsrCreacion"]  =  $strUsrCreacion;
                    $arrayParametrosAuditoria["strNumeroSerie"]  =  $strSerieCpeNueva;
                    $arrayParametrosAuditoria["strEstadoTelcos"] =  'N/A';
                    $arrayParametrosAuditoria["strEstadoNaf"]    =  'PendienteInstalar';
                    $arrayParametrosAuditoria["strEstadoActivo"] =  'EnTransito';
                    $arrayParametrosAuditoria["strUbicacion"]    =  'EnTransito';
                    $arrayParametrosAuditoria["strCodEmpresa"]   =  $intIdEmpresa;
                    $arrayParametrosAuditoria["strTransaccion"]  = 'Cambio de Elemento';
                    $arrayParametrosAuditoria["intOficinaId"]    =  0;
                    $arrayParametrosAuditoria["strObservacion"]    = 'Modificado por: '. $strUsrCreacion 
                    . '. Solicitud de cambio de equipo número: '.$intIdSolicitud;
                    $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);
                    
                }
                else
                {
                    $arrayRespuesta = array(
                        'status' => "ERROR" , 
                        'message'=> 'Registro no actualizado');

                    return $arrayRespuesta;
                }
            }
            //****************DECLARACION DE COMMITS****************************/
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->commit();
            }

            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->commit();
            }

            if ($this->emNaf->getConnection()->isTransactionActive())
            {
                $this->emNaf->getConnection()->commit();
            }

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
            
            if ($this->emNaf->getConnection()->isTransactionActive())
            {
                $this->emNaf->getConnection()->rollback();
            }
            
            if($strMensaje == "")
            {
                $strMensaje = "Ocurrio un ERROR. Por favor Notifiquelo a Sistemas.";
                $this->serviceUtil->insertError('Telcos+', 'CambioElemento', $e->getMessage(),$usrCreacion,$ipCreacion);
            }
            $arrayDataConfirmacionTn['datos']['respuesta_confirmacion'] = "ERROR";
            $arrayRespuesta = array('status'=>"ERROR", 'message'=>$strMensaje);
        }

        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        $this->emNaf->getConnection()->close();
        
        return $arrayRespuesta;
    }

}