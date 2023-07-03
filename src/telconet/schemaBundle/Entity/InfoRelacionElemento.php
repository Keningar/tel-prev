<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoRelacionElemento
 *
 * @ORM\Table(name="INFO_RELACION_ELEMENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoRelacionElementoRepository")
 */
class InfoRelacionElemento
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_RELACION_ELEMENTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_RELACION_ELEMENTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $elementoIdA
*
* @ORM\Column(name="ELEMENTO_ID_A", type="integer", nullable=true)
*/		
     		
private $elementoIdA;

/**
* @var integer $elementoIdB
*
* @ORM\Column(name="ELEMENTO_ID_B", type="integer", nullable=true)
*/		
     		
private $elementoIdB;

/**
* @var string $tipoRelacion
*
* @ORM\Column(name="TIPO_RELACION", type="string", nullable=true)
*/		
     		
private $tipoRelacion;

/**
* @var float $posicionX
*
* @ORM\Column(name="POSICION_X", type="float", nullable=true)
*/		
     		
private $posicionX;

/**
* @var float $posicionY
*
* @ORM\Column(name="POSICION_Y", type="float", nullable=true)
*/		
     		
private $posicionY;

/**
* @var float $posicionZ
*
* @ORM\Column(name="POSICION_Z", type="float", nullable=true)
*/		
     		
private $posicionZ;

/**
* @var string $observacion
*
* @ORM\Column(name="OBSERVACION", type="string", nullable=true)
*/		
     		
private $observacion;

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
* Get elementoIdA
*
* @return integer
*/		
     		
public function getElementoIdA(){
	return $this->elementoIdA; 
}

/**
* Set elementoIdA
*
* @param integer $elementoIdA
*/
public function setElementoIdA($elementoIdA)
{
        $this->elementoIdA = $elementoIdA;
}


/**
* Get elementoIdB
*
* @return integer
*/		
     		
public function getElementoIdB(){
	return $this->elementoIdB; 
}

/**
* Set elementoIdB
*
* @param integer $elementoIdB
*/
public function setElementoIdB($elementoIdB)
{
        $this->elementoIdB = $elementoIdB;
}


/**
* Get tipoRelacion
*
* @return string
*/		
     		
public function getTipoRelacion(){
	return $this->tipoRelacion; 
}

/**
* Set tipoRelacion
*
* @param string $tipoRelacion
*/
public function setTipoRelacion($tipoRelacion)
{
        $this->tipoRelacion = $tipoRelacion;
}


/**
* Get posicionX
*
* @return float
*/		
     		
public function getPosicionX(){
	return $this->posicionX; 
}

/**
* Set posicionX
*
* @param  $posicionX
*/
public function setPosicionX($posicionX)
{
        $this->posicionX = $posicionX;
}


/**
* Get posicionY
*
* @return float
*/		
     		
public function getPosicionY(){
	return $this->posicionY; 
}

/**
* Set posicionY
*
* @param  $posicionY
*/
public function setPosicionY($posicionY)
{
        $this->posicionY = $posicionY;
}


/**
* Get posicionZ
*
* @return float
*/		
     		
public function getPosicionZ(){
	return $this->posicionZ; 
}

/**
* Set posicionZ
*
* @param  $posicionZ
*/
public function setPosicionZ($posicionZ)
{
        $this->posicionZ = $posicionZ;
}


/**
* Get observacion
*
* @return string
*/		
     		
public function getObservacion(){
	return $this->observacion; 
}

/**
* Set observacion
*
* @param string $observacion
*/
public function setObservacion($observacion)
{
        $this->observacion = $observacion;
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