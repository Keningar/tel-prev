<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoContratoFormaPagoHist
 *
 * @ORM\Table(name="INFO_CONTRATO_FORMA_PAGO_HIST")
 * @ORM\Entity
 */
class InfoContratoFormaPagoHist
{


/**
* @var string $mesVencimiento
*
* @ORM\Column(name="MES_VENCIMIENTO", type="string", nullable=true)
*/		
     		
private $mesVencimiento;

/**
* @var string $anioVencimiento
*
* @ORM\Column(name="ANIO_VENCIMIENTO", type="string", nullable=true)
*/		
     		
private $anioVencimiento;

/**
* @var integer $id
*
* @ORM\Column(name="ID_DATOS_PAGO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_CONTRATO_FORMA_PAGO_H", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var InfoContrato
*
* @ORM\ManyToOne(targetEntity="InfoContrato")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="CONTRATO_ID", referencedColumnName="ID_CONTRATO")
* })
*/
		
private $contratoId;

/**
* @var AdmiBancoTipoCuenta
*
* @ORM\ManyToOne(targetEntity="AdmiBancoTipoCuenta")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="BANCO_TIPO_CUENTA_ID", referencedColumnName="ID_BANCO_TIPO_CUENTA")
* })
*/
		
private $bancoTipoCuentaId;

/**
* @var string $numeroCtaTarjeta
*
* @ORM\Column(name="NUMERO_CTA_TARJETA", type="string", nullable=true)
*/		
     		
private $numeroCtaTarjeta;

/**
* @var string $numeroDebitoBanco
*
* @ORM\Column(name="NUMERO_DEBITO_BANCO", type="string", nullable=true)
*/		
     		
private $numeroDebitoBanco;

/**
* @var string $codigoVerificacion
*
* @ORM\Column(name="CODIGO_VERIFICACION", type="string", nullable=true)
*/		
     		
private $codigoVerificacion;

/**
* @var string $titularCuenta
*
* @ORM\Column(name="TITULAR_CUENTA", type="string", nullable=true)
*/		
     		
private $titularCuenta;

/**
* @var string $cedulaTitular
*
* @ORM\Column(name="CEDULA_TITULAR", type="string", nullable=true)
*/		
     		
private $cedulaTitular;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
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
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;


/**
* @var integer $tipoCuentaId
*
* @ORM\Column(name="TIPO_CUENTA_ID", type="integer", nullable=true)

*/

private $tipoCuentaId;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;

/**
* @var integer $formaPago
*
* @ORM\Column(name="FORMA_PAGO", type="integer", nullable=true)
*/		
     		
private $formaPago;

/**
* @var integer $motivoId
*
* @ORM\Column(name="MOTIVO_ID", type="integer", nullable=true)
*/		
     		
private $motivoId;

/**
* @var integer $numeroActa
*
* @ORM\Column(name="NUMERO_ACTA", type="integer", nullable=true)
*/		
     		
private $numeroActa;


/**
* @var integer $formaPagoActualId
*
* @ORM\Column(name="FORMA_PAGO_ACTUAL_ID", type="integer", nullable=true)
*/		
     		
private $formaPagoActualId;

/**
* @var integer $dctoAplicado
*
* @ORM\Column(name="DCTO_APLICADO", type="integer", nullable=true)
*/		
     		
private $dctoAplicado;

/**
* @var string $factura
*
* @ORM\Column(name="FACTURA", type="string", nullable=true)
*/		
     		
private $factura;

/**
* @var string $observacion
*
* @ORM\Column(name="OBSERVACION", type="string", nullable=true)
*/		
     		
private $observacion;

/**
* @var string $nombreArchivoAbu
*
* @ORM\Column(name="NOMBRE_ARCHIVO_ABU", type="string", nullable=true)
*/      
            
private $nombreArchivoAbu;

/**
* Get motivoId
*
* @return integer
*/	
public function getMotivoId() {
    return $this->motivoId;
}

/**
* Set motivoId
*
* @param integer $motivoId
*/
public function setMotivoId($motivoId) {
    $this->motivoId = $motivoId;
}

/**
* Get numeroActa
*
* @return integer
*/	
public function getNumeroActa() {
    return $this->numeroActa;
}

/**
* Set numeroActa
*
* @param integer $numeroActa
*/
public function setNumeroActa($numeroActa) {
    $this->numeroActa = $numeroActa;
}

/**
* Get numeroActa
*
* @return integer
*/	
function getFormaPagoActualId() {
    return $this->formaPagoActualId;
}

/**
* Set formaPagoActualId
*
* @param integer $formaPagoActualId
*/
function setFormaPagoActualId($formaPagoActualId) {
    $this->formaPagoActualId = $formaPagoActualId;
}

/**
* Get factura
*
* @return string
*/	
public function getFactura() {
    return $this->factura;
}

/**
* Set factura
*
* @param string $factura
*/
public function setFactura($factura) {
    $this->factura = $factura;
}

/**
* Get observacion
*
* @return string
*/		
public function getObservacion() {
    return $this->observacion;
}

/**
* Set observacion
*
* @param integer $observacion
*/
public function setObservacion($observacion) {
    $this->observacion = $observacion;
}

/**
* Get nombreArchivoAbu
*
* @return string
*/      
public function getNombreArchivoAbu() {
   return $this->nombreArchivoAbu;
}
    
/**
* Set nombreArchivoAbu
*
* @param string $nombreArchivoAbu
*/
public function setNombreArchivoAbu($nombreArchivoAbu) {
    $this->nombreArchivoAbu = $nombreArchivoAbu;
}

/**
* Get formaPago
*
* @return integer
*/	
public function getFormaPago() {
    return $this->formaPago;
}

/**
* Set formaPago
*
* @param integer $formaPago
*/
public function setFormaPago($formaPago) {
    $this->formaPago = $formaPago;
}


/**
* Get mesVencimiento
*
* @return string
*/		
     		
public function getMesVencimiento(){
	return $this->mesVencimiento; 
}

/**
* Set mesVencimiento
*
* @param string $mesVencimiento
*/
public function setMesVencimiento($mesVencimiento)
{
        $this->mesVencimiento = $mesVencimiento;
}


/**
* Get anioVencimiento
*
* @return string
*/		
     		
public function getAnioVencimiento(){
	return $this->anioVencimiento; 
}

/**
* Set anioVencimiento
*
* @param string $anioVencimiento
*/
public function setAnioVencimiento($anioVencimiento)
{
        $this->anioVencimiento = $anioVencimiento;
}

 		
public function getFechaVencimiento(){
	$anio="";
	$mes=sprintf("%02s",$this->mesVencimiento);
	if(strlen($this->anioVencimiento)==2)
		$anio="20".$this->anioVencimiento;
	else
		$anio=$this->anioVencimiento;
	return $anio.$mes; 
}

public function getNumeroCtaTarjeta6Digitos(){
	$numero=substr($this->numeroCtaTarjeta,0,6);
	return $numero; 
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
* Get contratoId
*
* @return telconet\schemaBundle\Entity\InfoContrato
*/		
     		
public function getContratoId(){
	return $this->contratoId; 
}

/**
* Set contratoId
*
* @param telconet\schemaBundle\Entity\InfoContrato $contratoId
*/
public function setContratoId(\telconet\schemaBundle\Entity\InfoContrato $contratoId)
{
        $this->contratoId = $contratoId;
}


/**
* Get bancoTipoCuentaId
*
* @return telconet\schemaBundle\Entity\AdmiBancoTipoCuenta
*/		
     		
public function getBancoTipoCuentaId(){
	return $this->bancoTipoCuentaId; 
}

/**
* Set bancoTipoCuentaId
*
* @param telconet\schemaBundle\Entity\AdmiBancoTipoCuenta $bancoTipoCuentaId
*/
public function setBancoTipoCuentaId(\telconet\schemaBundle\Entity\AdmiBancoTipoCuenta $bancoTipoCuentaId)
{
        $this->bancoTipoCuentaId = $bancoTipoCuentaId;
}


/**
* Get numeroCtaTarjeta
*
* @return string
*/		
     		
public function getNumeroCtaTarjeta(){
	return $this->numeroCtaTarjeta; 
}

/**
* Set numeroCtaTarjeta
*
* @param string $numeroCtaTarjeta
*/
public function setNumeroCtaTarjeta($numeroCtaTarjeta)
{
        $this->numeroCtaTarjeta = $numeroCtaTarjeta;
}


/**
* Get numeroDebitoBanco
*
* @return string
*/		
     		
public function getNumeroDebitoBanco(){
	return $this->numeroDebitoBanco; 
}

/**
* Set numeroDebitoBanco
*
* @param string $numeroDebitoBanco
*/
public function setNumeroDebitoBanco($numeroDebitoBanco)
{
        $this->numeroDebitoBanco = $numeroDebitoBanco;
}


/**
* Get codigoVerificacion
*
* @return string
*/		
     		
public function getCodigoVerificacion(){
	return $this->codigoVerificacion; 
}

/**
* Set codigoVerificacion
*
* @param string $codigoVerificacion
*/
public function setCodigoVerificacion($codigoVerificacion)
{
        $this->codigoVerificacion = $codigoVerificacion;
}


/**
* Get titularCuenta
*
* @return string
*/		
     		
public function getTitularCuenta(){
	return $this->titularCuenta; 
}

/**
* Set titularCuenta
*
* @param string $titularCuenta
*/
public function setTitularCuenta($titularCuenta)
{
        $this->titularCuenta = $titularCuenta;
}

/**
* Get cedulaTitular
*
* @return string
*/		
     		
public function getCedulaTitular(){
	return $this->cedulaTitular; 
}

/**
* Set cedulaTitular
*
* @param string $cedulaTitular
*/
public function setCedulaTitular($cedulaTitular)
{
        $this->cedulaTitular = $cedulaTitular;
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
* Get tipoCuentaId
*
* @return integer
*/		
     		
public function getTipoCuentaId(){
	return $this->tipoCuentaId; 
}

/**
* Set tipoCuentaId
*
* @param integer $TipoCuentaId
*/
public function setTipoCuentaId($TipoCuentaId)
{
        $this->tipoCuentaId = $TipoCuentaId;
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
* Get dctoAplicado
*
* @return integer
*/	
public function getDctoAplicado() 
{
    return $this->dctoAplicado;
}

/**
* Set dctoAplicado
*
* @param integer $dctoAplicado
*/
public function setDctoAplicado($dctoAplicado)
{
    $this->dctoAplicado = $dctoAplicado;
}
}


