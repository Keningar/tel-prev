<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTagDocumento
 *
 * @ORM\Table(name="ADMI_TAG_DOCUMENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTagDocumentoRepository")
 */
class AdmiTagDocumento
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_TAG_DOCUMENTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TAG_DOCUMENTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $tagDocumento
*
* @ORM\Column(name="TAG_DOCUMENTO", type="string", nullable=true)
*/		
     		
private $tagDocumento;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var string $descripcionTag
*
* @ORM\Column(name="DESCRIPCION_TAG", type="string", nullable=true)
*/		
     		
private $descripcionTag;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=true)
*/		
     		
private $usrCreacion;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}
/**
* Get nombreTipoDocumento
*
* @return string
*/		
     		
public function getTagDocumento(){
	return $this->tagDocumento; 
}

/**
* Set tagDocumento
*
* @param string $tagDocumento
*/
public function setTagDocumento($tagDocumento)
{
        $this->tagDocumento = $tagDocumento;
}



/**
* Get descripcionTag
*
* @return string
*/		
     		
public function getDescripcionTag(){
	return $this->descripcionTag; 
}

/**
* Set descripcionTag
*
* @param string $descripcionTag
*/
public function setDescripcionTag($descripcionTag)
{
        $this->descripcionTag = $descripcionTag;
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
* Get feCreacion
*
* @return 
*/		
     		
public function getFeCreacion(){
	return $this->feCreacion; 
}

/**
* Set feCreacion
*
* @param  $feCreacion
*/
public function setFeCreacion($feCreacion)
{
        $this->feCreacion = $feCreacion;
}


/**
* Get feUltMod
*
* @return 
*/		
     		
public function getFeUltMod(){
	return $this->feUltMod; 
}

/**
* Set feUltMod
*
* @param  $feUltMod
*/
public function setFeUltMod($feUltMod)
{
        $this->feUltMod = $feUltMod;
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

}