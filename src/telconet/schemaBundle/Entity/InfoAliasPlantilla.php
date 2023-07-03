<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoAliasPlantilla
 *
 * @ORM\Table(name="INFO_ALIAS_PLANTILLA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoAliasPlantillaRepository")
 */
class InfoAliasPlantilla
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_ALIAS_PLANTILLA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_ALIAS_PLANTILLA", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $aliasId
*
* @ORM\Column(name="ALIAS_ID", type="integer", nullable=false)
*/		
     		
private $aliasId;

/**
* @var integer $plantillaId
*
* @ORM\Column(name="PLANTILLA_ID", type="integer", nullable=false)
*/		
     		
private $plantillaId;

/**
* @var integer $esCopia
*
* @ORM\Column(name="ES_COPIA", type="string", nullable=false)
*/		
     		
private $esCopia;

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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get aliasId
*
* @return integer
*/		
     		
public function getAliasId(){
	return $this->aliasId; 
}

/**
* Set aliasId
*
* @param integer $aliasId
*/
public function setAliasId($aliasId)
{
        $this->aliasId = $aliasId;
}


/**
* Get esCopia
*
* @return integer
*/		
     		
public function getEsCopia(){
	return $this->esCopia; 
}

/**
* Set esCopia
*
* @param integer $esCopia
*/
public function setEsCopia($esCopia)
{
        $this->esCopia = $esCopia;
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

}