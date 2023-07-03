<?php

namespace telconet\tecnicoBundle\Service;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Clase que sirve para activar servicios adicionales.
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 30-07-2015
 */
class InfoActivarProductoService 
{
    private $emComercial;
    private $emInfraestructura;
    private $emSoporte;
    private $emComunicacion;
    private $emSeguridad;
    private $emNaf;
    private $servicioGeneral;
    private $cancelarServicio;
    private $container;
    private $host;
    private $pathTelcos;
    private $pathParameters;
    
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
    }    
    
    public function setDependencies(InfoServicioTecnicoService $servicioGeneral) 
    {
        $this->servicioGeneral = $servicioGeneral;
    }
    
    /**
     * Funcion que sirve para activar el servicio adicional.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 30-07-2015
     * @param array $arrayParametros
     * @return array $respuestaFinal
     */
    public function activarServicio($arrayParametros)
    {
        $idServicio     = $arrayParametros['idServicio'];
        $idAccion       = $arrayParametros['idAccion'];
        $usrCreacion    = $arrayParametros['usrCreacion'];
        $ipCreacion     = $arrayParametros['ipCreacion'];
        
        $this->emComercial->getConnection()->beginTransaction();
        
        try
        {
            $servicio   = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
            $accion     = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($idAccion);
            $producto   = $servicio->getProductoId();
            
            //servicio
            $servicio->setEstado("Activo");
            $this->emComercial->persist($servicio);

            //historial del servicio
            $servicioHistorial = new InfoServicioHistorial();
            $servicioHistorial->setServicioId($servicio);
            $servicioHistorial->setObservacion($producto->getDescripcionProducto().": Se confirmo el servicio");
            $servicioHistorial->setEstado("Activo");
            $servicioHistorial->setUsrCreacion($usrCreacion);
            $servicioHistorial->setFeCreacion(new \DateTime('now'));
            $servicioHistorial->setIpCreacion($ipCreacion);
            $servicioHistorial->setAccion ($accion->getNombreAccion());
            $this->emComercial->persist($servicioHistorial);
            $this->emComercial->flush();
            
            $status     = "OK";
            $mensaje    = "Se Activo el Servicio!";
        }
        catch (\Exception $e) 
        {            
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            $status             = "ERROR";
            $mensaje            = $e->getMessage();
            $respuestaFinal[]   = array('status' => $status, 'mensaje' => $mensaje);
            return $respuestaFinal;
        }
        //*----------------------------------------------------------------------*/
        
        //*DECLARACION DE COMMITS*/
        if ($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }
        
        $this->emComercial->getConnection()->close();
        //*----------------------------------------------------------------------*/
        
        //*RESPUESTA-------------------------------------------------------------*/
        $respuestaFinal[] = array('status' => $status, 'mensaje' => $mensaje);
        return $respuestaFinal;
        //*----------------------------------------------------------------------*/
    }
}