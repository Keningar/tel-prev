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

class AsignarIpv4PublicaService {
    private $emComercial;
    private $emInfraestructura;
    private $emSoporte;
    private $emComunicacion;
    private $emSeguridad;
    private $servicioGeneral;
    private $container;
    private $objRdaMiddleware;
    private $strOpcion = "ASIGNAR_IP_PUBLICA";
    private $ejecutaComando;
    
    public function __construct(Container $container) 
    {
        $this->container            = $container;
        $this->emSoporte            = $this->container->get('doctrine')->getManager('telconet_soporte');
        $this->emInfraestructura    = $this->container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emSeguridad          = $this->container->get('doctrine')->getManager('telconet_seguridad');
        $this->emComercial          = $this->container->get('doctrine')->getManager('telconet');
        $this->emComunicacion       = $this->container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emNaf                = $this->container->get('doctrine')->getManager('telconet_naf');
        $this->ejecutaComando       = $container->getParameter('ws_rda_ejecuta_scripts');
    }    
    
    public function setDependencies(InfoServicioTecnicoService $servicioGeneral, RedAccesoMiddlewareService  $redAccesoMiddleware) 
    {
        $this->servicioGeneral = $servicioGeneral;
        $this->objRdaMiddleware   = $redAccesoMiddleware;
    }
    
    /**
     * Funcion que sirve para crear caracteristica IPV4, actualizar ldap de clientes y enviar al middleware para que eliminen las ips del cnr
     * y reiniciar el ont del cliente
     * 
     * @author Creado: Francisco Adum <fadum@netlife.net.ec>
     * @version 1.0 07-07-2017
     * 
     * @param array                    $arrayPeticiones
     * @return array con dos valores: String 'status' indica OK/ERROR, String 'mensaje' indica el mensaje a presentar en caso de ERROR
     */
    public function asignarIpv4Publica($arrayPeticiones)
    {
        //*DECLARACION DE VARIABLES----------------------------------------------*/
        $intIdEmpresa       = $arrayPeticiones['idEmpresa'];
        $strPrefijoEmpresa  = $arrayPeticiones['prefijoEmpresa'];
        $intIdServicio      = $arrayPeticiones['idServicio'];
        $usrCreacion        = $arrayPeticiones['usrCreacion'];
        $ipCreacion         = $arrayPeticiones['ipCreacion'];
        $boolFlagNoExiste   = false;
                
        //migracion_ttco_md
        $arrayEmpresaMigra = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                               ->getEmpresaEquivalente($intIdServicio, $strPrefijoEmpresa);

        if($arrayEmpresaMigra)
        { 
            if ($arrayEmpresaMigra['prefijo']=='TTCO')
            {
                 $intIdEmpresa     = $arrayEmpresaMigra['id'];
                 $strPrefijoEmpresa= $arrayEmpresaMigra['prefijo'];
            }
        }
        
        $objServicio            = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
        
        if(!is_object($objServicio))
        {
            $arrayRespuesta = array('status' => "ERROR", 'mensaje' => "No existe Servicio indicado, Favor Revisar!");
            return $arrayRespuesta; 
        }
        
        $objServicioTecnico     = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                ->findOneBy(array( "servicioId" => $objServicio->getId()));
        
        if(!is_object($objServicioTecnico))
        {
            $arrayRespuesta = array('status' => "ERROR", 'mensaje' => "No existe Servicio Tecnico indicado, Favor Revisar!");
            return $arrayRespuesta; 
        }
        
        $objInterfaceElemento   = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                          ->find($objServicioTecnico->getInterfaceElementoId());
        
        if(!is_object($objInterfaceElemento))
        {
            $arrayRespuesta = array('status' => "ERROR", 'mensaje' => "No existe Interface Elemento indicado, Favor Revisar!");
            return $arrayRespuesta; 
        }
        
        $objElemento            = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objInterfaceElemento->getElementoId());
        
        if(!is_object($objElemento))
        {
            $arrayRespuesta = array('status' => "ERROR", 'mensaje' => "No existe Elemento indicado, Favor Revisar!");
            return $arrayRespuesta; 
        }
        
        $objModeloElemento      = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                          ->find($objElemento->getModeloElementoId());
        
        if(!is_object($objModeloElemento))
        {
            $arrayRespuesta = array('status' => "ERROR", 'mensaje' => "No existe el Modelo Elemento indicado, Favor Revisar!");
            return $arrayRespuesta; 
        }
        
        $objIpElemento          = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                          ->findOneBy(array('elementoId' => $objElemento->getId(), 'estado' => 'Activo'));
        
        if(!is_object($objIpElemento))
        {
            $arrayRespuesta = array('status' => "ERROR", 'mensaje' => "No existe la Ip del Elemento, Favor Revisar!");
            return $arrayRespuesta; 
        }
        
        //OBTENER NOMBRE CLIENTE
        $objPersona             = $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
        
        if(!is_object($objPersona))
        {
            $arrayRespuesta = array('status' => "ERROR", 'mensaje' => "No existe el cliente, Favor Revisar!");
            return $arrayRespuesta; 
        }
        
