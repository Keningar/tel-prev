<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\VistaElementosContenedores
 *
 * @ORM\Table(name="VISTA_ELEMENTOS_CONTENEDORES")
 * @ORM\Entity
 */ 
class VistaElementosContenedores
{

    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_VISTA", type="integer", nullable=false)
    * @ORM\Id
    */		

    private $id;


    /**
    * @var integer $elementoIdA
    *
    * @ORM\Column(name="ELEMENTO_ID_A", type="integer", nullable=false)
    */		

    private $elementoIdA;

    /**
        * @var string $elementoIdB
    *
    * @ORM\Column(name="ELEMENTO_ID_B", type="integer", nullable=false)
    */		

    private $elementoIdB;

    /**
    * @var string $nombreTipoElementoA
    *
    * @ORM\Column(name="NOMBRE_TIPO_ELEMENTO_A", type="string", nullable=false)
    */		

    private $nombreTipoElementoA;
    
    /**
    * Get id
    *
    * @return integer
    */	

    public function getId(){
            return $this->id; 
    }
    
    /**
    * Get elementoIdA
    *
    * @return integer
    */	

    public function getElementoIdA(){
            return $this->elementoIdA; 
    }
    
    /**
    * Get elementoIdB
    *
    * @return integer
    */	

    public function getElementoIdB(){
            return $this->elementoIdB; 
    }

    /**
    * Get nombreTipoElementoA
    *
    * @return string
    */		

    public function getNombreTipoElementoA(){
            return $this->nombreTipoElementoA; 
    }
    
}