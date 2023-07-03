<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiInterfaceModelo
 *
 * @ORM\Table(name="ADMI_INTERFACE_MODELO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiInterfaceModeloRepository")
 */
class AdmiInterfaceModelo
{


/**
* @var string $claseInterface
*
* @ORM\Column(name="CLASE_INTERFACE", type="string", nullable=false)
*/		
     		
private $claseInterface;

/**
* @var string $formatoInterface
*
* @ORM\Column(name="FORMATO_INTERFACE", type="string", nullable=false)
*/		
     		
private $formatoInterface;

/**
* @var integer $cantidadInterface
*
* @ORM\Column(name="CANTIDAD_INTERFACE", type="integer", nullable=false)
*/		
     		
private $cantidadInterface;

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
* @var integer $id
*
* @ORM\Column(name="ID_INTERFACE_MODELO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_INTERFACE_MODELO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiModeloElemento
*
* @ORM\ManyToOne(targetEntity="AdmiModeloElemento")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="MODELO_ELEMENTO_ID", referencedColumnName="ID_MODELO_ELEMENTO")
* })
*/
		
private $modeloElementoId;

/**
* @var AdmiTipoInterface
*
* @ORM\ManyToOne(targetEntity="AdmiTipoInterface")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TIPO_INTERFACE_ID", referencedColumnName="ID_TIPO_INTERFACE")
* })
*/
		
private $tipoInterfaceId;

/**
* Get claseInterface
*
* @return string
*/		
     		
public function getClaseInterface(){
	return $this->claseInterface; 
}

/**
* Set claseInterface
*
* @param string $claseInterface
*/
public function setClaseInterface($claseInterface)
{
        $this->claseInterface = $claseInterface;
}

/**
* Get formatoInterface
*
* @return string
*/		
     		
public function getFormatoInterface(){
	return $this->formatoInterface; 
}

/**
* Set formatoInterface
*
* @param string $formatoInterface
*/
public function setFormatoInterface($formatoInterface)
{
        $this->formatoInterface = $formatoInterface;
}


/**
* Get cantidadInterface
*
* @return integer
*/		
     		
public function getCantidadInterface(){
	return $this->cantidadInterface; 
}

/**
* Set cantidadInterface
*
* @param integer $cantidadInterface
*/
public function setCantidadInterface($cantidadInterface)
{
        $this->cantidadInterface = $cantidadInterface;
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
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get modeloElementoId
*
* @return telconet\schemaBundle\Entity\AdmiModeloElemento
*/		
     		
public function getModeloElementoId(){
	return $this->modeloElementoId; 
}

/**
* Set modeloElementoId
*
* @param telconet\schemaBundle\Entity\AdmiModeloElemento $modeloElementoId
*/
public function setModeloElementoId(\telconet\schemaBundle\Entity\AdmiModeloElemento $modeloElementoId)
{
        $this->modeloElementoId = $modeloElementoId;
}


/**
* Get tipoInterfaceId
*
* @return telconet\schemaBundle\Entity\AdmiTipoInterface
*/		
     		
public function getTipoInterfaceId(){
	return $this->tipoInterfaceId; 
}

/**
* Set tipoInterfaceId
*
* @param telconet\schemaBundle\Entity\AdmiTipoInterface $tipoInterfaceId
*/
public function setTipoInterfaceId(\telconet\schemaBundle\Entity\AdmiTipoInterface $tipoInterfaceId)
{
        $this->tipoInterfaceId = $tipoInterfaceId;
}

public function __toString()
{
    return $this->formatoInterface;
}

}