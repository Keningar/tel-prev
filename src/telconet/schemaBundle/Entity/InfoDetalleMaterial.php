<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDetalleMaterial
 *
 * @ORM\Table(name="INFO_DETALLE_MATERIAL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDetalleMaterialRepository")
 */
class InfoDetalleMaterial
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_DETALLE_MATERIAL", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DETALLE_MATERIAL", allocationSize=1, initialValue=1)
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
* @var string $materialCod
*
* @ORM\Column(name="MATERIAL_COD", type="string", nullable=true)
*/		
     		
private $materialCod;

/**
* @var float $costoMaterial
*
* @ORM\Column(name="COSTO_MATERIAL", type="float", nullable=true)
*/		
     		
private $costoMaterial;

/**
* @var float $precioVentaMaterial
*
* @ORM\Column(name="PRECIO_VENTA_MATERIAL", type="float", nullable=true)
*/		
     		
private $precioVentaMaterial;

/**
* @var float $cantidadNoFacturada
*
* @ORM\Column(name="CANTIDAD_NO_FACTURADA", type="float", nullable=true)
*/		
     		
private $cantidadNoFacturada;

/**
* @var float $cantidadFacturada
*
* @ORM\Column(name="CANTIDAD_FACTURADA", type="float", nullable=true)
*/		
     		
private $cantidadFacturada;

/**
* @var float $valorCobrado
*
* @ORM\Column(name="VALOR_COBRADO", type="float", nullable=true)
*/		
     		
private $valorCobrado;

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
* Get materialCod
*
* @return string
*/		
     		
public function getMaterialCod(){
	return $this->materialCod; 
}

/**
* Set materialCod
*
* @param string $materialCod
*/
public function setMaterialCod($materialCod)
{
        $this->materialCod = $materialCod;
}


/**
* Get costoMaterial
*
* @return 
*/		
     		
public function getCostoMaterial(){
	return $this->costoMaterial; 
}

/**
* Set costoMaterial
*
* @param  $costoMaterial
*/
public function setCostoMaterial($costoMaterial)
{
        $this->costoMaterial = $costoMaterial;
}


/**
* Get precioVentaMaterial
*
* @return 
*/		
     		
public function getPrecioVentaMaterial(){
	return $this->precioVentaMaterial; 
}

/**
* Set precioVentaMaterial
*
* @param  $precioVentaMaterial
*/
public function setPrecioVentaMaterial($precioVentaMaterial)
{
        $this->precioVentaMaterial = $precioVentaMaterial;
}


/**
* Get cantidadNoFacturada
*
* @return float
*/		
     		
public function getCantidadNoFacturada(){
	return $this->cantidadNoFacturada; 
}

/**
* Set cantidadNoFacturada
*
* @param float $cantidadNoFacturada
*/
public function setCantidadNoFacturada($cantidadNoFacturada)
{
        $this->cantidadNoFacturada = $cantidadNoFacturada;
}


/**
* Get cantidadFacturada
*
* @return float
*/		
     		
public function getCantidadFacturada(){
	return $this->cantidadFacturada; 
}

/**
* Set cantidadFacturada
*
* @param float $cantidadFacturada
*/
public function setCantidadFacturada($cantidadFacturada)
{
        $this->cantidadFacturada = $cantidadFacturada;
}


/**
* Get valorCobrado
*
* @return 
*/		
     		
public function getValorCobrado(){
	return $this->valorCobrado; 
}

/**
* Set valorCobrado
*
* @param  $valorCobrado
*/
public function setValorCobrado($valorCobrado)
{
        $this->valorCobrado = $valorCobrado;
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