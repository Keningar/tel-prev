<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\VistaFacturasAbiertas
 *
 * @ORM\Table(name="VISTA_FACTURAS_ABIERTAS")
 * @ORM\Entity
 */ 
class VistaFacturasAbiertas
{

    /**
    * @var integer $puntoId
    *
    * @ORM\Column(name="PUNTO_ID", type="integer", nullable=false)
    * @ORM\Id
    */		

    private $puntoId;

    /**
        * @var integer $facturasAbiertas
    *
    * @ORM\Column(name="FACTURAS_ABIERTAS", type="string", nullable=false)
    */		

    private $facturasAbiertas;

   
    
    
       /**
    * Get puntoId
    *
    * @return integer
    */	

    public function getPuntoId(){
            return $this->puntoId; 
    }
    
    /**
    * Get facturasAbiertas
    *
    * @return integer
    */		

    public function getFacturasAbiertas(){
            return $this->facturasAbiertas; 
    }
        

}