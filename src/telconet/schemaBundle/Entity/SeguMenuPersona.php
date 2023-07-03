<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\SeguMenuPersona
 *
 * @ORM\Table(name="SEGU_MENU_PERSONA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\SeguMenuPersonaRepository")
 */
class SeguMenuPersona
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_MENU_PERSONA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_SEGU_MENU_PERSONA", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $personaId
*
* @ORM\Column(name="PERSONA_ID", type="integer", nullable=false)
*/		
     		
private $personaId;

/**
* @var string $menuHtml
*
* @ORM\Column(name="MENU_HTML", type="string", nullable=false)
*/		
     		
private $menuHtml;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;

/**
* @var string $feCreacion
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
* Get personaId
*
* @return integer
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
* Get menuHtml
*
* @return 
*/		
     		
public function getMenuHtml(){
	return $this->menuHtml; 
}

/**
* Set menuHtml
*
* @param  $menuHtml
*/
public function setMenuHtml($menuHtml)
{
        $this->menuHtml = $menuHtml;
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