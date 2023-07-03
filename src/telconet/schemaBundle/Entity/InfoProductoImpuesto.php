<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoProductoImpuesto
 *
 * @ORM\Table(name="INFO_PRODUCTO_IMPUESTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoProductoImpuestoRepository")
 */
class InfoProductoImpuesto
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PRODUCTO_IMPUESTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PRODUCTO_IMPUESTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiProducto
*
* @ORM\ManyToOne(targetEntity="AdmiProducto")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PRODUCTO_ID", referencedColumnName="ID_PRODUCTO")
* })
*/
		
private $productoId;

/**
* @var AdmiImpuesto
*
* @ORM\ManyToOne(targetEntity="AdmiImpuesto")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="IMPUESTO_ID", referencedColumnName="ID_IMPUESTO")
* })
*/
		
private $impuestoId;

/**
* @var integer $porcentajeImpuesto
*
* @ORM\Column(name="PORCENTAJE_IMPUESTO", type="integer", nullable=false)
*/		
     		
private $porcentajeImpuesto;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
*/		
     		
private $feCreacion;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

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
* @return telconet\schemaBundle\Entity\AdmiProducto
*/		
     		
public function getProductoId(){
	return $this->productoId; 
}

/**
* Set productoId
*
* @param telconet\schemaBundle\Entity\AdmiProducto $productoId
*/
public function setProductoId(\telconet\schemaBundle\Entity\AdmiProducto $productoId)
{
        $this->productoId = $productoId;
}


/**
* Get impuestoId
*
* @return telconet\schemaBundle\Entity\AdmiImpuesto
*/		
     		
public function getImpuestoId(){
	return $this->impuestoId; 
}

/**
* Set impuestoId
*
* @param telconet\schemaBundle\Entity\AdmiImpuesto $impuestoId
*/
public function setImpuestoId(\telconet\schemaBundle\Entity\AdmiImpuesto $impuestoId)
{
        $this->impuestoId = $impuestoId;
}


/**
* Get porcentajeImpuesto
*
* @return integer
*/		
     		
public function getPorcentajeImpuesto(){
	return $this->porcentajeImpuesto; 
}

/**
* Set porcentajeImpuesto
*
* @param integer $porcentajeImpuesto
*/
public function setPorcentajeImpuesto($porcentajeImpuesto)
{
        $this->porcentajeImpuesto = $porcentajeImpuesto;
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

}