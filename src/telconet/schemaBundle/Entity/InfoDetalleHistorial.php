<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDetalleHistorial
 *
 * @ORM\Table(name="INFO_DETALLE_HISTORIAL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDetalleHistorialRepository")
 */
class InfoDetalleHistorial
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_DETALLE_HISTORIAL", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DETALLE_HISTORIAL", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var InfoDetalle
*
* @ORM\ManyToOne(targetEntity="InfoDetalle")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="DETALLE_ID", referencedColumnName="ID_DETALLE")
* })
*/
		
private $detalleId;

/**
* @var text $observacion
*
* @ORM\Column(name="OBSERVACION", type="text", nullable=true)
*/		
     		
private $observacion;

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
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
*/		
     		
private $ipCreacion;

/**
* @var string $motivo
*
* @ORM\Column(name="MOTIVO", type="string", nullable=false)
*/		
     		
private $motivo;


/**
* @var integer $asignadoId
*
* @ORM\Column(name="ASIGNADO_ID", type="integer", nullable=false)
*/		
     		
private $asignadoId;

/**
* @var integer $personaEmpresaRolId
*
* @ORM\Column(name="PERSONA_EMPRESA_ROL_ID", type="integer", nullable=true)
*/

private $personaEmpresaRolId;

/**
* @var integer $departamentoOrigenId
*
* @ORM\Column(name="DEPARTAMENTO_ORIGEN_ID", type="integer", nullable=true)
*/

private $departamentoOrigenId;

/**
* @var integer $departamentoDestinoId
*
* @ORM\Column(name="DEPARTAMENTO_DESTINO_ID", type="integer", nullable=true)
*/

private $departamentoDestinoId;

/**
* @var string $accion
*
* @ORM\Column(name="ACCION", type="string", nullable=true)
*/

private $accion;

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
* @var string $esSolucion
*
* @ORM\Column(name="ES_SOLUCION", type="string", nullable=true)
*/		
     		
private $esSolucion;

/**
* @var string $motivoFinTarea
*
* @ORM\Column(name="MOTIVO_FIN_TAREA", type="text", nullable=true)
*/		
     		
private $motivoFinTarea;

/**
* @var integer $cantonId
*
* @ORM\Column(name="MOTIVO_ID", type="integer", nullable=true)
*/
private $motivoId;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get detalleId
*
* @return telconet\schemaBundle\Entity\InfoDetalle
*/		
     		
public function getDetalleId(){
	return $this->detalleId; 
}

/**
* Set detalleId
*
* @param telconet\schemaBundle\Entity\InfoDetalle $detalleId
*/
public function setDetalleId(\telconet\schemaBundle\Entity\InfoDetalle $detalleId)
{
        $this->detalleId = $detalleId;
}


/**
* Get observacion
*
* @return 
*/		
     		
public function getObservacion(){
	return $this->observacion; 
}

/**
* Set observacion
*
* @param  $observacion
*/
public function setObservacion($observacion)
{
        $this->observacion = $observacion;
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


/**
* Get motivo
*
* @return string
*/		
     		
public function getMotivo(){
	return $this->motivo; 
}

/**
* Set motivo
*
* @param string $motivo
*/
public function setMotivo($motivo)
{
        $this->motivo = $motivo;
}

/**
* Get asignadoId
*
* @return integer
*/		
     		
public function getAsignadoId(){
	return $this->asignadoId; 
}

/**
* Set asignadoId
*
* @param integer $asignadoId
*/
public function setAsignadoId($asignadoId)
{
        $this->asignadoId = $asignadoId;
}

/**
* Get personaEmpresaRolId
*
* @return integer
*/

public function getPersonaEmpresaRolId()
{
    return $this->personaEmpresaRolId;
}

/**
* Set personaEmpresaRolId
*
* @param integer $personaEmpresaRolId
*/
public function setPersonaEmpresaRolId($personaEmpresaRolId)
{
    $this->personaEmpresaRolId = $personaEmpresaRolId;
}


/**
* Get departamentoOrigenId
*
* @return integer
*/

public function getDepartamentoOrigenId()
{
    return $this->departamentoOrigenId;
}

/**
* Set departamentoOrigenId
*
* @param integer $departamentoOrigenId
*/
public function setDepartamentoOrigenId($departamentoOrigenId)
{
    $this->departamentoOrigenId = $departamentoOrigenId;
}


/**
* Get departamentoDestinoId
*
* @return integer
*/

public function getDepartamentoDestinoId()
{
    return $this->departamentoDestinoId;
}

/**
* Set departamentoDestinoId
*
* @param integer $departamentoDestinoId
*/
public function setDepartamentoDestinoId($departamentoDestinoId)
{
    $this->departamentoDestinoId = $departamentoDestinoId;
}


/**
* Get accion
*
* @return string
*/

public function getAccion()
{
    return $this->accion;
}

/**
* Set accion
*
* @param string accion
*/
public function setAccion($accion)
{
    $this->accion = $accion;
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
* @param telconet\schemaBundle\Entity\AdmiTarea $tareaId
*/
public function setTareaId(\telconet\schemaBundle\Entity\AdmiTarea $tareaId)
{
    $this->tareaId = $tareaId;
}

/**
* Get esSolucion
*
* @return string
*/		
     		
public function getEsSolucion(){
	return $this->esSolucion; 
}

/**
* Set esSolucion
*
* @param string $esSolucion
*/
public function setEsSolucion($esSolucion)
{
    $this->esSolucion = $esSolucion;
}

/**
* Get motivoFinTarea
*
* @return string
*/		
     		
public function getMotivoFinTarea(){
	return $this->motivoFinTarea; 
}

/**
* Set motivoFinTarea
*
* @param string $motivoFinTarea
*/
public function setMotivoFinTarea($motivoFinTarea)
{
    $this->motivoFinTarea = $motivoFinTarea;
}

/**
* Get motivoId
*
* @return integer
*/		
     		
public function getMotivoId(){
	return $this->motivoId; 
}

/**
* Set motivoId
*
* @param integer $motivoId
*/
public function setMotivoId($motivoId)
{
        $this->motivoId = $motivoId;
}


}