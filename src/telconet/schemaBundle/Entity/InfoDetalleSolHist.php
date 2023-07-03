<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDetalleSolHist
 *
 * @ORM\Table(name="INFO_DETALLE_SOL_HIST")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDetalleSolHistRepository")
 */
class InfoDetalleSolHist
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_SOLICITUD_HISTORIAL", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DETALLE_SOL_HIST", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var InfoDetalleSolicitud
*
* @ORM\ManyToOne(targetEntity="InfoDetalleSolicitud")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="DETALLE_SOLICITUD_ID", referencedColumnName="ID_DETALLE_SOLICITUD")
* })
*/
		
private $detalleSolicitudId;

/**
* @var datetime $feIniPlan
*
* @ORM\Column(name="FE_INI_PLAN", type="datetime", nullable=true)
*/		
     		
private $feIniPlan;

/**
* @var datetime $feFinPlan
*
* @ORM\Column(name="FE_FIN_PLAN", type="datetime", nullable=true)
*/		
     		
private $feFinPlan;

/**
* @var string $observacion
*
* @ORM\Column(name="OBSERVACION", type="string", nullable=true)
*/		
     		
private $observacion;

/**
* @var integer $motivoId
*
* @ORM\Column(name="MOTIVO_ID", type="integer", nullable=true)
*/		
     		
private $motivoId;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=true)
*/		
     		
private $usrCreacion;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
*/		
     		
private $ipCreacion;

/**
* Get id
*
* @return integer
*/		
   		
public function getId(){
	return $this->id; 
}

/**
* Get detalleSolicitudId
*
* @return telconet\schemaBundle\Entity\InfoDetalleSolicitud
*/		
     		
public function getDetalleSolicitudId(){
	return $this->detalleSolicitudId; 
}

/**
* Set detalleSolicitudId
*
* @param telconet\schemaBundle\Entity\InfoDetalleSolicitud $detalleSolicitudId
*/
public function setDetalleSolicitudId(\telconet\schemaBundle\Entity\InfoDetalleSolicitud $detalleSolicitudId)
{
        $this->detalleSolicitudId = $detalleSolicitudId;
}

/**
* Get feIniPlan
*
* @return datetime
*/		
     		
public function getFeIniPlan(){
	return $this->feIniPlan; 
}

/**
* Set feIniPlan
*
* @param datetime $feIniPlan
*/
public function setFeIniPlan($feIniPlan)
{
        $this->feIniPlan = $feIniPlan;
}

/**
* Get feFinPlan
*
* @return datetime
*/		
     		
public function getFeFinPlan(){
	return $this->feFinPlan; 
}

/**
* Set feFinPlan
*
* @param datetime $feFinPlan
*/
public function setFeFinPlan($feFinPlan)
{
        $this->feFinPlan = $feFinPlan;
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
* Get motivoId
*
* @return integer
*/		
     		
public function getMotivoId(){
	return $this->motivoId; 
}

/**
* Set motivoId
*
* @param integer $motivoId
*/
public function setMotivoId($motivoId)
{
        $this->motivoId = $motivoId;
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


}