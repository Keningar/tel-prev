<?php

namespace telconet\comercialBundle\Service;
use Doctrine\ORM\EntityManager;
use telconet\schemaBundle\Service\UtilService;
use telconet\schemaBundle\Entity\ReturnResponse;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\InfoPunto;
use telconet\schemaBundle\Entity\InfoPuntoContacto;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoContrato;
use telconet\comercialBundle\Service\InfoPuntoService;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use \telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;


class ComercialCrmCmService
{
    private $emGeneral;
    private $emComercial;
    private $emFinanciero;
    private $emBiFinanciero;
    private $serviceUtil;
    private $serviceTecnico;
    private $serviceInfoPunto;
    private $serviceInfoServicio;
    private $serviceInfoContrato;
    private $serviceSoporteServicio;
    private $restClient;


    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer )
    {
        $this->emComercial           = $objContainer->get('doctrine.orm.telconet_entity_manager');
        $this->emGeneral             = $objContainer->get('doctrine.orm.telconet_general_entity_manager');
        $this->emFinanciero          = $objContainer->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->emBiFinanciero        = $objContainer->get('doctrine.orm.telconet_bifinanciero_entity_manager');
        $this->serviceUtil           = $objContainer->get('schema.Util');
        $this->serviceTecnico        = $objContainer->get('tecnico.InfoServicioTecnico');
        $this->serviceInfoPunto      = $objContainer->get('comercial.InfoPunto');
        $this->serviceInfoServicio   = $objContainer->get('comercial.InfoServicio');
        $this->serviceInfoContrato   = $objContainer->get('comercial.InfoContrato');
        $this->serviceSoporteServicio= $objContainer->get('tecnico.InfoCambiarPlan');
        $this->strApiNafUser         = $objContainer->getParameter('telcoNaf_user');
        $this->strApiNafPass         = $objContainer->getParameter('telcoNaf_pass');
        $this->strApiNafUrl          = $objContainer->getParameter('telcoNaf_url');
        $this->restClient            = $objContainer->get('schema.RestClient');
    }
    
    /**
     * Documentación para la función flujoServicioCm
     *
     *
     * @param array $arrayParametros [
     *                                 Identificacion     => Identificación del cliente.
     *                                 NombrePunto        => Nombre o codigo con el que se registrara el punto, usado para futuros servicios.
     *                                 Empresa            => Empresa sobre la cual se creara el Punto.
     *                                 Telefono           => Teléfono de contacto para el punto.
     *                                 Direccion          => Dirección del Punto.
     *                                 Correo             => Correo para contacto del punto.
     *                                 UsrCreacion        => Usuario que creo el punto, servicio y contactos para seguimiento.
     *                                 IpCreacion         => Ip de la maquina que envia a realizar el registro.
     *                                 PuntoCobertura     => Dato necesario para la creación del punto.
     *                                 Canton             => Dato necesario para la creación del punto.
     *                                 Parroquia          => Dato necesario para la creación del punto.
     *                                 Sector             => Dato necesario para la creación del punto.
     *                                 TipoNegocio        => La naturaleza del negocio.
     *                                 Oficina            => Lugar desde donde se realiza la creación.
     *                                 LoginVendedor      => Login del vendedor que realizo la venta.
     *                                 DescripcionPunto   => Descripción del punto que contrata.
     *                                 Observacion        => Algun dato adicional del punto.
     *                                 DependeEdificio    => Se encuentra dentro de un edificio.
     *                                 Contactos          [
     *                                                    Nombre        => Nombre de la persona a contactar.
     *                                                    Apellido      => Apellido de la persona a contactar.
     *                                                    TiposContacto => El tipo de contacto de la persona.
     *                                                    FormasContacto[
     *                                                                  Tipo   => Medio por el que se puede contactar.
     *                                                                  Valor  => Datos del contacto.
     *                                                                  ]
     *                                                    ]
     *                                 Servicios          [
     *                                                    NombreProducto        => Nombre del producto como se encuentra en la base de Telcos.
     *                                                    Cantidad              => Cantidad de servicios de ese producto.
     *                                                    Precio                => Valor al que realizan la venta.
     *                                                    FrecuenciaFacturacion => Cada que tiempo se factura.
     *                                                    CaracteristicaProducto[
     *                                                                          nombre => Nombre de la caracteristica.
     *                                                                          valor  => Dato de la caracteristica.
     *                                                                          ]
     *                                                    ]
     *                               ]
     *
     * @return array $arrayResultado [
     *                                  'login' ,
                                        'PuntoId',
                                        'cod',
                                        'error'
     *                               ]
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 18-08-2020
     *
     */
    public function flujoServicioCm($arrayParametros)
    {
        $strIdentificacion       = $arrayParametros['Identificacion'] ? $arrayParametros['Identificacion']:"";
        $strNombrePunto          = $arrayParametros['NombrePunto'] ? $arrayParametros['NombrePunto']:"";
        $strEmpresa              = ( isset($arrayParametros['Empresa'])) ? $arrayParametros['Empresa']:"";
        $strTelefono             = $arrayParametros['Telefono'];
        $strDireccion            = $arrayParametros['Direccion'];
        $strCorreo               = $arrayParametros['Correo'];
        $strUsrCreacion          = ( isset($arrayParametros['UsrCreacion']) && !empty($arrayParametros['UsrCreacion']) )
                                    ? $arrayParametros['UsrCreacion'] : 'TELCOS +';
        $strIpCreacion           = ( isset($arrayParametros['IpCreacion']) && !empty($arrayParametros['IpCreacion']) )
                                    ? $arrayParametros['IpCreacion'] : '127.0.0.1';
        $strPuntoCobertura       = $arrayParametros['PuntoCobertura'] ? $arrayParametros['PuntoCobertura']:"";
        $strCanton               = $arrayParametros['Canton'] ? $arrayParametros['Canton']:"";
        $strParroquia            = $arrayParametros['Parroquia'] ? $arrayParametros['Parroquia']:"";
        $strSectorId             = $arrayParametros['Sector'] ? $arrayParametros['Sector']:"";
        $strTipoNegocio          = $arrayParametros['TipoNegocio'] ? $arrayParametros['TipoNegocio']:"";
        $strTipoUbicacion        = $arrayParametros['TipoUbicacion'] ? $arrayParametros['TipoUbicacion']:"";
        $strOficina              = $arrayParametros['Oficina'] ? $arrayParametros['Oficina']:"";
        $strLoginVendedor        = $arrayParametros['LoginVendedor'] ? $arrayParametros['LoginVendedor']:"";
        $strDescripcionPunto     = $arrayParametros['DescripcionPunto'] ? $arrayParametros['descripcionPunto']:"Na";
        $strObservacion          = $arrayParametros['Observacion'] ? $arrayParametros['observacion']:"Na";
        $strDependeEdificio      = $arrayParametros['DependeEdificio'] ? $arrayParametros['dependeEdificio']:"N";
        $strUltimaMilla          = $arrayParametros['UltimaMilla'] ? $arrayParametros['UltimaMilla']:"";
        $strMensaje              = '';
        $intCodEmpresa           = '';
        $strPrefijoEmpresa       = '';
        $intCantidadPuntos       = 0;
        $strNombrePais           = '';
        $strRol                  = '';
        $strCod                  = 0;



        $this->emComercial->getConnection()->beginTransaction();

        try
        {
            $arrayParametrosValor = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                       ->getOne('DATOS_CREACION_CM', 
                                                                                'COMERCIAL', 
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                           '10');
            if (is_array($arrayParametrosValor) && !empty($arrayParametrosValor))
            {
                $intCodEmpresa      = $arrayParametrosValor['valor1'];
                $strPrefijoEmpresa  = $arrayParametrosValor['valor2'];
                $strNombrePais      = $arrayParametrosValor['valor3'];
                $strRol             = $arrayParametrosValor['valor4'];
            }
            else
            {
                throw new \Exception('Error al obtener parámetros de registro, Favor comunicarse con Sistemas.');
            }
            if(empty($strTelefono))
            {
                throw new \Exception('Favor Ingresar un Numero de Teléfono para el registro');
            }
            if(empty($strDireccion))
            {
                throw new \Exception('Favor Ingresar una Dirección para el registro');
            }
            if(empty($strCorreo))
            {
                throw new \Exception('Favor Ingresar un Correo para el registro');
            }
            if(empty($strPuntoCobertura))
            {
                throw new \Exception('Favor Ingresar el Punto de cobertura');
            }
            else
            {
                $objPuntoCobertura = $this->emGeneral->getRepository('schemaBundle:AdmiJurisdiccion')->
                                                                                        findOneBy(array('nombreJurisdiccion'=>$strPuntoCobertura));
                
                if(is_object($objPuntoCobertura) && !empty($objPuntoCobertura))
                {
                    $intCoberturaId = $objPuntoCobertura->getId();
                }
                else
                {
                    throw new \Exception('No se encuentra la Jurisdicción Ingresada');
                }
            }
            if(empty($strCanton))
            {
                throw new \Exception('Favor Ingresar el Cantón');
            }
            else
            {
                $objCanton = $this->emGeneral->getRepository('schemaBundle:AdmiCanton')->
                                                                                        findOneBy(array('nombreCanton'=>$strCanton));
                
                if(is_object($objCanton) && !empty($objCanton))
                {
                    $intCantonId = $objCanton->getId();
                }
                else
                {
                    throw new \Exception('No se encuentra el Cantón Proporcionado');
                }
            }
            if(empty($strParroquia))
            {
                throw new \Exception('Favor Ingresar la Parroquia');
            }
            else
            {
                $objParroquia = $this->emGeneral->getRepository('schemaBundle:AdmiParroquia')->
                                                                                        findOneBy(array('nombreParroquia'=>$strParroquia));
                
                if(is_object($objParroquia) && !empty($objParroquia))
                {
                    $intParroquiaId = $objParroquia->getId();
                }
                else
                {
                    throw new \Exception('No se encuentra la Parroquia Proporcionada');
                }
            }
            if(empty($strSectorId))
            {
                throw new \Exception('Favor Ingresar el Sector');
            }
            else
            {
                $objSector = $this->emGeneral->getRepository('schemaBundle:AdmiSector')->
                                                                                        findOneBy(array('nombreSector'=>$strSectorId,
                                                                                                        'empresaCod' => $intCodEmpresa));
                
                if(is_object($objSector) && !empty($objSector))
                {
                    $intSectorId = $objSector->getId();
                }
                else
                {
                    throw new \Exception('No se encuentra el Sector Proporcionado');
                }
            }
            if(empty($strTipoNegocio))
            {
                throw new \Exception('Favor Ingresar el Tipo de Negocio Asociado');
            }
            else
            {
                $objTipoNegocio = $this->emComercial->getRepository('schemaBundle:AdmiTipoNegocio')->
                                                                                        findOneBy(array('nombreTipoNegocio'=>$strTipoNegocio));
                
                if(is_object($objTipoNegocio) && !empty($objTipoNegocio))
                {
                    $intTipoNegocioId = $objTipoNegocio->getId();
                }
                else
                {
                    throw new \Exception('No se encuentra el Tipo Negocio Proporcionado');
                }
            }
            if(empty($strTipoUbicacion))
            {
                throw new \Exception('Favor Ingresar el Tipo de Ubicación');
            }
            else
            {
                $objTipoUbicacion = $this->emComercial->getRepository('schemaBundle:AdmiTipoUbicacion')->
                                                                                   findOneBy(array('descripcionTipoUbicacion'=>$strTipoUbicacion));
                
                if(is_object($objTipoUbicacion) && !empty($objTipoUbicacion))
                {
                    $intTipoUbicacionId = $objTipoUbicacion->getId();
                }
                else
                {
                    throw new \Exception('No se encuentra el Tipo Ubicacion Proporcionado');
                }
            }
            if(empty($strOficina))
            {
                throw new \Exception('Favor Ingresar el Tipo de Ubicación');
            }
            else
            {
                $objOficinaGrupo = $this->emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->
                                                                                   findOneBy(array('nombreOficina'=>$strOficina));
                
                if(is_object($objOficinaGrupo) && !empty($objOficinaGrupo))
                {
                    $intOficinaId = $objOficinaGrupo->getId();
                }
                else
                {
                    throw new \Exception('No se encuentra la Oficina Proporcionado');
                }
            }
            if(empty($strEmpresa) && empty($strIdentificacion))
            {
                throw new \Exception('Favor Ingresar la empresa o identificación');
            }
            else
            {
                if(!empty($strIdentificacion))
                {
                    $arrayParametrosValor = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                       ->getOne('CLIENTE_CAJAMARCA', 
                                                                                'COMERCIAL', 
                                                                                '',
                                                                                '',
                                                                                $strIdentificacion,
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                           $intCodEmpresa);
                    if(is_array($arrayParametrosValor) && !empty($arrayParametrosValor))
                    {
                        $objInfoPersona = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                                ->findOneBy( array('identificacionCliente' => $arrayParametrosValor['valor1']));
                    }
                    else
                    {
                        throw new \Exception('La Empresa no esta habilitada para realizar el flujo, Favor verificar la identificación.');
                    }
                }
                else
                {
                    throw new \Exception('Favor Ingresar la identificación');
                }
                 
            }

            if(empty($strNombrePunto))
            {
                throw new \Exception('Favor Ingresar un Nombre para el Punto');
            }
            else
            {
                $objEntityPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')->
                                                                                   findOneBy(array('nombrePunto'=>$strNombrePunto));
                
                if(is_object($objEntityPunto) && !empty($objEntityPunto))
                {
                    $strNuevoPunto = 'N';
                }
                else
                {
                    $strNuevoPunto = 'S';
                }
            }
            
            if($strNuevoPunto == 'S')
            {            
                $strLogin = $this->serviceInfoPunto->generarLoginPrv($objInfoPersona,'');
                $strLogin = $strLogin.'-'.$strNombrePunto;
                $strLogin2 = $strLogin;
                do
                {
                    $strExiste = $this->serviceInfoPunto->validarLogin($strLogin2);
                
                    if($strExiste=='si')
                    {
                        $intCantidadPuntos++;
                        $strLogin2=$strLogin.$intCantidadPuntos;
                    }
                }
                while($strExiste=='si');
                $strLogin = $strLogin2;
                $arrayDatosForm = array(
                                        'tipoUbicacionId'       =>  $intTipoUbicacionId,
                                        'tipoNegocioId'         =>  $intTipoNegocioId,
                                        'telefonoDatoEnvio'     =>  $strTelefono,
                                        'strNombrePais'         =>  $strNombrePais,
                                        'sectorId'              =>  $intSectorId,
                                        'rol'                   =>  $strRol,
                                        'ptoCoberturaId'        =>  $intCoberturaId	,
                                        'prefijoEmpresa'        =>  $strPrefijoEmpresa,	
                                        'personaId'             =>  $objInfoPersona,	
                                        'parroquia'             =>	$intParroquiaId,	
                                        'origen_web'            =>	"N",
                                        'oficina'               =>	$intOficinaId,	
                                        'observacion'           =>	$strObservacion,
                                        'nombreDatoEnvio'       =>	"",	
                                        'longitudFloat'         =>	"",
                                        'login'                 =>	$strLogin,	
                                        'latitudFloat'          =>	"",	
                                        'intIdPais'             =>	1,	
                                        'formas_contacto'       =>	"0,Correo Electronico,".$strCorreo,	
                                        'esPadreFacturacion'	=>	"N",	
                                        'direccion'             =>	$strDireccion,	
                                        'direccionDatoEnvio'	=>	$strDireccion,	
                                        'descripcionpunto'      =>	$strDescripcionPunto,	
                                        'dependedeedificio'     =>	$strDependeEdificio	,
                                        'correoElectronicoDatoEnvio'	=>	$strCorreo	,
                                        'loginVendedor'         =>	$strLoginVendedor	,
                                        'cantonId'              =>	$intCantonId	,
                                        'nombrepunto'           =>  $strNombrePunto
                                        
                );
                
                $arrayParametrosPunto =  array('strCodEmpresa'        => $intCodEmpresa,
                                               'strUsrCreacion'       => $strUsrCreacion,
                                               'strClientIp'          => $strIpCreacion,
                                               'arrayDatosForm'       => $arrayDatosForm,
                                               'arrayFormasContacto'  => null);

                $objEntityPunto = $this->serviceInfoPunto->crearPunto($arrayParametrosPunto);
                $strMensaje = 'Punto creado con login; '.$objEntityPunto->getLogin().'. ';
            }
            //Creamos los contactos
            if(is_array($arrayParametros['Contactos']))
            {
                foreach($arrayParametros['Contactos'] as $arrayContactos)
                {
                    $strNombreContacto      = $arrayContactos['Nombre'];
                    $strApellidoContacto    = $arrayContactos['Apellido'];
                    $strTipoContacto        = $arrayContactos['TipoContacto'];
                    if(!empty($strNombreContacto) && !empty($strApellidoContacto) && !empty($strTipoContacto))
                    {
                        $entityInfoPersona = new InfoPersona();
                        $entityInfoPersona->setNombres(trim($strNombreContacto));
                        $entityInfoPersona->setApellidos(trim($strApellidoContacto));
                        $entityInfoPersona->setOrigenProspecto('N');
                        $entityInfoPersona->setFeCreacion(new \DateTime('now'));
                        $entityInfoPersona->setUsrCreacion($strUsrCreacion);
                        $entityInfoPersona->setIpCreacion($strIpCreacion);
                        $entityInfoPersona->setEstado('Activo');
                        $this->emComercial->persist($entityInfoPersona);
                    }
                    if(!empty($strTipoContacto))
                    {
                        $objTipoRol = $this->emComercial->getRepository('schemaBundle:AdmiTipoRol')->
                                                                                   findOneBy(array('descripcionTipoRol'=>'Contacto',
                                                                                                   'estado' => 'Activo'));
                        if(is_object($objTipoRol) && !empty($objTipoRol))
                        {
                            $objRol = $this->emComercial->getRepository('schemaBundle:AdmiRol')->
                                                                                   findOneBy(array('descripcionRol'=>$strTipoContacto,
                                                                                                   'tipoRolId' => $objTipoRol->getId()));
                        }
                        
                        if(is_object($objRol) && !empty($objRol))
                        {
                           $objInfoEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoEmpresaRol')->
                                                                                   findOneBy(array('rolId' => $objRol->getId(),
                                                                                                   'empresaCod' => $intCodEmpresa)); 
                           
                           if(is_object($objInfoEmpresaRol) && !empty($objInfoEmpresaRol))
                           {
                                $entityPersonaEmpresaRol = new InfoPersonaEmpresaRol();
                                $entityPersonaEmpresaRol->setEmpresaRolId($objInfoEmpresaRol);
                                $entityPersonaEmpresaRol->setPersonaId($entityInfoPersona);
                                $entityPersonaEmpresaRol->setOficinaId($objOficinaGrupo);
                                $entityPersonaEmpresaRol->setFeCreacion(new \DateTime('now'));
                                $entityPersonaEmpresaRol->setUsrCreacion($strUsrCreacion);
                                $entityPersonaEmpresaRol->setEstado('Activo');
                                $this->emComercial->persist($entityPersonaEmpresaRol);

                                $entityPersonaEmpresaRolHist = new InfoPersonaEmpresaRolHisto();
                                $entityPersonaEmpresaRolHist->setEstado($entityInfoPersona->getEstado());
                                $entityPersonaEmpresaRolHist->setFeCreacion(new \DateTime('now'));
                                $entityPersonaEmpresaRolHist->setIpCreacion($strIpCreacion);
                                $entityPersonaEmpresaRolHist->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                                $entityPersonaEmpresaRolHist->setUsrCreacion($strUsrCreacion);
                                $this->emComercial->persist($entityPersonaEmpresaRolHist);

                                $entityPuntoContacto = new InfoPuntoContacto();
                                $entityPuntoContacto->setPuntoId($objEntityPunto);
                                $entityPuntoContacto->setContactoId($entityInfoPersona);
                                $entityPuntoContacto->setFeCreacion(new \DateTime('now'));
                                $entityPuntoContacto->setUsrCreacion($strUsrCreacion);
                                $entityPuntoContacto->setIpCreacion($strIpCreacion);
                                $entityPuntoContacto->setEstado('Activo');
                                $entityPuntoContacto->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                                $this->emComercial->persist($entityPuntoContacto);
                           }
                        }
                    }
                    foreach($arrayContactos['FormasContacto'] as $arrayFormaContacto)
                    {
                        $objAdmiFormaContacto = $this->emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                            ->findOneBy(array('descripcionFormaContacto' => $arrayFormaContacto['Tipo']));
                        
                        if(is_object($objAdmiFormaContacto) && !empty($objAdmiFormaContacto))
                        {
                            $entityInfoPersonaFormaContacto = new InfoPersonaFormaContacto();
                            $entityInfoPersonaFormaContacto->setValor($arrayFormaContacto['Valor']);
                            $entityInfoPersonaFormaContacto->setEstado('Activo');
                            $entityInfoPersonaFormaContacto->setFeCreacion(new \DateTime('now'));
                            $entityInfoPersonaFormaContacto->setFormaContactoId($objAdmiFormaContacto);
                            $entityInfoPersonaFormaContacto->setIpCreacion($strIpCreacion);
                            $entityInfoPersonaFormaContacto->setPersonaId($entityInfoPersona);
                            $entityInfoPersonaFormaContacto->setUsrCreacion($strUsrCreacion);

                            $this->emComercial->persist($entityInfoPersonaFormaContacto);
                        }
                    }
                }
            }
            
            if(!is_array($arrayParametros['Servicios']))
            {
                $strMensaje = $strMensaje . 'No hay Servicios Ingresados';
            }
            foreach($arrayParametros['Servicios'] as $arrayDatosServicio)
            {
                $strDescripcionProducto = $arrayDatosServicio['NombreProducto'];
                $intCantidad            = $arrayDatosServicio['Cantidad'];
                $intPrecio              = $arrayDatosServicio['Precio'];
                $strFrecfacturacion     = $arrayDatosServicio['FrecuenciaFacturacion'];
            
                $objProducto = $this->emComercial->getRepository("schemaBundle:AdmiProducto")->findOneBy(array
                                                                ('descripcionProducto'=>$strDescripcionProducto,
                                                                 'estado'=>'Activo'));
                
                if(is_object($objProducto) && !empty($objProducto))
                {
                    $intPrecio = $this->evaluarFuncionPrecio($objProducto->getFuncionPrecio(),$arrayDatosServicio['CaracteristicaProducto']);
                    if(!$intPrecio)
                    {
                        $strMensaje = $strMensaje . 'No se puede evaluar la función precio del producto, Por favor verificar las caracteristicas';
                    }
                    
                    if($intCantidad > 0)
                    {
                        $intTotal  = $intCantidad * $intPrecio;
                    }
                    else
                    {
                        $intTotal  = $intPrecio;
                    }
             
                    
                    $arrayServicio = array(array(
                                            'um_desc'                       =>	"Ninguna",	
                                            'ultimaMilla'                   =>	$strUltimaMilla,	
                                            'servicio'                      =>	0,	
                                            'producto'                      =>	$objProducto->getDescripcionProducto(),	
                                            'precio_venta'                  =>	$intPrecio,	
                                            'precio_total'                  =>	$intTotal,	
                                            'precio_instalacion_pactado'	=>	"0",	
                                            'precio_instalacion'            =>	$objProducto->getInstalacion(),	
                                            'precio'                        =>	$intPrecio,	
                                            'info'                          =>	"C",	
                                            'hijo'                          =>	0,	
                                            'frecuencia'                    =>	$strFrecfacturacion,	
                                            'descripcion_producto'          =>	$objProducto->getDescripcionProducto(),	
                                            'codigo'                        =>	$objProducto->getId(),	
                                            'cantidad'                      =>	$intCantidad	
                                                                                            ));
                    $arrayParamsServicio = array("codEmpresa"            => $intCodEmpresa,
                                                 "idOficina"             => $intOficinaId,
                                                 "entityPunto"           => $objEntityPunto,
                                                 "entityRol"             => null,
                                                 "usrCreacion"           => $strUsrCreacion,
                                                 "clientIp"              => $strIpCreacion,
                                                 "tipoOrden"             => 'N',
                                                 "ultimaMillaId"         => null,
                                                 "servicios"             => $arrayServicio,
                                                 "strPrefijoEmpresa"     => $strPrefijoEmpresa,
                                                 "session"               => null

                                        );
                    $arrayServicio = $this->serviceInfoServicio->crearServicio($arrayParamsServicio);
                    
                }
                else
                {
                    $strMensaje = $strMensaje . 'No se encuentra el Producto solicitado';
                }
                if(is_array($arrayServicio))
                {
                    $intServicioId  =   $arrayServicio['intIdServicio'];
                    if (empty($intServicioId))
                    {
                       throw new \Exception('Error al crear el servicio, Favor Notificar a Sistema.'); 
                    }
                    $objServicio = $this->emComercial->getRepository("schemaBundle:InfoServicio")->find($intServicioId);
                    
                    if(is_object($objServicio) && !empty($objServicio))
                    {
                        
                        //CREAMOS EL HISTORIAL
                        $objServicioHist = new InfoServicioHistorial();
                        $objServicioHist->setServicioId($objServicio);
                        $objServicioHist->setObservacion('Se Crea el servicio de forma Automatica');
                        $objServicioHist->setIpCreacion($strIpCreacion);
                        $objServicioHist->setUsrCreacion($strUsrCreacion);
                        $objServicioHist->setFeCreacion(new \DateTime('now'));
                        $objServicioHist->setAccion();
                        $objServicioHist->setEstado($objServicio->getEstado());
                        $this->emComercial->persist($objServicioHist);
                    }
                }
            }
            $strMensaje = $strMensaje . 'Servicios Creados'; 
            $this->emComercial->flush();
            $this->emComercial->getConnection()->commit();
            $strLoginPunto = $objEntityPunto->getLogin();
            $strPuntoId    = $objEntityPunto->getId();
        }
        catch(\Exception $ex)
        {
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollBack();
            }
            $this->emComercial->getConnection()->close();
            if($strCod == 0)
            {
                $strCod = 1;
            }
            $strMensaje ="Falló al crear el servicio. ". $ex->getMessage();
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCrmCm.flujoServicioCm',
                                            $strMensaje,
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        $arrayResultado = array('login'     => $strLoginPunto,
                                'PuntoId'   => $strPuntoId,
                                'cod'       => $strCod,
                                'error'     => $strMensaje);
        return $arrayResultado;
    }
    
    /**
     * Documentación para la función evaluarFuncionPrecio, devuelve el valor despues de calcular el producto segun sus caracteristicas.
     *
     * @param array $arrayParametros [
     *                                 $strFuncionPrecio     => función precio del producto.
     *                                 $arrayProductoCaracteristicasValores => caracteristica del servicio a evaluar.
     *                               ]
     * @return float precio
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 18-08-2020
     * 
     */
    private function evaluarFuncionPrecio($strFuncionPrecio, $arrayProductoCaracteristicasValores)
    {
        try
        {
            $floatPrecio        = 0;        
            $arrayFunctionJs    = array('Math.ceil','Math.floor','Math.pow',"}");
            $arrayFunctionPhp   = array('ceil','floor','pow',';}');
            $strFuncionPrecio   = str_replace($arrayFunctionJs, $arrayFunctionPhp, $strFuncionPrecio);

            foreach($arrayProductoCaracteristicasValores as $arrayCaracteristicaProd)
            {
                $strFuncionPrecio = str_replace("[" . $arrayCaracteristicaProd['nombre'] . "]",  $arrayCaracteristicaProd['valor'] , $strFuncionPrecio);
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
        catch(\Exception $ex)
        {
            $strMensaje ="Falló al evaluar la función precio.". $ex->getMessage();
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCrmCm.evaluarFuncionPrecio',
                                            $strMensaje,
                                            'Telcos+',
                                            '127.0.0.1');
        }    
    }
    
    /**
     * Documentación para la función historialServicioCm, devuelve el servicio con su respectivo historial.
     *
     * @param array $arrayParametros [
     *                                 IdPunto     => Punto a consultar con todos sus servicios.
     *                               ]
     * @return array $arrayParametros
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 27-08-2020
     *
     */
    public function historialServicioCm($arrayParametros)
    {
        $intIdPunto              = $arrayParametros['IdPunto'] ? $arrayParametros['IdPunto']:"";
        $strIdentificacion       = $arrayParametros['Identificacion'] ? $arrayParametros['Identificacion']:"";
        $strCod           = 0;
        $arrayDatosServices =  array();
        try
        {
            $arrayParametrosValor = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                       ->getOne('DATOS_CREACION_CM', 
                                                                                'COMERCIAL', 
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                           '10');
            if (is_array($arrayParametrosValor) && !empty($arrayParametrosValor))
            {
                $intCodEmpresa      = $arrayParametrosValor['valor1'];
                $strPrefijoEmpresa  = $arrayParametrosValor['valor2'];
                $strNombrePais      = $arrayParametrosValor['valor3'];
                $strRol             = $arrayParametrosValor['valor4'];
            }
            else
            {
                throw new \Exception('Error al obtener parámetros de registro, Favor comunicarse con Sistemas.');
            }
            if(!empty($strIdentificacion))
            {
                $arrayParametrosValor = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                   ->getOne('CLIENTE_CAJAMARCA', 
                                                                            'COMERCIAL', 
                                                                            '',
                                                                            '',
                                                                            $strIdentificacion,
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                       $intCodEmpresa);
                if(is_array($arrayParametrosValor) && !empty($arrayParametrosValor))
                {
                    $objInfoPersonaEmpresaRol = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                            ->findByIdentificacionTipoRolEmpresa($strIdentificacion, $strRol, $intCodEmpresa);

                    if(!is_object($objInfoPersonaEmpresaRol) || empty($objInfoPersonaEmpresaRol))
                    {
                        throw new \Exception('No se encuentra el Rol del cliente. Favor contactar con Sistemas');
                    }
                }
                else
                {
                    throw new \Exception('La Empresa no esta habilitada para realizar el flujo');
                }
            }
            else
            {
                throw new \Exception('Favor Ingresar la identificación');
            }                  
            if(empty($intIdPunto))
            {
                throw new \Exception('Favor Ingresar un Punto para la busqueda');
            }
            $objEntityPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($intIdPunto);
            
            if($objEntityPunto->getPersonaEmpresaRolId()->getId()!=$objInfoPersonaEmpresaRol->getId())
            {
                throw new \Exception('Punto solicitado no corresponde a la Identificación enviada.');
            }
           
            if(is_object($objEntityPunto) && !empty($objEntityPunto))
            {
                $arrayServicios = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findBy(
                                                                                            array('puntoId' => $objEntityPunto->getId()));

                foreach($arrayServicios as $objServicio)
                {                        
                    $arrayDatos['data'] = array(
                                        'idServicio'=>$objServicio->getId()
                    );
                    $arrayHistorial = $this->getServicioHistorial($arrayDatos);
                    $arrayDatosServices[] = array(  'idServicio' => $objServicio->getId(),
                                                    'descripcion' => $objServicio->getProductoId()->getDescripcionProducto(),
                                                    'estadoActual'=> $objServicio->getEstado(),
                                                    'historial'   => $arrayHistorial['historiales']
                                      );
                }
            }
            else
            {
                throw new \Exception('No se encuentra el punto solicitado');
            }
            
        }
        catch(\Exception $ex)
        {
            if($strCod == 0)
            {
                $strCod = 1;
            }
            $strMensaje ="Falló al consultar el servicio. ". $ex->getMessage();
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCrmCm.historialServicioCm',
                                            $strMensaje,
                                            'Telcos+',
                                            '127.0.0.1');
        }
        $arrayResultado = array('servicios' => $arrayDatosServices,
                                'cod'       => $strCod,
                                'error'     => $strMensaje);
        return $arrayResultado;
    }
    
    /**
     * Documentación para la función getServicioHistorial, permite obtener el historial de un servicio.
     *
     * @param array $arrayParametros [
     *                                 idServicio     => Servicio al cual se le consultara el historial.
     *                               ]
     * @return array $arrayParametros
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 27-08-2020
     */
    private function getServicioHistorial($arrayData)
    {
        $arrayResultado  = array();
        try
        {
            $intServicioId    = $arrayData['data']['idServicio'];
            $intStart         = 0;
            $intLimit         = 20;
                        
            $arrayHistorial   = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                        ->getHistorialServicio($intServicioId,$intStart,$intLimit,'DESC');
            
            if(count($arrayHistorial['registros'])==0)
            {
                $arrayResultado['status']  = $this->status['NULL'];
                $arrayResultado['mensaje'] = $this->mensaje['NULL'];
                
                return $arrayResultado;
            }
            
            $arrayHistoriales = $arrayHistorial['registros'];
            
            foreach($arrayHistoriales as $entity)
            {
                $strUsrCreacion    = $entity->getUsrCreacion();
                $strFeCreacion     = $entity->getFeCreacion();
                $strFechaCreacion  = strval(date_format($strFeCreacion, "d/m/Y G:i"));
                $strIpCreacion     = $entity->getIpCreacion();
                $strEstado         = $entity->getEstado();
                $intMotivoId       = $entity->getMotivoId();
                $strObservacion    = $entity->getObservacion();
                $strAccion         = $entity->getAccion();
                
                if($intMotivoId!=null)
                {
                    $objMotivo         = $this->emGeneral->find('schemaBundle:AdmiMotivo', $intMotivoId);
                    $strNombreMotivo   = $objMotivo->getNombreMotivo();
                }
                else
                {
                    $strNombreMotivo = "NA";
                }
                
                $arrEncontrados[] = array(  'usrCreacion'   => $strUsrCreacion,
                                            'feCreacion'    => $strFechaCreacion,
                                            'ipCreacion'    => $strIpCreacion,
                                            'estado'        => $strEstado,
                                            'nombreMotivo'  => $strNombreMotivo,
                                            'observacion'   => $strObservacion,
                                            'accion'        => $strAccion
                                          );
            }
        }
        catch(Exception $e)
        {
            $arrayResultado['status']  = $this->status['ERROR'];
            $arrayResultado['mensaje'] = $this->mensaje['ERROR'];
            return $arrayResultado;
        }
        
        $arrayResultado['historiales'] = $arrEncontrados;
        $arrayResultado['status']      = $this->status['OK'];
        $arrayResultado['mensaje']     = $this->mensaje['OK'];
        return $arrayResultado;
    }
}
