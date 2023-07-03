<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTipoSolicitud
 *
 * @ORM\Table(name="ADMI_TIPO_SOLICITUD")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTipoSolicitudRepository")
 */
class AdmiTipoSolicitud
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_TIPO_SOLICITUD", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TIPO_SOLICITUD", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $descripcionSolicitud
*
* @ORM\Column(name="DESCRIPCION_SOLICITUD", type="string", nullable=false)
*/		
     		
private $descripcionSolicitud;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
*/		
     		
private $feCreacion;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

/**
* @var integer $tareaId
*
* @ORM\Column(name="TAREA_ID", type="integer", nullable=true)
*/
		
private $tareaId;

/**
* @var integer $itemMenuId
*
* @ORM\Column(name="ITEM_MENU_ID", type="integer", nullable=true)
*/
		
private $itemMenuId;

/**
* @var integer $procesoId
*
* @ORM\Column(name="PROCESO_ID", type="integer", nullable=true)
*/
		
private $procesoId;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get descripcionSolicitud
*
* @return string
*/		
     		
public function getDescripcionSolicitud(){
	return $this->descripcionSolicitud; 
}

/**
* Set descripcionSolicitud
*
* @param string $descripcionSolicitud
*/
public function setDescripcionSolicitud($descripcionSolicitud)
{
        $this->descripcionSolicitud = $descripcionSolicitud;
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


/**
* Get tareaId
*
* @return integer
*/		
     		
public function getTareaId(){
	return $this->tareaId; 
}

/**
* Set tareaId
*
* @param integer $tareaId
*/
public function setTareaId($tareaId)
{
        $this->tareaId = $tareaId;
}


/**
* Get itemMenuId
*
* @return integer
*/		
     		
public function getItemMenuId(){
	return $this->itemMenuId; 
}

/**
* Set itemMenuId
*
* @param integer $itemMenuId
*/
public function setItemMenuId($itemMenuId)
{
        $this->itemMenuId = $itemMenuId;
}


/**
* Get procesoId
*
* @return integer
*/		
     		
public function getProcesoId(){
	return $this->procesoId; 
}

/**
* Set procesoId
*
* @param integer $procesoId
*/
public function setProcesoId($procesoId)
{
        $this->procesoId = $procesoId;
}


public function __toString()
{
        return $this->descripcionSolicitud;
}

}