<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiGrupoPromocion
 *
 * @ORM\Table(name="ADMI_GRUPO_PROMOCION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiGrupoPromocionRepository")
 */
class AdmiGrupoPromocion
{

/**
* @var integer $id
*
* @ORM\Column(name="ID_GRUPO_PROMOCION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_GRUPO_PROMOCION", allocationSize=1, initialValue=1)
*/
private $id;

/**
* @var string $nombreGrupo
*
* @ORM\Column(name="NOMBRE_GRUPO", type="string", nullable=false)
*/
private $nombreGrupo;

/**
 * @var datetime $feInicioVigencia
 *
 * @ORM\Column(name="FE_INICIO_VIGENCIA", type="datetime", nullable=false)
 */
private $feInicioVigencia;

/**
 * @var datetime $feFinVigencia
 *
 * @ORM\Column(name="FE_FIN_VIGENCIA", type="datetime", nullable=false)
 */
private $feFinVigencia;

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
* @var InfoEmpresaGrupo
*
* @ORM\ManyToOne(targetEntity="InfoEmpresaGrupo")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="EMPRESA_COD", referencedColumnName="COD_EMPRESA")
* })
*/
private $empresaCod;

/**
 * @var integer $grupoPromocionId
 *
 * @ORM\Column(name="GRUPO_PROMOCION_ID", type="integer", nullable=false)
 */
private $grupoPromocionId;

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
* Get nombreGrupo
*
* @return string
*/
public function getNombreGrupo()
{
    return $this->nombreGrupo; 
}

/**
* Set nombreGrupo
*
* @param string $nombreGrupo
*/
public function setNombreGrupo($nombreGrupo)
{
    $this->nombreGrupo = $nombreGrupo;
}

/**
* Get feInicioVigencia
*
* @return datetime
*/
public function getFeInicioVigencia()
{
    return $this->feInicioVigencia;
}

/**
* Set feInicioVigencia
*
* @param  $feInicioVigencia
*/
public function setFeInicioVigencia($feInicioVigencia)
{
    $this->feInicioVigencia = $feInicioVigencia;
}

/**
* Get feFinVigencia
*
* @return datetime
*/
public function getFeFinVigencia()
{
    return $this->feFinVigencia;
}

/**
* Set feFinVigencia
*
* @param  $feFinVigencia
*/
public function setFeFinVigencia($feFinVigencia)
{
    $this->feFinVigencia = $feFinVigencia;
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
* Get empresaCod
*
* @return telconet\schemaBundle\Entity\InfoEmpresaGrupo
*/
public function getEmpresaCod()
{
    return $this->empresaCod; 
}

/**
* Set empresaCod
*
* @param telconet\schemaBundle\Entity\InfoEmpresaGrupo $empresaCod
*/
public function setEmpresaCod(\telconet\schemaBundle\Entity\InfoEmpresaGrupo $empresaCod)
{
    $this->empresaCod = $empresaCod;
}

/**
* Get grupoPromocionId
*
* @return integer
*/
public function getGrupoPromocionId()
{
    return $this->grupoPromocionId;
}

/**
* Set grupoPromocionId
*
* @param integer $grupoPromocionId
*/
public function setGrupoPromocionId($grupoPromocionId)
{
    $this->grupoPromocionId = $grupoPromocionId;
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
