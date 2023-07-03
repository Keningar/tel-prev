<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiMotivo
 *
 * @ORM\Table(name="ADMI_MOTIVO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiMotivoRepository")
 */
class AdmiMotivo
{


/**
* @var string $nombreMotivo
*
* @ORM\Column(name="NOMBRE_MOTIVO", type="string", nullable=false)
*/		
     		
private $nombreMotivo;


/**
* @var integer $relacionSistemaId
*
* @ORM\Column(name="RELACION_SISTEMA_ID", type="integer", nullable=false)
*/
		
private $relacionSistemaId;

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
* @var integer $id
*
* @ORM\Column(name="ID_MOTIVO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_MOTIVO", allocationSize=1, initialValue=1)
*/		
		
private $id;	

/**
* @var string $ctaContable
*
* @ORM\Column(name="CTA_CONTABLE", type="string", nullable=true)
*/		
     		
private $ctaContable;


/**
* @var integer $refMotivoId
*
* @ORM\Column(name="REF_MOTIVO_ID", type="integer", nullable=false)
*/
		
private $refMotivoId;


/**
* Get nombreMotivo
*
* @return string
*/		
     		
public function getNombreMotivo(){
	return $this->nombreMotivo; 
}

/**
* Set nombreMotivo
*
* @param string $nombreMotivo
*/
public function setNombreMotivo($nombreMotivo)
{
        $this->nombreMotivo = $nombreMotivo;
}


/**
* Get relacionSistemaId
*
* @return integer
*/		
     		
public function getRelacionSistemaId(){
	return $this->relacionSistemaId; 
}

/**
* Set relacionSistemaId
*
* @param integer $relacionSistemaId
*/
public function setRelacionSistemaId($relacionSistemaId)
{
        $this->relacionSistemaId = $relacionSistemaId;
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


/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get ctaContable
*
* @return string
*/		
     		
public function getCtaContable(){
	return $this->ctaContable; 
}

/**
* Set ctaContable
*
* @param string $ctaContable
*/
public function setCtaContable($ctaContable)
{
        $this->ctaContable = $ctaContable;
}

public function __toString()
{
    return $this->nombreMotivo;
}


/**
* Get refMotivoId
*
* @return integer
*/
public function getRefMotivoId()
{
	return $this->refMotivoId; 
}


/**
* Set refMotivoId
*
* @param integer $refMotivoId
*/
public function setRefMotivoId($refMotivoId)
{
    $this->refMotivoId = $refMotivoId;
}

}
