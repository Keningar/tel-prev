<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDebitoGeneralHistorial
 *
 * @ORM\Table(name="INFO_DEBITO_GENERAL_HISTORIAL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDebitoGeneralHistorialRepository")
 */
class InfoDebitoGeneralHistorial
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_DEBITO_GENERAL_HISTORIAL", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DEBITO_GENERAL_HISTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var InfoDebitoGeneral
*
* @ORM\ManyToOne(targetEntity="InfoDebitoGeneral")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="DEBITO_GENERAL_ID", referencedColumnName="ID_DEBITO_GENERAL")
* })
*/
		
private $debitoGeneralId;

/**
* @var string $cuentaContableId
*
* @ORM\ManyToOne(targetEntity="AdmiCuentaContable")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="CUENTA_CONTABLE_ID", referencedColumnName="ID_CUENTA_CONTABLE")
* })
*/		
     		
private $cuentaContableId;


/**
* @var string $numeroDocumento
*
* @ORM\Column(name="NUMERO_DOCUMENTO", type="string", nullable=false)
*/		
     		
private $numeroDocumento;


/**
* @var datetime $feDocumento
*
* @ORM\Column(name="FE_DOCUMENTO", type="datetime", nullable=false)
*/		
     		
private $feDocumento;



/**
* @var float $porcentajeComisionBco
*
* @ORM\Column(name="PORCENTAJE_COMISION_BCO", type="float", nullable=true)
*/		

private $porcentajeComisionBco;
    

/**
* @var string $contieneRetencionFte
*
* @ORM\Column(name="CONTIENE_RETENCION_FTE", type="string", nullable=false)
*/		
     		
private $contieneRetencionFte;


/**
* @var string $contieneRetencionIva
*
* @ORM\Column(name="CONTIENE_RETENCION_IVA", type="string", nullable=false)
*/		
     		
private $contieneRetencionIva;



/**
* @var float $valorComisionBco
*
* @ORM\Column(name="VALOR_COMISION_BCO", type="float", nullable=true)
*/		

private $valorComisionBco;


/**
* @var float $valorRetencionFuente
*
* @ORM\Column(name="VALOR_RETENCION_FUENTE", type="float", nullable=true)
*/		

private $valorRetencionFuente;

/**
* @var float $valorRetencionIva
*
* @ORM\Column(name="VALOR_RETENCION_IVA", type="float", nullable=true)
*/		

private $valorRetencionIva;

/**
* @var float $valorNeto
*
* @ORM\Column(name="VALOR_NETO", type="float", nullable=true)
*/		

private $valorNeto;


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
* @var string $observacionDescuadre
*
* @ORM\Column(name="OBSERVACION_DESCUADRE", type="string", nullable=true)
*/    
         
private $observacionDescuadre;

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
* @return telconet\schemaBundle\Entity\InfoDebitoGeneralHistorial
*/		
     		
public function getPagoId(){
	return $this->pagoId; 
}

/**
* Set pagoId
*
* @param telconet\schemaBundle\Entity\InfoDebitoGeneralHistorial $pagoId
*/
public function setPagoId(\telconet\schemaBundle\Entity\InfoDebitoGeneralHistorial $pagoId)
{
        $this->pagoId = $pagoId;
}


/**
* Get debitoGeneralId
*
* @return telconet\schemaBundle\Entity\InfoDebitoGeneral
*/		
     		
public function getDebitoGeneralId(){
	return $this->debitoGeneralId; 
}

/**
* Set debitoGeneralId
*
* @param telconet\schemaBundle\Entity\InfoDebitoGeneral $debitoGeneralId
*/
public function setDebitoGeneralId(\telconet\schemaBundle\Entity\InfoDebitoGeneral $debitoGeneralId)
{
        $this->debitoGeneralId = $debitoGeneralId;
}



/**
* Get cuentaContableId
*
* @return int
*/		

public function getCuentaContableId(){
    return $this->cuentaContableId; 
}

/**
* Set cuentaContableId
*
* @param int $cuentaContableId
*/
public function setCuentaContableId($cuentaContableId)
{
    $this->cuentaContableId = $cuentaContableId;
}


/**
* Get numeroDocumento
*
* @return string
*/		
     		
public function getNumeroDocumento(){
	return $this->numeroDocumento; 
}

/**
* Set numeroDocumento
*
* @param string $numeroDocumento
*/
public function setNumeroDocumento($numeroDocumento)
{
        $this->numeroDocumento = $numeroDocumento;
}


/**
* Get feDocumento
*
* @return datetime
*/		
     		
public function getFeDocumento(){
	return $this->feDocumento; 
}

/**
* Set feDocumento
*
* @param datetime $feDocumento
*/
public function setFeDocumento($feDocumento)
{
        $this->feDocumento = $feDocumento;
}


/**
* Get porcentajeComisionBco
*
* @return float
*/		

public function getPorcentajeComisionBco(){
    return $this->porcentajeComisionBco; 
}

/**
* Set porcentajeComisionBco
*
* @param float $porcentajeComisionBco
*/
public function setPorcentajeComisionBco($porcentajeComisionBco)
{
    $this->porcentajeComisionBco = $porcentajeComisionBco;
}
    
/**
* Get contieneRetencionFte
*
* @return string
*/		
     		
public function getContieneRetencionFte(){
	return $this->contieneRetencionFte; 
}

/**
* Set contieneRetencionFte
*
* @param string $contieneRetencionFte
*/
public function setContieneRetencionFte($contieneRetencionFte)
{
        $this->contieneRetencionFte = $contieneRetencionFte;
}


    
/**
* Get contieneRetencionIva
*
* @return string
*/		
     		
public function getContieneRetencionIva(){
	return $this->contieneRetencionIva; 
}

/**
* Set contieneRetencionIva
*
* @param string $contieneRetencionIva
*/
public function setContieneRetencionIva($contieneRetencionIva)
{
        $this->contieneRetencionIva = $contieneRetencionIva;
}



/**
* Get valorComisionBco
*
* @return float
*/		

public function getValorComisionBco(){
    return $this->valorComisionBco; 
}

/**
* Set valorComisionBco
*
* @param float $valorComisionBco
*/
public function setValorComisionBco($valorComisionBco)
{
    $this->valorComisionBco = $valorComisionBco;
}    



/**
* Get valorRetencionFuente
*
* @return float
*/		

public function getValorRetencionFuente(){
    return $this->valorRetencionFuente; 
}

/**
* Set valorRetencionFuente
*
* @param float $valorRetencionFuente
*/
public function setValorRetencionFuente($valorRetencionFuente)
{
    $this->valorRetencionFuente = $valorRetencionFuente;
}    



/**
* Get valorRetencionIva
*
* @return float
*/		

public function getValorRetencionIva(){
    return $this->valorRetencionIva; 
}

/**
* Set valorRetencionIva
*
* @param float $valorRetencionIva
*/
public function setValorRetencionIva($valorRetencionIva)
{
    $this->valorRetencionIva = $valorRetencionIva;
}    




/**
* Get valorNeto
*
* @return float
*/		

public function getValorNeto(){
    return $this->valoNeto; 
}

/**
* Set valorNeto
*
* @param float $valorNeto
*/
public function setValorNeto($valorNeto)
{
    $this->valorNeto = $valorNeto;
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


/*
* Get observacionDescuadre
*
* @return string
*/    
         
public function getObservacionDescuadre(){
  return $this->observacionDescuadre; 
}

/*
* Set observacionDescuadre
*
* @param string $observacionDescuadre
*/
public function setObservacionDescuadre($observacionDescuadre)
{
        $this->observacionDescuadre = $observacionDescuadre;
}


}
