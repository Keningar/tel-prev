<?php

namespace telconet\tecnicoBundle\Service;

use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoDetalleInterface;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoDetalleHistorial;
use telconet\schemaBundle\Entity\InfoServicioTecnico;
use telconet\schemaBundle\Service\UtilService;
use telconet\schemaBundle\Entity\InfoDetalleElemento;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Clase donde se encuentra las funciones para gestionar el producto COU LINEAS TELEFONIA FIJA
 * 
 * @author John Vera <javera@telconet.ec>
 * @version 1.0 31-08-2018
 * 
 */

class InfoTelefoniaService 
{
    private $emComercial;
    private $emInfraestructura;
    private $emSoporte;
    private $emComunicacion;
    private $emSeguridad;
    private $emNaf;
    private $container;
    private $host;
    private $pathTelcos;
    private $pathParameters;
    private $serviceInfoElemento;
    private $serviceUtil;
                  
    public function __construct(Container $container)
    {
        $this->container            = $container;
        $this->emSoporte            = $this->container->get('doctrine')->getManager('telconet_soporte');
        $this->emInfraestructura    = $this->container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emSeguridad          = $this->container->get('doctrine')->getManager('telconet_seguridad');
        $this->emComercial          = $this->container->get('doctrine')->getManager('telconet');
        $this->emComunicacion       = $this->container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emNaf                = $this->container->get('doctrine')->getManager('telconet_naf');
        $this->host                 = $this->container->getParameter('host');
        $this->pathTelcos           = $this->container->getParameter('path_telcos');
        $this->pathParameters       = $this->container->getParameter('path_parameters');
        $this->serviceSoporte       = $this->container->get('soporte.SoporteService');
        $this->serviceInfoElemento  = $container->get('tecnico.InfoElemento');
        $this->envioPlantilla       = $container->get('soporte.EnvioPlantilla');
        $this->callGeneralWeb       = $container->get('tecnico.CallGeneralWeb');
        
    }
  
    
    /**
     * Función que setea las dependencias
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 19-11-2018 Se envia como parámetro el $container para que sea más  generico y se agregó un nuevo service cambioPlan
     */
    public function setDependencies(Container $container)
    {
        $this->servicioGeneral      = $container->get('tecnico.InfoServicioTecnico');
        $this->networkingScripts    = $container->get('tecnico.NetworkingScripts');
        $this->serviceUtil          = $container->get('schema.Util');
        $this->cambioElemento       = $container->get('tecnico.InfoCambioElemento');   
        $this->cambioPlan           = $container->get('tecnico.InfoCambiarPlan');  
    }
    
    
    
    
    /**
     * Función que permite consultar la información técnica de un producto de telefonía
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 31-08-2018
     */
    
    public function infoTecnicaTelefonia($arrayParametros)
    {

        $intIdServicio          = $arrayParametros['intIdServicio'];
        $strPlanTelefonia       = '';
        $strCategoriaTelefonia  = '';
        $strProveedor           = '';
       
        $strNombreElemento = 'N/A';
        $strModeloElemento = 'N/A';
        $strSerie          = 'N/A';
        $strMac            = 'N/A';

        $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
        
        if(is_object($objServicio))
        {

            $objSpc = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 'PLAN TELEFONIA', $objServicio->getProductoId());
            if(is_object($objSpc))
            {
                $strPlanTelefonia = $objSpc->getValor();
            }

            $objSpc = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 'CATEGORIAS TELEFONIA', $objServicio->getProductoId());
            if(is_object($objSpc))
            {
                $strCategoriaTelefonia = $objSpc->getValor();
            }

