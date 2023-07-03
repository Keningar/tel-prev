<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoCuadrillaTarea
 *
 * @ORM\Table(name="INFO_CUADRILLA_TAREA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoCuadrillaTareaRepository")
 */
class InfoCuadrillaTarea
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_CUADRILLA_TAREA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_CUADRILLA_TAREA", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	


/**
* @var InfoDetalle
*
* @ORM\ManyToOne(targetEntity="InfoDetalle")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="DETALLE_ID", referencedColumnName="ID_DETALLE")
* })
*/	
     		
private $detalleId;

/**
* @var string $cuadrillaId
*
* @ORM\Column(name="CUADRILLA_ID", type="integer", nullable=true)
*/		
     		
private $cuadrillaId;

/**
* @var string $personaId
*
* @ORM\Column(name="PERSONA_ID", type="integer", nullable=true)
*/		
     		
private $personaId;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=true)
*/		
     		
private $usrCreacion;


/**
* @var date $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="date", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;


		 



/**
* Set Id
*
* @param integer $id
*/
public function setId($id)
{
    $this->id = $id;
}

/**
* Get Id
*
* @return integer 
*/
public function getId()
{
    return $this->Id;
}
  
/**
* Set DetalleId
*
* @param integer $detalleId
*/
public function setDetalleId($detalleId)
{
    $this->detalleId = $detalleId;
}

/**
* Get DetalleId
*
* @return integer
*/
public function getDetalleId()
{
    return $this->detalleId;
}


/**
* Set CuadrillaId
*
* @param integer $cuadrillaId
*/
public function setCuadrillaId($cuadrillaId)
{
    $this->cuadrillaId = $cuadrillaId;
}

/**
* Get CuadrillaId
*
* @return integer
*/
public function getCuadrillaId()
{
    return $this->cuadrillaId;
}


/**
* Set PersonaId
*
* @param integer $personaId
*/
public function setPersonaId($personaId)
{
    $this->personaId = $personaId;
}

/**
* Get PersonaId
*
* @return integer
*/
public function getPersonaId()
{
    return $this->personaId;
}


/**
* Set UsrCreacion
*
* @param string $usrCreacion
*/
public function setUsrCreacion($usrCreacion)
{
    $this->usrCreacion = $usrCreacion;
}

/**
* Get UsrCreacion
*
* @return string
*/
public function getUsrCreacion()
{
    return $this->usrCreacion;
}


/**
* Set FeCreacion
*
* @param date $feCreacion
*/
public function setFeCreacion($feCreacion)
{
    $this->feCreacion = $feCreacion;
}

/**
* Get FeCreacion
*
* @return date
*/
public function getFeCreacion()
{
    return $this->feCreacion;
}

/**
* Set IpCreacion
*
* @param string $ipCreacion
*/
public function setIpCreacion($ipCreacion)
{
    $this->ipCreacion = $ipCreacion;
}


/**
* Get IpCreacion
*
* @return string
*/
public function getIpCreacion()
{
    return $this->ipCreacion;
}


    
}