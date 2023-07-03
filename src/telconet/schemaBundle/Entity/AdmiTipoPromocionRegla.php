<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTipoPromocionRegla
 *
 * @ORM\Table(name="ADMI_TIPO_PROMOCION_REGLA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTipoPromocionReglaRepository")
 */
class AdmiTipoPromocionRegla
{

/**
* @var integer $id
*
* @ORM\Column(name="ID_TIPO_PROMOCION_REGLA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TIPO_PROMOCION_REGLA", allocationSize=1, initialValue=1)
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
* @var AdmiCaracteristica
*
* @ORM\ManyToOne(targetEntity="AdmiCaracteristica")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="CARACTERISTICA_ID", referencedColumnName="ID_CARACTERISTICA")
* })
*/
private $caracteristicaId;

/**
* @var string $valor
*
* @ORM\Column(name="VALOR", type="string", nullable=false)
*/
private $valor;

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
* @var integer $secuencia
*
* @ORM\Column(name="SECUENCIA", type="integer", nullable=true)
*/		
     		
private $secuencia;

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
* Get caracteristicaId
*
* @return \telconet\schemaBundle\Entity\AdmiCaracteristica
*/
public function getCaracteristicaId()
{
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
* Get valor
*
* @return string
*/
public function getValor()
{
    return $this->valor; 
}

/**
* Set valor
*
* @param string $valor
*/
public function setValor($valor)
{
    $this->valor = $valor;
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
* Get secuencia
*
* @return integer
*/		     		
public function getSecuencia()
{
    return $this->secuencia;
}

/**
* Set secuencia
*
* @param integer $secuencia
*/
public function setSecuencia($secuencia)
{
    $this->secuencia = $secuencia;
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
