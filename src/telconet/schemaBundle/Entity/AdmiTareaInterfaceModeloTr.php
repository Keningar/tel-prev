<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTareaInterfaceModeloTr
 *
 * @ORM\Table(name="ADMI_TAREA_INTERFACE_MODELO_TR")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTareaInterfaceModeloTrRepository")
 */
class AdmiTareaInterfaceModeloTr
{

/**
* @var AdmiInterfaceModelo
*
* @ORM\Column(name="INTERFACE_MODELO_ID", type="integer", nullable=true)
*/
		
private $interfaceModeloId;

/**
* @var AdmiModeloElemento
*
* @ORM\Column(name="MODELO_ELEMENTO_ID", type="integer", nullable=true)
*/
		
private $modeloElementoId;

/**
* @var InfoTramo
*
* @ORM\Column(name="TRAMO_ID", type="integer", nullable=true)
*/
		
private $tramoId;

/**
* @var integer $tiempoMax
*
* @ORM\Column(name="TIEMPO_MAX", type="integer", nullable=true)
*/		
     		
private $tiempoMax;

/**
* @var string $unidadMedidaTiempo
*
* @ORM\Column(name="UNIDAD_MEDIDA_TIEMPO", type="string", nullable=true)
*/		
     		
private $unidadMedidaTiempo;

/**
* @var AdmiTarea
*
* @ORM\ManyToOne(targetEntity="AdmiTarea")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TAREA_ID", referencedColumnName="ID_TAREA")
* })
*/
private $tareaId;

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
* @var integer $id
*
* @ORM\Column(name="ID_TAREA_INTERFACE_MODELO_TRA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TAREA_INT_MOD_TR", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* Get interfaceModeloId
*
* @return integer
*/		
     		
public function getInterfaceModeloId(){
	return $this->interfaceModeloId; 
}

/**
* Set interfaceModeloId
*
* @param integer $interfaceMOdeloId
*/
public function setInterfaceModeloId($interfaceModeloId)
{
        $this->interfaceModeloId = $interfaceModeloId;
}


/**
* Get modeloElementoId
*
* @return integer
*/		
     		
public function getModeloElementoId(){
	return $this->modeloElementoId; 
}

/**
* Set modeloElementoId
*
* @param integer $modeloElementoId
*/
public function setModeloElementoId($modeloElementoId)
{
        $this->modeloElementoId = $modeloElementoId;
}


/**
* Get tramoId
*
* @return integer
*/		
     		
public function getTramoId(){
	return $this->tramoId; 
}

/**
* Set tramoId
*
* @param integer $tramoId
*/
public function setTramoId($tramoId)
{
        $this->tramoId = $tramoId;
}


/**
* Get tiempoMax
*
* @return integer
*/		
     		
public function getTiempoMax(){
	return $this->tiempoMax; 
}

/**
* Set tiempoMax
*
* @param integer $tiempoMax
*/
public function setTiempoMax($tiempoMax)
{
        $this->tiempoMax = $tiempoMax;
}


/**
* Get unidadMedidaTiempo
*
* @return string
*/		
     		
public function getUnidadMedidaTiempo(){
	return $this->unidadMedidaTiempo; 
}

/**
* Set unidadMedidaTiempo
*
* @param string $unidadMedidaTiempo
*/
public function setUnidadMedidaTiempo($unidadMedidaTiempo)
{
        $this->unidadMedidaTiempo = $unidadMedidaTiempo;
}


/**
* Get tareaId
*
* @return telconet\schemaBundle\Entity\AdmiTarea
*/		
     		
public function getTareaId(){
	return $this->tareaId; 
}

/**
* Set tareaId
*
* @param telconet\schemaBundle\Entity\AdmiTarea $tareaId
*/
public function setTareaId(\telconet\schemaBundle\Entity\AdmiTarea $tareaId)
{
        $this->tareaId = $tareaId;
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


/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

}