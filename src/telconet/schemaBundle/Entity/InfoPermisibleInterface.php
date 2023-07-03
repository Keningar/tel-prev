<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPermisibleInterface
 *
 * @ORM\Table(name="INFO_PERMISIBLE_INTERFACE")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPermisibleInterfaceRepository")
 */
class InfoPermisibleInterface
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PERMISIBLE_INTERFACE", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PERMISIBLE_INTERFACE", allocationSize=1, initialValue=1)
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
* @var string $permisible
*
* @ORM\Column(name="PERMISIBLE", type="string", nullable=true)
*/		
     		
private $permisible;

/**
* @var string $tipo
*
* @ORM\Column(name="TIPO", type="string", nullable=true)
*/		
     		
private $tipo;

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
* Get permisible
*
* @return string
*/		
     		
public function getPermisible(){
	return $this->permisible; 
}

/**
* Set permisible
*
* @param string $permisible
*/
public function setPermisible($permisible)
{
        $this->permisible = $permisible;
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