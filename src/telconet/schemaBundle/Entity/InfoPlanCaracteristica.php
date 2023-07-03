<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPlanCaracteristica
 *
 * @ORM\Table(name="INFO_PLAN_CARACTERISTICA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPlanCaracteristicaRepository")
 */
class InfoPlanCaracteristica
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PLAN_CARACTERISITCA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PLAN_CARACTERISTICA", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var InfoPlanCab
*
* @ORM\ManyToOne(targetEntity="InfoPlanCab")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PLAN_ID", referencedColumnName="ID_PLAN")
* })
*/
		
private $planId;

/**
* @var AdmiCaracteristica
*
* @ORM\ManyToOne(targetEntity="AdmiCaracteristica")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="CARACTERISTICA_ID", referencedColumnName="ID_CARACTERISTICA")
* })
*/
		
private $caracteristicaId;

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
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

/**
* @var string $valor
*
* @ORM\Column(name="VALOR", type="string", nullable=true)
*/		
     		
private $valor;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get planId
*
* @return telconet\schemaBundle\Entity\InfoPlanCab
*/		
     		
public function getPlanId(){
	return $this->planId; 
}

/**
* Set planId
*
* @param telconet\schemaBundle\Entity\InfoPlanCab $planId
*/
public function setPlanId(\telconet\schemaBundle\Entity\InfoPlanCab $planId)
{
        $this->planId = $planId;
}


/**
* Get caracteristicaId
*
* @return telconet\schemaBundle\Entity\AdmiCaracteristica
*/		
     		
public function getCaracteristicaId(){
	return $this->caracteristicaId; 
}

/**
* Set caracteristicaId
*
* @param telconet\schemaBundle\Entity\AdmiCaracteristica $caracteristicaId
*/
public function setCaracteristicaId(\telconet\schemaBundle\Entity\AdmiCaracteristica $caracteristicaId)
{
        $this->caracteristicaId = $caracteristicaId;
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

}