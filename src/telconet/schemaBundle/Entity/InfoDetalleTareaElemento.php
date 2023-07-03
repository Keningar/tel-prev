<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDetalleTareaElemento
 *
 * @ORM\Table(name="INFO_DETALLE_TAREA_ELEMENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDetalleTareaElementoRepository")
 */
class InfoDetalleTareaElemento
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_DETALLE_ELEMENTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DETALLE_TAREA_ELEM", allocationSize=1, initialValue=1)
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
* @var string $elementoId
*
* @ORM\Column(name="ELEMENTO_ID", type="integer", nullable=false)
*/
		
private $elementoId;

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
* Get elementoId
*
* @return integer
*/		
     		
public function getElementoId(){
	return $this->elementoId; 
}

/**
* Set elementoId
*
* @param integer $elementoId
*/
public function setElementoId($elementoId)
{
        $this->elementoId = $elementoId;
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

}