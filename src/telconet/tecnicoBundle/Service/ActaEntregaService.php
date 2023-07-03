<?php

namespace telconet\tecnicoBundle\Service;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use telconet\schemaBundle\Entity\InfoEncuesta;
use telconet\schemaBundle\Entity\InfoEncuestaPregunta;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;

/**
 * Clase que sirve para generar, grabar, obtener el acta de entrega
 * para servicios y soporte
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 30-07-2015
 */
class ActaEntregaService
{
    private $emComercial;
    private $emInfraestructura;
    private $emSoporte;
    private $emComunicacion;
    private $emSeguridad;
    private $emGeneral;
    private $emNaf;
    private $servicioGeneral;
    private $procesarImagenesService;
    private $envioPlantilla;
    private $serviceUtil;
    private $serviceSolicitudes;
    private $dataTecnica;
    private $container;
    private $host;
    private $pathTelcos;
    private $pathParameters;
    private $soporteService;

    
    // Constantes
    const CLASE_REQUERIMIENTOS_CLIENTES     = 'Requerimientos de Clientes';
    const SOLICITUD_REQUERIMIENTOS_CLIENTES = 'SOLICITUD REQUERIMIENTOS DE CLIENTES';
    const SOLICITUD_VISITA_TECNICA          = 'SOLICITUD VISITA TECNICA';
    const CARACTERISTICA_SOLICITUD          = 'SOLICITUD_TAREA_CLIENTE';
    const ESTADO_PENDIENTE                  = 'Pendiente';
    const ESTADO_ACTIVO                     = 'Activo';
    /** 
    * Documentación para el método '__construct'.
    * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
    * @author  telcos
    * @version 1.0
    *
    * @author Walther Joao Gaibor <wgaibor@telconet.ec>
    * @version 1.1 13-06-2017 Se agregan la variable 'dataTecnica' asignandole el service "tecnico.DataTecnica",
    *                         se agregan la variable 'emGeneral' asignandole entity manager "telconet_general" 
    *
    * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
    * @author Wilmer Vera <wvera@telconet.ec>
    * @version 1.2 03-04-2019 Se elimina el constructor y se crea las importaciones en las dependencias. 
    * 
    *
    * @author Ronny Morán Chancay <rmoranc@telconet.ec>
    * @version 1.2 09-07-2019 Se agrega la variable 'soporteService' asignandole el service "soporte.SoporteService"
    *
    *       
    */

    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container) 
    {
        $this->container                = $container;
        $this->emSoporte                = $this->container->get('doctrine')->getManager('telconet_soporte');
        $this->emInfraestructura        = $this->container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emSeguridad              = $this->container->get('doctrine')->getManager('telconet_seguridad');
        $this->emComercial              = $this->container->get('doctrine')->getManager('telconet');
        $this->emComunicacion           = $this->container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emNaf                    = $this->container->get('doctrine')->getManager('telconet_naf');
        $this->emGeneral                = $this->container->get('doctrine')->getManager('telconet_general');
        $this->host                     = $this->container->getParameter('host');
        $this->pathTelcos               = $this->container->getParameter('path_telcos');
        $this->pathParameters           = $this->container->getParameter('path_parameters');
        $this->envioPlantilla           = $this->container->get('soporte.EnvioPlantilla');
        $this->serviceUtil              = $this->container->get('schema.Util');
        $this->serviceSolicitudes       = $this->container->get('comercial.Solicitudes');
        $this->dataTecnica              = $this->container->get('tecnico.DataTecnica');
        $this->servicioGeneral          = $this->container->get('tecnico.InfoServicioTecnico');
        $this->procesarImagenesService  = $this->container->get('tecnico.ProcesarImagenes');
        $this->soporteService           = $this->container->get('soporte.SoporteService');

    }

    /**
     * Funcion que sirve para obtener los datos necesarios para cargar el acta de entrega del servicio
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 17-06-2015
     * @param array $arrayParametros
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.1 20-10-2016 - Se requiere visualizar el elemento WIFI del cliente el cual se debe a que el
     *                           cliente tuvo una migración de tecnología de Tellion a Huawei y se quedo con
     *                           el wifi de la instalación anterior a la migración.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.2 13-06-2017 - Obtengo los elementos que tiene instalado un servicio TN.
     * 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.2 13-06-2017 - Validacion de Elemento Cliente Id
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.4 19-09-2018 - Se cambia la direccion del cliente por la del punto
     * @since 1.2
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.5 18-12-2018 - Se adiciona los equipos ExtenderDualBand al acta.
     * @since 1.4
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.6 11-01-2019 - Se agrega opción para generar actas para Netvoice
     * @since 1.5
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.7 17-07-2019 - Se obtiene información de equipos ingresados para ser guardadas en el acta
     *                           en instalaciones TN.
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.8 30-09-2020 - Se obtiene información de equipos ingresados en tareas de instalacion TN con UM Fttx
     * @since 1.8
     *  
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.8 26-11-2020 - Se agrega validación al obtener información de elementos.
     * @since 1.9
     * 
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.9 26-09-2021 - Se elimina validación de tipo medio, para que pueda obtener
     * datos de equipos registrados sin importar su tipoMedio.
     * Se agrega nomenclatura "P" para extraer datos de actas para aplicacion PILOTO.
     * @since 1.10
     * 
     * @author Ronny Morán Chancay <rmoranc@telconet.ec>
     * @version 2.0 25-07-2021 - Se agrega validación al obtener información de elementos en producto netlifecam Md.
     *
     * Se reemplaza logica para extraer dato del producto, haciendo uso del ORM de SF.
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.11 28-01-2022 
     *   
     * Se agrega logica para extraer productos que no tienen data tecnica y mostrar preguntas en TM-Operaciones.
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 2.2 11-07-2022 
     * 
     * Se setea 'NA' a los elementos cuando el producto no tenga data tecnica
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 2.3 15-07-2022 
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.9 20/08/2022 - Se corrige validacion para que los elementos que no tengan data tecnica.
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.9 21/08/2022 - Se agrega validacion para no repetir elementos en NA.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.0 19-10-2022 - Se agrega validación para obtener las respuestas por producto.
     *
     * @author Jefferson Leon <jlleona@telconet.ec>
     * @version 2.4 19-10-2022 - Se agrega validación cuando no existe la última milla
     * 
     */
    public function getActaEntrega($arrayParametros)
    {
        //inicializar variables de los parametros
        $idEmpresa        = $arrayParametros['idEmpresa'];
        $idServicio       = $arrayParametros['idServicio'];
        $prefijoEmpresa   = $arrayParametros['prefijoEmpresa'];
        $start            = $arrayParametros['start'];
        $limit            = $arrayParametros['limit'];
        $arrayEquiposIn   = $arrayParametros['equiposIngresados'];
        $booleanEsProducto= true;
        $codigoPlantilla  = "";
        
        //inicializar variables
        $mac                    = "";
        $elementoCpe            = "";
        $macOnt                 = "";
        $elementoOnt            = "";
        $macWifi                = "";
        $elementoWifi           = "";
        $arrayInfoTecnica       = array();
        $arrayElementosExtender = array();
        $arrayElementosRegis    = array();
        $intIdProductoWifi      = "";
        $intIdProductNetLifeCam = "";
        $boolProdSinDatTecnica  = false;

        //inicializar objetos necesarios
        $producto        = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                              ->findOneBy(array("nombreTecnico"   => "INTERNET", 
                                                                "estado"          => "Activo",
                                                                "empresaCod"      => $idEmpresa
                                                                )
                                                          );

        $servicio        = $this->emComercial->find('schemaBundle:InfoServicio', $idServicio);
        $servicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneBy(array("servicioId" => $idServicio));

        if($servicioTecnico->getUltimaMillaId()!="" && $servicioTecnico->getElementoId()!="" && $servicioTecnico->getInterfaceElementoId()!=""
           && $servicioTecnico->getElementoClienteId()!="" && $servicioTecnico->getInterfaceElementoClienteId()!="")
        {
            try 
            {
                $objUltimaMilla = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                    ->find($servicioTecnico->getUltimaMillaId()); 
            } 
            catch (\Throwable $th) 
            {
                throw new \Exception("Error al obtener ultima milla del servicio");
            }
            
        }   
        else 
        {
            $boolProdSinDatTecnica = true;
            $objUltimaMilla = (object) null;
        }

        if (!is_object($servicio))
        {
           throw new \Exception("No se encontró información del servicio");
        }

        if (!is_object($servicioTecnico))
        {
           throw new \Exception("No se encontró información del servicio tecnico");
        }

        if(!$boolProdSinDatTecnica && !is_object($objUltimaMilla))
        {
            throw new \Exception("No se encontró información de la ultima milla");
        }

        if( (!is_object($objUltimaMilla) || count((array) $objUltimaMilla)==0) && $prefijoEmpresa == 'MD')
        { 
            throw new \Exception("No se encontró información de la última milla");
        }

        $planCab         = $servicio->getPlanId();

        //obtener datos del cliente-----------------------------------------------------------------------------------------------------------
        if($servicio->getPlanId() != null)
        {
            $booleanEsProducto = false;
        }
        
        $arrayParametrosActa = array(
            'idServicio'        => $idServicio,
            'booleanEsProducto' => $booleanEsProducto
        );
        $datosCliente   = $this->emComercial->getRepository('schemaBundle:InfoPersona')->getDatosClienteDelPuntoPorIdServicio($arrayParametrosActa);
        $idPersona      = $datosCliente['ID_PERSONA'];
        
        //obtener formas contactos del punto--------------------------------------------------------------------------------------------------
        $arrFormaContactosPunto = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                    ->getFormaContactoPorPunto($servicio->getPuntoId()->getId(), $start, $limit);
        
        //obtener formas contactos del cliente------------------------------------------------------------------------------------------------
        $arrFormaContactoCliente = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                       ->getFormaContactoPorCliente($idPersona, $start, $limit);
        
        //obtener contacto del cliente--------------------------------------------------------------------------------------------------------
        $contactoCliente = $this->emComercial->getRepository('schemaBundle:InfoPersona')->getContactosPorCliente($idPersona);
        
        $arrayParametrosPersonaEmpresaRol = array(
                'intIdPersona'  => $idPersona,
                'strDescRol'    => 'Cliente',
                'intCodEmpresa' => $idEmpresa
            );
        $objPersonaEmpRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                        ->getPersonaEmpresaRolPorPersonaPorTipoRolNew($arrayParametrosPersonaEmpresaRol);
            
        if(is_object($objPersonaEmpRol))
        {
            $intPersonaEmpresaRolId = $objPersonaEmpRol->getId();
        }
        //obtener elementos del cliente---------------------------------------------------------------------------------------------------------
        if($prefijoEmpresa == 'TN')
        {
            $objElementoCliente = $this->emInfraestructura->getRepository("schemaBundle:InfoElementoInstalacion")
                                                            ->findBy(array(
                                                                'personaEmpresaRolId'   => $intPersonaEmpresaRolId,
                                                                'estado'                => 'Activo',
                                                                'ubicacion'             => 'CLIENTE',
                                                                'puntoId'               => $servicio->getPuntoId()->getId(),
                                                                'servicioId'            => $idServicio 
                                                            )); 

                if(count($objElementoCliente) > 0)
                {
                
                    $arrayEquiposAsig = array();
                    for($intIndex = 0; $intIndex<count($objElementoCliente); $intIndex++)
                    {

                        $objTipoElemento = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
                                                ->find($objElementoCliente[$intIndex]->getTipoElementoId());

                        $arrayEquiposAsig   = array(
                            'modelo'        => $objElementoCliente[$intIndex]->getModeloElemento(),
                            'marca'         => '',
                            'serie'         => $objElementoCliente[$intIndex]->getSerieElemento(),
                            'mac'           => $objElementoCliente[$intIndex]->getMacElemento(),
                            'tipo'          => $objTipoElemento->getNombreTipoElemento()
                                );
                        $arrayInfoTecnica[] = $arrayEquiposAsig;
                    }   
                }
            if(isset($arrayEquiposIn) && count($arrayEquiposIn)>0)
            {
                $arrayEquiposAsig = array();
                for($intIndex = 0; $intIndex<count($arrayEquiposIn); $intIndex++)
                {
                    $arrayEquiposAsig   = array(
                        'modelo'        => '',
                        'marca'         => '',
                        'serie'         => $arrayEquiposIn[$intIndex]['strNoArticulo'],
                        'mac'           => '',
                        'tipo'          => $arrayEquiposIn[$intIndex]['strTipoArticulo']
                         );
                    $arrayInfoTecnica[] = $arrayEquiposAsig;
                }   
            }
            else
            {
                if ($boolProdSinDatTecnica && empty($arrayInfoTecnica))
                {
                    $arrayInfoTecnica[] = array(
                        'modelo'    => "NA",
                        'marca'     => "NA", 
                        'serie'     => "NA",
                        'mac'       => "NA",
                        'tipo'      => "NA"
                    );
                }
                else 
                {
                $arrayPeticiones  = array(      'idServicio'    => $idServicio,
                'idEmpresa'     => $idEmpresa,
                'prefijoEmpresa'=> $prefijoEmpresa);
                $arrayDataTecnica = $this->dataTecnica->getDataTecnica($arrayPeticiones);
                $arrayTransceiver = array();
                $arrayRoseta      = array();
                $arrayCpe         = array();
                $arrayRadCliente  = array();
                // Obtengo el Transceiver
                if(isset($arrayDataTecnica['elementoTransceiver']) && is_object($arrayDataTecnica['elementoTransceiver']))
                {
                $arrayTransceiver = array(
                                    'modelo'    => $arrayDataTecnica['elementoTransceiver']->getModeloElementoId()
                                                                                        ->getNombreModeloElemento(),
                                    'marca'     => $arrayDataTecnica['elementoTransceiver']->getModeloElementoId()
                                                                                        ->getMarcaElementoId()
                                                                                        ->getNombreMarcaElemento(),
                                    'serie'     => $arrayDataTecnica['elementoTransceiver']->getSerieFisica(),
                                    'mac'       => "",
                                    'tipo'      => $arrayDataTecnica['elementoTransceiver']->getModeloElementoId()
                                                                                        ->getTipoElementoId()
                                                                                        ->getNombreTipoElemento()
                                );
                $arrayInfoTecnica[] = $arrayTransceiver;
                }

                // Obtengo la Roseta si el tipo medio es Fibra Optica
                if(isset($arrayDataTecnica['elementoRoseta']) && is_object($arrayDataTecnica['elementoRoseta']) &&
                $arrayDataTecnica['tipoMedio']->getCodigoTipoMedio() == 'FO')
                {
                $arrayRoseta = array(
                                'modelo'    => $arrayDataTecnica['elementoRoseta']->getModeloElementoId()->getNombreModeloElemento(),
                                'marca'     => $arrayDataTecnica['elementoRoseta']->getModeloElementoId()
                                                                                ->getMarcaElementoId()
                                                                                ->getNombreMarcaElemento(),
                                'serie'     => $arrayDataTecnica['elementoRoseta']->getSerieFisica(),
                                'mac'       => "",
                                'tipo'      => $arrayDataTecnica['elementoRoseta']->getModeloElementoId()
                                                                                ->getTipoElementoId()
                                                                                ->getNombreTipoElemento()
                            );
                $arrayInfoTecnica[] = $arrayRoseta;
                }

                // Obtengo la radio del cliente
                if(isset($arrayDataTecnica['elementoRoseta']) && is_object($arrayDataTecnica['elementoRoseta']) &&
                $arrayDataTecnica['tipoMedio']->getCodigoTipoMedio() == 'RAD')
                {
                $arrayRadCliente = array(
                                    'modelo'    => $arrayDataTecnica['elementoRoseta']->getModeloElementoId()->getNombreModeloElemento(),
                                    'marca'     => $arrayDataTecnica['elementoRoseta']->getModeloElementoId()
                                                                                    ->getMarcaElementoId()
                                                                                    ->getNombreMarcaElemento(),
                                    'serie'     => $arrayDataTecnica['elementoRoseta']->getSerieFisica(),
                                    'mac'       => $arrayDataTecnica['macRadio'],
                                    'tipo'      => $arrayDataTecnica['elementoRoseta']->getModeloElementoId()
                                                                                    ->getTipoElementoId()->getNombreTipoElemento()
                            );
                $arrayInfoTecnica[] = $arrayRadCliente;
                }

                // Obtengo el CPE
                if(isset($arrayDataTecnica['elementoCpe']) && is_object($arrayDataTecnica['elementoCpe']))
                {
                $arrayCpe = array(
                            'modelo'    => $arrayDataTecnica['elementoCpe']->getModeloElementoId()->getNombreModeloElemento(),
                            'marca'     => $arrayDataTecnica['elementoCpe']->getModeloElementoId()
                                                                        ->getMarcaElementoId()
                                                                        ->getNombreMarcaElemento(),
                            'serie'     => $arrayDataTecnica['elementoCpe']->getSerieFisica(),
                            'mac'       => $arrayDataTecnica['macCpe'],
                            'tipo'      => $arrayDataTecnica['elementoCpe']->getModeloElementoId()
                                                                        ->getTipoElementoId()->getNombreTipoElemento()
                    );
                $arrayInfoTecnica[] = $arrayCpe;
                }                
            }

        }            
        }
        else
        {
            $strElementoClienteId = $servicioTecnico->getElementoClienteId();
            if($objUltimaMilla->getNombreTipoMedio()=="Cobre" || $objUltimaMilla->getNombreTipoMedio()=="Radio")
            {
                //datos cpe-------------------------------------------------------------------------------------------------------------------
                $mac            = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC", $producto);
                if(isset($strElementoClienteId) && !empty($strElementoClienteId))
                {
                    $elementoCpe    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                          ->find($servicioTecnico->getElementoClienteId());
                }
                //----------------------------------------------------------------------------------------------------------------------------
            }
            else if($objUltimaMilla->getNombreTipoMedio()=="Fibra Optica")
            {  
                //datos ont-------------------------------------------------------------------------------------------------------------------
                $macOnt         = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC ONT", $producto);
                if(isset($strElementoClienteId) && !empty($strElementoClienteId))
                {
                    
                    $elementoOnt    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                          ->find($servicioTecnico->getElementoClienteId());                
                    //----------------------------------------------------------------------------------------------------------------------------

                    if($macOnt)
                    {
                        $strMacOnt = $macOnt->getValor();
                    }
                    else
                    {
                        $strMacOnt = "";
                    }
                    
                    $strSerieOnt   = $elementoOnt->getSerieFisica();
                    $strModeloOnt  = $elementoOnt->getModeloElementoId()->getNombreModeloElemento();
                    $strMarcaOnt   = $elementoOnt->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();
                    $strTipoOnt    = $elementoOnt->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
                    
                    $arrayOnt      = array(
                                            'modelo'    => $strModeloOnt,
                                            'marca'     => $strMarcaOnt,
                                            'serie'     => $strSerieOnt,
                                            'mac'       => $strMacOnt,
                                            'tipo'      => $strTipoOnt
                                        );

                    $arrayElementosRegis[] = $arrayOnt;
                    
                    $arrayIdProductoWifi            = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                           ->getOne( 'PARAMETROS_GENERALES_MOVIL', 
                                                                     '', 
                                                                     '', 
                                                                     '', 
                                                                     'ID_PRODUCTO_WIFI+AP', 
                                                                     '', 
                                                                     '', 
                                                                     ''
                                                                    );

                    if(is_array($arrayIdProductoWifi))
                    {
                        $intIdProductoWifi = !empty($arrayIdProductoWifi['valor2']) ? $arrayIdProductoWifi['valor2'] : "";
                    }
                    
                    $arrayIdProductoNetLifeCam            = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                           ->getOne( 'PARAMETROS_GENERALES_MOVIL', 
                                                                     '', 
                                                                     '', 
                                                                     '', 
                                                                     'ID_PRODUCTO_NETLIFECAM_MD', 
                                                                     '', 
                                                                     '', 
                                                                     ''
                                                                    );

                    if(is_array($arrayIdProductoNetLifeCam))
                    {
                        $intIdProductNetLifeCam = !empty($arrayIdProductoNetLifeCam['valor2']) ? $arrayIdProductoNetLifeCam['valor2'] : "";
                    }
                    
                    
                    $objServicioPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                             ->findOneBy(array( "puntoId"      => $servicio->getPuntoId(),
                                                                "productoId"   => null,
                                                                "estado"       => "Activo"  ));
                    if(is_object($objServicioPunto))
                    {
                      $intIdServicioInternet = $objServicioPunto->getId();
                    }
                    
                    $objPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($servicio->getPuntoId());
                    $strLogin = $objPunto->getLogin();
                    
                    $objElementoOntEliminado    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                            ->findOneBy(array("nombreElemento" => $strLogin."-ont",
                                                                              "estado"         => "Eliminado" ));  
                    
                    
                    if(is_object($servicio->getProductoId()) && $servicio->getProductoId()->getId() == $intIdProductoWifi 
                       && is_object($objServicioPunto) && is_object($objElementoOntEliminado))
                    {
                        $objServicioTecnicoInt = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                   ->findOneBy(array("servicioId" => $intIdServicioInternet));
                        
                        $objMacOntCambio        = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioPunto, "MAC ONT", $producto);
                        $strElementoClienteId   = $objServicioTecnicoInt->getElementoClienteId();
                        
                        if(isset($strElementoClienteId) && !empty($strElementoClienteId))
                        {
                                $objElementoOntCambio    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                ->find($strElementoClienteId);                

                                if($objMacOntCambio)
                                {
                                    $strMacOntCambio = $objMacOntCambio->getValor();
                                }
                                else
                                {
                                    $strMacOntCambio = "";
                                }

                                $elementoCpe         = $objElementoOntCambio;
                                $mac                 = $objMacOntCambio;
                                $strSerieOntCambio   = $objElementoOntCambio->getSerieFisica();
                                $strModeloOntCambio  = $objElementoOntCambio->getModeloElementoId()->getNombreModeloElemento();
                                $strMarcaOntCambio   = $objElementoOntCambio->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();
                                $strTipoOntCambio    = $objElementoOntCambio->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();

                                $arrayOntCambio      = array(
                                                        'modelo'    => $strModeloOntCambio,
                                                        'marca'     => $strMarcaOntCambio,
                                                        'serie'     => $strSerieOntCambio,
                                                        'mac'       => $strMacOntCambio,
                                                        'tipo'      => $strTipoOntCambio
                                                        );

                                $arrayElementosRegis[] = $arrayOntCambio;
                            
                        }
                    }
                    
                    //Netlifecam
                    $objElementoMicroSd    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                            ->findOneBy(array("nombreElemento" => $strLogin."micro-SD",
                                                                              "estado"         => "Activo" )); 
                    
                   
                    $objServicioPuntoProd   = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                    ->findOneBy(array(  "puntoId"      => $servicio->getPuntoId(),
                                                                        "productoId"   => null,
                                                                        "estado"       => "Activo"  ));
                    
                    if(is_object($servicio->getProductoId()) && $servicio->getProductoId()->getId() == $intIdProductNetLifeCam 
                        && is_object($objElementoMicroSd) && is_object($objServicioPuntoProd))
                    {
                        $objMacOntCambio        = $this->servicioGeneral
                                                       ->getServicioProductoCaracteristica($objServicioPuntoProd, 
                                                                                           "MAC WIFI", 
                                                                                           $producto);
                                       
                        // verificar mac
                        if($macOnt)
                        {
                            $strMacMicroSd = $macOnt->getValor();
                        }
                        else
                        {
                            $strMacMicroSd = "";
                        }

                        $strSerieMicroSd   = $objElementoMicroSd->getSerieFisica();
                        $strModeloMicroSd  = $objElementoMicroSd->getModeloElementoId()->getNombreModeloElemento();
                        $strMarcaMicroSd   = $objElementoMicroSd->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();
                        $strTipoMicroSd    = $objElementoMicroSd->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();

                        $arrayMicroSd      = array(
                                                'modelo'    => $strModeloMicroSd,
                                                'marca'     => $strMarcaMicroSd,
                                                'serie'     => $strSerieMicroSd,
                                                'mac'       => $strMacMicroSd,
                                                'tipo'      => $strTipoMicroSd
                                            );

                        $arrayElementosRegis[] = $arrayMicroSd;

                         
                     }    
                    
                    //datos elemento de backbone----------------------------------------------------------------------------------------------------
                    if(!is_null($servicioTecnico->getElementoId()))
                    {
                    
                        $serviceElementoOlt    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                               ->find($servicioTecnico->getElementoId());
                        $objModeloOlt             = $serviceElementoOlt->getModeloElementoId();

                        if($objModeloOlt->getNombreModeloElemento() == "EP-3116" || $objModeloOlt->getNombreModeloElemento() == "MA5608T")
                        {
                        //buscar enlace ont - wifi /--------------------------------------------------------------------------------------------------
                        $objEnlace = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                       ->findOneBy(array(
                                                         "interfaceElementoIniId" => $servicioTecnico->getInterfaceElementoClienteId(),
                                                         "estado"                 => "Activo"
                                                        )
                                                  );
                        //----------------------------------------------------------------------------------------------------------------------------

                        if($objEnlace)
                        {
                            $objInterfaceFin = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                       ->find($objEnlace->getInterfaceElementoFinId());

                            $objMacWifi         = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC WIFI", $producto);
                            $elementoWifi    = $objEnlace->getInterfaceElementoFinId()->getElementoId();

                            if($objMacWifi)
                            {
                                $strMacWifi = $objMacWifi->getValor();
                            }
                            else
                            {
                                $strMacWifi = "";
                            }

                            $strSerieWifi   = $elementoWifi->getSerieFisica();
                            $strModeloWifi  = $elementoWifi->getModeloElementoId()->getNombreModeloElemento();
                            $strMarcaWifi   = $elementoWifi->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();
                            $strTipoWifi    = $elementoWifi->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();

                            $arrayWifi      = array(
                                                    'modelo'    => $strModeloWifi,
                                                    'marca'     => $strMarcaWifi,
                                                    'serie'     => $strSerieWifi,
                                                    'mac'       => $strMacWifi,
                                                    'tipo'      => $strTipoWifi
                                                );
                            
                            
                            if(isset($objInterfaceFin) && !empty($objInterfaceFin))
                            {
                                $objElementosExtender = $objInterfaceFin->getElementoId();
                                if (strpos($objElementosExtender->getNombreElemento(), 'ExtenderDualBand') !== false)
                                {
                                    //datos wifi--------------------------------------------------------
                                    $elementoWifi = "";
                                }
                                else
                                {   
                                    $arrayElementosRegis[] = $arrayWifi;
                                }
                            }

                            //---------------------------------------------------------------------------
                        }
                    }//if($objModeloOlt->getNombreModeloElemento() == "EP-3116")

                    }
                    
                    $boolBanderaCiclo                = true;
                    $intIdInterfaceElementoCliente   = $servicioTecnico->getInterfaceElementoClienteId();

                    while($boolBanderaCiclo)
                    {
                        $entityEnlace = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                       ->findOneBy(array(
                                                         "interfaceElementoIniId" => $intIdInterfaceElementoCliente,
                                                         "estado"                 => "Activo"
                                                        )
                                                  );

                        if(isset($entityEnlace) && !empty($entityEnlace))
                        {   

                            $objInterfaceFin = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                       ->find($entityEnlace->getInterfaceElementoFinId());

                            if(isset($objInterfaceFin) && !empty($objInterfaceFin))
                            {
                                $objElementosExtender = $objInterfaceFin->getElementoId();

                                if(isset($objElementosExtender) && !empty($objElementosExtender))
                                {
                                    $intIdInterfaceElementoCliente = $objInterfaceFin->getId();

                                    if (strpos($objElementosExtender->getNombreElemento(), 'ExtenderDualBand') !== false)
                                    {
                                        $objDetEleMacExtender = $this->emInfraestructura
                                                                    ->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->findOneBy(array('elementoId'    => $objElementosExtender->getId(),
                                                                                      'detalleNombre' => 'MAC',
                                                                                      'estado'        => 'Activo'
                                                                               )
                                                                         );

                                        $strMacExtender = "";
                                        if(is_object($objDetEleMacExtender))
                                        {
                                            $strMacExtender = $objDetEleMacExtender->getDetalleValor();
                                            $objElementosExtender->setMacElemento($strMacExtender);
                                        }

                                        $strSerieExtender   = $objElementosExtender->getSerieFisica();
                                        $strModeloExtender  = $objElementosExtender->getModeloElementoId()->getNombreModeloElemento();
                                        $strMarcaExtender   = $objElementosExtender->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();
                                        $strTipoExtender    = $objElementosExtender->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();

                                        $arrayExtender      = array(
                                                                'modelo'    => $strModeloExtender,
                                                                'marca'     => $strMarcaExtender,
                                                                'serie'     => $strSerieExtender,
                                                                'mac'       => $strMacExtender,
                                                                'tipo'      => $strTipoExtender
                                                            );

                                        $arrayElementosRegis[] = $arrayExtender;

                                        array_push($arrayElementosExtender, $objElementosExtender);
                                    }

                                }
                                else
                                {
                                    $boolBanderaCiclo=false;
                                }   
                            }
                            else
                            {
                                $boolBanderaCiclo=false;
                            }
                            //---------------------------------------------------------------------------------------------------------------------------
                        }
                        else
                        {
                            $boolBanderaCiclo=false;

                        }
                    }
                }
            }//else if($ultimaMilla->getNombreTipoMedio()=="Fibra Optica")
        }
        
        //obtener opciones multiples del acta de entrega---------------------------------------------------------------------------------------
        //Obtenemos el codigo de la plantilla para poder generar el acta
        if($arrayParametros['strModulo'] == 'SOPORTE')
        {
            $strTipoActa = 'CODIGO_ACTA_ENTREGA_VISITA_POR_EMPRESA';
        }
        else
        {
            $strTipoActa = 'CODIGO_ACTA_ENTREGA_INSTALACION_POR_EMPRESA';
        }
        $arrayAdmiParametroDetActa = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                     ->getOne($strTipoActa,
                                                              'TECNICO',
                                                              '',
                                                              '',
                                                              'CODIGO_ACTA_ENTREGA',
                                                              '',
                                                              '',
                                                              '',
                                                              '',
                                                              $idEmpresa);
        if (isset($arrayAdmiParametroDetActa['valor2']) && !empty($arrayAdmiParametroDetActa['valor2']))
        {
            $strDescTarea = "";
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
                $codigoPlantilla = 'ACT-ENT-NV-INS';
            }
            else
            {
                $codigoPlantilla = $arrayAdmiParametroDetActa['valor2'];
            }        
        }
        $arrayInfoTecnica = $this->eliminarElementosRepetidos($arrayInfoTecnica, 'serie');
        $arrayOpcionesActa = $this->getPlantillaActaEntrega(array("objServicio"        => $servicio,
                                                                "strCodigoPlantilla" => $codigoPlantilla,
                                                                "intIdEmpresa"       => $idEmpresa)); 
        
        $arrayActaEntrega = array(
                                    'servicio'              => $servicio,
                                    'planCab'               => $planCab,
                                    'ultimaMilla'           => $objUltimaMilla,
                                    'datosCliente'          => $datosCliente,
                                    'formaContactoPunto'    => $arrFormaContactosPunto,
                                    'formaContactoCliente'  => $arrFormaContactoCliente,
                                    'contactoCliente'       => $contactoCliente,
                                    'macCpe'                => $mac,
                                    'elementoCpe'           => $elementoCpe,
                                    'macOnt'                => $macOnt,
                                    'elementoOnt'           => $elementoOnt,
                                    'macWifi'               => $objMacWifi,
                                    'elementoWifi'          => $elementoWifi,
                                    'dataTecnica'           => $arrayInfoTecnica,
                                    'opcionesActaEntrega'   => $arrayOpcionesActa,
                                    'arrayElementosExtender'=> $arrayElementosExtender,
                                    'arrayElementosRegis'   => $arrayElementosRegis,
                                    'elementoMicroSd'       => $objElementoMicroSd,
                                    'boolProdSinDatTecnica' => $boolProdSinDatTecnica,
                                );
        
        return $arrayActaEntrega;
    }

