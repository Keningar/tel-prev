<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiParroquia
 *
 * @ORM\Table(name="ADMI_PARROQUIA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiParroquiaRepository")
 */
class AdmiParroquia
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PARROQUIA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_PARROQUIA", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiCanton
*
* @ORM\ManyToOne(targetEntity="AdmiCanton")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="CANTON_ID", referencedColumnName="ID_CANTON")
* })
*/
		
private $cantonId;

/**
* @var AdmiTipoParroquia
*
* @ORM\ManyToOne(targetEntity="AdmiTipoParroquia")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TIPO_PARROQUIA_ID", referencedColumnName="ID_TIPO_PARROQUIA")
* })
*/		
     		
private $tipoParroquiaId;

/**
* @var string $nombreParroquia
*
* @ORM\Column(name="NOMBRE_PARROQUIA", type="string", nullable=false)
*/		
     		
private $nombreParroquia;

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
* Get cantonId
*
* @return \telconet\schemaBundle\Entity\AdmiCanton
*/		
     		
public function getCantonId(){
	return $this->cantonId; 
}

/**
* Set cantonId
*
* @param telconet\schemaBundle\Entity\AdmiCanton $cantonId
*/
public function setCantonId(\telconet\schemaBundle\Entity\AdmiCanton $cantonId)
{
        $this->cantonId = $cantonId;
}


/**
* Get tipoParroquiaId
*
* @return telconet\schemaBundle\Entity\AdmiTipoParroquia
*/		
     		
public function getTipoParroquiaId(){
	return $this->tipoParroquiaId; 
}

/**
* Set tipoParroquiaId
*
* @param telconet\schemaBundle\Entity\AdmiTipoParroquia $tipoParroquiaId
*/
public function setTipoParroquiaId(\telconet\schemaBundle\Entity\AdmiTipoParroquia $tipoParroquiaId)
{
        $this->tipoParroquiaId = $tipoParroquiaId;
}


/**
* Get nombreParroquia
*
* @return string
*/		
     		
public function getNombreParroquia(){
	return $this->nombreParroquia; 
}

/**
* Set nombreParroquia
*
* @param string $nombreParroquia
*/
public function setNombreParroquia($nombreParroquia)
{
        $this->nombreParroquia = $nombreParroquia;
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
        return $this->nombreParroquia;
}

}