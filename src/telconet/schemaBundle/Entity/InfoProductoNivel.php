<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoProductoNivel
 *
 * @ORM\Table(name="INFO_PRODUCTO_NIVEL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoProductoNivelRepository")
 */
class InfoProductoNivel
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PRODUCTO_NIVEL", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PRODUCTO_NIVEL", allocationSize=1, initialValue=1)
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
* @var string $empresaRolId
*
* @ORM\Column(name="EMPRESA_ROL_ID", type="string", nullable=true)
*/		
     		
private $empresaRolId;

/**
* @var string $porcentajeDescuento
*
* @ORM\Column(name="PORCENTAJE_DESCUENTO", type="string", nullable=true)
*/		
     		
private $porcentajeDescuento;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
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
* Get empresaRolId
*
* @return string
*/		
     		
public function getEmpresaRolId(){
	return $this->empresaRolId; 
}

/**
* Set empresaRolId
*
* @param string $empresaRolId
*/
public function setEmpresaRolId($empresaRolId)
{
        $this->empresaRolId = $empresaRolId;
}


/**
* Get porcentajeDescuento
*
* @return string
*/		
     		
public function getPorcentajeDescuento(){
	return $this->porcentajeDescuento; 
}

/**
* Set porcentajeDescuento
*
* @param string $porcentajeDescuento
*/
public function setPorcentajeDescuento($porcentajeDescuento)
{
        $this->porcentajeDescuento = $porcentajeDescuento;
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