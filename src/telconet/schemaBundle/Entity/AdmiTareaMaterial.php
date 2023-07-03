<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTareaMaterial
 *
 * @ORM\Table(name="ADMI_TAREA_MATERIAL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTareaMaterialRepository")
 */
class AdmiTareaMaterial
{


/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

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
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=false)
*/		
     		
private $usrUltMod;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
*/		
     		
private $feUltMod;

/**
* @var string $unidadMedidaMaterial
*
* @ORM\Column(name="UNIDAD_MEDIDA_MATERIAL", type="string", nullable=true)
*/		
     		
private $unidadMedidaMaterial;

/**
* @var integer $id
*
* @ORM\Column(name="ID_TAREA_MATERIAL", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TAREA_MATERIAL", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
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
* @var string $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
*/		
     		
private $empresaCod;

/**
* @var string $materialCod
*
* @ORM\Column(name="MATERIAL_COD", type="string", nullable=false)
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
* @var float $cantidadMaterial
*
* @ORM\Column(name="CANTIDAD_MATERIAL", type="float", nullable=false)
*/		
     		
private $cantidadMaterial;


/**
* @var string $facturable
*
* @ORM\Column(name="FACTURABLE", type="string", nullable=false)
*/		
     		
private $facturable;


/**
* Get Facturable
*
* @return string
*/		
     		
public function getFactuable(){
    return $this->facturable; 
}

/**
* Set Facturable
*
* @param string $facturable
*/
public function setFacturable($facturable)
{
    $this->facturable = $facturable;
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
* Get usrUltMod
*
* @return string
*/		
     		
public function getUsrUltMod(){
	return $this->usrUltMod; 
}

/**
* Set usrUltMod
*
* @param string $usrUltMod
*/
public function setUsrUltMod($usrUltMod)
{
        $this->usrUltMod = $usrUltMod;
}


/**
* Get feUltMod
*
* @return datetime
*/		
     		
public function getFeUltMod(){
	return $this->feUltMod; 
}

/**
* Set feUltMod
*
* @param datetime $feUltMod
*/
public function setFeUltMod($feUltMod)
{
        $this->feUltMod = $feUltMod;
}


/**
* Get unidadMedidaMaterial
*
* @return string
*/		
     		
public function getUnidadMedidaMaterial(){
	return $this->unidadMedidaMaterial; 
}

/**
* Set unidadMedidaMaterial
*
* @param string $unidadMedidaMaterial
*/
public function setUnidadMedidaMaterial($unidadMedidaMaterial)
{
        $this->unidadMedidaMaterial = $unidadMedidaMaterial;
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
* Get empresaCod
*
* @return string
*/		
     		
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
* Get cantidadMaterial
*
* @return float
*/		
     		
public function getCantidadMaterial(){
	return $this->cantidadMaterial; 
}

/**
* Set cantidadMaterial
*
* @param float $cantidadMaterial
*/
public function setCantidadMaterial($cantidadMaterial)
{
        $this->cantidadMaterial = $cantidadMaterial;
}

}