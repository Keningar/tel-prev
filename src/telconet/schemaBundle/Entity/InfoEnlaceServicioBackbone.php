<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiProcesosTelconet
 *
 * @ORM\Table(name="INFO_ENLACE_SERVICIO_BACKBONE")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoEnlaceServicioBackboneRepository")
 */
class InfoEnlaceServicioBackbone
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_ENLACE_SERVICIO_BACKCBONE", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_ENLACE_SERVICIO", allocationSize=1, initialValue=1)
*/

private $id;

 /**
 * @var InfoEnlace
 *
 * @ORM\ManyToOne(targetEntity="InfoEnlace")
 * @ORM\JoinColumns({
 *   @ORM\JoinColumn(name="ENLACE_ID", referencedColumnName="ID_ENLACE")
 * })
 */
private $enlaceId;


/**
* @var string $servicioId
*
* @ORM\Column(name="SERVICIO_ID", type="integer", nullable=false)
*/
     
private $servicioId;

/**
* @var string $loginAux
*
* @ORM\Column(name="LOGIN_AUX", type="string", nullable=false)
*/
     
private $loginAux;

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
* @var string $usrModificacion
*
* @ORM\Column(name="USR_MODIFICACION", type="string", nullable=false)
*/		
     		
private $usrModificacion;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
*/		
     		
private $feCreacion;

/**
* @var $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;

/**
* Get id
*
* @return integer
*/

/**
* @var $tipoRuta
*
* @ORM\Column(name="TIPO_RUTA", type="string", nullable=true)
*/		
     		
private $tipoRuta;

/**
* Get id
*
* @return string
*/

public function getId()
{
    return $this->id; 
}

/**
* Set id
*
* @param string $intId
*/
public function setId($intId)
{
    $this->id = $intId;
}

/**
* Get enlaceId
*
* @return integer
*/

public function getEnlaceId()
{
    return $this->enlaceId; 
}

/**
* Set enlaceId
*
* @param integer $strEnlaceId
*/
public function setEnlaceId($strEnlaceId)
{
    $this->enlaceId = $strEnlaceId;
}

/**
* Get servicioId
*
* @return integer
*/

public function getServicioId()
{
    return $this->servicioId; 
}

/**
* Set servicioId
*
* @param integer $strServicioId
*/
public function setServicioId($strServicioId)
{
    $this->servicioId = $strServicioId;
}

/**
* Get loginAux
*
* @return string
*/

public function getLoginAux()
{
    return $this->loginAux; 
}

/**
* Set loginAux
*
* @param string $strLoginAux
*/
public function setLoginAux($strLoginAux)
{
    $this->loginAux = $strLoginAux;
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
* @param string $strEstado
*/
public function setEstado($strEstado)
{
    $this->estado = $strEstado;
}

public function getFeCreacion() 
{
    return $this->feCreacion;
}

public function setFeCreacion($strFeCreacion) 
{
$this->feCreacion = $strFeCreacion;
}

public function getUsrCreacion() 
{
    return $this->usrCreacion;
}

public function setUsrCreacion($strUsrCreacion) 
{
    $this->usrCreacion = $strUsrCreacion;
}

public function getIpCreacion() 
{
    return $this->ipCreacion;
}

public function setIpCreacion($strIpCreacion) 
{
    $this->ipCreacion = $strIpCreacion;
}
public function getTipoRuta()
{
    return $this->tipoRuta; 
}

/**
* Set tipoRuta
*
* @param string $strTipoRuta
*/
public function setTipoRuta($strTipoRuta)
{
    $this->tipoRuta = $strTipoRuta;
}

}