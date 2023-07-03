<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoCotizacionCab
 *
 * @ORM\Table(name="INFO_COTIZACION_CAB")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoCotizacionCabRepository")
 */
class InfoCotizacionCab
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_COTIZACION", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_COTIZACION_CAB", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $numeroCotizacion
*
* @ORM\Column(name="NUMERO_COTIZACION", type="string", nullable=false)
*/		
     		
private $numeroCotizacion;

/**
* @var InfoPersonaEmpresaRol
*
* @ORM\ManyToOne(targetEntity="InfoPersonaEmpresaRol")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PERSONA_EMPRESA_ROL_ID", referencedColumnName="ID_PERSONA_ROL")
* })
*/
		
private $personaEmpresaRolId;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
*/		
     		
private $feCreacion;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var string $observacionAnulacion
*
* @ORM\Column(name="OBSERVACION_ANULACION", type="string", nullable=true)
*/		
     		
private $observacionAnulacion;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;

/**
* @var puntoId
*
* @ORM\ManyToOne(targetEntity="InfoPunto")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PUNTO_ID", referencedColumnName="ID_PUNTO")
* })
*/
		
private $puntoId;

/**
* @var string $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=true)
*/		
     		
private $empresaCod;

/**
* @var string $archivoDigital
*
* @ORM\Column(name="ARCHIVO_DIGITAL", type="string", nullable=true)
*/		
     		
private $archivoDigital;


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
* Get numeroCotizacion
*
* @return string
*/		
     		
public function getNumeroCotizacion()
{
	return $this->numeroCotizacion; 
}

/**
* Set numeroCotizacion
*
* @param string $strNumeroCotizacion
*/
public function setNumeroCotizacion($strNumeroCotizacion)
{
        $this->numeroCotizacion = $strNumeroCotizacion;
}


/**
* Get personaEmpresaRolId
*
* @return telconet\schemaBundle\Entity\InfoPersonaEmpresaRol
*/		
     		
public function getPersonaEmpresaRolId()
{
	return $this->personaEmpresaRolId; 
}

/**
* Set personaEmpresaRolId
*
* @param telconet\schemaBundle\Entity\InfoPersonaEmpresaRol $intPersonaEmpresaRolId
*/
public function setPersonaEmpresaRolId(\telconet\schemaBundle\Entity\InfoPersonaEmpresaRol $intPersonaEmpresaRolId)
{
        $this->personaEmpresaRolId = $intPersonaEmpresaRolId;
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
* Get observacionAnulacion
*
* @return string
*/		
     		
public function getObservacionAnulacion()
{
	return $this->observacionAnulacion; 
}

/**
* Set observacionAnulacion
*
* @param string $strObservacionAnulacion
*/
public function setObservacionAnulacion($strObservacionAnulacion)
{
        $this->observacionAnulacion = $strObservacionAnulacion;
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
* @param string $strIpCreacion
*/
public function setIpCreacion($strIpCreacion)
{
        $this->ipCreacion = $strIpCreacion;
}

/**
* Get puntoId
*
* @return integer
*/		
     		
public function getPuntoId()
{
	return $this->puntoId; 
}

/**
* Set puntoId
*
* @param number $intPuntoId
*/
public function setPuntoId($intPuntoId)
{
        $this->puntoId = $intPuntoId;
}

/**
* Get empresaCod
*
* @return string
*/		
     		
public function getEmpresaCod()
{
	return $this->empresaCod; 
}

/**
* Set empresaCod
*
* @param string $strEmpresaCod
*/
public function setEmpresaCod($strEmpresaCod)
{
        $this->empresaCod = $strEmpresaCod;
}

/**
* Get archivoDigital
*
* @return string
*/		
     		
public function getArchivoDigital()
{
	return $this->archivoDigital; 
}

/**
* Set archivoDigital
*
* @param string $strArchivoDigital
*/
public function setArchivoDigital($strArchivoDigital)
{
        $this->archivoDigital = $strArchivoDigital;
}


}