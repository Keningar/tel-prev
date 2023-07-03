<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoMantenimientoTarea
 *
 * @ORM\Table(name="INFO_MANTENIMIENTO_TAREA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoMantenimientoTareaRepository")
 */
class InfoMantenimientoTarea
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_MANTENIMIENTO_TAREA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_MANTENIMIENTO_TAREA", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	


/**
* @var AdmiProceso
*
* @ORM\ManyToOne(targetEntity="AdmiProceso")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="MANTENIMIENTO_ID", referencedColumnName="ID_PROCESO")
* })
*/	
     		
private $mantenimientoId;
	
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
* @var string $frecuencia
*
* @ORM\Column(name="FRECUENCIA", type="string", nullable=false)
*/		
     		
private $frecuencia;


/**
* @var string $tipoFrecuencia
*
* @ORM\Column(name="TIPO_FRECUENCIA", type="string", nullable=false)
*/		
     		
private $tipoFrecuencia;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;


/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=true)
*/		
     		
private $usrCreacion;


/**
* @var date $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="date", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;


		 



/**
* Set Id
*
* @param integer $id
*/
public function setId($id)
{
    $this->id = $id;
}

/**
* Get Id
*
* @return integer 
*/
public function getId()
{
    return $this->Id;
}


/**
* Get mantenimientoId
*
* @return telconet\schemaBundle\Entity\AdmiProceso
*/		
     		
public function getMantenimientoId(){
	return $this->mantenimientoId; 
}

/**
* Set mantenimientoId
*
* @param telconet\schemaBundle\Entity\AdmiProceso $mantenimientoId
*/
public function setMantenimientoId(\telconet\schemaBundle\Entity\AdmiProceso $mantenimientoId)
{
        $this->mantenimientoId = $mantenimientoId;
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
* Get frecuencia
*
* @return string
*/		
     		
public function getFrecuencia(){
	return $this->frecuencia; 
}

/**
* Set frecuencia
*
* @param string $frecuencia
*/
public function setFrecuencia($frecuencia)
{
        $this->frecuencia = $frecuencia;
}


/**
* Get tipoFrecuencia
*
* @return string
*/		
     		
public function getTipoFrecuencia(){
	return $this->tipoFrecuencia; 
}

/**
* Set tipoFrecuencia
*
* @param string $tipoFrecuencia
*/
public function setTipoFrecuencia($tipoFrecuencia)
{
        $this->tipoFrecuencia = $tipoFrecuencia;
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
* Set UsrCreacion
*
* @param string $usrCreacion
*/
public function setUsrCreacion($usrCreacion)
{
    $this->usrCreacion = $usrCreacion;
}

/**
* Get UsrCreacion
*
* @return string
*/
public function getUsrCreacion()
{
    return $this->usrCreacion;
}


/**
* Set FeCreacion
*
* @param date $feCreacion
*/
public function setFeCreacion($feCreacion)
{
    $this->feCreacion = $feCreacion;
}

/**
* Get FeCreacion
*
* @return date
*/
public function getFeCreacion()
{
    return $this->feCreacion;
}

/**
* Set IpCreacion
*
* @param string $ipCreacion
*/
public function setIpCreacion($ipCreacion)
{
    $this->ipCreacion = $ipCreacion;
}


/**
* Get IpCreacion
*
* @return string
*/
public function getIpCreacion()
{
    return $this->ipCreacion;
}


    
}