            $objSpc = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 'PROVEEDOR LINEAS TELEFONIA', $objServicio->getProductoId());
            if(is_object($objSpc))
            {
                $strProveedor = $objSpc->getValor(); 
            }

            $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($objServicio->getId());

            if(is_object($objServicioTecnico) && $objServicioTecnico->getElementoClienteId() > 0)
            {
                
                $intElemento = $objServicioTecnico->getElementoClienteId();

                $objElemento = $this->emComercial->getRepository('schemaBundle:InfoElemento')->find($intElemento);

                if(is_object($objElemento))
                {
                    $strNombreElemento = $objElemento->getNombreElemento();
                    $strSerie = $objElemento->getSerieFisica();

                    $objDetalleElemento = $this->emComercial->getRepository('schemaBundle:InfoDetalleElemento')
                                                            ->findOneBy(array('elementoId' => $objElemento->getId(),
                                                                              'detalleNombre' => 'MAC'));
                    if(is_object($objDetalleElemento))
                    {
                        $strMac = $objDetalleElemento->getDetalleValor();
                    }

                    if(is_object($objElemento->getModeloElementoId()))
                    {
                        $strModeloElemento = $objElemento->getModeloElementoId()->getNombreModeloElemento();
                    }
                }
                
            }
        }
        
        
        $arrayData = array('nombreElemento'      => $strNombreElemento,
                           'modeloElemento'      => $strModeloElemento,
                           'serie'               => $strSerie,
                           'mac'                 => $strMac,
                           'planTelefonia'       => $strPlanTelefonia,
                           'categoriaTelefonia'  => $strCategoriaTelefonia,
                           'proveedor'           => $strProveedor );
        
        return $arrayData;
    }
    
    
    /**
     * getParametrosLineas
     *
     * Método que obtiene los parámetros de las linea telefénicas, número y clave
     *
     * @return $arrayParametros[strMensaje, strNumeroTelefono, strContrasena, strDominio]
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 06-08-2018
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 22-11-2018 se agrega en el return el parámetro strDominio
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.2 22-11-2018 se agrega validación por empresa para MD
     */    
    
    
        public function getParametrosLineas($arrayParametros)
    {
        $strProvincia      = $arrayParametros['strProvincia'];
        $strPrefijoEmpresa = $arrayParametros['strPrefijoEmpresa'];
        $intCuentaNetvoice = $arrayParametros['intCuentaNetvoice'];
        $strCanton         = $arrayParametros['strCanton'];
        
        try
        {
            $strMensaje = 'OK';
            $strNumeroTelefono = '';

            //ingresar los numeros con sus respectivos, clave, dominio y canales, dependiendo de la categoria, agrupandolos todos bajo un 
            $objParametro = $this->emComercial->getRepository('schemaBundle:AdmiParametroCab')
                ->findOneBy(array("nombreParametro" => 'PARAMETROS_LINEAS_TELEFONIA',
                "estado" => 'Activo'));

            if(is_object($objParametro))
            {
                $objParametroPrefijo = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                    ->findOneBy(array("descripcion" => 'PREFIJOS_PROVINCIA',
                    "parametroId" => $objParametro->getId(),
                    'valor1' => $strProvincia,
                    "estado" => 'Activo'));

                if(is_object($objParametroPrefijo))
                {
                    $intPrefijoCiudad = $objParametroPrefijo->getValor2();
                }
            }

            $arrayParametroInfo['intPrefijoCiudad']  = $intPrefijoCiudad;
            $arrayParametroInfo['strPrefijoEmpresa'] = $strPrefijoEmpresa;
            $arrayParametroInfo['intCuentaNetvoice'] = $intCuentaNetvoice;
            $arrayParametroInfo['strCanton'] = $strCanton;

            $arrayRepuesta = $this->callGeneralWeb->getNumero($arrayParametroInfo);

            if($arrayRepuesta['mensaje'] != 'OK')
            {
                throw new \Exception($arrayRepuesta['mensaje']);
            }
            else
            {
                $strMensaje = 'OK';
                $strNumeroTelefono = $arrayRepuesta['numero'];
                
                //genero contraseña
                $strPass = substr(MD5(rand(5, 100) . microtime()), 0, 8);                
            }

        }
        catch(\Exception $ex)
        {
            $strMensaje = $ex->getMessage();
        }

        $arrayResult = array( 'strMensaje' => $strMensaje,
                              'strNumeroTelefono' => $strNumeroTelefono,
                              'strContrasena' => $strPass);


        return $arrayResult;
    }

    /**
     * asignarLinea
     *
     * Metodo que asigna la linea telefónica y la reserva
     *
     * $arrayPeticiones[ intIdServicio, strDominio, strUser, strIpClient ]
     * @return $arrayResult [status, mensaje, strTelefono, strContrasena ]
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 16-02-2017
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 22-11-2018 se agrega validación por empresa para MD
     */    
    
    public function asignarLineaTN($arrayPeticiones)
    {

        $intIdServicio  = $arrayPeticiones['intIdServicio'];
        $strDominio     = $arrayPeticiones['strDominio'];
        $intCanales     = $arrayPeticiones['intCanales'];
        $strUser        = $arrayPeticiones['strUser'];
        $strIpClient    = $arrayPeticiones['strIpClient'];
        $intCuentaNetvoice = $arrayPeticiones['intCuentaNetvoice'];
        $strPrefijoEmpresa= $arrayPeticiones['strPrefijoEmpresa'];
        
        $strContrasena  = '';
        $strTelefono    = '';
        
        $this->emComercial->getConnection()->beginTransaction();

        try
        {
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            
            if(is_object($objServicio))
            {
                //obtengo la provincia
                $arrayParametroInfo['intServicio']          = $objServicio->getId();
                $arrayParametroInfo['strPrefijoEmpresa']    = $strPrefijoEmpresa;
                
                $arrayInfo = $this->emComercial->getRepository("schemaBundle:InfoPersona")->getDataUsuario($arrayParametroInfo);
                
                if($arrayInfo[0]['provincia'])
                {
                    $arrayParametrosLinea['strProvincia'] = $arrayInfo[0]['provincia'];
                    $arrayParametrosLinea['strCanton']    = $arrayInfo[0]['canton'];
                }
                else
                {
                    $arrayResult = array("status" => "ERROR", "mensaje" => 'El Punto no tiene Provincia.');
                    return $arrayResult;
                }
                
                $arrayParametrosLinea['intCuentaNetvoice'] = $intCuentaNetvoice ;
                $arrayParametrosLinea['strPrefijoEmpresa'] = $strPrefijoEmpresa ;
                //obtengo numero y contraseña
                $arrayResultado = $this->getParametrosLineas($arrayParametrosLinea);


                if($arrayResultado['strMensaje'] == 'OK')
                {
                    $strContrasena  = $arrayResultado['strContrasena'] ;
                    $strTelefono    = $arrayResultado['strNumeroTelefono'] ;
                }
                else
                {
                    $arrayResult = array("status" => "ERROR", "mensaje" => $arrayResultado['strMensaje']);
                    return $arrayResult;                        
                }
                
                //consulto la categoria de la linea
                $objSpcCategoria = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 'CATEGORIAS TELEFONIA',
                                                                                             $objServicio->getProductoId());

                if(is_object($objSpcCategoria))
                {
                    $strCategoria = $objSpcCategoria->getValor();

                    //valido el numero de canales
                    if($intCanales < 2 || $strCategoria == 'FIJA ANALOGA' || $strCategoria == 'FIJA SMB')
                    {
                        $intCanales = 2;
                    }
                }

                $objProducto = $objServicio->getProductoId();
                
                if(!is_object($objProducto))
                {
                    $arrayResult = array("status" => "ERROR", "mensaje" => "No existe el producto de la orden de servicio.");
                    return $arrayResult;                    
                }
                
                $arrayParametros['objServicio'] = $objServicio;
                $arrayParametros['objProducto'] = $objProducto;
                $arrayParametros['strUser']     = $strUser ;
                                               
                if($strTelefono)
                {
                    $arrayParametros['strCaract']   = "NUMERO";
                    $arrayParametros['strValor']    = $strTelefono;
                    $arrayParametros['strEstado']   = 'Reservado';

                    $objSpcTelefono = $this->servicioGeneral
                                           ->insertServicioProductoCaracteristica($arrayParametros);
                    if(!is_object($objSpcTelefono))
                    {
                        $arrayResult = array("status" => "ERROR", "mensaje" => "No se ingresó el teléfono.");
                        return $arrayResult;   
                    }                    
                }                

                if($strContrasena)
                {
                    $arrayParametros['strEstado']   = 'Activo';
                    $arrayParametros['strCaract']   = "CLAVE";
                    $arrayParametros['strValor']    = $strContrasena;
                    $arrayParametros['intRefId']    = $objSpcTelefono->getId();

                    $objSpcClave = $this->servicioGeneral
                                        ->insertServicioProductoCaracteristica($arrayParametros);
                    if(!is_object($objSpcClave))
                    {
                        $arrayResult = array("status" => "ERROR", "mensaje" => "No se ingresó la clave.");
                        return $arrayResult;   
                    }
                }
                
                if($strDominio)
                {
                    $arrayParametros['strEstado']   = 'Activo';
                    $arrayParametros['strCaract']   = "DOMINIO";
                    $arrayParametros['strValor']    = $strDominio;
                    $arrayParametros['intRefId']    = $objSpcTelefono->getId();

                    $objSpcDominio = $this->servicioGeneral
                                          ->insertServicioProductoCaracteristica($arrayParametros);
                    if(!is_object($objSpcDominio))
                    {
                        $arrayResult = array("status" => "ERROR", "mensaje" => "No se ingresó el dominio.");
                        return $arrayResult;   
                    }                    
                }
                

                //solo si es TN valido que tenga número de canales

                if($intCanales)
                {                         
                    $arrayParametros['strEstado']   = 'Activo';
                    $arrayParametros['strCaract']   = "NUMERO CANALES";
                    $arrayParametros['strValor']    = $intCanales;
                    $arrayParametros['intRefId']    = $objSpcTelefono->getId();

                    $objSpcDominio = $this->servicioGeneral
                                          ->insertServicioProductoCaracteristica($arrayParametros);
                    if(!is_object($objSpcDominio))
                    {
                        $arrayResult = array("status" => "ERROR", "mensaje" => "No se ingresó el número de canales.");
                        return $arrayResult;   
                    }                   
                }
                               
            }
            else
            {
                $arrayResult = array("status" => "ERROR", "mensaje" => "No existe el servicio.");
                return $arrayResult;
            }           
            $this->emComercial->getConnection()->commit();

            
            $arrayResult = array("status" => "OK", "mensaje" => "OK", "strTelefono" => $strTelefono, "strContrasena" => $strContrasena, 
                                 'strDominio' => $strDominio, 'intNumero' => $objSpcTelefono->getId());
            
        }
        catch(\Exception $ex)
        {
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();

            }
            $arrayResult = array("status" => "ERROR", "mensaje" => $ex->getMessage());
            
            $this->serviceUtil->insertError('Telcos+', 
                                            'asignarLinea', 
                                            $ex->getMessage(), 
                                            $strUser, 
                                            $strIpClient);    
        }        
        
        return $arrayResult;
    }
    
    /**
     * solicitarFactibilidadTelefonia
     *
     * Método que asigna las líneas telefónicas
     *
     * $arrayPeticiones[ intServicio, strUser, strIp ]
     * @return $arrayResult [status, mensaje]
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 05-12-2018
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 19-11-2018 Se agrega a la solicitud el detalle de los números telefónicos para posteriormente usarlos en las tareas
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.2 13-02-2020 Se consulta si el procducto COU LINEAS TELEFONIA FIJA tiene la marca de activación simultánea para no grabar
     *                         la observación "Proceder a coordinar el servicio".
     * 
     */    
    
    public function solicitarFactibilidadTelefonia($arrayParametros)
    {
        $intServicio            = $arrayParametros['intServicio'];
        $strUser                = $arrayParametros['strUser'];
        $strIpClient            = $arrayParametros['strIp'];
        $strPrefijo             = $arrayParametros['strPrefijoEmpresa'];
        $boolProcesoActivacion  = $arrayParametros['boolProcesoActivacion'];
        $strActivaSim           = $arrayParametros['strActivaSim'];
                                
        try
        {

            $objServicio = $this->emComercial->getRepository("schemaBundle:InfoServicio")->find($intServicio);
            
            if(is_object($objServicio))
            {
                
                //obtener la caracteristica del producto para conocer que flujo seguir
                $objCaract = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 'CATEGORIAS TELEFONIA', $objServicio->getProductoId());
                
                if(is_object($objCaract))
                {
                    $strCategoria       = $objCaract->getValor();
                }
                else
                {
                    throw new \Exception('Servicio no tiene la característica CATEGORIAS TELEFONIA. ');                        
                }
                
                $objServicioCanal = $this->emComercial->getRepository("schemaBundle:InfoServicio")
                                                    ->findOneBy(array(  'puntoId' => $objServicio->getPuntoId(),
                                                                        'descripcionPresentaFactura' => 'CANAL TELEFONIA',
                                                                        'estado' => 'Activo'));

                if(is_object($objServicioCanal) || $boolProcesoActivacion || $strCategoria == 'FIJA SMB')
                {
                    $strEstadoServicio  = '';  

                    //llamo al webservice para generar el id de cuenta netvoice y guardarlo en la base de datos
                    $arrayParametroInfo['intServicio']          = $intServicio;
                    $arrayParametroInfo['strPrefijoEmpresa']    = $strPrefijo;

                    $intCuentaNetvoice = $this->callGeneralWeb->getCuentaNetvoice($arrayParametroInfo);

                    if(is_numeric($intCuentaNetvoice))
                    {

                        $arrayParametroInfo['intServicio']          = $objServicio->getId();
                        $arrayParametroInfo['intCuentaNetvoice']    = $intCuentaNetvoice;
                        $arrayParametroInfo['strPrefijoEmpresa']    = $strPrefijo;
                        $this->callGeneralWeb->asignarPlanNetvoice($arrayParametroInfo);

                    }
                    else
                    {
                        throw new \Exception('No pudo crear la cuenta netvoice '.$intCuentaNetvoice);
                    }                           

                    //ingresar los numeros con sus respectivos, clave, dominio y canales, dependiendo de la categoria, agrupandolos todos bajo un 
                    $objParametro = $this->emComercial->getRepository('schemaBundle:AdmiParametroCab')
                                                      ->findOneBy(array("nombreParametro" => 'PARAMETROS_LINEAS_TELEFONIA',
                                                                        "estado" => 'Activo'));

                    if(is_object($objParametro))
                    {
                        $objParametroDominio = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                                 ->findOneBy(array( "descripcion" => 'DOMINIO',
                                                                                    "parametroId" => $objParametro->getId(),
                                                                                    'valor1'      => $strCategoria,
                                                                                    "estado"      => 'Activo'));

                        if(is_object($objParametroDominio))
                        {
                            $strParametroDominio = $objParametroDominio->getValor2();
                        }
                    }

                    $objNumeroCanales = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 'NUMERO CANALES', $objServicio->getProductoId());

                    if(is_object($objNumeroCanales))
                    {
                        $intNumeroCanales = $objNumeroCanales->getValor();
                    }

                    $objCantidadLineas = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 'CANTIDAD LINEAS', $objServicio->getProductoId());

                    $strCuerpoCorreo = '<br> Se han asignado los siguientes números telefónicos: <br><br> ';


                    if(is_object($objCantidadLineas))
                    {
                        $intCantidadLineas = $objCantidadLineas->getValor();

                        for($i = 0; $i < $intCantidadLineas; $i++)
                        {
                            $arrayPeticiones['intIdServicio']     = $objServicio->getId();
                            $arrayPeticiones['strDominio']        = $strParametroDominio;
                            $arrayPeticiones['intCanales']        = $intNumeroCanales;
                            $arrayPeticiones['strUser']           = $strUser;
                            $arrayPeticiones['strPrefijoEmpresa'] = $strPrefijo;
                            $arrayPeticiones['strIpClient']       = $strIpClient;
                            $arrayPeticiones['intCuentaNetvoice'] = $intCuentaNetvoice;

                            $arrayResult = $this->asignarLineaTN($arrayPeticiones);

                            if($arrayResult['status'] != 'OK')
                            {
                                throw new \Exception($arrayResult['mensaje']);
                            }
                            else
                            {
                                $strCuerpoCorreo .= 'Número Telefónico: '.$arrayResult['strTelefono'].'  Clave: '.$arrayResult['strContrasena'].
                                                    '  Dominio: '.$arrayResult['strDominio'].'<br>' ;
                                
                                
                            }
                        }
                    }
                    
                    //se agrega al correo la ip del servicio de CANAL 
                    $objServicioEnlace = $this->emComercial->getRepository("schemaBundle:InfoServicio")
                                                            ->findOneBy(array(  'puntoId'                   => $objServicio->getPuntoId(), 
                                                                                'descripcionPresentaFactura'=> 'CANAL TELEFONIA',
                                                                                'estado'                    => 'Activo'     ));                    
                    
                   if(is_object($objServicioEnlace))
                    {                        
                        $objIp = $this->emComercial->getRepository("schemaBundle:InfoIp")->findOneBy(array('servicioId' => $objServicioEnlace->getId(), 
                                                                                                           'tipoIp'     => 'TELEFONIA'));
                        if(is_object($objIp))
                        {
                            $strCuerpoCorreo .= '<br> <br> IP LAN: '.$objIp->getIp();
                        }
                    }
                    
                    //agrego el modelo del equipo 
                    if($intCantidadLineas == 1)
                    {
                        $strModelo = 'HT801';
                    }
                    else if($intCantidadLineas <= 2)
                    {
                        $strModelo = 'HT812';
                    }
                    else if($intCantidadLineas <= 4)
                    {
                        $strModelo = 'HT814';
                    }
                    else if($intCantidadLineas <= 8)
                    {
                        $strModelo = 'GXW-4008';
                    }
                    
                    if($strModelo && $strCategoria == 'FIJA ANALOGA')
                    {
                        $strCuerpoCorreo .= '<br> <br> MODELO EQUIPO : '.$strModelo;
                    }
                    
                    
                    if($strCategoria == 'FIJA SIP TRUNK')
                    {
                        $strEstadoServicio = 'Asignada';
                    }
                    //se debe activar el servicio L3 y coordinar para que vayan a instalar el equipo gateway
                    else if($strCategoria == 'FIJA ANALOGA' || $strCategoria == 'FIJA SMB' )
                    {
                        if ($strActivaSim !== null && $strCategoria == 'FIJA SMB')
                        {
                            $strEstadoServicio = 'AsignadaSimultanea';
                        }
                        else
                        {
                            if ($strCategoria == 'FIJA ANALOGA')
                            {
                                //Preguntamos si es activación simultánea y consultamos el estado del servicio tradicional
                                $arrayCouSim          = $this->getIdTradInstSimCouTelefonia($intServicio);
                                $intIdServTradicional = $arrayCouSim[0];
                                if ($intIdServTradicional !== null)
                                {
                                    $strEstadoServicio = 'Asignada';
                                }
                                else
                                {
                                    $strObservacion = '<br><b>Proceder a coordinar el servicio.</b><br>';
                                    $strEstadoServicio = 'PrePlanificada';
                                }
                            }
                            else
                            {
                                $strObservacion = '<br><b>Proceder a coordinar el servicio.</b><br>';
                                $strEstadoServicio = 'PrePlanificada';
                            }
                        }
                        
                        $objTipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                            ->findOneByDescripcionSolicitud("SOLICITUD PLANIFICACION");

                        if(is_object($objTipoSolicitud))
                        {
                            $objSolicitud = new InfoDetalleSolicitud();
                            $objSolicitud->setServicioId($objServicio);
                            $objSolicitud->setTipoSolicitudId($objTipoSolicitud);
                            $objSolicitud->setEstado("PrePlanificada");
                            $objSolicitud->setObservacion($strCuerpoCorreo);
                            $objSolicitud->setUsrCreacion($strUser);
                            $objSolicitud->setFeCreacion(new \DateTime('now'));
                            $this->emComercial->persist($objSolicitud);
                            $this->emComercial->flush();

                            $objSolicitudHist = new InfoDetalleSolHist();
                            $objSolicitudHist->setDetalleSolicitudId($objSolicitud);
                            $objSolicitudHist->setIpCreacion($strIpClient);
                            $objSolicitudHist->setFeCreacion(new \DateTime('now'));
                            $objSolicitudHist->setUsrCreacion($strUser);
                            $objSolicitudHist->setEstado($strEstadoServicio);

                            $this->emComercial->persist($objSolicitudHist);
                            $this->emComercial->flush();
                        }
                    }

                    $arraySpc = array(  'objServicio'   => $objServicio,
                                        'objProducto'   => $objServicio->getProductoId(),
                                        'strCaract'     => 'ID CUENTA NETVOICE',                                    
                                        'strValor'      => $intCuentaNetvoice,
                                        'strUser'       => $strUser);

                    $this->servicioGeneral->insertServicioProductoCaracteristica($arraySpc);

                    $objServicio->setEstado($strEstadoServicio);

                    $this->emComercial->persist($objServicio);
                    $this->emComercial->flush();                    
       
                    
                    $objServicioHist = new InfoServicioHistorial();
                    $objServicioHist->setServicioId($objServicio);
                    $objServicioHist->setObservacion('Factibilidad al servicio ' . $strCategoria . ' a estado: '
                        . $strEstadoServicio . '. <br>'.$strCuerpoCorreo.$strObservacion);
                    $objServicioHist->setIpCreacion($strIpClient);
                    $objServicioHist->setFeCreacion(new \DateTime('now'));
                    $objServicioHist->setUsrCreacion($strUser);
                    $objServicioHist->setEstado($strEstadoServicio);

                    $this->emComercial->persist($objServicioHist);
                    $this->emComercial->flush();
                    
                    $strLogin   = $objServicio->getPuntoId()->getLogin();
                    $strAsunto  = 'Notificación de asignación de números telefónicos '.$strLogin;
                    //envio una notificacion con los numeros que se generaron
                    // Parametros a reemplazar en el cuerpo del correo
                    $arrayParametrosCorreo = array('cuerpoCorreo' => $strCuerpoCorreo);
                    //Llamada a la generacion del correo de notificacion y el envio del correo
                    $arrayTo = array_unique($arrayTo);
                    $this->envioPlantilla->generarEnvioPlantilla($strAsunto, $arrayTo, 'NOTI_GEN_TELEFO', $arrayParametrosCorreo, null, null, null);
                }
                else
                {
                    throw new \Exception('Debe tener un servicio Activo de canal de telefonía.');
                }
            }
        }
        catch(\Exception $ex)
        {
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            $this->emComercial->getConnection()->close();
            $arrayResult = array("status" => "ERROR", "mensaje" => $ex->getMessage());

            $this->serviceUtil->insertError('Telcos+', 'asignarLinea', $ex->getMessage(), $strUser, $strIpClient);
        }

        return $arrayResult;
    }
    
    
    /**
     * asignarLineaMD
     *
     * Método que reserva las líneas para megadatos
     *
     * $arrayPeticiones[ intServicio, strUser, strIp ]
     * @return $arrayResult [status, mensaje]
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 05-12-2018
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.1 19-06-2019 - Se modifica funcion para que en caso de que la respuesta del web service sea error, se lance una excepción
     *                           con el mensaje que retorna del web service.
     * 
     */    
    
    public function asignarLineaMD($arrayParametros)
    {
        $intServicio = $arrayParametros['intServicio'];
        $strUser = $arrayParametros['strUser'];
        $strIpClient = $arrayParametros['strIp'];
        $strPrefijo = $arrayParametros['strPrefijoEmpresa'];

        try
        {

            $objServicio = $this->emComercial->getRepository("schemaBundle:InfoServicio")->find($intServicio);

            if(is_object($objServicio))
            {

                //llamo al webservice para generar el id de cuenta netvoice y guardarlo en la base de datos
                $arrayParametroInfo['intServicio'] = $intServicio;
                $arrayParametroInfo['strPrefijoEmpresa'] = $strPrefijo;

                $intCuentaNetvoice = $this->callGeneralWeb->getCuentaNetvoice($arrayParametroInfo);

                if(is_numeric($intCuentaNetvoice))
                {
                    $arrayParametroInfo['intServicio']       = $objServicio->getId();
                    $arrayParametroInfo['intCuentaNetvoice'] = $intCuentaNetvoice;
                    $arrayParametroInfo['strPrefijoEmpresa'] = $strPrefijo;
                    $this->callGeneralWeb->asignarPlanNetvoice($arrayParametroInfo);
                }
                else
                {
                    /*Se devuelve el error que retorna del web service*/
                    throw new \Exception($intCuentaNetvoice);
                }

                $objParametro = $this->emComercial->getRepository('schemaBundle:AdmiParametroCab')
                                                    ->findOneBy(array("nombreParametro" => 'PARAMETROS NETVOICE',
                                                                      "estado"          => 'Activo'));
                if(is_object($objParametro))
                {
                    $objParametroDominio = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                             ->findOneBy(array( "descripcion" => 'DOMINIO',
                                                                                "parametroId" => $objParametro->getId(),
                                                                                "estado"      => 'Activo'));

                    if(is_object($objParametroDominio))
                    {
                        $strParametroDominio = $objParametroDominio->getValor1();
                    }
                }

                $strCuerpoCorreo = '<br> Se han asignado los siguientes números telefónicos: <br><br> ';

                $arrayPeticiones['intIdServicio']       = $objServicio->getId();
                $arrayPeticiones['strDominio']          = $strParametroDominio;
                $arrayPeticiones['intCanales']          = 2;
                $arrayPeticiones['strUser']             = $strUser;
                $arrayPeticiones['strPrefijoEmpresa']   = $strPrefijo;
                $arrayPeticiones['strIpClient']         = $strIpClient;
                $arrayPeticiones['intCuentaNetvoice']   = $intCuentaNetvoice;

                $arrayResult = $this->asignarLineaTN($arrayPeticiones);

                if($arrayResult['status'] != 'OK')
                {
                    throw new \Exception($arrayResult['mensaje']);
                }
                else
                {
                    
                    $strCuerpoCorreo .= 'Número Telefónico: ' . $arrayResult['strTelefono'] . '  Clave: ' . $arrayResult['strContrasena'] .
                        '  Dominio: ' . $arrayResult['strDominio'] . '<br>';
                }


                $arraySpc = array(  'objServicio'   => $objServicio,
                                    'objProducto'   => $objServicio->getProductoId(),
                                    'strCaract'     => 'ID CUENTA NETVOICE',
                                    'strValor'      => $intCuentaNetvoice,
                                    'strUser'       => $strUser);

                $objSpcCuenta = $this->servicioGeneral->insertServicioProductoCaracteristica($arraySpc);

                if(!is_object($objSpcCuenta))
                {
                    throw new \Exception('No se agregó el id cuenta netvoice al servicio.');
                }

                $objServicioHist = new InfoServicioHistorial();
                $objServicioHist->setServicioId($objServicio);
                $objServicioHist->setObservacion($strCuerpoCorreo);
                $objServicioHist->setIpCreacion($strIpClient);
                $objServicioHist->setFeCreacion(new \DateTime('now'));
                $objServicioHist->setUsrCreacion($strUser);
                $objServicioHist->setEstado('PrePlanificada');

                $this->emComercial->persist($objServicioHist);
                $this->emComercial->flush();

                $strLogin = $objServicio->getPuntoId()->getLogin();
                $strAsunto = 'Notificación de asignación de números telefónicos ' . $strLogin;
                //envio una notificacion con los numeros que se generaron
                // Parametros a reemplazar en el cuerpo del correo
                $arrayParametrosCorreo = array('cuerpoCorreo' => $strCuerpoCorreo);
                //Llamada a la generacion del correo de notificacion y el envio del correo
                $arrayTo = array_unique($arrayTo);
                $this->envioPlantilla->generarEnvioPlantilla($strAsunto, $arrayTo, 'NOTI_GEN_TELEFO', $arrayParametrosCorreo, null, null, null);
            }

        }
        catch(\Exception $ex)
        {
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();

            }
            $arrayResult = array("status" => "ERROR", "mensaje" => $ex->getMessage());

            $this->serviceUtil->insertError('Telcos+', 'asignarLinea', $ex->getMessage(), $strUser, $strIpClient);
        }
        

        return $arrayResult;
    }

    /**
     * activarLineasTelefonicas
     *
     * Metodo que obtiene los datos de las lineas telefonicas
     *
     * $arrayPeticiones[ intIdServicio, strTelefono, strDominio, strUser, strIpClient ]
     * 
     * @return $arrayResult [status, mensaje]
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 24-06-2018
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 14-11-2018 se realiza validación para que el ancho de banda dependa del numero de canales de cada linea
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.2 19-12-2018 se aumentó parámetro de empresa para validación en el caso de MD
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.3 2-12-2019 Se aumentó parámetro de acción confirmarServicio para que se grabe en la InfoServicioHistorial
     *                        al realizar la activación.
     */     
    
    public function activarLineasTelefonicas($arrayPeticiones)
    {
        $intIdServicio      = $arrayPeticiones['intIdServicio'];
        $strSerie           = $arrayPeticiones['strSerie'];
        $strMac             = $arrayPeticiones['strMac'];
        $strModelo          = $arrayPeticiones['strModelo'];
        $strObservacion     = $arrayPeticiones['strObservacion'];
        $strUser            = $arrayPeticiones['strUser'];
        $strIpClient        = $arrayPeticiones['strIpClient'];
        $intIdEmpresa       = $arrayPeticiones['intCodEmpresa'];
        $strPrefijoEmpresa  = $arrayPeticiones['strPrefijoEmpresa'];
        $boolTieneEquipo    = false;
        $intIdAccion        = '847';
        $objAccion          = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($intIdAccion);

        try
        {
            $this->emComercial->getConnection()->beginTransaction();
            $this->emInfraestructura->getConnection()->beginTransaction();
        
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            if(!$objServicio)
            {
                throw new \Exception('No existe el servicio.');
            }

            $objProducto = $objServicio->getProductoId();

            if(!$objProducto)
            {
                throw new \Exception('No existe el producto.');
            }
            
            $objCaract = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 'CATEGORIAS TELEFONIA', $objServicio->getProductoId());

            if(is_object($objCaract))
            {
                $strCategoria = $objCaract->getValor();
                //Va directamente a asignado tarea porque no se debe activar ningun servicio adicional
                if($strCategoria == 'FIJA ANALOGA')
                {
                    $boolTieneEquipo = true;
                }
            }
            
            $this->servicioGeneral->generarLoginAuxiliar($intIdServicio);

            //verifico que exista un producto canales telefonía activo

            $objServicioCanal = $this->emComercial->getRepository("schemaBundle:InfoServicio")
                                                  ->findOneBy(array('puntoId' => $objServicio->getPuntoId(),
                                                                    'descripcionPresentaFactura' => 'CANAL TELEFONIA',
                                                                    'estado' => 'Activo'));

            if(is_object($objServicioCanal))
            {
                //si existe ingreso la caracteristica a este servicio de ENLACE DE DATOS
                
                $arraySpc = array(  'objServicio'   => $objServicio,
                                    'objProducto'   => $objProducto,
                                    'strCaract'     => 'ENLACE_DATOS',                                    
                                    'strValor'      => $objServicioCanal->getId(),
                                    'strUser'       => $strUser);
                
                $this->servicioGeneral->insertServicioProductoCaracteristica($arraySpc);
                
            }
            else
            {
                if($strCategoria != 'FIJA SMB' && $strPrefijoEmpresa == 'TN' )
                {                
                    throw new \Exception('No tiene servicio CANAL TELEFONIA en estado Activo.');
                }
            }
            
            //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
            $entityServicioHist = new InfoServicioHistorial();
            $entityServicioHist->setServicioId($objServicio);
            $entityServicioHist->setIpCreacion($strIpClient);
            $entityServicioHist->setFeCreacion(new \DateTime('now'));
            $entityServicioHist->setUsrCreacion($strUser);
            $entityServicioHist->setEstado('Activo');

            $entityServicioHist->setObservacion('Se activo el servicio.');
            $entityServicioHist->setAccion($objAccion->getNombreAccion());

            $this->emComercial->persist($entityServicioHist);
            $this->emComercial->flush();

            $arrayParams['intServicio'] = $objServicio->getId();

            $arrayLineas = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')->getLineasTn($arrayParams);            

            foreach($arrayLineas as $arrayLinea)
            {   
                
                $strNumeros .= $arrayLinea['caractNumero'].', ';
                
                if($arrayLinea['idNumeroCanales'] > 0)
                {
                    //voy sumando todos los canales por cada linea para subir el bw
                    $objSpcCanales = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')->find($arrayLinea['idNumeroCanales']);
                    if(is_object($objSpcCanales))
                    {
                        $intTotalCanales =  $intTotalCanales + $objSpcCanales->getValor();
                    }
                }

                $arrayActivarNumero['intSpc']               = $arrayLinea['idNumero'];
                $arrayActivarNumero['strPrefijoEmpresa']    = $strPrefijoEmpresa;
                //envio el numero a activar y el puerto que va a ocupar
                $strResult  = $this->activarNumero($arrayActivarNumero);
                
                if($strResult['status'] != 'OK')
                {
                    throw new \Exception('Error '.$strResult['mensaje']);
                }

            }
            
            if($strCategoria != 'FIJA SMB' && $strPrefijoEmpresa == 'TN')
            {
                //cambio el anchos de banda de los concentradores
                $arrayCambioBw = array();

                $arrayCambioBw['objServicio']       = $objServicio;
                $arrayCambioBw['intCapacidadNueva'] = $intTotalCanales * 100;
                $arrayCambioBw['strOperacion']      = '+';
                $arrayCambioBw['usrCreacion']       = $strUser;
                $arrayCambioBw['ipCreacion']        = $strIpClient;

                $arrayCambio = $this->cambioAnchoBanda($arrayCambioBw);

                if($arrayCambio['status'] == 'ERROR')
                {
                    throw new \Exception($arrayCambio['mensaje']);
                }
            }
          
            // fin activar linea telefonica
            if($boolTieneEquipo)
            {
                //buscar elemento cpe
                $arrayNafCpe = $this->servicioGeneral->buscarElementoEnNaf($strSerie, $strModelo, "PI", "ActivarServicio");
                $strStatusNaf = $arrayNafCpe[0]['status'];
                $strMensajeNaf = $arrayNafCpe[0]['mensaje'];
                if($strStatusNaf == "OK")
                {
                    //actualizamos registro en el naf del cpe
                    $arrayParametrosNaf = array('tipoArticulo'          => 'AF',
                                                'identificacionCliente' => '',
                                                'empresaCod'            => '',
                                                'modeloCpe'             => $strModelo,
                                                'serieCpe'              => $strSerie,
                                                'cantidad'              => '1');

                    $strMensaje = $this->cambioElemento->procesaInstalacionElemento($arrayParametrosNaf);

                    if(strlen(trim($strMensaje)) > 0)
                    {
                        $arrayRespuestaFinal[] = array("status" => "NAF", "mensaje" => "ERROR WIFI NAF: " . $strMensaje);
                        return $arrayRespuestaFinal;
                    }
                }
                else
                {
                    throw new \Exception($strMensajeNaf);
                }                                 
            
                //crear login aux

                $objInterfaceOnt = $this->servicioGeneral->ingresarElementoCliente( $objServicio->getLoginAux(), 
                                                                                    $strSerie, 
                                                                                    $strModelo, 
                                                                                    "-gateway",
                                                                                    '', //$objInterfaceConector, 
                                                                                    'UTP', 
                                                                                    $objServicio, 
                                                                                    $strUser, 
                                                                                    $strIpClient, 
                                                                                    $intIdEmpresa);
                //ocupo el puerto IN
                if(is_object($objInterfaceOnt))
                {
                    $objInterfaceOnt->setEstado('connected');
                    $this->emInfraestructura->persist($objInterfaceOnt);
                    $this->emInfraestructura->flush();
                }
                
                $intElemento = $objInterfaceOnt->getElementoId()->getId();
                
                //ocupo los puertos en el equipo segun la cantidad de los números
                for($i = 0; $i < count($arrayLineas); $i++)                
                {
                    $objInterface = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                        ->findOneBy(array('elementoId' => $intElemento,
                                                          'estado'     => "not connect"));

                    if(is_object($objInterface))
                    {
                        $objInterface->setEstado('connected');
                        $this->emInfraestructura->persist($objInterface);
                        $this->emInfraestructura->flush();
                    }
                }

                //ingreso la mac como detalle del elemento
                $objInfoDetalleElemento = new InfoDetalleElemento();
                $objInfoDetalleElemento->setElementoId($objInterfaceOnt->getElementoId()->getId());
                $objInfoDetalleElemento->setDetalleNombre('MAC');
                $objInfoDetalleElemento->setDetalleValor($strMac);
                $objInfoDetalleElemento->setDetalleDescripcion('MAC DEL EQUIPO');
                $objInfoDetalleElemento->setEstado("Activo");
                $objInfoDetalleElemento->setUsrCreacion($strUser);
                $objInfoDetalleElemento->setIpCreacion($strIpClient);
                $objInfoDetalleElemento->setFeCreacion(new \DateTime('now'));
                $this->emInfraestructura->persist($objInfoDetalleElemento);
                $this->emInfraestructura->flush();
                
                $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                        ->findOneBy(array("servicioId" => $objServicio->getId()));

                if($objServicioTecnico)
                {
                    $objTipoMedio = $this->emComercial->getRepository('schemaBundle:AdmiTipoMedio')->findOneByCodigoTipoMedio('UTP');
                                    
                    if(is_object($objTipoMedio))
                    {
                        $objServicioTecnico->setUltimaMillaId($objTipoMedio->getId());
                    }
                    
                    //guardar ont en servicio tecnico
                    $objServicioTecnico->setElementoClienteId($objInterfaceOnt->getElementoId()->getId());
                    $objServicioTecnico->setInterfaceElementoClienteId($objInterfaceOnt->getId());
                    $this->emComercial->persist($objServicioTecnico);
                    $this->emComercial->flush();
                }
                
                $strObservacion = '<br> <b>Modelo:</b> ' . $strModelo
                . '<br> <b>Serie:</b> ' . $strSerie
                . '<br> <b>Mac:</b> ' . $strMac
                . '<br> <b>Observación:</b> ' . $strObservacion;
            
            }           
            
            //creo una colicitud de retiro de equipo
            $objTipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                  ->findOneBy(array("descripcionSolicitud" => "SOLICITUD PLANIFICACION", "estado" => "Activo"));
            
            if(!is_object($objTipoSolicitud))
            {
                throw new \Exception('No existe el tipo SOLICITUD PLANIFICACION. ');
            }

            $objDetalleSol = $this->emComercial ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                ->findOneBy(array(  'servicioId'        => $objServicio->getId(), 
                                                                    'tipoSolicitudId'   => $objTipoSolicitud->getId()));

            if(is_object($objDetalleSol))
            {
                $objDetalleSol->setEstado('Finalizada');
                $this->emComercial->persist($objDetalleSol);
                $this->emComercial->flush();

                //crear historial para la solicitud
                $objHistorialSolicitud = new InfoDetalleSolHist();
                $objHistorialSolicitud->setDetalleSolicitudId($objDetalleSol);
                $objHistorialSolicitud->setEstado("Finalizada");
                $objHistorialSolicitud->setObservacion("Finalización de solicitud por activación.");
                $objHistorialSolicitud->setUsrCreacion($strUser);
                $objHistorialSolicitud->setFeCreacion(new \DateTime('now'));
                $objHistorialSolicitud->setIpCreacion($strIpClient);
                $this->emComercial->persist($objHistorialSolicitud);
                $this->emComercial->flush();
            }

            
            $strLogin   = $objServicio->getPuntoId()->getLogin();
            $strAsunto  = 'Notificación de activación de números telefónicos '.$strLogin;
            //envio una notificacion con los numeros que se generaron
            // Parametros a reemplazar en el cuerpo del correo
            $arrayParametrosCorreo = array('cuerpoCorreo' => ' Se han activado los números: '.$strNumeros);
            //Llamada a la generacion del correo de notificacion y el envio del correo
            $arrayTo = array_unique($arrayTo);
            $this->envioPlantilla->generarEnvioPlantilla($strAsunto, $arrayTo, 'NOTI_GEN_TELEFO'
                                                         , $arrayParametrosCorreo, null, null, null);            
            
            $objServicio->setEstado('Activo');
            $this->emComercial->persist($objServicio);
            $this->emComercial->flush();

            $this->emInfraestructura->getConnection()->commit();
            $this->emComercial->getConnection()->commit();
        }
        catch(\Exception $ex)
        {
            $arrayResult = array("status" => "ERROR", "mensaje" => $ex->getMessage());

            return $arrayResult;
        }

        $arrayResult = array("status" => "OK", "mensaje" => "OK");

        return $arrayResult;
    }
    
    /**
     * activarLineasTelefonicas
     *
     * Metodo que activa el número de netvoice
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 19-12-2018 se aumentó parámetro de empresa para validación en el caso de MD
     */  

    
    public function activarNumero($arrayParam)
    {
                
        $intSpc             = $arrayParam['intSpc'];
        $intElemento        = $arrayParam['intElemento'];
        $strPrefijoEmpresa  = $arrayParam['strPrefijoEmpresa'];
        
        try
        {
            
            $this->emComercial->getConnection()->beginTransaction();
            $this->emInfraestructura->getConnection()->beginTransaction();            
            
            //activar linea telefonica inicio
            $objSpcNumero = $this->emComercial->getRepository("schemaBundle:InfoServicioProdCaract")->find($intSpc);

            if(is_object($objSpcNumero))
            {     
                $strNumero = $objSpcNumero->getValor();
                
                $objServicio = $this->emComercial->getRepository("schemaBundle:InfoServicio")->find($objSpcNumero->getServicioId());
                
                if(is_object($objServicio))
                {
                    
                    $arraySpc  = array('objServicio' => $objServicio, 'strCaracteristica' => 'ID CUENTA NETVOICE');
                    $objSpcCuenta = $this->servicioGeneral->obtenerServicioProductoCaracteristica($arraySpc);
                    
                    if(is_object($objSpcCuenta))
                    {
                        $intCuenta = $objSpcCuenta->getValor();
                    }
                    else
                    {
                        throw new \Exception('No existe cuenta del número '.$objSpcNumero->getValor());
                    }
                    
                    $arraySpc  = array('objServicio' => $objServicio, 'strCaracteristica' => 'CLAVE', 'intRefId' => $intSpc );
                    $objSpcClave = $this->servicioGeneral->obtenerServicioProductoCaracteristica($arraySpc); 
                    
                    if(is_object($objSpcClave))
                    {
                        $strClave = $objSpcClave->getValor();
                    }
                    else
                    {
                        throw new \Exception('No existe clave del número '.$strNumero);
                    }                    
                    
                    $arraySpc  = array('objServicio' => $objServicio, 'strCaracteristica' => 'NUMERO CANALES', 'intRefId' => $intSpc);
                    $objSpcCanales = $this->servicioGeneral->obtenerServicioProductoCaracteristica($arraySpc); 
                    
                    if(is_object($objSpcCanales))
                    {
                        $intCanales = $objSpcCanales->getValor();
                    }
                    else
                    {
                        throw new \Exception('No existe canales del número '.$objSpcNumero->getValor());
                    }
                    $arraySpc  = null;
                    $arraySpc  = array('objServicio' => $objServicio, 'strCaracteristica' => 'CATEGORIAS TELEFONIA');
                    $objSpcCategoria = $this->servicioGeneral->obtenerServicioProductoCaracteristica($arraySpc); 
                    
                   if(is_object($objSpcCategoria))
                    {
                        $strCategoria = $objSpcCategoria->getValor();
                    }
                    else
                    {
                        if($strPrefijoEmpresa == 'TN')
                        {
                            throw new \Exception('No existe categoría del número '.$objSpcNumero->getValor());
                        }
                    }                    
                    
                }
                
                $strChecksbc    = '';
                $strForwardtime = '';                
                
                if($strCategoria == 'FIJA SMB')
                {
                    $strChecksbc = '1';
                }
                if($strPrefijoEmpresa == 'MD')
                {
                    $strChecksbc    = '1';
                    $strForwardtime = '5';                    
                }
                
                $arrayParamActivar = array('intCuentaNetvoice'  => $intCuenta , 
                                           'strClave'           =>  $strClave, 
                                           'intCanales'         => $intCanales,
                                           'intNumero'          => $strNumero,
                                           'checksbc'           => $strChecksbc, 
                                           'forwardtime'        => $strForwardtime);
                
                //llamo al webservice
                $strResult = $this->callGeneralWeb->activarLineaNetvoice($arrayParamActivar);   
                
                if($strResult != 'OK')
                {
                    throw new \Exception('Error: '.$strResult);
                }
                
                if($strCategoria == 'FIJA SIP TRUNK')
                {
                    //obtengo la Ip del servicio CANAL TELEFONIA
                    $arraySpc  = null;
                    $arraySpc  = array('objServicio' => $objServicio, 'strCaracteristica' => 'ENLACE_DATOS');
                    $objSpcEnlace = $this->servicioGeneral->obtenerServicioProductoCaracteristica($arraySpc); 
                    
                   if(is_object($objSpcEnlace))
                    {
                        
                        $objIp = $this->emComercial->getRepository("schemaBundle:InfoIp")->findOneBy(array('servicioId' => $objSpcEnlace->getValor(), 
                                                                                                           'tipoIp'     => 'TELEFONIA'));
                        if(!is_object($objIp))
                        {
                            throw new \Exception('No existe ip para telefonía.');
                        }
                        else
                        {
                            $strIp = $objIp->getIp();
                        }

                    }
                    else
                    {
                        throw new \Exception('No existe el ENLACE_DATOS del número: '.$objSpcNumero->getValor());
                    }                       
                    
                    $arrayParamConfig = array('intNumero'   => $strNumero , 
                                              'strAccion'   => 'SET', 
                                              'strIp'       => $strIp);

                    //llamo al webservice
                    $strResultTroncal = $this->callGeneralWeb->configuracionTroncal($arrayParamConfig);                

                    if($strResultTroncal != 'OK')
                    {
                        throw new \Exception('Error: '.$strResultTroncal);
                    }
                }
                
                $objSpcNumero->setEstado('Activo');
                $this->emComercial->persist($objSpcNumero);
                $this->emComercial->flush();      
                
                if($intElemento > 0)
                {

                    $objInterface = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                            ->findOneBy(array('elementoId' => $intElemento,
                                                                              'estado'     => "not connect"));

                    if(is_object($objInterface))
                    {
                        $objInterface->setEstado('connected');
                        $this->emInfraestructura->persist($objInterface);
                        $this->emInfraestructura->flush();
                    }      
                }

            }
        }
        catch(\Exception $ex)
        {
            $this->emInfraestructura->getConnection()->rollback();
            $this->emComercial->getConnection()->rollback();
            $arrayResult = array("status" => "ERROR", "mensaje" => $ex->getMessage());
            return $arrayResult;
        }
        
        $this->emInfraestructura->getConnection()->commit();
        $this->emComercial->getConnection()->commit();
        
        $arrayResult = array("status" => "OK", "mensaje" => 'OK');
        return $arrayResult; 
    }
    

    /**
     * cortarServicio
     * Función que corta el servicio al cliente
     *
     * @params $arrayPeticiones [$idEmpresa, $prefijoEmpresa, $idServicio, $idProducto, $idAccion,  $strUser,  $strIp]
     * 
     * @return $arrayResult [status, mensaje]
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 10-01-2018 se aumenta notificación por correo para indicar el cambio de estado de la línea telefónica
     * 
     * 
     */
    
    public function cortarLinea($arrayPeticiones)
    {
        //*OBTENCION DE PARAMETROS-----------------------------------------------*/
        $strUser        = $arrayPeticiones['strUser'];
        $strIpCreacion  = $arrayPeticiones['strIpClient'];
        $intIdServicio  = $arrayPeticiones['intIdServicio'];

        //*---------------------------------------------------------------------*/
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        //LOGICA DE NEGOCIO-----------------------------------------------------
        try
        {          
            
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);

            if(!is_object($objServicio))
            {
                throw new \Exception('No existe el servicio.');
            }
            
            
            $objSpcCuenta = $this->servicioGeneral
                                 ->getServicioProductoCaracteristica($objServicio, 'ID CUENTA NETVOICE', $objServicio->getProductoId());
            
            if(is_object($objSpcCuenta))
            {
                $intCuentaNetvoice = $objSpcCuenta->getValor();
            }
            else
            {
                throw new \Exception('No hay cuenta de netvoice.');
            }
            
            //obtengo la caracteristica
            $objCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                   ->findOneBy(array('descripcionCaracteristica' => 'NUMERO', 'estado' => 'Activo'));
            
            if(is_object($objCaracteristica))
            {
                
                $objProdCaract = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                      ->findOneBy(array('productoId'        => $objServicio->getProductoId(), 
                                                        'caracteristicaId'  => $objCaracteristica->getId())); 
                
                if(is_object($objProdCaract))
                {
                     $objSpcNumeros = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                        ->findBy(array('productoCaracterisiticaId' => $objProdCaract->getId(), 
                                                                       'servicioId' => $intIdServicio,
                                                                       'estado' => 'Activo'));
                     
                    foreach($objSpcNumeros as $objNumero)
                    {
                        $strNumeros .= $objNumero->getValor().', ';
                        //se debe cancelar a nivel de equipo medianto los service
                        
                        $arrayParametroInfo['intCuentaNetvoice'] = $intCuentaNetvoice;
                        $arrayParametroInfo['intNumero']         = $objNumero->getValor();
                        $arrayParametroInfo['strStatus']         = 'Suspended';

                        $strMensaje = $this->callGeneralWeb->cambiarEstadoNumero($arrayParametroInfo);

                        if($strMensaje != 'OK')
                        {
                            throw new \Exception('Error número: '.$objNumero->getValor().' - '.$strMensaje);
                        }                        

                        $objNumero->setEstado('In-Corte');
                        $this->emComercial->persist($objNumero);
                        $this->emComercial->flush();
                    }
                     
                }
                
           
            }            
            
    
            
            $objServicio->setEstado('In-Corte');
            $this->emComercial->persist($objServicio);
            $this->emComercial->flush();            
            
            //historial del servicio
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion('Se cortó el Servicio de las llamadas salientes');
            $objServicioHistorial->setEstado($objServicio->getEstado());
            $objServicioHistorial->setUsrCreacion($strUser);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($strIpCreacion);
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();
            
            $strLogin   = $objServicio->getPuntoId()->getLogin();
            $strAsunto  = 'Notificación de corte de números telefónicos '.$strLogin;
            //envio una notificacion con los numeros que se generaron
            // Parametros a reemplazar en el cuerpo del correo
            $arrayParametrosCorreo = array('cuerpoCorreo' => ' Se han cortado los números: <br>'.$strNumeros);
            //Llamada a la generacion del correo de notificacion y el envio del correo
            $arrayTo = array_unique($arrayTo);
            $this->envioPlantilla->generarEnvioPlantilla($strAsunto, $arrayTo, 'NOTI_GEN_TELEFO'
                                                         , $arrayParametrosCorreo, null, null, null);              
            
            $strMensaje = 'OK';
            $strStatus = "OK";
            

        }
        catch(\Exception $e)
        {

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            $arrayFinal = array('status' => "ERROR", 'mensaje' => $e->getMessage());
            return $arrayFinal;
        }

        //*---------------------------------------------------------------------*/
        //*DECLARACION DE COMMITS*/

        if($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }

        $this->emComercial->getConnection()->close();
        //*----------------------------------------------------------------------*/

        $arrayFinal = array('status' => $strStatus, 'mensaje' => $strMensaje);

        return $arrayFinal;
    }
    
    /**
     * reconectarServicio
     * Función que reconecta el servicio al cliente
     *
     * @params $arrayPeticiones [idNumero, strUser, strIpClient]
     * 
     * @return $arrayResult [status, mensaje]
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 01-08-2018
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 10-01-2018 se aumenta notificación por correo para indicar el cambio de estado de la línea telefónica
     * 
     */
    
    public function reconectarLinea($arrayPeticiones)
    {
        $strUser        = $arrayPeticiones['strUser'];
        $strIpCreacion  = $arrayPeticiones['strIpClient'];
        $intIdServicio  = $arrayPeticiones['intIdServicio'];

        //*---------------------------------------------------------------------*/
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        //LOGICA DE NEGOCIO-----------------------------------------------------
        try
        {          
            
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);

            if(!is_object($objServicio))
            {
                throw new \Exception('No existe el servicio.');
            }
            
            
            $objSpcCuenta = $this->servicioGeneral
                                 ->getServicioProductoCaracteristica($objServicio, 'ID CUENTA NETVOICE', $objServicio->getProductoId());
            
            if(is_object($objSpcCuenta))
            {
                $intCuentaNetvoice = $objSpcCuenta->getValor();
            }
            else
            {
                throw new \Exception('No hay cuenta de netvoice.');
            }
            
            //obtengo la caracteristica
            $objCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                   ->findOneBy(array('descripcionCaracteristica' => 'NUMERO', 'estado' => 'Activo'));
            
            if(is_object($objCaracteristica))
            {
                
                $objProdCaract = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                      ->findOneBy(array('productoId'        => $objServicio->getProductoId(), 
                                                        'caracteristicaId'  => $objCaracteristica->getId())); 
                
                if(is_object($objProdCaract))
                {
                     $objSpcNumeros = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                        ->findBy(array('productoCaracterisiticaId' => $objProdCaract->getId(), 
                                                                       'servicioId' => $intIdServicio,
                                                                       'estado' => 'In-Corte'));
                     
                    foreach($objSpcNumeros as $objNumero)
                    {
                        $strNumeros .= $objNumero->getValor().', ';

                        //se debe cancelar a nivel de equipo medianto los service
                        
                        $arrayParametroInfo['intCuentaNetvoice'] = $intCuentaNetvoice;
                        $arrayParametroInfo['intNumero']         = $objNumero->getValor();
                        $arrayParametroInfo['strStatus']         = 'Enabled';

                        $strMensaje = $this->callGeneralWeb->cambiarEstadoNumero($arrayParametroInfo);

                        if($strMensaje != 'OK')
                        {
                            throw new \Exception('Error número: '.$objNumero->getValor().' - '.$strMensaje);
                        }                        

                        $objNumero->setEstado('Activo');
                        $this->emComercial->persist($objNumero);
                        $this->emComercial->flush();
                    }
                     
                }
            }            
               
            
            $objServicio->setEstado('Activo');
            $this->emComercial->persist($objServicio);
            $this->emComercial->flush();    
            
            //historial del servicio
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion("Se reactivó el servicio de las llamadas salientes. ");
            $objServicioHistorial->setEstado($objServicio->getEstado());
            $objServicioHistorial->setUsrCreacion($strUser);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($strIpCreacion);
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();
            
            $strLogin   = $objServicio->getPuntoId()->getLogin();
            $strAsunto  = 'Notificación de reactivación de números telefónicos '.$strLogin;
            //envio una notificacion con los numeros que se generaron
            // Parametros a reemplazar en el cuerpo del correo
            $arrayParametrosCorreo = array('cuerpoCorreo' => ' Se han reactivado los números: <br>'.$strNumeros);
            //Llamada a la generacion del correo de notificacion y el envio del correo
            $arrayTo = array_unique($arrayTo);
            $this->envioPlantilla->generarEnvioPlantilla($strAsunto, $arrayTo, 'NOTI_GEN_TELEFO'
                                                         , $arrayParametrosCorreo, null, null, null);                  
            
            $strMensaje = 'OK';
            $strStatus = "OK";
            

        }
        catch(\Exception $e)
        {

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            $arrayFinal = array('status' => "ERROR", 'mensaje' => $e->getMessage());
            return $arrayFinal;
        }

        //*---------------------------------------------------------------------*/
        //*DECLARACION DE COMMITS*/

        if($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }

        $this->emComercial->getConnection()->close();
        //*----------------------------------------------------------------------*/

        $arrayFinal = array('status' => $strStatus, 'mensaje' => $strMensaje);

        return $arrayFinal;
    }
    
    /**
     * envioDetalleLlamada
     * Función que envía el detalle de las llamadas
     *
     * @params $arrayPeticiones [idNumero, strCorreo, strUser, fechaInicio, fechaFin, strIpClient]
     * 
     * @return $arrayResult [status, mensaje]
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 01-08-2018
     * 
     */
        
    public function envioDetalleLlamada($arrayPeticiones)
    {
        
        $intSpc         = $arrayPeticiones['idNumero'];
        $strCorreo      = $arrayPeticiones['strCorreo'];
        $strDesde       = $arrayPeticiones['fechaInicio'];
        $strHasta       = $arrayPeticiones['fechaFin'];
        $strUser        = $arrayPeticiones['strUser'];
        $strIpCreacion  = $arrayPeticiones['strIpClient'];
        //*---------------------------------------------------------------------*/
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        //LOGICA DE NEGOCIO-----------------------------------------------------
        try
        {           

            $objSpc = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')->find($intSpc);

            if(!is_object($objSpc))
            {
                throw new \Exception('No existe el número.');
            }


            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($objSpc->getServicioId());

            if(!is_object($objServicio))
            {
                throw new \Exception('No existe el servicio.');
            }
            
            
            $objSpcCuenta = $this->servicioGeneral
                                 ->getServicioProductoCaracteristica($objServicio, 'ID CUENTA NETVOICE', $objServicio->getProductoId());
            
            if(is_object($objSpcCuenta))
            {
                $intCuentaNetvoice = $objSpcCuenta->getValor();
            }
            else
            {
                throw new \Exception('No hay cuenta de netvoice.');
            }
            
            //se debe cancelar a nivel de equipo medianto los service

            $arrayParametroInfo['intCuentaNetvoice'] = $intCuentaNetvoice;
            $arrayParametroInfo['intNumero']         = $objSpc->getValor();
            $arrayParametroInfo['strFechaIni']       = $strDesde;
            $arrayParametroInfo['strFechaFin']       = $strHasta;
            
            $arrayRespuesta = $this->callGeneralWeb->getDetalleLLamadas($arrayParametroInfo);
            
            if($arrayRespuesta['mensaje'] != 'OK')
            {
                throw new \Exception('Error al obtener detalle del número: '.$objSpc->getValor().' - '.$arrayRespuesta['mensaje']);
            }
            
            //armo una tabla con los datos proporcionados
            
            $strHeaderHtml =   '<ol style="list-style: none; font-size: 14px; line-height: 32px; font-weight: bold;">
                <li style="clear: both;">
                <h2 style="text-align: center;">Detalle de llamadas del n&uacute;mero&nbsp; '.$objSpc->getValor().'</h2>
                </li>
                </ol>
                <table style="width: 590px;" border="1" cellspacing="1" cellpadding="1"><caption>&nbsp;</caption>
                <thead>
                <tr>
                <td style="text-align: center; width: 150px;"><strong>N&uacute;mero Destino</strong></td>
                <td style="text-align: center; width: 300px;"><strong>Fecha / Hora</strong></td>
                <td style="text-align: center; width: 140px;"><strong>Duraci&oacute;n</strong></td>
                </tr>
                </thead>
                <tbody>';
            
            foreach ($arrayRespuesta['data'] as $arrayRecord)
            {
                $strRecordHtml .=   '<tr>
                <td style="width: 152px;">'.$arrayRecord['numerodestino'].'</td>
                <td style="width: 302px;">'.$arrayRecord['fechahora'].'</td>
                <td style="width: 142px;">'.$arrayRecord['duracionformatohhmmss'].'</td>
                </tr>';
            }

            $strFootHtml =     '</tbody>  </table>';
            
            $strTableHtml = $strHeaderHtml.$strRecordHtml.$strFootHtml;
            
            $strLogin   = $objServicio->getPuntoId()->getLogin();
            $strAsunto  = 'Notificación de detalle llamadas del número '.$objSpc->getValor().' login '.$strLogin;
            //envio una notificacion con los numeros que se generaron
            // Parametros a reemplazar en el cuerpo del correo
            $arrayParametrosCorreo = array('cuerpoCorreo' => $strTableHtml);
            //Llamada a la generacion del correo de notificacion y el envio del correo
            $arrayTo[] = $strCorreo;
            $arrayTo = array_unique($arrayTo);
            $this->envioPlantilla->generarEnvioPlantilla($strAsunto, $arrayTo, 'NOTI_GEN_TELEFO', $arrayParametrosCorreo, null, null, null);
            
            //historial del servicio
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion("Se solicitó el detalle de llamadas del número ".$objSpc->getValor().' <br> al correo '.$strCorreo);
            $objServicioHistorial->setEstado($objServicio->getEstado());
            $objServicioHistorial->setUsrCreacion($strUser);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($strIpCreacion);
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();
            
            $strMensaje = 'OK';
            $strStatus = "OK";
            

        }
        catch(\Exception $e)
        {

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            $arrayFinal = array('status' => "ERROR", 'mensaje' => $e->getMessage());
            return $arrayFinal;
        }

        //*---------------------------------------------------------------------*/
        //*DECLARACION DE COMMITS*/

        if($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }

        $this->emComercial->getConnection()->close();
        //*----------------------------------------------------------------------*/

        $arrayFinal = array('status' => $strStatus, 'mensaje' => $strMensaje);

        return $arrayFinal;        
        
    }
    
    /**
     * liberarNumero
     * Función que libera el numero en los services
     *
     * @params $arrayPeticiones [intSpc, strUser, strIpClient]
     * 
     * @return $arrayResult [status, mensaje]
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 01-09-2018
     * 
     */
    
    public function liberarNumero($arrayPeticiones)
    {
        $intSpc         = $arrayPeticiones['intSpc'];
        $strUser        = $arrayPeticiones['strUser'];
        $strIpCreacion  = $arrayPeticiones['strIpClient'];

        //*---------------------------------------------------------------------*/
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        //LOGICA DE NEGOCIO-----------------------------------------------------
        try
        {         

            $objSpc = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')->find($intSpc);

            if(!is_object($objSpc))
            {
                throw new \Exception('No existe el número.');
            }

            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($objSpc->getServicioId());

            if(!is_object($objServicio))
            {
                throw new \Exception('No existe el servicio.');
            }
            
            
            $objSpcCuenta = $this->servicioGeneral
                                 ->getServicioProductoCaracteristica($objServicio, 'ID CUENTA NETVOICE', $objServicio->getProductoId());
            if(is_object($objSpcCuenta))
            {
                $intCuentaNetvoice = $objSpcCuenta->getValor();
            }
            else
            {
                throw new \Exception('No hay cuenta de netvoice.');
            }
            
            //se debe cancelar a nivel de equipo medianto los service

            $arrayParametroInfo['intCuentaNetvoice'] = $intCuentaNetvoice;
            $arrayParametroInfo['intNumero'] = $objSpc->getValor();
            
            $strRespuesta = $this->callGeneralWeb->removerNumero($arrayParametroInfo);
            
            if($strRespuesta != 'OK')
            {
                throw new \Exception('Error al remover el número: '.$objSpc->getValor().' - '.$strRespuesta);
            }                        
            
            $objSpc->setEstado('Cancelado');
            $this->emComercial->persist($objSpc);
            $this->emComercial->flush();
            
            //cancelo las caracteristicas relacionadas al numero
            $arraySpc = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                          ->findBy(array('servicioId' => $objServicio->getId(),'refServicioProdCaractId' => $intSpc));
            
            foreach($arraySpc as $objSpcRef)
            {                
                $objSpcRef->setEstado('Cancelado');
                $this->emComercial->persist($objSpcRef);
                $this->emComercial->flush();
            }


            //historial del servicio
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion("Se canceló el número ".$objSpc->getValor());
            $objServicioHistorial->setEstado($objServicio->getEstado());
            $objServicioHistorial->setUsrCreacion($strUser);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($strIpCreacion);
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();
            
            //libero un puerto del equipo cliente
            $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($objServicio->getId());
            
            if(is_object($objServicioTecnico))
            {

                    $objInterfaceElemento = $this->emComercial->getRepository('schemaBundle:InfoInterfaceElemento')
                                                              ->findOneBy(array('elementoId' => $objServicioTecnico->getElementoClienteId(), 
                                                                                'estado' => 'connected'));           
                    
                    if(is_object($objInterfaceElemento))
                    {
                        $objInterfaceElemento->setEstado('not connect');
                        $this->emComercial->persist($objInterfaceElemento);
                        $this->emComercial->flush();
                    }
            }          

            
            $strMensaje = 'OK';
            $strStatus = "OK";
            

        }
        catch(\Exception $e)
        {

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            $arrayFinal = array('status' => "ERROR", 'mensaje' => $e->getMessage());
            return $arrayFinal;
        }

        //*---------------------------------------------------------------------*/
        //*DECLARACION DE COMMITS*/

        if($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }
        

        //*----------------------------------------------------------------------*/

        $arrayFinal = array('status' => $strStatus, 'mensaje' => $strMensaje);

        return $arrayFinal;
    }
    
    /**
     * cancelarLineas
     * Función que cancela el servicio al cliente
     *
     * @params $arrayPeticiones [intIdServicio, strUser, strIpClient]
     * 
     * @return $arrayResult [status, mensaje]
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 14-11-2018 se realiza validación para que el ancho de banda dependa del numero de canales de cada linea
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.2 18-12-2018 se valida para que el ancho de banda solo lo reduzca cuando se trate de la empresa TN
    
     * @author John Vera <javera@telconet.ec>
     * @version 1.3 10-01-2018 se aumenta notificación por correo para indicar el cambio de estado de la línea telefónica
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.4 06-03-2020 Se valida si es activación simultánea para asignar el estado que corresponde (Rechazada o Anulada)
     */
    
    public function cancelarLineas($arrayPeticiones)
    {
        $intServicio        = $arrayPeticiones['intIdServicio'];
        $strUser            = $arrayPeticiones['strUser'];
        $strIpCreacion      = $arrayPeticiones['strIpClient'];
        $strPrefijoEmpresa  = $arrayPeticiones['strPrefijoEmpresa'];        
        $strActSimu         = $arrayPeticiones['strActSimu'];
        $strEstado          = $arrayPeticiones['strEstado'];
        $strCategoria       = $arrayPeticiones['strCategoria'];
       
        //*---------------------------------------------------------------------*/
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        //LOGICA DE NEGOCIO-----------------------------------------------------
        try
        {

            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intServicio);

            if(!is_object($objServicio))
            {
                throw new \Exception('No existe el servicio.');
            }
            
            $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($intServicio);
            
            if(!is_object($objServicioTecnico))
            {
                throw new \Exception('No existe el servicio técnico.');
            }


            $objCaracteristicaNumero = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                         ->findOneBy(array(   'descripcionCaracteristica' => 'NUMERO',
                                                                              'estado' => 'Activo'));

            if(!is_object($objCaracteristicaNumero))
            {
                throw new \Exception('No existe característica NÚMERO.');
            }

            $objProdCaract = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                               ->findOneBy(array( 'productoId' => $objServicio->getProductoId()->getId() ,
                                                                  'caracteristicaId' => $objCaracteristicaNumero->getId()));

            if(!is_object($objProdCaract))
            {
                 throw new \Exception('No existe producto característica NÚMERO.');
            }

            $arraySpc = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                           ->findBy(array( 'servicioId'                 => $objServicio->getId() ,
                                           'productoCaracterisiticaId'  => $objProdCaract->getId(),
                                            'estado'                    => array('Activo', 'In-Corte','Reservado')));

            $arrayParamLibera = array ( 'strUser'       => $strUser,
                                        'strIpClient'   => $strIpCreacion);

            //cancelo los numeros y los libero
            foreach($arraySpc as $objSpc)
            {
                $strNumeros = $objSpc->getValor().', ';
                
                //consulto el numero de canales
                $objCanales = $this->servicioGeneral->obtenerServicioProductoCaracteristica(array(  'objServicio'         => $objServicio,
                                                                                                    'strCaracteristica'   => 'NUMERO CANALES', 
                                                                                                    'intRefId'            => $objSpc->getId()));
                if(is_object($objCanales))
                {
                    $intTotalCanales = $intTotalCanales + $objCanales->getValor();
                }                
                
                $arrayParamLibera['intSpc'] = $objSpc->getId();
                $arrayResult = $this->liberarNumero($arrayParamLibera);
                
                if($arrayResult['status'] == 'ERROR')
                {
                    throw new \Exception($arrayResult['mensaje']);
                }
            }

            if($strPrefijoEmpresa != 'MD' && $strCategoria !== 'FIJA SMB')
            {          
                    $intCapacidad = 100 * $intTotalCanales;


                    //bajo el ancho de banda
                    //cambio el anchos de banda de los concentradores
                    $arrayCambioBw = array ();

                    $arrayCambioBw['objServicio']       = $objServicio;
                    $arrayCambioBw['intCapacidadNueva'] = $intCapacidad;
                    $arrayCambioBw['strOperacion']      = '-';
                    $arrayCambioBw['usrCreacion']       = $strUser;
                    $arrayCambioBw['ipCreacion']        = $strIpCreacion;

                    $arrayCambio = $this->cambioAnchoBanda($arrayCambioBw);

                    if($arrayCambio['status'] == 'ERROR')
                    {
                        throw new \Exception($arrayCambio['mensaje']);
                    }      
            
            }
            
            //si tiene la caracteristica de FIJA ANALOGA solicitar e retiro de equipo
            $objSpcTipo = $this->servicioGeneral
                               ->getServicioProductoCaracteristica($objServicio, 'CATEGORIAS TELEFONIA', $objServicio->getProductoId());
            
            if(is_object($objSpcTipo) && $objSpcTipo->getValor() == 'FIJA ANALOGA')
            {
                                   
                //creo la solicitud
                $objTipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                    ->findOneBy(array("descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO", "estado" => "Activo"));

                if(!is_object($objTipoSolicitud))
                {
                    throw new \Exception('No tiene tipo de solicitud.');
                }

                $objDetalleSolicitud = new InfoDetalleSolicitud();
                $objDetalleSolicitud->setServicioId($objServicio);
                $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
                $objDetalleSolicitud->setEstado("AsignadoTarea");
                $objDetalleSolicitud->setUsrCreacion($strUser);
                $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitud->setObservacion("SOLICITA RETIRO DE EQUIPO POR CANCELACION DEL SERVICIO");
                $this->emComercial->persist($objDetalleSolicitud);

                //crear las caract para la solicitud de retiro de equipo
                $objAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneByDescripcionCaracteristica('ELEMENTO CLIENTE');

                if(!is_object($objAdmiCaracteristica))
                {
                    throw new \Exception('No tiene característica ELEMENTO CLIENTE.');
                }

                //valor del ont
                $objCaract = new InfoDetalleSolCaract();
                $objCaract->setCaracteristicaId($objAdmiCaracteristica);
                $objCaract->setDetalleSolicitudId($objDetalleSolicitud);
                $objCaract->setValor($objServicioTecnico->getElementoClienteId());
                $objCaract->setEstado("AsignadoTarea");
                $objCaract->setUsrCreacion($strUser);
                $objCaract->setFeCreacion(new \DateTime('now'));
                $this->emComercial->persist($objCaract);
                $this->emComercial->flush();

                //crear historial para la solicitud
                $objHistorialSolicitud = new InfoDetalleSolHist();
                $objHistorialSolicitud->setDetalleSolicitudId($objDetalleSolicitud);
                $objHistorialSolicitud->setEstado("AsignadoTarea");
                $objHistorialSolicitud->setObservacion("GENERACION AUTOMATICA DE SOLICITUD RETIRO DE EQUIPO POR CANCELACION DEL SERVICIO");
                $objHistorialSolicitud->setUsrCreacion($strUser);
                $objHistorialSolicitud->setFeCreacion(new \DateTime('now'));
                $objHistorialSolicitud->setIpCreacion($strIpCreacion);
                $this->emComercial->persist($objHistorialSolicitud);                   
                
            }

            //historial del servicio
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion("Se canceló el servicio.");
            $objServicioHistorial->setEstado('Cancel');
            $objServicioHistorial->setUsrCreacion($strUser);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($strIpCreacion);
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();
            
            //cancelo todas las caracteristicas
            $arrayCaracteristicas = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                      ->findBy(array( 'servicioId'                 => $objServicio->getId()));

            //cancelo los numeros y los libero
            foreach($arrayCaracteristicas as $objSpc)
            {
                if ($strActSimu == 'S')
                {
                    $objSpc->setEstado($strEstado);
                }
                else
                {
                    $objSpc->setEstado('Cancel');
                }
                
                $this->emComercial->persist($objSpc);
                $this->emComercial->flush();
            }            
            
            
            //actualizo el servicio a estado cancelado
            if ($strActSimu == 'S')
            {
                $objServicio->setEstado($strEstado);
            }
            else
            {
                $objServicio->setEstado('Cancel');
            }
            
            $this->emComercial->persist($objServicio);
            $this->emComercial->flush();
            
            $strLogin   = $objServicio->getPuntoId()->getLogin();
            $strAsunto  = 'Notificación de cancelación de números telefónicos '.$strLogin;
            //envio una notificacion con los numeros que se generaron
            // Parametros a reemplazar en el cuerpo del correo
            $arrayParametrosCorreo = array('cuerpoCorreo' => ' Se han cancelado los números: <br>'.$strNumeros);
            //Llamada a la generacion del correo de notificacion y el envio del correo
            $arrayTo = array_unique($arrayTo);
            $this->envioPlantilla->generarEnvioPlantilla($strAsunto, $arrayTo, 'NOTI_GEN_TELEFO'
                                                         , $arrayParametrosCorreo, null, null, null);               
            

            $strMensaje = 'OK';
            $strStatus = 'OK';
        }
        catch(\Exception $e)
        {

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            $arrayFinal = array('status' => "ERROR", 'mensaje' => $e->getMessage());
            return $arrayFinal;
        }

        //*---------------------------------------------------------------------*/
        //*DECLARACION DE COMMITS*/

        if($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }

        $this->emComercial->getConnection()->close();
        //*----------------------------------------------------------------------*/

        $arrayFinal = array('status' => $strStatus, 'mensaje' => $strMensaje);

        return $arrayFinal;
    }

    
    /**
     * cancelarLinea
     * Función que cancela el servicio al cliente
     *
     * @params $arrayPeticiones [intIdServicio, idNumero, strUser, strIpClient]
     * 
     * @return $arrayResult [status, mensaje]
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 01-09-2018
     * 
     * @version 1.1 14-11-2018 se realiza validación para que el ancho de banda dependa del numero de canales de cada linea
     * 
     */
    
    public function cancelarLinea($arrayPeticiones)
    {

        $intNumero      = $arrayPeticiones['idNumero'];
        $strUser        = $arrayPeticiones['strUser'];
        $strIpCreacion  = $arrayPeticiones['strIpClient'];


        //*---------------------------------------------------------------------*/
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        //LOGICA DE NEGOCIO-----------------------------------------------------
        try
        {
            
            $objSpc = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')->find($intNumero);            
            
            if(!is_object($objSpc))
            {
                throw new \Exception('No existe la característica número en el servicio.');
            }
            else
            {
                $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($objSpc->getServicioId());
                
                if(!is_object($objServicio))
                {
                    throw new \Exception('No existe el servicio.');
                }                

            }
            
            //consulto el numero de canales
            $objCanales = $this->servicioGeneral->obtenerServicioProductoCaracteristica(array(  'objServicio'         => $objServicio,
                                                                                                'strCaracteristica'   => 'NUMERO CANALES', 
                                                                                                'intRefId'            => $objSpc->getId()));
            if(is_object($objCanales))
            {
                $intTotalCanales = $objCanales->getValor();
            }                
            
            $arrayParamLibera = array('strUser'     => $strUser,
                                      'strIpClient' => $strIpCreacion,
                                      'intSpc'      => $intNumero);

            $arrayResult = $this->liberarNumero($arrayParamLibera);
            
            if($arrayResult['status'] != 'OK')
            {
                throw new \Exception($arrayResult['mensaje']);
            }

            //bajo el ancho de banda
            //cambio el anchos de banda de los concentradores
            $arrayCambioBw = array();

            $arrayCambioBw['objServicio']       = $objServicio;
            $arrayCambioBw['intCapacidadNueva'] = $intTotalCanales * 100;
            $arrayCambioBw['strOperacion']      = '-';
            $arrayCambioBw['usrCreacion']       = $strUser;
            $arrayCambioBw['ipCreacion']        = $strIpCreacion;
            
            $arrayCambio = $this->cambioAnchoBanda($arrayCambioBw);
            
            if($arrayCambio['status'] == 'ERROR')
            {
                throw new \Exception($arrayCambio['mensaje']);
            }
            
            
            //verifico que si ya no hay más números 
            $objSpcNumeros = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                               ->findBy(array('productoCaracterisiticaId' => $objSpc->getProductoCaracterisiticaId(), 
                                                              'servicioId'                => $objServicio->getId(),
                                                              'estado'                    => array('Activo','In-Corte')));
            
            if(count($objSpcNumeros) == 0)
            {
                //cancelamos el servicio
                //historial del servicio
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion("Se canceló el servicio.");
                $objServicioHistorial->setEstado($objServicio->getEstado());
                $objServicioHistorial->setUsrCreacion($strUser);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();

                //actualizo el servicio a estado cancelado
                $objServicio->setEstado('Cancel');
                $this->emComercial->persist($objServicio);
                $this->emComercial->flush();            
            }     
            

            $strMensaje = 'OK';
            $strStatus = 'OK';
        }
        catch(\Exception $e)
        {

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            $arrayFinal = array('status' => "ERROR", 'mensaje' => $e->getMessage());
            return $arrayFinal;
        }

        //*---------------------------------------------------------------------*/
        //*DECLARACION DE COMMITS*/

        if($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }

        $this->emComercial->getConnection()->close();
        //*----------------------------------------------------------------------*/

        $arrayFinal = array('status' => $strStatus, 'mensaje' => $strMensaje);

        return $arrayFinal;
    }
    
    /**
     * cambioAnchoBanda
     * Función que realiza el cambio de ancho de banda en los elementos
     *
     * @params $arrayPeticiones [objServicio, usrCreacion, ipCreacion, strOperacion, intCapacidadNueva ]
     * 
     * @return $arrayResult [status, mensaje]
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 01-09-2018
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.1 11-05-2020 | Se realiza ajuste para evitar los numeros negativos en los calculos de BandWidth.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 01-06-2020 - Se agrega el id del servicio a la url 'configBW' del ws de networking para la validación del BW
     *
     */    
    public function cambioAnchoBanda($arrayPeticiones)
    {
        $objServicio = $arrayPeticiones['objServicio'];
        $objProducto = $objServicio->getProductoId();
        $strUsrCreacion = $arrayPeticiones['usrCreacion'];
        $strIpCreacion = $arrayPeticiones['ipCreacion'];
        $strOperacion = $arrayPeticiones['strOperacion'];
        $intCapacidadNueva = $arrayPeticiones['intCapacidadNueva'];

        try
        {

            //primero actualizo las capacidades del servicio

            $objSpcEnlaceDatos = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "ENLACE_DATOS", $objProducto);

            if($objSpcEnlaceDatos)
            {
                $this->emComercial->getConnection()->beginTransaction();

                $intServicioEnlace = $objSpcEnlaceDatos->getValor();

                $objServicioEnlace = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intServicioEnlace);
                if(!$objServicioEnlace)
                {
                    $arrayResult = array("status" => "ERROR", "mensaje" => "No existe el servicio.");
                    return $arrayResult;
                }

                $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($intServicioEnlace);
                if(!$objServicioTecnico)
                {
                    $arrayResult = array("status" => "ERROR", "mensaje" => "No existe el servicio técnico.");
                    return $arrayResult;
                }


                $objSpcCapacidadServ = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioEnlace, "CAPACIDAD1", 
                                                                                                 $objServicioEnlace->getProductoId());

                $objSpcCapacidadServ2 = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioEnlace, "CAPACIDAD2", 
                                                                                                  $objServicioEnlace->getProductoId());                
                

                if(is_object($objSpcCapacidadServ))
                {
                    $intCapacidad = $objSpcCapacidadServ->getValor();
                }
                else
                {
                    $arrayResult = array("status" => "ERROR", "mensaje" => "No existe la característica capacidad.");
                    return $arrayResult;
                }
                
                if($strOperacion == '+')
                {
                    $intCapacidadTotal = $objSpcCapacidadServ->getValor() + $intCapacidadNueva;
                }
                else
                {
                    /* Se realiza un ajuste para evitar que se registren valores negativos. */
                    $intCapacidadTotal = ($objSpcCapacidadServ->getValor() - $intCapacidadNueva) < 0 ? 0 :
                                          $objSpcCapacidadServ->getValor() - $intCapacidadNueva;
                }                

                //actualizo las capacidades
                $objSpcCapacidadServ->setValor($intCapacidadTotal);
                $this->emComercial->persist($objSpcCapacidadServ);
                $this->emComercial->flush();

                $objSpcCapacidadServ2->setValor($intCapacidadTotal);
                $this->emComercial->persist($objSpcCapacidadServ2);
                $this->emComercial->flush();
            

                //luego obtengo la capacidad total por el puerto
                //Capacidades totales de los servicios activos ligados a un puerto
                $arrayCapacidades = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                         ->getResultadoCapacidadesPorInterface($objServicioTecnico->getInterfaceElementoId());


                //reconfiguro en el service la capacidad

                $objInterfaceElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                    ->find($objServicioTecnico->getInterfaceElementoId());

                $objDetalleAnilloSw = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                    ->findOneBy(array("elementoId" => $objServicioTecnico->getElementoId(),
                    "detalleNombre" => "ANILLO",
                    "estado" => "Activo"));
                $strAnilloSw = '';
                if($objDetalleAnilloSw)
                {
                    $strAnilloSw = $objDetalleAnilloSw->getDetalleValor();
                }
                else
                {
                    $arrayResult = array("status" => "ERROR", "mensaje" => "No existe el anillo del switch del servicio.");
                    return $arrayResult;
                }

                if($objInterfaceElemento->getElementoId())
                {
                    $strNombreElemento = $objInterfaceElemento->getElementoId()->getNombreElemento();
                }
                else
                {
                    $arrayResult = array("status" => "ERROR", "mensaje" => "El servicio técnico no tiene asignado elemento.");
                    return $arrayResult;
                }

                $strNombreProducto = $objServicioEnlace->getProductoId()->getNombreTecnico();
                $strLoginAux = $objServicioEnlace->getLoginAux();

                //accion a ejecuta        
                $arrayService['url'] = 'configBW';
                $arrayService['accion'] = 'reconectar';
                $arrayService['id_servicio'] = $objServicioEnlace->getId();
                $arrayService['nombreMetodo'] = 'InfoTelefoniaService.cambioAnchoBanda';
                $arrayService['sw'] = $strNombreElemento;
                $arrayService['user_name'] = $strUsrCreacion;
                $arrayService['user_ip'] = $strIpCreacion;
                $arrayService['bw_up'] = $arrayCapacidades['totalCapacidad1'];
                $arrayService['bw_down'] = $arrayCapacidades['totalCapacidad2'];
                $arrayService['servicio'] = $strNombreProducto;
                $arrayService['login_aux'] = $strLoginAux;
                $arrayService['pto'] = $objInterfaceElemento->getNombreInterfaceElemento();
                $arrayService['anillo'] = $strAnilloSw;

                //Ejecucion del metodo via WS para realizar la configuracion del SW
                $arrayRespuestaBw = $this->networkingScripts->callNetworkingWebService($arrayService);

                if($arrayRespuestaBw['status'] == 'ERROR')
                {
                    throw new \Exception('Error: ' . $arrayRespuestaBw['mensaje'] . ' codigo: ' . $arrayRespuestaBw['statusCode']);
                }
                
                //Se realiza validacion para que solo ejecute recalculo de BW para Servicios con tipo de enlace PRINCIPAL
                if($objServicioTecnico->getTipoEnlace() == 'PRINCIPAL' && $objServicioEnlace->getEstado() == 'Activo')
                {
                    //bajar bw del concentrador
                    $arrayParametrosBw = array(
                        "objServicio" => $objServicioEnlace,
                        "usrCreacion" => $arrayPeticiones['usrCreacion'],
                        "ipCreacion" => $arrayPeticiones['ipCreacion'],
                        "capacidadUnoNueva" => intval($arrayPeticiones['capacidadUno']),
                        "capacidadDosNueva" => intval($arrayPeticiones['capacidadDos']),
                        "operacion" => "-",
                        "accion" => "Se actualiza Capacidades por Cancelación de servicio : <b>" . $objServicioEnlace->getLoginAux() . "<b>"
                    );

                    //Se actualiza las capacidades del Concentrador
                    $this->servicioGeneral->actualizarCapacidadesEnConcentrador($arrayParametrosBw);
                }


                //historial del servicio
                $objServicioHistorialNavega = new InfoServicioHistorial();
                $objServicioHistorialNavega->setServicioId($objServicioEnlace);
                
                $objServicioHistorialNavega->setObservacion(
                    '<b>Cambio de Velocidad Realizado<br> Cliente: </b>' . $objServicio->getLoginAux() . " <b>BW:</b> " . $intCapacidadNueva .
                    "<br> <b>Velocidad Up anterior  : </b>" . $intCapacidad .
                    "<br> <b>Velocidad Down anterior: </b>" . $intCapacidad .
                    "<br> <b>Velocidad Up Nuevo  : </b>" . $intCapacidadTotal .
                    "<br> <b>Velocidad Down Nuevo: </b>" . $intCapacidadTotal
                );
                
                $objServicioHistorialNavega->setEstado($objServicioEnlace->getEstado());
                $objServicioHistorialNavega->setUsrCreacion($strUsrCreacion);
                $objServicioHistorialNavega->setFeCreacion(new \DateTime('now'));
                $objServicioHistorialNavega->setIpCreacion($strIpCreacion);
                $this->emComercial->persist($objServicioHistorialNavega);
                $this->emComercial->flush();
                
                if($this->emComercial->getConnection()->isTransactionActive())
                {
                    $this->emComercial->getConnection()->commit();
                }

            }

        }
        catch(\Exception $ex)
        {
            $arrayResult = array("status" => "ERROR", "mensaje" => $ex->getMessage());
            $this->emComercial->getConnection()->rollback();
            return $arrayResult;
        }

        $arrayResult = array("status" => "OK", "mensaje" => "OK");
        return $arrayResult;
    }


    /**
     * cambioElemento
     * Función que cambio el elemento al cliente
     *
     * @params $arrayPeticiones [idServicio, modeloCpe, macCpe, serieCpe, usrCreacion, ipCreacion]
     * 
     * @return $arrayResult [status, mensaje]
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 20-11-2018 finalizo la solicitud relacionada a este cambio de equipo.
     * 
     */
    
    public function cambioElementoTelefonia($arrayParametros)
    {
        $intServicio        = $arrayParametros['idServicio'];
        $strModeloElemento  = $arrayParametros['modeloCpe'];
        $strMacElemento     = $arrayParametros['macCpe'];
        $strSerieElemento   = $arrayParametros['serieCpe'];
        $strUser            = $arrayParametros['usrCreacion'];
        $strIp              = $arrayParametros['ipCreacion'];
        $intIdSolicitud     = $arrayParametros['idSolicitud'];

        $strStatus          = 'OK';
        $strMensaje         = 'OK';
                
        try
        {
            //*DECLARACION DE TRANSACCIONES------------------------------------------*/
            $this->emComercial->getConnection()->beginTransaction();
            $this->emInfraestructura->getConnection()->beginTransaction();

            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findOneById($intServicio);
                        
            if(!is_object($objServicio))
            {
                throw new \Exception('No existe el servicio.');
            }   


            $objServicioTecnico = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($intServicio);
            if(!is_object($objServicio))
            {
                throw new \Exception('No existe el servicio técnico.');
            }           

            
            //buscar elemento cpe
            $arrayNafCpe = $this->servicioGeneral->buscarElementoEnNaf($strSerieElemento, $strModeloElemento, "PI", "ActivarServicio");
            $strStatusNaf = $arrayNafCpe[0]['status'];
            $strMensajeNaf = $arrayNafCpe[0]['mensaje'];
            if($strStatusNaf == "OK")
            {
                //actualizamos registro en el naf del cpe
                $arrayParametrosNaf = array('tipoArticulo'          => 'AF',
                                            'identificacionCliente' => '',
                                            'empresaCod'            => '',
                                            'modeloCpe'             => $strModeloElemento,
                                            'serieCpe'              => $strSerieElemento,
                                            'cantidad'              => '1');

                $strMensajeInstalacion = $this->cambioElemento->procesaInstalacionElemento($arrayParametrosNaf);

                if(strlen(trim($strMensajeInstalacion)) > 0)
                {
                    throw new \Exception($strMensajeInstalacion);
                }
            }
            else
            {
                throw new \Exception($strMensajeNaf);
            }
            
            
            
            $objElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objServicioTecnico->getElementoClienteId());
            
             //elimino todas las interfaces y cuento cuantas estan en connect
            $arrayInterfacesActual = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                             ->findByElementoId($objElemento->getId());
            
            $intConectadas = 0;
            foreach($arrayInterfacesActual as $objInterfaceActual)
            {
                if(is_object($objInterfaceActual))
                {
                    //si el estado esta en conectado hago un contador
                    if($objInterfaceActual->getEstado() == 'connected')
                    {
                        $intConectadas++;
                    }

                    $objInterfaceActual->setEstado('Eliminado');
                    $this->emInfraestructura->persist($objInterfaceActual);
                    $this->emInfraestructura->flush();
                }
            }
            

            //nuevo elemento
            $objModeloElemento = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                      ->findOneBy(array("nombreModeloElemento" => $strModeloElemento, "estado" => "Activo"));
            if(!is_object($objModeloElemento))
            {
                throw new \Exception('No existe el modelo elemento.');
            }
            
            //buscar el interface Modelo
            $arrayInterfaceModelo = $this->emInfraestructura->getRepository('schemaBundle:AdmiInterfaceModelo')
                                         ->findBy(array("modeloElementoId" => $objModeloElemento->getId()));
            
            //$cantInterfacesNuevas->$objInterface->getCantidadInterface()
            
            foreach($arrayInterfaceModelo as $objInterface)
            {
                $intCantidadInterfaces = $objInterface->getCantidadInterface();
                $strFormato = $objInterface->getFormatoInterface();

                for($i = 1; $i <= $intCantidadInterfaces; $i++)
                {
                    $objInterfaceCpe = new InfoInterfaceElemento();
                    $arrayFormat = explode("?", $strFormato);
                    $strNombreInterfaceElemento = $arrayFormat[0] . $i;

                    $objInterfaceCpe->setNombreInterfaceElemento($strNombreInterfaceElemento);
                    $objInterfaceCpe->setElementoId($objElemento);
                    if($intConectadas > $i)
                    {
                        $objInterfaceCpe->setEstado("connected");
                    }
                    else
                    {
                        $objInterfaceCpe->setEstado("not connect");
                    }
                    $objInterfaceCpe->setUsrCreacion($strUser);
                    $objInterfaceCpe->setFeCreacion(new \DateTime('now'));
                    $objInterfaceCpe->setIpCreacion($strIp);

                    $this->emInfraestructura->persist($objInterfaceCpe);
                }
            }

            //actualizo la mac
            $objDetalleMac = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                  ->findOneBy(array("elementoId" => $objElemento->getId(), 'estado' => 'Activo', 'detalleNombre' => 'MAC'));

            if(is_object($objDetalleMac))
            {
                $strMacActual = $objDetalleMac->getDetalleValor();
                
                $objDetalleMac->setDetalleValor($strMacElemento);
                $this->emInfraestructura->persist($objInterfaceCpe);
                $this->emInfraestructura->flush();
            }

            $strSerieActual = $objElemento->getSerieFisica();
            $strModeloElementoActual = $objElemento->getModeloElementoId()->getNombreModeloElemento();
            
            //actualizo la serie
            $objElemento->setModeloElementoId($objModeloElemento);
            $objElemento->setSerieFisica($strSerieElemento);
            $this->emInfraestructura->persist($objElemento);
            $this->emInfraestructura->flush();


            //historial elemento
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objElemento);
            $objHistorialElemento->setEstadoElemento("Activo");
            $objHistorialElemento->setObservacion("Se Realizo un Cambio de Elemento Cliente. "."<br> <b>Elemento Anterior:<b/> <br> Modelo: ".
            $strModeloElementoActual."  <br> Serie: ".$strSerieActual." <br> "." Mac: ".$strMacActual." <br> ".
            "<br> <b>Elemento Nuevo:</b><br> Modelo: ".$strModeloElemento."<br> Serie: ".$strSerieElemento."<br> "." Mac: ".$strMacElemento." <br> ");
            $objHistorialElemento->setUsrCreacion($strUser);
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setIpCreacion($strIp);
            $this->emInfraestructura->persist($objHistorialElemento);
            $this->emInfraestructura->flush();

            //historial del servicio
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion("Se Realizo un Cambio de Elemento Cliente. "."<br> <b>Elemento Anterior:<b/> <br> Modelo: ".
            $strModeloElementoActual."  <br> Serie: ".$strSerieActual." <br> "."  <br> Mac: ".$strMacActual." <br> ".
            "<br> <b>Elemento Nuevo:</b><br> Modelo: ".$strModeloElemento."<br> Serie: ".$strSerieElemento."<br> "." Mac: ".$strMacElemento." <br> ");
            $objServicioHistorial->setEstado($objServicio->getEstado());
            $objServicioHistorial->setUsrCreacion($strUser);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($strIp);
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();
            
            //finalizo la solicitud            
            if($intIdSolicitud > 0)
            {                
                $objDetalleSol = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdSolicitud);
                
                if(is_object($objDetalleSol))
                {
                    $objDetalleSol->setEstado('Finalizada');
                    $this->emComercial->persist($objDetalleSol);
                    $this->emComercial->flush();                       
                    
                    //creo una colicitud de retiro de equipo
                    $objTipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                        ->findOneBy(array("descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO", "estado" => "Activo"));

                    $objDetalleSolicitud = new InfoDetalleSolicitud();
                    $objDetalleSolicitud->setServicioId($objServicio);
                    $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
                    $objDetalleSolicitud->setEstado("AsignadoTarea");
                    $objDetalleSolicitud->setUsrCreacion($strUser);
                    $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolicitud->setObservacion("SOLICITA RETIRO DE EQUIPO POR CAMBIO DE MODEM");
                    $this->emComercial->persist($objDetalleSolicitud);
                    $this->emComercial->flush();

                    //crear las caract para la solicitud de retiro de equipo
                    $objCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                           ->findOneBy(array('descripcionCaracteristica' => 'ELEMENTO CLIENTE', 'tipo' => 'TECNICA'));
                    if(is_object($objCaracteristica))
                    {              

                        $objCaractSolicitudCambioElemento = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                            ->findBy(array("detalleSolicitudId" => $objDetalleSol->getId()));

                        for($i = 0; $i < count($objCaractSolicitudCambioElemento); $i++)
                        {
                            $objSolCaract = new InfoDetalleSolCaract();
                            $objSolCaract->setCaracteristicaId($objCaracteristica);
                            $objSolCaract->setDetalleSolicitudId($objDetalleSolicitud);
                            $objSolCaract->setValor($objCaractSolicitudCambioElemento[$i]->getValor());
                            $objSolCaract->setEstado("AsignadoTarea");
                            $objSolCaract->setUsrCreacion($strUser);
                            $objSolCaract->setFeCreacion(new \DateTime('now'));
                            $this->emComercial->persist($objSolCaract);
                            $this->emComercial->flush();
                        }
                        //crear historial para la solicitud
                        $objHistorialSolicitud = new InfoDetalleSolHist();
                        $objHistorialSolicitud->setDetalleSolicitudId($objDetalleSolicitud);
                        $objHistorialSolicitud->setEstado("AsignadoTarea");
                        $objHistorialSolicitud->setObservacion("GENERACION AUTOMATICA DE SOLICITUD RETIRO DE EQUIPO POR CAMBIO DE MODEM");
                        $objHistorialSolicitud->setUsrCreacion($strUser);
                        $objHistorialSolicitud->setFeCreacion(new \DateTime('now'));
                        $objHistorialSolicitud->setIpCreacion($strIp);
                        $this->emComercial->persist($objHistorialSolicitud);
                        $this->emComercial->flush();
                    }
                }
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

            $strStatus = "ERROR";
            $strMensaje = "ERROR EN LA LOGICA DE NEGOCIO, " . $e->getMessage();
            $arrayFinal = array('status' => $strStatus, 'mensaje' => $strMensaje);
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


        $this->emInfraestructura->getConnection()->close();
        $this->emSoporte->getConnection()->close();
        $arrayFinal = array('status' => $strStatus, 'mensaje' => $strMensaje);
        return $arrayFinal;
    }

    /**
     * editarLinea
     * Función que edita la linea del cliente
     *
     * @params $arrayPeticiones [idSpcCanales, idNumero, strCanales]
     * 
     * @return $arrayResult [status, mensaje]
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     *
     */
    
    public function editarLinea($arrayParam)
    {
                
        $intSpcCanales   = $arrayParam['idSpcCanales'];
        $intSpc          = $arrayParam['idNumero'];
        $strCanales      = $arrayParam['strCanales'];
        
        try
        {            
            $this->emComercial->getConnection()->beginTransaction();
            
            $objSpcNumero = $this->emComercial->getRepository("schemaBundle:InfoServicioProdCaract")->find($intSpc);
            if(!is_object($objSpcNumero))
            {
                throw new \Exception('No existe el número.');
            }
         
            $objServicio = $this->emComercial->getRepository("schemaBundle:InfoServicio")->find($objSpcNumero->getServicioId());

            if(!is_object($objServicio))
            {
                throw new \Exception('No existe el servicio.');
            }

            $arraySpc  = array('objServicio' => $objServicio, 'strCaracteristica' => 'ID CUENTA NETVOICE');
            $objSpcCuenta = $this->servicioGeneral->obtenerServicioProductoCaracteristica($arraySpc);

            if(is_object($objSpcCuenta))
            {
                $intCuenta = $objSpcCuenta->getValor();
            }
            else
            {
                throw new \Exception('No existe cuenta del número '.$objSpcNumero->getValor());
            }

            $arraySpc  = array('objServicio' => $objServicio, 'strCaracteristica' => 'CLAVE', 'intRefId' => $intSpc );
            $objSpcClave = $this->servicioGeneral->obtenerServicioProductoCaracteristica($arraySpc); 

            if(is_object($objSpcClave))
            {
                $strClave = $objSpcClave->getValor();
            }
            else
            {
                throw new \Exception('No existe clave del número '.$objSpcNumero->getValor());
            }                    

            $arrayParamActivar = array( 'intCuentaNetvoice' => $intCuenta , 
                                        'strClave'   =>  $strClave, 
                                        'intCanales' => $strCanales,
                                        'intNumero'  => $objSpcNumero->getValor());
           
            $strResult = $this->callGeneralWeb->editarLineaNetvoice($arrayParamActivar);

            if($strResult != 'OK')
            {
                throw new \Exception('Error: '.$strResult);
            }       
            
            $objSpcCanales = $this->emComercial->getRepository("schemaBundle:InfoServicioProdCaract")->find($intSpcCanales);

            if(is_object($objSpcCanales))
            {
                $objSpcCanales->setValor($strCanales);
                $this->emComercial->persist($objSpcCanales);
                $this->emComercial->flush();
            }
            
        }
        catch(\Exception $ex)
        {
            $this->emComercial->getConnection()->rollback();
            $arrayResult = array("status" => "ERROR", "mensaje" => $ex->getMessage());
            return $arrayResult;
        }
        
        $this->emComercial->getConnection()->commit();
        
        $arrayResult = array("status" => "OK", "mensaje" => 'OK');
        return $arrayResult; 
    }
    
    /**
     * cambiarNumero
     * Función que permite cambiar el número de un servicio
     *
     * @params $arrayPeticiones [idNumero, strNuevoNumero, strUser, strIpClient ]
     * 
     * @return $arrayResult [status, mensaje]
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 21-12-2018 se aumenta el parametro checksbc en la activación de la línea
     * 
     */    
    
    public function cambiarNumero($arrayParam)
    {
                
        $intSpc          = $arrayParam['idNumero'];
        $strNumeroNuevo = $arrayParam['strNuevoNumero'];
        $strUser        = $arrayParam['strUser'];
        $strIp          = $arrayParam['strIpClient'];        
        
        try
        {            
            $this->emComercial->getConnection()->beginTransaction();
            
            $objSpcNumero = $this->emComercial->getRepository("schemaBundle:InfoServicioProdCaract")->find($intSpc);            

            if(!is_object($objSpcNumero))
            {
                throw new \Exception('El número no esta relacionado al servicio.');
            }
            
            $strNumeroAnterior = $objSpcNumero->getValor();

            $objServicio = $this->emComercial->getRepository("schemaBundle:InfoServicio")->find($objSpcNumero->getServicioId());

            if(!is_object($objServicio))
            {
                throw new \Exception('No existe el servicio.');
            }

            $arraySpc  = array('objServicio' => $objServicio, 'strCaracteristica' => 'ID CUENTA NETVOICE');
            $objSpcCuenta = $this->servicioGeneral->obtenerServicioProductoCaracteristica($arraySpc);

            if(is_object($objSpcCuenta))
            {
                $intCuenta = $objSpcCuenta->getValor();
            }
            else
            {
                throw new \Exception('No existe cuenta del número '.$objSpcNumero->getValor());
            }

            $arraySpc  = array('objServicio' => $objServicio, 'strCaracteristica' => 'CLAVE', 'intRefId' => $intSpc );
            $objSpcClave = $this->servicioGeneral->obtenerServicioProductoCaracteristica($arraySpc); 

            if(is_object($objSpcClave))
            {
                $strClave = $objSpcClave->getValor();
            }
            else
            {
                throw new \Exception('No existe clave del número '.$strNumero);
            }                    

            $arraySpc  = array('objServicio' => $objServicio, 'strCaracteristica' => 'NUMERO CANALES', 'intRefId' => $intSpc);
            $objSpcCanales = $this->servicioGeneral->obtenerServicioProductoCaracteristica($arraySpc); 

           if(is_object($objSpcCanales))
            {
                $intCanales = $objSpcCanales->getValor();
            }
            else
            {
                throw new \Exception('No existe canales del número '.$objSpcNumero->getValor());
            } 
            
            $strChecksbc    = '';

            $arraySpc  = null;
            $arraySpc  = array('objServicio' => $objServicio, 'strCaracteristica' => 'CATEGORIAS TELEFONIA');
            $objSpcCategoria = $this->servicioGeneral->obtenerServicioProductoCaracteristica($arraySpc); 

            if(is_object($objSpcCategoria))
            {
                $strCategoria = $objSpcCategoria->getValor();
                
                if($strCategoria == 'FIJA SMB')
                {
                    $strChecksbc = '1';
                }                
            }

            $arrayParamActivar = array('intCuentaNetvoice'  => $intCuenta , 
                                       'strClave'           =>  $strClave, 
                                       'intCanales'         => $intCanales,
                                       'intNumero'          => $strNumeroNuevo,
                                       'checksbc'           => $strChecksbc);
           
            $strResult = $this->callGeneralWeb->activarLineaNetvoice($arrayParamActivar);

            if($strResult != 'OK')
            {
                throw new \Exception('Error: '.$strResult);
            }           
            
            //se cancela el numero
            $arrayParametroInfo['intCuentaNetvoice'] = $intCuenta;
            $arrayParametroInfo['intNumero'] = $strNumeroAnterior;
            
            
            $strRespuesta = $this->callGeneralWeb->removerNumero($arrayParametroInfo);
            
            if($strRespuesta != 'OK')
            {
                throw new \Exception('Error al remover el número: '.$objSpcNumero->getValor().' - '.$strRespuesta);
            }
            
            $objSpcNumero->setValor($strNumeroNuevo);
            
            $this->emComercial->persist($objSpcNumero);
            $this->emComercial->flush();            

            $objServicioHist = new InfoServicioHistorial();
            $objServicioHist->setServicioId($objServicio);
            $objServicioHist->setObservacion('<b>Cambio de número</b> <br> Anterior: '.$strNumeroAnterior.'<br> Nuevo: '.$strNumeroNuevo);
            $objServicioHist->setIpCreacion($strIp);
            $objServicioHist->setFeCreacion(new \DateTime('now'));
            $objServicioHist->setUsrCreacion($strUser);
            $objServicioHist->setEstado($objServicio->getEstado());

            $this->emComercial->persist($objServicioHist);
            $this->emComercial->flush();

                    
        }
        catch(\Exception $ex)
        {
            $this->emComercial->getConnection()->rollback();
            $arrayResult = array("status" => "ERROR", "mensaje" => $ex->getMessage());
            return $arrayResult;
        }
        
        $this->emComercial->getConnection()->commit();
        
        $arrayResult = array("status" => "OK", "mensaje" => 'OK');
        return $arrayResult; 
    }
    
    /**
     * consultarNumero
     * Función que consulta un número en el service de netvoice
     *
     * @params $arrayPeticiones [strBusqueda, strPrefijoEmpresa ]
     * 
     * @return $arrayResult [status, mensaje]
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     *
     */          
    
    public function consultarNumero($arrayParam)
    {
                
        $strBusqueda  = $arrayParam['strBusqueda'];
        $strPrefijoEmpresa = $arrayParam['strPrefijoEmpresa'];
        
        try
        {            
            //obtengo la provincia
            $arrayParametroInfo['intServicio']          = $arrayParam['intIdServicio'];
            $arrayParametroInfo['strPrefijoEmpresa']    = $strPrefijoEmpresa;

            $arrayInfo = $this->emComercial->getRepository("schemaBundle:InfoPersona")->getDataUsuario($arrayParametroInfo);

            if($arrayInfo[0]['provincia'])
            {
                //ingresar los numeros con sus respectivos, clave, dominio y canales, dependiendo de la categoria, agrupandolos todos bajo un 
                $objParametro = $this->emComercial->getRepository('schemaBundle:AdmiParametroCab')
                    ->findOneBy(array("nombreParametro" => 'PARAMETROS_LINEAS_TELEFONIA',
                                      "estado" => 'Activo'));

                if(is_object($objParametro))
                {
                    $objParametroPrefijo = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findOneBy(array(  "descripcion" => 'PREFIJOS_PROVINCIA',
                                                                    "parametroId" => $objParametro->getId(),
                                                                    'valor1'      => $arrayInfo[0]['provincia'],
                                                                    "estado"      => 'Activo'));

                    if(is_object($objParametroPrefijo))
                    {
                        $intPrefijoCiudad = $objParametroPrefijo->getValor2();
                    }
                }
            }
            else
            {
                throw new \Exception('No existe provincia en el cliente.');
            }
            
            if($arrayParam['strFiltro'] == 'TERMINA')
            {
                $arrayParametroInfo['strBusqueda'] = '%'.$strBusqueda ;
            }
            else if ($arrayParam['strFiltro'] == 'EMPIEZA')
            {
                $arrayParametroInfo['strBusqueda'] = $strBusqueda.'%' ;
            }
            else
            {
                $arrayParametroInfo['strBusqueda'] = '%'.$strBusqueda.'%';
            }
            
            $arrayParametroInfo['intPrefijoCiudad'] = $intPrefijoCiudad;
            $arrayParametroInfo['strPrefijoEmpresa'] = $strPrefijoEmpresa;
            
            $arrayRepuesta = $this->callGeneralWeb->getNumero($arrayParametroInfo);
            
            if($arrayRepuesta['mensaje'] != 'OK')
            {
                throw new \Exception($arrayRepuesta['mensaje']);
            }
            else
            {
                $strResultado = $arrayRepuesta['numero'] ;
            }         
            
        }
        catch(\Exception $ex)
        {
            return $ex->getMessage();
        }
                
        return $strResultado; 
    }
    
    /**
     * Funcion que permite obtener el id del servicio tradicional del producto que tiene la marca de activación simultánea.
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 21-02-2020 - Version Inicial.
     *
     * @param $intIdServicio -> Contiene un int que representa el Id del servicio que tiene activación simultánea.
     * @return null|$intIdCouInstSim
     */

    public function getIdTradInstSimCouTelefonia($intIdServicio)
    {
        $arrayIdCouInstSim = null;
        
        $objServicioTradicional = $this->emComercial->getRepository('schemaBundle:InfoServicio')
            ->find($intIdServicio);

        if (is_object($objServicioTradicional))
        {
            $objAdmiProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                ->findOneBy(array(
                    'descripcionProducto' => 'COU LINEAS TELEFONIA FIJA'
                ));

            $objAdmiCaract = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                ->findOneBy(array(
                    'descripcionCaracteristica' => 'INSTALACION_SIMULTANEA_COU_TELEFONIA_FIJA',
                    'estado' => 'Activo'
                ));

            $objAdmiProdCaract = $this->emInfraestructura->getRepository('schemaBundle:AdmiProductoCaracteristica')
                ->findOneBy(array(
                    'caracteristicaId' => $objAdmiCaract->getId()
                ));

            $arrayServiciosCou = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                ->findBy(array(
                    'id'         =>  $intIdServicio,
                    'puntoId'    =>  $objServicioTradicional->getPuntoId(),
                    'productoId' =>  $objAdmiProducto->getId()
                ));

            if (count($arrayServiciosCou)>=1)
            {
                foreach ($arrayServiciosCou as $intKey=>$objServCouLineas)
                {
                    //Solo pendientes, asignada, asignada tarea
                    if ($objServCouLineas->getEstado() !== 'Anulado' || $objServCouLineas->getEstado() !== 'Rechazada' ||
                        $objServCouLineas->getEstado() !== 'Cancel')
                    {
                        $objInfoServProdCaract = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioProdCaract')
                                                ->findOneBy(array('servicioId' => $objServCouLineas->getId(),
                                                                  'productoCaracterisiticaId' => $objAdmiProdCaract->getId()
                        ));

                        if (is_object($objInfoServProdCaract))
                        {
                            $intKey = 0;
                            $arrayIdCouInstSim[$intKey]     = $objInfoServProdCaract->getValor();
                            $arrayIdCouInstSim[$intKey + 1] = $objInfoServProdCaract->getServicioId();
                            $arrayIdCouInstSim[$intKey + 2] = $objServicioTradicional->getPuntoId();
                        }
                    }
                }
            }
        }

        return $arrayIdCouInstSim;
    }

}

