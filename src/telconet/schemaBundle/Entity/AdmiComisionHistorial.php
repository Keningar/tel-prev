<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiComisionHistorial
 *
 * @ORM\Table(name="ADMI_COMISION_HISTORIAL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiComisionHistorialRepository")
 */
class AdmiComisionHistorial
{

/**
* @var integer $id
*
* @ORM\Column(name="ID_COMISION_HISTORIAL", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_COMISION_HISTORIAL", allocationSize=1, initialValue=1)
*/
private $id;
/**
* @var AdmiComisionDet
*
* @ORM\ManyToOne(targetEntity="AdmiComisionDet")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="COMISION_DET_ID", referencedColumnName="ID_COMISION_DET")
* })
*/
private $comisionDetId;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
*/
private $feCreacion;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/
private $usrCreacion;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
*/
private $ipCreacion;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/
private $estado;

/**
* @var string $observacion
*
* @ORM\Column(name="OBSERVACION", type="string", nullable=false)
*/
private $observacion;

/**
* Get id
*
* @return integer
*/
public function getId()
{
    return $this->id; 
}

/**
* Get comisionDetId
*
* @return telconet\schemaBundle\Entity\AdmiComisionDet
*/
public function getComisionDetId()
{
    return $this->comisionDetId; 
}

/**
* Set comisionDetId
*
* @param telconet\schemaBundle\Entity\AdmiComisionDet $comisionDetId
*/
public function setComisionDetId(\telconet\schemaBundle\Entity\AdmiComisionDet $comisionDetId)
{
    $this->comisionDetId = $comisionDetId;
}

/**
* Get feCreacion
*
* @return datetime
*/
public function getFeCreacion()
{
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
* Get usrCreacion
*
* @return string
*/
public function getUsrCreacion()
{
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
* Get ipCreacion
*
* @return string
*/

public function getIpCreacion()
{
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
* Get observacion
*
* @return string
*/
public function getObservacion()
{
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
    
}
