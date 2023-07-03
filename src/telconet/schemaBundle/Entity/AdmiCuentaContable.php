<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiBancoCtaContable
 *
 * @ORM\Table(name="ADMI_CUENTA_CONTABLE")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiCuentaContableRepository")
 */
class AdmiCuentaContable
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_CUENTA_CONTABLE", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_CUENTA_CONTABLE", allocationSize=1, initialValue=1)
*/		
		
private $id;	

/**
* @var string $noCia
*
* @ORM\Column(name="NO_CIA", type="string", nullable=true)
*/		
     		
private $noCia;


/**
* @var string $noCta
*
* @ORM\Column(name="NO_CTA", type="string", nullable=true)
*/		

private $noCta;

/**
* @var string $cuenta
*
* @ORM\Column(name="CUENTA", type="string", nullable=true)
*/		

private $cuenta;


/**
* @var string $tablaReferencial
*
* @ORM\Column(name="TABLA_REFERENCIAL", type="string", nullable=true)
*/		
     		
private $tablaReferencial;

/**
* @var string $campoReferencial
*
* @ORM\Column(name="CAMPO_REFERENCIAL", type="string", nullable=true)
*/		
     		
private $campoReferencial;

/**
* @var string $valorCampoReferencial
*
* @ORM\Column(name="VALOR_CAMPO_REFERENCIAL", type="string", nullable=true)
*/		
     		
private $valorCampoReferencial;

/**
* @var string $nombreObjetoNaf
*
* @ORM\Column(name="NOMBRE_OBJETO_NAF", type="string", nullable=true)
*/		
     		
private $nombreObjetoNaf;

/**
* @var AdmiTipoCuentaContable
*
* @ORM\ManyToOne(targetEntity="AdmiTipoCuentaContable")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TIPO_CUENTA_CONTABLE_ID", referencedColumnName="ID_TIPO_CUENTA_CONTABLE")
* })
*/
		 		
private $tipoCuentaContableId;

/**
* @var string $descripcion
*
* @ORM\Column(name="DESCRIPCION", type="string", nullable=true)
*/		
     		
private $descripcion;

/**
* @var string $empresaCod
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=true)
*/		
     		
private $empresaCod;


/**
* @var datetime $feIni
*
* @ORM\Column(name="FE_INI", type="datetime", nullable=false)
*/		
     		
private $feIni;

/**
* @var datetime $feFin
*
* @ORM\Column(name="FE_FIN", type="datetime", nullable=false)
*/		
     		
private $feFin;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
*/		
     		
private $feCreacion;


/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=true)
*/		
     		
private $usrCreacion;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;


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
* Get noCia
*
* @return string
*/
public function getNoCia()
{
    return $this->noCia;
}

/**
* Get noCta
*
* @return string
*/
public function getNoCta()
{
    return $this->noCta;
}

/**
* Get cuenta
*
* @return string
*/
public function getCuenta()
{
    return $this->cuenta;
}

/**
* Get tablaReferencial
*
* @return string
*/
public function getTablaReferencial()
{
    return $this->tablaReferencial;
}

/**
* Get campoReferencial
*
* @return string
*/
public function getCampoReferencial()
{
    return $this->campoReferencial;
}

/**
* Get valorCampoReferencial
*
* @return string
*/
public function getValorCampoReferencial()
{
    return $this->valorCampoReferencial;
}

/**
* Get nombreObjetoNaf
*
* @return string
*/
public function getNombreObjetoNaf()
{
    return $this->nombreObjetoNaf;
}

/**
* Get tipoCuentaContableId
*
* @return telconet\schemaBundle\Entity\AdmiTipoCuentaContable
*/
public function getTipoCuentaContableId()
{
    return $this->tipoCuentaContableId;
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
* Get empresaCod
*
* @return string
*/
public function getEmpresaCod()
{
    return $this->empresaCod;
}

/**
* Get feIni
*
* @return datetime
*/
public function getFeIni()
{
    return $this->feIni;
}

/**
* Get feFin
*
* @return datetime
*/
public function getFeFin()
{
    return $this->feFin;
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
* Get usrCreacion
*
* @return string
*/
public function getUsrCreacion()
{
    return $this->usrCreacion;
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
* Get estado
*
* @return string
*/
public function getEstado()
{
    return $this->estado;
}


/**
* Set noCia
*
* @param string $noCia
*/
public function setNoCia($noCia)
{
    $this->noCia = $noCia;
}

/**
* Set noCta
*
* @param string $noCta
*/
public function setNoCta($noCta)
{
    $this->noCta = $noCta;
}

/**
* Set cuenta
*
* @param string $cuenta
*/
public function setCuenta($cuenta)
{
    $this->cuenta = $cuenta;
}

/**
* Set tablaReferencial
*
* @param string $tablaReferencial
*/
public function setTablaReferencial($tablaReferencial)
{
    $this->tablaReferencial = $tablaReferencial;
}

/**
* Set campoReferencial
*
* @param string $campoReferencial
*/
public function setCampoReferencial($campoReferencial)
{
    $this->campoReferencial = $campoReferencial;
}

/**
* Set valorCampoReferencial
*
* @param string $valorCampoReferencial
*/
public function setValorCampoReferencial($valorCampoReferencial)
{
    $this->valorCampoReferencial = $valorCampoReferencial;
}

/**
* Set nombreObjetoNaf
*
* @param string $nombreObjetoNaf
*/
public function setNombreObjetoNaf($nombreObjetoNaf)
{
    $this->nombreObjetoNaf = $nombreObjetoNaf;
}

/**
* Set tipoCuentaContableId
*
* @param telconet\schemaBundle\Entity\AdmiTipoCuentaContable $tipoCuentaContableId
*/
public function setTipoCuentaContableId($tipoCuentaContableId)
{
    $this->tipoCuentaContableId = $tipoCuentaContableId;
}


/**
* Set descripcion
*
* @param string $descripcion
*/
public function setDescripcion($descripcion)
{
    $this->descripcion = $descripcion;
}

/**
* Set empresaCod
*
* @param string $empresaCod
*/
public function setEmpresaCod($empresaCod)
{
    $this->empresaCod = $empresaCod;
}

/**
* Set feIni
*
* @param datetime $feIni
*/
public function setFeIni( $feIni)
{
    $this->feIni = $feIni;
}

/**
* Set feFin
*
* @param datetime $feFin
*/
public function setFeFin( $feFin)
{
    $this->feFin = $feFin;
}

/**
* Set feCreacion
*
* @param datetime $feCreacion
*/
public function setFeCreacion( $feCreacion)
{
    $this->feCreacion = $feCreacion;
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
* Set ipCreacion
*
* @param string $ipCreacion
*/
public function setIpCreacion($ipCreacion)
{
    $this->ipCreacion = $ipCreacion;
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
