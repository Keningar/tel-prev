<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoCasoHistorial
 *
 * @ORM\Table(name="INFO_CASO_HISTORIAL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoCasoHistorialRepository")
 */
class InfoCasoHistorial
{


/**
* @var text $observacion
*
* @ORM\Column(name="OBSERVACION", type="text", nullable=true)
*/		
     		
private $observacion;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

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
* @var integer $id
*
* @ORM\Column(name="ID_CASO_HISTORIAL", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_CASO_HISTORIAL", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var InfoCaso
*
* @ORM\ManyToOne(targetEntity="InfoCaso")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="CASO_ID", referencedColumnName="ID_CASO")
* })
*/
		
private $casoId;

/**
* Get observacion
*
* @return 
*/		
     		
public function getObservacion(){
	return $this->observacion; 
}

/**
* Set observacion
*
* @param  $observacion
*/
public function setObservacion($observacion)
{
        $this->observacion = $observacion;
}


/**
* Get estado
*
* @return string
*/		
     		
public function getEstado(){
	return $this->estado; 
}

/**
* Set estado
*
* @param string $estado
*/
public function setEstado($estado)
{
        $this->estado = $estado;
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


/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get casoId
*
* @return telconet\schemaBundle\Entity\InfoCaso
*/		
     		
public function getCasoId(){
	return $this->casoId; 
}

/**
* Set casoId
*
* @param telconet\schemaBundle\Entity\InfoCaso $casoId
*/
public function setCasoId(\telconet\schemaBundle\Entity\InfoCaso $casoId)
{
        $this->casoId = $casoId;
}

}