<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiSector
 *
 * @ORM\Table(name="ADMI_SECTOR")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiSectorRepository")
 */
class AdmiSector
{


/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
*/		
     		
private $feUltMod;

/**
* @var integer $id
*
* @ORM\Column(name="ID_SECTOR", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_SECTOR", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiParroquia
*
* @ORM\ManyToOne(targetEntity="AdmiParroquia")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PARROQUIA_ID", referencedColumnName="ID_PARROQUIA")
* })
*/
		
private $parroquiaId;

/**
* @var string $nombreSector
*
* @ORM\Column(name="NOMBRE_SECTOR", type="string", nullable=false)
*/		
     		
private $nombreSector;

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
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

/**
* @var string $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
*/		
     		
private $empresaCod;

/**
* Get empresaCod
*
* @return string
*/
public function getEmpresaCod() {
    return $this->empresaCod;
}
/**
* Set empresaCod
*
* @param string $empresaCod
*/
public function setEmpresaCod($empresaCod) {
    $this->empresaCod = $empresaCod;
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


/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get parroquiaId
*
* @return \telconet\schemaBundle\Entity\AdmiParroquia
*/		
     		
public function getParroquiaId(){
	return $this->parroquiaId; 
}

/**
* Set parroquiaId
*
* @param telconet\schemaBundle\Entity\AdmiParroquia $parroquiaId
*/
public function setParroquiaId(\telconet\schemaBundle\Entity\AdmiParroquia $parroquiaId)
{
        $this->parroquiaId = $parroquiaId;
}


/**
* Get nombreSector
*
* @return string
*/		
     		
public function getNombreSector(){
	return $this->nombreSector; 
}

/**
* Set nombreSector
*
* @param string $nombreSector
*/
public function setNombreSector($nombreSector)
{
        $this->nombreSector = $nombreSector;
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

}