<?php

namespace telconet\financieroBundle\Service;

use telconet\schemaBundle\Entity\InfoRecaudacionDet;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class InfoRecaudacionDetService
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emfinan;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emcom;
    
    
    private $container;
    
    public function __construct(Container $container) 
    {
         
        $this->container=$container;
        $this->emfinan = $this->container->get('doctrine')->getManager('telconet_financiero');
        $this->emcom = $this->container->get('doctrine')->getManager('telconet');
      
    }
    
    
    /**
     * 
     * Actualizacion: Se corrige que no asigne en esta funcion Asignado:S, es_cliente:S y se grabe personaEmpresaRolId
     * ya que esto se realiza al momento de crear el pago en funcion InfoPagoService=> generarPagoAnticipoPrv()
     * @version 1.1 20-07-2016 
     * @author amontero@telconet.ec
     * 
     * Metodo que ingresa detalle de recaudacion 
     * @param string $empresaCod  
     * @param string $identificacion
     * @param string $nombre
     * @param string $numeroReferencia
     * @param InfoRecaudacion $entityRecaudacion
     * @author Andres Montero <amontero@telconet.ec>
     * @since 23-12-2014
     */
    function grabaDetalleRecaudacion($empresaCod, $identificacion, $nombre, $numeroReferencia, $entityRecaudacion)
    {      
        $entityRecaudacionDet = new InfoRecaudacionDet();
        $entityRecaudacionDet->setRecaudacionId($entityRecaudacion->getId());
        $entityRecaudacionDet->setEstado('Activo');
        $entityRecaudacionDet->setFeCreacion($entityRecaudacion->getFeCreacion());
        $entityRecaudacionDet->setUsrCreacion($entityRecaudacion->getUsrCreacion());      
        //Se graba por default esCliente 'N' porque Cuando se ingrese el pago se cambia a 'S' si es cliente
        $entityRecaudacionDet->setEsCliente("N"); 
        //Se graba por default esCliente 'N' porque Cuando se ingrese el pago se cambia a 'S' si se 
        //asigno el detalle de retencion a un pago de algun cliente    
        $entityRecaudacionDet->setAsignado("N");   
        $entityRecaudacionDet->setNombre($nombre);
        $entityRecaudacionDet->setIdentificacion($identificacion);
        $entityRecaudacionDet->setNumeroReferencia($numeroReferencia);
        $this->emfinan->persist($entityRecaudacionDet);
        $this->emfinan->flush();
        return $entityRecaudacionDet;
    } 
}