/*
    * Función que sirve para 
    * recorrer un array y eliminar datos repetidos
    * $array array a recorrer. 
    * $key palabra clave que no debe repetirse, 
    * en este caso los Identificadores.
    * 
    * @autor Wilmer Vera <wvera@telconet.ec>
    * @since 1,0 11-10-2021
*/
    public function eliminarElementosRepetidos($arrayElementos,$strValor)
    {
       $arrayTemporal = [];
       foreach ($arrayElementos as &$strElementoK) 
       {
           if (!isset($arrayTemporal[$strElementoK[$strValor]]))
           {
                $arrayTemporal[$strElementoK[$strValor]] =& $strElementoK;
           }
       }
       $arrayElementos = array_values($arrayTemporal);
       return $arrayElementos;

    }
    /**
     * Funcion que sirve para obtener las opciones necesarias para mostrar
     * en el acta de entrega del servicio hacia el cliente, realizadas por el operativo
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 17-06-2015
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.1 13-06-2017 - Codifico los caracteres con tildes para que se visualizen en el movil correctamente.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 19-10-2022 - Se agrega validación para obtener las respuestas por producto.
     *
     * @param string $codigoPlantilla
     */
    public function getPlantillaActaEntrega($arrayParametros)
    {
        $objServicio        = $arrayParametros['objServicio'];
        $strCodigoPlantilla = $arrayParametros['strCodigoPlantilla'];
        $objPlantilla       = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')->findOneByCodigo($strCodigoPlantilla);	  
        $arrayPlantillaPregunta = $this->emComunicacion->getRepository('schemaBundle:InfoPlantillaPregunta')
                                                   ->getPreguntasPorPlantilla($objPlantilla->getId());
        
        $strPreguntasIds = '';
        foreach( $arrayPlantillaPregunta as $objPlantillaPregunta )
        {		      
            $booleanValidarRespuestas = false;
            $objPregunta = $this->emComunicacion->getRepository('schemaBundle:AdmiPregunta')->find($objPlantillaPregunta->getPreguntaId());	 
            if(is_object($objServicio) && is_object($objServicio->getProductoId()))
            {
                $arrayParPlanPregProducto = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get('PRODUCTO_PLANTILLA_PREGUNTA',
                                                                      'TECNICO',
                                                                      '',
                                                                      '',
                                                                      $objServicio->getProductoId()->getId(),
                                                                      $objPlantilla->getId(),
                                                                      $objPregunta->getId(),
                                                                      '',
                                                                      '',
                                                                      $arrayParametros['intIdEmpresa']);
                if(isset($arrayParPlanPregProducto) && !empty($arrayParPlanPregProducto))
                {
                    $booleanValidarRespuestas = true;
                }
            }

            $arrayPreguntaRespuestas = $this->emComunicacion->getRepository('schemaBundle:AdmiPreguntaRespuesta')
                                                                            ->findByPreguntaId($objPregunta->getId());	      	      	      
            $arrayRespuestas    = null;
            foreach($arrayPreguntaRespuestas as $objPreguntaRespuesta)
            {
                $objRespuesta = $this->emComunicacion->getRepository('schemaBundle:AdmiRespuesta')->find($objPreguntaRespuesta->getRespuestaId());	
                if($booleanValidarRespuestas)
                {
                    $arrayParPlanPregProducto = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->getOne('PRODUCTO_PLANTILLA_PREGUNTA',
                                                                            'TECNICO',
                                                                            '',
                                                                            '',
                                                                            $objServicio->getProductoId()->getId(),
                                                                            $objPlantilla->getId(),
                                                                            $objPregunta->getId(),
                                                                            $objRespuesta->getId(),
                                                                            '',
                                                                            $arrayParametros['intIdEmpresa']);
                    if(isset($arrayParPlanPregProducto) && !empty($arrayParPlanPregProducto))
                    {
                        $arrayRespuestas[]  = array(
                                                    'idRespuesta'   => $objRespuesta->getId(),
                                                    'respuesta'     => html_entity_decode($objRespuesta->getRespuesta())
                                                    );
                    }
                }
                else
                {
                    $arrayRespuestas[]  = array(
                                                'idRespuesta'   => $objRespuesta->getId(),
                                                'respuesta'     => html_entity_decode($objRespuesta->getRespuesta())
                                                );
                }
            }

            $strPreguntasIds .= $objPregunta->getId().'-';
            $arrayPreguntaEncuesta[] =  array(
                                              'idPregunta'    => $objPregunta->getId(),
                                              'pregunta'      => html_entity_decode($objPregunta->getPregunta()),
                                              'tipoRespuesta' => $objPregunta->getTipoRespuesta(),
                                              'descPregunta'  => $objPregunta->getDescripcion(),
                                              'respuestas'    => $arrayRespuestas
                                              );
        } 

        $arrayResultado = array(
                            'preguntas' => $arrayPreguntaEncuesta,
                            'ids'       => $strPreguntasIds			      
                        );

        return $arrayResultado;
    }

    /**
     * Funcion que sirve para grabar el Acta EPP, generar la firma,
     * generar el pdf almacenarlo en las tablas correspondiente.
     *
     * @author Walther Joao Gaibor<wgaibor@telconet.ec>
     * @version 1.0 - 25/05/2018
     * 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 21-05-2018 Se añadio el id del documento guardado
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.2 02-06-2020 - Se modifica código para crear nueva estructura de archivos.
     * 
     * 
     * @param array $arrayParametros
     */
    public function grabarActaEpp($arrayParametros)
    {
        $intIdComunicacion      = $arrayParametros['idComunicacion'];
        $strFirmaEmpleado       = $arrayParametros['firma'];
        $serverRoot             = $arrayParametros['serverRoot'];
        $pathSource             = $arrayParametros['pathSource'];
        $hora                   = date('Y-m-d-His');
        $horaActual             = date('h:i:s A');
        $fecha                  = date('Y-m-d');
        $strCodigo              = "ACT-ARC";
        $intIdServicio          = !empty($arrayParametros['idServicio']) ? $arrayParametros['idServicio'] : '' ;
        $intIdEvidencias        = 0;
        $arrayEvidencias        = array();
        $intIdDocumento         = 0;
        $strRutaFisicaCompleta  = '';
        $arrayFirma             = array();
        $arrayImagenes          = array();
        $arrayDocumento         = array();
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComunicacion->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        try
        {
            //datos para grabar la firma
            $strServerRoot          = $this->container->getParameter('path_telcos');

            if(isset($arrayParametros['strRutaFisicaCompleta']) && !empty($arrayParametros['strRutaFisicaCompleta']))
            {
                $strRutaFisicaCompleta  = $arrayParametros['strRutaFisicaCompleta'];
                $strDirFirmas           = $serverRoot . '/' . substr($strRutaFisicaCompleta, 0, strrpos($strRutaFisicaCompleta, '/')+1) . 'firmas/';
                $strRutaFisicaArchivo   = $strServerRoot . 'telcos/web/' . 
                                            substr($strRutaFisicaCompleta, 0, strrpos($strRutaFisicaCompleta, '/')+1) . 
                                            'imagenes/';

                $arrayFirma['strPath']      = $strDirFirmas;
                $arrayDocumento['strPath']  = $strRutaFisicaCompleta;
                $arrayImagenes['strPath']   = $strRutaFisicaArchivo;
                // Si el directorio se crea exitosamente retornara valor 100.
                if("100" != $this->serviceUtil->creaDirectorio($arrayFirma)->getStrStatus() ||
                   "100" != $this->serviceUtil->creaDirectorio($arrayDocumento)->getStrStatus() ||
                   "100" != $this->serviceUtil->creaDirectorio($arrayImagenes)->getStrStatus()
                   )
                {
                    throw new \Exception("Problemas al crear los directorios, intenta nuevamente");
                }
            }
            else
            {
                $strRutaFisicaCompleta  = 'public/uploads/documentos';                
                $strDirFirmas           = $serverRoot . '/public/uploads/firmas/';
                $strRutaFisicaArchivo   = $strServerRoot.'telcos/web/public/uploads/firmas/';
            }
            
            $strExtensionArchivo       = 'png';
            $nombreArchivoCliente      = '_cuadrilla'.$intIdComunicacion;
            $nombreArchivofirma        = '_firma'.$intIdComunicacion;
            //grabar firma empleado en imagen.

             
            if($strFirmaEmpleado != "")
            {
                
             
                $this->procesarImagenesService->grabarImagenBase64($strFirmaEmpleado,
                                                                   $nombreArchivofirma,
                                                                   $strDirFirmas,
                                                                   $strExtensionArchivo);
            }
            
            $arrayfotografia           = json_decode($arrayParametros['fotografiaEvidencia'],true);
            
            //Recuperar las fotografias de evidencia
            foreach($arrayfotografia as $arrayEvidenciasFotos)
            {
                            
                $nombreArchivoCliente  = '_cuadrilla_evidencia'.$intIdEvidencias;
                $this->procesarImagenesService->grabarImagenBase64($arrayEvidenciasFotos['foto'],
                                                                   $nombreArchivoCliente,
                                                                   $strRutaFisicaArchivo,
                                                                   $strExtensionArchivo);
                $intIdEvidencias++;
                $arrayEvidencias[] = $strRutaFisicaArchivo . $nombreArchivoCliente . '.' . $strExtensionArchivo;
            }
            $objTipoDocumento       = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                                            ->findOneByExtensionTipoDocumento('PDF');
            $objDocumentoGeneral    = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                      ->findOneByDescripcionTipoDocumento('ACTA ARCOTEL');
            //obtener la plantilla de arcotel
            $objActa        = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                                           ->findOneBy(array("codigo" => 'ACT-ARCOTEL'));
            $strHtml        = is_object($objActa)?$objActa->getPlantilla():"";

            $strArchivo     = fopen($pathSource . '/Resources/views/Default/ActaEPP.html.twig', "w");

            if($strArchivo)
            {
                fwrite($strArchivo, $strHtml);
                fclose($strArchivo);
                //generar PDF
                $arrayPdf = array   (   'firmaImagenurl'        => $strDirFirmas . $nombreArchivofirma . '.' . $strExtensionArchivo,
                                        'firmaEvidenciaurl'     => $arrayEvidencias,
                                        'NombreCoordinador'     => $arrayParametros['coordinador'],
                                        'NombreDepartamento'    => $arrayParametros['departamento'],
                                        'actaPersonalCargo'     => $arrayParametros['actaPersonalCargo'],
                                        'NombreVehiculo'        => $arrayParametros['vehiculo'],
                                        'Fecha'                 => $fecha,
                                        'Hora'                  => $hora,
                                        'HoraActual'            => $horaActual,
                                        'Login'                 => $arrayParametros['usrCreacion'],
                                        'Coordenadas'           => $arrayParametros['coordenadas'],
                                        'materiales'            => $arrayParametros['cumplePorMaterial'],
                                        'direccion'             => $arrayParametros['direccionTrabajoInspeccionado'],
                                        'serverRoot'            => $serverRoot,
                                        'idDetalle'             => $arrayParametros['idDetalle'],
                                        'idComunicacion'        => $intIdComunicacion,
                                        'modulo'                => 'tecnico',
                                        'strRutaFisicaCompleta' => $strRutaFisicaCompleta,
                                        'bandNfs'               => $arrayParametros['bandNfs'],
                                        'strApp'                => $arrayParametros['strAplicacion'],
                                        'prefijoEmpresa'        => $arrayParametros['prefijoEmpresa'],
                                        'strSubModulo'          => $arrayParametros['strOrigenAccion']
                                    );

                $arrayRespuestaActa = $this->generarPdfArcotel($arrayPdf);

                //eliminar evidencia
                foreach($arrayEvidencias as $strFotoEvidencia)
                {
                    unlink($strFotoEvidencia);
                }

                unlink($strDirFirmas . $nombreArchivofirma . '.' . $strExtensionArchivo);

            }
            else
            {
                throw new \Exception("Problema al procesar el archivo, intenta nuevamente");
            }
            //InfoDocumento
            $infoDocumento = new InfoDocumento();
            $infoDocumento->setTipoDocumentoId($objTipoDocumento);
            $infoDocumento->setTipoDocumentoGeneralId($objDocumentoGeneral->getId());
            $infoDocumento->setNombreDocumento('Acta EPP : ' . $intIdComunicacion);
            $infoDocumento->setUbicacionLogicaDocumento('Acta_EPP_' . $intIdComunicacion . '-' . $hora . '.pdf');
            if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'])
            {
                $infoDocumento->setUbicacionFisicaDocumento($arrayRespuestaActa['strSrc']);
            }
            else
            {
                $infoDocumento->setUbicacionFisicaDocumento($serverRoot . '/'
                                                            . $strRutaFisicaCompleta . '/Acta_EPP_' .
                                                            $intIdComunicacion . '-' . $hora . '.pdf');
            }

            $infoDocumento->setEstado('Activo');
            $infoDocumento->setEmpresaCod($arrayParametros['idEmpresa']);
            $infoDocumento->setFechaDocumento(new \DateTime('now'));
            $infoDocumento->setUsrCreacion($arrayParametros['usrCreacion']);
            $infoDocumento->setFeCreacion(new \DateTime('now'));
            $infoDocumento->setIpCreacion($arrayParametros['ipCreacion']);
            $this->emComunicacion->persist($infoDocumento);
            $this->emComunicacion->flush();

            if(is_object($infoDocumento) && $infoDocumento->getId() > 0)
            {
                $intIdDocumento = $infoDocumento->getId();
            }
            
            //InfoDocumentoRelacion
            $infoDocumentoRelacion = new InfoDocumentoRelacion();
            $infoDocumentoRelacion->setDocumentoId($infoDocumento->getId());
            $infoDocumentoRelacion->setModulo('TECNICO');
            if(!empty($intIdServicio))
            {
               $infoDocumentoRelacion->setServicioId($intIdServicio);
               $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                ->find($intIdServicio);
               if(is_object($objServicio))
               {
                   $infoDocumentoRelacion->setPuntoId($objServicio->getPuntoId()->getId());
               }
               if(is_object($objServicio->getPuntoId()))
               {
                   $infoDocumentoRelacion->setPersonaEmpresaRolId($objServicio->getPuntoId()->getPersonaEmpresaRolId()->getId());
               }
            }
            //Se obtiene el id de la solicitud de planificacion que tiene asociado el servicio, con el objetivo de relacionarlo con el
            //documento
            $infoDocumentoRelacion->setDetalleId($arrayParametros['idDetalle']);
            $infoDocumentoRelacion->setEstado('Activo');
            $infoDocumentoRelacion->setFeCreacion(new \DateTime('now'));
            $infoDocumentoRelacion->setUsrCreacion($arrayParametros['usrCreacion']);
            $this->emComunicacion->persist($infoDocumentoRelacion);
            $this->emComunicacion->flush();
            
            $strMensaje = 'Acta Arcotel procesada correctamente';
            $strStatus = "OK";
        }
        catch(\Exception $e)
        {
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
            }

            $strStatus             = "ERROR";
            $strMensaje            = "Mensaje: ".$e->getMessage();
            $arrayRespuestaFinal   = array('status'=>$strStatus, 'mensaje'=>$strMensaje);
            return $arrayRespuestaFinal;
        }
        if ($this->emComunicacion->getConnection()->isTransactionActive())
        {
            $this->emComunicacion->getConnection()->commit();
        }

        $this->emComunicacion->getConnection()->close();

        //*RESPUESTA-------------------------------------------------------------*/
        $arrayRespuestaFinal = array('status' => $strStatus, 'mensaje' => $strMensaje, 'documento' => $intIdDocumento);
        return $arrayRespuestaFinal;
        //*----------------------------------------------------------------------*/
    }

    /**
     * Funcion que sirve para grabar el Acta de Entrega, generar la firma,
     * generar el pdf y enviar el pdf por mail al cliente.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 25-Junio-2015
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 02-12-2015 - Se realizan ajustes para relacionar el detalleId de la solicitud de planificacion al momento de guardar la 
     *                           acta de recepcion
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.2 13-06-2017 - Se obtiene el codigo de la plantilla por empresa para el acta y terminos y condiciones
     *                           desde la tabla ADMI_PARAMETROS_DET, se obtiene los equipos entregados al cliente para armar el PDF.
     *
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.3 18-12-2018 - Se adiciona los equipos ExtenderDualBand al generar acta.
     * @since 1.2 
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.4 11-01-2019 - Se agrega validación para guardar acta de Netvoice y el nombre del producto.
     *                           
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.4 09-07-2019 - Se agrega parámetro $arrayEquiposInTec que contiene los equipos ingresados por el técnico y el
     *                           parámetro $arrayParametros['firmaTelcos'] con imagen de 'firma generada desde Telcos' 
     *                           cuando el acta es generada desde Telcos, valida el envío de correo, inserta el progreso de ACTAS.
     *
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.5 13-05-2020 - Se modifica parametro en la consulta de la vista de articulos del NAF.
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.6 02-06-2020 - Se modifica código para crear nueva estructura de archivos.
     * 
     *
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.7 01-12-2020 - Se agrega variable $strTipoProducto para validar nueva plantilla
     * de acta de entrega de servicio para producto de Cableado Md
     *   
     * 
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.7 12-11-2020 - Almacenar el pdf en el servidor NFS remoto.
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.8 20-09-2021 - Se inicializa campo $strMaterial['materialDescripcion'] para
     * que no exista problema al momento de hacer match con el html del acta en productos CABLEADO_MD.
     * 
     *
     * Se reemplaza logica para extraer dato del producto, haciendo uso del ORM de SF.
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.9 28-01-2022 
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.0 14/10/2022 - Se agrega la generación del reporte fotográfico.
     * 
     * @param array $arrayParametros
     */
    public function grabarActaEntrega($arrayParametros)
    {
        $idEmpresa              = $arrayParametros['idEmpresa'];
        $prefijoEmpresa         = $arrayParametros['prefijoEmpresa'];
        $idServicio             = $arrayParametros['idServicio'];
        $idDetalle              = $arrayParametros['idDetalle'];
        $firmaClienteCoord      = $arrayParametros['firmaClienteCoord'];
        $firmaEmpleadoCoord     = $arrayParametros['firmaEmpleadoCoord'];
        $firmaCliente64         = $arrayParametros['firmaCliente64'];
        $firmaEmpleado64        = $arrayParametros['firmaEmpleado64'];
        $arrayPreguntaRespuesta = $arrayParametros['preguntaRespuesta'];
        $usrCreacion            = $arrayParametros['usrCreacion'];
        $ipCreacion             = $arrayParametros['ipCreacion'];
        $feCreacion             = $arrayParametros['feCreacion'];
        $serverRoot             = $arrayParametros['serverRoot'];
        $pathSource             = $arrayParametros['pathSource'];
        $start                  = $arrayParametros['start'];
        $limit                  = $arrayParametros['limit'];
        $hora                   = date('Y-m-d-His');
        $fecha                  = date('Y-m-d');
        $status                 = "ERROR";
        $strModulo              = "TECNICO";
        $strCodigoActa          = '';
        $strCodigoActaTerminos  = '';
        $boolFirmaDefault       = false;
        $strTelcosFirma         = $arrayParametros['firmaTelcos'];
        $intDetalleId           = '';
        $arrayEquiposInTec      = $arrayParametros['equiposIngresados'];
        $strRutaFisicaCompleta  = '';
        $arrayFirma             = array();
        $arrayDocumento         = array();
        $strTipoProducto        = $arrayParametros['strTipoProducto'];
        $boolGenerar            = false;
        $arrayData              = array();
        $arrayData['urlReporteFotografico'] = "";
               
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

            $extensionArchivo       = 'png';
            $nombreArchivoCliente   = $idServicio.'_cliente';
            $nombreArchivoEmpleado  = $idServicio.'_empleado';

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
                //grabar firma cliente en imagen
                if($firmaClienteCoord!="")
                {
                    $this->procesarImagenesService->grabarImagenCoordenadas($firmaClienteCoord, $nombreArchivoCliente, 
                                                                            $strRutaFisicaArchivo, $extensionArchivo);
                }
                if($firmaCliente64!="")
                {
                    $this->procesarImagenesService->grabarImagenBase64($firmaCliente64, 
                                                                        $nombreArchivoCliente, 
                                                                        $strRutaFisicaArchivo, 
                                                                        $extensionArchivo);
                }
                
                //grabar firma empleado en imagen
                if($firmaEmpleadoCoord!="")
                {
                    $this->procesarImagenesService->grabarImagenCoordenadas($firmaEmpleadoCoord, $nombreArchivoEmpleado, 
                                                                            $strRutaFisicaArchivo, $extensionArchivo);
                }
                if($firmaEmpleado64!="")
                {
                    $this->procesarImagenesService->grabarImagenBase64($firmaEmpleado64, 
                                                                        $nombreArchivoEmpleado, 
                                                                        $strRutaFisicaArchivo, 
                                                                        $extensionArchivo);
                }
                else
                {
                    $boolFirmaDefault = true;
                }
                
                //InfoEncuesta
                $infoEncuesta = new InfoEncuesta();
                $infoEncuesta->setEstado('Activo');
                $codigo = $this->emComunicacion->getRepository('schemaBundle:InfoEncuesta')->getCodigoActaEntrega();
                $infoEncuesta->setCodigo($codigo);
                $infoEncuesta->setNombreEncuesta('Acta Entrega Codigo : ' . $codigo);
                $infoEncuesta->setFeCreacion($feCreacion);
                $infoEncuesta->setUsrCreacion($usrCreacion);
                $infoEncuesta->setIpCreacion($ipCreacion);
                $this->emComunicacion->persist($infoEncuesta);
                $this->emComunicacion->flush();

                $servicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
                $punto    = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($servicio->getPuntoId());
                //Preguntas con sus Respuestas
                $arrayPreguntasRespuestas = explode("|", $arrayPreguntaRespuesta);

                if(!is_object($infoEncuesta) || $infoEncuesta->getId()==null || $infoEncuesta->getId()==0)
                {
                    throw new \Exception("Problema al procesar el archivo. 
                    Objeto encuesta no se ha guardado correctamente, intente nuevamente");
                }
                foreach($arrayPreguntasRespuestas as $pregResp)
                {
                    $respuestas = explode("-", $pregResp);
                    
                    if(count($respuestas) > 1)
                    {
                        $pregunta   = $respuestas[0];
                        
                        for($i=1;$i<count($respuestas);$i++)
                        {
                            $infoEncuestaPregunta = new InfoEncuestaPregunta();
                            $infoEncuestaPregunta->setPreguntaId($pregunta);
                            $infoEncuestaPregunta->setEncuestaId($infoEncuesta->getId());
                            $infoEncuestaPregunta->setValor($respuestas[$i]);
                            $infoEncuestaPregunta->setEstado('Activo');
                            $infoEncuestaPregunta->setFeCreacion($feCreacion);
                            $infoEncuestaPregunta->setUsrCreacion($usrCreacion);
                            $infoEncuestaPregunta->setIpCreacion($ipCreacion);
                            $this->emComunicacion->persist($infoEncuestaPregunta);
                            $this->emComunicacion->flush();
                        }
                    }
                }

                //obtener datos cliente, servicio, punto, contactos, preguntas
                $arrParametros  = array (
                                        'idServicio'        => $idServicio,
                                        'idEmpresa'         => $idEmpresa,
                                        'prefijoEmpresa'    => $prefijoEmpresa,
                                        'strModulo'         => $strModulo,
                                        'start'             => $start,
                                        'limit'             => $limit,
                                        'equiposIngresados' => $arrayEquiposInTec
                                        );
                $arrResultado   = $this->getActaEntrega($arrParametros);
                
                //Obtenemos el codigo de la plantilla para poder generar el acta
                $arrayAdmiParametroDetActa = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne('CODIGO_ACTA_ENTREGA_INSTALACION_POR_EMPRESA',
                                                                    'TECNICO',
                                                                    '',
                                                                    '',
                                                                    'CODIGO_ACTA_ENTREGA',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    $idEmpresa);
                if (isset($arrayAdmiParametroDetActa['valor2']) && !empty($arrayAdmiParametroDetActa['valor2']))
                {
                    $strDescTarea = "";
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
                        $strCodigoActa = 'ACT-ENT-NV-INS';
                    }
                    else if ($strTipoProducto == 'CABLEADO_MD')
                    {
                           $strCodigoActa ='ACT-MD-INS-CABL';
                    }
                    else
                    {
                        $strCodigoActa = $arrayAdmiParametroDetActa['valor2'];
                    }  
                    
                    
                    
                }
                if (isset($arrayAdmiParametroDetActa['valor3']) && !empty($arrayAdmiParametroDetActa['valor3']))
                {
                    $strCodigoActaTerminos = $arrayAdmiParametroDetActa['valor3'];
                }
                //obtener terminos y condiciones
                $objPlantillaTerminos   = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                                            ->findOneBy(array("codigo" => $strCodigoActaTerminos));
                $strTerminosCondiciones = is_object($objPlantillaTerminos)?$objPlantillaTerminos->getPlantilla():"";

                //Se genera documento fisico del PDF relacionado a la plantilla
                $objPlantillaActa = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')->findOneByCodigo($strCodigoActa);
                $strHtml          = is_object($objPlantillaActa)?$objPlantillaActa->getPlantilla():"";

                //obtener los materiales asignados en la solicitud
                $infoMateriales   = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolMaterial')
                                                    ->getMaterialesPorServicio($idServicio);
                
                $arrayMateriales  = array();
                $totalMateriales  = 0;
                if(is_array($infoMateriales))
                {
                    foreach($infoMateriales as $strMaterial)
                    {
                        if($strMaterial['cantidadFacturada']>0)
                        {
                            $strMaterial['materialDescripcion']= "";

                            $vArticulo = $this->emNaf->getRepository('schemaBundle:VArticulosEmpresas')
                                                    ->getOneArticulobyEmpresabyCodigo( $idEmpresa, $strMaterial['materialCod']); 
                            if($vArticulo && count($vArticulo)>0)
                            {
                                $descripcionArticulo = (isset($vArticulo) ? ($vArticulo->getDescripcion() ? $vArticulo->getDescripcion() : "") : ""); 
                                $strMaterial['materialDescripcion']= $descripcionArticulo; 
                            }
                            $strMaterial['cantidadEmpresa'] = $strMaterial['cantidadUsada'] - $strMaterial['cantidadFacturada'];
                            $strMaterial['materialCostoExcedente'] = $strMaterial['costoMaterial']*$strMaterial['cantidadFacturada'];
                            
                            $totalMateriales += $strMaterial['materialCostoExcedente'];
                            
                            $arrayMateriales[] = $strMaterial;
                        }
                    }
                }            
                        
                //obtener las preguntas con sus respuestas para generar el pdf
                $infoActaPreguntaRespuesta = $this->emComunicacion->getRepository('schemaBundle:InfoEncuestaPregunta')
                                                ->findByEncuestaId($infoEncuesta->getId());
                
                $preguntasActa = $arrResultado['opcionesActaEntrega']['preguntas'];
                foreach($preguntasActa as $preguntaActa)
                {
                    $idPreguntaActa     = $preguntaActa['idPregunta'];
                    $nombrePreguntaActa = $preguntaActa['pregunta'];
                    $descPregunta       = $preguntaActa['descPregunta'];
                    $tipoRespuestaActa  = $preguntaActa['tipoRespuesta'];
                    $respuestasActa     = $preguntaActa['respuestas'];


                    foreach((array) $respuestasActa as $respuestaActa)
                    {
                        $flagRespuesta       = "false";
                        $nombreRespuestaActa = $respuestaActa['respuesta'];
                        foreach($infoActaPreguntaRespuesta as $actaPreguntaRespuesta)
                        {
                            if($actaPreguntaRespuesta->getPreguntaId() == $idPreguntaActa && 
                                $actaPreguntaRespuesta->getValor() == $nombreRespuestaActa)
                            {
                                $flagRespuesta = "true";
                                break;
                            }
                        }
                        
                        $arrayPreguntaActaEntregaPdf[] = array  (
                                                                    'idPregunta'    => $idPreguntaActa,
                                                                    'pregunta'      => $nombrePreguntaActa,
                                                                    'descPregunta' => $descPregunta,
                                                                    'respuesta'     => $nombreRespuestaActa,
                                                                    'tipoRespuesta' => $tipoRespuestaActa,
                                                                    'flag'          => $flagRespuesta
                                                                );
                    }
                    
                    //SI LA PREGUNTA NO ES DE SELECCION DE RESPUESTA ENTONCES SE BUSCA EL TEXTO ESCRITO POR EL USUARIO
                    if($tipoRespuestaActa == "TEXTO")
                    {
                        $infoActaPreguntaRespuestaText = $this->emComunicacion->getRepository('schemaBundle:InfoEncuestaPregunta')
                                                                            ->findOneBy(
                                                                                            array(
                                                                                                'encuestaId'=>$infoEncuesta->getId(),
                                                                                                'preguntaId'=>$idPreguntaActa
                                                                                                )
                                                                                        );
                        $strRespuestaText="";
                        if (is_object($infoActaPreguntaRespuestaText))
                        {
                            $strRespuestaText = $infoActaPreguntaRespuestaText->getValor();
                        }
                        $arrayPreguntaActaEntregaPdf[] = array  (
                                                                'idPregunta'    => $idPreguntaActa,
                                                                'pregunta'      => $nombrePreguntaActa,
                                                                'descPregunta'  => $descPregunta,
                                                                'respuesta'     => $strRespuestaText,
                                                                'tipoRespuesta' => $tipoRespuestaActa,
                                                                'flag'          => "true"
                                                                );
                    }
                }
                                
                $archivo = fopen($pathSource . '/Resources/views/Default/actaEntrega.html.twig', "w");
                
                
                if($archivo)
                {
                    fwrite($archivo, $strHtml);
                    fclose($archivo);
                    //generar PDF
                    $arrayPdf = array   (
                                            'serverRoot'            => $serverRoot,
                                            'fecha'                 => $fecha,
                                            'idServicio'            => $idServicio,
                                            'idCaso'                => '',
                                            'prefijoEmpresa'        => $prefijoEmpresa,
                                            'preguntaActaEntrega'   => $arrayPreguntaActaEntregaPdf,
                                            'materiales'            => $arrayMateriales,
                                            'totalMateriales'       => $totalMateriales,
                                            'codigo'                => $codigo,
                                            'hora'                  => $hora,
                                            'nombreProducto'        => $strDescTarea,
                                            'servicio'              => $arrResultado['servicio'],
                                            'datosCliente'          => $arrResultado['datosCliente'],
                                            'formaContactoPunto'    => $arrResultado['formaContactoPunto'],
                                            'formaContactoCliente'  => $arrResultado['formaContactoCliente'],
                                            'contactoCliente'       => $arrResultado['contactoCliente'],
                                            'elementoCpe'           => $arrResultado['elementoCpe'],
                                            'elementoOnt'           => $arrResultado['elementoOnt'],
                                            'elementoWifi'          => $arrResultado['elementoWifi'],
                                            'macCpe'                => $arrResultado['macCpe'],
                                            'macOnt'                => $arrResultado['macOnt'],
                                            'macWifi'               => $arrResultado['macWifi'],
                                            'equiposEntregado'      => $arrResultado['dataTecnica'],
                                            'ultimaMilla'           => $arrResultado['ultimaMilla'],
                                            'comparticion'          => "2:1",
                                            'terminosCondiciones'   => $strTerminosCondiciones,
                                            'modulo'                => "tecnico",
                                            'boolFirmaDefault'      => $boolFirmaDefault,
                                            'arrayElementosRegis'   => $arrResultado['arrayElementosRegis'],
                                            'firmaTelcos'           => $strTelcosFirma,
                                            'strRutaFisicaCompleta' => $strRutaFisicaCompleta,
                                            'strRutaFisicaArchivo'  => $strRutaFisicaArchivo,
                                            'bandNfs'               => $arrayParametros['bandNfs'],
                                            'strApp'                => $arrayParametros['strAplicacion'],
                                            'strSubModulo'          => $arrayParametros['strOrigenAccion'],
                                            'idComunicacion'        => $idDetalle,
                                            'strUsrCreacion'        => $usrCreacion
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

                    //generar reporte fotografico
                    if(!empty($idServicio))
                    {
                        $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
                        if(is_object($objServicio) && is_object($objServicio->getProductoId()))
                        {
                            $arrayParProPermReporteFoto = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('PARAMETROS_REPORTE_FOTOGRAFICO',
                                                                                'TECNICO',
                                                                                '',
                                                                                '',
                                                                                'PRODUCTO_PERMITIDO',
                                                                                $objServicio->getProductoId()->getId(),
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                $idEmpresa);
                            if(isset($arrayParProPermReporteFoto) && !empty($arrayParProPermReporteFoto))
                            {
                                $arrayPdf['idEmpresa']        = $idEmpresa;
                                $arrayPdf['objPunto']         = $objServicio->getPuntoId();
                                $arrayPdf['pathSource']       = $arrayParametros['pathSource'];
                                $arrayPdf['idProducto']       = $objServicio->getProductoId()->getId();
                                $arrayPdf['strFeCreacion']    = $feCreacion;
                                $arrayPdf['strIpCreacion']    = $ipCreacion;
                                $arrayRespuestaReporteFoto    = $this->generarReporteFotografico($arrayPdf);
                                //verificar si muestra el reporte fotografico
                                $arrayParProMosReporteFoto    = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('PARAMETROS_REPORTE_FOTOGRAFICO',
                                                                                'TECNICO',
                                                                                '',
                                                                                '',
                                                                                'MOSTRAR_REPORTE_FOTOGRAFICO',
                                                                                $objServicio->getProductoId()->getId(),
                                                                                'NO',
                                                                                '',
                                                                                '',
                                                                                $idEmpresa);
                                if($arrayRespuestaReporteFoto['status'] == "OK"
                                   && (!isset($arrayParProMosReporteFoto) || empty($arrayParProMosReporteFoto)))
                                {
                                    $arrayData['urlReporteFotografico'] = $arrayRespuestaReporteFoto['rutaArchivo'];
                                }
                            }
                        }
                    }

                    //eliminar los archivos de firmas
                    unlink($strRutaFisicaArchivo.$nombreArchivoCliente.'.'.$extensionArchivo);
                    unlink($strRutaFisicaArchivo.$nombreArchivoEmpleado.'.'.$extensionArchivo);
                    
                    //enviar por mail plantilla
                    $arrayPlantilla = array(
                                                'serverRoot'            => $serverRoot,
                                                'codigo'                => $codigo,
                                                'hora'                  => $hora,
                                                'punto'                 => $punto,
                                                'strModulo'             => $strModulo,
                                                'prefijoEmpresa'        => $prefijoEmpresa,
                                                'idEmpresa'             => $idEmpresa,
                                                'idServicio'            => $idServicio,
                                                'strRutaFisicaCompleta' => $strRutaFisicaCompleta,
                                                'bandNfs'               => $arrayParametros['bandNfs']
                                            );
                    if(empty($strTelcosFirma))
                    {
                        $this->enviarPlantilla($arrayPlantilla);
                    }
                    else
                    {   
                        $intIdComunicacion = $this->emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                                            ->getMinimaComunicacionPorDetalleId($intDetalleId);
                
                        $strParametroRegularizar    = "";
                        $strParametroOrigenWeb      = "";
                        
                        $arrayParametroOrigenWeb = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne('ORIGEN_WEB', 
                                                                    '', 
                                                                    '', 
                                                                    '', 
                                                                    '', 
                                                                    '', 
                                                                    '', 
                                                                    ''
                                                                    );

                        if (is_array($arrayParametroOrigenWeb))
                        {
                            $strParametroOrigenWeb = !empty($arrayParametroOrigenWeb['valor1']) ? $arrayParametroOrigenWeb['valor1'] : "";
                        }

                        $arrayParametroRegularizar = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->getOne('PROGRESO_REGULARIZACION', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            ''
                                                                            );

                        if (is_array($arrayParametroRegularizar))
                        {
                            $strParametroRegularizar = !empty($arrayParametroRegularizar['valor1']) ? $arrayParametroRegularizar['valor1'] : "";
                        }
                        
                        $arrayProgresoActa     = array(
                                                'strCodEmpresa'        => $idEmpresa,
                                                'intIdTarea'           => $intIdComunicacion,
                                                'intIdDetalle'         => $intDetalleId,
                                                'strCodigoTipoProgreso'=> "ACTAS",
                                                'intIdServicio'        => $idServicio,
                                                'strOrigen'            => $strParametroOrigenWeb,
                                                'strUsrCreacion'       => $usrCreacion,
                                                'strIpCreacion'        => '127.0.0.1');
                        
                        $this->soporteService->ingresarProgresoTarea($arrayProgresoActa);

                        $arrayProgRegularizar     = array(
                                                'strCodEmpresa'        => $idEmpresa,
                                                'intIdTarea'           => $intIdComunicacion,
                                                'intIdDetalle'         => $intDetalleId,
                                                'strCodigoTipoProgreso'=> $strParametroRegularizar,
                                                'intIdServicio'        => $idServicio,
                                                'strOrigen'            => $strParametroOrigenWeb,
                                                'strUsrCreacion'       => $usrCreacion,
                                                'strIpCreacion'        => '127.0.0.1');
                                        
                        $this->soporteService->ingresarProgresoTarea($arrayProgRegularizar);
                    }
                    $strMensaje = 'Acta procesada correctamente';
                    $status = "OK";
                }
                else
                {
                    throw new \Exception("Problema al procesar el archivo (Permisos), intenta nuevamente");
                }

                $tipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')->findOneByExtensionTipoDocumento('PDF');
                if(!is_object($tipoDocumento))
                {
                    throw new \Exception("Problema al procesar el archivo.
                    Objeto tipo documento PDF no se ha encontrado, intente nuevamente");
                }
                //InfoDocumento
                $infoDocumento = new InfoDocumento();
                $infoDocumento->setTipoDocumentoId($tipoDocumento);
                $infoDocumento->setTipoDocumentoGeneralId(8);
                $infoDocumento->setNombreDocumento('Acta Entrega Codigo : ' . $codigo);
                $infoDocumento->setUbicacionLogicaDocumento('Acta_Entrega_' . $codigo . '-' . $hora . '.pdf');
                if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'])
                {
                    $infoDocumento->setUbicacionFisicaDocumento($strRutaFisicaCompleta);
                }
                else
                {
                    $infoDocumento->setUbicacionFisicaDocumento($serverRoot . '/' . 
                                                                $strRutaFisicaCompleta . '/Acta_Entrega_' . 
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
                    $intDetalleId = $detalle->getId();
                }
                else if(is_array($detalle))
                {
                    $infoDocumentoRelacion->setDetalleId($detalle['IDDETALLE']);
                    $intDetalleId = $detalle['IDDETALLE'];
                }
                else
                {
                    throw new \Exception("Problema al procesar el acta no se ha encontrado la solicitud asociada a este servicio," .
                                         "intente nuevamente.");
                }

                $infoDocumentoRelacion->setEstado('Activo');
                $infoDocumentoRelacion->setFeCreacion($feCreacion);
                $infoDocumentoRelacion->setUsrCreacion($usrCreacion);
                $this->emComunicacion->persist($infoDocumentoRelacion);
                $this->emComunicacion->flush();

                if(!is_object($infoDocumentoRelacion) || $infoDocumentoRelacion->getId()==null || $infoDocumentoRelacion->getId()==0){
                    throw new \Exception("Problema al procesar el acta, no se ha podido relacionar el documento. Intente nuevamente.");
                }
            }
            else
            {
                $status     = "ERROR";
                $strMensaje = "No se puede crear directorio.";
            }

            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->commit();
            }
            
            $this->emComunicacion->getConnection()->close();
        }
        catch(\Exception $e)
        {
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
            }

            $status             = "ERROR";
            $strMensaje         = "Mensaje: ".$e->getMessage();
            $arrayRespFinal     = array('status'=>$status, 'mensaje'=>$strMensaje, 'data'=>$arrayData);
            return $arrayRespFinal;
        }
        
        //*RESPUESTA-------------------------------------------------------------*/
        $arrayRespFinal = array('status' => $status, 'mensaje' => $strMensaje, 'data'=>$arrayData);
        return $arrayRespFinal;
        //*----------------------------------------------------------------------*/
    }
    
    /**
     * Funcion que sirve para grabar el Acta de Entrega de Soporte (visita tecnica), generar la firma,
     * generar el pdf y enviar el pdf por mail al cliente.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-07-2015
     * @param array $arrayParametros
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 - 20-06-2017 - Se envia el idServicio para generar la acta de soporte, se obtiene los equipos instalados del servicio,
     *                             se recibe el parametro facturable para determinar si la visita debe ser facturada con su respectivo valor,
     *                             se envia el modulo para generar la acta de soporte.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.2 - 07-09-2017 - Se implementa bandera para determinar si la factura debe ser realizada o no.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.3 - 01-12-2017 - Primero se debe generar el acta y luego se procede a facturar la visita tecnica.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.4 - 21-05-2018 - Se manda por default la imagen del Ing.Hugo Proaño.
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.5
     * @since 07-09-2018
     * Se envían los parámetros strNumeroCaso y floatHorasFacturadas para insertarlos como valores en las características.<br>
     * Se agregan los parámetros facturacionEquipos, equiposFacturados, floatTotalEquipos para llenar la plantilla.<br>
     * Se modifica el orden del código. Primero se realiza el cálculo y creación de la solicitud y posteriormente se envía el acta al cliente.<br>
     * Si el idCaso no es proporcionado desde el móvil, se obtiene desde la tarea.
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.6 - 04-07-2019 - Se agrega control de transacción de esquema de base de datos comercial para poder realizar
     *                             rollback de información en caso de que se presente alguna excepción
     * @since 1.5
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.7 02-06-2020 - Se modifica código para crear nueva estructura de archivos.
     * @since 1.6
     * 
     * @author Wilmer Vera González <wvera@telconet.ec>
     * @version 1.8 06-07-2020 - Se agrega datos del enlace, para lógica de nueva plantilla de correo.
     * @since 1.7
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.9 29-12-2020 - Se modifica código para visualizar documentos en grid de tareas.
     * @since 1.8
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 2.0 09-02-2021 - Se modifica código para guardar archivos por medio de NFS.
     * 
     */
    public function grabarActaEntregaSoporte($arrayParametros)
    {
        $idEmpresa              = $arrayParametros['idEmpresa'];
        $prefijoEmpresa         = $arrayParametros['prefijoEmpresa'];
        $idCaso                 = $arrayParametros['idCaso'];
        $intIdDetalle           = $arrayParametros['idDetalle'];
        $idServicio             = $arrayParametros['idServicio'];
        $firmaClienteCoord      = $arrayParametros['firmaClienteCoord'];
        $firmaEmpleadoCoord     = $arrayParametros['firmaEmpleadoCoord'];
        $firmaCliente64         = $arrayParametros['firmaCliente64'];
        $firmaEmpleado64        = $arrayParametros['firmaEmpleado64'];
        $arrayPreguntaRespuesta = $arrayParametros['preguntaRespuesta'];
        $usrCreacion            = $arrayParametros['usrCreacion'];
        $ipCreacion             = $arrayParametros['ipCreacion'];
        $feCreacion             = $arrayParametros['feCreacion'];
        $serverRoot             = $arrayParametros['serverRoot'];
        $pathSource             = $arrayParametros['pathSource'];
        $boolFacturable         = $arrayParametros['facturable'];
        $floatHorasFactura      = $arrayParametros['horasFactura'];
        $start                  = $arrayParametros['start'];
        $limit                  = $arrayParametros['limit'];
        //1.8
        $intLatenciaMedia          = $arrayParametros['latenciaMedia'];
        $intPaquetesEnviados       = $arrayParametros['paquetesEnviados'];
        $intPaquetesRecibidos      = $arrayParametros['paquetesRecibidos']; 
        $boolStatusPing            = $arrayParametros['statusPing']; 
        
        
        $hora                   = date('Y-m-d-His');
        $fecha                  = date('Y-m-d');
        $strModulo              = "SOPORTE";
        $status                 = "ERROR";
        $strCodigoActa          = '';
        $strCodigoActaTerminos  = '';
        $boolFirmaDefault       = false;
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
        $this->emComercial->getConnection()->beginTransaction();
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

            $extensionArchivo       = 'png';
            $nombreArchivoCliente   = $idCaso.'_cliente';
            $nombreArchivoEmpleado  = $idCaso.'_empleado';
            
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
                //grabar firma cliente en imagen
                if($firmaClienteCoord!="")
                {
                    $this->procesarImagenesService->grabarImagenCoordenadas($firmaClienteCoord, $nombreArchivoCliente, 
                                                                            $strRutaFisicaArchivo, $extensionArchivo);
                }
                if($firmaCliente64!="")
                {
                    $this->procesarImagenesService->grabarImagenBase64($firmaCliente64, 
                                                                        $nombreArchivoCliente, 
                                                                        $strRutaFisicaArchivo, 
                                                                        $extensionArchivo);
                }
                else
                {
                    $boolFirmaDefault = true;
                }
                //grabar firma empleado en imagen
                if($firmaEmpleadoCoord!="")
                {
                    $this->procesarImagenesService->grabarImagenCoordenadas($firmaEmpleadoCoord, $nombreArchivoEmpleado, 
                                                                            $strRutaFisicaArchivo, $extensionArchivo);
                }
                if($firmaEmpleado64!="")
                {
                    $this->procesarImagenesService->grabarImagenBase64($firmaEmpleado64, 
                                                                        $nombreArchivoEmpleado, 
                                                                        $strRutaFisicaArchivo, 
                                                                        $extensionArchivo);
                }
                else
                {
                    $boolFirmaDefault = true;
                }
                
                //InfoEncuesta
                $infoEncuesta = new InfoEncuesta();
                $infoEncuesta->setEstado('Activo');
                $codigo = $this->emComunicacion->getRepository('schemaBundle:InfoEncuesta')->getCodigoActaEntrega();
                $infoEncuesta->setCodigo($codigo);
                $infoEncuesta->setNombreEncuesta('Acta Entrega Codigo : ' . $codigo);
                $infoEncuesta->setFeCreacion($feCreacion);
                $infoEncuesta->setUsrCreacion($usrCreacion);
                $infoEncuesta->setIpCreacion($ipCreacion);
                $this->emComunicacion->persist($infoEncuesta);
                $this->emComunicacion->flush();

                $servicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
                $punto    = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($servicio->getPuntoId());
                //Preguntas con sus Respuestas
                $arrayPreguntasRespuestas = explode("|", $arrayPreguntaRespuesta);

                foreach($arrayPreguntasRespuestas as $pregResp)
                {
                    $respuestas = explode("-", $pregResp);
                    
                    if(count($respuestas) > 1)
                    {
                        $pregunta   = $respuestas[0];
                        
                        for($i=1;$i<count($respuestas);$i++)
                        {
                            $infoEncuestaPregunta = new InfoEncuestaPregunta();
                            $infoEncuestaPregunta->setPreguntaId($pregunta);
                            $infoEncuestaPregunta->setEncuestaId($infoEncuesta->getId());
                            $infoEncuestaPregunta->setValor($respuestas[$i]);
                            $infoEncuestaPregunta->setEstado('Activo');
                            $infoEncuestaPregunta->setFeCreacion($feCreacion);
                            $infoEncuestaPregunta->setUsrCreacion($usrCreacion);
                            $infoEncuestaPregunta->setIpCreacion($ipCreacion);
                            $this->emComunicacion->persist($infoEncuestaPregunta);
                            $this->emComunicacion->flush();
                        }
                    }
                }
                
                //obtener datos cliente, servicio, punto, contactos, preguntas
                $arrParametros  = array (
                                        'idServicio'    => $idServicio,
                                        'idEmpresa'     => $idEmpresa,
                                        'prefijoEmpresa'=> $prefijoEmpresa,
                                        'strModulo'     => $strModulo,
                                        'start'         => $start,
                                        'limit'         => $limit
                                        );
                $arrResultado   = $this->getActaEntrega($arrParametros);
                //obtener terminos y condiciones
                $objPlantilla   = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                                    ->findOneBy(array("codigo" => "TER-CON-ACT-ENT"));
                
                //Se genera documento fisico del PDF relacionado a la plantilla
                //Obtenemos el codigo de la plantilla para poder generar el acta
                $arrayAdmiParametroDetActa = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('CODIGO_ACTA_ENTREGA_VISITA_POR_EMPRESA',
                                                                'TECNICO',
                                                                '',
                                                                '',
                                                                'CODIGO_ACTA_ENTREGA',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                $idEmpresa);
                if (isset($arrayAdmiParametroDetActa['valor2']) && !empty($arrayAdmiParametroDetActa['valor2']))
                {
                    $strCodigoActa = $arrayAdmiParametroDetActa['valor2'];
                }
                if (isset($arrayAdmiParametroDetActa['valor3']) && !empty($arrayAdmiParametroDetActa['valor3']))
                {
                    $strCodigoActaTerminos = $arrayAdmiParametroDetActa['valor3'];
                }
                //obtener terminos y condiciones
                $objPlantillaTerminos      = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                                                ->findOneBy(array("codigo" => $strCodigoActaTerminos));
                $strTerminosCondiciones    = is_object($objPlantillaTerminos)?$objPlantillaTerminos->getPlantilla():"";

                //Se genera documento fisico del PDF relacionado a la plantilla
                $objPlantillaActa          = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')->findOneByCodigo($strCodigoActa);
                $strHtml                   = is_object($objPlantillaActa)?$objPlantillaActa->getPlantilla():"";
                
                //obtener las preguntas con sus respuestas para generar el pdf
                $infoActaPreguntaRespuesta = $this->emComunicacion->getRepository('schemaBundle:InfoEncuestaPregunta')
                                            ->findByEncuestaId($infoEncuesta->getId());
                
                $preguntasActa = $arrResultado['opcionesActaEntrega']['preguntas'];
                
                foreach($preguntasActa as $preguntaActa)
                {
                    $idPreguntaActa     = $preguntaActa['idPregunta'];
                    $nombrePreguntaActa = $preguntaActa['pregunta'];
                    $descPregunta       = $preguntaActa['descPregunta'];
                    $tipoRespuestaActa  = $preguntaActa['tipoRespuesta'];
                    $respuestasActa     = $preguntaActa['respuestas'];
                    foreach($respuestasActa as $respuestaActa)
                    {
                        $flagRespuesta       = "false";
                        $nombreRespuestaActa = $respuestaActa['respuesta'];
                        foreach($infoActaPreguntaRespuesta as $actaPreguntaRespuesta)
                        {
                            if($actaPreguntaRespuesta->getPreguntaId() == $idPreguntaActa && 
                                $actaPreguntaRespuesta->getValor() == $nombreRespuestaActa)
                            {
                                $flagRespuesta = "true";
                                break;
                            }
                        }
                        
                        $arrayPreguntaActaEntregaPdf[] = array  (
                                                                    'idPregunta'    => $idPreguntaActa,
                                                                    'pregunta'      => $nombrePreguntaActa,
                                                                    'descPregunta'  => $descPregunta,
                                                                    'respuesta'     => $nombreRespuestaActa,
                                                                    'tipoRespuesta' => $tipoRespuestaActa,
                                                                    'flag'          => $flagRespuesta
                                                                );
                    }
                    //SI LA PREGUNTA NO ES DE SELECCION DE RESPUESTA ENTONCES SE BUSCA EL TEXTO ESCRITO POR EL USUARIO
                    if($tipoRespuestaActa == "TEXTO")
                    {
                        $infoActaPreguntaRespuestaText = $this->emComunicacion->getRepository('schemaBundle:InfoEncuestaPregunta')
                                                                            ->findOneBy(
                                                                                            array(
                                                                                                'encuestaId'=>$infoEncuesta->getId(),
                                                                                                'preguntaId'=>$idPreguntaActa
                                                                                                )
                                                                                        );
                        $strRespuestaText="";
                        if (is_object($infoActaPreguntaRespuestaText))
                        {
                            $strRespuestaText = $infoActaPreguntaRespuestaText->getValor();
                        }
                        $arrayPreguntaActaEntregaPdf[] = array  (
                                                                'idPregunta'    => $idPreguntaActa,
                                                                'pregunta'      => $nombrePreguntaActa,
                                                                'descPregunta'  => $descPregunta,
                                                                'respuesta'     => $strRespuestaText,
                                                                'tipoRespuesta' => $tipoRespuestaActa,
                                                                'flag'          => "true"
                                                                );
                    }
                }
                
                $archivo = fopen($pathSource . '/Resources/views/Default/actaEntrega.html.twig', "w");
                            
                if($archivo)
                {
                    fwrite($archivo, $strHtml);
                    fclose($archivo);

                    //Si es facturable se crea una solicitud.
                    if($boolFacturable)
                    {
                        //Se obtiene el número de caso.
                        if (!($idCaso > 0))
                        {
                            $arrayInfoCaso = $this->emSoporte->getRepository("schemaBundle:InfoDetalle")->getCasoPadreDesdeTarea($intIdDetalle);
                        }
                        else
                        {
                            $objInfoCaso                    = $this->emSoporte->getRepository("schemaBundle:InfoCaso")->findOneById($idCaso);
                            $arrayInfoCaso["strNumeroCaso"] = $objInfoCaso->getNumeroCaso();
                        }

                        $arrayParametrosSolicitudes['strUser']              = $usrCreacion;
                        $arrayParametrosSolicitudes['intIdServicio']        = $idServicio;
                        $arrayParametrosSolicitudes['floatHorasFacturadas'] = $floatHorasFactura ? floatval($floatHorasFactura) : 1;
                        $arrayParametrosSolicitudes['strNumeroCaso']        = $arrayInfoCaso["strNumeroCaso"];
                        $arrayParametrosSolicitudes['strEmpresaCod']        = $idEmpresa;

                        $intNumerosHorasAcobrar                      = 1;
                        if($floatHorasFactura < 1 && $prefijoEmpresa == 'MD')
                        {
                            $arrayAdmiParametroValorCobrar  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                            ->getOne('VALOR_COBRO_POR_VISITA_TAREAS',
                                                                                    'TECNICO',
                                                                                    '',
                                                                                    '',
                                                                                    'VALOR_COBRAR_POR_FRACCION',
                                                                                    '',
                                                                                    '',
                                                                                    '',
                                                                                    '',
                                                                                    $idEmpresa);
                        }
                        else
                        {
                            $arrayAdmiParametroValorCobrar  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                            ->getOne('VALOR_COBRO_POR_VISITA_TAREAS',
                                                                                    'TECNICO',
                                                                                    '',
                                                                                    '',
                                                                                    'VALOR_COBRAR_POR_HORA',
                                                                                    '',
                                                                                    '',
                                                                                    '',
                                                                                    '',
                                                                                    $idEmpresa);
                            if($prefijoEmpresa == "TN")
                            {
                                $intNumerosHorasAcobrar = round(floatval($floatHorasFactura)) == 0 ? 1 : round(floatval($floatHorasFactura));
                            }
                        }
                        if (isset($arrayAdmiParametroValorCobrar['valor2']) && !empty($arrayAdmiParametroValorCobrar['valor2']))
                        {
                            $arrayParametrosSolicitudes['floatValor'] = floatval($arrayAdmiParametroValorCobrar['valor2']) * $intNumerosHorasAcobrar;
                        }
                        // Consultar en los parametros si la bandera de facturar los soportes esta encendida.
                        $arrayAdmiParametroDebeFacurar  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('FACTURAR_VISITA_DESDE_MOVIL',
                                                                                $strModulo,
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                $idEmpresa);
                        if (isset($arrayAdmiParametroDebeFacurar['valor1']) &&
                            !empty($arrayAdmiParametroDebeFacurar['valor1']) &&
                            $arrayAdmiParametroDebeFacurar['valor1'] === 'SI')
                        {
                            $this->generarSolicitudDeSoporte($arrayParametrosSolicitudes);
                        }
                    }

                    //Obtiene información de la SOLICITUD DE CAMBIO DE MODEM INMEDIATO/ SOLICITUD FACTURACION DE CAMBIO DE EQUIPO.
                    $arrayRespuestaFactEquipos = $this->serviceSolicitudes
                                                    ->buscaInformacionSolicitudCambioModemPorFacturar(array("intServicioId" => $idServicio));
                    //generar PDF
                    $arrayPdf = array   (
                                            'serverRoot'            => $serverRoot,
                                            'fecha'                 => $fecha,
                                            'idServicio'            => '',
                                            'idCaso'                => $idCaso,
                                            'prefijoEmpresa'        => $prefijoEmpresa,
                                            'preguntaActaEntrega'   => $arrayPreguntaActaEntregaPdf,
                                            'codigo'                => $codigo,
                                            'hora'                  => $hora,
                                            'servicio'              => $arrResultado['servicio'],
                                            'datosCliente'          => $arrResultado['datosCliente'],
                                            'formaContactoPunto'    => $arrResultado['formaContactoPunto'],
                                            'formaContactoCliente'  => $arrResultado['formaContactoCliente'],
                                            'contactoCliente'       => $arrResultado['contactoCliente'],
                                            'elementoCpe'           => $arrResultado['elementoCpe'],
                                            'elementoOnt'           => $arrResultado['elementoOnt'],
                                            'elementoWifi'          => $arrResultado['elementoWifi'],
                                            'macCpe'                => $arrResultado['macCpe'],
                                            'macOnt'                => $arrResultado['macOnt'],
                                            'macWifi'               => $arrResultado['macWifi'],
                                            'equiposEntregado'      => $arrResultado['dataTecnica'],
                                            'facturable'            => $boolFacturable,
                                            'valorFacturable'       => $arrayParametrosSolicitudes['floatValor'],
                                            'ultimaMilla'           => $arrResultado['ultimaMilla'],
                                            'comparticion'          => "2:1",
                                            'terminosCondiciones'   => $strTerminosCondiciones,
                                            'modulo'                => "soporte",
                                            'boolFirmaDefault'      => $boolFirmaDefault,
                                            'facturacionEquipos'    => $arrayRespuestaFactEquipos['boolFacturacionEquipos'],
                                            'equiposFacturados'     => $arrayRespuestaFactEquipos['arrayEquiposFacturados'],
                                            'floatTotalEquipos'     => $arrayRespuestaFactEquipos['floatTotalEquipos'],
                                            'strRutaFisicaCompleta' => $strRutaFisicaCompleta,
                                            'strRutaFisicaArchivo'  => $strRutaFisicaArchivo,
                                            'bandNfs'               => $arrayParametros['bandNfs'],
                                            'strApp'                => $arrayParametros['strAplicacion'],
                                            'strSubModulo'          => $arrayParametros['strOrigenAccion'],
                                            'idComunicacion'        => $intIdDetalle,
                                            'strUsrCreacion'        => $usrCreacion,
                                            'arrayElementosRegis'   => $arrResultado['arrayElementosRegis']
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
                    
                    //eliminar los archivos de firmas
                    if(file_exists($strRutaFisicaArchivo.$nombreArchivoCliente.'.'.$extensionArchivo))
                    {
                        unlink($strRutaFisicaArchivo.$nombreArchivoCliente.'.'.$extensionArchivo);
                    }
                     
                    if(file_exists($strRutaFisicaArchivo.$nombreArchivoEmpleado.'.'.$extensionArchivo))
                    {
                        unlink($strRutaFisicaArchivo.$nombreArchivoEmpleado.'.'.$extensionArchivo);
                    }

                    //enviar por mail plantilla
                    $arrayPlantilla = array(
                                                'serverRoot'            => $serverRoot,
                                                'codigo'                => $codigo,
                                                'hora'                  => $hora,
                                                'punto'                 => $punto,
                                                'strModulo'             => $strModulo,
                                                'prefijoEmpresa'        => $prefijoEmpresa,
                                                'idEmpresa'             => $idEmpresa,
                                                'idServicio'            => $idServicio,
                                                'strRutaFisicaCompleta' => $strRutaFisicaCompleta,
                                                'latenciaMedia'         => $intLatenciaMedia,
                                                'paquetesEnviados'      => $intPaquetesEnviados,
                                                'paquetesRecibidos'     => $intPaquetesRecibidos,
                                                'statusPing'            => $boolStatusPing,
                                                'bandNfs'               => $arrayParametros['bandNfs']
                                            );
                    
                    $this->enviarPlantilla($arrayPlantilla);

                    $tipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')->findOneByExtensionTipoDocumento('PDF');

                    //InfoDocumento
                    $infoDocumento = new InfoDocumento();
                    $infoDocumento->setTipoDocumentoId($tipoDocumento);
                    $infoDocumento->setTipoDocumentoGeneralId(8);
                    $infoDocumento->setNombreDocumento('Acta Entrega Codigo : ' . $codigo);
                    $infoDocumento->setUbicacionLogicaDocumento('Acta_Entrega_' . $codigo . '-' . $hora . '.pdf');
                    if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'])
                    {
                        $infoDocumento->setUbicacionFisicaDocumento($strRutaFisicaCompleta);
                    }
                    else
                    {
                        $infoDocumento->setUbicacionFisicaDocumento($serverRoot . '/' .
                                                                $strRutaFisicaCompleta . '/Acta_Entrega_' .
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
                    $infoDocumentoRelacion = new InfoDocumentoRelacion();
                    $infoDocumentoRelacion->setDocumentoId($infoDocumento->getId());
                    $infoDocumentoRelacion->setModulo('SOPORTE');
                    $infoDocumentoRelacion->setCasoId($idCaso);
                    $infoDocumentoRelacion->setDetalleId($intIdDetalle);
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
    
                    $infoDocumentoRelacion->setEstado('Activo');
                    $infoDocumentoRelacion->setFeCreacion($feCreacion);
                    $infoDocumentoRelacion->setUsrCreacion($usrCreacion);
                    $this->emComunicacion->persist($infoDocumentoRelacion);
                    $this->emComunicacion->flush();

                    $mensaje = 'Acta Entrega procesada correctamente';
                    $status = "OK";
                }
                else
                {
                    throw new \Exception("Problema al procesar el archivo, intenta nuevamente");
                }
            }
            else
            {
                $status     = "ERROR";
                $mensaje    = "No se puede crear directorio.";
            }

            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->commit();
            }
            
            $this->emComunicacion->getConnection()->close();
            
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->commit();
            }
            
            $this->emComercial->getConnection()->close();
        }
        catch(\Exception $e)
        {
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
            }
            
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }

            $status             = "ERROR";
            $mensaje            = "Mensaje: ".$e->getMessage();
            $respuestaFinal     = array('status'=>$status, 'mensaje'=>$mensaje);
            return $respuestaFinal;
        }
        
        //*RESPUESTA-------------------------------------------------------------*/
        $respuestaFinal = array('status' => $status, 'mensaje' => $mensaje);
        return $respuestaFinal;
        //*----------------------------------------------------------------------*/
    }
    
    /**
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 12-11-2018
     */
    public function getParametroActaEntrega($arrayParametros)
    {
        $strNombreParametro = $arrayParametros["strNombreParametro"];
        $strModulo          = $arrayParametros["strModulo"];
        $strEmpresaCod      = $arrayParametros["strEmpresaCod"];

        //Se obtiene los elementos dependientes según el parámetro VALOR4 = 'D' y la tecnología del equipo principal.
        $objDQL                = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                      ->getDql($strNombreParametro,
                                               $strModulo,
                                               null,
                                               null,
                                               null,
                                               null,
                                               null,
                                               null,
                                               null,
                                               strval($strEmpresaCod));
        $arrayParametroDet     = $objDQL->getOneOrNullResult();
        $arrayRespuesta        = array('strTvFacturable' => $arrayParametroDet["valor1"] ? $arrayParametroDet["valor1"] : 'Facturable',
                                       'strMostrarHoras' => $arrayParametroDet["valor2"] ? $arrayParametroDet["valor2"] : 'N',
                                       'intHorasDefecto' => $arrayParametroDet["valor3"] ? $arrayParametroDet["valor3"] : '1');
        return $arrayRespuesta;
    }

    /**
     * Funcion que sirve para generar una solicitud de requerimientos de clientes.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 13-06-2017
     * @param $arrayParametros
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1
     * @since 07-09-2018
     * Se agrega el ingreso de las características HORAS SOPORTE y CARACTERISTICA_CASO según corresponda a la empresa que aplique.<br>
     * Se modifica el tipo de solicitud a facturar por SOLICITUD VISITA TECNICA
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.2
     * @since 05-03-2021 - Se añade el valor del caso a la caracteristica SOLICITUD_TAREA_CLIENTE
     * 
     * @author Jean Pierre Nazareno Martinez <jnazareno@telconet.ec>
     * @version 1.3 08/02/2022 - Se agrega validación para cuando ya exista una solicitud 
     * de visita técnica en estado pendiente no cree mas.
     * 
     */
    public function generarSolicitudDeSoporte($arrayParametros)
    {
        $arrayListCaracteristicas = array(array("strProcesoAplica"             => "CARACTERISTICA_HORAS_VISITA",
                                                "strDescripcionCaract"         => "HORAS SOPORTE",
                                                "strValor"                     => $arrayParametros["floatHorasFacturadas"],
                                                "strEstado"                    => "Facturable"),
                                          array("strProcesoAplica"             => "CARACTERISTICA_CASO",
                                                "strDescripcionCaract"         => "CASO",
                                                "strValor"                     => $arrayParametros["strNumeroCaso"],
                                                "strEstado"                    => "Activo"));
        $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                         ->findOneById($arrayParametros['intIdServicio']);
        if(is_object($objServicio))
        {
            $objMotivo       = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                               ->findOneByNombreMotivo('Solicitud al crear tarea por requerimientos de clientes');

            $intIdMotivo     = 0;
            if( $objMotivo )
            {
                $intIdMotivo = $objMotivo->getId();
            }
            $strObservacion      = 'Se crea '.self::SOLICITUD_VISITA_TECNICA;
            $objTipoSolicitud    = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                     ->findOneByDescripcionSolicitud( self::SOLICITUD_VISITA_TECNICA );

            $objDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
            ->findOneBy(array( 
                            'servicioId'        => $objServicio,
                            'tipoSolicitudId'   => $objTipoSolicitud,
                            'estado'            => self::ESTADO_PENDIENTE
                        ) 
                );
            
            if(!is_object($objDetalleSolicitud))
            {

                $objDetalleSolicitud = new InfoDetalleSolicitud();
                $objDetalleSolicitud->setEstado(self::ESTADO_PENDIENTE);
                $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitud->setUsrCreacion($arrayParametros['strUser']);
                $objDetalleSolicitud->setServicioId($objServicio);
                $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
                $objDetalleSolicitud->setMotivoId($intIdMotivo);
                $objDetalleSolicitud->setPrecioDescuento($arrayParametros['floatValor']);
                $objDetalleSolicitud->setObservacion($strObservacion);
                $this->emComercial->persist($objDetalleSolicitud);
                $this->emComercial->flush();

                $objAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica( self::CARACTERISTICA_SOLICITUD );

                $objDetalleSolCarac    = new InfoDetalleSolCaract();
                $objDetalleSolCarac->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolCarac->setEstado(self::ESTADO_ACTIVO);
                $objDetalleSolCarac->setFeCreacion(new \DateTime('now'));
                $objDetalleSolCarac->setUsrCreacion($arrayParametros['strUser']);
                $objDetalleSolCarac->setCaracteristicaId($objAdmiCaracteristica);
                $objDetalleSolCarac->setValor($arrayParametros["strNumeroCaso"]);
                $this->emComercial->persist($objDetalleSolCarac);
                $this->emComercial->flush();

                $arrayParametrosAplica["strEmpresaCod"] = $arrayParametros["strEmpresaCod"];
                foreach ($arrayListCaracteristicas as $arrayCaracteristica)
                {
                    $arrayParametrosAplica["strProcesoAccion"] = $arrayCaracteristica["strProcesoAplica"];
                    $strAplicaCaracteristica                   = $this->serviceUtil->empresaAplicaProceso($arrayParametrosAplica);
                    if ('S' == $strAplicaCaracteristica)
                    {
                        $objAdmiCaracteristica = $this->emComercial
                                                    ->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy(array("descripcionCaracteristica" => $arrayCaracteristica["strDescripcionCaract"],
                                                                        "estado"                    => "Activo"));
                        $objDetalleSolCarac = new InfoDetalleSolCaract();
                        $objDetalleSolCarac->setDetalleSolicitudId($objDetalleSolicitud);
                        $objDetalleSolCarac->setEstado($arrayCaracteristica["strEstado"]);
                        $objDetalleSolCarac->setFeCreacion(new \DateTime('now'));
                        $objDetalleSolCarac->setUsrCreacion($arrayParametros['strUser']);
                        $objDetalleSolCarac->setCaracteristicaId($objAdmiCaracteristica);
                        $objDetalleSolCarac->setValor($arrayCaracteristica["strValor"]);
                        $this->emComercial->persist($objDetalleSolCarac);
                        $this->emComercial->flush();
                    }
                }

                $objDetalleSolHist     = new InfoDetalleSolHist();
                $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolHist->setEstado(self::ESTADO_PENDIENTE);
                $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $objDetalleSolHist->setUsrCreacion($arrayParametros['strUser']);
                $objDetalleSolHist->setMotivoId($intIdMotivo);
                $objDetalleSolHist->setObservacion($strObservacion);
                $this->emComercial->persist($objDetalleSolHist);
                $this->emComercial->flush();
            }
        }
    }
    /**
     * Funcion que sirve para enviar la plantilla de la encuesta por mail al cliente
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 11-06-2015
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.1 18-01-2019 - Se valida el envío correo para Netvoice.
     * @param $arrayParametros
     *
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.2 27-02-2019 - Modificación en el asunto del correo enviado al cliente para tareas de instalación y soporte de TN.
     * @param $arrayParametros
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.3 02-06-2020 - Se modifica código para crear nueva estructura de archivos.
     *
     * @author Wilmer Vera González <wvera@telconet.ec>
     * @version 1.4 06-07-2020 - Se agrega datos de latencia para el envio de correo.
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.5 08-07-2020 - Se valida la plantilla a enviar por correo al cliente en caso de confirmar el enlace
     *                           en una tarea de soporte TN.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.6 11-11-2020 - Se envia la url del archivo almacenado en el servidor NFS remoto.
     *
     * Se reemplaza logica para extraer dato del producto, haciendo uso del ORM de SF.
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.7 28-01-2022 
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.8 21/10/2022 - Se agrega la validación para obtener el asunto y código de acta por producto.
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.9 20/03/2023 - Se agrega el asunto del correo enviado al cliente para tareas de instalación y soporte de EN.
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 2.0 05/04/2023 - Se cambia el remitente y se agrega nueva plantilla
     */
    public function enviarPlantilla($arrayParametros)
    {

        $serverRoot      = $arrayParametros['serverRoot'];
        $codigo          = $arrayParametros['codigo'];
        $hora            = $arrayParametros['hora'];
        $punto           = $arrayParametros['punto'];
        $idEmpresa       = $arrayParametros['idEmpresa'];
        $intIdServicio   = $arrayParametros['idServicio'];
        $boolStatusPing  = $arrayParametros['statusPing'];   
        $strDescTarea    = '';
        $objServicio     = null;
        if(!empty($intIdServicio) && $intIdServicio != 0 )                    
        {
            $objServicio    = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);  
            if(is_object($objServicio) && is_object($objServicio->getProductoId()))
            {
                $strDescTarea = $objServicio->getProductoId()->getDescripcionProducto();
            }
        }

        $strAsuntoCorreo = 'NETLIFE te confirma sobre tu requerimiento de instalación/soporte. Adjunto Acta de Recepción del Servicio.';
             
        $strDirDocumentos   = $serverRoot . '/'. $arrayParametros['strRutaFisicaCompleta'] . '/';
        
        $correos         = $this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')->findCorreosPorPunto($punto->getId());

        if($correos != '')
        {
            $correos = explode(",", $correos);                   

            $strArchivoAdjunto = $strDirDocumentos . 'Acta_Entrega_' . $codigo . '-' . $hora . '.pdf';
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
                $strCorreo        = 'notificaciones_telcos@telconet.ec';

                if($arrayParametros['strModulo'] == 'SOPORTE')
                {
                    if($boolStatusPing)
                    {
                        $strCodigoCorreo  = 'SOP-CLI-CORTNCE';
                    }
                    else
                    {
                        $strCodigoCorreo  = 'SOP-CLI-COR-TN';
                    }    
                    
                    $strAsuntoCorreo  = 'TELCONET, confirma su requerimiento de Soporte Técnico.  Adjunto Acta de Visita Técnica.'; 
                }
                else
                {
                    $strCodigoCorreo  = 'INS-CLI-COR-TN';
                    $strAsuntoCorreo  = 'TELCONET, confirma su requerimiento de Instalación/Activación de servicio. '
                                        . ' Adjunto Acta de Entrega de Última Milla.'; 
                }
            }
            else if($arrayParametros['prefijoEmpresa'] == 'MD')
            {
                $strCodigoCorreo  = 'ACT-ENT-CORREO';
                $strCorreo        = 'notificaciones@netlife.net.ec';
            }
            else if($arrayParametros['prefijoEmpresa'] == 'EN')
            {
                $strCodigoCorreo  = 'ACT-EN-CORREO';
                $strCorreo        = 'notificacionesecuanet@ecuanet.com.ec';
                $strAsuntoCorreo = 'ECUANET te confirma sobre tu requerimiento de instalación/soporte. Adjunto Acta de Recepción del Servicio.';
            }
            if($strDescTarea == 'NETVOICE')
            {
                $strAsuntoCorreo = 'NETVOICE te confirma sobre tu requerimiento de instalación/soporte. Adjunto Acta de Recepción del Servicio.';
                $strCodigoCorreo = 'ACT-ENT-CORR-NV';
                $strCorreo       = 'notificaciones@netvoice.ec';
            }
            //asunto de acta entrega por producto
            if(is_object($objServicio) && is_object($objServicio->getProductoId())
               && (!isset($arrayParametros['strModulo']) || $arrayParametros['strModulo'] != 'SOPORTE') )
            {
                $arrayParPlantillaCorreo = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne('CORREO_ACTA_SERVICIO_POR_PRODUCTO',
                                                                    'TECNICO',
                                                                    '',
                                                                    '',
                                                                    $objServicio->getProductoId()->getId(),
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    $idEmpresa);
                if(isset($arrayParPlantillaCorreo) && !empty($arrayParPlantillaCorreo)
                   && !empty($arrayParPlantillaCorreo['valor2']) && !empty($arrayParPlantillaCorreo['valor3']))
                {
                    $strCodigoCorreo = $arrayParPlantillaCorreo['valor2'];
                    $strAsuntoCorreo = $arrayParPlantillaCorreo['valor3']; 
                }
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
     * Funcion que sirve para generar pdf de la acta de arcotel
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 11-06-2015
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.1 02-06-2020 - Se modifica código para crear nueva estructura de archivos.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.2 12-11-2020 - Almacenar el pdf en el servidor NFS
     *
     * @param $arrayParametros
     */
    public function generarPdfArcotel($arrayParametros)
    {
        $serverRoot             = $arrayParametros['serverRoot'];
        $hora                   = $arrayParametros['Hora'];
        $HoraActual             = $arrayParametros['HoraActual'];
        $intIdDetalle           = $arrayParametros['idDetalle'];
        $modulo                 = $arrayParametros['modulo'];
        $strDirDocumentos          = $serverRoot . '/' . $arrayParametros['strRutaFisicaCompleta'] . '/';

        $strImagen              = $this->pathTelcos . 'telcos/web/public/images/logo_telconet_plantilla.jpg';
        $arrayPersonalACargo    = json_decode($arrayParametros['actaPersonalCargo'],true);
        $arrayMateriales    = json_decode($arrayParametros['materiales'],true);

        $arrayPDFCorreo = array('urlImagen'             => $strImagen,
                                'firmaImagenurl'        => $arrayParametros['firmaImagenurl'],
                                'firmaEvidenciaurl'     => $arrayParametros['firmaEvidenciaurl'],
                                'NombreCoordinador'     => $arrayParametros['NombreCoordinador'],
                                'NombreDepartamento'    => $arrayParametros['NombreDepartamento'],
                                'actaPersonalCargo'     => $arrayPersonalACargo,
                                'NombreVehiculo'        => $arrayParametros['NombreVehiculo'],
                                'Fecha'                 => $arrayParametros['Fecha'],
                                'Hora'                  => $HoraActual,
                                'Login'                 => $arrayParametros['Login'],
                                'Coordenadas'           => $arrayParametros['Coordenadas'],
                                'material'              => $arrayMateriales,
                                'direccion'             => $arrayParametros['direccion'],
                                );
        $htmlPdf = $this->container->get('templating')->render($modulo.'Bundle:Default:ActaEPP.html.twig', $arrayPDFCorreo);
        
        if($arrayParametros['bandNfs'])
        {
            $objFile                = $this->container->get('knp_snappy.pdf')->getOutputFromHtml($htmlPdf);
            $arrayPathAdicional     = null;
            $strKey                 = isset($arrayParametros['idDetalle']) ? $arrayParametros['idDetalle'] : 'SinTarea';
            $arrayPathAdicional[]   = array('key' => $strKey);
            $strNombreArchivo       = 'Acta_EPP_' . $arrayParametros['idComunicacion'] . '-' . $hora . '.pdf';
            $arrayParamNfs          = array(
                                            'prefijoEmpresa'       => $arrayParametros['prefijoEmpresa'],
                                            'strApp'               => $arrayParametros['strApp'],
                                            'strSubModulo'         => $arrayParametros['strSubModulo'],
                                            'arrayPathAdicional'   => $arrayPathAdicional,
                                            'strBase64'            => base64_encode($objFile),
                                            'strNombreArchivo'     => $strNombreArchivo,
                                            'strUsrCreacion'       => $arrayParametros['Login']);
            $arrayRespNfsPdf        = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
            if(isset($arrayRespNfsPdf) && $arrayRespNfsPdf['intStatus'] == 200)
            {
                $arrayParametrosDoc['strSrc']           = $arrayRespNfsPdf['strUrlArchivo'];
            }
            else
            {
                throw new \Exception($arrayRespNfsPdf['strMensaje'].'  -> generarPdfArcotel()');
            }
        }
        else
        {
            $this->container->get('knp_snappy.pdf')->generateFromHtml($htmlPdf, $strDirDocumentos .
                                                                  'Acta_EPP_' . $arrayParametros['idComunicacion'] . '-' .
                                                                  $hora . '.pdf');
        }
        return $arrayParametrosDoc;
    }

    /**
     * Funcion que sirve para generar pdf de la encuesta llenada por el cliente
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 11-06-2015
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.1 13-06-2017 - Se coloca el ruta fisica donde se encuentra el logo de la empresa TN, se envia nuevos parametros al momento
     *                           de generar el PDF equiposEntregado(Equipos Entregados al Cliente), facturable (Se consulta si la visita es
     *                           facturable), valorFacturable( Valor a Cancelar por el cliente sin IVA).
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.2 21-05-2018 - Se manda por default la imagen del Ing.Hugo Proaño.
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.3
     * @since 30-10-2018
     * Se agregan los parámetros facturacionEquipos, equiposFacturados, floatTotalEquipos para llenar la plantilla.
     *
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.3
     * @since 11-01-2019
     * Se agrega validación para generar el acta Netvoice en pdf.
     *
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.4
     * @since 09-07-2019
     * Se agrega parámetro  $arrayParametros['firmaTelcos'] para agregar una imagen de 'firma generada desde Telcos'
     * cuando se genera el acta desde Telcos.
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.5 02-06-2020 - Se modifica código para crear nueva estructura de archivos.
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.6 11-11-2020 - Se almacena el archivo en el servidor NFS remoto.
     *
     * Se reemplaza logica para extraer dato del producto, haciendo uso del ORM de SF.
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.7 28-01-2022 
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.8 20-03-2023 - Se agrega condicion para agregar la imagen de Ecuanet a las actas de entrega.
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
        $idCaso                 = $arrayParametros['idCaso'];
        $arrayActaEntrega       = $arrayParametros['preguntaActaEntrega'];
        $prefijoEmpresa         = $arrayParametros['prefijoEmpresa'];
        $modulo                 = $arrayParametros['modulo'];
        $strFirmaTelcos         = $arrayParametros['firmaTelcos'];
        $strDescTarea           = '';
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
        
        //buscar firmas dependiendo del modulo
        if($modulo == "tecnico")
        {
            if(!empty($strFirmaTelcos))
            {
                $strFirmaCliente    = $this->pathTelcos .$strFirmaTelcos;
            }else
            {
                $strFirmaCliente   = $strDirFirmas . $idServicio . '_cliente.png';
                $strFirmaEmpleado  = $strDirFirmas . $idServicio . '_empleado.png';
            }
        }
        else if($modulo == "soporte")
        {
            $strFirmaCliente   = $strDirFirmas . $idCaso . '_cliente.png';
            $strFirmaEmpleado  = $strDirFirmas . $idCaso . '_empleado.png';
        }
        else
        {
            $strFirmaCliente   = '';
            $strFirmaEmpleado  = '';
        }
        if(isset($arrayParametros['boolFirmaDefault']) && $arrayParametros['boolFirmaDefault'])
        {
        if($strDescTarea == 'NETVOICE')
        {
            $strFirmaEmpleado = $this->pathTelcos . 'telcos/web/public/images/firma_smc.png';
        }
        else
        {
            $strFirmaEmpleado = $this->pathTelcos . 'telcos/web/public/images/firma_telcos.jpg';
        }
        }
        $arrayPDFCorreo = array('cuerpo'                => $arrayActaEntrega, 
                                'materiales'            => $arrayParametros['materiales'],
                                'totalMateriales'       => $arrayParametros['totalMateriales'],
                                'equiposEntregado'      => $arrayParametros['equiposEntregado'],
                                'facturable'            => $arrayParametros['facturable'],
                                'valorFacturable'       => $arrayParametros['valorFacturable'],
                                'firmaCliente'          => $strFirmaCliente, 
                                'firmaEmpleado'         => $strFirmaEmpleado,
                                'fecha'                 => $arrayParametros['fecha'],
                                'nombreProducto'        => $arrayParametros['nombreProducto'],
                                'servicio'              => $arrayParametros['servicio'],
                                'datosCliente'          => $arrayParametros['datosCliente'],
                                'formaContactoPunto'    => $arrayParametros['formaContactoPunto'],
                                'formaContactoCliente'  => $arrayParametros['formaContactoCliente'],
                                'contactoCliente'       => $arrayParametros['contactoCliente'],
                                'elementoCpe'           => $arrayParametros['elementoCpe'],
                                'elementoOnt'           => $arrayParametros['elementoOnt'],
                                'elementoWifi'          => $arrayParametros['elementoWifi'],
                                'macCpe'                => $arrayParametros['macCpe'],
                                'macOnt'                => $arrayParametros['macOnt'],
                                'macWifi'               => $arrayParametros['macWifi'],
                                'ultimaMilla'           => $arrayParametros['ultimaMilla'],
                                'comparticion'          => $arrayParametros['comparticion'],
                                'terminosCondiciones'   => $arrayParametros['terminosCondiciones'],
                                'firmaEmpresa'          => $arrayParametros['boolFirmaDefault'],
                                'imagenCabecera'        => $imagen,
                                'facturacionEquipos'    => $arrayParametros['facturacionEquipos'],
                                'equiposFacturados'     => $arrayParametros['equiposFacturados'],
                                'floatTotalEquipos'     => $arrayParametros['floatTotalEquipos'],
                                'arrayElementosRegis'   => $arrayParametros['arrayElementosRegis']);
        $htmlPdf = $this->container->get('templating')->render($modulo.'Bundle:Default:actaEntrega.html.twig', $arrayPDFCorreo);

        $strResult = $strDirDocumentos . 'Acta_Entrega_' . $codigo . '-' . $hora . '.pdf';
        if($arrayParametros['bandNfs'])
        {
            $objFile                = $this->container->get('knp_snappy.pdf')->getOutputFromHtml($htmlPdf);
            $arrayPathAdicional     = null;
            $strKey                 = isset($arrayParametros['idComunicacion']) ? $arrayParametros['idComunicacion'] : 'SinTarea';
            $arrayPathAdicional[]   = array('key' => $strKey);
            $strNombreArchivo       = 'Acta_Entrega_' . $codigo . '-' . $hora . '.pdf';
            $arrayParamNfs          = array(
                                            'prefijoEmpresa'       => $arrayParametros['prefijoEmpresa'],
                                            'strApp'               => $arrayParametros['strApp'],
                                            'strSubModulo'         => $arrayParametros['strSubModulo'],
                                            'arrayPathAdicional'   => $arrayPathAdicional,
                                            'strBase64'            => base64_encode($objFile),
                                            'strNombreArchivo'     => $strNombreArchivo,
                                            'strUsrCreacion'       => $arrayParametros['strUsrCreacion']);
            $arrayRespNfsPdf        = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
            if(isset($arrayRespNfsPdf) && $arrayRespNfsPdf['intStatus'] == 200)
            {
                $strResult = $arrayRespNfsPdf['strUrlArchivo'];
            }
            else
            {
                throw new \Exception($arrayRespNfsPdf['strMensaje'].'  -> generarPdf()');
            }
        }
        else
        {
            $this->container->get('knp_snappy.pdf')->generateFromHtml($htmlPdf, $strResult);
        }
       
        return $strResult;

    }

    /**
     * Funcion que sirve para generar pdf del reporte fotográfico para el cliente.
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 14-10-2022
     * 
     * @param $arrayParametros
     */
    public function generarReporteFotografico($arrayParametros)
    {
        $strServerRoot          = $arrayParametros['serverRoot'];
        $strCodigo              = $arrayParametros['codigo'];
        $strHora                = $arrayParametros['hora'];
        $intIdServicio          = $arrayParametros['idServicio'];
        $strModulo              = $arrayParametros['modulo'];
        $intIdCaso              = $arrayParametros['idCaso'];
        $strFirmaTelcos         = $arrayParametros['firmaTelcos'];
        $strPrefijoEmpresa      = $arrayParametros['prefijoEmpresa'];
        $strDirFirmas           = $strServerRoot.'/'.$arrayParametros['strRutaFisicaArchivo'];
        $strDirDocumentos       = $strServerRoot.'/'.$arrayParametros['strRutaFisicaCompleta'].'/';
        $strDescTarea           = "";

        try
        {
            //Se genera documento fisico del PDF relacionado a la plantilla
            $arrayParPlantilla  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('PARAMETROS_REPORTE_FOTOGRAFICO',
                                                                'TECNICO',
                                                                '',
                                                                '',
                                                                'CODIGO_PLANTILLA',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                $arrayParametros['idEmpresa']);
            if(isset($arrayParPlantilla) && !empty($arrayParPlantilla))
            {
                $objPlantilla = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                                            ->findOneByCodigo($arrayParPlantilla['valor2']);
                $strHtml      = is_object($objPlantilla)?$objPlantilla->getPlantilla():"";
                if(!empty($strHtml))
                {
                    $objArchivo = fopen($arrayParametros['pathSource'] . '/Resources/views/Default/reporteFotografico.html.twig', "w");
                    if($objArchivo)
                    {  
                        fwrite($objArchivo, $strHtml);
                        fclose($objArchivo);
                    }
                    else
                    {
                        throw new \Exception("Problema al procesar el archivo de reporte fotográfico (Permisos), intenta nuevamente");
                    }
                }
            }
            //obtener las imagenes
            $arrayEtiquetasFotos = array();
            $arrayParEtiquetas   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('PARAMETROS_REPORTE_FOTOGRAFICO',
                                                              'TECNICO',
                                                              '',
                                                              '',
                                                              'REPORTE_IMAGENES',
                                                              $arrayParametros['idProducto'],
                                                              '',
                                                              '',
                                                              '',
                                                              $arrayParametros['idEmpresa'],
                                                              'valor5');
            foreach($arrayParEtiquetas as $intKey => $arrayItemEtiquetas)
            {
                $arrayEtiquetasFotos[$intKey]["nombre"]    = $arrayItemEtiquetas['valor4'];
                $arrayEtiquetasFotos[$intKey]["ubicacion"] = "";
                //obtengo el documento
                $objDocumento = $this->emComunicacion->getRepository('schemaBundle:InfoDocumento')
                                            ->createQueryBuilder('t')
                                            ->innerJoin('schemaBundle:InfoDocumentoRelacion', 'rel', 'WITH', 'rel.documentoId = t.id')
                                            ->where("t.nombreDocumento LIKE :nombreDocumento")
                                            ->andWhere("rel.detalleId  = :detalleId")
                                            ->andWhere("t.estado   = :estado")
                                            ->andWhere("rel.estado = :estado")
                                            ->setParameter('nombreDocumento', $arrayItemEtiquetas['valor3']."%")
                                            ->setParameter('detalleId',       $arrayParametros['idComunicacion'])
                                            ->setParameter('estado',          'Activo')
                                            ->setMaxResults(1)
                                            ->getQuery()
                                            ->getOneOrNullResult();
                if(is_object($objDocumento))
                {
                    $arrayEtiquetasFotos[$intKey]["ubicacion"] = $objDocumento->getUbicacionFisicaDocumento();
                }
            }
            //obtener logo
            if($strPrefijoEmpresa == 'MD')
            {
                $strImagen = $this->pathTelcos . 'telcos/web/public/images/logo_netlife_big.jpg';    
            }
            else if($strPrefijoEmpresa == 'TN')
            {
                $strImagen = $this->pathTelcos . 'telcos/web/public/images/logo_telconet_plantilla.jpg';
            }
            else if($strPrefijoEmpresa == 'TTCO')
            {
                $strImagen = $this->pathTelcos . 'telcos/web/public/images/logo_transtelco_new.jpg';
            }
            if(!empty($intIdServicio) && $intIdServicio != 0 )
            {
                $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
                if(is_object($objServicio) && is_object($objServicio->getProductoId()))
                {
                    $strDescTarea = $objServicio->getProductoId()->getDescripcionProducto();
                }
            }
            if($strDescTarea == 'NETVOICE')
            {
                $strImagen = $this->pathTelcos . 'telcos/web/public/images/logo_netvoice.png';
            }
            //buscar firmas dependiendo del modulo
            if($strModulo == "tecnico")
            {
                if(!empty($strFirmaTelcos))
                {
                    $strFirmaCliente  = $this->pathTelcos.$strFirmaTelcos;
                }
                else
                {
                    $strFirmaCliente  = $strDirFirmas . $intIdServicio . '_cliente.png';
                    $strFirmaEmpleado = $strDirFirmas . $intIdServicio . '_empleado.png';
                }
            }
            else if($strModulo == "soporte")
            {
                $strFirmaCliente  = $strDirFirmas . $intIdCaso . '_cliente.png';
                $strFirmaEmpleado = $strDirFirmas . $intIdCaso . '_empleado.png';
            }
            else
            {
                $strFirmaCliente  = '';
                $strFirmaEmpleado = '';
            }
            if(isset($arrayParametros['boolFirmaDefault']) && $arrayParametros['boolFirmaDefault'])
            {
                if($strDescTarea == 'NETVOICE')
                {
                    $strFirmaEmpleado = $this->pathTelcos . 'telcos/web/public/images/firma_smc.png';
                }
                else
                {
                    $strFirmaEmpleado = $this->pathTelcos . 'telcos/web/public/images/firma_telcos.jpg';
                }
            }
            //generar pdf
            $arrayPDFCorreo = array('firmaCliente'          => $strFirmaCliente, 
                                    'firmaEmpleado'         => $strFirmaEmpleado,
                                    'imagenCabecera'        => $strImagen,
                                    'fecha'                 => $arrayParametros['fecha'],
                                    'datosCliente'          => $arrayParametros['datosCliente'],
                                    'contactoCliente'       => $arrayParametros['contactoCliente'],
                                    'formaContactoCliente'  => $arrayParametros['formaContactoCliente'],
                                    'arrayEtiquetasFotos'   => $arrayEtiquetasFotos);
            $strHtmlPdf = $this->container->get('templating')->render('tecnicoBundle:Default:reporteFotografico.html.twig', $arrayPDFCorreo);
            $strRutaArchivo = $strDirDocumentos . "Reporte_Fotografico_" . $strCodigo . '-' . $strHora . '.pdf';
            if($arrayParametros['bandNfs'])
            {
                $objFile                = $this->container->get('knp_snappy.pdf')->getOutputFromHtml($strHtmlPdf);
                $arrayPathAdicional     = null;
                $strKey                 = isset($arrayParametros['idComunicacion']) ? $arrayParametros['idComunicacion'] : 'SinTarea';
                $arrayPathAdicional[]   = array('key' => $strKey);
                $strNombreArchivo       = "Reporte_Fotografico_" . $strCodigo . '-' . $strHora . '.pdf';
                $arrayParamNfs          = array(
                                                'prefijoEmpresa'       => $arrayParametros['prefijoEmpresa'],
                                                'strApp'               => $arrayParametros['strApp'],
                                                'strSubModulo'         => $arrayParametros['strSubModulo'],
                                                'arrayPathAdicional'   => $arrayPathAdicional,
                                                'strBase64'            => base64_encode($objFile),
                                                'strNombreArchivo'     => $strNombreArchivo,
                                                'strUsrCreacion'       => $arrayParametros['strUsrCreacion']);
                $arrayRespNfsPdf        = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
                if(isset($arrayRespNfsPdf) && $arrayRespNfsPdf['intStatus'] == 200)
                {
                    $strRutaArchivo = $arrayRespNfsPdf['strUrlArchivo'];
                }
                else
                {
                    throw new \Exception($arrayRespNfsPdf['strMensaje'].'  -> generarPdf()');
                }
            }
            else
            {
                $this->container->get('knp_snappy.pdf')->generateFromHtml($strHtmlPdf, $strRutaArchivo);
                if(!file_exists($strRutaArchivo))
                {
                    throw new \Exception("Problema al crear el archivo PDF en el directorio, intenta nuevamente");    
                }
            }
            //obtener tipo documento
            $objAdmiTipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                                                    ->findOneByExtensionTipoDocumento('PDF');
            if(!is_object($objAdmiTipoDocumento))
            {
                throw new \Exception("Problema al procesar el archivo. ".
                                     "Objeto tipo documento PDF no se ha encontrado, intente nuevamente");
            }
            //obtener tipo documento general
            $objTipoDocumentoGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                    ->findOneBy(array('descripcionTipoDocumento' => 'IMAGENES',
                                                                      'estado'                   => 'Activo'));
            if(!is_object($objTipoDocumentoGeneral))
            {
                throw new \Exception("Problema al procesar el archivo. ".
                                     "Objeto tipo documento general IMAGENES no se ha encontrado, intente nuevamente");
            }
            //guardar info documento
            $objInfoDocumento = new InfoDocumento();
            $objInfoDocumento->setTipoDocumentoId($objAdmiTipoDocumento);
            $objInfoDocumento->setTipoDocumentoGeneralId($objTipoDocumentoGeneral->getId());
            $objInfoDocumento->setNombreDocumento("Reporte Fotografico");
            $objInfoDocumento->setUbicacionLogicaDocumento('Reporte_Fotografico_' . $strCodigo . '-' . $strHora . '.pdf');
            $objInfoDocumento->setUbicacionFisicaDocumento($strRutaArchivo);
            $objInfoDocumento->setEstado('Activo');
            $objInfoDocumento->setEmpresaCod($arrayParametros['idEmpresa']);
            $objInfoDocumento->setFechaDocumento($arrayParametros['strFeCreacion']);
            $objInfoDocumento->setUsrCreacion($arrayParametros['strUsrCreacion']);
            $objInfoDocumento->setFeCreacion($arrayParametros['strFeCreacion']);
            $objInfoDocumento->setIpCreacion($arrayParametros['strIpCreacion']);
            $this->emComunicacion->persist($objInfoDocumento);
            $this->emComunicacion->flush();
            if(!is_object($objInfoDocumento))
            {
                throw new \Exception("Problema al procesar el archivo. Objeto documento no se ha guardado correctamente, intente nuevamente");
            }
            //guardar info documento relacion
            $objInfoDocumentoRelacion = new InfoDocumentoRelacion();
            $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());
            $objInfoDocumentoRelacion->setModulo('TECNICO');
            $objInfoDocumentoRelacion->setServicioId($arrayParametros['idServicio']);
            $objInfoDocumentoRelacion->setPuntoId($arrayParametros['objPunto']->getId());
            $objInfoDocumentoRelacion->setPersonaEmpresaRolId($arrayParametros['objPunto']->getPersonaEmpresaRolId()->getId());
            $objInfoDocumentoRelacion->setDetalleId($arrayParametros['idComunicacion']);
            $objInfoDocumentoRelacion->setEstado('Activo');
            $objInfoDocumentoRelacion->setFeCreacion($arrayParametros['strFeCreacion']);
            $objInfoDocumentoRelacion->setUsrCreacion($arrayParametros['strUsrCreacion']);
            $this->emComunicacion->persist($objInfoDocumentoRelacion);
            $this->emComunicacion->flush();
            //setear respuesta
            $arrayRespuesta = array('status'=>"OK", 'mensaje'=>"Reporte Fotografico procesado correctamente", "rutaArchivo"=>$strRutaArchivo);
        }
        catch(\Exception $ex)
        {
            $arrayRespuesta = array('status'=>"ERROR", 'mensaje'=>$ex->getMessage());
            $this->serviceUtil->insertError('Telcos+',
                                            'ActaEntregaService.generarReporteFotografico',
                                            $ex->getMessage(),
                                            $arrayParametros['strUsrCreacion'],
                                            '127.0.0.1');
        }
        return $arrayRespuesta;
    }
}
