<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDetalleElemento
 *
 * @ORM\Table(name="INFO_DETALLE_ELEMENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDetalleElementoRepository")
 */
class InfoDetalleElemento
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_DETALLE_ELEMENTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DETALLE_ELEMENTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $elementoId
*
* @ORM\Column(name="ELEMENTO_ID", type="integer", nullable=false)
*/	
private $elementoId;

/**
* @var string $detalleNombre
*
* @ORM\Column(name="DETALLE_NOMBRE", type="string", nullable=false)
*/		
     		
private $detalleNombre;

/**
* @var string $detalleValor
*
* @ORM\Column(name="DETALLE_VALOR", type="string", nullable=false)
*/		
     		
private $detalleValor;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;

/**
* @var string $detalleDescripcion
*
* @ORM\Column(name="DETALLE_DESCRIPCION", type="string", nullable=false)
*/		
     		
private $detalleDescripcion;

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
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		 		
private $estado;

/**
* @ORM\OneToMany(targetEntity="InfoDetalleElemento", mappedBy="parent")
*/
protected $children;

/**
* @ORM\ManyToOne(targetEntity="InfoDetalleElemento", inversedBy="children", fetch="LAZY")
* @ORM\JoinColumn(name="REF_DETALLE_ELEMENTO_ID", referencedColumnName="ID_DETALLE_ELEMENTO")
*/
protected $parent;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get elementoId
*
* @return telconet\schemaBundle\Entity\InfoElemento
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
* Get detalleNombre
*
* @return string
*/		
     		
public function getDetalleNombre(){
	return $this->detalleNombre; 
}

/**
* Set detalleNombre
*
* @param string $detalleNombre
*/
public function setDetalleNombre($detalleNombre)
{
        $this->detalleNombre = $detalleNombre;
}


/**
* Get detalleValor
*
* @return string
*/		
     		
public function getDetalleValor(){
	return $this->detalleValor; 
}

/**
* Set detalleValor
*
* @param string $detalleValor
*/
public function setDetalleValor($detalleValor)
{
        $this->detalleValor = $detalleValor;
}


/**
* Get detalleDescripcion
*
* @return string
*/		
     		
public function getDetalleDescripcion(){
	return $this->detalleDescripcion; 
}

/**
* Set detalleDescripcion
*
* @param string $detalleDescripcion
*/
public function setDetalleDescripcion($detalleDescripcion)
{
        $this->detalleDescripcion = $detalleDescripcion;
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
* Get estado
*
* @return string
*/		
public function getEstado()
{
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


public function getChildren() {
    return $this->children;
}

public function getParent() {
    return $this->parent;
}

public function setChildren($children) {
    $this->children = $children;
}

public function setParent($parent) {
    $this->parent = $parent;
}

public function getUsrCreacion() {
    return $this->usrCreacion;
}

public function setUsrCreacion($usrCreacion) {
    $this->usrCreacion = $usrCreacion;
}

}