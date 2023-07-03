<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTipoRol
 *
 * @ORM\Table(name="ADMI_TIPO_ROL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTipoRolRepository")
 */
class AdmiTipoRol
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_TIPO_ROL", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TIPO_ROL", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $descripcionTipoRol
*
* @ORM\Column(name="DESCRIPCION_TIPO_ROL", type="string", nullable=false)
*/		
     		
private $descripcionTipoRol;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
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
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get descripcionTipoRol
*
* @return string
*/		
     		
public function getDescripcionTipoRol(){
	return $this->descripcionTipoRol; 
}

/**
* Set descripcionTipoRol
*
* @param string $descripcionTipoRol
*/
public function setDescripcionTipoRol($descripcionTipoRol)
{
        $this->descripcionTipoRol = $descripcionTipoRol;
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
* Get feUltMod
*
* @return datetime
*/		
     		
public function getFeUltMod(){
	return $this->feUltMod; 
}

/**
* Set feUltMod
*
* @param datetime $feUltMod
*/
public function setFeUltMod($feUltMod)
{
        $this->feUltMod = $feUltMod;
}


/**
* Get usrUltMod
*
* @return string
*/		
     		
public function getUsrUltMod(){
	return $this->usrUltMod; 
}

/**
* Set usrUltMod
*
* @param string $usrUltMod
*/
public function setUsrUltMod($usrUltMod)
{
        $this->usrUltMod = $usrUltMod;
}

public function __toString()
{
        return $this->descripcionTipoRol;
}

    /**
    * @ORM\OneToMany(targetEntity="AdmiRol", mappedBy="tipoRolId")
    */
    private $tipo_roles;
    public function __construct()
    {
        $this->tipo_roles = new \Doctrine\Common\Collections\ArrayCollection();
    }
    public function addTipoRoles(\telconet\schemaBundle\Entity\AdmiRol $tipo_roles)
    {
        $this->tipo_roles[] = $tipo_roles;
    }

    public function getTipoRoles()
    {
        return $this->tipo_roles;
    }

}