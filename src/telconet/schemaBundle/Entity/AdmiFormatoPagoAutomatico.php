<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiFormatoPagoAutomatico
 *
 * @ORM\Table(name="ADMI_FORMATO_PAGO_AUTOMATICO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiFormatoPagoAutomaticoRepository")
 */
class AdmiFormatoPagoAutomatico
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_FORMATO_PAGO_AUTOMATICO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_FORMATO_PAGO_AUT", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $bancoTipoCuentaId
*
* @ORM\Column(name="BANCO_TIPO_CUENTA_ID", type="integer", nullable=true)
*/
		
private $bancoTipoCuentaId;

/**
* @var integer $cuentaContableId
*
* @ORM\Column(name="CUENTA_CONTABLE_ID", type="integer", nullable=true)
*/
		
private $cuentaContableId;

/**
* @var string $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=true)
*/		
     		
private $empresaCod;

/**
* @var integer $filaInicia
*
* @ORM\Column(name="FILA_INICIA", type="integer", nullable=true)
*/		
     		
private $filaInicia;

/**
* @var integer $cantidadFilas
*
* @ORM\Column(name="CANTIDAD_FILAS", type="integer", nullable=true)
*/		
     		
private $cantidadFilas;


/**
* @var string $colFecha
*
* @ORM\Column(name="COL_FECHA", type="string", nullable=true)
*/		
     		
private $colFecha;

/**
* @var string $colConcepto
*
* @ORM\Column(name="COL_CONCEPTO", type="string", nullable=true)
*/		
     		
private $colConcepto;

/**
* @var string $colTipo
*
* @ORM\Column(name="COL_TIPO", type="string", nullable=true)
*/		
     		
private $colTipo;

/**
* @var string $colReferencia
*
* @ORM\Column(name="COL_REFERENCIA", type="string", nullable=true)
*/		
     		
private $colReferencia;

/**
* @var string $colOficina
*
* @ORM\Column(name="COL_OFICINA", type="string", nullable=true)
*/		
     		
private $colOficina;

/**
* @var string $colMonto
*
* @ORM\Column(name="COL_MONTO", type="string", nullable=true)
*/		
     		
private $colMonto;


/**
* @var string $hoja
*
* @ORM\Column(name="HOJA", type="string", nullable=true)
*/		
     		
private $hoja;


/**
* @var string $tipoArchivo
*
* @ORM\Column(name="TIPO_ARCHIVO", type="string", nullable=true)
*/		
     		
private $tipoArchivo;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

/**
* @var string $colValidaTipo
*
* @ORM\Column(name="COL_VALIDA_TIPO", type="string", nullable=true)
*/		
     		
private $colValidaTipo;

/**
* @var string $colValidaRef
*
* @ORM\Column(name="COL_VALIDA_REF", type="string", nullable=true)
*/		
     		
private $colValidaRef;

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
* @var string $formatoFecha
*
* @ORM\Column(name="FORMATO_FECHA", type="string", nullable=true)
*/		
     		
private $formatoFecha;

public function getEmpresaCod() {
    return $this->empresaCod;
}

public function setEmpresaCod($empresaCod) {
    $this->empresaCod = $empresaCod;
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
* Get cuentaContableId
*
* @return integer
*/		
     		
public function getCuentaContableId(){
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
* Get filaInicia
*
* @return integer
*/		
     		
public function getFilaInicia(){
	return $this->filaInicia; 
}

/**
* Set filaInicia
*
* @param integer $filaInicia
*/
public function setFilaInicia($filaInicia)
{
        $this->filaInicia = $filaInicia;
}

/**
* Get colFecha
*
* @return string
*/		
     		
public function getColFecha(){
	return $this->colFecha; 
}

/**
* Get colConcepto
*
* @return string
*/		
     		
public function getColConcepto(){
	return $this->colConcepto; 
}

/**
* Get colTipo
*
* @return string
*/		
     		
public function getColTipo(){
	return $this->colTipo; 
}

/**
* Get colValidaTipo
*
* @return string
*/		
     		
public function getColValidaTipo(){
	return $this->colValidaTipo; 
}

/**
* Get colValidaRef
*
* @return string
*/		
     		
public function getColValidaRef(){
	return $this->colValidaRef; 
}

/**
* Get colReferencia
*
* @return string
*/		
     		
public function getColReferencia(){
	return $this->colReferencia; 
}


/**
* Get colMonto
*
* @return string
*/		
     		
public function getColMonto(){
	return $this->colMonto; 
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
* Get formatoFecha
*
* @return string
*/		
     		
public function getFormatoFecha(){
	return $this->formatoFecha; 
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
* Set formatoFecha
*
* @param integer $formatoFecha
*/
public function setFormatoFecha($formatoFecha)
{
        $this->formatoFecha = $formatoFecha;
}
}
