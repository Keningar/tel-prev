<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoHistorialIngresoApp
 *
 * @ORM\Table(name="INFO_HISTORIAL_INGRESO_APP")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoHistorialIngresoAppRepository")
 */
class InfoHistorialIngresoApp
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_HISTORIAL_INGRESO_APP", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_HISTORIAL_INGRESO_APP", allocationSize=1, initialValue=1)
*/		
		
private $id;	


/**
* @var InfoPersona
*
* @ORM\ManyToOne(targetEntity="InfoPersona")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PERSONA_ID", referencedColumnName="ID_PERSONA")
* })
*/
    
private $personaId;


/**
* @var string $codigoDispositivo
*
* @ORM\Column(name="CODIGO_DISPOSITIVO", type="string", nullable=false)
*/		
     		
private $codigoDispositivo;


/**
* @var string $ipAcceso
*
* @ORM\Column(name="IP_ACCESO", type="string", nullable=true)
*/		
     		
private $ipAcceso;


/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

/**
* @var FLOAT $latitud
*
* @ORM\Column(name="LATITUD", type="float", nullable=true)
*/		
     		
private $latitud;

/**
* @var FLOAT $longitud
*
* @ORM\Column(name="LONGITUD", type="float", nullable=true)
*/		
     		
private $longitud;


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
* @var datetime $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
*/		
     		
private $ipCreacion;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
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


/*
* Get personaId
*
* @return telconet\schemaBundle\Entity\InfoPersona
*/    
         
public function getPersonaId()
{
  return $this->personaId; 
}

/*
* Set personaId
*
* @param telconet\schemaBundle\Entity\InfoPersona $intPersonaid
*/
public function setPersonaId(\telconet\schemaBundle\Entity\InfoPersona $intPersonaid)
{
    $this->personaId = $intPersonaid;
}

/**
* Get codigoDispositivo
*
* @return string
*/		
     		
public function getCodigoDispositivo()
{
	return $this->codigoDispositivo; 
}

/**
* Set codigoDispositivo
*
* @param string $strCodigodispositivo
*/
public function setCodigoDispositivo($strCodigodispositivo)
{
    $this->codigoDispositivo = $strCodigodispositivo;
}

/**
* Get ipAcceso
*
* @return string
*/		
     		
public function getIpAcceso()
{
	return $this->ipAcceso; 
}

/**
* Set ipAcceso
*
* @param string $strIpacceso
*/
public function setIpAcceso($strIpacceso)
{
    $this->ipAcceso = $strIpacceso;
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
* Get latitud
*
* @return 
*/
public function getLatitud()
{
    return $this->latitud;
}

/**
* Set latitud
*
* @param  $strLatitud
*/
public function setLatitud($strLatitud)
{
    $this->latitud = $strLatitud;
}

    
/**
* Get longitud
*
* @return 
*/
public function getLongitud()
{
    return $this->longitud;
}

/**
* Set longitud
*
* @param  $strLongitud
*/
public function setLongitud($strLongitud)
{
    $this->longitud = $strLongitud;
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
* @return 
*/		
     		
public function getFeCreacion()
{
	return $this->feCreacion; 
}

/**
* Set feCreacion
*
* @param  $strFeCreacion
*/
public function setFeCreacion($strFeCreacion)
{
    $this->feCreacion = $strFeCreacion;
}

/**
* Get ipCreacion
*
* @return 
*/		
     		
public function getIpCreacion()
{
	return $this->ipCreacion; 
}

/**
* Set ipCreacion
*
* @param  $strIpCreacion
*/
public function setIpCreacion($strIpCreacion)
{
    $this->ipCreacion = $strIpCreacion;
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
* @return 
*/		
     		
public function getFeUltMod()
{
	return $this->feUltMod; 
}

/**
* Set feUltMod
*
* @param  $strFeUltMod
*/
public function setFeUltMod($strFeUltMod)
{
    $this->feUltMod = $strFeUltMod;
}
    
}
