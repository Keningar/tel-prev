<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiPeriodoCab
 *
 * @ORM\Table(name="ADMI_PERIODO_CAB")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiPeriodoCabRepository")
 */
class AdmiPeriodoCab
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PERIODO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_PERIODO_CAB", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var DATE $feInicio
*
* @ORM\Column(name="FE_INICIO", type="DATE", nullable=true)
*/		
     		
private $feInicio;

/**
* @var DATE $feFin
*
* @ORM\Column(name="FE_FIN", type="DATE", nullable=true)
*/		
     		
private $feFin;

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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get feInicio
*
* @return 
*/		
     		
public function getFeInicio(){
	return $this->feInicio; 
}

/**
* Set feInicio
*
* @param  $feInicio
*/
public function setFeInicio($feInicio)
{
        $this->feInicio = $feInicio;
}


/**
* Get feFin
*
* @return 
*/		
     		
public function getFeFin(){
	return $this->feFin; 
}

/**
* Set feFin
*
* @param  $feFin
*/
public function setFeFin($feFin)
{
        $this->feFin = $feFin;
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

}