        $strNombreCliente       = ($objPersona->getRazonSocial() != "") ? $objPersona->getRazonSocial() : 
                                                                          $objPersona->getNombres()." ".$objPersona->getApellidos();

        //OBTENER IDENTIFICACION
        $strIdentificacion      = $objPersona->getIdentificacionCliente();

        //OBTENER LOGIN
        $strLogin               = $objServicio->getPuntoId()->getLogin();
        
        $objProducto            = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                    ->findOneBy(array(  "nombreTecnico" => "INTERNET",
                                                                        "empresaCod"    => $intIdEmpresa, 
                                                                        "estado"        => "Activo"));
        //*----------------------------------------------------------------------*/
        
        $arrayRespuesta         = null;
        
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        
        try
        {
            if($strPrefijoEmpresa == "MD")
            {
                $objSpcIpv4 = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "IPV4", $objProducto);

                $boolFlagNoExiste = true;
                if(is_object($objSpcIpv4))
                {
                    if($objSpcIpv4->getEstado() == 'Activo')
                    {
                        $boolFlagNoExiste = false;
                    }
                }
                
                if($boolFlagNoExiste)
                {
                    //CREAR LA CARACTERISTICA
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "IPV4", "PUBLICO", $usrCreacion);

                    //OBTENER SERIE ONT
                    $objElementoCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                  ->find($objServicioTecnico->getElementoClienteId());
                    
                    if(!is_object($objElementoCliente))
                    {
                        $arrayRespuesta = array('status' => "ERROR", 'mensaje' => "No existe el elemento del cliente, Favor Revisar!");
                        return $arrayRespuesta; 
                    }
                    
                    $strSerieOnt        = $objElementoCliente->getSerieFisica();

                    //OBTENER MAC ONT
                    $objSpcMacOnt       = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "MAC ONT", $objProducto);
                    if(is_object($objSpcMacOnt))
                    {
                        $strMacOnt      = $objSpcMacOnt->getValor();
                    }
                    else
                    {
                        $arrayRespuesta = array('status' => "ERROR", 'mensaje' => "No existe la mac del cliente, Favor Revisar!");
                        return $arrayRespuesta; 
                    }

                    //OBTENER INDICE CLIENTE
                    $objSpcIndiceCliente    = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 
                                                                                                        "INDICE CLIENTE", 
                                                                                                        $objProducto);
                    if(is_object($objSpcIndiceCliente))
                    {
                        $strIndiceCliente   = $objSpcIndiceCliente->getValor();
                    }
                    else
                    {
                        $arrayRespuesta = array('status' => "ERROR", 'mensaje' => "No existe el indice del cliente, Favor Revisar!");
                        return $arrayRespuesta; 
                    }
                }
                else
                {
                    $arrayRespuesta = array('status'=>"ERROR", 'mensaje'=>"El cliente ya tiene asignada una ipv4 publica y");
                    return $arrayRespuesta; 
                }
            }
        }
        catch(\Exception $e)
        {
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            $arrayRespuesta = array('status'=>"ERROR", 'mensaje'=>"Error en la Logica de Negocio!, ".$e->getMessage());
            return $arrayRespuesta; 
        }
        
        //*DECLARACION DE COMMITS*/
        if ($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }
        
        if($boolFlagNoExiste)
        {
            //ACTUALIZAR LDAP CLIENTES
            $resultadoJsonLdap = $this->servicioGeneral->ejecutarComandoLdap("A", $intIdServicio);
            if($resultadoJsonLdap->status!="OK")
            {
                $mensaje = $mensaje . "<br>" . $resultadoJsonLdap->mensaje;
            }

            //ENVIAR PETICION AL MIDDLEWARE PARA QUE ELIMINE IP EN EL CNR Y REINICIE EL ONT
            $arrayDatos = array(
                                    'serial_ont'        => $strSerieOnt,
                                    'mac_ont'           => $strMacOnt,
                                    'nombre_olt'        => $objElemento->getNombreElemento(),
                                    'ip_olt'            => $objIpElemento->getIp(),
                                    'puerto_olt'        => $objInterfaceElemento->getNombreInterfaceElemento(),
                                    'modelo_olt'        => $objModeloElemento->getNombreModeloElemento(),
                                    'gemport'           => "",
                                    'service_profile'   => "",
                                    'line_profile'      => "",
                                    'traffic_table'     => "",
                                    'ont_id'            => $strIndiceCliente,
                                    'service_port'      => "",
                                    'vlan'              => "",
                                    'estado_servicio'   => $objServicio->getEstado()
                                );

            $arrayDatosMiddleware = array(
                                            'nombre_cliente'        => $strNombreCliente,
                                            'login'                 => $strLogin,
                                            'identificacion'        => $strIdentificacion,
                                            'datos'                 => $arrayDatos,
                                            'opcion'                => $this->strOpcion,
                                            'ejecutaComando'        => $this->ejecutaComando,
                                            'usrCreacion'           => $usrCreacion,
                                            'ipCreacion'            => $ipCreacion
                                        );

            $arrayRespuesta = $this->objRdaMiddleware->middleware(json_encode($arrayDatosMiddleware));
        }
        
        $this->emComercial->getConnection()->close();
        //*----------------------------------------------------------------------*/
                
        return $arrayRespuesta;
    }
}