<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\VistaContratosRechazar
 *
 * @ORM\Table(name="VISTA_CONTRATOS_RECHAZAR")
 * @ORM\Entity
 */ 
class VistaContratosRechazar
{

    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_CONTRATO", type="integer", nullable=false)
    * @ORM\Id
    */		
    private $id;


    /**
    * @var integer $personaEmpresaRolId
    *
    * @ORM\Column(name="PERSONA_EMPRESA_ROL_ID", type="integer", nullable=false)
    */	
    private $personaEmpresaRolId;
    

    /**
    * @var string $rechazar
    *
    * @ORM\Column(name="RECHAZAR", type="string", nullable=false)
    */		
    private $rechazar;

    
    /**
    * Get id
    *
    * @return integer
    */	
    public function getId()
    {
        return $this->id; 
    }
    
    /**
    * Get personaEmpresaRolId
    *
    * @return integer
    */	
    public function getPersonaEmpresaRolId()
    {
        return $this->personaEmpresaRolId; 
    }
    
    /**
    * Get rechazar
    *
    * @return string
    */	
    public function getRechazar()
    {
        return $this->rechazar; 
    }    
}