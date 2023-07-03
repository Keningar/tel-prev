<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoLog
 *
 * @ORM\Table(name="INFO_LOG")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoLogRepository")
 */
class InfoLog
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_LOG", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_LOG", allocationSize=1, initialValue=1)
*/

private $id;

/**
* @var string $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
*/

private $empresaCod;

/**
* @var string $tipoLog
*
* @ORM\Column(name="TIPO_LOG", type="string", nullable=false)
*/

private $tipoLog;


/**
* @var integer $origenLog
*
* @ORM\Column(name="ORIGEN_LOG", type="string", nullable=true)
*/

private $origenLog;

/**
* @var integer $latitud
*
* @ORM\Column(name="LATITUD", type="string", nullable=true)
*/

private $latitud;

/**
* @var string $longitud
*
* @ORM\Column(name="LONGITUD", type="string", nullable=false)
*/

private $longitud;

/**
* @var string $aplicacion
*
* @ORM\Column(name="APLICACION", type="string", nullable=false)
*/

private $aplicacion;

/**
* @var string $clase
*
* @ORM\Column(name="CLASE", type="string", nullable=false)
*/

private $clase;

/**
* @var string $metodo
*
* @ORM\Column(name="METODO", type="string", nullable=false)
*/

private $metodo;

/**
* @var string $accion
*
* @ORM\Column(name="ACCION", type="string", nullable=false)
*/

private $accion;

/**
* @var string $mensaje
*
* @ORM\Column(name="MENSAJE", type="string", nullable=false)
*/

private $mensaje;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/

private $estado;

/**
* @var string $descripcion
*
* @ORM\Column(name="DESCRIPCION", type="string", nullable=false)
*/

private $descripcion;

/**
* @var string $imei
*
* @ORM\Column(name="IMEI", type="string", nullable=false)
*/

private $imei;

/**
* @var string $modelo
*
* @ORM\Column(name="MODELO", type="string", nullable=false)
*/

private $modelo;

/**
* @var string $versionApk
*
* @ORM\Column(name="VERSION_APK", type="string", nullable=false)
*/

private $versionApk;

/**
* @var string $versionSo
*
* @ORM\Column(name="VERSION_SO", type="string", nullable=false)
*/

private $versionSo;

/**
* @var string $tipoConexion
*
* @ORM\Column(name="TIPO_CONEXION", type="string", nullable=false)
*/

private $tipoConexion;

/**
* @var string $intensidadSenal
*
* @ORM\Column(name="INTENSIDAD_SENAL", type="string", nullable=false)
*/

private $intensidadSenal;

/**
* @var string $parametroEntrada
*
* @ORM\Column(name="PARAMETRO_ENTRADA", type="string", nullable=false)
*/

private $parametroEntrada;

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
* Get tipoLog
*
* @return string
*/

public function getTipoLog()
{
	return $this->tipoLog; 
}

/**
* Set tipoLog
*
* @param string $strTipoLog
*/
public function setTipoLog($strTipoLog)
{
        $this->tipoLog = $strTipoLog;
}


/**
* Get origenLog
*
* @return string
*/

public function getOrigenLog()
{
	return $this->origenLog; 
}

/**
* Set origenLog
*
* @param string $strOrigenLog
*/
public function setOrigenLog($strOrigenLog)
{
        $this->origenLog = $strOrigenLog;
}


/**
* Get latitud
*
* @return string
*/

public function getLatitud()
{
	return $this->latitud; 
}

/**
* Set latitud
*
* @param string $strLatitud
*/
public function setLatitud($strLatitud)
{
        $this->latitud = $strLatitud;
}

/**
* Get longitud
*
* @return string
*/

public function getLongitud()
{
	return $this->longitud; 
}

/**
* Set longitud
*
* @param string $strLongitud
*/
public function setLongitud($strLongitud)
{
        $this->longitud = $strLongitud;
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
* Get clase
*
* @return string
*/

public function getClase()
{
	return $this->clase; 
}

/**
* Set clase
*
* @param string $strClase
*/
public function setClase($strClase)
{
        $this->clase = $strClase;
}

/**
* Get metodo
*
* @return string
*/

public function getMetodo()
{
    return $this->metodo; 
}

/**
* Set metodo
*
* @param string $strMetodo
*/

public function setMetodo($strMetodo)
{
    $this->metodo = $strMetodo;
}

/**
* Get accion
*
* @return string
*/

public function getAccion()
{
    return $this->accion; 
}

/**
* Set accion
*
* @param string $strAccion
*/

public function setAccion($strAccion)
{
    $this->accion = $strAccion;
}

/**
* Get mensaje
*
* @return string
*/

public function getMensaje()
{
    return $this->mensaje; 
}

/**
* Set Mensaje
*
* @param string $strMensaje
*/

public function setMensaje($strMensaje)
{
    $this->mensaje = $strMensaje;
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
* @param string $strDescripcion
*/

public function setDescripcion($strDescripcion)
{
    $this->descripcion = $strDescripcion;
}

/**
* Get imei
*
* @return string
*/

public function getImei()
{
    return $this->imei; 
}

/**
* Set imei
*
* @param string $strImei
*/

public function setImei($strImei)
{
    $this->imei = $strImei;
}

/**
* Get modelo
*
* @return string
*/

public function getModelo()
{
    return $this->modelo; 
}

/**
* Set modelo
*
* @param string $strModelo
*/

public function setModelo($strModelo)
{
    $this->modelo = $strModelo;
}

/**
* Get versionApk
*
* @return string
*/

public function getVersionApk()
{
    return $this->versionApk;
}

/**
* Set versionApk
*
* @param string $strVersionApk
*/

public function setVersionApk($strVersionApk)
{
    $this->versionApk = $strVersionApk;
}

/**
* Get versionSo
*
* @return string
*/

public function getVersionSo()
{
    return $this->versionSo; 
}

/**
* Set versionSo
*
* @param string $strVersionSo
*/

public function setVersionSo($strVersionSo)
{
    $this->versionSo = $strVersionSo;
}

/**
* Get tipoConexion
*
* @return string
*/

public function getTipoConexion()
{
    return $this->tipoConexion; 
}

/**
* Set tipoConexion
*
* @param string $strTipoConexion
*/

public function setTipoConexion($strTipoConexion)
{
    $this->tipoConexion = $strTipoConexion;
}

/**
* Get intensidadSenal
*
* @return string
*/

public function getIntensidadSenal()
{
    return $this->intensidadSenal; 
}

/**
* Set intensidadSenal
*
* @param string $strIntensidadSenal
*/

public function setIntensidadSenal($strIntensidadSenal)
{
    $this->intensidadSenal = $strIntensidadSenal;
}

/**
* Get parametroEntrada
*
* @return string
*/

public function getParametroEntrada()
{
    return $this->parametroEntrada; 
}

/**
* Set parametroEntrada
*
* @param string $strParametroEntrada
*/

public function setParametroEntrada($strParametroEntrada)
{
    $this->parametroEntrada = $strParametroEntrada;
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
* @param datetime $arrayFeCreacion
*/
public function setFeCreacion($arrayFeCreacion)
{
        $this->feCreacion = $arrayFeCreacion;
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
* @param datetime $arrayFeUltMod
*/
public function setFeUltMod($arrayFeUltMod)
{
        $this->feUltMod = $arrayFeUltMod;
}

}
