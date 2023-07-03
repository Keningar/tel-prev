<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDetalle
 *
 * @ORM\Table(name="INFO_DETALLE")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDetalleRepository")
 */
class InfoDetalle
{


/**
* @var text $observacion
*
* @ORM\Column(name="OBSERVACION", type="text", nullable=true)
*/		
     		
private $observacion;

/**
* @var date $feSolicitada
*
* @ORM\Column(name="FE_SOLICITADA", type="datetime", nullable=true)
*/		
     		
private $feSolicitada;

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
* @var string $esSolucion
*
* @ORM\Column(name="ES_SOLUCION", type="string", nullable=true)
*/		
     		
private $esSolucion;

/**
* @var integer $id
*
* @ORM\Column(name="ID_DETALLE", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DETALLE", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var AdmiTarea
*
* @ORM\ManyToOne(targetEntity="AdmiTarea")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TAREA_ID", referencedColumnName="ID_TAREA")
* })
*/
		
private $tareaId;

/**
* @var float $longitud
*
* @ORM\Column(name="LONGITUD", type="float", nullable=true)
*/		
     		
private $longitud;

/**
* @var float $latitud
*
* @ORM\Column(name="LATITUD", type="float", nullable=true)
*/		
     		
private $latitud;

/**
* @var integer $pesoPresupuestado
*
* @ORM\Column(name="PESO_PRESUPUESTADO", type="integer", nullable=false)
*/		
     		
private $pesoPresupuestado;

/**
* @var string $tipoZona
*
* @ORM\Column(name="TIPO_ZONA", type="string", nullable=true)
*/		
     		
private $tipoZona;

/**
* @var integer $pesoReal
*
* @ORM\Column(name="PESO_REAL", type="integer", nullable=true)
*/		
     		
private $pesoReal;

/**
* @var float $valorPresupuestado
*
* @ORM\Column(name="VALOR_PRESUPUESTADO", type="float", nullable=false)
*/		
     		
private $valorPresupuestado;

/**
* @var float $valorFacturado
*
* @ORM\Column(name="VALOR_FACTURADO", type="float", nullable=true)
*/		
     		
private $valorFacturado;

/**
* @var float $valorNoFacturado
*
* @ORM\Column(name="VALOR_NO_FACTURADO", type="float", nullable=true)
*/		
     		
private $valorNoFacturado;

/**
* @var integer $detalleSolicitudId
*
* @ORM\Column(name="DETALLE_SOLICITUD_ID", type="integer", nullable=true)
*/		
     		
private $detalleSolicitudId;

/**
* @var integer $detalleHipotesisId
*
* @ORM\Column(name="DETALLE_HIPOTESIS_ID", type="integer", nullable=true)
*/		
     		
private $detalleHipotesisId;

/**
* @var integer $detalleIdRelacionado
*
* @ORM\Column(name="DETALLE_ID_RELACIONADO", type="integer", nullable=true)
*/

private $detalleIdRelacionado;


/**
* @var InfoProgresoTarea
*
* @ORM\ManyToOne(targetEntity="InfoProgresoTarea")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="PROGRESO_TAREA_ID", referencedColumnName="ID_PROGRESO_TAREA")
* })
*/
    
private $progresoTareaId;


/**
* Get observacion
*
* @return 
*/		
     		
public function getObservacion(){
	return $this->observacion; 
}

/**
* Set observacion
*
* @param  $observacion
*/
public function setObservacion($observacion)
{
        $this->observacion = $observacion;
}


/**
* Get feSolicitada
*
* @return 
*/		
     		
public function getFeSolicitada(){
	return $this->feSolicitada; 
}

/**
* Set feSolicitada
*
* @param  $feSolicitada
*/
public function setFeSolicitada($feSolicitada)
{
        $this->feSolicitada = $feSolicitada;
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
* Get esSolucion
*
* @return string
*/		
     		
public function getEsSolucion(){
	return $this->esSolucion; 
}

/**
* Set esSolucion
*
* @param string $esSolucion
*/
public function setEsSolucion($esSolucion)
{
        $this->esSolucion = $esSolucion;
}

/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}


/**
* Get tareaId
*
* @return telconet\schemaBundle\Entity\AdmiTarea
*/		
     		
public function getTareaId(){
	return $this->tareaId; 
}

/**
* Set tareaId
*
* @param telconet\schemaBundle\Entity\AdmiTarea $tareaId
*/
public function setTareaId(\telconet\schemaBundle\Entity\AdmiTarea $tareaId)
{
        $this->tareaId = $tareaId;
}


/**
* Get longitud
*
* @return 
*/		
     		
public function getLongitud(){
	return $this->longitud; 
}

/**
* Set longitud
*
* @param  $longitud
*/
public function setLongitud($longitud)
{
        $this->longitud = $longitud;
}


/**
* Get latitud
*
* @return 
*/		
     		
public function getLatitud(){
	return $this->latitud; 
}

/**
* Set latitud
*
* @param  $latitud
*/
public function setLatitud($latitud)
{
        $this->latitud = $latitud;
}


/**
* Get pesoPresupuestado
*
* @return integer
*/		
     		
public function getPesoPresupuestado(){
	return $this->pesoPresupuestado; 
}

/**
* Set pesoPresupuestado
*
* @param integer $pesoPresupuestado
*/
public function setPesoPresupuestado($pesoPresupuestado)
{
        $this->pesoPresupuestado = $pesoPresupuestado;
}


/**
* Get tipoZona
*
* @return string
*/		
     		
public function getTipoZona(){
	return $this->tipoZona; 
}

/**
* Set tipoZona
*
* @param string $tipoZona
*/
public function setTipoZona($tipoZona)
{
        $this->tipoZona = $tipoZona;
}


/**
* Get pesoReal
*
* @return integer
*/		
     		
public function getPesoReal(){
	return $this->pesoReal; 
}

/**
* Set pesoReal
*
* @param integer $pesoReal
*/
public function setPesoReal($pesoReal)
{
        $this->pesoReal = $pesoReal;
}


/**
* Get valorPresupuestado
*
* @return 
*/		
     		
public function getValorPresupuestado(){
	return $this->valorPresupuestado; 
}

/**
* Set valorPresupuestado
*
* @param  $valorPresupuestado
*/
public function setValorPresupuestado($valorPresupuestado)
{
        $this->valorPresupuestado = $valorPresupuestado;
}


/**
* Get valorFacturado
*
* @return 
*/		
     		
public function getValorFacturado(){
	return $this->valorFacturado; 
}

/**
* Set valorFacturado
*
* @param  $valorFacturado
*/
public function setValorFacturado($valorFacturado)
{
        $this->valorFacturado = $valorFacturado;
}


/**
* Get valorNoFacturado
*
* @return 
*/		
     		
public function getValorNoFacturado(){
	return $this->valorNoFacturado; 
}

/**
* Set valorNoFacturado
*
* @param  $valorNoFacturado
*/
public function setValorNoFacturado($valorNoFacturado)
{
        $this->valorNoFacturado = $valorNoFacturado;
}


/**
* Get detalleSolicitudId
*
* @return integer
*/		
     		
public function getDetalleSolicitudId(){
	return $this->detalleSolicitudId; 
}

/**
* Set detalleSolicitudId
*
* @param integer $detalleSolicitudId
*/
public function setDetalleSolicitudId($detalleSolicitudId)
{
        $this->detalleSolicitudId = $detalleSolicitudId;
}


/**
* Get detalleHipotesisId
*
* @return integer
*/		
     		
public function getDetalleHipotesisId(){
	return $this->detalleHipotesisId;
}

/**
* Set detalleHipotesisId
*
* @param integer $detalleHipotesisId
*/
public function setDetalleHipotesisId($detalleHipotesisId)
{
        $this->detalleHipotesisId = $detalleHipotesisId;
}

/**
* Get detalleIdRelacionado
*
* @return integer
*/

public function getDetalleIdRelacionado(){
	return $this->detalleIdRelacionado;
}

/**
* Set detalleHipotesisId $detalleIdRelacionado
*
* @param integer $detalleIdRelacionado
*/
public function setDetalleIdRelacionado($detalleIdRelacionado)
{
        $this->detalleIdRelacionado = $detalleIdRelacionado;
}

/*
* Get progresoTareaId
*
* @return telconet\schemaBundle\Entity\InfoProgresoTarea
*/    
         
public function getProgresoTareaId(){
  return $this->progresoTareaId; 
}

/*
* Set progresoTareaId
*
* @param telconet\schemaBundle\Entity\InfoProgresoTarea $progresoTareaId
*/
public function setProgresoTareaId(\telconet\schemaBundle\Entity\InfoProgresoTarea $progresoTareaId)
{
        $this->progresoTareaId = $progresoTareaId;
}


}