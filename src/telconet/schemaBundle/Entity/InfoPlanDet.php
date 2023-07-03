<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPlanDet
 *
 * @ORM\Table(name="INFO_PLAN_DET")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPlanDetRepository")
 */
class InfoPlanDet
{


/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;

/**
* @var integer $id
*
* @ORM\Column(name="ID_ITEM", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PLAN_DET", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $productoId
*
* @ORM\Column(name="PRODUCTO_ID", type="integer", nullable=true)
*/		
     		
private $productoId;

/**
* @var integer $cantidadDetalle
*
* @ORM\Column(name="CANTIDAD_DETALLE", type="integer", nullable=true)
*/		
     		
private $cantidadDetalle;

/**
* @var float $costoItem
*
* @ORM\Column(name="COSTO_ITEM", type="float", nullable=true)
*/		
     		
private $costoItem;

/**
* @var float $precioItem
*
* @ORM\Column(name="PRECIO_ITEM", type="float", nullable=true)
*/		
     		
private $precioItem;

/**
* @var integer $descuentoItem
*
* @ORM\Column(name="DESCUENTO_ITEM", type="integer", nullable=true)
*/		
     		
private $descuentoItem;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=true)
*/		
     		
private $usrCreacion;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var InfoPlanCab
*
* @ORM\ManyToOne(targetEntity="InfoPlanCab")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PLAN_ID", referencedColumnName="ID_PLAN")
* })
*/
		
private $planId;

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
* Get productoId
*
* @return integer
*/		
     		
public function getProductoId(){
	return $this->productoId; 
}

/**
* Set productoId
*
* @param integer $productoId
*/
public function setProductoId($productoId)
{
        $this->productoId = $productoId;
}


/**
* Get cantidadDetalle
*
* @return integer
*/		
     		
public function getCantidadDetalle(){
	return $this->cantidadDetalle; 
}

/**
* Set cantidadDetalle
*
* @param integer $cantidadDetalle
*/
public function setCantidadDetalle($cantidadDetalle)
{
        $this->cantidadDetalle = $cantidadDetalle;
}


/**
* Get costoItem
*
* @return float
*/		
     		
public function getCostoItem(){
	return $this->costoItem; 
}

/**
* Set costoItem
*
* @param float $costoItem
*/
public function setCostoItem($costoItem)
{
        $this->costoItem = $costoItem;
}


/**
* Get precioItem
*
* @return float
*/		
     		
public function getPrecioItem(){
	return $this->precioItem; 
}

/**
* Set precioItem
*
* @param float $precioItem
*/
public function setPrecioItem($precioItem)
{
        $this->precioItem = $precioItem;
}


/**
* Get descuentoItem
*
* @return integer
*/		
     		
public function getDescuentoItem(){
	return $this->descuentoItem; 
}

/**
* Set descuentoItem
*
* @param integer $descuentoItem
*/
public function setDescuentoItem($descuentoItem)
{
        $this->descuentoItem = $descuentoItem;
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
* Get planId
*
* @return telconet\schemaBundle\Entity\InfoPlanCab
*/		
     		
public function getPlanId(){
	return $this->planId; 
}

/**
* Set planId
*
* @param telconet\schemaBundle\Entity\InfoPlanCab $planId
*/
public function setPlanId(\telconet\schemaBundle\Entity\InfoPlanCab $planId)
{
        $this->planId = $planId;
}

}
