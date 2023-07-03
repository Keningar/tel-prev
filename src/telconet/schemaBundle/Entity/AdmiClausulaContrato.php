<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiClausulaContrato
 *
 * @ORM\Table(name="ADMI_CLAUSULA_CONTRATO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiClausulaContratoRepository")
 */
class AdmiClausulaContrato
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_CLAUSULA_CONTRATO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_CLAUSULA_CONTRATO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiTipoContrato
*
* @ORM\ManyToOne(targetEntity="AdmiTipoContrato")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TIPO_CONTRATO_ID", referencedColumnName="ID_TIPO_CONTRATO")
* })
*/
		
private $tipoContratoId;

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
* @var string $nombreClausula
*
* @ORM\Column(name="NOMBRE_CLAUSULA", type="string", nullable=true)
*/		
     		
private $nombreClausula;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get tipoContratoId
*
* @return telconet\schemaBundle\Entity\AdmiTipoContrato
*/		
     		
public function getTipoContratoId(){
	return $this->tipoContratoId; 
}

/**
* Set tipoContratoId
*
* @param telconet\schemaBundle\Entity\AdmiTipoContrato $tipoContratoId
*/
public function setTipoContratoId(\telconet\schemaBundle\Entity\AdmiTipoContrato $tipoContratoId)
{
        $this->tipoContratoId = $tipoContratoId;
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


/**
* Get nombreClausula
*
* @return string
*/		
     		
public function getNombreClausula(){
	return $this->nombreClausula; 
}

/**
* Set nombreClausula
*
* @param string $nombreClausula
*/
public function setNombreClausula($nombreClausula)
{
        $this->nombreClausula = $nombreClausula;
}

}