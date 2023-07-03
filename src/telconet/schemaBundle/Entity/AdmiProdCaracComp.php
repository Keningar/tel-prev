<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiProdCaracComp
 *
 * @ORM\Table(name="ADMI_PROD_CARAC_COMPORTAMIENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiProdCaracCompRepository")
 */
class AdmiProdCaracComp
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_PROD_CARAC_COMP", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_PROD_CARAC_COMP", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiProductoCaracteristica
*
* @ORM\ManyToOne(targetEntity="AdmiProductoCaracteristica")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PRODUCTO_CARACTERISTICA_ID", referencedColumnName="ID_PRODUCTO_CARACTERISITICA")
* })
*/
		
private $productoCaracteristicaId;

/**
* @var integer $esVisible
*
* @ORM\Column(name="ES_VISIBLE", type="integer", nullable=true)
*/		
     		
private $esVisible;

/**
* @var integer $editable
*
* @ORM\Column(name="EDITABLE", type="integer", nullable=true)
*/		
     		
private $editable;

/**
* @var string $valoresSeleccionable
*
* @ORM\Column(name="VALORES_SELECCIONABLE", type="string", nullable=true)
*/		
     		
private $valoresSeleccionable;

/**
* @var string $valoresDefault
*
* @ORM\Column(name="VALORES_DEFAULT", type="string", nullable=true)
*/		
     		
private $valoresDefault;

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
* @return telconet\schemaBundle\Entity\AdmiProductoCaracteristica
*/		
     		
public function getProductoCaracteristicaId(){
	return $this->productoCaracteristicaId; 
}

/**
* Set productoId
*
* @param telconet\schemaBundle\Entity\AdmiProductoCaracteristica $productoCaracteristicaId
*/
public function setProductoCaracteristicaId(\telconet\schemaBundle\Entity\AdmiProductoCaracteristica $productoCaracteristicaId)
{
        $this->productoCaracteristicaId = $productoCaracteristicaId;
}

/**
* Get esVisible
*
* @return integer
*/		
     		
public function getEsVisible(){
	return $this->esVisible; 
}

/**
* Set esVisible
*
* @param integer $esVisible
*/
public function setEsVisible($esVisible)
{
        $this->esVisible = $esVisible;
}

/**
* Get editable
*
* @return integer
*/		
     		
public function getEditable(){
	return $this->editable; 
}

/**
* Set editable
*
* @param integer $editable
*/
public function setEditable($editable)
{
        $this->editable = $editable;
}

/**
* Get valoresSeleccionable
*
* @return string
*/		
     		
public function getValoresSeleccionable(){
	return $this->valoresSeleccionable; 
}

/**
* Set valoresSeleccionable
*
* @param string $valoresSeleccionable
*/
public function setValoresSeleccionable($valoresSeleccionable)
{
        $this->valoresSeleccionable = $valoresSeleccionable;
}

/**
* Get valoresDefault
*
* @return string
*/		
     		
public function getValoresDefault(){
	return $this->valoresDefault; 
}

/**
* Set valoresDefault
*
* @param string $valoresDefault
*/
public function setValoresDefault($valoresDefault)
{
        $this->valoresDefault = $valoresDefault;
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

}
