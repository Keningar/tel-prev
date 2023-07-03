<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoContratoClausula
 *
 * @ORM\Table(name="INFO_CONTRATO_CLAUSULA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoContratoClausulaRepository")
 */
class InfoContratoClausula
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_CLAUSULA_CONTRATO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_CONTRATO_CLAUSULA", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var InfoContrato
*
* @ORM\ManyToOne(targetEntity="InfoContrato")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="CONTRATO_ID", referencedColumnName="ID_CONTRATO")
* })
*/
		
private $contratoId;

/**
* @var integer $clausulaId
*
* @ORM\Column(name="CLAUSULA_ID", type="integer", nullable=false)
*/		
     		
private $clausulaId;

/**
* @var string $descripcionClausula
*
* @ORM\Column(name="DESCRIPCION_CLAUSULA", type="string", nullable=false)
*/		
     		
private $descripcionClausula;

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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get contratoId
*
* @return telconet\schemaBundle\Entity\InfoContrato
*/		
     		
public function getContratoId(){
	return $this->contratoId; 
}

/**
* Set contratoId
*
* @param telconet\schemaBundle\Entity\InfoContrato $contratoId
*/
public function setContratoId(\telconet\schemaBundle\Entity\InfoContrato $contratoId)
{
        $this->contratoId = $contratoId;
}


/**
* Get clausulaId
*
* @return integer
*/		
     		
public function getClausulaId(){
	return $this->clausulaId; 
}

/**
* Set clausulaId
*
* @param integer $clausulaId
*/
public function setClausulaId($clausulaId)
{
        $this->clausulaId = $clausulaId;
}


/**
* Get descripcionClausula
*
* @return 
*/		
     		
public function getDescripcionClausula(){
	return $this->descripcionClausula; 
}

/**
* Set descripcionClausula
*
* @param  $descripcionClausula
*/
public function setDescripcionClausula($descripcionClausula)
{
        $this->descripcionClausula = $descripcionClausula;
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

}