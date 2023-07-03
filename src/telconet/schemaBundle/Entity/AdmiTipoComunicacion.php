<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTipoComunicacion
 *
 * @ORM\Table(name="ADMI_TIPO_COMUNICACION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTipoComunicacionRepository")
 */
class AdmiTipoComunicacion
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_TIPO_COMUNICACION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TIPO_COMUNICACION", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $nombreTipoComunicacion
*
* @ORM\Column(name="NOMBRE_TIPO_COMUNICACION", type="string", nullable=true)
*/		
     		
private $nombreTipoComunicacion;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var string $descripcionTipoComunicacion
*
* @ORM\Column(name="DESCRIPCION_TIPO_COMUNICACION", type="string", nullable=true)
*/		
     		
private $descripcionTipoComunicacion;

/**
* @var DATE $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="date", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var DATE $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="date", nullable=true)
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
* Get nombreTipoComunicacion
*
* @return string
*/		
     		
public function getNombreTipoComunicacion(){
	return $this->nombreTipoComunicacion; 
}

/**
* Set nombreTipoComunicacion
*
* @param string $nombreTipoComunicacion
*/
public function setNombreTipoComunicacion($nombreTipoComunicacion)
{
        $this->nombreTipoComunicacion = $nombreTipoComunicacion;
}



/**
* Get descripcionTipoComunicacion
*
* @return string
*/		
     		
public function getDescripcionTipoComunicacion(){
	return $this->descripcionTipoComunicacion; 
}

/**
* Set descripcionTipoComunicacion
*
* @param string $descripcionTipoComunicacion
*/
public function setDescripcionTipoComunicacion($descripcionTipoComunicacion)
{
        $this->descripcionTipoComunicacion = $descripcionTipoComunicacion;
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