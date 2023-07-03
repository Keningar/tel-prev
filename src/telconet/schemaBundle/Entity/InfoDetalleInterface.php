<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDetalleInterface
 *
 * @ORM\Table(name="INFO_DETALLE_INTERFACE")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDetalleInterfaceRepository")
 */
class InfoDetalleInterface
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_DETALLE_INTERFACE", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DETALLE_INTERFACE", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var InfoInterfaceElemento
*
* @ORM\ManyToOne(targetEntity="InfoInterfaceElemento")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="INTERFACE_ELEMENTO_ID", referencedColumnName="ID_INTERFACE_ELEMENTO")
* })
*/
		
private $interfaceElementoId;

/**
* @var string $detalleNombre
*
* @ORM\Column(name="DETALLE_NOMBRE", type="string", nullable=true)
*/		
     		
private $detalleNombre;

/**
* @var string $detalleValor
*
* @ORM\Column(name="DETALLE_VALOR", type="string", nullable=true)
*/		
     		
private $detalleValor;

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
* Get interfaceElementoId
*
* @return telconet\schemaBundle\Entity\InfoInterfaceElemento
*/		
     		
public function getInterfaceElementoId(){
	return $this->interfaceElementoId; 
}

/**
* Set interfaceElementoId
*
* @param telconet\schemaBundle\Entity\InfoInterfaceElemento $interfaceElementoId
*/
public function setInterfaceElementoId(\telconet\schemaBundle\Entity\InfoInterfaceElemento $interfaceElementoId)
{
        $this->interfaceElementoId = $interfaceElementoId;
}


/**
* Get detalleNombre
*
* @return string
*/		
     		
public function getDetalleNombre(){
	return $this->detalleNombre; 
}

/**
* Set detalleNombre
*
* @param string $detalleNombre
*/
public function setDetalleNombre($detalleNombre)
{
        $this->detalleNombre = $detalleNombre;
}


/**
* Get detalleValor
*
* @return string
*/		
     		
public function getDetalleValor(){
	return $this->detalleValor; 
}

/**
* Set detalleValor
*
* @param string $detalleValor
*/
public function setDetalleValor($detalleValor)
{
        $this->detalleValor = $detalleValor;
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