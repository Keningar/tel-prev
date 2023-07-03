<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoOidElemento
 *
 * @ORM\Table(name="INFO_OID_ELEMENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoOidElementoRepository")
 */
class InfoOidElemento
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_OID_ELEMENTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_OID_ELEMENTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiOid
*
* @ORM\ManyToOne(targetEntity="AdmiOid")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="OID_ID", referencedColumnName="ID_OID")
* })
*/
		
private $oidId;

/**
* @var InfoElemento
*
* @ORM\ManyToOne(targetEntity="InfoElemento")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="ELEMENTO_ID", referencedColumnName="ID_ELEMENTO")
* })
*/
		
private $elementoId;

/**
* @var string $valor
*
* @ORM\Column(name="VALOR", type="string", nullable=false)
*/		
     		
private $valor;

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
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
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
* Get oidId
*
* @return telconet\schemaBundle\Entity\AdmiOid
*/		
     		
public function getOidId(){
	return $this->oidId; 
}

/**
* Set oidId
*
* @param telconet\schemaBundle\Entity\AdmiOid $oidId
*/
public function setOidId(\telconet\schemaBundle\Entity\AdmiOid $oidId)
{
        $this->oidId = $oidId;
}


/**
* Get elementoId
*
* @return telconet\schemaBundle\Entity\InfoElemento
*/		
     		
public function getElementoId(){
	return $this->elementoId; 
}

/**
* Set elementoId
*
* @param telconet\schemaBundle\Entity\InfoElemento $elementoId
*/
public function setElementoId(\telconet\schemaBundle\Entity\InfoElemento $elementoId)
{
        $this->elementoId = $elementoId;
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