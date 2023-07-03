<?php
 
namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\VistaEstadoCuentaResumido
 *
 * @ORM\Table(name="VISTA_ESTADO_CUENTA_RESUMIDO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\VistaEstadoCuentaResumidoRepository")
 */ 
class VistaEstadoCuentaResumido
{

    /**
    * @var integer $id
    *
    * @ORM\Column(name="PUNTO_ID", type="integer", nullable=false)
    * @ORM\Id
    */		

    private $id;

    
    /**
    * @var string $saldo
    *
    * @ORM\Column(name="SALDO", type="float", nullable=false)
    */		

    private $saldo;
    
    
    /**
    * Get id
    *
    * @return integer
    */	

    public function getId(){
            return $this->id; 
    }
    
    /**
    * Get saldo
    *
    * @return float
    */		

    public function getSaldo(){
            return $this->saldo; 
    }

}