<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTipoInterface
 *
 * @ORM\Table(name="ADMI_TIPO_INTERFACE")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTipoInterfaceRepository")
 */
class AdmiTipoInterface
{


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
* @var AdmiConectorInterface
*
* @ORM\ManyToOne(targetEntity="AdmiConectorInterface")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="CONECTOR_INTERFACE_ID", referencedColumnName="ID_CONECTOR_INTERFACE")
* })
*/
		
private $conectorInterfaceId;

/**
* @var integer $capacidadEntrada
*
* @ORM\Column(name="CAPACIDAD_ENTRADA", type="integer", nullable=true)
*/		
     		
private $capacidadEntrada;

/**
* @var string $nombreTipoInterface
*
* @ORM\Column(name="NOMBRE_TIPO_INTERFACE", type="string", nullable=false)
*/		
     		
private $nombreTipoInterface;

/**
* @var integer $id
*
* @ORM\Column(name="ID_TIPO_INTERFACE", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TIPO_INTERFACE", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $descripcionTipoInterface
*
* @ORM\Column(name="DESCRIPCION_TIPO_INTERFACE", type="string", nullable=true)
*/		
     		
private $descripcionTipoInterface;

/**
* @var string $unidadMedidaEntrada
*
* @ORM\Column(name="UNIDAD_MEDIDA_ENTRADA", type="string", nullable=true)
*/		
     		
private $unidadMedidaEntrada;

/**
* @var integer $capacidadSalida
*
* @ORM\Column(name="CAPACIDAD_SALIDA", type="integer", nullable=true)
*/		
     		
private $capacidadSalida;

/**
* @var string $unidadMedidaSalida
*
* @ORM\Column(name="UNIDAD_MEDIDA_SALIDA", type="string", nullable=true)
*/		
     		
private $unidadMedidaSalida;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

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
* Get conectorInterfaceId
*
* @return telconet\schemaBundle\Entity\AdmiConectorInterface
*/		
     		
public function getConectorInterfaceId(){
	return $this->conectorInterfaceId; 
}

/**
* Set conectorInterfaceId
*
* @param telconet\schemaBundle\Entity\AdmiConectorInterface $conectorInterfaceId
*/
public function setConectorInterfaceId(\telconet\schemaBundle\Entity\AdmiConectorInterface $conectorInterfaceId)
{
        $this->conectorInterfaceId = $conectorInterfaceId;
}


/**
* Get capacidadEntrada
*
* @return integer
*/		
     		
public function getCapacidadEntrada(){
	return $this->capacidadEntrada; 
}

/**
* Set capacidadEntrada
*
* @param integer $capacidadEntrada
*/
public function setCapacidadEntrada($capacidadEntrada)
{
        $this->capacidadEntrada = $capacidadEntrada;
}


/**
* Get nombreTipoInterface
*
* @return string
*/		
     		
public function getNombreTipoInterface(){
	return $this->nombreTipoInterface; 
}

/**
* Set nombreTipoInterface
*
* @param string $nombreTipoInterface
*/
public function setNombreTipoInterface($nombreTipoInterface)
{
        $this->nombreTipoInterface = $nombreTipoInterface;
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
* Get descripcionTipoInterface
*
* @return string
*/		
     		
public function getDescripcionTipoInterface(){
	return $this->descripcionTipoInterface; 
}

/**
* Set descripcionTipoInterface
*
* @param string $descripcionTipoInterface
*/
public function setDescripcionTipoInterface($descripcionTipoInterface)
{
        $this->descripcionTipoInterface = $descripcionTipoInterface;
}


/**
* Get unidadMedidaEntrada
*
* @return string
*/		
     		
public function getUnidadMedidaEntrada(){
	return $this->unidadMedidaEntrada; 
}

/**
* Set unidadMedidaEntrada
*
* @param string $unidadMedidaEntrada
*/
public function setUnidadMedidaEntrada($unidadMedidaEntrada)
{
        $this->unidadMedidaEntrada = $unidadMedidaEntrada;
}


/**
* Get capacidadSalida
*
* @return integer
*/		
     		
public function getCapacidadSalida(){
	return $this->capacidadSalida; 
}

/**
* Set capacidadSalida
*
* @param integer $capacidadSalida
*/
public function setCapacidadSalida($capacidadSalida)
{
        $this->capacidadSalida = $capacidadSalida;
}


/**
* Get unidadMedidaSalida
*
* @return string
*/		
     		
public function getUnidadMedidaSalida(){
	return $this->unidadMedidaSalida; 
}

/**
* Set unidadMedidaSalida
*
* @param string $unidadMedidaSalida
*/
public function setUnidadMedidaSalida($unidadMedidaSalida)
{
        $this->unidadMedidaSalida = $unidadMedidaSalida;
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

public function __toString()
{
    return $this->nombreTipoInterface;
}

}