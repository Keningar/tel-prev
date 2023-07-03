<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiGrupoPromocionHisto
 *
 * @ORM\Table(name="ADMI_GRUPO_PROMOCION_HISTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiGrupoPromocionHistoRepository")
 */
class AdmiGrupoPromocionHisto
{

/**
* @var integer $id
*
* @ORM\Column(name="ID_GRUPO_PROMOCION_HISTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_GRUPO_PROMOCION_HISTO", allocationSize=1, initialValue=1)
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
* @var integer $motivoId
*
* @ORM\Column(name="MOTIVO_ID", type="integer", nullable=true)
*/		
     		
private $motivoId;

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
* Get motivoId
*
* @return integer
*/		
     		
public function getMotivoId(){
	return $this->motivoId; 
}

/**
* Set motivoId
*
* @param integer $motivoId
*/
public function setMotivoId($motivoId)
{
        $this->motivoId = $motivoId;
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
