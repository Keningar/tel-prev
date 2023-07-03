<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\SeguAsignacion
 *
 * @ORM\Table(name="SEGU_ASIGNACION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\SeguAsignacionRepository")
 */
class SeguAsignacion
{


/**
* @var SistPerfil
*
* @ORM\Id @ORM\ManyToOne(targetEntity="SistPerfil")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PERFIL_ID", referencedColumnName="ID_PERFIL")
* })
*/
		
private $perfilId;

/**
* @var SeguRelacionSistema
*
* @ORM\Id @ORM\ManyToOne(targetEntity="SeguRelacionSistema", inversedBy="asignacion_relacion")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="RELACION_SISTEMA_ID", referencedColumnName="ID_RELACION_SISTEMA")
* })
*/
		
private $relacionSistemaId;

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
* Get perfilId
*
* @return telconet\schemaBundle\Entity\SistPerfil
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


/**
* Get relacionSistemaId
*
* @return telconet\schemaBundle\Entity\SeguRelacionSistema
*/		
     		
public function getRelacionSistemaId(){
	return $this->relacionSistemaId; 
}

/**
* Set relacionSistemaId
*
* @param telconet\schemaBundle\Entity\SeguRelacionSistema $relacionSistemaId
*/
public function setRelacionSistemaId(\telconet\schemaBundle\Entity\SeguRelacionSistema $relacionSistemaId)
{
        $this->relacionSistemaId = $relacionSistemaId;
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
