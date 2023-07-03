<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoTareaSeguimiento
 *
 * @ORM\Table(name="INFO_TAREA_SEGUIMIENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoTareaSeguimientoRepository")
 */
class InfoTareaSeguimiento
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_SEGUIMIENTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_TAREA_SEGUIMIENTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $detalleId
*
* @ORM\Column(name="DETALLE_ID", type="integer", nullable=true)
*/		
     		
private $detalleId;

/**
* @var string $observacion
*
* @ORM\Column(name="OBSERVACION", type="string", nullable=true)
*/		
     		
private $observacion;

/**
* @var string $estadoTarea
*
* @ORM\Column(name="ESTADO_TAREA", type="string", nullable=true)
*/		
     		
private $estadoTarea;

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
* @var string $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
*/		
     		
private $empresaCod;

/**
* @var string $interno
*
* @ORM\Column(name="INTERNO", type="string", nullable=false)
*/

private $interno;

/**
* @var string $departamentoId
*
* @ORM\Column(name="DEPARTAMENTO_ID", type="string", nullable=false)
*/

private $departamentoId;

/**
* @var integer $personaEmpresaRolId
*
* @ORM\Column(name="PERSONA_EMPRESA_ROL_ID", type="integer", nullable=true)
*/

private $personaEmpresaRolId;

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
* @return integer
*/		
     		
public function getDetalleId(){
	return $this->detalleId; 
}

/**
* Set detalleId
*
* @param integer $detalleId
*/
public function setDetalleId($detalleId)
{
        $this->detalleId = $detalleId;
}

/**
* Get observacion
*
* @return string
*/		
     		
public function getObservacion(){
	return $this->observacion; 
}

/**
* Set observacion
*
* @param string $observacion
*/
public function setObservacion($observacion)
{
        $this->observacion = $observacion;
}
	
     		
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
* Get estadoTarea
*
* @return string
*/		
     		
public function getEstadoTarea(){
	return $this->estadoTarea; 
}

/**
* Set estadoTarea
*
* @param string $estadoTarea
*/
public function setEstadoTarea($estadoTarea)
{
        $this->estadoTarea = $estadoTarea;
}

/**
* Get interno
*
* @return string
*/

public function getInterno()
{
    return $this->interno;
}

/**
* Set interno
*
* @param string $interno
*/
public function setInterno($interno)
{
    $this->interno = $interno;
}

/**
* Get departamentoId
*
* @return string
*/

public function getDepartamentoId()
{
    return $this->departamentoId;
}

/**
* Set departamentoId
*
* @param string $departamentoId
*/
public function setDepartamentoId($departamentoId)
{
    $this->departamentoId = $departamentoId;
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

}