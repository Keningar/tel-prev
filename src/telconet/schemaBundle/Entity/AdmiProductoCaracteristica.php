<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiProductoCaracteristica
 *
 * @ORM\Table(name="ADMI_PRODUCTO_CARACTERISTICA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiProductoCaracteristicaRepository")
 */
class AdmiProductoCaracteristica
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PRODUCTO_CARACTERISITICA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_PRODUCTO_CARAC", allocationSize=1, initialValue=1)
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
* @var AdmiCaracteristica
*
* @ORM\ManyToOne(targetEntity="AdmiCaracteristica")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="CARACTERISTICA_ID", referencedColumnName="ID_CARACTERISTICA")
* })
*/
		
private $caracteristicaId;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=true)
*/		
     		
private $usrCreacion;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var string $visibleComercial
*
* @ORM\Column(name="VISIBLE_COMERCIAL", type="string", nullable=true)
*/		
     		
private $visibleComercial;

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
* Get caracteristicaId
*
* @return \telconet\schemaBundle\Entity\AdmiCaracteristica
*/		
     		
public function getCaracteristicaId(){
	return $this->caracteristicaId; 
}

/**
* Set caracteristicaId
*
* @param telconet\schemaBundle\Entity\AdmiCaracteristica $caracteristicaId
*/
public function setCaracteristicaId(\telconet\schemaBundle\Entity\AdmiCaracteristica $caracteristicaId)
{
        $this->caracteristicaId = $caracteristicaId;
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

/**
* Get visibleComercial
*
* @return string
*/		
     		
public function getVisibleComercial(){
	return $this->visibleComercial; 
}

/**
* Set visibleComercial
*
* @param string $visibleComercial
*/
public function setVisibleComercial($visibleComercial)
{
        $this->visibleComercial = $visibleComercial;
}

}
