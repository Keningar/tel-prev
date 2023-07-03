<?php

namespace telconet\tecnicoBundle\Service;

use Doctrine\ORM\EntityManager;

use telconet\schemaBundle\Entity\InfoElemento;
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
use \telconet\schemaBundle\Entity\InfoDetalleElemento;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;


class InfoIpService {
    private $container;
    private $emGeneral;
    private $emComercial;
    private $emInfraestructura;
    
    public function __construct(Container $container) {
        $this->container = $container;
        $this->emComercial       = $this->container->get('doctrine')->getManager('telconet');
        $this->emGeneral         = $this->container->get('doctrine')->getManager('telconet_general');
        $this->emInfraestructura = $this->container->get('doctrine')->getManager('telconet_infraestructura');
    }
    
    /**
     * Funcion que sirve para obtener la ip siguiente disponible de una subred 
     * Ejemplo.
     *        Subred: 		192.168.15.0/24
     *        IPs Reservadas: 	5
     *        IP Inicial:	192.168.15.6
     *        IP Final:		192.168.15.254
     * 
     * y mantenemos una tabla de ips para esta subred de la siguiente manera
     * 192.168.15.6	->	Activa, Asignada a un cliente X.
     * 192.168.15.7	->	Activa, Asignada a un cliente C.
     * 192.168.15.9	->	Activa, Asignada a un cliente A.
     * 
     * La IP Disponible seria
     * 192.168.15.8
     * La IP Disponible seria
     * 192.168.15.10
     * 
     * Si existen vacios en la secuencia de IPs el aprovicionamiento seria 
     * completando primeramente la secuencia con limites definidos en la subred
     * 
     * @author Juan Lafuente <jlafuente@telconet.ec>
     * @version 1.0 29-03-2013
     * @param string $intIdSubred
     * @return string $strIpDisponible
     */
    public function getIpDisponibleBySubred($intIdSubred) 
    {
        $strIpDisponible = 'NoDisponible';
        
        // Se obtiene los datos de la subred
        $objSubred = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                             ->findOneBy(array("id"     => $intIdSubred,
                                                               "estado" => "Activo"));
        $ipInicial = explode( '.', $objSubred->getIpInicial())[3];
        $ipFinal   = explode( '.', $objSubred->getIpFinal())[3];
        // Se obtiene toas las ips activas para la subred
        $objIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                         ->findBy(array('subredId' => $intIdSubred,
                                                        'estado'   => 'Activo'));        
        // se verifican si existen ips activas para la subred
        if(count($objIp)>0)
        {
            // Se segmenta todas las ips activas en un solo array
            $arrayOnlyIp = array();
            foreach ($objIp as $ip) 
            {
                $arrayOnlyIp[] = $ip->getIp();
            }
                        
            // Se almacena los primeros octetos de la subred
            $subredIp = explode( '.', $objSubred->getIpInicial())[0].'.'.
                        explode( '.', $objSubred->getIpInicial())[1].'.'.
                        explode( '.', $objSubred->getIpInicial())[2].'.';

            // Se procede a aislar el ultimo octeto de la ip en un array ($arrayIpActivas)
            $arrayIpActivas = array();
            foreach ($arrayOnlyIp as $ipActive) 
            {
                 $arrayIpActivas[] = explode( '.', $ipActive )[3];
            }

            // Se procede a generar un array con todos los ultimos octetos del rango de ips 
            $arrayIpTodas = array();
            for ($x = $ipInicial; $x <= $ipFinal; $x++) 
            {
                $arrayIpTodas[] = $x;
            }
            
            // Se calcula la diferencia antre ambos arreglos, dando como resultado las ips disponibles
            $resultado = array_diff($arrayIpTodas, $arrayIpActivas );
            natsort($resultado); 
            
            // Se verifica que exista ip disponibles para la subred
            if(count($resultado)>0)
            {
                $strIpDisponible = $subredIp.array_values($resultado)[0];
            }
            else
            {
                $strIpDisponible = 'NoDisponible';
            }
        }
        else
        {
            $strIpDisponible = $objSubred->getIpInicial();
        }
        return $strIpDisponible;
    }
}