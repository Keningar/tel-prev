<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPagoHistorial
 *
 * @ORM\Table(name="INFO_PAGO_HISTORIAL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPagoHistorialRepository")
 */
class InfoPagoHistorial
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PAGO_HISTORIAL", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PAGO_HISTORIAL", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var InfoPagoCab
*
* @ORM\ManyToOne(targetEntity="InfoPagoCab")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PAGO_ID", referencedColumnName="ID_PAGO")
* })
*/
		
private $pagoId;

/**
* @var string $motivoId
*
* @ORM\Column(name="MOTIVO_ID", type="string", nullable=false)
*/		
     		
private $motivoId;

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
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var string $observacion
*
* @ORM\Column(name="OBSERVACION", type="string", nullable=true)
*/		
     		
private $observacion;

/**
* Get id
*
* @return integer
*/		
   		
public function getId(){
	return $this->id; 
}

/**
* Get pagoId
*
* @return telconet\schemaBundle\Entity\InfoPagoCab
*/		
     		
public function getPagoId(){
	return $this->pagoId; 
}

/**
* Set pagoId
*
* @param telconet\schemaBundle\Entity\InfoPagoCab $pagoId
*/
public function setPagoId(\telconet\schemaBundle\Entity\InfoPagoCab $pagoId)
{
        $this->pagoId = $pagoId;
}


/**
* Get motivoId
*
* @return string
*/		
     		
public function getMotivoId(){
	return $this->motivoId; 
}

/**
* Set motivoId
*
* @param string $motivoId
*/
public function setMotivoId($motivoId)
{
        $this->motivoId = $motivoId;
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
* Get observacion
*
* @return string
*/		
     		
public function getObservacion(){
	return $this->observacion; 
}

/**
* Set observacion
*
* @param string $observacion
*/
public function setObservacion($observacion)
{
        $this->observacion = $observacion;
}


}
