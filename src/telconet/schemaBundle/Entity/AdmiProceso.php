<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * telconet\schemaBundle\Entity\AdmiProceso
 *
 * @ORM\Table(name="ADMI_PROCESO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiProcesoRepository")
 */
class AdmiProceso
{
/****Inicio Planes de Mantenimiento****/
    
/**
* @var array
*/
public $frecuencias;

/**
* @var array
*/
public $tiposFrecuencia;

public function __construct()
{
    $this->frecuencias = new ArrayCollection();
    $this->tiposFrecuencia = new ArrayCollection();
}

/**
* Set frecuencias
*
* @param string $frecuencias
*/
public function setFrecuencias(ArrayCollection $frecuencias)
{
    $this->frecuencias = $frecuencias;
}

/**
* Get frecuencias
*
* @return string
*/
public function getFrecuencias()
{
    return $this->frecuencias;
} 
    

/**
* Set frecuencias
*
* @param string $tiposFrecuencia
*/
public function setTiposFrecuencia(ArrayCollection $tiposFrecuencia)
{
    $this->tiposFrecuencia = $tiposFrecuencia;
}

/**
* Get tiposFrecuencia
*
* @return string
*/
public function getTiposFrecuencia()
{
    return $this->tiposFrecuencia;
} 
 
/****Fin Planes de Mantenimiento****/


/**
* @var string $aplicaEstado
*
* @ORM\Column(name="APLICA_ESTADO", type="string", nullable=true)
*/		
     		
private $aplicaEstado;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

/**
* @var string $esPlanMantenimiento
*
* @ORM\Column(name="PLANMANTENIMIENTO", type="string", nullable=false)
*/		
     		
private $esPlanMantenimiento;

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
* @ORM\Column(name="ID_PROCESO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_PROCESO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
		
/**
* @var AdmiProceso
*
* @ORM\ManyToOne(targetEntity="AdmiProceso")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PROCESO_PADRE_ID", referencedColumnName="ID_PROCESO")
* })
*/			
private $procesoPadreId;

/**
* @var string $nombreProceso
*
* @ORM\Column(name="NOMBRE_PROCESO", type="string", nullable=false)
*/		
     		
private $nombreProceso;

/**
* @var string $descripcionProceso
*
* @ORM\Column(name="DESCRIPCION_PROCESO", type="string", nullable=true)
*/		
     		
private $descripcionProceso;


/**
* @var string $visible
*
* @ORM\Column(name="VISIBLE", type="string", nullable=true)
*/		
     		
private $visible;

/**
* Get aplicaEstado
*
* @return string
*/		
     		
public function getAplicaEstado(){
	return $this->aplicaEstado; 
}

/**
* Set aplicaEstado
*
* @param string $aplicaEstado
*/
public function setAplicaEstado($aplicaEstado)
{
        $this->aplicaEstado = $aplicaEstado;
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
* Get esPlanMantenimiento
*
* @return string
*/		
     		
public function getEsPlanMantenimiento(){
	return $this->esPlanMantenimiento; 
}

/**
* Set esPlanMantenimiento
*
* @param string $esPlanMantenimiento
*/
public function setEsPlanMantenimiento($esPlanMantenimiento)
{
        $this->esPlanMantenimiento = $esPlanMantenimiento;
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

/**
* Get procesoPadreId
*
* @return telconet\schemaBundle\Entity\AdmiProceso
*/		
     		
public function getProcesoPadreId(){
	return $this->procesoPadreId; 
}

/**
* Set procesoPadreId
*
* @param telconet\schemaBundle\Entity\AdmiProceso $procesoPadreId
*/
public function setProcesoPadreId(\telconet\schemaBundle\Entity\AdmiProceso $procesoPadreId)
{
        $this->procesoPadreId = $procesoPadreId;
}


/**
* Get nombreProceso
*
* @return string
*/		
     		
public function getNombreProceso(){
	return $this->nombreProceso; 
}

/**
* Set nombreProceso
*
* @param string $nombreProceso
*/
public function setNombreProceso($nombreProceso)
{
        $this->nombreProceso = $nombreProceso;
}


/**
* Get descripcionProceso
*
* @return string
*/		
     		
public function getDescripcionProceso(){
	return $this->descripcionProceso; 
}

/**
* Set descripcionProceso
*
* @param string $descripcionProceso
*/
public function setDescripcionProceso($descripcionProceso)
{
        $this->descripcionProceso = $descripcionProceso;
}


/**
* Get visible
*
* @return string
*/		
     		
public function getVisible(){
	return $this->visible; 
}

/**
* Set visible
*
* @param string $visible
*/
public function setVisible($visible)
{
        $this->visible = $visible;
}


public function __toString()
{
        return $this->nombreProceso;
}

}