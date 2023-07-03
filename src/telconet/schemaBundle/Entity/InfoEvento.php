<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoEvento
 *
 * @ORM\Table(name="INFO_EVENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoEventoRepository")
 */
class InfoEvento
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_EVENTO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_EVENTO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $cuadrillaId
*
* @ORM\Column(name="CUADRILLA_ID", type="integer", nullable=false)
*/		
     		
private $cuadrillaId;

/**
* @var AdmiTipoEvento
*
* @ORM\ManyToOne(targetEntity="AdmiTipoEvento")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TIPO_EVENTO_ID", referencedColumnName="ID_TIPO_EVENTO")
* })
*/
    
private $tipoEventoId;


/**
* @var string $detalleId
*
* @ORM\Column(name="DETALLE_ID", type="integer", nullable=false)
*/		
     		
private $detalleId;

/**
* @var string $fechaInicio
*
* @ORM\Column(name="FECHA_INICIO", type="datetime", nullable=false)
*/		
     		
private $fechaInicio;

/**
* @var string $fechaFin
*
* @ORM\Column(name="FECHA_FIN", type="datetime", nullable=false)
*/		
     		
private $fechaFin;

/**
* @var string $valorTiempo
*
* @ORM\Column(name="VALOR_TIEMPO", type="integer", nullable=false)
*/		
     		
private $valorTiempo;

/**
* @var string $personaEmpresaRolId
*
* @ORM\Column(name="PERSONA_EMPRESA_ROL_ID", type="integer", nullable=false)
*/		
     		
private $personaEmpresaRolId;

/**
* @var string $observacion
*
* @ORM\Column(name="OBSERVACION", type="string", nullable=false)
*/		
     		
private $observacion;

/**
* @var string $publishId
*
* @ORM\Column(name="PUBLISH_ID", type="string", nullable=false)
*/		
     		
private $publishId;

/**
* @var string $latitud
*
* @ORM\Column(name="LATITUD", type="string", nullable=false)
*/		
     		
private $latitud;

/**
* @var string $longitud
*
* @ORM\Column(name="LONGITUD", type="string", nullable=false)
*/		
     		
private $longitud;

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
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
*/		
     		
private $feUltMod;

/**
* @var datetime $feUltMod
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;

/**
* @var datetime $ipUltMod
*
* @ORM\Column(name="IP_ULT_MOD", type="string", nullable=true)
*/		
     		
private $ipUltMod;

/**
 * @var varchar $version
 *
 * @ORM\Column(name="VERSION", type="string", nullable=true)
 */

private $version;

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}


/**
* Get cuadrillaId
*
* @return integer
*/		
     		
public function getCuadrillaId(){
	return $this->cuadrillaId; 
}

/**
* Set cuadrillaId
*
* @param integer $cuadrillaId
*/
public function setCuadrillaId($cuadrillaId)
{
    $this->cuadrillaId = $cuadrillaId;
}

/*
* Get AdmiTipoEvento
*
* @return telconet\schemaBundle\Entity\AdmiTipoEvento
*/    
         
public function getTipoEventoId(){
  return $this->tipoEventoId; 
}

/*
* Set AdmiTipoEvento
*
* @param telconet\schemaBundle\Entity\AdmiTipoEvento $tipoEventoId
*/
public function setTipoEventoId(\telconet\schemaBundle\Entity\AdmiTipoEvento $tipoEventoId)
{
        $this->tipoEventoId = $tipoEventoId;
}

/**
* Get detalleId
*
* @return integer
*/		
     		
public function getDetalleId(){
	return $this->detalleId;
}

/**
* Set detalleId
*
* @param integer $detalleId
*/
public function setDetalleId($detalleId)
{
    $this->detalleId = $detalleId;
}

/**
* Get fechaInicio
*
* @return datetime
*/		
     		
public function getFechaInicio(){
	return $this->fechaInicio;
}

/**
* Set fechaInicio
*
* @param integer $fechaInicio
*/
public function setFechaInicio($fechaInicio)
{
    $this->fechaInicio = $fechaInicio;
}

/**
* Get fechaFin
*
* @return datetime
*/		
     		
public function getFechaFin(){
	return $this->fechaFin;
}

/**
* Set fechaFin
*
* @param datetime $fechaFin
*/
public function setFechaFin($fechaFin)
{
    $this->fechaFin = $fechaFin;
}

/**
* Get valorTiempo
*
* @return integer
*/		
     		
public function getValorTiempo(){
	return $this->valorTiempo;
}

/**
* Set valorTiempo
*
* @param integer $valorTiempo
*/
public function setValorTiempo($valorTiempo)
{
    $this->valorTiempo = $valorTiempo;
}


/**
* Get personaEmpresaRolId
*
* @return integer
*/		
     		
public function getPersonaEmpresaRolId(){
	return $this->personaEmpresaRolId;
}

/**
* Set personaEmpresaRolId
*
* @param integer $personaEmpresaRolId
*/
public function setPersonaEmpresaRolId($personaEmpresaRolId)
{
    $this->personaEmpresaRolId = $personaEmpresaRolId;
}

/**
* Get accion
*
* @return string
*/		
     		
public function getObservacion(){
	return $this->observacion;
}

/**
* Set accion
*
* @param string $accion
*/
public function setObservacion($accion)
{
    $this->observacion = $accion;
}

/**
* Get estado
*
* @return string
*/		
     		
public function getEstado(){
	return $this->estado; 
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
* Get usrUltMod
*
* @return string
*/		
     		
public function getUsrUltMod(){
	return $this->usrUltMod; 
}

/**
* Set usrUltMod
*
* @param string $usrUltMod
*/
public function setUsrUltMod($usrUltMod)
{
    $this->usrUltMod = $usrUltMod;
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
* Get feUltMod
*
* @return datetime
*/		
     		
public function getFeUltMod(){
	return $this->feUltMod; 
}

/**
* Set feUltMod
*
* @param datetime $feUltMod
*/
public function setFeUltMod($feUltMod)
{
    $this->feUltMod = $feUltMod;
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
* Get ipUltMod
*
* @return string
*/		
     		
public function getIpUltMod(){
	return $this->ipUltMod; 
}

/**
* Set ipUltMod
*
* @param string $ipUltMod
*/
public function setIpUltMod($ipUltMod)
{
    $this->ipUltMod = $ipUltMod;
}

/**
 * Get $version
 * @return string $version
 */
public function getVersion()
{
    return $this->version;
}

/**
 * Set $version
 * @param string $version
 */
public function setVersion($version)
{
    $this->version = $version;
}

/**
 * Get $publishId
 * @return string $publishId
 */
public function getPublishId()
{
    return $this->publishId;
}

/**
 * Set $publishId
 * @param string $publishId
 */
public function setPublishId($publishId)
{
    $this->publishId = $publishId;
}


/*
 * Get $latitud
 * @return string $latitud
 */
public function getLatitud()
{
    return $this->latitud;
}

/*
 * Set $latitud
 * @param string $latitud
 */
public function setLatitud($latitud)
{
    $this->latitud = $latitud;
}

/*
 * Get $longitud
 * @return string $longitud
 */
public function getLongitud()
{
    return $this->longitud;
}

/*
 * Set $longitud
 * @param string $longitud
 */
public function setLongitud($longitud)
{
    $this->longitud = $longitud;
}


}