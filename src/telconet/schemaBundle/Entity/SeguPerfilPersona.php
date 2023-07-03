<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\SeguPerfilPersona
 *
 * @ORM\Table(name="SEGU_PERFIL_PERSONA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\SeguPerfilPersonaRepository")
 */
class SeguPerfilPersona
{

/**
* @var integer $personaId
*
* @ORM\Id @ORM\Column(name="PERSONA_ID", type="integer", nullable=false)
*/		
     		
private $personaId;

/**
* @var integer $oficinaId
*
* @ORM\Column(name="OFICINA_ID", type="integer", nullable=false)
*/		
     		
private $oficinaId;

/**
* @var integer $empresaId
*
* @ORM\Column(name="EMPRESA_ID", type="integer", nullable=false)
*/		
     		
private $empresaId;

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
* @var SistPerfil
*
* @ORM\Id @ORM\ManyToOne(targetEntity="SistPerfil", inversedBy="perfil_persona")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PERFIL_ID", referencedColumnName="ID_PERFIL")
* })
*/
		
private $perfilId;

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
* @return integer
*/		
     		
public function getEmpresaId(){
	return $this->empresaId; 
}

/**
* Set empresaId
*
* @param integer $empresaId
*/
public function setEmpresaId($empresaId)
{
        $this->empresaId = $empresaId;
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


/**
* Get perfilId
*
* @return integer
*/		
     		
public function getPerfilId(){
	return $this->perfilId; 
}

/**
* Set perfilId
*
* @param telconet\schemaBundle\Entity\SistPerfil $perfilId
*/
public function setPerfilId(\telconet\schemaBundle\Entity\SistPerfil $perfilId)
{
        $this->perfilId = $perfilId;
}

}
