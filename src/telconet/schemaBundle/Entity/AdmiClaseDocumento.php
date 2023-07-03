<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiClaseDocumento
 *
 * @ORM\Table(name="ADMI_CLASE_DOCUMENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiClaseDocumentoRepository")
 */
class AdmiClaseDocumento
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_CLASE_DOCUMENTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_CLASE_DOCUMENTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $nombreClaseDocumento
*
* @ORM\Column(name="NOMBRE_CLASE_DOCUMENTO", type="string", nullable=true)
*/		
     		
private $nombreClaseDocumento;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var string $descripcionClaseDocumento
*
* @ORM\Column(name="DESCRIPCION_CLASE_DOCUMENTO", type="string", nullable=true)
*/		
     		
private $descripcionClaseDocumento;

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
* @var string $visible
*
* @ORM\Column(name="VISIBLE", type="string", nullable=true)
*/		
     		
private $visible;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}
/**
* Get nombreClaseDocumento
*
* @return string
*/		
     		
public function getNombreClaseDocumento(){
	return $this->nombreClaseDocumento; 
}

/**
* Set nombreClaseDocumento
*
* @param string $nombreClaseDocumento
*/
public function setNombreClaseDocumento($nombreClaseDocumento)
{
        $this->nombreClaseDocumento = $nombreClaseDocumento;
}



/**
* Get descripcionClaseDocumento
*
* @return string
*/		
     		
public function getDescripcionClaseDocumento(){
	return $this->descripcionClaseDocumento; 
}

/**
* Set descripcionClaseDocumento
*
* @param string $descripcionClaseDocumento
*/
public function setDescripcionClaseDocumento($descripcionClaseDocumento)
{
        $this->descripcionClaseDocumento = $descripcionClaseDocumento;
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
public function __toString() {
    return $this->descripcionClaseDocumento;
}

/**
* Get visible
*
* @return string
*/		
     		
public function getVisible(){  
	return $this->visible; 
}

/**
* Set visible
*
* @param string $visible
*/
public function setVisible($visible)
{
        $this->visible = $visible;
}

}