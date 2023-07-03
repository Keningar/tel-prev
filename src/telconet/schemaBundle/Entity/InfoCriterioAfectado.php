<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoCriterioAfectado
 *
 * @ORM\Table(name="INFO_CRITERIO_AFECTADO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoCriterioAfectadoRepository")
 */
class InfoCriterioAfectado
{


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
* @ORM\Column(name="ID_CRITERIO_AFECTADO", type="integer", nullable=false)
* @ORM\Id
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
* @var string $criterio
*
* @ORM\Column(name="CRITERIO", type="string", nullable=false)
*/		
     		
private $criterio;

/**
* @var string $opcion
*
* @ORM\Column(name="OPCION", type="string", nullable=false)
*/		
     		
private $opcion;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;

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

public function setId($id){
	return $this->id=$id; 
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
* Get criterio
*
* @return string
*/		
     		
public function getCriterio(){
	return $this->criterio; 
}

/**
* Set criterio
*
* @param string $criterio
*/
public function setCriterio($criterio)
{
        $this->criterio = $criterio;
}


/**
* Get opcion
*
* @return string
*/		
     		
public function getOpcion(){
	return $this->opcion; 
}

/**
* Set opcion
*
* @param string $opcion
*/
public function setOpcion($opcion)
{
        $this->opcion = $opcion;
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

}