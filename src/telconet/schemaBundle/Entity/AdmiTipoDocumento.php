<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM; 

/**
 * telconet\schemaBundle\Entity\AdmiTipoDocumento
 *
 * @ORM\Table(name="ADMI_TIPO_DOCUMENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTipoDocumentoRepository")
 */
class AdmiTipoDocumento
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_TIPO_DOCUMENTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TIPO_DOCUMENTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var string $extensionTipoDocumento
*
* @ORM\Column(name="EXTENSION_TIPO_DOCUMENTO", type="string", nullable=true)
*/		
     		
private $extensionTipoDocumento;

/**
* @var string $descripcionTipoDocumento
*
* @ORM\Column(name="DESCRIPCION_TIPO_DOCUMENTO", type="string", nullable=true)
*/		
     		
private $descripcionTipoDocumento;

/**
* @var string $tipoMime
*
* @ORM\Column(name="TIPO_MIME", type="string", nullable=true)
*/		
     		
private $tipoMime;

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

/**
* Get extensionTipoDocumento
*
* @return string
*/		
     		
public function getExtensionTipoDocumento(){
	return $this->extensionTipoDocumento; 
}

/**
* Set extensionTipoDocumento
*
* @param string $extensionTipoDocumento
*/
public function setExtensionTipoDocumento($extensionTipoDocumento)
{
        $this->extensionTipoDocumento = $extensionTipoDocumento;
}

/**
* Get descripcionTipoDocumento
*
* @return string
*/		
     		
public function getDescripcionTipoDocumento(){
	return $this->descripcionTipoDocumento; 
}

/**
* Set descripcionTipoDocumento
*
* @param string $descripcionTipoDocumento
*/
public function setDescripcionTipoDocumento($descripcionTipoDocumento)
{
        $this->descripcionTipoDocumento = $descripcionTipoDocumento;
}

/**
* Get tipoMime
*
* @return string
*/
public function getTipoMime(){
	return $this->tipoMime; 
}

/**
* Set tipoMime
*
* @param string $tipoMime
*/
public function setTipoMime($tipoMime)
{
        $this->tipoMime = $tipoMime;
}


public function __toString()
{
        return $this->extensionTipoDocumento;
}

}
