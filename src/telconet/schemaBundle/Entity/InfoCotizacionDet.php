<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoCotizacionDet
 *
 * @ORM\Table(name="INFO_COTIZACION_DET")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoCotizacionDetRepository")
 */
class InfoCotizacionDet
{


/**
* @var datetime $fechaHoraSolicPlanifi
*
* @ORM\Column(name="FECHA_HORA_SOLIC_PLANIFI", type="datetime", nullable=true)
*/

private $fechaHoraSolicPlanifi;

/**
* @var integer $prospectoId
*
* @ORM\Column(name="PROSPECTO_ID", type="integer", nullable=false)
*/

private $prospectoId;

/**
* @var integer $id
*
* @ORM\Column(name="ID_COTIZACION_DET", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_COTIZACION_DET", allocationSize=1, initialValue=1)
*/

private $id;

/**
* @var InfoCotizacionCab
*
* @ORM\ManyToOne(targetEntity="InfoCotizacionCab")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="COTIZACION_ID", referencedColumnName="ID_COTIZACION")
* })
*/

private $cotizacionId;

/**
* @var integer $planId
*
* @ORM\Column(name="PLAN_ID", type="integer", nullable=false)
*/

private $planId;

/**
* @var string $esVenta
*
* @ORM\Column(name="ES_VENTA", type="string", nullable=false)
*/

private $esVenta;

/**
* @var integer $cantidad
*
* @ORM\Column(name="CANTIDAD", type="integer", nullable=false)
*/

private $cantidad;

/**
* @var integer $precioVenta
*
* @ORM\Column(name="PRECIO_VENTA", type="decimal", nullable=false)
*/

private $precioVenta;

/**
* @var integer $costo
*
* @ORM\Column(name="COSTO", type="integer", nullable=false)
*/

private $costo;

/**
* @var integer $porcentajeDescuento
*
* @ORM\Column(name="PORCENTAJE_DESCUENTO", type="integer", nullable=true)
*/

private $porcentajeDescuento;

/**
* @var integer $valorDescuento
*
* @ORM\Column(name="VALOR_DESCUENTO", type="integer", nullable=false)
*/

private $valorDescuento;

/**
* @var integer $diasGracia
*
* @ORM\Column(name="DIAS_GRACIA", type="integer", nullable=false)
*/

private $diasGracia;

/**
* @var integer $frecuenciaProducto
*
* @ORM\Column(name="FRECUENCIA_PRODUCTO", type="integer", nullable=false)
*/

private $frecuenciaProducto;

/**
* @var integer $mesesRestantes
*
* @ORM\Column(name="MESES_RESTANTES", type="integer", nullable=true)
*/

private $mesesRestantes;

/**
* @var string $descripcionPresentaFactura
*
* @ORM\Column(name="DESCRIPCION_PRESENTA_FACTURA", type="string", nullable=false)
*/

private $descripcionPresentaFactura;

/**
* @var string $tieneSolicitudDescuento
*
* @ORM\Column(name="TIENE_SOLICITUD_DESCUENTO", type="string", nullable=false)
*/

private $tieneSolicitudDescuento;

/**
* @var string $tieneSolicitudCambioDoc
*
* @ORM\Column(name="TIENE_SOLICITUD_CAMBIO_DOC", type="string", nullable=false)
*/

private $tieneSolicitudCambioDoc;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/

private $estado;

/**
* @var string $bwSubida
*
* @ORM\Column(name="BW_SUBIDA", type="string", nullable=true)
*/

private $bwSubida;

/**
* @var string $bwBajada
*
* @ORM\Column(name="BW_BAJADA", type="string", nullable=true)
*/

private $bwBajada;

/**
* @var integer $empresaId
*
* @ORM\Column(name="EMPRESA_ID", type="integer", nullable=false)
*/

private $empresaId;

/**
* @var integer $productoId
*
* @ORM\Column(name="PRODUCTO_ID", type="integer", nullable=false)
*/

private $productoId;

/**
* @var integer $porcentajeIva
*
* @ORM\Column(name="PORCENTAJE_IVA", type="integer", nullable=true)
*/

private $porcentajeIva;

/**
* Get fechaHoraSolicPlanifi
*
* @return array
*/

public function getFechaHoraSolicPlanifi()
{
	return $this->fechaHoraSolicPlanifi;
}

/**
* Set fechaHoraSolicPlanifi
*
* @param  $arrayFechaHoraSolicPlanifi
*/
public function setFechaHoraSolicPlanifi($arrayFechaHoraSolicPlanifi)
{
        $this->fechaHoraSolicPlanifi = $arrayFechaHoraSolicPlanifi;
}


/**
* Get prospectoId
*
* @return integer
*/

public function getProspectoId()
{
	return $this->prospectoId; 
}

/**
* Set prospectoId
*
* @param integer $prospectoId
*/
public function setProspectoId($prospectoId)
{
        $this->prospectoId = $prospectoId;
}


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
* Get cotizacionId
*
* @return telconet\schemaBundle\Entity\InfoCotizacionCab
*/

public function getCotizacionId()
{
	return $this->cotizacionId; 
}

/**
* Set cotizacionId
*
* @param telconet\schemaBundle\Entity\InfoCotizacionCab $entityCotizacionId
*/
public function setCotizacionId(\telconet\schemaBundle\Entity\InfoCotizacionCab $entityCotizacionId)
{
        $this->cotizacionId = $entityCotizacionId;
}


/**
* Get planId
*
* @return integer
*/

public function getPlanId()
{
	return $this->planId; 
}

/**
* Set planId
*
* @param integer $intPlanId
*/
public function setPlanId($intPlanId)
{
        $this->planId = $intPlanId;
}


/**
* Get esVenta
*
* @return string
*/

public function getEsVenta()
{
	return $this->esVenta; 
}

/**
* Set esVenta
*
* @param string $strEsVenta
*/
public function setEsVenta($strEsVenta)
{
        $this->esVenta = $strEsVenta;
}


/**
* Get cantidad
*
* @return integer
*/

public function getCantidad()
{
	return $this->cantidad; 
}

/**
* Set cantidad
*
* @param integer $intCantidad
*/
public function setCantidad($intCantidad)
{
        $this->cantidad = $intCantidad;
}


/**
* Get precioVenta
*
* @return integer
*/

public function getPrecioVenta()
{
	return $this->precioVenta;
}

/**
* Set precioVenta
*
* @param integer $intPrecioVenta
*/
public function setPrecioVenta($intPrecioVenta)
{
        $this->precioVenta = $intPrecioVenta;
}


/**
* Get costo
*
* @return integer
*/

public function getCosto()
{
	return $this->costo; 
}

/**
* Set costo
*
* @param integer $intCosto
*/
public function setCosto($intCosto)
{
        $this->costo = $intCosto;
}


/**
* Get porcentajeDescuento
*
* @return integer
*/

public function getPorcentajeDescuento()
{
	return $this->porcentajeDescuento; 
}

/**
* Set porcentajeDescuento
*
* @param integer $intPorcentajeDescuento
*/
public function setPorcentajeDescuento($intPorcentajeDescuento)
{
        $this->porcentajeDescuento = $intPorcentajeDescuento;
}


/**
* Get valorDescuento
*
* @return integer
*/

public function getValorDescuento()
{
	return $this->valorDescuento; 
}

/**
* Set valorDescuento
*
* @param integer $intValorDescuento
*/
public function setValorDescuento($intValorDescuento)
{
        $this->valorDescuento = $intValorDescuento;
}


/**
* Get diasGracia
*
* @return integer
*/

public function getDiasGracia()
{
	return $this->diasGracia; 
}

/**
* Set diasGracia
*
* @param integer $intDiasGracia
*/
public function setDiasGracia($intDiasGracia)
{
        $this->diasGracia = $intDiasGracia;
}


/**
* Get frecuenciaProducto
*
* @return integer
*/

public function getFrecuenciaProducto()
{
	return $this->frecuenciaProducto;
}

/**
* Set frecuenciaProducto
*
* @param integer $intFrecuenciaProducto
*/
public function setFrecuenciaProducto($intFrecuenciaProducto)
{
        $this->frecuenciaProducto = $intFrecuenciaProducto;
}


/**
* Get mesesRestantes
*
* @return integer
*/

public function getMesesRestantes()
{
	return $this->mesesRestantes; 
}

/**
* Set mesesRestantes
*
* @param integer $intMesesRestantes
*/
public function setMesesRestantes($intMesesRestantes)
{
        $this->mesesRestantes = $intMesesRestantes;
}


/**
* Get descripcionPresentaFactura
*
* @return string
*/

public function getDescripcionPresentaFactura()
{
	return $this->descripcionPresentaFactura; 
}

/**
* Set descripcionPresentaFactura
*
* @param string $strDescripcionPresentaFactura
*/
public function setDescripcionPresentaFactura($strDescripcionPresentaFactura)
{
        $this->descripcionPresentaFactura = $strDescripcionPresentaFactura;
}


/**
* Get tieneSolicitudDescuento
*
* @return string
*/

public function getTieneSolicitudDescuento()
{
	return $this->tieneSolicitudDescuento; 
}

/**
* Set tieneSolicitudDescuento
*
* @param string $strTieneSolicitudDescuento
*/
public function setTieneSolicitudDescuento($strTieneSolicitudDescuento)
{
        $this->tieneSolicitudDescuento = $strTieneSolicitudDescuento;
}


/**
* Get tieneSolicitudCambioDoc
*
* @return string
*/

public function getTieneSolicitudCambioDoc()
{
	return $this->tieneSolicitudCambioDoc; 
}

/**
* Set tieneSolicitudCambioDoc
*
* @param string $strTieneSolicitudCambioDoc
*/
public function setTieneSolicitudCambioDoc($strTieneSolicitudCambioDoc)
{
        $this->tieneSolicitudCambioDoc = $strTieneSolicitudCambioDoc;
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
* Get bwSubida
*
* @return string
*/

public function getBwSubida()
{
	return $this->bwSubida; 
}

/**
* Set bwSubida
*
* @param string $strBwSubida
*/
public function setBwSubida($strBwSubida)
{
        $this->bwSubida = $strBwSubida;
}


/**
* Get bwBajada
*
* @return string
*/

public function getBwBajada()
{
	return $this->bwBajada; 
}

/**
* Set bwBajada
*
* @param string $strBwBajada
*/
public function setBwBajada($strBwBajada)
{
        $this->bwBajada = $strBwBajada;
}


/**
* Get empresaId
*
* @return integer
*/

public function getEmpresaId()
{
	return $this->empresaId; 
}

/**
* Set empresaId
*
* @param integer $intEmpresaId
*/
public function setEmpresaId($intEmpresaId)
{
        $this->empresaId = $intEmpresaId;
}

/**
* Get productoId
*
* @return integer
*/

public function getProductoId()
{
	return $this->productoId; 
}

/**
* Set productoId
*
* @param integer $intProductoId
*/
public function setProductoId($intProductoId)
{
        $this->productoId = $intProductoId;
}

/**
* Get porcentajeIva
*
* @return integer
*/

public function getPorcentajeIva()
{
	return $this->porcentajeIva; 
}

/**
* Set porcentajeIva
*
* @param integer $intPorcentajeIva
*/
public function setPorcentajeIva($intPorcentajeIva)
{
        $this->porcentajeIva = $intPorcentajeIva;
}

}