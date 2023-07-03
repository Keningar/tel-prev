<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoHistorialElemento
 *
 * @ORM\Table(name="INFO_HISTORIAL_ELEMENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoHistorialElementoRepository")
 */
class InfoHistorialElemento
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_HISTORIAL", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_HISTORIAL_ELEMENTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var InfoElemento
*
* @ORM\ManyToOne(targetEntity="InfoElemento")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="ELEMENTO_ID", referencedColumnName="ID_ELEMENTO")
* })
*/
		
private $elementoId;

/**
* @var string $estadoElemento
*
* @ORM\Column(name="ESTADO_ELEMENTO", type="string", nullable=false)
*/		
     		
private $estadoElemento;

/**
* @var integer $capacidad
*
* @ORM\Column(name="CAPACIDAD", type="integer", nullable=true)
*/		
     		
private $capacidad;

/**
* @var string $observacion
*
* @ORM\Column(name="OBSERVACION", type="string", nullable=true)
*/		
     		
private $observacion;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
*/		
     		
private $feCreacion;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
*/		
     		
private $ipCreacion;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get elementoId
*
* @return telconet\schemaBundle\Entity\InfoElemento
*/		
     		
public function getElementoId(){
	return $this->elementoId; 
}

/**
* Set elementoId
*
* @param telconet\schemaBundle\Entity\InfoElemento $elementoId
*/
public function setElementoId(\telconet\schemaBundle\Entity\InfoElemento $elementoId)
{
        $this->elementoId = $elementoId;
}


/**
* Get estadoElemento
*
* @return string
*/		
     		
public function getEstadoElemento(){
	return $this->estadoElemento; 
}

/**
* Set estadoElemento
*
* @param string $estadoElemento
*/
public function setEstadoElemento($estadoElemento)
{
        $this->estadoElemento = $estadoElemento;
}


/**
* Get capacidad
*
* @return integer
*/		
     		
public function getCapacidad(){
	return $this->capacidad; 
}

/**
* Set capacidad
*
* @param integer $capacidad
*/
public function setCapacidad($capacidad)
{
        $this->capacidad = $capacidad;
}


/**
* Get observacion
*
* @return string
*/		
     		
public function getObservacion(){
	return $this->observacion; 
}

/**
* Set observacion
*
* @param string $observacion
*/
public function setObservacion($observacion)
{
        $this->observacion = $observacion;
}


/**
* Get usrCreacion
*
* @return string
*/		
     		
public function getUsrCreacion(){
	return $this->usrCreacion; 
}

/**
* Set usrCreacion
*
* @param string $usrCreacion
*/
public function setUsrCreacion($usrCreacion)
{
        $this->usrCreacion = $usrCreacion;
}


/**
* Get feCreacion
*
* @return datetime
*/		
     		
public function getFeCreacion(){
	return $this->feCreacion; 
}

/**
* Set feCreacion
*
* @param datetime $feCreacion
*/
public function setFeCreacion($feCreacion)
{
        $this->feCreacion = $feCreacion;
}


/**
* Get ipCreacion
*
* @return string
*/		
     		
public function getIpCreacion(){
	return $this->ipCreacion; 
}

/**
* Set ipCreacion
*
* @param string $ipCreacion
*/
public function setIpCreacion($ipCreacion)
{
        $this->ipCreacion = $ipCreacion;
}

}