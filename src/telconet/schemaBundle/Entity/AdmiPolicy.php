<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiPolicy
 *
 * @ORM\Table(name="ADMI_POLICY")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiPolicyRepository")
 */
class AdmiPolicy
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_POLICY", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_POLICY", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $nombrePolicy
*
* @ORM\Column(name="NOMBRE_POLICY", type="string", nullable=false)
*/		
     		
private $nombrePolicy;


/**
* @var string $leaseTime
*
* @ORM\Column(name="LEASE_TIME", type="string", nullable=false)
*/		


private $leaseTime;


/**
* @var string $mascara
*
* @ORM\Column(name="MASCARA", type="string", nullable=false)
*/	


private $mascara;

/**
* @var string $gateway
*
* @ORM\Column(name="GATEWAY", type="string", nullable=false)
*/	


private $gateway;


/**
* @var string $dnsName
*
* @ORM\Column(name="DNS_NAME", type="string", nullable=false)
*/	

private $dnsName;


/**
* @var string $dnsServers
*
* @ORM\Column(name="DNS_SERVERS", type="string", nullable=false)
*/	


private $dnsServers;


/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/	


     		
private $estado;

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
* @var integer $elementoId
*
*/		
     		
private $elementoId;


/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get id
*
* @return integer
*/		
     		
public function getElementoId(){
	return $this->elementoId; 
}


/**
* Get nombrePolicy
*
* @return nombrePolicy
*/		
     		
public function getNombrePolicy(){
	return $this->nombrePolicy; 
}

/**
* Set nombrePolicy
*
* @param string $nombrePolicy
*/
public function setNombrePolicy($nombrePolicy)
{
        $this->nombrePolicy = $nombrePolicy;
}



/**
* Get leaseTime
*
* @return leaseTime
*/		
     		
public function getLeaseTime(){
	return $this->leaseTime; 
}

/**
* Set leaseTime
*
* @param string $leaseTime
*/
public function setLeaseTime($leaseTime)
{
        $this->leaseTime = $leaseTime;
}

/**
* Get mascara
*
* @return mascara
*/		
     		
public function getMascara(){
	return $this->mascara; 
}

/**
* Set mascara
*
* @param string $mascara
*/
public function setMascara($mascara)
{
        $this->mascara = $mascara;
}


/**
* Get gateway
*
* @return gateway
*/		
     		
public function getGateway(){
	return $this->gateway; 
}

/**
* Set gateway
*
* @param string $gateway
*/
public function setGateway($gateway)
{
        $this->gateway = $gateway;
}

/**
* Get dnsName
*
* @return dnsName
*/		
     		
public function getDnsName(){
	return $this->dnsName; 
}

/**
* Set dnsName
*
* @param string $dnsName
*/
public function setDnsName($dnsName)
{
        $this->dnsName = $dnsName;
}

/**
* Get dnsServers
*
* @return dnsServers
*/		
     		
public function getDnsServers(){
	return $this->dnsServers; 
}

/**
* Set dnsServers
*
* @param string $dnsServers
*/
public function setDnsServers($dnsServers)
{
        $this->dnsServers = $dnsServers;
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


public function __toString()
{
        return $this->nombrePolicy;
}

}