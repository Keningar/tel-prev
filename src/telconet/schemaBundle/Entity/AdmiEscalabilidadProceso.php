<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiEscalabilidadProceso
 *
 * @ORM\Table(name="ADMI_ESCALABILIDAD_PROCESO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiEscalabilidadProcesoRepository")
 */
class AdmiEscalabilidadProceso
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_ESCALABILIDAD_PROCESO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_ESCALABILIDAD_PROCESO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiProceso
*
* @ORM\ManyToOne(targetEntity="AdmiProceso")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PROCESO_ID", referencedColumnName="ID_PROCESO")
* })
*/
		
private $procesoId;

/**
* @var integer $rolId
*
* @ORM\Column(name="ROL_ID", type="integer", nullable=true)
*/		
     		
private $rolId;

/**
* @var integer $ordenEscalabilidad
*
* @ORM\Column(name="ORDEN_ESCALABILIDAD", type="integer", nullable=true)
*/		
     		
private $ordenEscalabilidad;

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
* Get procesoId
*
* @return telconet\schemaBundle\Entity\AdmiProceso
*/		
     		
public function getProcesoId(){
	return $this->procesoId; 
}

/**
* Set procesoId
*
* @param telconet\schemaBundle\Entity\AdmiProceso $procesoId
*/
public function setProcesoId(\telconet\schemaBundle\Entity\AdmiProceso $procesoId)
{
        $this->procesoId = $procesoId;
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
* Get ordenEscalabilidad
*
* @return integer
*/		
     		
public function getOrdenEscalabilidad(){
	return $this->ordenEscalabilidad; 
}

/**
* Set ordenEscalabilidad
*
* @param integer $ordenEscalabilidad
*/
public function setOrdenEscalabilidad($ordenEscalabilidad)
{
        $this->ordenEscalabilidad = $ordenEscalabilidad;
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

}