<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPersonaReferido
 *
 * @ORM\Table(name="INFO_PERSONA_REFERIDO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPersonaReferidoRepository")
 */
class InfoPersonaReferido
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PERSONA_REFERIDO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PERSONA_REFERIDO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var InfoPersonaEmpresaRol
*
* @ORM\ManyToOne(targetEntity="InfoPersonaEmpresaRol")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PERSONA_EMPRESA_ROL_ID", referencedColumnName="ID_PERSONA_ROL")
* })
*/
		
private $personaEmpresaRolId;

/**
* @var InfoPersonaEmpresaRol
*
* @ORM\ManyToOne(targetEntity="InfoPersonaEmpresaRol")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="REF_PERSONA_EMPRESA_ROL_ID", referencedColumnName="ID_PERSONA_ROL")
* })
*/
		
private $refPersonaEmpresaRolId;

/**
* @var InfoPersona
*
* @ORM\ManyToOne(targetEntity="InfoPersona")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="REFERIDO_ID", referencedColumnName="ID_PERSONA")
* })
*/

private $referidoId;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get personaEmpresaRolId
*
* @return telconet\schemaBundle\Entity\InfoPersonaEmpresaRol
*/
public function getPersonaEmpresaRolId(){
	return $this->personaEmpresaRolId; 
}

/**
* Set personaEmpresaRolId
*
* @param telconet\schemaBundle\Entity\InfoPersonaEmpresaRol $personaEmpresaRolId
*/
public function setPersonaEmpresaRolId(\telconet\schemaBundle\Entity\InfoPersonaEmpresaRol $personaEmpresaRolId)
{
        $this->personaEmpresaRolId = $personaEmpresaRolId;
}


/**
* Get refPersonaEmpresaRolId
*
* @return telconet\schemaBundle\Entity\InfoPersonaEmpresaRol
*/
public function getRefPersonaEmpresaRolId(){
	return $this->refPersonaEmpresaRolId; 
}

/**
* Set refPersonaEmpresaRolId
*
* @param telconet\schemaBundle\Entity\InfoPersonaEmpresaRol $refPersonaEmpresaRolId
*/
public function setRefPersonaEmpresaRolId(\telconet\schemaBundle\Entity\InfoPersonaEmpresaRol $refPersonaEmpresaRolId)
{
        $this->refPersonaEmpresaRolId = $refPersonaEmpresaRolId;
}

/**
* Get referidoId
*
* @return telconet\schemaBundle\Entity\InfoPersona
*/		
     		
public function getReferidoId(){
	return $this->referidoId; 
}

/**
* Set referidoId
*
* @param telconet\schemaBundle\Entity\InfoPersona $referidoId
*/
public function setReferidoId(\telconet\schemaBundle\Entity\InfoPersona $referidoId)
{
        $this->referidoId = $referidoId;
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

}
