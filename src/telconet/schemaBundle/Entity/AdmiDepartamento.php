<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiDepartamento
 *
 * @ORM\Table(name="ADMI_DEPARTAMENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiDepartamentoRepository")
 */
class AdmiDepartamento
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_DEPARTAMENTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_DEPARTAMENTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiArea
*
* @ORM\ManyToOne(targetEntity="AdmiArea")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="AREA_ID", referencedColumnName="ID_AREA")
* })
*/
		
private $areaId;

/**
* @var string $nombreDepartamento
*
* @ORM\Column(name="NOMBRE_DEPARTAMENTO", type="string", nullable=false)
*/		
     		
private $nombreDepartamento;

/**
* @var string $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
*/		
     		
private $empresaCod;

/**
* @var string $emailDepartamento
*
* @ORM\Column(name="EMAIL_DEPARTAMENTO", type="string", nullable=false)
*/		
     		
private $emailDepartamento;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

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
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=false)
*/		
     		
private $usrUltMod;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
*/		
     		
private $feUltMod;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get areaId
*
* @return telconet\schemaBundle\Entity\AdmiArea
*/		
     		
public function getAreaId(){
	return $this->areaId; 
}

/**
* Set areaId
*
* @param telconet\schemaBundle\Entity\AdmiArea $areaId
*/
public function setAreaId(\telconet\schemaBundle\Entity\AdmiArea $areaId)
{
        $this->areaId = $areaId;
}


/**
* Get nombreDepartamento
*
* @return string
*/		
     		
public function getNombreDepartamento(){
	return $this->nombreDepartamento; 
}

/**
* Set nombreDepartamento
*
* @param string $nombreDepartamento
*/
public function setNombreDepartamento($nombreDepartamento)
{
        $this->nombreDepartamento = $nombreDepartamento;
}


/**
* Get empresaCod
*
* @return string
*/		
     		
public function getEmpresaCod(){
	return $this->empresaCod; 
}

/**
* Set empresaCod
*
* @param string $empresaCod
*/
public function setEmpresaCod($empresaCod)
{
        $this->empresaCod = $empresaCod;
}

/**
* Get emailDepartamento
*
* @return string
*/		
     		
public function getEmailDepartamento(){
	return $this->emailDepartamento; 
}

/**
* Set emailDepartamento
*
* @param string $emailDepartamento
*/
public function setEmailDepartamento($emailDepartamento)
{
        $this->emailDepartamento = $emailDepartamento;
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

public function __toString()
{
        return $this->nombreDepartamento;
}

}