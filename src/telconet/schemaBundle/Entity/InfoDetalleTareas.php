<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDetalleTareas
 *
 * @ORM\Table(name="INFO_DETALLE_TAREAS")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDetalleTareasRepository")
 */
class InfoDetalleTareas
{

  
/**
* @var integer $id
*
* @ORM\Column(name="DETALLE_ID", type="integer", nullable=false)
* @ORM\Id
*/	    
     		
private $id;


/**
* @var integer $numeroTarea
*
* @ORM\Column(name="NUMERO_TAREA", type="integer", nullable=false)
*/	    
     		
private $numeroTarea;

/**
* @var integer $personaEmpresaRolId
*
* @ORM\Column(name="PERSONA_EMPRESA_ROL_ID", type="integer", nullable=false)
*/		
     		
private $personaEmpresaRolId;

/**
* @var integer $departamentoId
*
* @ORM\Column(name="DEPARTAMENTO_ID", type="integer", nullable=false)
*/		
     		
private $departamentoId;

/**
* @var integer $oficinaId
*
* @ORM\Column(name="OFICINA_ID", type="integer", nullable=false)
*/		
     		
private $oficinaId;

/**
* @var integer $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

/**
* @var integer $detalleAsignacionId
*
* @ORM\Column(name="DETALLE_ASIGNACION_ID", type="integer", nullable=false)
*/		
     		
private $detalleAsignacionId;

/**
* @var integer $detalleHistorialId
*
* @ORM\Column(name="DETALLE_HISTORIAL_ID", type="integer", nullable=false)
*/		
     		
private $detalleHistorialId;

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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Set id
*
* @param  $id
*/
public function setId($id)
{
        $this->id = $id;
}

/**
* Get numeroTarea
*
* @return integer
*/		
     		
public function getNumeroTarea(){
	return $this->numeroTarea; 
}

/**
* Set numeroTarea
*
* @param  $numeroTarea
*/
public function setNumeroTarea($numeroTarea)
{
        $this->numeroTarea = $numeroTarea;
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
* @param  $personaEmpresaRolId
*/
public function setPersonaEmpresaRolId($personaEmpresaRolId)
{
        $this->personaEmpresaRolId = $personaEmpresaRolId;
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
* @param  $departamentoId
*/
public function setDepartamentoId($departamentoId)
{
        $this->departamentoId = $departamentoId;
}


/**
* Get oficinaId
*
* @return integer
*/		
     		
public function getOficinaId(){
	return $this->oficinaId; 
}

/**
* Set oficinaId
*
* @param  $oficinaId
*/
public function setOficinaId($oficinaId)
{
        $this->oficinaId = $oficinaId;
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
* @param  $estado
*/
public function setEstado($estado)
{
        $this->estado = $estado;
}


/**
* Get detalleAsignacionId
*
* @return integer
*/		
     		
public function getDetalleAsignacionId(){
	return $this->detalleAsignacionId; 
}

/**
* Set detalleAsignacionId
*
* @param  $detalleAsignacionId
*/
public function setDetalleAsignacionId($detalleAsignacionId)
{
        $this->detalleAsignacionId = $detalleAsignacionId;
}


/**
* Get detalleHistorialId
*
* @return integer
*/		
     		
public function getDetalleHistorialId(){
	return $this->detalleHistorialId; 
}

/**
* Set detalleHistorialId
*
* @param  $detalleHistorialId
*/
public function setDetalleHistorialId($detalleHistorialId)
{
        $this->detalleHistorialId = $detalleHistorialId;
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
* @param  $usrCreacion
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
* @param  $feCreacion
*/
public function setFeCreacion($feCreacion)
{
        $this->feCreacion = $feCreacion;
}

}
