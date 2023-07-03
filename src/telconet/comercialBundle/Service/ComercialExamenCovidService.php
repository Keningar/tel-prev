<?php


namespace telconet\comercialBundle\Service;
use Doctrine\ORM\EntityManager;
use telconet\schemaBundle\Service\UtilService;
use telconet\schemaBundle\Entity\ReturnResponse;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\InfoPunto;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoContrato;
use telconet\comercialBundle\Service\InfoPuntoService;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use \telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;


class ComercialExamenCovidService
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
     * Documentación para la función 
     *
     *
     * @param array $arrayParametros [
     *                                 strIdentificacion     => Identificación del cliente.
     *                                 strCiudad             => Ciudad de la solicitud,
     *                                 intCantidad           => Numero de registro a ingresar
     *                               ]
     *
     * @return array $arrayResultado [
     *                                 login     => login del punto
     *                                 error     => mensaje de error
     *                               ]
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 03-05-2020
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.1 14-05-2020 - Se modifica para crear los servicios en los clientes sobre puntos matriz.
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.2 22-05-2020 - Se permite la creación de los empleados de Tn como clientes.
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.3 28-05-2020 - Se toma el producto que presenta iva para generar la OS.
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.4 03-06-2020 - Se Modifica para permitir la creación de clientes a empleados de otra empresas del consorcio.
     * 
     * @author kevin ortiz <kcortiz@telconet.ec>
     * @version 1.4 31-07-2020 - Se valido el $arrayInfoPersonaExt para envio de correos de para quienes son de guayaquil o otras cuidades
     */
    public function getClientes($arrayParametros)
    {
        $intCantidad             = $arrayParametros['intCantidad'] ? $arrayParametros['intCantidad']:1;
        $strCiudad               = $arrayParametros['strCiudad'] ? $arrayParametros['strCiudad']:'Guayaquil';
        $strEsEmpleado           = $arrayParametros['esEmpleado'] ? $arrayParametros['esEmpleado']:'N';
        $strDescripcionTipoRol   = $arrayParametros['strDescripcionTipoRol'] ? $arrayParametros['strDescripcionTipoRol']:"";
        $strIdentificacion       = $arrayParametros['strIdentificacion'] ? $arrayParametros['strIdentificacion']:"";
        $strUsrCreacion          = ( isset($arrayParametros['strUsrCreacion']) && !empty($arrayParametros['strUsrCreacion']) )
                                    ? $arrayParametros['strUsrCreacion'] : 'TELCOS +';
        $strIpCreacion           = ( isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']) )
                                    ? $arrayParametros['strIpCreacion'] : '127.0.0.1';
        $strMensaje              = '';
        $intCodEmpresa           = '10';
        $strUsrCreacion          = 'covid';
        $strEstadoActivo         = 'Activo';
        $strClientIp             = '127.0.0.1';
        $strPrefijoEmpresa       = 'TN';
        $intPersonaEmpresaClt    = 0;
        $strTipoConsulta         = 'PERSONA';
        $intCantidadPuntos       = 0;
        $strOficinaFacturacion   = 2;
        $strNombrePais           = 'ECUADOR';
        $strRol                  = 'Cliente';
        $strTipoContrato         = '108';
        $strFormaPago            =  '1' ;
        $strPadreFacturacion     =  'S';
        $strCod                  = 0;
        $strLoginPunto           = '';
        $strEsCliente            = 'N';
        $strEmpleado             = 'N';
        $strStatus               = 'Cliente';

        /////////DATOS DEL PUNTO/////////
        if($strCiudad=='Guayaquil')
        {
            $intTipoUbicacionId     =6; //ciudadela,abierto,Cooperativa
            $strTipoNegocio         ='184';
            $strSectorId            ='3711';
            $strPuntoCobertura      ='257';
            $strParroquia           ="469";
            $strOficinaId           ="2";
            $strLoginVendedor       ="Admin Account";
            $strCanton              = '75';
        }
        else
        {
            $intTipoUbicacionId     =6;
            $strTipoNegocio         ='184';
            $strSectorId            ='3711';
            $strPuntoCobertura      ='257';
            $strParroquia           ="469";
            $strOficinaId           ="2";
            $strLoginVendedor       ="Admin Account";
            $strCanton              = '75';
        }

        $this->emComercial->getConnection()->beginTransaction();

        try
        {
            if( empty($strIdentificacion))
            {
                throw new \Exception('la identificación no puede estar vacia.');
            }
            $objInfoPersona = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                                ->findOneBy( array('identificacionCliente' => $strIdentificacion));
            
            $objInfoPersonaEmpresaRol = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                    ->findByIdentificacionTipoRolEmpresa($strIdentificacion, $strRol, $intCodEmpresa);
            if((!is_object($objInfoPersona) && empty($objInfoPersona) && $strEsEmpleado=='N') || (!is_object($objInfoPersonaEmpresaRol)
                && $strEsEmpleado=='N'))
            {
                $arrayParametrosNaf = array(
                                        'strCodEmpresa'     => $intCodEmpresa,
                                        'strIdentificacion' => trim($strIdentificacion)
                                       );
                $arrayDatosProveedor = $this->getProvedores($arrayParametrosNaf);

                $arrayDatosNaf = $arrayDatosProveedor['datos'];
                if (!is_array($arrayDatosNaf) ||empty($arrayDatosNaf))
                {
                    $strCod = 99;
                    throw new \Exception('No se encuentra Datos del Proveedor.');
                }
            }
            else if (!is_object($objInfoPersona) && empty($objInfoPersona) && $strEsEmpleado=='S')
            {
                $arrayInfoPersonaExt = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                                                                ->getDataEmpleados(array('strIndentificacion'=>$strIdentificacion));
                if(is_array($arrayInfoPersonaExt) && !empty($arrayInfoPersonaExt) && $strEsEmpleado=='S')
                {
                    $strIdentificacion     = $arrayInfoPersonaExt[0]['cedula'];
                    $strNombre             = $arrayInfoPersonaExt[0]['nombre'];
                    $strRazonSocial        = $arrayInfoPersonaExt[0]['nombre'];
                    $strDireccion          = $arrayInfoPersonaExt[0]['direccion']? $arrayInfoPersonaExt[0]['direccion']:'xxxxxxxxxxxxx';
                    $strTipoTributario     = $arrayInfoPersonaExt[0]['nombres'];
                    $strTelefono           = $arrayInfoPersonaExt[0]['telefono']? $arrayInfoPersonaExt[0]['telefono']:'222222222';
                   
                    if($strCiudad=='Guayaquil')
                   {
                    $strCorreo             = $arrayInfoPersonaExt[0]['correo']?$arrayInfoPersonaExt[0]['correo']:'facturacion_gye@telconet.ec';
                   }
                   else
                   {
                    $strCorreo             = $arrayInfoPersonaExt[0]['correo']?$arrayInfoPersonaExt[0]['correo']:'facturacion_uio@telconet.ec';
                   }
                   
                }
                else
                {
                    $strCod = 99;
                    throw new \Exception('No se encuentra la identificación registrada como Empleado.'); 
                }
            }
            else 
            {
                $objInfoPersonaEmpresaRol = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                    ->findByIdentificacionTipoRolEmpresa($strIdentificacion, $strRol, $intCodEmpresa);
                
                if (!is_object($objInfoPersonaEmpresaRol) || empty($objInfoPersonaEmpresaRol))
                {
                    $objInfoPersonaEmpresaRol = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                    ->findByIdentificacionTipoRolEmpresa($strIdentificacion, 'Empleado', $intCodEmpresa);
                    if (!is_object($objInfoPersonaEmpresaRol) || empty($objInfoPersonaEmpresaRol))
                    {
                        if($strEsEmpleado=='S')
                        {
                            $arrayInfoPersonaExt = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                ->getDataEmpleados(array('strIndentificacion'=>$strIdentificacion));
                            if(is_array($arrayInfoPersonaExt) && !empty($arrayInfoPersonaExt) && $strEsEmpleado=='S')
                            {
                                $strIdentificacion    = $arrayInfoPersonaExt[0]['cedula'];
                                $strNombre            = $arrayInfoPersonaExt[0]['nombre'];
                                $strRazonSocial       = $arrayInfoPersonaExt[0]['nombre'];
                                $strDireccion         = $arrayInfoPersonaExt[0]['direccion']? $arrayInfoPersonaExt[0]['direccion']:'xxxxxxxxxxxxx';
                                $strTipoTributario    = $arrayInfoPersonaExt[0]['nombres'];
                                $strTelefono          = $arrayInfoPersonaExt[0]['telefono']? $arrayInfoPersonaExt[0]['telefono']:'222222222';
                                $strCorreo      = $arrayInfoPersonaExt[0]['correo']? $arrayInfoPersonaExt[0]['correo']:'sistemas-soporte@telconet.ec';
                            }
                            else
                            {
                                $strCod = 99;
                                throw new \Exception('No se encuentra la identificación registrada como Empleado.'); 
                            }

                            $strEmpleado = 'S';
                            $strStatus   = 'Empleado';
                        }
                        else
                        {
                            $strCod = 99;
                            throw new \Exception('La Persona no cuenta en los registros como Cliente de Telconet.'); 
                        }
                    }
                    else
                    {
                        $strEmpleado = 'S';
                        $strStatus   = 'Empleado';
                    }
                }
            }

            if ((!is_array($arrayDatosNaf) && empty($arrayDatosNaf)) && (!is_object($objInfoPersona) || empty($objInfoPersona)) && 
                (!is_array($arrayInfoPersonaExt) || empty($arrayInfoPersonaExt)))
            {
                $strCod = 99;
                throw new \Exception('No existe registro como Cliente o Proveedor, Favor Verificar la Identificación.'); 
            }
            
            if(is_object($objInfoPersona) && !empty($objInfoPersona))
            {
                $strIdentificacion     = $objInfoPersona->getIdentificacionCliente();
                $strNombre             = $objInfoPersona->getNombres();
                $strRazonSocial        = $objInfoPersona->getRazonSocial();
                $strDireccion          = $objInfoPersona->getDireccion();
                $strTipoTributario     = $objInfoPersona->getTipoTributario();
                $strTelefono           = '2222222222';
                $strCorreo             = 'sistemas-soporte@telconet.ec';
                
                $strTelefonos = $this->emComercial->getRepository("schemaBundle:InfoPersonaFormaContacto")
                                                                  ->getStringFormasContactoParaSession($objInfoPersona->getId(),'Telefono Movil');
            
                if(!empty($strTelefonos))
                {
                    $arrayTelefonos = explode(",", $strTelefonos, 5);
                    $strTelefono = $arrayTelefonos [0];
                }

                $strCorreos = $this->emComercial->getRepository("schemaBundle:InfoPersonaFormaContacto")
                                                               ->getStringFormasContactoParaSession($objInfoPersona->getId(),'Correo Electronico');
                if(!empty($strCorreos))
                {
                    $arrayCorreos = explode(", ", $strCorreos, 5);
                    $strCorreo   = $arrayCorreos[0];
                }
                $arrayDatosBusqueda = array('intEmpleadoId'=> $objInfoPersona->getId(),
                                            'estado'       => $strEstadoActivo,
                                            'codEmpresa'   => $intCodEmpresa);
                $arrayPunto =  $this->emComercial->getRepository("schemaBundle:InfoPuntoDatoAdicional")->getPuntoByPersona($arrayDatosBusqueda);
                $objEntityPunto = $arrayPunto[0];
                if(is_object($objEntityPunto) && ! empty($objEntityPunto))
                {
                    $strEsCliente          = 'S';
                }

                $objInfoPersona->setPagaIva('S');
                $this->emComercial->persist($objInfoPersona);                
                $this->emComercial->flush(); 
                $this->emComercial->getConnection()->commit();                
            }
            else if(is_array($arrayDatosNaf) && !empty($arrayDatosNaf))
            {
                $strTipoIdentificacion = $arrayDatosNaf[0]['tipoIdentificacion'];
                $strIdentificacion     = $arrayDatosNaf[0]['identificacion'];
                $strNombre             = $arrayDatosNaf[0]['nombre'];
                $strRazonSocial        = $arrayDatosNaf[0]['razonSocial'];
                $strDireccion          = $arrayDatosNaf[0]['direccion']? $arrayDatosNaf[0]['direccion']:'xxxxxxxxxxxxx';
                $strTipoTributario     = $arrayDatosNaf[0]['tipoTributario'];
                $strTelefono           = $arrayDatosNaf[0]['telefono']? $arrayDatosNaf[0]['telefono']:'222222222';
                $strCorreo             = $arrayDatosNaf[0]['correo']? $arrayDatosNaf[0]['correo']:'sistemas-soporte@telconet.ec'; 
            }
            
            if( !is_object($objInfoPersona) || empty($objInfoPersona) || $strEmpleado=='S' || (is_array($arrayDatosNaf) && !empty($arrayDatosNaf)))
            {

                $objTitulo = $this->emComercial->getRepository("schemaBundle:AdmiTitulo")
                                                ->findOneBy( array('descripcionTitulo' => 'Ingeniero'));
                
                $this->emComercial->getConnection()->beginTransaction();
                if($strEmpleado=='N' && !is_object($objInfoPersona) || empty($objInfoPersona))
                {
                    $objInfoPersona = new InfoPersona();
                    $objInfoPersona->setIdentificacionCliente($strIdentificacion);
                    if($strTipoTributario=='PERSONA NATURAL')
                    {
                        $objInfoPersona->setTipoTributario('NAT');
                    }
                    else
                    {
                        $objInfoPersona->setTipoTributario('JUR');
                    }
                    $objInfoPersona->setRazonSocial($strRazonSocial);
                    $objInfoPersona->setRepresentanteLegal($strNombre);
                    $objInfoPersona->setNacionalidad('NAC');
                    $objInfoPersona->setDireccion($strDireccion);
                    $objInfoPersona->setDireccionTributaria($strDireccion);
                    $objInfoPersona->setNombres($strNombre);
                    $objInfoPersona->setTituloId($objTitulo);
                    $objInfoPersona->setOrigenProspecto('N');
                    $objInfoPersona->setPagaIva('S');
                    if($strTipoIdentificacion=='R')
                    {
                        $objInfoPersona->setTipoIdentificacion('RUC');
                    }
                    else
                    {
                        $objInfoPersona->setTipoIdentificacion('CED');
                    }

                    $objInfoPersona->setFeCreacion(new \DateTime('now'));
                    $objInfoPersona->setUsrCreacion($strUsrCreacion);
                    $objInfoPersona->setIpCreacion($strClientIp);
                    $objInfoPersona->setEstado('Activo');
                    $this->emComercial->persist($objInfoPersona);
                }
                $objOficina = $this->emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($strOficinaFacturacion);
                if(!is_object($objOficina))
                {
                    throw new \Exception('No existe registro en la oficina de facturación');
                }
                $objPersonaEmpresaRol = new InfoPersonaEmpresaRol();
                        
                $entityEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoEmpresaRol')
                                                ->findPorNombreTipoRolPorEmpresa($strRol, $intCodEmpresa);
                $objPersonaEmpresaRol->setEmpresaRolId($entityEmpresaRol);
                $objPersonaEmpresaRol->setPersonaId($objInfoPersona);
                $objPersonaEmpresaRol->setFeCreacion(new \DateTime('now'));
                $objPersonaEmpresaRol->setUsrCreacion($strUsrCreacion);
                $objPersonaEmpresaRol->setOficinaId($objOficina);
                $objPersonaEmpresaRol->setEstado('Activo'); 
                $this->emComercial->persist($objPersonaEmpresaRol);
                
                $objPersonaEmpresaRolHist = new InfoPersonaEmpresaRolHisto();
                $objPersonaEmpresaRolHist->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                $objPersonaEmpresaRolHist->setEstado('Activo');
                $objPersonaEmpresaRolHist->setUsrCreacion($strUsrCreacion);
                $objPersonaEmpresaRolHist->setIpCreacion($strClientIp);
                $objPersonaEmpresaRolHist->setFeCreacion(new \DateTime('now'));
                $this->emComercial->persist($objPersonaEmpresaRolHist);
                
                $this->emComercial->flush(); 
                $this->emComercial->getConnection()->commit();
                
                
                if (!is_object($objInfoPersona))
                {
                   $strCod = 99;
                   throw new \Exception('Error al crear al cliente, Favor Notificar a Sistema.'); 
                }
            }
            if($strEsCliente=='N')
            {            
                $strLogin = $this->serviceInfoPunto->generarLoginPrv($objInfoPersona,'');
                $strLogin = $strLogin.'-covid';
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
                                        'tipoUbicacionId'       =>$intTipoUbicacionId,
                                        'tipoNegocioId'         =>$strTipoNegocio,
                                        'telefonoDatoEnvio'     =>$strTelefono,
                                        'strNombrePais'         =>$strNombrePais,
                                        'sectorId'              =>$strSectorId,
                                        'rol'                   =>$strRol,
                                        'ptoCoberturaId'        =>$strPuntoCobertura	,
                                        'prefijoEmpresa'        =>$strPrefijoEmpresa,	
                                        'personaId'             =>$objInfoPersona,	
                                        'parroquia'             =>	$strParroquia,	
                                        'origen_web'            =>	"N",
                                        'oficina'               =>	$strOficinaId,	
                                        'observacion'           =>	"Na",
                                        'nombreDatoEnvio'       =>	$strNombre,	
                                        'longitudFloat'         =>	"",
                                        'login'                 =>	$strLogin,	
                                        'latitudFloat'          =>	"",	
                                        'intIdPais'             =>	1,	
                                        'formas_contacto'       =>	"0,Correo Electronico,".$strCorreo,	
                                        'esPadreFacturacion'	=>	$strPadreFacturacion,	
                                        'direccion'             =>	$strDireccion,	
                                        'direccionDatoEnvio'	=>	$strDireccion,	
                                        'descripcionpunto'      =>	"Na",	
                                        'dependedeedificio'     =>	"N"	,
                                        'correoElectronicoDatoEnvio'	=>	$strCorreo	,
                                        'loginVendedor'         =>	$strLoginVendedor	,
                                        'cantonId'              =>	$strCanton	
                                        
                );
                
                $arrayParametrosPunto =  array('strCodEmpresa'        => $intCodEmpresa,
                                               'strUsrCreacion'       => $strUsrCreacion,
                                               'strClientIp'          => $strClientIp,
                                               'arrayDatosForm'       => $arrayDatosForm,
                                               'arrayFormasContacto'  => null);

                $objEntityPunto = $this->serviceInfoPunto->crearPunto($arrayParametrosPunto);
            }
                $objProducto = $this->emComercial->getRepository("schemaBundle:AdmiProducto")->findOneBy(array
                                                                ('descripcionProducto'=>'Provisión de Servicios Varios',
                                                                 'estado'=>'Activo',
                                                                 'id'=>976));
                
                $arrayParametrosValor = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                       ->getOne('VALOR_PRUEBAS_COVID', 
                                                                                'COMERCIAL', 
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                           $intCodEmpresa);
                if(is_array($arrayParametrosValor) && isset($arrayParametrosValor['valor1']))
                {
                    $intPrecio=$arrayParametrosValor['valor1'];
                }
                
                $intTotal  = $intCantidad * $intPrecio;
                $arrayServicio = array(array(
                                        'um_desc'                       =>	"Ninguna",	
                                        'ultimaMilla'                   =>	"",	
                                        'servicio'                      =>	0,	
                                        'producto'                      =>	$objProducto->getDescripcionProducto(),	
                                        'precio_venta'                  =>	$intPrecio,	
                                        'precio_total'                  =>	$intTotal,	
                                        'precio_instalacion_pactado'	=>	"0",	
                                        'precio_instalacion'            =>	"0",	
                                        'precio'                        =>	$intPrecio,	
                                        'info'                          =>	"C",	
                                        'hijo'                          =>	0,	
                                        'frecuencia'                    =>	"0",	
                                        'descripcion_producto'          =>	$objProducto->getDescripcionProducto(),	
                                        'codigo'                        =>	$objProducto->getId(),	
                                        'cantidad'                      =>	$intCantidad	
                                                                                        ));
                $arrayParamsServicio = array("codEmpresa"            => $intCodEmpresa,
                                            "idOficina"             => $strOficinaId,
                                            "entityPunto"           => $objEntityPunto,
                                            "entityRol"             => null,
                                            "usrCreacion"           => $strUsrCreacion,
                                            "clientIp"              => $strClientIp,
                                            "tipoOrden"             => 'N',
                                            "ultimaMillaId"         => null,
                                            "servicios"             => $arrayServicio,
                                            "strPrefijoEmpresa"     => $strPrefijoEmpresa,
                                            "session"               => null
                                            
                                    );
                $arrayServicio = $this->serviceInfoServicio->crearServicio($arrayParamsServicio);
                
                if(is_array($arrayServicio))
                {
                    $intServicioId  =   $arrayServicio['intIdServicio'];
                    if (empty($intServicioId))
                    {
                       throw new \Exception('Error al crear el servicio, Favor Notificar a Sistema.'); 
                    }
                    $objServicio = $this->emComercial->getRepository("schemaBundle:InfoServicio")->find($intServicioId);
                    
                    if(!empty($objServicio) && $objServicio->getEstado()=='Pendiente')
                    {
                        $objServicio->setEstado('Activo');
                        $this->emComercial->persist($objServicio);
                    }
                    //CONFIRMAMOS EL SERVICIO
                    $objServicioHist = new InfoServicioHistorial();
                            $objServicioHist->setServicioId($objServicio);
                            $objServicioHist->setObservacion('Se Confirmó el Servicio');
                            $objServicioHist->setIpCreacion($strClientIp);
                            $objServicioHist->setUsrCreacion($strUsrCreacion);
                            $objServicioHist->setFeCreacion(new \DateTime('now'));
                            $objServicioHist->setAccion('confirmarServicio');
                            $objServicioHist->setEstado($objServicio->getEstado());
                            $this->emComercial->persist($objServicioHist);
                }
                
                //CONTRATO
                if($strEsCliente=='N')
                {
                    $objInfoContrato = $this->emComercial->getRepository('schemaBundle:InfoContrato')
                                    ->findContratoActivoPorPersonaEmpresaRol($objEntityPunto->getPersonaEmpresaRolId()->getId());
                    if (!empty($objInfoContrato))
                    {
                        $boolTieneContratoActivo = true;
                    }
                    else
                    {
                        $boolTieneContratoActivo = false;
                    }
                    $objPersonaEmpresaRol = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                                ->findByIdentificacionTipoRolEmpresa($strIdentificacion, $strRol, $intCodEmpresa);

                    if(!is_object($objPersonaEmpresaRol))
                    {
                        throw new \Exception('No se encuentra el Rol de la Persona, Favor Notificar a Sistemas.'); 
                    }

                    if (!$boolTieneContratoActivo)
                    {
                        $strFechaAct       = date("Y-m-d");
                        $strFecha           = date('Y-m-d', strtotime("+12 months $strFechaAct"));

                        $arrayDatosForm = array(
                                                'tipoContratoId'        =>	$strTipoContrato,
                                                'personaEmpresaRolId'	=>	$objPersonaEmpresaRol->getId()	,
                                                'idcliente'             =>	$objInfoPersona->getId()	,
                                                'formaPagoId'           =>	$strFormaPago	,
                                                'feFinContratoPost' 	=>	$strFecha	,
                                                'cliente'               =>	$objInfoPersona->getRepresentanteLegal()
                        );

                        $arrayParametrosContrato                   = array();
                        $arrayParametrosContrato['codEmpresa']     = $intCodEmpresa;
                        $arrayParametrosContrato['prefijoEmpresa'] = $strPrefijoEmpresa; 
                        $arrayParametrosContrato['idOficina']      = $strOficinaId; 
                        $arrayParametrosContrato['usrCreacion']    = $strUsrCreacion; 
                        $arrayParametrosContrato['clientIp']       = $strClientIp; 
                        $arrayParametrosContrato['datos_form']     = $arrayDatosForm; 
                        $arrayParametrosContrato['origen']         = 'WEB';
                        $objEntityContrato = $this->serviceInfoContrato->crearContrato($arrayParametrosContrato);

                        if (!is_object($objEntityContrato)) 
                        {
                            throw $this->createNotFoundException('No se encontro el contrato');
                        }            
                        //Activa el contrato
                        $objEntityContrato->setEstado('Activo');
                        $objEntityContrato->setUsrAprobacion('Automatico');
                        $objEntityContrato->setFeAprobacion(new \DateTime('now'));
                        $this->emComercial->persist($objEntityContrato);
                        $this->emComercial->flush();
                        $this->emComercial->getConnection()->commit();
                        

                    }
                     //FIN DE CAMBIOS CONTRATO
                }

                
                $objAdmiTarea = $this->emComercial->getRepository("schemaBundle:AdmiTarea")->findOneBy(array('nombreTarea'=>'EMISION DE FACTURAS'));
                
                $objDepartamento = $this->emGeneral->getRepository("schemaBundle:AdmiDepartamento")
                                                                                ->findOneBy(array('nombreDepartamento'=>'Contabilidad Facturacion'));
                
                $objCanton = $this->emGeneral->getRepository("schemaBundle:AdmiCanton")
                                                                                ->findOneBy(array('nombreCanton'=>strtoupper('GUAYAQUIL')));
                
                $strObservacionTarea = "Estimados, por favor su ayuda generando la Factura manual al ".$strStatus ." "
                                    .$objInfoPersona->getRepresentanteLegal()."con C.I./RUC : <b>".$objInfoPersona->getIdentificacionCliente()."</b>,"
                                . " respecto a Servicios de <b>Provisión de Servicios Varios</b>"
                                . " por pruebas realizadas para COVID-19:<br><b>Total :</b> ".$intCantidad." Pruebas <br><b>Valor : </b> $".$intTotal;
                $arrayCreaTarea = array (
                                         'intTarea'               => $objAdmiTarea->getId(),
                                         'strTipoAfectado'        => 'Cliente',
                                         'objPunto'               => $objEntityPunto ,
                                         'objDepartamento'        => $objDepartamento,
                                         'strCantonId'            => $objCanton->getId(),
                                         'strEmpresaCod'          => $intCodEmpresa,
                                         'strPrefijoEmpresa'      => $strPrefijoEmpresa,
                                         'strObservacion'         => $strObservacionTarea,
                                         'strUsrCreacion'         => $strUsrCreacion,
                                         'strIpCreacion'          => $strClientIp
                                        );
                
                               
                                
                $arrayRespuesta = $this->serviceSoporteServicio->crearTareaRetiroEquipoPorDemo($arrayCreaTarea);
            if ($arrayRespuesta['mensaje'] === 'fail')
            {
                throw new \Exception('Error al crear la Tarea, Favor Notificar a Sistema.'); 
            }
            $strLoginPunto = $objEntityPunto->getLogin();
        }
        catch(\Exception $ex)
        {
            if($strCod ==0)
            {
                $strCod = 1;
            }
            $strMensaje ="Falló al agendar las citas.". $ex->getMessage();
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCovidService.getClientes',
                                            $strMensaje,
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        $arrayResultado = array('login' => $strLoginPunto,
                                'tarea' => $arrayRespuesta,
                                'cod'   => $strCod,
                                'error' => $strMensaje);
        return $arrayResultado;
    }
    
    /**
    * Documentación para la función 'getProvedores'.
    *
    * Función encargada de recuperar el detalle de un proveedor por su identificación.
    *
    * @author David Leon <mdleon@telconet.ec>
    * @version 1.0 - 03-05-2020
    *
    */
    protected function getProvedores($arrayParametros)
    {
         $strUsrCreacion = ( isset($arrayParametros['strUsrCreacion']) && !empty($arrayParametros['strUsrCreacion']) )
                            ? $arrayParametros['strUsrCreacion'] : 'TELCOS +';
        try
        {
            $strMensajeError     = "";
            $strStatus           = "200";
            $arrayResultado      = array();
            if(is_array($arrayParametros) && !empty($arrayParametros))
            {
                $arrayParametroNaf = array( 'user'             => $strUsrCreacion,
                                            'nombreAplicativo' => "citas-covid19",
                                            'accion'           => "getProveedor",
                                            'data'             => $arrayParametros);
                $arrayOptions      = array(CURLOPT_SSL_VERIFYPEER => false);
                $arrayResponse     = $this->restClient->postJSON($this->strApiNafUrl,json_encode($arrayParametroNaf) ,$arrayOptions);
                if(!isset($arrayResponse['result']) || $arrayResponse['status']!="200")
                {
                    throw new \Exception('Problemas al obtener información con apiNaf: '.$arrayResultado["error"]);
                }
                
                $arrayResultado = json_decode($arrayResponse['result'],true);
                
                if((empty($arrayResultado) || !is_array($arrayResultado))|| (!isset($arrayResultado['message']) || $arrayResultado["message"] == ""))
                {
                    throw new \Exception('Problemas al obtener información, reintente nuevamente.');
                }

                $arrayDataTemp = $arrayResultado["message"];

                if((empty($arrayDataTemp) || !is_array($arrayDataTemp))|| (!isset($arrayDataTemp["proveedor"]) || $arrayDataTemp["proveedor"] == ""))
                {
                    throw new \Exception('Problemas al obtener información, reintente nuevamente.');
                }
                $arrayDatosProveedor = $arrayDataTemp["proveedor"];
            }
            else
            {
                throw new \Exception('Problemas al obtener información, reintente nuevamente.');
            }
        }
        catch( \Exception $e )
        {
            $strMensajeError = $e->getMessage();
            $strStatus       = "500";
        }
        $arrayRespuesta = array('error'  => $strMensajeError,
                                'datos'  => $arrayDatosProveedor,
                                'status' => $strStatus);
        return $arrayRespuesta;
    }
    
     /**
     * Documentación para la función 
     *
     *
     * @param array $arrayParametros [
     *                                 strIdentificacion     => Identificación del cliente.
     *                               ]
     *
     * @return array $arrayResultado [
     *                                  'cedula' ,
                                        'nombre1',
                                        'nombre2',
                                        'apellido1',
                                        'apellido2',
                                        'fechaNacimento',
                                        'genero',
                                        'edad',
                                        'telefono',
                                        'correo',
                                        'direccion'
     *                               ]
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 13-05-2020
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.1 03-06-2020 Se Modifica para consultar los empleados del consorcio.
     * 
     */
    public function getEmpleado($arrayParametros)
    {
        $strStatus       = "200";
        $strIdentificacion  = $arrayParametros['strIdentificacion'] ? $arrayParametros['strIdentificacion']:'';
        
        if(empty($strIdentificacion))
        {
            throw new \Exception('la identificación no puede estar vacia.');
        }
        try
        {
            
            $objInfoPersonaRol = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                                        ->findByIdentificacionTipoRolEmpresa($strIdentificacion,'Empleado',10);
            if(is_object($objInfoPersonaRol))
            {
                $arrayInfoPersonaExt = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                ->getDataEmpleados(array('strIndentificacion'=>$strIdentificacion));
                $objEmpresaGrupo = $this->emComercial->getRepository("schemaBundle:InfoEmpresaGrupo")->find($arrayInfoPersonaExt[0]['empresa']);
                if(is_array($arrayInfoPersonaExt) && !empty($arrayInfoPersonaExt))
                {
                    $arrayDatosEmpleado = array(
                    'cedula'            => $arrayInfoPersonaExt[0]['cedula'],
                    'login'             => $arrayInfoPersonaExt[0]['login'],
                    'nombre1'           => $arrayInfoPersonaExt[0]['nombre1'],
                    'nombre2'           => $arrayInfoPersonaExt[0]['nombre2'],
                    'apellido1'         => $arrayInfoPersonaExt[0]['apellido1'],
                    'apellido2'         => $arrayInfoPersonaExt[0]['apellido2'],
                    'fechaNacimento'    => date_format($arrayInfoPersonaExt[0]['fechaNacimiento'], "d/m/Y"),
                    'genero'            => $arrayInfoPersonaExt[0]['genero'],
                    'edad'              => $arrayInfoPersonaExt[0]['edad'],
                    'codEmpresa'        => $arrayInfoPersonaExt[0]['empresa'],
                    'empresa'           => $objEmpresaGrupo->getNombreEmpresa(),    
                    'telefono'          => $arrayInfoPersonaExt[0]['telefono'],
                    'celular'           => $arrayInfoPersonaExt[0]['celular'],
                    'correo'            => $arrayInfoPersonaExt[0]['correo'],
                    'direccion'         => $arrayInfoPersonaExt[0]['direccion']
                    );
                }
                else
                {
                    throw new \Exception('No se encuentra registrado en la base de Empleados.');
                }
            }
            else
            {                
            $objInfoPersona = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                                ->findOneBy( array('identificacionCliente' => $strIdentificacion));
            if(!is_object($objInfoPersona) && empty($objInfoPersona))
            {
                throw new \Exception('No se encuentra registro de la persona.');
            }
            
            $arrayNombres = explode(" ", $objInfoPersona->getNombres(), 2);
            $strNombre1   = $arrayNombres[0];
            $strNombre2   = $arrayNombres[1];
            $arrayApellidos = explode(" ", $objInfoPersona->getApellidos(), 2);
            $strApellido1 = $arrayApellidos[0];
            $strApellido2 = $arrayApellidos[1];
            $strTelefono = $this->emComercial->getRepository("schemaBundle:InfoPersonaFormaContacto")
                                                                  ->getStringFormasContactoParaSession($objInfoPersona->getId(),'Telefono Movil');
            
            if(!empty($strTelefono))
            {
                $arrayTelefonos = explode(",", $strTelefono, 5);
                $strConvencional = $arrayTelefonos [0];
                $strCelular      = $arrayTelefonos [1];
            }
            
            $strCorreos = $this->emComercial->getRepository("schemaBundle:InfoPersonaFormaContacto")
                                                                  ->getStringFormasContactoParaSession($objInfoPersona->getId(),'Correo Electronico');
            if(!empty($strCorreos))
            {
                $arrayCorreos = explode(", ", $strCorreos, 5);
                $strCorreo   = $arrayCorreos[0];
            }
            $strFechaNaci = date_format($objInfoPersona->getFechaNacimiento(), "Y/m/d");
            $strFechaNacimiento = new \DateTime($strFechaNaci);
            $strHoy = new \DateTime('now');
            $intEdad = $strHoy->diff($strFechaNacimiento);
            $arrayDatosEmpleado = array(
                'cedula'            => $objInfoPersona->getIdentificacionCliente(),
                'login'             => $objInfoPersona->getLogin(),
                'nombre1'           => $strNombre1,
                'nombre2'           => $strNombre2,
                'apellido1'         => $strApellido1,
                'apellido2'         => $strApellido2,
                'fechaNacimento'    => date_format($objInfoPersona->getFechaNacimiento(), "d/m/Y"),
                'genero'            => $objInfoPersona->getGenero(),
                'edad'              => $intEdad->y,
                'codEmpresa'        => '10',
                'empresa'           => 'TELCONET S.A.',
                'telefono'          => $strConvencional,
                'celular'           => $strCelular,
                'correo'            => $strCorreo,
                'direccion'         => $objInfoPersona->getDireccion()
            );
        } 
        }
        catch( \Exception $e )
        {
            $strMensajeError = $e->getMessage();
            $strStatus       = "500";
        }
        $arrayRespuesta = array('error'  => $strMensajeError,
                                'datos'  => $arrayDatosEmpleado,
                                'status' => $strStatus);
        return $arrayRespuesta;
    }
}
