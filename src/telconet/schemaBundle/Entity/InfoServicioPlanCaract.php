<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoServicioPlanCaract
 *
 * @ORM\Table(name="INFO_SERVICIO_PLAN_CARACT")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoServicioPlanCaractRepository")
 */
class InfoServicioPlanCaract
{


/**
* @var integer $servicioId
*
* @ORM\Column(name="SERVICIO_ID", type="integer", nullable=false)
*/		
     		
private $servicioId;

/**
* @var integer $planCaracteristicaId
*
* @ORM\Column(name="PLAN_CARACTERISTICA_ID", type="integer", nullable=false)
*/		
     		
private $planCaracteristicaId;

/**
* @var string $valor
*
* @ORM\Column(name="VALOR", type="string", nullable=true)
*/		
     		
private $valor;

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
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var integer $id
*
* @ORM\Column(name="ID_SERVICIO_PLAN_CARACT", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_SERVICIO_PLAN_CARACT", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* Get servicioId
*
* @return integer
*/		
     		
public function getServicioId(){
	return $this->servicioId; 
}

/**
* Set servicioId
*
* @param integer $servicioId
*/
public function setServicioId($servicioId)
{
        $this->servicioId = $servicioId;
}


/**
* Get planCaracteristicaId
*
* @return integer
*/		
     		
public function getPlanCaracteristicaId(){
	return $this->planCaracteristicaId; 
}

/**
* Set planCaracteristicaId
*
* @param integer $planCaracteristicaId
*/
public function setPlanCaracteristicaId($planCaracteristicaId)
{
        $this->planCaracteristicaId = $planCaracteristicaId;
}

/**
* Get valor
*
* @return string
*/		
     		
public function getValor(){
	return $this->valor; 
}

/**
* Set valor
*
* @param string $valor
*/
public function setValor($valor)
{
        $this->valor = $valor;
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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

public function __clone() {
    $this->id = null;
}

}
