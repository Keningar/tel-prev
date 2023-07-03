<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoInterfaceElemento
 *
 * @ORM\Table(name="INFO_INTERFACE_ELEMENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoInterfaceElementoRepository")
 */
class InfoInterfaceElemento
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_INTERFACE_ELEMENTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_INTERFACE_ELEMENTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $numeroSerie
*
* @ORM\Column(name="NUMERO_SERIE", type="string", nullable=true)
*/		
     		
private $numeroSerie;

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
* @var string $nombreInterfaceElemento
*
* @ORM\Column(name="NOMBRE_INTERFACE_ELEMENTO", type="string", nullable=false)
*/		
     		
private $nombreInterfaceElemento;

/**
* @var string $descripcionInterfaceElemento
*
* @ORM\Column(name="DESCRIPCION_INTERFACE_ELEMENTO", type="string", nullable=true)
*/		
     		
private $descripcionInterfaceElemento;

/**
* @var integer $capacidadUtilizada
*
* @ORM\Column(name="CAPACIDAD_UTILIZADA", type="integer", nullable=true)
*/		
     		
private $capacidadUtilizada;

/**
* @var string $unidadMedidaUtilizada
*
* @ORM\Column(name="UNIDAD_MEDIDA_UTILIZADA", type="string", nullable=true)
*/		
     		
private $unidadMedidaUtilizada;

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
* @var string $macInterfaceElemento
*
* @ORM\Column(name="MAC_INTERFACE_ELEMENTO", type="string", nullable=true)
*/		
     		
private $macInterfaceElemento;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;


/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}

/**
* Get numeroSerie
*
* @return string
*/		
     		
public function getNumeroSerie(){
	return $this->numeroSerie; 
}

/**
* Set numeroSerie
*
* @param string $numeroSerie
*/
public function setNumeroSerie($numeroSerie)
{
        $this->numeroSerie = $numeroSerie;
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
* Get nombreInterfaceElemento
*
* @return string
*/		
     		
public function getNombreInterfaceElemento(){
	return $this->nombreInterfaceElemento; 
}

/**
* Set nombreInterfaceElemento
*
* @param string $nombreInterfaceElemento
*/
public function setNombreInterfaceElemento($nombreInterfaceElemento)
{
        $this->nombreInterfaceElemento = $nombreInterfaceElemento;
}


/**
* Get descripcionInterfaceElemento
*
* @return string
*/		
     		
public function getDescripcionInterfaceElemento(){
	return $this->descripcionInterfaceElemento; 
}

/**
* Set descripcionInterfaceElemento
*
* @param string $descripcionInterfaceElemento
*/
public function setDescripcionInterfaceElemento($descripcionInterfaceElemento)
{
        $this->descripcionInterfaceElemento = $descripcionInterfaceElemento;
}


/**
* Get capacidadUtilizada
*
* @return integer
*/		
     		
public function getCapacidadUtilizada(){
	return $this->capacidadUtilizada; 
}

/**
* Set capacidadUtilizada
*
* @param integer $capacidadUtilizada
*/
public function setCapacidadUtilizada($capacidadUtilizada)
{
        $this->capacidadUtilizada = $capacidadUtilizada;
}


/**
* Get unidadMedidaUtilizada
*
* @return string
*/		
     		
public function getUnidadMedidaUtilizada(){
	return $this->unidadMedidaUtilizada; 
}

/**
* Set unidadMedidaUtilizada
*
* @param string $unidadMedidaUtilizada
*/
public function setUnidadMedidaUtilizada($unidadMedidaUtilizada)
{
        $this->unidadMedidaUtilizada = $unidadMedidaUtilizada;
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

/**
* Get macInterfaceElemento
*
* @return string
*/		
     		
public function getMacInterfaceElemento()
{
	return $this->macInterfaceElemento; 
}

/**
* Set macInterfaceElemento
*
* @param string $macInterfaceElemento
*/
public function setMacInterfaceElemento($macInterfaceElemento)
{
    $this->macInterfaceElemento = $macInterfaceElemento;
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

public function __toString()
{
        return $this->nombreInterfaceElemento;
}

}