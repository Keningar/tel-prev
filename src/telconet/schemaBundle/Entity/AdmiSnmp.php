<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiSnmp
 *
 * @ORM\Table(name="ADMI_SNMP")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiSnmpRepository")
 */
class AdmiSnmp
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_SNMP", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_SNMP", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $snmpCommunity
*
* @ORM\Column(name="SNMP_COMMUNITY", type="string", nullable=true)
*/		
     		
private $snmpCommunity;

/**
* @var string $snmpVersion
*
* @ORM\Column(name="SNMP_VERSION", type="string", nullable=true)
*/		
     		
private $snmpVersion;

/**
* @var string $descripcionSnmp
*
* @ORM\Column(name="DESCRIPCION_SNMP", type="string", nullable=true)
*/		
     		
private $descripcionSnmp;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=true)
*/		
     		
private $usrCreacion;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get snmpCommunity
*
* @return string
*/		
     		
public function getSnmpCommunity(){
	return $this->snmpCommunity; 
}

/**
* Set snmpCommunity
*
* @param string $snmpCommunity
*/
public function setSnmpCommunity($snmpCommunity)
{
        $this->snmpCommunity = $snmpCommunity;
}


/**
* Get snmpVersion
*
* @return string
*/		
     		
public function getSnmpVersion(){
	return $this->snmpVersion; 
}

/**
* Set snmpVersion
*
* @param string $snmpVersion
*/
public function setSnmpVersion($snmpVersion)
{
        $this->snmpVersion = $snmpVersion;
}


/**
* Get descripcionSnmp
*
* @return string
*/		
     		
public function getDescripcionSnmp(){
	return $this->descripcionSnmp; 
}

/**
* Set descripcionSnmp
*
* @param string $descripcionSnmp
*/
public function setDescripcionSnmp($descripcionSnmp)
{
        $this->descripcionSnmp = $descripcionSnmp;
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

public function __toString()
{
    return $this->snmpCommunity;
}

}