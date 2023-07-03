<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDetalleSeguimiento
 *
 * @ORM\Table(name="INFO_DETALLE_SEGUIMIENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDetalleSeguimientoRepository")
 */
class InfoDetalleSeguimiento
{


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
* @var integer $id
*
* @ORM\Column(name="ID_DETALLE_SEGUIMIENTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DETALLE_SEGUIMIENTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var InfoDetalleAsignacion
*
* @ORM\ManyToOne(targetEntity="InfoDetalleAsignacion")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="DETALLE_ASIGNACION_ID", referencedColumnName="ID_DETALLE_ASIGNACION")
* })
*/
		
private $detalleAsignacionId;

/**
* @var LONG $detalle
*
* @ORM\Column(name="DETALLE", type="LONG", nullable=true)
*/		
     		
private $detalle;

/**
* @var string $origen
*
* @ORM\Column(name="ORIGEN", type="string", nullable=false)
*/		
     		
private $origen;

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
* Get detalleAsignacionId
*
* @return telconet\schemaBundle\Entity\InfoDetalleAsignacion
*/		
     		
public function getDetalleAsignacionId(){
	return $this->detalleAsignacionId; 
}

/**
* Set detalleAsignacionId
*
* @param telconet\schemaBundle\Entity\InfoDetalleAsignacion $detalleAsignacionId
*/
public function setDetalleAsignacionId(\telconet\schemaBundle\Entity\InfoDetalleAsignacion $detalleAsignacionId)
{
        $this->detalleAsignacionId = $detalleAsignacionId;
}


/**
* Get detalle
*
* @return 
*/		
     		
public function getDetalle(){
	return $this->detalle; 
}

/**
* Set detalle
*
* @param  $detalle
*/
public function setDetalle($detalle)
{
        $this->detalle = $detalle;
}


/**
* Get origen
*
* @return string
*/		
     		
public function getOrigen(){
	return $this->origen; 
}

/**
* Set origen
*
* @param string $origen
*/
public function setOrigen($origen)
{
        $this->origen = $origen;
}

}