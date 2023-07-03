<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\PagosPeriodo
 *
 * @ORM\Table(name="PAGOS_PERIODO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\PagosPeriodoRepository")
 */
class PagosPeriodo
{
	
/**
* @var string $id
*
* @ORM\Column(name="ID_FA", type="string", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_PAGOS_PERI", allocationSize=1, initialValue=1)
*/		
		
private $id;
    
/**
* @var integer $total
*
* @ORM\Column(name="TOTAL", type="integer", nullable=false)
*/		
     		
private $total;

/**
* @var integer $oficinaId
*
* @ORM\Column(name="OFICINA_ID", type="integer", nullable=false)
*/		
     		
private $oficinaId;


/**
* @var string $rango
*
* @ORM\Column(name="RANGO", type="string", nullable=false)
*/		
     		
private $rango;


/**
* Get rango
*
* @return string
*/		
     		
public function getRango(){
	return $this->rango; 
}


/**
* Get total
*
* @return integer
*/		
     		
public function getTotal(){
	return $this->total; 
}

/**
* Get id
*
* @return string
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get oficinaId
*
* @return integer
*/		
     		
public function getOficinaId(){
	return $this->oficinaId; 
}

public function __toString()
{
        return $this->rango;
}

}