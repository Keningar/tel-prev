<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\VistaServicioCaracteristicas
 *
 * @ORM\Table(name="VISTA_SERVICIO_CARACTERISTICAS")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\VistaServicioCaracteristicasRepository")
 */
class VistaServicioCaracteristicas
{

/**
* @var integer $id
*
* @ORM\Column(name="ID", type="integer", nullable=false)
* @ORM\Id
*/		
		
private $id;

/**
* @var integer $id_servicio
*
* @ORM\Column(name="ID_SERVICIO", type="integer", nullable=false)
*/		
		
private $idServicio;	
	
/**
    * @var string $tipo
*
* @ORM\Column(name="TIPO", type="string", nullable=false)
*/		
     		
private $tipo;

/**
* @var string $ip
*
* @ORM\Column(name="IP", type="string", nullable=false)
*/		
     		
private $ip;

/**
* @var string $mascara
*
* @ORM\Column(name="MASCARA", type="string", nullable=false)
*/		
     		
private $mascara;

/**
* @var datetime $gateway
*
* @ORM\Column(name="GATEWAY", type="string", nullable=false)
*/		
     		
private $gateway;

public function getIdServicio(){
	return $this->idServicio; 
}

/**
* Get tipo
*
* @return string
*/		
     		
public function getTipo(){
	return $this->tipo; 
}



/**
* Get ip
*
* @return string
*/		
     		
public function getIp(){
	return $this->ip; 
}


/**
* Get mascara
*
* @return string
*/		
     		
public function getMascara(){
	return $this->mascara; 
}

/**
* Get gateway
*
* @return string
*/		
     		
public function getGateway(){
	return $this->gateway; 
}

}