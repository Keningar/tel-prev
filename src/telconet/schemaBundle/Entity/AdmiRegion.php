<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiRegion
 *
 * @ORM\Table(name="ADMI_REGION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiRegionRepository")
 */
class AdmiRegion
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_REGION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_REGION", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiPais
*
* @ORM\ManyToOne(targetEntity="AdmiPais")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PAIS_ID", referencedColumnName="ID_PAIS")
* })
*/
		
private $paisId;

/**
* @var string $nombreRegion
*
* @ORM\Column(name="NOMBRE_REGION", type="string", nullable=false)
*/		
     		
private $nombreRegion;

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
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
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
* Get paisId
*
* @return telconet\schemaBundle\Entity\AdmiPais
*/		
     		
public function getPaisId(){
	return $this->paisId; 
}

/**
* Set paisId
*
* @param telconet\schemaBundle\Entity\AdmiPais $paisId
*/
public function setPaisId(\telconet\schemaBundle\Entity\AdmiPais $paisId)
{
        $this->paisId = $paisId;
}


/**
* Get nombreRegion
*
* @return string
*/		
     		
public function getNombreRegion(){
	return $this->nombreRegion; 
}

/**
* Set nombreRegion
*
* @param string $nombreRegion
*/
public function setNombreRegion($nombreRegion)
{
        $this->nombreRegion = $nombreRegion;
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
        return $this->nombreRegion;
}

}