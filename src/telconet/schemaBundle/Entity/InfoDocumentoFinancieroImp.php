<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDocumentoFinancieroImp
 *
 * @ORM\Table(name="INFO_DOCUMENTO_FINANCIERO_IMP")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDocumentoFinancieroImpRepository")
 */
class InfoDocumentoFinancieroImp
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_DOC_IMP", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DOC_FINANCIERO_IMP", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $detalleDocId
*
* @ORM\Column(name="DETALLE_DOC_ID", type="integer", nullable=true)
*/		
     		
private $detalleDocId;

/**
* @var integer $impuestoId
*
* @ORM\Column(name="IMPUESTO_ID", type="integer", nullable=true)
*/	

private $impuestoId;

/**
* @var integer $valorImpuesto
*
* @ORM\Column(name="VALOR_IMPUESTO", type="float", nullable=true)
*/		
     		
private $valorImpuesto;

/**
* @var integer $porcentaje
*
* @ORM\Column(name="PORCENTAJE", type="integer", nullable=true)
*/		
     		
private $porcentaje;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
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
* Get detalleDocId
*
* @return integer
*/		
     		
public function getDetalleDocId(){
	return $this->detalleDocId; 
}

/**
* Set detalleDocId
*
* @param integer $detalleDocId
*/
public function setDetalleDocId($detalleDocId)
{
        $this->detalleDocId = $detalleDocId;
}


/**
* Get impuestoId
*
* @return integer
*/		
     		
public function getImpuestoId(){
	return $this->impuestoId; 
}

/**
* Set impuestoId
*
* @param integer $impuestoId
*/
public function setImpuestoId($impuestoId)
{
        $this->impuestoId = $impuestoId;
}


/**
* Get valorImpuesto
*
* @return float
*/		
public function getValorImpuesto()
{
    return $this->valorImpuesto; 
}

/**
* Set valorImpuesto
*
* @param float $valorImpuesto
*/
public function setValorImpuesto($valorImpuesto)
{
    $this->valorImpuesto = $valorImpuesto;
}


/**
* Get porcentaje
*
* @return integer
*/		
     		
public function getPorcentaje(){
	return $this->porcentaje; 
}

/**
* Set porcentaje
*
* @param integer $porcentaje
*/
public function setPorcentaje($porcentaje)
{
        $this->porcentaje = $porcentaje;
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
