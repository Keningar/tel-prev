<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoRelacionDetalle
 *
 * @ORM\Table(name="INFO_RELACION_DETALLE")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoRelacionDetalleRepository")
 */
class InfoRelacionDetalle
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_RELACION_DETALLE", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_RELACION_DETALLE", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $detalleInterfaceIdA
*
* @ORM\Column(name="DETALLE_INTERFACE_ID_A", type="integer", nullable=true)
*/		
     		
private $detalleInterfaceIdA;

/**
* @var integer $detalleInterfaceIdB
*
* @ORM\Column(name="DETALLE_INTERFACE_ID_B", type="integer", nullable=true)
*/		
     		
private $detalleInterfaceIdB;

/**
* @var string $tipoRelacion
*
* @ORM\Column(name="TIPO_RELACION", type="string", nullable=true)
*/		
     		
private $tipoRelacion;

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
* Get detalleInterfaceIdA
*
* @return integer
*/		
     		
public function getDetalleInterfaceIdA(){
	return $this->detalleInterfaceIdA; 
}

/**
* Set detalleInterfaceIdA
*
* @param integer $detalleInterfaceIdA
*/
public function setDetalleInterfaceIdA($detalleInterfaceIdA)
{
        $this->detalleInterfaceIdA = $detalleInterfaceIdA;
}


/**
* Get detalleInterfaceIdB
*
* @return integer
*/		
     		
public function getDetalleInterfaceIdB(){
	return $this->detalleInterfaceIdB; 
}

/**
* Set detalleInterfaceIdB
*
* @param integer $detalleInterfaceIdB
*/
public function setDetalleInterfaceIdB($detalleInterfaceIdB)
{
        $this->detalleInterfaceIdB = $detalleInterfaceIdB;
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