<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiDispositivoApp
 *
 * @ORM\Table(name="ADMI_DISPOSITIVO_APP")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiDispositivoAppRepository")
 */
class AdmiDispositivoApp
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_DISPOSITIVO_APP", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_DISPOSITIVO_APP", allocationSize=1, initialValue=1)
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
* @var string $descripcion
*
* @ORM\Column(name="DESCRIPCION", type="string", nullable=true)
*/		
     		
private $descripcion;


/**
* @var string $correo
*
* @ORM\Column(name="CORREO", type="string", nullable=true)
*/		
     		
private $correo;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var string $nombreAppmovil
*
* @ORM\Column(name="NOMBRE_APP_MOVIL", type="string", nullable=true)
*/		
     		
private $nombreAppmovil;

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
* @var integer $bloqueado
*
* @ORM\Column(name="BLOQUEADO", type="integer", nullable=false)
*/		
     		
private $bloqueado;


/**
* @var string $sistemaOperativo
*
* @ORM\Column(name="SISTEMA_OPERATIVO", type="string", nullable=true)
*/		
     		
private $sistemaOperativo;


/**
* @var string $tipoDispositivo
*
* @ORM\Column(name="TIPO_DISPOSITIVO", type="string", nullable=true)
*/		
     		
private $tipoDispositivo;


/**
* @var string $tokenFCM
*
* @ORM\Column(name="TOKEN_FCM", type="string", nullable=false)
*/		
     		
private $tokenFCM;

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
* Get descripcion
*
* @return string
*/		
     		
public function getDescripcion()
{
	return $this->descripcion; 
}

/**
* Set descripcion
*
* @param string $strDescrip
*/
public function setDescripcion($strDescrip)
{
    $this->descripcion = $strDescrip;
}

/**
* Get correo
*
* @return string
*/		
     		
public function getCorreo()
{
	return $this->correo; 
}

/**
* Set correo
*
* @param string $strCorr
*/
public function setCorreo($strCorr)
{
    $this->correo = $strCorr;
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
* Get nombreAppmovil
*
* @return string
*/		
     		
public function getNombreAppMovil()
{
	return $this->nombreAppmovil; 
}

/**
* Set nombreAppmovil
*
* @param string $strNombreapp
*/
public function setNombreAppMovil($strNombreapp)
{
    $this->nombreAppmovil = $strNombreapp;
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
* Get bloqueado
*
* @return 
*/
public function getBloqueado()
{
    return $this->bloqueado;
}

/**
* Set bloqueado
*
* @param  $strBloqueado
*/
public function setBloqueado($strBloqueado)
{
    $this->bloqueado = $strBloqueado;
}
    
    
/**
* Get sistemaOperativo
*
* @return 
*/
public function getSistemaOperativo()
{
        return $this->sistemaOperativo;
}

/**
* Set sistemaOperativo
*
* @param  $strSistema
*/
public function setSistemaOperativo($strSistema)
{
    $this->sistemaOperativo = $strSistema;
}
    
/**
* Get tipoDispositivo
*
* @return 
*/
public function getTipoDispositivo()
{
    return $this->tipoDispositivo;
}

/**
* Set tipoDispositivo
*
* @param  $strTipodisp
*/
public function setTipoDispositivo($strTipodisp)
{
    $this->tipoDispositivo = $strTipodisp;
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
* @param string $strUsrUltModi
*/
public function setUsrUltMod($strUsrUltModi)
{
    $this->usrUltMod = $strUsrUltModi;
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
* @param  $strFeUltModi
*/
public function setFeUltMod($strFeUltModi)
{
    $this->feUltMod = $strFeUltModi;
}


/**
* Get tokenFCM
*
* @return 
*/

public function getTokenFCM()
{
    return $this->tokenFCM;
}

/**
* Set tokenFCM
*
* @param  $strTokenFCM
*/
public function setTokenFCM($strTokenFCM)
{
    $this->tokenFCM = $strTokenFCM;
}
    
}
