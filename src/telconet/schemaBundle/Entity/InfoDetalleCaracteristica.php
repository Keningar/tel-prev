<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDetalleCaracteristica
 *
 * @ORM\Table(name="INFO_DETALLE_CARACTERISTICA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDetalleCaracteristicaRepository")
 */
class InfoDetalleCaracteristica
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_DETALLE_CARACTERISTICA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DETALLE_CARACT", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $detalleElementoId
*
* @ORM\Column(name="DETALLE_ELEMENTO_ID", type="integer", nullable=false)
*/
		
private $detalleElementoId;	
	
/**
* @var integer $caracteristicaId
*
* @ORM\Column(name="CARACTERISTICA_ID", type="integer", nullable=false)
*/
		
private $caracteristicaId;

/**
* @var string $descripcionCaracteristica
*
* @ORM\Column(name="DESCRIPCION_CARACTERISTICA", type="string", nullable=false)
*/		
     		
private $descripcionCaracteristica;

/**
* @var string $valorCaracteristica
*
* @ORM\Column(name="VALOR_CARACTERISTICA", type="string", nullable=false)
*/		
     		
private $valorCaracteristica;

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
* Get detalleElementoId
*
* @return telconet\schemaBundle\Entity\InfoDetalleElemento
*/		
     		
public function getDetalleElementoId(){
	return $this->detalleElementoId; 
}

/**
* Set detalleElementoId
*
* @param telconet\schemaBundle\Entity\InfoDetalleElemento $detalleElementoId
*/
public function setDetalleElementoId(\telconet\schemaBundle\Entity\InfoDetalleElemento $detalleElementoId)
{
        $this->detalleElementoId = $detalleElementoId;
}

/**
* Get caracteristicaId
*
* @return telconet\schemaBundle\Entity\AdmiCaracteristica
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
* Get descripcionCaracteristica
*
* @return string
*/		
     		
public function getDescripcionCaracteristica(){
	return $this->descripcionCaracteristica; 
}

/**
* Set descripcionCaracteristica
*
* @param string $descripcionCaracteristica
*/
public function setDescripcionCaracteristica($descripcionCaracteristica)
{
        $this->descripcionCaracteristica = $descripcionCaracteristica;
}


/**
* Get valorCaracteristica
*
* @return string
*/		
     		
public function getValorCaracteristica(){
	return $this->valorCaracteristica; 
}

/**
* Set valorCaracteristica
*
* @param string $valorCaracteristica
*/
public function setValorCaracteristica($valorCaracteristica)
{
        $this->valorCaracteristica = $valorCaracteristica;
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