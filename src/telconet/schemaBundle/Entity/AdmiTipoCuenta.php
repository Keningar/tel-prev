<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTipoCuenta
 *
 * @ORM\Table(name="ADMI_TIPO_CUENTA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTipoCuentaRepository")
 */
class AdmiTipoCuenta
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_TIPO_CUENTA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TIPO_CUENTA", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $descripcionCuenta
*
* @ORM\Column(name="DESCRIPCION_CUENTA", type="string", nullable=false)
*/		
     		
private $descripcionCuenta;

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
* @var string $esTarjeta
*
* @ORM\Column(name="ES_TARJETA", type="string", nullable=true)
*/		
     		
private $esTarjeta;

/**
* @var string $visibleFormato
*
* @ORM\Column(name="VISIBLE_FORMATO", type="string", nullable=true)
*/		
     		
private $visibleFormato;

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
* Get descripcionCuenta
*
* @return string
*/		
     		
public function getDescripcionCuenta(){
	return $this->descripcionCuenta; 
}

/**
* Set descripcionCuenta
*
* @param string $descripcionCuenta
*/
public function setDescripcionCuenta($descripcionCuenta)
{
        $this->descripcionCuenta = $descripcionCuenta;
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
* Get esTarjeta
*
* @return string
*/		
     		
public function getEsTarjeta(){
	return $this->esTarjeta; 
}

/**
* Set esTarjeta
*
* @param string $esTarjeta
*/
public function setEsTarjeta($esTarjeta)
{
        $this->esTarjeta = $esTarjeta;
}


public function __toString()
{
    return $this->descripcionCuenta;
}



/**
* Get visibleFormato
*
* @return string
*/		
     		
public function getVisibleFormato(){
	return $this->visibleFormato; 
}

/**
* Set visibleFormato
*
* @param string $visibleFormato
*/
public function setVisibleFormato($visibleFormato)
{
        $this->visibleFormato = $visibleFormato;
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


}
