<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoEstadoInstalacion
 *
 * @ORM\Table(name="INFO_ESTADO_INSTALACION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoEstadoInstalacionRepository")
 */
class InfoEstadoInstalacion
{

    /**
* @var integer $id
*
* @ORM\Column(name="ID_ESTADO_INSTALACION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_ESTADO_INSTALACION", allocationSize=1, initialValue=1)
*/		
		
private $id;

/**
* @var integer $servicioId
*
* @ORM\Column(name="SERVICIO_ID", type="integer", nullable=false)
*/		
     		
private $servicioId;


/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

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
* @var datetime $feUltMod
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;

/**
* @var datetime $ipUltMod
*
* @ORM\Column(name="IP_ULT_MOD", type="string", nullable=true)
*/		
     		
private $ipUltMod;

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
* Get ipUltMod
*
* @return string
*/		
     		
public function getIpUltMod(){
	return $this->ipUltMod; 
}

/**
* Set ipUltMod
*
* @param string $ipUltMod
*/
public function setIpUltMod($ipUltMod)
{
    $this->ipUltMod = $ipUltMod;
}

/**
* Get id
*
* @return string
*/	
function getId()
{
    return $this->id;
}

/**
* Get sercicioId
*
* @return string
*/	
function getServicioId()
{
    return $this->servicioId;
}

/**
* Get estado
*
* @return string
*/	
function getEstado()
{
    return $this->estado;
}

/**
* Set id
*
* @param integer $id
*/
function setId($id)
{
    $this->id = $id;
}

/**
* Set servicioId
*
* @param integer $servicioId
*/
function setServicioId($servicioId)
{
    $this->servicioId = $servicioId;
}

/**
* Set estado
*
* @param string $estado
*/
function setEstado($estado)
{
    $this->estado = $estado;
}



}

