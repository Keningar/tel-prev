<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiBanco
 *
 * @ORM\Table(name="ADMI_BANCO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiBancoRepository")
 */
class AdmiBanco
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_BANCO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_BANCO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $descripcionBanco
*
* @ORM\Column(name="DESCRIPCION_BANCO", type="string", nullable=false)
*/		
     		
private $descripcionBanco;

/**
* @var string $requiereNumeroDebito
*
* @ORM\Column(name="REQUIERE_NUMERO_DEBITO", type="string", nullable=false)
*/		
     		
private $requiereNumeroDebito;

/**
* @var string $generaDebitoBancario
*
* @ORM\Column(name="GENERA_DEBITO_BANCARIO", type="string", nullable=false)
*/		
     		
private $generaDebitoBancario;

/**
* @var string $numeroCuentaContable
*
* @ORM\Column(name="NUMERO_CUENTA_CONTABLE", type="string", nullable=true)
*/		
     		
private $numeroCuentaContable;

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
* @var string $noCta
*
* @ORM\Column(name="NO_CTA", type="string", nullable=true)
*/		
     		
private $noCta;

/**
* @var string $ctaContable
*
* @ORM\Column(name="CTA_CONTABLE", type="string", nullable=true)
*/		
     		
private $ctaContable;

/**
* @var integer $paisId
*
* @ORM\Column(name="PAIS_ID", type="integer", nullable=true)
*/		
     		
private $paisId;


/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get descripcionBanco
*
* @return string
*/		
     		
public function getDescripcionBanco(){
	return $this->descripcionBanco; 
}

/**
* Set descripcionBanco
*
* @param string $descripcionBanco
*/
public function setDescripcionBanco($descripcionBanco)
{
        $this->descripcionBanco = $descripcionBanco;
}


/**
* Get requiereNumeroDebito
*
* @return string
*/		
     		
public function getRequiereNumeroDebito(){
	return $this->requiereNumeroDebito; 
}

/**
* Set requiereNumeroDebito
*
* @param string $requiereNumeroDebito
*/
public function setRequiereNumeroDebito($requiereNumeroDebito)
{
        $this->requiereNumeroDebito = $requiereNumeroDebito;
}


/**
* Get generaDebitoBancario
*
* @return string
*/		
     		
public function getGeneraDebitoBancario(){
	return $this->generaDebitoBancario; 
}

/**
* Set generaDebitoBancario
*
* @param string $generaDebitoBancario
*/
public function setGeneraDebitoBancario($generaDebitoBancario)
{
        $this->generaDebitoBancario = $generaDebitoBancario;
}


/**
* Get numeroCuentaContable
*
* @return string
*/		
     		
public function getNumeroCuentaContable(){
	return $this->numeroCuentaContable; 
}

/**
* Set numeroCuentaContable
*
* @param string $numeroCuentaContable
*/
public function setNumeroCuentaContable($numeroCuentaContable)
{
        $this->numeroCuentaContable = $numeroCuentaContable;
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

public function __toString()
{
    return $this->descripcionBanco;
}

/**
* Get noCta
*
* @return string
*/		
     		
public function getNoCta(){
	return $this->noCta; 
}

/**
* Set noCta
*
* @param string $noCta
*/
public function setNoCta($noCta)
{
        $this->noCta = $noCta;
}

/**
* Get paisId
*
* @return integer
*/		
     		
public function getPaisId(){
	return $this->paisId; 
}

/**
* Set paisId
*
* @param integer $paisId
*/
public function setPaisId($paisId)
{
        $this->paisId = $paisId;
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
	
}
