<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoSnmpElemento
 *
 * @ORM\Table(name="INFO_SNMP_ELEMENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoSnmpElementoRepository")
 */
class InfoSnmpElemento
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_SNMP_ELEMENTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_SNMP_ELEMENTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiSnmp
*
* @ORM\ManyToOne(targetEntity="AdmiSnmp")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="SNMP_ID", referencedColumnName="ID_SNMP")
* })
*/
		
private $snmpId;

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
* Get snmpId
*
* @return telconet\schemaBundle\Entity\AdmiSnmp
*/		
     		
public function getSnmpId(){
	return $this->snmpId; 
}

/**
* Set snmpId
*
* @param telconet\schemaBundle\Entity\AdmiSnmp $snmpId
*/
public function setSnmpId(\telconet\schemaBundle\Entity\AdmiSnmp $snmpId)
{
        $this->snmpId = $snmpId;
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