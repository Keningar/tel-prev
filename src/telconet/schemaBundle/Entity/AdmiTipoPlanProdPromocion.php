<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTipoPlanProdPromocion
 *
 * @ORM\Table(name="ADMI_TIPO_PLAN_PROD_PROMOCION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTipoPlanProdPromocionRepository")
 */
class AdmiTipoPlanProdPromocion
{

/**
* @var integer $id
*
* @ORM\Column(name="ID_TIPO_PLAN_PROD_PROMOCION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TIPO_PLAN_PROD_PROMO", allocationSize=1, initialValue=1)
*/
private $id;

/**
* @var AdmiTipoPromocion
*
* @ORM\ManyToOne(targetEntity="AdmiTipoPromocion")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TIPO_PROMOCION_ID", referencedColumnName="ID_TIPO_PROMOCION")
* })
*/
private $tipoPromocionId;

/**
* @var InfoPlanCab
*
* @ORM\ManyToOne(targetEntity="InfoPlanCab")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PLAN_ID", referencedColumnName="ID_PLAN", nullable=true)
* })
*/
private $planId;

/**
* @var AdmiProducto
*
* @ORM\ManyToOne(targetEntity="AdmiProducto")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PRODUCTO_ID", referencedColumnName="ID_PRODUCTO", nullable=true)
* })
*/
private $productoId;

/**
 * @var integer $solucionId
 *
 * @ORM\Column(name="SOLUCION_ID", type="integer", nullable=false)
 */
private $solucionId;

/**
* @var InfoPlanCab
*
* @ORM\ManyToOne(targetEntity="InfoPlanCab")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PLAN_ID_SUPERIOR", referencedColumnName="ID_PLAN", nullable=true)
* })
*/
private $planIdSuperior;

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
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/
private $feUltMod;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/
private $usrUltMod;

/**
* @var string $ipUltMod
*
* @ORM\Column(name="IP_ULT_MOD", type="string", nullable=true)
*/
private $ipUltMod;

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
* Get tipoPromocionId
*
* @return telconet\schemaBundle\Entity\AdmiTipoPromocion
*/
public function getTipoPromocionId()
{
    return $this->tipoPromocionId; 
}

/**
* Set tipoPromocionId
*
* @param telconet\schemaBundle\Entity\AdmiTipoPromocion $tipoPromocionId
*/
public function setTipoPromocionId(\telconet\schemaBundle\Entity\AdmiTipoPromocion $tipoPromocionId)
{
    $this->tipoPromocionId = $tipoPromocionId;
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
* Get solucionId
*
* @return integer
*/
public function getSolucionId()
{
    return $this->solucionId;
}

/**
* Set solucionId
*
* @param integer $solucionId
*/
public function setSolucionId($solucionId)
{
    $this->solucionId = $solucionId;
}

/**
* Get planIdSuperior
*
* @return telconet\schemaBundle\Entity\InfoPlanCab
*/
public function getPlanIdSuperior()
{
    return $this->planIdSuperior; 
}

/**
* Set planIdSuperior
*
* @param telconet\schemaBundle\Entity\InfoPlanCab $planIdSuperior
*/
public function setPlanIdSuperior(\telconet\schemaBundle\Entity\InfoPlanCab $planIdSuperior)
{
    $this->planIdSuperior = $planIdSuperior;
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
* Get feUltMod
*
* @return datetime
*/
public function getFeUltMod()
{
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
* Get usrUltMod
*
* @return string
*/
public function getUsrUltMod()
{
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
* Get ipUltMod
*
* @return string
*/
public function getIpUltMod()
{
    return $this->ipUltMod; 
}

/**
* Set ipUltMod
*
* @param string $ipUltMod
*/
public function setIpUltMod($ipUltMod)
{
    $this->ipUltMod = $ipUltMod;
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
