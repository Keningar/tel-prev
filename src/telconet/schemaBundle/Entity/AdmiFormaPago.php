<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiFormaPago
 *
 * @ORM\Table(name="ADMI_FORMA_PAGO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiFormaPagoRepository")
 */
class AdmiFormaPago
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_FORMA_PAGO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_FORMA_PAGO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $codigoFormaPago
*
* @ORM\Column(name="CODIGO_FORMA_PAGO", type="string", nullable=false)
*/		
     		
private $codigoFormaPago;

/**
* @var string $descripcionFormaPago
*
* @ORM\Column(name="DESCRIPCION_FORMA_PAGO", type="string", nullable=false)
*/		
     		
private $descripcionFormaPago;

/**
* @var string $esDepositable
*
* @ORM\Column(name="ES_DEPOSITABLE", type="string", nullable=false)
*/		
     		
private $esDepositable;

/**
* @var string $esMonetario
*
* @ORM\Column(name="ES_MONETARIO", type="string", nullable=false)
*/		
     		
private $esMonetario;

/**
* @var string $esPagoParaContrato
*
* @ORM\Column(name="ES_PAGO_PARA_CONTRATO", type="string", nullable=false)
*/		
     		
private $esPagoParaContrato;

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
* @var string $ctaContable
*
* @ORM\Column(name="CTA_CONTABLE", type="string", nullable=false)
*/		
     		
private $ctaContable;

/**
* @var string $visibleEnPago
*
* @ORM\Column(name="VISIBLE_EN_PAGO", type="string", nullable=false)
*/		
     		
private $visibleEnPago;

/**
* @var string $corteMasivo
*
* @ORM\Column(name="CORTE_MASIVO", type="string", nullable=false)
*/
private $corteMasivo;

/**
* @var string $tipoFormaPago
*
* @ORM\Column(name="TIPO_FORMA_PAGO", type="string", nullable=true)
*/
private $tipoFormaPago;


/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get codigoFormaPago
*
* @return string
*/		
     		
public function getCodigoFormaPago(){
	return $this->codigoFormaPago; 
}

/**
* Set codigoFormaPago
*
* @param string $codigoFormaPago
*/
public function setCodigoFormaPago($codigoFormaPago)
{
        $this->codigoFormaPago = $codigoFormaPago;
}


/**
* Get descripcionFormaPago
*
* @return string
*/		
     		
public function getDescripcionFormaPago(){
	return $this->descripcionFormaPago; 
}

/**
* Set descripcionFormaPago
*
* @param string $descripcionFormaPago
*/
public function setDescripcionFormaPago($descripcionFormaPago)
{
        $this->descripcionFormaPago = $descripcionFormaPago;
}


/**
* Get esDepositable
*
* @return string
*/		
     		
public function getEsDepositable(){
	return $this->esDepositable; 
}

/**
* Set esDepositable
*
* @param string $esDepositable
*/
public function setEsDepositable($esDepositable)
{
        $this->esDepositable = $esDepositable;
}


/**
* Get esMonetario
*
* @return string
*/		
     		
public function getEsMonetario(){
	return $this->esMonetario; 
}

/**
* Set esMonetario
*
* @param string $esMonetario
*/
public function setEsMonetario($esMonetario)
{
        $this->esMonetario = $esMonetario;
}


/**
* Get esPagoParaContrato
*
* @return string
*/		
     		
public function getEsPagoParaContrato(){
	return $this->esPagoParaContrato; 
}

/**
* Set esPagoParaContrato
*
* @param string $esPagoParaContrato
*/
public function setEsPagoParaContrato($esPagoParaContrato)
{
        $this->esPagoParaContrato = $esPagoParaContrato;
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

/**
* Get visibleEnPago
*
* @return string
*/		
     		
public function getVisibleEnPago(){
	return $this->visibleEnPago; 
}

/**
* Set visibleEnPago
*
* @param string $visibleEnPago
*/
public function setVisibleEnPago($visibleEnPago)
{
        $this->visibleEnPago = $visibleEnPago;
}

/**
* Get corteMasivo
*
* @return string
*/
public function getCorteMasivo() {
    return $this->corteMasivo;
}

/**
* Set corteMasivo
*
* @param string $corteMasivo
*/
public function setCorteMasivo($corteMasivo) {
    $this->corteMasivo = $corteMasivo;
}


/**
* Get tipoFormaPago
*
* @return string
*/	
public function getTipoFormaPago()
{
    return $this->tipoFormaPago; 
}

/**
* Set tipoFormaPago
*
* @param string $tipoFormaPago
*/
public function setTipoFormaPago($tipoFormaPago)
{
    $this->tipoFormaPago = $tipoFormaPago;
}

}
