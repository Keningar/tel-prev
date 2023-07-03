<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTipoNotificacion
 *
 * @ORM\Table(name="ADMI_TIPO_NOTIFICACION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTipoNotificacionRepository")
 */
class AdmiTipoNotificacion
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
* @ORM\Column(name="ID_TIPO_NOTIFICACION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TIPO_NOTIFICACION", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $nombreTipoNotificacion
*
* @ORM\Column(name="NOMBRE_TIPO_NOTIFICACION", type="string", nullable=false)
*/		
     		
private $nombreTipoNotificacion;

/**
* @var string $descripcionTipoNotificacion
*
* @ORM\Column(name="DESCRIPCION_TIPO_NOTIFICACION", type="string", nullable=true)
*/		
     		
private $descripcionTipoNotificacion;

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
* Get nombreTipoNotificacion
*
* @return string
*/		
     		
public function getNombreTipoNotificacion(){
	return $this->nombreTipoNotificacion; 
}

/**
* Set nombreTipoNotificacion
*
* @param string $nombreTipoNotificacion
*/
public function setNombreTipoNotificacion($nombreTipoNotificacion)
{
        $this->nombreTipoNotificacion = $nombreTipoNotificacion;
}


/**
* Get descripcionTipoNotificacion
*
* @return string
*/		
     		
public function getDescripcionTipoNotificacion(){
	return $this->descripcionTipoNotificacion; 
}

/**
* Set descripcionTipoNotificacion
*
* @param string $descripcionTipoNotificacion
*/
public function setDescripcionTipoNotificacion($descripcionTipoNotificacion)
{
        $this->descripcionTipoNotificacion = $descripcionTipoNotificacion;
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

public function __toString()
{
        return $this->nombreTipoNotificacion;
}
}