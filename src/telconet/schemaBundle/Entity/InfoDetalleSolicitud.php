<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDetalleSolicitud
 *
 * @ORM\Table(name="INFO_DETALLE_SOLICITUD")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDetalleSolicitudRepository")
 */
class InfoDetalleSolicitud
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_DETALLE_SOLICITUD", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DETALLE_SOLICITUD", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var InfoServicio
*
* @ORM\ManyToOne(targetEntity="InfoServicio")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="SERVICIO_ID", referencedColumnName="ID_SERVICIO")
* })
*/
		
private $servicioId;

/**
* @var AdmiTipoSolicitud
*
* @ORM\ManyToOne(targetEntity="AdmiTipoSolicitud")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="TIPO_SOLICITUD_ID", referencedColumnName="ID_TIPO_SOLICITUD")
* })
*/
		
private $tipoSolicitudId;

/**
* @var integer $motivoId
*
* @ORM\Column(name="MOTIVO_ID", type="integer", nullable=true)
*/		
     		
private $motivoId;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=true)
*/		
     		
private $usrCreacion;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var string $usrRechazo
*
* @ORM\Column(name="USR_RECHAZO", type="string", nullable=true)
*/		
     		
private $usrRechazo;

/**
* @var datetime $feRechazo
* @ORM\Column(name="FE_RECHAZO", type="datetime", nullable=true)
*/		
     		
private $feRechazo;

/**
* @var float $precioDescuento
*
* @ORM\Column(name="PRECIO_DESCUENTO", type="float", nullable=true)
*/		
     		
private $precioDescuento;

/**
* @var float $porcentajeDescuento
*
* @ORM\Column(name="PORCENTAJE_DESCUENTO", type="float", nullable=true)
*/		
     		
private $porcentajeDescuento;

/**
* @var string $tipoDocumento
*
* @ORM\Column(name="TIPO_DOCUMENTO", type="string", nullable=true)
*/		
     		
private $tipoDocumento;


/**
* @var string $observacion
*
* @ORM\Column(name="OBSERVACION", type="string", nullable=true)
*/		
     		
private $observacion;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var datetime $feEjecucion
*
* @ORM\Column(name="FE_EJECUCION", type="datetime", nullable=true)
*/		
     		
private $feEjecucion;


/**
* @var integer $elementoId
*
* @ORM\Column(name="ELEMENTO_ID", type="string", nullable=true)
*/		
     		
private $elementoId;

/**
* Get id
*
* @return integer
*/		
   		
public function getId(){
	return $this->id; 
}

/**
* Get servicioId
*
* @return telconet\schemaBundle\Entity\InfoServicio
*/		
     		
public function getServicioId(){
	return $this->servicioId; 
}

/**
* Set servicioId
*
* @param telconet\schemaBundle\Entity\InfoServicio $servicioId
*/
public function setServicioId(\telconet\schemaBundle\Entity\InfoServicio $servicioId)
{
        $this->servicioId = $servicioId;
}


/**
* Get tipoSolicitudId
*
* @return \telconet\schemaBundle\Entity\AdmiTipoSolicitud
*/		
     		
public function getTipoSolicitudId(){
	return $this->tipoSolicitudId; 
}

/**
* Set tipoSolicitudId
*
* @param \telconet\schemaBundle\Entity\AdmiTipoSolicitud $tipoSolicitudId
*/
public function setTipoSolicitudId(\telconet\schemaBundle\Entity\AdmiTipoSolicitud $tipoSolicitudId)
{
        $this->tipoSolicitudId = $tipoSolicitudId;
}


/**
* Get motivoId
*
* @return integer
*/		
     		
public function getMotivoId(){
	return $this->motivoId; 
}

/**
* Set motivoId
*
* @param integer $motivoId
*/
public function setMotivoId($motivoId)
{
        $this->motivoId = $motivoId;
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
* Get usrRechazo
*
* @return string
*/		
     		
public function getUsrRechazo(){
	return $this->usrRechazo; 
}

/**
* Set usrRechazo
*
* @param string $usrRechazo
*/
public function setUsrRechazo($usrRechazo)
{
        $this->usrRechazo = $usrRechazo;
}

/**
* Get feRechazo
*
* @return datetime
*/		
     		
public function getFeRechazo(){
	return $this->feRechazo; 
}

/**
* Set feRechazo
*
* @param datetime $feRechazo
*/
public function setFeRechazo($feRechazo)
{
        $this->feRechazo = $feRechazo;
}

/**
* Get precioDescuento
*
* @return float
*/		
     		
public function getPrecioDescuento(){
	return $this->precioDescuento; 
}

/**
* Set precioDescuento
*
* @param float $precioDescuento
*/
public function setPrecioDescuento($precioDescuento)
{
        $this->precioDescuento = $precioDescuento;
}

/**
* Get porcentajeDescuento
*
* @return float
*/		
     		
public function getPorcentajeDescuento(){
	return $this->porcentajeDescuento; 
}

/**
* Set porcentajeDescuento
*
* @param float $porcentajeDescuento
*/
public function setPorcentajeDescuento($porcentajeDescuento)
{
        $this->porcentajeDescuento = $porcentajeDescuento;
}


/**
* Get tipoDocumento
*
* @return string
*/		
     		
public function getTipoDocumento(){
	return $this->tipoDocumento; 
}

/**
* Set tipoDocumento
*
* @param string $tipoDocumento
*/
public function setTipoDocumento($tipoDocumento)
{
        $this->tipoDocumento = $tipoDocumento;
}


/**
* Get observacion
*
* @return string
*/		
     		
public function getObservacion(){
	return $this->observacion; 
}

/**
* Set observacion
*
* @param string $observacion
*/
public function setObservacion($observacion)
{
        $this->observacion = $observacion;
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
* Get feEjecucion
*
* @return datetime
*/		
     		
public function getFeEjecucion(){
	return $this->feEjecucion; 
}

/**
* Set feEjecucion
*
* @param datetime $feEjecucion
*/
public function setFeEjecucion($feEjecucion)
{
        $this->feEjecucion = $feEjecucion;
}



/**
* Get elementoId
*
* @return integer
*/		
     		
public function getElementoId(){
	return $this->elementoId; 
}

/**
* Set elementoId
*
* @param integer $elementoId
*/
public function setElementoId($elementoId)
{
        $this->elementoId = $elementoId;
}





}
