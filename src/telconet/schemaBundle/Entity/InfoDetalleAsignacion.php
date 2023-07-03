<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDetalleAsignacion
 *
 * @ORM\Table(name="INFO_DETALLE_ASIGNACION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDetalleAsignacionRepository")
 */
class InfoDetalleAsignacion
{


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
* @var integer $asignadoId
*
* @ORM\Column(name="ASIGNADO_ID", type="integer", nullable=false)
*/		
     		
private $asignadoId;

/**
* @var string $asignadoNombre
*
* @ORM\Column(name="ASIGNADO_NOMBRE", type="string", nullable=false)
*/		
     		
private $asignadoNombre;

/**
* @var integer $refAsignadoId
*
* @ORM\Column(name="REF_ASIGNADO_ID", type="integer", nullable=false)
*/		
     		
private $refAsignadoId;

/**
* @var string $refAsignadoNombre
*
* @ORM\Column(name="REF_ASIGNADO_NOMBRE", type="string", nullable=false)
*/		
     		
private $refAsignadoNombre;

/**
* @var integer $personaEmpresaRolId
*
* @ORM\Column(name="PERSONA_EMPRESA_ROL_ID", type="integer", nullable=false)
*/		
     		
private $personaEmpresaRolId;

/**
* @var text $motivo
*
* @ORM\Column(name="MOTIVO", type="text", nullable=true)
*/		
     		
private $motivo;

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
* @var string $tipoAsignado
*
* @ORM\Column(name="TIPO_ASIGNADO", type="string", nullable=false)
*/		
     		
private $tipoAsignado;

/**
* @var integer $departamentoId
*
* @ORM\Column(name="DEPARTAMENTO_ID", type="integer", nullable=false)
*/

private $departamentoId;

/**
* @var integer $cantonId
*
* @ORM\Column(name="CANTON_ID", type="integer", nullable=true)
*/

private $cantonId;

/**
* @var integer $id
*
* @ORM\Column(name="ID_DETALLE_ASIGNACION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DETALLE_ASIGNACION", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
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
* Get asignadoNombre
*
* @return string
*/		
     		
public function getAsignadoNombre(){
	return $this->asignadoNombre; 
}

/**
* Set asignadoNombre
*
* @param string $asignadoNombre
*/
public function setAsignadoNombre($asignadoNombre)
{
        $this->asignadoNombre = $asignadoNombre;
}


/**
* Get refAsignadoId
*
* @return integer
*/		
     		
public function getRefAsignadoId(){
	return $this->refAsignadoId; 
}

/**
* Set refAsignadoId
*
* @param integer $refAsignadoId
*/
public function setRefAsignadoId($refAsignadoId)
{
        $this->refAsignadoId = $refAsignadoId;
}


/**
* Get refAsignadoNombre
*
* @return string
*/		
     		
public function getRefAsignadoNombre(){
	return $this->refAsignadoNombre; 
}

/**
* Set refAsignadoNombre
*
* @param string $refAsignadoNombre
*/
public function setRefAsignadoNombre($refAsignadoNombre)
{
        $this->refAsignadoNombre = $refAsignadoNombre;
}


/**
* Get personaEmpresaRolId
*
* @return integer
*/		
     		
public function getPersonaEmpresaRolId(){
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
* Get motivo
*
* @return 
*/		
     		
public function getMotivo(){
	return $this->motivo; 
}

/**
* Set motivo
*
* @param  $motivo
*/
public function setMotivo($motivo)
{
        $this->motivo = $motivo;
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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}


/**
* Get tipoAsignado
*
* @return string
*/		
     		
public function getTipoAsignado(){
	return $this->tipoAsignado; 
}

/**
* Set tipoAsignado
*
* @param string $tipoAsignado
*/
public function setTipoAsignado($tipoAsignado)
{
        $this->tipoAsignado = $tipoAsignado;
}

/**
* Get departamentoId
*
* @return integer
*/

public function getDepartamentoId(){
	return $this->departamentoId;
}

/**
* Set departamentoId
*
* @param integer $departamentoId
*/
public function setDepartamentoId($departamentoId)
{
        $this->departamentoId = $departamentoId;
}

/**
* Get cantonId
*
* @return integer
*/

public function getCantonId(){
	return $this->cantonId;
}

/**
* Set cantonId
*
* @param integer $cantonId
*/
public function setCantonId($cantonId)
{
        $this->cantonId = $cantonId;
}

}