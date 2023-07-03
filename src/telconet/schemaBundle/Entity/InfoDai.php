<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDai
 *
 * @ORM\Table(name="INFO_DAI")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDaiRepository")
 */
class InfoDai
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_DAI", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DAI", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $cantonJurisdiccionId
*
* @ORM\Column(name="CANTON_JURISDICCION_ID", type="integer", nullable=false)
*/		
     		
private $cantonJurisdiccionId;

/**
* @var integer $cantonId
*
* @ORM\Column(name="CANTON_ID", type="integer", nullable=false)
*/		
     		
private $cantonId;

/**
* @var integer $jurisdiccionId
*
* @ORM\Column(name="JURISDICCION_ID", type="integer", nullable=false)
*/		
     		
private $jurisdiccionId;

/**
* @var integer $oficinaId
*
* @ORM\Column(name="OFICINA_ID", type="integer", nullable=false)
*/		
     		
private $oficinaId;

/**
* @var string $empresaId
*
* @ORM\Column(name="EMPRESA_ID", type="string", nullable=false)
*/		
     		
private $empresaId;

/**
* @var string $tipo
*
* @ORM\Column(name="TIPO", type="string", nullable=false)
*/		
     		
private $tipo;

/**
* @var string $ip
*
* @ORM\Column(name="IP", type="string", nullable=false)
*/		
     		
private $ip;

/**
* @var string $mac
*
* @ORM\Column(name="MAC", type="string", nullable=false)
*/		
     		
private $mac;

/**
* @var integer $orden
*
* @ORM\Column(name="ORDEN", type="integer", nullable=false)
*/		
     		
private $orden;

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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get cantonJurisdiccionId
*
* @return integer
*/		
     		
public function getCantonJurisdiccionId(){
	return $this->cantonJurisdiccionId; 
}

/**
* Set cantonJurisdiccionId
*
* @param integer $cantonJurisdiccionId
*/
public function setCantonJurisdiccionId($cantonJurisdiccionId)
{
        $this->cantonJurisdiccionId = $cantonJurisdiccionId;
}


/**
* Get cantonId
*
* @return integer
*/		
     		
public function getCantonId(){
	return $this->cantonId; 
}

/**
* Set cantonId
*
* @param integer $cantonId
*/
public function setCantonId($cantonId)
{
        $this->cantonId = $cantonId;
}


/**
* Get jurisdiccionId
*
* @return integer
*/		
     		
public function getJurisdiccionId(){
	return $this->jurisdiccionId; 
}

/**
* Set jurisdiccionId
*
* @param integer $jurisdiccionId
*/
public function setJurisdiccionId($jurisdiccionId)
{
        $this->jurisdiccionId = $jurisdiccionId;
}


/**
* Get oficinaId
*
* @return integer
*/		
     		
public function getOficinaId(){
	return $this->oficinaId; 
}

/**
* Set oficinaId
*
* @param integer $oficinaId
*/
public function setOficinaId($oficinaId)
{
        $this->oficinaId = $oficinaId;
}


/**
* Get empresaId
*
* @return string
*/		
     		
public function getEmpresaId(){
	return $this->empresaId; 
}

/**
* Set empresaId
*
* @param string $empresaId
*/
public function setEmpresaId($empresaId)
{
        $this->empresaId = $empresaId;
}


/**
* Get tipo
*
* @return string
*/		
     		
public function getTipo(){
	return $this->tipo; 
}

/**
* Set tipo
*
* @param string $tipo
*/
public function setTipo($tipo)
{
        $this->tipo = $tipo;
}


/**
* Get ip
*
* @return string
*/		
     		
public function getIp(){
	return $this->ip; 
}

/**
* Set ip
*
* @param string $ip
*/
public function setIp($ip)
{
        $this->ip = $ip;
}


/**
* Get mac
*
* @return string
*/		
     		
public function getMac(){
	return $this->mac; 
}

/**
* Set mac
*
* @param string $mac
*/
public function setMac($mac)
{
        $this->mac = $mac;
}


/**
* Get orden
*
* @return integer
*/		
     		
public function getOrden(){
	return $this->orden; 
}

/**
* Set orden
*
* @param integer $orden
*/
public function setOrden($orden)
{
        $this->orden = $orden;
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

}