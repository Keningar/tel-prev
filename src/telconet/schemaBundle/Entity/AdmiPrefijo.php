<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiPrefijo
 *
 * @ORM\Table(name="ADMI_PREFIJO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiPrefijoRepository")
 */
class AdmiPrefijo
{


/**
* @var string $descripcionPrefijo
*
* @ORM\Column(name="DESCRIPCION_PREFIJO", type="string", nullable=true)
*/		
     		
private $descripcionPrefijo;

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
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=false)
*/		
     		
private $usrUltMod;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
*/		
     		
private $feUltMod;

/**
* @var integer $id
*
* @ORM\Column(name="ID_PREFIJO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_PREFIJO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $proveedorRedId
*
* @ORM\Column(name="PROVEEDOR_RED_ID", type="integer", nullable=false)
*/		
     		
private $proveedorRedId;

/**
* @var string $cliente
*
* @ORM\Column(name="CLIENTE", type="string", nullable=true)
*/		
     		
private $cliente;

/**
* @var string $nombreIpv4
*
* @ORM\Column(name="NOMBRE_IPV4", type="string", nullable=true)
*/		
     		
private $nombreIpv4;

/**
* @var string $nombreIpv6
*
* @ORM\Column(name="NOMBRE_IPV6", type="string", nullable=true)
*/		
     		
private $nombreIpv6;

/**
* Get descripcionPrefijo
*
* @return string
*/		
     		
public function getDescripcionPrefijo(){
	return $this->descripcionPrefijo; 
}

/**
* Set descripcionPrefijo
*
* @param string $descripcionPrefijo
*/
public function setDescripcionPrefijo($descripcionPrefijo)
{
        $this->descripcionPrefijo = $descripcionPrefijo;
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


/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get proveedorRedId
*
* @return integer
*/		
     		
public function getProveedorRedId(){
	return $this->proveedorRedId; 
}

/**
* Set proveedorRedId
*
* @param integer $proveedorRedId
*/
public function setProveedorRedId($proveedorRedId)
{
        $this->proveedorRedId = $proveedorRedId;
}


/**
* Get cliente
*
* @return string
*/		
     		
public function getCliente(){
	return $this->cliente; 
}

/**
* Set cliente
*
* @param string $cliente
*/
public function setCliente($cliente)
{
        $this->cliente = $cliente;
}


/**
* Get nombreIpv4
*
* @return string
*/		
     		
public function getNombreIpv4(){
	return $this->nombreIpv4; 
}

/**
* Set nombreIpv4
*
* @param string $nombreIpv4
*/
public function setNombreIpv4($nombreIpv4)
{
        $this->nombreIpv4 = $nombreIpv4;
}


/**
* Get nombreIpv6
*
* @return string
*/		
     		
public function getNombreIpv6(){
	return $this->nombreIpv6; 
}

/**
* Set nombreIpv6
*
* @param string $nombreIpv6
*/
public function setNombreIpv6($nombreIpv6)
{
        $this->nombreIpv6 = $nombreIpv6;
}

public function __toString()
{
    return $this->nombreIpv4;
}

}