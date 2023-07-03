<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiFormatoDebito
 *
 * @ORM\Table(name="INFO_PAGO_AUTOMATICO_HIST")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPagoAutomaticoHistRepository")
 */
class InfoPagoAutomaticoHist
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PAGO_AUTOMATICO_HIST", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PAGO_AUTOMATICO_HIST", allocationSize=1, initialValue=1)
*/		
		
private $id;


/**
* @var InfoPagoAutomaticoDet
*
* @ORM\ManyToOne(targetEntity="InfoPagoAutomaticoDet")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="DETALLE_PAGO_AUTOMATICO_ID", referencedColumnName="ID_DETALLE_PAGO_AUTOMATICO")
* })
*/		
private $detallePagoAutomaticoId;
	

/**
* @var string $observacion
*
* @ORM\Column(name="OBSERVACION", type="string", nullable=false)
*/		
     		
private $observacion;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
*/		
     		
private $ipCreacion;


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
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;


/**
* @var integer $motivoId
*
* @ORM\Column(name="MOTIVO_ID", type="integer", nullable=true)
*/

private $motivoId;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}


/**
* Get detallePagoAutomaticoId
*
* @return telconet\schemaBundle\Entity\InfoPagoAutomaticoDet
*/		
     		
public function getDetallePagoAutomaticoId()
{
	return $this->detallePagoAutomaticoId; 
}

/**
* Set detallePagoAutomaticoId
*
* @param telconet\schemaBundle\Entity\InfoPagoAutomaticoDet $detallePagoAutomaticoId
*/
public function setDetallePagoAutomaticoId($detallePagoAutomaticoId)
{
    $this->detallePagoAutomaticoId = $detallePagoAutomaticoId;
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

/**
* Get ipCreacion
*
* @return string
*/		
     		
public function getIpCreacion(){
	return $this->ipCreacion; 
}

/**
* Set ipCreacion
*
* @param string $ipCreacion
*/
public function setIpCreacion($ipCreacion)
{
        $this->ipCreacion = $ipCreacion;
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
* Get motivoId
*
* @return integer
*/		
     		
public function getMotivoId(){
	return $this->motivoId; 
}

/**
* Set integer
*
* @param integer $motivoId
*/
public function setMotivoId($motivoId)
{
        $this->motivoId = $motivoId;
}

}
