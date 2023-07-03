<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiGestionDirectorios
 *
 * @ORM\Table(name="DB_GENERAL.ADMI_GESTION_DIRECTORIOS")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiGestionDirectoriosRepository")
 */
class AdmiGestionDirectorios
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_GESTION_DIRECTORIO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_GESTION_DIRECTORIOS", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var integer $codigoApp
*
* @ORM\Column(name="CODIGO_APP", type="integer", nullable=true)
*/		
     		
private $codigoApp;

/**
* @var integer $codigoPath
*
* @ORM\Column(name="CODIGO_PATH", type="integer", nullable=true)
*/		
     		
private $codigoPath;


/**
* @var string $aplicacion
*
* @ORM\Column(name="APLICACION", type="string", nullable=true)
*/		
     		
private $aplicacion;

/**
* @var string $pais
*
* @ORM\Column(name="PAIS", type="string", nullable=true)
*/		
     		
private $pais;

/**
* @var string $empresa
*
* @ORM\Column(name="EMPRESA", type="string", nullable=true)
*/		
     		
private $empresa;

/**
* @var string $modulo
*
* @ORM\Column(name="MODULO", type="string", nullable=true)
*/		
     		
private $modulo;

/**
* @var string $subModulo
*
* @ORM\Column(name="SUBMODULO", type="string", nullable=true)
*/		
     		
private $subModulo;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;


/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
*/		
     		
private $feCreacion;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=false)
*/		
     		
private $usrUltMod;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
*/		
     		
private $feUltMod;

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
* Get codigoApp
*
* @return codigoApp
*/		
     		
public function getCodigoApp()
{
    return $this->codigoApp; 
}

/**
* Set codigoApp
*
* @param string $intCodigoApp
*/
public function setCodigoApp($intCodigoApp)
{
    $this->codigoApp = $intCodigoApp;
}

/**
* Get codigoPath
*
* @return integer
*/		
     		
public function getCodigoPath()
{
    return $this->codigoPath; 
}

/**
* Set codigoPath
*
* @param string $intCodigoPath
*/
public function setCodigoPath($intCodigoPath)
{
    $this->codigoPath = $intCodigoPath;
}


/**
* Get aplicacion
*
* @return string
*/		
     		
public function getAplicacion()
{
    return $this->aplicacion; 
}

/**
* Set aplicacion
*
* @param string $strAplicacion
*/
public function setAplicacion($strAplicacion)
{
    $this->aplicacion = $strAplicacion;
}


/**
* Get pais
*
* @return string
*/		
     		
public function getPais()
{
    return $this->pais; 
}

/**
* Set pais
*
* @param string $strPais
*/
public function setPais($strPais)
{
    $this->pais = $strPais;
}

/**
* Get empresa
*
* @return string
*/		
     		
public function getEmpresa()
{
    return $this->empresa; 
}

/**
* Set empresa
*
* @param string $strEmpresa
*/
public function setEmpresa($strEmpresa)
{
    $this->empresa = $strEmpresa;
}

/**
* Get modulo
*
* @return string
*/		
     		
public function getModulo()
{    
    return $this->modulo; 
}

/**
* Set modulo
*
* @param integer $strModulo
*/
public function setModulo($strModulo)
{
    $this->modulo = $strModulo;
}

/**
* Get subModulo
*
* @return string
*/		
     		
public function getSubModulo()
{    
    return $this->subModulo; 
}

/**
* Set subModulo
*
* @param integer $strSubModulo
*/
public function setSubModulo($strSubModulo)
{
    $this->subModulo = $strSubModulo;
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
* @param string $strUsrCreacion
*/
public function setUsrCreacion($strUsrCreacion)
{
    $this->usrCreacion = $strUsrCreacion;
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
* @param datetime $objFeCreacion
*/
public function setFeCreacion($objFeCreacion)
{
    $this->feCreacion = $objFeCreacion;
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
* @param string $strUsrUltMod
*/
public function setUsrUltMod($strUsrUltMod)
{
    $this->usrUltMod = $strUsrUltMod;
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
* @param datetime $objFeUltMod
*/
public function setFeUltMod($objFeUltMod)
{
    $this->feUltMod = $objFeUltMod;
}

public function __toString()
{
    return $this->aplicacion;
}

}
