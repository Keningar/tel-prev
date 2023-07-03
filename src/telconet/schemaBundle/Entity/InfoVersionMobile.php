<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoVersionMobile
 *
 * @ORM\Table(name="DB_MOBILEVERSION.mobile_version")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoVersionMobileRepository")
 */
class InfoVersionMobile
{

    /**
* @var integer $id
*
* @ORM\Column(name="ID_MOBILE_VERSION", type="integer", nullable=false)
* @ORM\Id
*/		
		
private $id;

/**
* @var integer $appMobile
*
* @ORM\Column(name="APP_MOBILE", type="string", nullable=false)
*/		
     		
private $appMobile;


/**
* @var string $url
*
* @ORM\Column(name="URL", type="string", nullable=true)
*/		
     		
private $url;

/**
* @var string $version
*
* @ORM\Column(name="VERSION", type="string", nullable=false)
*/		
     		
private $version;

/**
* @var string $fechaInicio
*
* @ORM\Column(name="FECHA_INICIO", type="string", nullable=false)
*/		
     		
private $fechaInicio;

/**
* @var datetime $fechaFin
*
* @ORM\Column(name="FECHA_FIN", type="datetime", nullable=false)
*/		
     		
private $fechaFin;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
    
private $estado;

/**
* @var string $fechaCreacion
*
* @ORM\Column(name="FE_CREACION", type="string", nullable=false)
*/		
     		
private $fechaCreacion;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=true)
*/		
     		
private $usrCreacion;

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
* @var datetime $feUltMod
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;
	

function getId()
{
    return $this->id;
}

function getAppMobile()
{
    return $this->appMobile;
}

function getUrl()
{
    return $this->url;
}

function getVersion()
{
    return $this->version;
}

function getFechaInicio()
{
    return $this->fechaInicio;
}

function getFechaFin()
{
    return $this->fechaFin;
}

function getEstado()
{
    return $this->estado;
}

function getFechaCreacion()
{
    return $this->fechaCreacion;
}

function getUsrCreacion()
{
    return $this->usrCreacion;
}

function getFeUltMod()
{
    return $this->feUltMod;
}

function getUsrUltMod()
{
    return $this->usrUltMod;
}

function getIpCreacion()
{
    return $this->ipCreacion;
}

function setId($id)
{
    $this->id = $id;
}

function setAppMobile($appMobile)
{
    $this->appMobile = $appMobile;
}

function setUrl($url)
{
    $this->url = $url;
}

function setVersion($version)
{
    $this->version = $version;
}

function setFechaInicio($fechaInicio)
{
    $this->fechaInicio = $fechaInicio;
}

function setFechaFin($fechaFin)
{
    $this->fechaFin = $fechaFin;
}

function setEstado($estado)
{
    $this->estado = $estado;
}

function setFechaCreacion($fechaCreacion)
{
    $this->fechaCreacion = $fechaCreacion;
}

function setUsrCreacion($usrCreacion)
{
    $this->usrCreacion = $usrCreacion;
}

function setFeUltMod($feUltMod)
{
    $this->feUltMod = $feUltMod;
}

function setUsrUltMod($usrUltMod)
{
    $this->usrUltMod = $usrUltMod;
}

function setIpCreacion($ipCreacion)
{
    $this->ipCreacion = $ipCreacion;
}


}

