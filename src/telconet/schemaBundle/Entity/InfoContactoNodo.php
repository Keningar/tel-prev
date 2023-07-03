<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoContactoNodo
 *
 * @ORM\Table(name="INFO_CONTACTO_NODO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoContactoNodoRepository")
 */
class InfoContactoNodo
{



/**
* @var integer $id
*
* @ORM\Column(name="ID_NODO_PERSONA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_CONTACTO_NODO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var InfoElemento
*
* @ORM\ManyToOne(targetEntity="InfoElemento")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="NODO_ID", referencedColumnName="ID_ELEMENTO")
* })
*/
     		
private $nodoId;


/**
* @var integer $personaId
*
* @ORM\Column(name="PERSONA_ID", type="integer", nullable=false)
*/	
		
private $personaId;



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
* Get personaId
*
* @return integer $personaId
*/		
     		
public function getPersonaId(){
	return $this->personaId; 
}

/**
* Set personaId
*
* @param integer $personaId
*/
public function setPersonaId($personaId)
{
        $this->personaId = $personaId;
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
* Get nodoId
*
* @return telconet\schemaBundle\Entity\InfoElemento
*/		
     		
public function getNodoId(){
	return $this->nodoId; 
}

/**
* Set nodoId
*
* @param telconet\schemaBundle\Entity\InfoElemento $nodoId
*/
public function setNodoId(\telconet\schemaBundle\Entity\InfoElemento $nodoId)
{
        $this->nodoId = $nodoId;
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