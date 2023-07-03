<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoProgresoPorcentaje
 *
 * @ORM\Table(name="INFO_PROGRESO_PORCENTAJE")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoProgresoPorcentajeRepository")
 */
class InfoProgresoPorcentaje
{
    
/**
* @var integer $id
*
* @ORM\Column(name="ID_PROGRESO_PORCENTAJE", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PROGRESO_PORCENTAJE", allocationSize=1, initialValue=1)
*/		
		
private $id;	

/**
* @var string $porcentaje
*
* @ORM\Column(name="PORCENTAJE", type="integer", nullable=false)
*/		
     		
private $porcentaje;

/**
* @var AdmiTipoProgreso
*
* @ORM\ManyToOne(targetEntity="AdmiTipoProgreso")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TIPO_PROGRESO_ID", referencedColumnName="ID_TIPO_PROGRESO")
* })
*/
    
private $tipoProgresoId;

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
* @var datetime $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
*/		
     		
private $ipCreacion;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;

/**
* @var string $orden
*
* @ORM\Column(name="ORDEN", type="integer", nullable=false)
*/		
     		
private $orden;

/**
* @var InfoEmpresaGrupo
*
* @ORM\ManyToOne(targetEntity="InfoEmpresaGrupo")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="EMPRESA_ID", referencedColumnName="COD_EMPRESA")
* })
*/
    
private $empresaId;

/**
* Get id
*
* @return integer
*/		


public function getId(){
	return $this->id; 
}

/**
* Get $porcentaje
*
* @return string
*/		
     		
public function getPorcentaje(){
	return $this->porcentaje; 
}

/**
* Set $porcentaje
*
* @param string $porcentaje
*/
public function setPorcentaje($porcentaje)
{
        $this->porcentaje = $porcentaje;
}

/*
* Get tipoProgresoId
*
* @return telconet\schemaBundle\Entity\AdmiTipoProgreso
*/    
         
public function getTipoProgreso(){
  return $this->tipoProgresoId; 
}

/*
* Set tipoProgresoId
*
* @param telconet\schemaBundle\Entity\AdmiTipoProgreso $tipoProgresoId
*/
public function setTipoProgreso(\telconet\schemaBundle\Entity\AdmiTipoProgreso $tipoProgresoId)
{
        $this->tipoProgresoId = $tipoProgresoId;
}

/*
* Get tareaId
*
* @return telconet\schemaBundle\Entity\AdmiTarea
*/    
         
public function getTareaId(){
  return $this->tareaId; 
}

/*
* Set tareaId
*
* @param telconet\schemaBundle\Entity\AdmiTarea $tareaid
*/
public function setTareaId(\telconet\schemaBundle\Entity\AdmiTarea $tareaid)
{
        $this->tareaId = $tareaid;
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
* @return 
*/		
     		
public function getFeCreacion(){
	return $this->feCreacion; 
}

/**
* Set feCreacion
*
* @param  $feCreacion
*/
public function setFeCreacion($feCreacion)
{
        $this->feCreacion = $feCreacion;
}

/**
* Get ipCreacion
*
* @return 
*/		
     		
public function getIpCreacion(){
	return $this->ipCreacion; 
}

/**
* Set ipCreacion
*
* @param  $ipCreacion
*/
public function setIpCreacion($ipCreacion)
{
        $this->ipCreacion = $ipCreacion;
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
* @return 
*/		
     		
public function getFeUltMod(){
	return $this->feUltMod; 
}

/**
* Set feUltMod
*
* @param  $feUltMod
*/
public function setFeUltMod($feUltMod)
{
        $this->feUltMod = $feUltMod;
}

/**
* Get orden
*
* @return string
*/		
     		
public function getOrden(){
	return $this->orden; 
}

/**
* Set orden
*
* @param string $orden
*/
public function setOrden($orden)
{
        $this->orden = $orden;
}

/*
* Get empresaId
*
* @return telconet\schemaBundle\Entity\InfoEmpresaGrupo
*/    
         
public function getEmpresaId(){
  return $this->empresaId; 
}

/*
* Set empresaId
*
* @param telconet\schemaBundle\Entity\InfoEmpresaGrupo $empresaId
*/
public function setEmpresaId(\telconet\schemaBundle\Entity\InfoEmpresaGrupo $empresaId)
{
        $this->empresaId = $empresaId;
}
    
}