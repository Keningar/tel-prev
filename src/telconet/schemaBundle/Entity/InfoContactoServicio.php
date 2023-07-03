<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoContactoServicio
 *
 * @ORM\Table(name="INFO_CONTACTO_SERVICIO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoContactoServicioRepository")
 */
class InfoContactoServicio
{


/**
* @var InfoPersonaContacto
*
* @ORM\ManyToOne(targetEntity="InfoPersonaContacto")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PERSONA_CONTACTO_ID", referencedColumnName="ID_PERSONA_CONTACTO")
* })
*/
		
private $personaContactoId;

/**
* @var InfoServicio
*
* @ORM\ManyToOne(targetEntity="InfoServicio")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="SERVICIO_ID", referencedColumnName="ID_SERVICIO")
* })
*/
		
private $servicioId;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
*/		
     		
private $feCreacion;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

/**
* @var integer $id
*
* @ORM\Column(name="ID_CONTACTO_SERVICIO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_CONTACTO_SERVICIO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* Get personaContactoId
*
* @return telconet\schemaBundle\Entity\InfoPersonaContacto
*/		
     		
public function getPersonaContactoId(){
	return $this->personaContactoId; 
}

/**
* Set personaContactoId
*
* @param telconet\schemaBundle\Entity\InfoPersonaContacto $personaContactoId
*/
public function setPersonaContactoId(\telconet\schemaBundle\Entity\InfoPersonaContacto $personaContactoId)
{
        $this->personaContactoId = $personaContactoId;
}


/**
* Get servicioId
*
* @return telconet\schemaBundle\Entity\InfoServicio
*/		
     		
public function getServicioId(){
	return $this->servicioId; 
}

/**
* Set servicioId
*
* @param telconet\schemaBundle\Entity\InfoServicio $servicioId
*/
public function setServicioId(\telconet\schemaBundle\Entity\InfoServicio $servicioId)
{
        $this->servicioId = $servicioId;
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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}
}