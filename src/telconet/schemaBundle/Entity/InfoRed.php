<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoRed
 *
 * @ORM\Table(name="INFO_RED")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoRedRepository")
 */
class InfoRed
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_RED", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_RED", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiPrefijo
*
* @ORM\ManyToOne(targetEntity="AdmiPrefijo")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PREFIJO_ID", referencedColumnName="ID_PREFIJO")
* })
*/
		
private $prefijoId;

/**
* @var integer $proveedorRedId
*
* @ORM\Column(name="PROVEEDOR_RED_ID", type="integer", nullable=false)
*/		
     		
private $proveedorRedId;

/**
* @var InfoElemento
*
* @ORM\ManyToOne(targetEntity="InfoElemento")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="ELEMENTO_ID", referencedColumnName="ID_ELEMENTO")
* })
*/
		
private $elementoId;

/**
* @var integer $cantonId
*
* @ORM\Column(name="CANTON_ID", type="integer", nullable=true)
*/		
     		
private $cantonId;

/**
* @var string $redClaseC
*
* @ORM\Column(name="RED_CLASE_C", type="string", nullable=false)
*/		
     		
private $redClaseC;

/**
* @var string $tipo
*
* @ORM\Column(name="TIPO", type="string", nullable=true)
*/		
     		
private $tipo;

/**
* @var string $uso
*
* @ORM\Column(name="USO", type="string", nullable=true)
*/		
     		
private $uso;

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
* Get prefijoId
*
* @return telconet\schemaBundle\Entity\AdmiPrefijo
*/		
     		
public function getPrefijoId(){
	return $this->prefijoId; 
}

/**
* Set prefijoId
*
* @param telconet\schemaBundle\Entity\AdmiPrefijo $prefijoId
*/
public function setPrefijoId(\telconet\schemaBundle\Entity\AdmiPrefijo $prefijoId)
{
        $this->prefijoId = $prefijoId;
}


/**
* Get proveedorRedId
*
* @return integer
*/		
     		
public function getProveedorRedId(){
	return $this->proveedorRedId; 
}

/**
* Set proveedorRedId
*
* @param integer $proveedorRedId
*/
public function setProveedorRedId($proveedorRedId)
{
        $this->proveedorRedId = $proveedorRedId;
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
* @param telconet\schemaBundle\Entity\InfoElemento $elementoId
*/
public function setElementoId(\telconet\schemaBundle\Entity\InfoElemento $elementoId)
{
        $this->elementoId = $elementoId;
}


/**
* Get cantonId
*
* @return integer
*/		
     		
public function getCantonId(){
	return $this->cantonId; 
}

/**
* Set cantonId
*
* @param integer $cantonId
*/
public function setCantonId($cantonId)
{
        $this->cantonId = $cantonId;
}


/**
* Get redClaseC
*
* @return string
*/		
     		
public function getRedClaseC(){
	return $this->redClaseC; 
}

/**
* Set redClaseC
*
* @param string $redClaseC
*/
public function setRedClaseC($redClaseC)
{
        $this->redClaseC = $redClaseC;
}


/**
* Get tipo
*
* @return string
*/		
     		
public function getTipo(){
	return $this->tipo; 
}

/**
* Set tipo
*
* @param string $tipo
*/
public function setTipo($tipo)
{
        $this->tipo = $tipo;
}


/**
* Get uso
*
* @return string
*/		
     		
public function getUso(){
	return $this->uso; 
}

/**
* Set uso
*
* @param string $uso
*/
public function setUso($uso)
{
        $this->uso = $uso;
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