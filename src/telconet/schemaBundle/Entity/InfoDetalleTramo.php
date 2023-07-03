<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDetalleTramo
 *
 * @ORM\Table(name="INFO_DETALLE_TRAMO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDetalleTramoRepository")
 */
class InfoDetalleTramo
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_DETALLE_TRAMO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DETALLE_TRAMO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $tramoId
*
* @ORM\Column(name="TRAMO_ID", type="integer", nullable=false)
*/
		
private $tramoId;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;

/**
* @var string $nombreDetalle
*
* @ORM\Column(name="NOMBRE_DETALLE", type="string", nullable=false)
*/		
     		
private $nombreDetalle;

/**
* @var string $valorDetalle
*
* @ORM\Column(name="VALOR_DETALLE", type="string", nullable=false)
*/		
     		
private $valorDetalle;

/**
* @var string $descripcionDetalle
*
* @ORM\Column(name="DESCRIPCION_DETALLE", type="string", nullable=false)
*/		
     		
private $descripcionDetalle;

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
* @var string $usrModifica
*
* @ORM\Column(name="USR_MODIFICA", type="string", nullable=true)
*/		
     		
private $usrModifica;

/**
* @var datetime $feModifica
*
* @ORM\Column(name="FE_MODIFICA", type="datetime", nullable=true)
*/		
     		
private $feModifica;

/**
* @var string $ipModifica
*
* @ORM\Column(name="IP_MODIFICA", type="string", nullable=true)
*/		
     		
private $ipModifica;

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
* Get tramoId
*
* @return telconet\schemaBundle\Entity\InfoTramo
*/		
     		
public function getTramoId(){
	return $this->tramoId; 
}

/**
* Set tramoId
*
* @param integer $tramoId
*/
public function setTramoId($tramoId)
{
        $this->tramoId = $tramoId;
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
* Get usrModifica
*
* @return string
*/		
     		
public function getUsrModifica()
{
	return $this->usrModifica; 
}

/**
* Set usrModifica
*
* @param string $usrModifica
*/
public function setUsrModifica($usrModifica)
{
    $this->usrModifica = $usrModifica;
}


/**
* Get feModifica
*
* @return datetime
*/		
     		
public function getFeModifica()
{
	return $this->feModifica; 
}

/**
* Set feModifica
*
* @param datetime $feModifica
*/
public function setFeModifica($feModifica)
{
    $this->feModifica = $feModifica;
}


/**
* Get ipModifica
*
* @return string
*/		
     		
public function getIpModifica()
{
	return $this->ipModifica; 
}

/**
* Set ipModifica
*
* @param string $ipModifica
*/
public function setIpModifica($ipModifica)
{
    $this->ipModifica = $ipModifica;
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


/**
* Get nombreDetalle
*
* @return string
*/		
     		
public function getNombreDetalle()
{
	return $this->nombreDetalle; 
}

/**
* Set nombreDetalle
*
* @param string $nombreDetalle
*/
public function setNombreDetalle($nombreDetalle)
{
    $this->nombreDetalle = $nombreDetalle;
}

/**
* Get valorDetalle
*
* @return string
*/		
     		
public function getValorDetalle()
{
	return $this->valorDetalle; 
}

/**
* Set valorDetalle
*
* @param string $valorDetalle
*/
public function setValorDetalle($valorDetalle)
{
    $this->valorDetalle = $valorDetalle;
}

/**
* Get descripcionDetalle
*
* @return string
*/		
     		
public function getDescripcionDetalle()
{
	return $this->descripcionDetalle; 
}

/**
* Set descripcionDetalle
*
* @param string $descripcionDetalle
*/
public function setDescripcionDetalle($descripcionDetalle)
{
    $this->descripcionDetalle = $descripcionDetalle;
}


}