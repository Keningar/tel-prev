<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiPeriodoDet
 *
 * @ORM\Table(name="ADMI_PERIODO_DET")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiPeriodoDetRepository")
 */
class AdmiPeriodoDet
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_DETALLE_PERIODO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_PERIODO_DET", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiPeriodoCab
*
* @ORM\ManyToOne(targetEntity="AdmiPeriodoCab")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PERIODO_ID", referencedColumnName="ID_PERIODO")
* })
*/
		
private $periodoId;

/**
* @var string $facturado
*
* @ORM\Column(name="FACTURADO", type="string", nullable=true)
*/		
     		
private $facturado;

/**
* @var InfoDocumentoFinancieroCab
*
* @ORM\ManyToOne(targetEntity="InfoDocumentoFinancieroCab")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="DOCUMENTO_ID", referencedColumnName="ID_DOCUMENTO")
* })
*/
		
private $documentoId;

/**
* @var LONG $observacion
*
* @ORM\Column(name="OBSERVACION", type="LONG", nullable=true)
*/		
     		
private $observacion;

/**
* @var DATE $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="DATE", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var DATE $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="DATE", nullable=true)
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
* @var string $usrFacturacion
*
* @ORM\Column(name="USR_FACTURACION", type="string", nullable=true)
*/		
     		
private $usrFacturacion;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get periodoId
*
* @return telconet\schemaBundle\Entity\AdmiPeriodoCab
*/		
     		
public function getPeriodoId(){
	return $this->periodoId; 
}

/**
* Set periodoId
*
* @param telconet\schemaBundle\Entity\AdmiPeriodoCab $periodoId
*/
public function setPeriodoId(\telconet\schemaBundle\Entity\AdmiPeriodoCab $periodoId)
{
        $this->periodoId = $periodoId;
}


/**
* Get facturado
*
* @return string
*/		
     		
public function getFacturado(){
	return $this->facturado; 
}

/**
* Set facturado
*
* @param string $facturado
*/
public function setFacturado($facturado)
{
        $this->facturado = $facturado;
}


/**
* Get documentoId
*
* @return telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab
*/		
     		
public function getDocumentoId(){
	return $this->documentoId; 
}

/**
* Set documentoId
*
* @param telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab $documentoId
*/
public function setDocumentoId(\telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab $documentoId)
{
        $this->documentoId = $documentoId;
}


/**
* Get observacion
*
* @return 
*/		
     		
public function getObservacion(){
	return $this->observacion; 
}

/**
* Set observacion
*
* @param  $observacion
*/
public function setObservacion($observacion)
{
        $this->observacion = $observacion;
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


/**
* Get usrFacturacion
*
* @return string
*/		
     		
public function getUsrFacturacion(){
	return $this->usrFacturacion; 
}

/**
* Set usrFacturacion
*
* @param string $usrFacturacion
*/
public function setUsrFacturacion($usrFacturacion)
{
        $this->usrFacturacion = $usrFacturacion;
}

}