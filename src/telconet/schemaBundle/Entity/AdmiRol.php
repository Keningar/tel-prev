<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiRol
 *
 * @ORM\Table(name="ADMI_ROL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiRolRepository")
 */
class AdmiRol
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_ROL", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_ROL", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiTipoRol
*
* @ORM\ManyToOne(targetEntity="AdmiTipoRol", inversedBy="tipo_roles")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TIPO_ROL_ID", referencedColumnName="ID_TIPO_ROL")
* })
*/
		
private $tipoRolId;

/**
* @var string $descripcionRol
*
* @ORM\Column(name="DESCRIPCION_ROL", type="string", nullable=false)
*/		
     		
private $descripcionRol;

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
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;


/**
* @var string $esJefe
*
* @ORM\Column(name="ES_JEFE", type="string", nullable=false)
*/		
     		
private $esJefe;

/**
* @var string $permiteAsignacion
*
* @ORM\Column(name="PERMITE_ASIGNACION", type="string", nullable=false)
*/		
     		
private $permiteAsignacion;
	
/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get tipoRolId
*
* @return integer
*/		
     		
public function getTipoRolId(){
	return $this->tipoRolId; 
}

/**
* Set tipoRolId
*
* @param telconet\schemaBundle\Entity\AdmiTipoRol $tipoRolId
*/
public function setTipoRolId(\telconet\schemaBundle\Entity\AdmiTipoRol $tipoRolId)
{
        $this->tipoRolId = $tipoRolId;
}

/**
* Get descripcionRol
*
* @return string
*/		
     		
public function getDescripcionRol(){
	return $this->descripcionRol; 
}

/**
* Set descripcionRol
*
* @param string $descripcionRol
*/
public function setDescripcionRol($descripcionRol)
{
        $this->descripcionRol = $descripcionRol;
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
* Get esJefe
*
* @return string
*/		
     		
public function getEsJefe(){
	return $this->esJefe; 
}

/**
* Set esJefe
*
* @param string $esJefe
*/
public function setEsJefe($esJefe)
{
        $this->esJefe = $esJefe;
}

/**
* Get permiteAsignacion
*
* @return string
*/		
     		
public function getPermiteAsignacion(){
	return $this->permiteAsignacion; 
}

/**
* Set permiteAsignacion
*
* @param string $permiteAsignacion
*/
public function setPermiteAsignacion($permiteAsignacion)
{
        $this->permiteAsignacion = $permiteAsignacion;
}
public function __toString()
{
        return $this->descripcionRol;
}

}