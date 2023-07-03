<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTipoPromocion
 *
 * @ORM\Table(name="ADMI_TIPO_PROMOCION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTipoPromocionRepository")
 */
class AdmiTipoPromocion
{

/**
* @var integer $id
*
* @ORM\Column(name="ID_TIPO_PROMOCION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TIPO_PROMOCION", allocationSize=1, initialValue=1)
*/
private $id;

/**
* @var AdmiGrupoPromocion
*
* @ORM\ManyToOne(targetEntity="AdmiGrupoPromocion")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="GRUPO_PROMOCION_ID", referencedColumnName="ID_GRUPO_PROMOCION")
* })
*/
private $grupoPromocionId;

/**
* @var string $codigoTipoPromocion
*
* @ORM\Column(name="CODIGO_TIPO_PROMOCION", type="string", nullable=false)
*/
private $codigoTipoPromocion;

/**
* @var string $tipo
*
* @ORM\Column(name="TIPO", type="string", nullable=false)
*/
private $tipo;

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
 * @var integer $tipoPromocionId
 *
 * @ORM\Column(name="TIPO_PROMOCION_ID", type="integer", nullable=false)
 */
private $tipoPromocionId;

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
* Get grupoPromocionId
*
* @return telconet\schemaBundle\Entity\AdmiGrupoPromocion
*/
public function getGrupoPromocionId()
{
    return $this->grupoPromocionId; 
}

/**
* Set grupoPromocionId
*
* @param telconet\schemaBundle\Entity\AdmiGrupoPromocion $grupoPromocionId
*/
public function setGrupoPromocionId(\telconet\schemaBundle\Entity\AdmiGrupoPromocion $grupoPromocionId)
{
    $this->grupoPromocionId = $grupoPromocionId;
}

/**
* Get codigoTipoPromocion
*
* @return string
*/
public function getCodigoTipoPromocion()
{
    return $this->codigoTipoPromocion; 
}

/**
* Set codigoTipoPromocion
*
* @param string $codigoTipoPromocion
*/
public function setCodigoTipoPromocion($codigoTipoPromocion)
{
    $this->codigoTipoPromocion = $codigoTipoPromocion;
}

/**
* Get tipo
*
* @return string
*/
public function getTipo()
{
    return $this->tipo; 
}

/**
* Set tipo
*
* @param string $tipo
*/
public function setTipo($tipo)
{
        $this->tipo = $tipo;
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
* Get tipoPromocionId
*
* @return integer
*/
public function getTipoPromocionId()
{
    return $this->tipoPromocionId;
}

/**
* Set tipoPromocionId
*
* @param integer $tipoPromocionId
*/
public function setTipoPromocionId($tipoPromocionId)
{
    $this->tipoPromocionId = $tipoPromocionId;
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
