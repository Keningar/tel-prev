<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTipoMedidor
 *
 * @ORM\Table(name="ADMI_TIPO_MEDIDOR")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTipoMedidorRepository")
 */
class AdmiTipoMedidor
{


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
* @var integer $id
*
* @ORM\Column(name="ID_TIPO_MEDIDOR", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TIPO_MEDIDOR", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $nombreTipoMedidor
*
* @ORM\Column(name="NOMBRE_TIPO_MEDIDOR", type="string", nullable=false)
*/		
     		
private $nombreTipoMedidor;

/**
* @var string $descripcionTipoMedidor
*
* @ORM\Column(name="DESCRIPCION_TIPO_MEDIDOR", type="string", nullable=true)
*/		
     		
private $descripcionTipoMedidor;

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


/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get nombreTipoMedidor
*
* @return string
*/		
     		
public function getNombreTipoMedidor(){
	return $this->nombreTipoMedidor; 
}

/**
* Set nombreTipoMedidor
*
* @param string $nombreTipoMedidor
*/
public function setNombreTipoMedidor($nombreTipoMedidor)
{
        $this->nombreTipoMedidor = $nombreTipoMedidor;
}


/**
* Get descripcionTipoMedidor
*
* @return string
*/		
     		
public function getDescripcionTipoMedidor(){
	return $this->descripcionTipoMedidor; 
}

/**
* Set descripcionTipoMedidor
*
* @param string $descripcionTipoMedidor
*/
public function setDescripcionTipoMedidor($descripcionTipoMedidor)
{
        $this->descripcionTipoMedidor = $descripcionTipoMedidor;
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

public function __toString()
{
    return $this->nombreTipoMedidor;
}

}