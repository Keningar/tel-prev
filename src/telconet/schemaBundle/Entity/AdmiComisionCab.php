<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiComisionCab
 *
 * @ORM\Table(name="ADMI_COMISION_CAB")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiComisionCabRepository")
 */
class AdmiComisionCab
{

/**
* @var integer $id
*
* @ORM\Column(name="ID_COMISION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_COMISION_CAB", allocationSize=1, initialValue=1)
*/
private $id;
/**
* @var AdmiProducto
*
* @ORM\ManyToOne(targetEntity="AdmiProducto")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PRODUCTO_ID", referencedColumnName="ID_PRODUCTO")
* })
*/
private $productoId;

/**
* @var InfoPlanCab
*
* @ORM\ManyToOne(targetEntity="InfoPlanCab")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PLAN_ID", referencedColumnName="ID_PLAN")
* })
*/
private $planId;

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
* Get productoId
*
* @return telconet\schemaBundle\Entity\AdmiProducto
*/
public function getProductoId()
{
    return $this->productoId; 
}

/**
* Set productoId
*
* @param telconet\schemaBundle\Entity\AdmiProducto $productoId
*/
public function setProductoId(\telconet\schemaBundle\Entity\AdmiProducto $productoId)
{
    $this->productoId = $productoId;
}

/**
* Get planId
*
* @return telconet\schemaBundle\Entity\InfoPlanCab
*/
public function getPlanId()
{
    return $this->planId; 
}

/**
* Set planId
*
* @param telconet\schemaBundle\Entity\InfoPlanCab $planId
*/
public function setPlanId(\telconet\schemaBundle\Entity\InfoPlanCab $planId)
{
    $this->planId = $planId;
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
