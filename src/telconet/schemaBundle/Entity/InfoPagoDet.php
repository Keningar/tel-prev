<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPagoDet
 *
 * @ORM\Table(name="INFO_PAGO_DET")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPagoDetRepository")
 */
class InfoPagoDet
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PAGO_DET", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PAGO_DET", allocationSize=1, initialValue=1)
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
* @var integer $formaPagoId
*
* @ORM\Column(name="FORMA_PAGO_ID", type="integer", nullable=true)
*/

private $formaPagoId;

/**
* @var integer $referenciaId
*
* @ORM\Column(name="REFERENCIA_ID", type="integer", nullable=true)
*/		
     		
private $referenciaId;

/**
* @var float $valorPago
*
* @ORM\Column(name="VALOR_PAGO", type="float", nullable=true)
*/		
     		
private $valorPago;

/**
* @var integer $bancoTipoCuentaId
*
* @ORM\Column(name="BANCO_TIPO_CUENTA_ID", type="integer", nullable=true)
*/
		
private $bancoTipoCuentaId;

/**
* @var integer $bancoCtaContableId
*
* @ORM\Column(name="BANCO_CTA_CONTABLE_ID", type="integer", nullable=true)
*/
		
private $bancoCtaContableId;

/**
* @var string $numeroReferencia
*
* @ORM\Column(name="NUMERO_REFERENCIA", type="string", nullable=true)
*/		
     		
private $numeroReferencia;

/**
* @var date $feAplicacion
*
* @ORM\Column(name="FE_APLICACION", type="date", nullable=true)
*/		
     		
private $feAplicacion;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var string $comentario
*
* @ORM\Column(name="COMENTARIO", type="string", nullable=true)
*/		
     		
private $comentario;

/**
* @var string $depositado
*
* @ORM\Column(name="DEPOSITADO", type="string", nullable=true)
*/		
     		
private $depositado;



/**
* @var string $depositoPagoId
*
* @ORM\Column(name="DEPOSITO_PAGO_ID", type="integer", nullable=true)
*/

private $depositoPagoId;


/**
* @var string $numeroCuentaBanco
*
* @ORM\Column(name="NUMERO_CUENTA_BANCO", type="string", nullable=true)
*/		
     		
private $numeroCuentaBanco;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var date $feUltMod
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
* @var datetime $feDeposito
*
* @ORM\Column(name="FE_DEPOSITO", type="datetime", nullable=true)
*/		
     		
private $feDeposito;


/**
* @var integer $cuentaContableId
*
* @ORM\Column(name="CUENTA_CONTABLE_ID", type="integer", nullable=true)
*/
		
private $cuentaContableId;


/**
* @var string $contabilizado
*
* @ORM\Column(name="CONTABILIZADO", type="string", nullable=true)
*/		
     		
private $contabilizado='N';

/**
* @var integer $referenciaDetPagoAutId
*
* @ORM\Column(name="REFERENCIA_DET_PAGO_AUT_ID", type="integer", nullable=true)
*/
		
private $referenciaDetPagoAutId;

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
* Get formaPagoId
*
* @return integer
*/		
     		
public function getFormaPagoId(){
	return $this->formaPagoId; 
}

/**
* Set formaPagoId
*
* @param integer $formaPagoId
*/
public function setFormaPagoId($formaPagoId)
{
        $this->formaPagoId = $formaPagoId;
}


/**
* Get referenciaId
*
* @return integer
*/		
     		
public function getReferenciaId(){
	return $this->referenciaId; 
}

/**
* Set referenciaId
*
* @param integer $referenciaId
*/
public function setReferenciaId($referenciaId)
{
        $this->referenciaId = $referenciaId;
}


/**
* Get valorPago
*
* @return float
*/		
     		
public function getValorPago(){
	return $this->valorPago; 
}

/**
* Set valorPago
*
* @param float $valorPago
*/
public function setValorPago($valorPago)
{
        $this->valorPago = $valorPago;
}


/**
* Get bancoTipoCuentaId
*
* @return integer
*/		
     		
