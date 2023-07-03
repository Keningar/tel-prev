<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoElementoTrazabilidad
 *
 * @ORM\Table(name="INFO_ELEMENTO_TRAZABILIDAD")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoElementoTrazabilidadRepository")
 */
class InfoElementoTrazabilidad
{

/**
* @var integer $id
*
* @ORM\Column(name="ID_TRAZABILIDAD", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_ELEMENTO_TRAZABILIDAD", allocationSize=1, initialValue=1)
*/
private $id;

/**
* @var string $numeroSerie
*
* @ORM\Column(name="NUMERO_SERIE", type="string", nullable=false)
*/
private $numeroSerie;

/**
* @var string $codEmpresa
*
* @ORM\Column(name="COD_EMPRESA", type="string", nullable=false)
*/
private $codEmpresa;

/**
* @var string $estadoTelcos
*
* @ORM\Column(name="ESTADO_TELCOS", type="string", nullable=false)
*/
private $estadoTelcos;

/**
* @var string $estadoNaf
*
* @ORM\Column(name="ESTADO_NAF", type="string", nullable=false)
*/
private $estadoNaf;

/**
* @var string $estadoActivo
*
* @ORM\Column(name="ESTADO_ACTIVO", type="string", nullable=false)
*/
private $estadoActivo;

/**
* @var string $ubicacion
*
* @ORM\Column(name="UBICACION", type="string", nullable=false)
*/
private $ubicacion;

/**
* @var string $login
*
* @ORM\Column(name="LOGIN", type="string", nullable=false)
*/
private $login;

/**
* @var string $responsable
*
* @ORM\Column(name="RESPONSABLE", type="string", nullable=false)
*/
private $responsable;

/**
* @var string $oficinaId
*
* @ORM\Column(name="OFICINA_ID", type="integer", nullable=false)
*/
private $oficinaId;

/**
* @var string $observacion
*
* @ORM\Column(name="OBSERVACION", type="string", nullable=false)
*/
private $observacion;

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
* @var datetime $feCreacionNaf
*
* @ORM\Column(name="FE_CREACION_NAF", type="datetime", nullable=false)
*/
private $feCreacionNaf;


/**
* @var string $transaccion
*
* @ORM\Column(name="TRANSACCION", type="string", nullable=false)
*/
private $transaccion;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
*/
private $ipCreacion;


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
* Get numeroSerie
*
* @return numeroSerie
*/
public function getNumeroSerie()
{
    return $this->numeroSerie;
}

/**
* Set numeroSerie
*
* @param string
*/
public function setNumeroSerie($numeroSerie)
{
    $this->numeroSerie = $numeroSerie;
}


/**
* Get codEmpresa
*
* @return string
*/
public function getCodEmpresa()
{
    return $this->codEmpresa;
}

/**
* Set codEmpresa
*
* @param string
*/
public function setCodEmpresa($codEmpresa)
{
    $this->codEmpresa = $codEmpresa;
}


/**
* Get estadoTelcos
*
* @return string
*/
public function getEstadoTelcos()
{
    return $this->estadoTelcos;
}

/**
* Set estadoTelcos
*
* @param $estadoTelcos
*/
public function setEstadoTelcos($estadoTelcos)
{
    $this->estadoTelcos = $estadoTelcos;
}

/**
* Get estadoNaf
*
* @return string
*/
public function getEstadoNaf()
{
    return $this->estadoNaf;
}

/**
* Set estadoNaf
*
* @param $estadoNaf
*/
public function setEstadoNaf($estadoNaf)
{
    $this->estadoNaf = $estadoNaf;
}

/**
* Get estadoActivo
*
* @return string
*/
public function getEstadoActivo()
{
    return $this->estadoActivo;
}

/**
* Set estadoActivo
*
* @param $estadoActivo
*/
public function setEstadoActivo($estadoActivo)
{
    $this->estadoActivo = $estadoActivo;
}

/**
* Get ubicacion
*
* @return string
*/
public function getUbicacion()
{
    return $this->ubicacion;
}

/**
* Set ubicacion
*
* @param $ubicacion
*/
public function setUbicacion($ubicacion)
{
    $this->ubicacion = $ubicacion;
}

/**
* Get login
*
* @return string
*/
public function getLogin()
{
    return $this->login;
}

/**
* Set login
*
* @param $login
*/
public function setLogin($login)
{
    $this->login = $login;
}


/**
* Get responsable
*
* @return string
*/
public function getResponsable()
{
    return $this->responsable;
}

/**
* Set responsable
*
* @param $responsable
*/
public function setResponsable($responsable)
{
    $this->responsable = $responsable;
}


/**
* Get oficinaId
*
* @return integer
*/
public function getOficinaId()
{
    return $this->oficinaId;
}

/**
* Set oficinaId
*
* @param $oficinaId
*/
public function setOficinaId($oficinaId)
{
    $this->oficinaId = $oficinaId;
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
* @param $observacion
*/
public function setObservacion($observacion)
{
    $this->observacion = $observacion;
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
* Get feCreacionNaf
*
* @return datetime
*/
public function getFeCreacionNaf()
{
    return $this->feCreacionNaf;
}

/**
* Set feCreacionNaf
*
* @param datetime $feCreacionNaf
*/
public function setFeCreacionNaf($feCreacionNaf)
{
    $this->feCreacionNaf = $feCreacionNaf;
}

/**
* Get transaccion
*
* @return string
*/

public function getTransaccion()
{
    return $this->transaccion;
}

/**
* Set transaccion
*
* @param string $transaccion
*/
public function setTransaccion($transaccion)
{
    $this->transaccion = $transaccion;
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

}