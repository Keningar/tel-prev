<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoCaso
 *
 * @ORM\Table(name="INFO_CASO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoCasoRepository")
 */
class InfoCaso
{


/**
* @var AdmiTipoCaso
*
* @ORM\ManyToOne(targetEntity="AdmiTipoCaso")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TIPO_CASO_ID", referencedColumnName="ID_TIPO_CASO")
* })
*/
	
private $tipoCasoId;

/**
* @var integer tipoNotificacionId
*
* @ORM\Column(name="FORMA_CONTACTO_ID", type="integer", nullable=false)
*/	
		
private $tipoNotificacionId;

/**
* @var AdmiNivelCriticidad
*
* @ORM\ManyToOne(targetEntity="AdmiNivelCriticidad")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="NIVEL_CRITICIDAD_ID", referencedColumnName="ID_NIVEL_CRITICIDAD")
* })
*/
		
private $nivelCriticidadId;

/**
* @var string $numeroCaso
*
* @ORM\Column(name="NUMERO_CASO", type="string", nullable=false)
*/		
     		
private $numeroCaso;

/**
* @var string $tituloIni
*
* @ORM\Column(name="TITULO_INI", type="string", nullable=true)
*/		
     		
private $tituloIni;

/**
* @var string $tituloFin
*
* @ORM\Column(name="TITULO_FIN", type="string", nullable=true)
*/		
     		
private $tituloFin;

/**
* @var integer $tituloFinHip
*
* @ORM\Column(name="TITULO_FIN_HIP", type="integer", nullable=true)
*/		
     		
private $tituloFinHip;

/**
* @var string $versionIni
*
* @ORM\Column(name="VERSION_INI", type="string", nullable=true)
*/		
     		
private $versionIni;

/**
* @var string $versionFin
*
* @ORM\Column(name="VERSION_FIN", type="string", nullable=true)
*/		
     		
private $versionFin;

/**
* @var datetime $feApertura
*
* @ORM\Column(name="FE_APERTURA", type="datetime", nullable=false)
*/		
     		
private $feApertura;

/**
* @var datetime $feCierre
*
* @ORM\Column(name="FE_CIERRE", type="datetime", nullable=false)
*/		
     		
private $feCierre;

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
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
*/		
     		
private $ipCreacion;

/**
* @var InfoEmpresaGrupo
*
* @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
*/
		
private $empresaCod;

/**
* @var integer $id
*
* @ORM\Column(name="ID_CASO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_CASO", allocationSize=1, initialValue=1)
*/		
		
private $id;	

/**
* @var string
*
* @ORM\Column(name="TIPO_AFECTACION", type="string", nullable=true)
*/
		
private $tipoAfectacion;

	
/**
* @var string
*
* @ORM\Column(name="TIPO_BACKBONE", type="string", nullable=true)
*/

private $tipoBackbone;

/**
* @var string
*
* @ORM\Column(name="ORIGEN", type="string", nullable=true)
*/

private $origen;

/**
* Get tipoCasoId
*
* @return telconet\schemaBundle\Entity\AdmiTipoCaso
*/		
     		
public function getTipoCasoId(){
	return $this->tipoCasoId; 
}

/**
* Set tipoCasoId
*
* @param telconet\schemaBundle\Entity\AdmiTipoCaso $tipoCasoId
*/
public function setTipoCasoId(\telconet\schemaBundle\Entity\AdmiTipoCaso $tipoCasoId)
{
        $this->tipoCasoId = $tipoCasoId;
}


/**
* Get tipoNotificacionId
*
* @return integer
*/		
     		
public function getTipoNotificacionId(){
	return $this->tipoNotificacionId; 
}

/**
* Set tipoNotificacionId
*
* @param integer $tipoNotificacionId
*/
public function setTipoNotificacionId($tipoNotificacionId)
{
        $this->tipoNotificacionId = $tipoNotificacionId;
}


/**
* Get nivelCriticidadId
*
* @return telconet\schemaBundle\Entity\AdmiNivelCriticidad
*/		
     		
public function getNivelCriticidadId(){
	return $this->nivelCriticidadId; 
}

/**
* Set nivelCriticidadId
*
* @param telconet\schemaBundle\Entity\AdmiNivelCriticidad $nivelCriticidadId
*/
public function setNivelCriticidadId(\telconet\schemaBundle\Entity\AdmiNivelCriticidad $nivelCriticidadId)
{
        $this->nivelCriticidadId = $nivelCriticidadId;
}


/**
* Get numeroCaso
*
* @return string
*/		
     		
public function getNumeroCaso(){
	return $this->numeroCaso; 
}

/**
* Set numeroCaso
*
* @param string $numeroCaso
*/
public function setNumeroCaso($numeroCaso)
{
        $this->numeroCaso = $numeroCaso;
}

/**
* Get tituloIni
*
* @return string
*/		
     		
public function getTituloIni(){
	return $this->tituloIni; 
}

/**
* Set tituloIni
*
* @param string $tituloIni
*/
public function setTituloIni($tituloIni)
{
        $this->tituloIni = $tituloIni;
}

/**
* Get tituloFin
*
* @return string
*/		
     		
public function getTituloFin(){
	return $this->tituloFin; 
}

/**
* Set tituloFin
*
* @param string $tituloFin
*/
public function setTituloFin($tituloFin)
{
        $this->tituloFin = $tituloFin;
}

/**
* Get tituloFinHip
*
* @return integer
*/		
     		
public function getTituloFinHip(){
	return $this->tituloFinHip; 
}

/**
* Set tituloFinHip
*
* @param integer $tituloFinHip
*/
public function setTituloFinHip($tituloFinHip)
{
        $this->tituloFinHip = $tituloFinHip;
}

/**
* Get versionIni
*
* @return string
*/		
     		
public function getVersionIni(){
	return $this->versionIni; 
}

/**
* Set versionIni
*
* @param string $versionIni
*/
public function setVersionIni($versionIni)
{
        $this->versionIni = $versionIni;
}

/**
* Get versionFin
*
* @return string
*/		
     		
public function getVersionFin(){
	return $this->versionFin; 
}

/**
* Set versionFin
*
* @param string $versionFin
*/
public function setVersionFin($versionFin)
{
        $this->versionFin = $versionFin;
}
/**
* Get feApertura
*
* @return datetime
*/		
     		
public function getFeApertura(){
	return $this->feApertura; 
}

/**
* Set feApertura
*
* @param datetime $feApertura
*/
public function setFeApertura($feApertura)
{
        $this->feApertura = $feApertura;
}


/**
* Get feCierre
*
* @return datetime
*/		
     		
public function getFeCierre(){
	return $this->feCierre; 
}

/**
* Set feCierre
*
* @param datetime $feCierre
*/
public function setFeCierre($feCierre)
{
        $this->feCierre = $feCierre;
}


/**
* Get usrCreacion
*
* @return string
*/		
     		
public function getUsrCreacion(){
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
     		
public function getFeCreacion(){
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
* Get ipCreacion
*
* @return string
*/		
     		
public function getIpCreacion(){
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
* Get empresaCod
*
* @return string
*/		
     		
public function getEmpresaCod(){
	return $this->empresaCod; 
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
* Get tipoAfectacion
*
* @return string
*/		
     		
public function getTipoAfectacion()
{
	return $this->tipoAfectacion; 
}

/**
* Set tipoAfectacion
*
* @param string $tipoAfectacion
*/
public function setTipoAfectacion($tipoAfectacion)
{
    $this->tipoAfectacion = $tipoAfectacion;
}

/**
* Get tipoBackbone
*
* @return string
*/

public function gettipoBackbone()
{
	return $this->tipoBackbone;
}

/**
* Set origen
*
* @param string $strOrigen
*/
public function setOrigen($strOrigen)
{
    $this->origen = $strOrigen;
}

/**
* Get origen
*
* @return string
*/

public function getOrigen()
{
	return $this->origen;
}

/**
* Set tipoAfectacion
*
* @param string $tipoBackbone
*/
public function settipoBackbone($tipoBackbone)
{
    $this->tipoBackbone = $tipoBackbone;
}

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}
}