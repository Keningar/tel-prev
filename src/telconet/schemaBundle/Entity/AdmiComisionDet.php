<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiComisionDet
 *
 * @ORM\Table(name="ADMI_COMISION_DET")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiComisionDetRepository")
 */
class AdmiComisionDet
{

/**
* @var integer $id
*
* @ORM\Column(name="ID_COMISION_DET", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_COMISION_DET", allocationSize=1, initialValue=1)
*/
private $id;
/**
* @var AdmiComisionCab
*
* @ORM\ManyToOne(targetEntity="AdmiComisionCab")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="COMISION_ID", referencedColumnName="ID_COMISION")
* })
*/
private $comisionId;

/**
* @var integer $parametroDetId
*
* @ORM\Column(name="PARAMETRO_DET_ID", type="integer", nullable=false)
*/
private $parametroDetId;

/**
* @var float $comisionVenta
*
* @ORM\Column(name="COMISION_VENTA", type="float", nullable=false)
*/
private $comisionVenta;

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
* Get id
*
* @return integer
*/
public function getId()
{
    return $this->id; 
}

/**
* Get comisionId
*
* @return telconet\schemaBundle\Entity\AdmiComisionCab
*/
public function getComisionId()
{
    return $this->comisionId; 
}

/**
* Set comisionId
*
* @param telconet\schemaBundle\Entity\AdmiComisionCab $comisionId
*/
public function setComisionId(\telconet\schemaBundle\Entity\AdmiComisionCab $comisionId)
{
    $this->comisionId = $comisionId;
}

/**
* Get parametroDetId
*
* @return integer 
*/
public function getParametroDetId()
{
    return $this->parametroDetId; 
}

/**
* Set parametroDetId
*
* @param integer $parametroDetId
*/
public function setParametroDetId($parametroDetId)
{
    $this->parametroDetId = $parametroDetId;
}

/**
* Get comisionVenta
*
* @return float
*/
public function getComisionVenta()
{
    return $this->comisionVenta; 
}

/**
* Set comisionVenta
*
* @param float $comisionVenta
*/
public function setComisionVenta($comisionVenta)
{
    $this->comisionVenta = $comisionVenta;
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

}
