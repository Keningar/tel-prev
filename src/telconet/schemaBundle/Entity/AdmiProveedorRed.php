<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiProveedorRed
 *
 * @ORM\Table(name="ADMI_PROVEEDOR_RED")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiProveedorRedRepository")
 */
class AdmiProveedorRed
{


/**
* @var string $descripcionProveedorRed
*
* @ORM\Column(name="DESCRIPCION_PROVEEDOR_RED", type="string", nullable=true)
*/		
     		
private $descripcionProveedorRed;

/**
* @var string $tipoRed
*
* @ORM\Column(name="TIPO_RED", type="string", nullable=false)
*/		
     		
private $tipoRed;

/**
* @var string $routeMapIpv4
*
* @ORM\Column(name="ROUTE_MAP_IPV4", type="string", nullable=true)
*/		
     		
private $routeMapIpv4;

/**
* @var string $routeMapIpv6
*
* @ORM\Column(name="ROUTE_MAP_IPV6", type="string", nullable=true)
*/		
     		
private $routeMapIpv6;

/**
* @var string $ipNeighborIpv4
*
* @ORM\Column(name="IP_NEIGHBOR_IPV4", type="string", nullable=true)
*/		
     		
private $ipNeighborIpv4;

/**
* @var string $ipNeighborIpv6
*
* @ORM\Column(name="IP_NEIGHBOR_IPV6", type="string", nullable=true)
*/		
     		
private $ipNeighborIpv6;

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
* @ORM\Column(name="ID_PROVEEDOR", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_PROVEEDOR_RED", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $nombreProveedorRed
*
* @ORM\Column(name="NOMBRE_PROVEEDOR_RED", type="string", nullable=false)
*/		
     		
private $nombreProveedorRed;

/**
* Get descripcionProveedorRed
*
* @return string
*/		
     		
public function getDescripcionProveedorRed(){
	return $this->descripcionProveedorRed; 
}

/**
* Set descripcionProveedorRed
*
* @param string $descripcionProveedorRed
*/
public function setDescripcionProveedorRed($descripcionProveedorRed)
{
        $this->descripcionProveedorRed = $descripcionProveedorRed;
}


/**
* Get tipoRed
*
* @return string
*/		
     		
public function getTipoRed(){
	return $this->tipoRed; 
}

/**
* Set tipoRed
*
* @param string $tipoRed
*/
public function setTipoRed($tipoRed)
{
        $this->tipoRed = $tipoRed;
}


/**
* Get routeMapIpv4
*
* @return string
*/		
     		
public function getRouteMapIpv4(){
	return $this->routeMapIpv4; 
}

/**
* Set routeMapIpv4
*
* @param string $routeMapIpv4
*/
public function setRouteMapIpv4($routeMapIpv4)
{
        $this->routeMapIpv4 = $routeMapIpv4;
}


/**
* Get routeMapIpv6
*
* @return string
*/		
     		
public function getRouteMapIpv6(){
	return $this->routeMapIpv6; 
}

/**
* Set routeMapIpv6
*
* @param string $routeMapIpv6
*/
public function setRouteMapIpv6($routeMapIpv6)
{
        $this->routeMapIpv6 = $routeMapIpv6;
}


/**
* Get ipNeighborIpv4
*
* @return string
*/		
     		
public function getIpNeighborIpv4(){
	return $this->ipNeighborIpv4; 
}

/**
* Set ipNeighborIpv4
*
* @param string $ipNeighborIpv4
*/
public function setIpNeighborIpv4($ipNeighborIpv4)
{
        $this->ipNeighborIpv4 = $ipNeighborIpv4;
}


/**
* Get ipNeighborIpv6
*
* @return string
*/		
     		
public function getIpNeighborIpv6(){
	return $this->ipNeighborIpv6; 
}

/**
* Set ipNeighborIpv6
*
* @param string $ipNeighborIpv6
*/
public function setIpNeighborIpv6($ipNeighborIpv6)
{
        $this->ipNeighborIpv6 = $ipNeighborIpv6;
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
* Get nombreProveedorRed
*
* @return string
*/		
     		
public function getNombreProveedorRed(){
	return $this->nombreProveedorRed; 
}

/**
* Set nombreProveedorRed
*
* @param string $nombreProveedorRed
*/
public function setNombreProveedorRed($nombreProveedorRed)
{
        $this->nombreProveedorRed = $nombreProveedorRed;
}

public function __toString()
{
    return $this->nombreProveedorRed;
}

}