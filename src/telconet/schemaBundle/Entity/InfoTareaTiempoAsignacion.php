<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoCasoTiempoAsignacion
 *
 * @ORM\Table(name="INFO_TAREA_TIEMPO_ASIGNACION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoTareaTiempoAsignacionRepository")
 */
class InfoTareaTiempoAsignacion
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_TAREA_TIEMPO_ASIGNACION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_TAREA_TIEMPO_ASIG", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $casoId
*
* @ORM\Column(name="CASO_ID", type="integer", nullable=false)
*/		
     		
private $casoId;

/**
* @var integer $detalleId
*
* @ORM\Column(name="DETALLE_ID", type="integer", nullable=false)
*/		
     		
private $detalleId;


/**
* @var string $tiempoCliente
*
* @ORM\Column(name="TIEMPO_CLIENTE", type="integer", nullable=false)
*/		
     		
private $tiempoCliente;

/**
* @var string $tiempoEmpresa
*
* @ORM\Column(name="TIEMPO_EMPRESA", type="integer", nullable=false)
*/		
     		
private $tiempoEmpresa;

/**
* @var string $observacion
*
* @ORM\Column(name="OBSERVACION", type="string", nullable=false)
*/		
     		
private $observacion;

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
* @var datetime $feEjecucion
*
* @ORM\Column(name="FE_EJECUCION", type="datetime", nullable=false)
*/		
     		
private $feEjecucion;

/**
* @var datetime $feFinalizacion
*
* @ORM\Column(name="FE_FINALIZACION", type="datetime", nullable=false)
*/		
     		
private $feFinalizacion;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get casoId
*
* @return integer
*/		
     		
public function getCasoId(){
	return $this->casoId; 
}

/**
* Set casoId
*
* @param integer $casoId
*/
public function setCasoId($casoId)
{
        $this->casoId = $casoId;
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
* Get tiempoCliente
*
* @return integer
*/		
     		
public function getTiempoCliente(){
	return $this->tiempoCliente; 
}

/**
* Set tiempoCliente
*
* @param integer $tiempoCliente
*/
public function setTiempoCliente($tiempoCliente)
{
        $this->tiempoCliente = $tiempoCliente;
}




/**
* Get tiempoEmpresa
*
* @return integer
*/		
     		
public function getTiempoEmpresa(){
	return $this->tiempoEmpresa; 
}

/**
* Set tiempoEmpresa
*
* @param integer $tiempoEmpresa
*/
public function setTiempoEmpresa($tiempoEmpresa)
{
        $this->tiempoEmpresa = $tiempoEmpresa;
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
* Get feEjecucion
*
* @return datetime
*/		
     		
public function getFeEjecucion(){
	return $this->feEjecucion; 
}

/**
* Set feEjecucion
*
* @param datetime $feEjecucion
*/
public function setFeEjecucion($feEjecucion)
{
        $this->feEjecucion = $feEjecucion;
}


/**
* Get feFinalizacion
*
* @return datetime
*/		
     		
public function getFeFinalizacion(){
	return $this->feFinalizacion; 
}

/**
* Set feFinalizacion
*
* @param datetime $feFinalizacion
*/
public function setFeFinalizacion($feFinalizacion)
{
        $this->feFinalizacion = $feFinalizacion;
}


}