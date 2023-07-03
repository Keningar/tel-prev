<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTipoCaso
 *
 * @ORM\Table(name="ADMI_TIPO_CASO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTipoCasoRepository")
 */
class AdmiTipoCaso
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
* @ORM\Column(name="ID_TIPO_CASO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TIPO_CASO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $nombreTipoCaso
*
* @ORM\Column(name="NOMBRE_TIPO_CASO", type="string", nullable=false)
*/		
     		
private $nombreTipoCaso;

/**
* @var string $descripcionTipoCaso
*
* @ORM\Column(name="DESCRIPCION_TIPO_CASO", type="string", nullable=true)
*/		
     		
private $descripcionTipoCaso;

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
* Get nombreTipoCaso
*
* @return string
*/		
     		
public function getNombreTipoCaso(){
	return $this->nombreTipoCaso; 
}

/**
* Set nombreTipoCaso
*
* @param string $nombreTipoCaso
*/
public function setNombreTipoCaso($nombreTipoCaso)
{
        $this->nombreTipoCaso = $nombreTipoCaso;
}


/**
* Get descripcionTipoCaso
*
* @return string
*/		
     		
public function getDescripcionTipoCaso(){
	return $this->descripcionTipoCaso; 
}

/**
* Set descripcionTipoCaso
*
* @param string $descripcionTipoCaso
*/
public function setDescripcionTipoCaso($descripcionTipoCaso)
{
        $this->descripcionTipoCaso = $descripcionTipoCaso;
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

public function __toString() {
    return $this->nombreTipoCaso;
}

}