public function getBancoTipoCuentaId(){
	return $this->bancoTipoCuentaId; 
}

/**
* Set bancoTipoCuentaId
*
* @param integer $bancoTipoCuentaId
*/
public function setBancoTipoCuentaId($bancoTipoCuentaId)
{
        $this->bancoTipoCuentaId = $bancoTipoCuentaId;
}


/**
* Get bancoCtaContableId
*
* @return integer
*/		
     		
public function getBancoCtaContableId(){
	return $this->bancoCtaContableId; 
}

/**
* Set bancoCtaContableId
*
* @param integer $bancoCtaContableId
*/
public function setBancoCtaContableId($bancoCtaContableId)
{
        $this->bancoCtaContableId= $bancoCtaContableId;
}

/**
* Get numeroReferencia
*
* @return string
*/		
     		
public function getNumeroReferencia(){
	return $this->numeroReferencia; 
}

/**
* Set numeroReferencia
*
* @param string $numeroReferencia
*/
public function setNumeroReferencia($numeroReferencia)
{
        $this->numeroReferencia = $numeroReferencia;
}


/**
* Get feAplicacion
*
* @return 
*/		
     		
public function getFeAplicacion(){
	return $this->feAplicacion; 
}

/**
* Set feAplicacion
*
* @param  $feAplicacion
*/
public function setFeAplicacion($feAplicacion)
{
        $this->feAplicacion = $feAplicacion;
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
* Get comentario
*
* @return 
*/		
     		
public function getComentario(){
	return $this->comentario; 
}

/**
* Set comentario
*
* @param  $comentario
*/
public function setComentario($comentario)
{
        $this->comentario = $comentario;
}


/**
* Get depositado
*
* @return string
*/		
     		
public function getDepositado(){
	return $this->depositado; 
}

/**
* Set depositado
*
* @param string $depositado
*/
public function setDepositado($depositado)
{
        $this->depositado = $depositado;
}


/**
* Get depositoPagoId
*
* @return integer
*/		
     		
public function getDepositoPagoId(){
	return $this->depositoPagoId; 
}

/**
* Set depositoPagoId
*
* @param integer $depositoPagoId
*/
public function setDepositoPagoId($depositoPagoId)
{
        $this->depositoPagoId = $depositoPagoId;
}


/**
* Get numeroCuentaBanco
*
* @return string
*/		
     		
public function getNumeroCuentaBanco(){
	return $this->numeroCuentaBanco; 
}

/**
* Set numeroCuentaBanco
*
* @param string $numeroCuentaBanco
*/
public function setNumeroCuentaBanco($numeroCuentaBanco)
{
        $this->numeroCuentaBanco = $numeroCuentaBanco;
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



/**
* Get feDeposito
*
* @return datetime
*/		
     		
public function getFeDeposito(){
	return $this->feDeposito; 
}

/**
* Set feDeposito
*
* @param datetime $feDeposito
*/
public function setFeDeposito($feDeposito)
{
        $this->feDeposito = $feDeposito;
}

/**
* Get cuentaContableId
*
* @return integer
*/
public function getCuentaContableId()
{
    return $this->cuentaContableId;
}

/**
* Set cuentaContableId
*
* @param integer $cuentaContableId
*/
public function setCuentaContableId($cuentaContableId)
{
    $this->cuentaContableId = $cuentaContableId;
}


/**
* Get referenciaDetPagoAutId
*
* @return integer
*/
public function getReferenciaDetPagoAutId()
{
    return $this->referenciaDetPagoAutId;
}

/**
* Set referenciaDetPagoAutId
*
* @param integer $referenciaDetPagoAutId
*/
public function setReferenciaDetPagoAutId($referenciaDetPagoAutId)
{
    $this->referenciaDetPagoAutId = $referenciaDetPagoAutId;
}


/**
* Get contabilizado
*
* @return string
*/
public function getContabilizado()
{
    return $this->contabilizado;
}

/**
* Set contabilizado
*
* @param integer $contabilizado
*/
public function setContabilizado($contabilizado)
{
    $this->contabilizado = $contabilizado;
}


}


