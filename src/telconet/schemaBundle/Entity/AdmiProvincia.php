<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiProvincia
 *
 * @ORM\Table(name="ADMI_PROVINCIA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiProvinciaRepository")
 */
class AdmiProvincia
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PROVINCIA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_PROVINCIA", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiRegion
*
* @ORM\ManyToOne(targetEntity="AdmiRegion")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="REGION_ID", referencedColumnName="ID_REGION")
* })
*/
		
private $regionId;

/**
* @var string $nombreProvincia
*
* @ORM\Column(name="NOMBRE_PROVINCIA", type="string", nullable=false)
*/		
     		
private $nombreProvincia;

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
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=false)
*/		
     		
private $usrUltMod;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
*/		
     		
private $feUltMod;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get regionId
*
* @return telconet\schemaBundle\Entity\AdmiRegion
*/		
     		
public function getRegionId(){
	return $this->regionId; 
}

/**
* Set regionId
*
* @param telconet\schemaBundle\Entity\AdmiRegion $regionId
*/
public function setRegionId(\telconet\schemaBundle\Entity\AdmiRegion $regionId)
{
        $this->regionId = $regionId;
}


/**
* Get nombreProvincia
*
* @return string
*/		
     		
public function getNombreProvincia(){
	return $this->nombreProvincia; 
}

/**
* Set nombreProvincia
*
* @param string $nombreProvincia
*/
public function setNombreProvincia($nombreProvincia)
{
        $this->nombreProvincia = $nombreProvincia;
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
* Get usrUltMod
*
* @return string
*/		
     		
public function getUsrUltMod(){
	return $this->usrUltMod; 
}

/**
* Set usrUltMod
*
* @param string $usrUltMod
*/
public function setUsrUltMod($usrUltMod)
{
        $this->usrUltMod = $usrUltMod;
}


/**
* Get feUltMod
*
* @return datetime
*/		
     		
public function getFeUltMod(){
	return $this->feUltMod; 
}

/**
* Set feUltMod
*
* @param datetime $feUltMod
*/
public function setFeUltMod($feUltMod)
{
        $this->feUltMod = $feUltMod;
}

public function __toString()
{
        return $this->nombreProvincia;
}

}