<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoEmpresaRol
 *
 * @ORM\Table(name="INFO_EMPRESA_ROL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoEmpresaRolRepository")
 */
class InfoEmpresaRol
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_EMPRESA_ROL", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_EMPRESA_ROL", allocationSize=1, initialValue=1)
*/		
		
private $id;	

/**
* @var InfoEmpresaGrupo
*
* @ORM\ManyToOne(targetEntity="InfoEmpresaGrupo", inversedBy="empresagrupo_rol")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="EMPRESA_COD", referencedColumnName="COD_EMPRESA")
* })
*/	
private $empresaCod;

/**
* @var integer $rolId
*
* @ORM\Column(name="ROL_ID", type="integer", nullable=false)
*/
		
private $rolId;

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
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

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
* Get empresaCod
*
* @return telconet\schemaBundle\Entity\InfoEmpresaGrupo
*/		
     		
public function getEmpresaCod(){
	return $this->empresaCod; 
}

/**
* Set empresaCod
*
* @param telconet\schemaBundle\Entity\InfoEmpresaGrupo $empresaCod
*/
public function setEmpresaCod(\telconet\schemaBundle\Entity\InfoEmpresaGrupo $empresaCod)
{
        $this->empresaCod = $empresaCod;
}


/**
* Get rolId
*
* @return integer
*/		
     		
public function getRolId(){
	return $this->rolId; 
}

/**
* Set rolId
*
* @param integer $rolId
*/
public function setRolId($rolId)
{
        $this->rolId = $rolId;
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

public function __toString()
{
    return $this->empresaId . " - " . $this->rolId;
}

    /**
    * @ORM\OneToMany(targetEntity="InfoPersonaEmpresaRol", mappedBy="empresaRolId")
    */
    private $persona_rol;
    public function __construct()
    {
        $this->persona_rol = new \Doctrine\Common\Collections\ArrayCollection();
    }
    public function addPersonaRol(\telconet\schemaBundle\Entity\InfoPersonaEmpresaRol $persona_rol)
    {
        $this->persona_rol[] = $persona_rol;
    }

    public function getPersonaRol()
    {
        return $this->persona_rol;
    }

}
