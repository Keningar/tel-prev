<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPlantillaPregunta
 *
 * @ORM\Table(name="INFO_PLANTILLA_PREGUNTA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPlantillaPreguntaRepository")
 */
class InfoPlantillaPregunta
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PLANTILLA_PREGUNTA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PLANTILLA_PREGUNTA", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $plantillaId
*
* @ORM\Column(name="PLANTILLA_ID", type="integer", nullable=false)
*/		
     		
private $plantillaId;

/**
* @var string $preguntaId
*
* @ORM\Column(name="PREGUNTA_ID", type="integer", nullable=false)
*/		
     		
private $preguntaId;

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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get plantillaId
*
* @return integer
*/		
     		
public function getPlantillaId(){
	return $this->plantillaId; 
}

/**
* Set plantillaId
*
* @param integer $plantillaId
*/
public function setPlantillaId($plantillaId)
{
        $this->plantillaId = $plantillaId;
}


/**
* Get preguntaId
*
* @return integer
*/		
     		
public function getPreguntaId(){
	return $this->preguntaId; 
}

/**
* Set preguntaId
*
* @param integer $preguntaId
*/
public function setPreguntaId($preguntaId)
{
        $this->preguntaId = $preguntaId;
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

}