<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDocumentoFinancieroDet
 *
 * @ORM\Table(name="INFO_DOCUMENTO_FINANCIERO_DET")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDocumentoFinancieroDetRepository")
 */
class InfoDocumentoFinancieroDet
{


/**
* @var string $empresaId
*
* @ORM\Column(name="EMPRESA_ID", type="string", nullable=true)
*/		
     		
private $empresaId;

/**
* @var integer $oficinaId
*
* @ORM\Column(name="OFICINA_ID", type="integer", nullable=true)
*/		
     		
private $oficinaId;

/**
* @var integer $id
*
* @ORM\Column(name="ID_DOC_DETALLE", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DOC_FINANCIERO_DET", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var InfoDocumentoFinancieroCab
*
* @ORM\ManyToOne(targetEntity="InfoDocumentoFinancieroCab")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="DOCUMENTO_ID", referencedColumnName="ID_DOCUMENTO")
* })
*/
		
private $documentoId;

/**
* @var integer $planId
*
* @ORM\Column(name="PLAN_ID", type="integer", nullable=true)
*/	
	
		
private $planId;

/**
* @var integer $productoId
*
* @ORM\Column(name="PRODUCTO_ID", type="integer", nullable=true)
*/	
		
private $productoId;

/**
* @var integer $puntoId
*
* @ORM\Column(name="PUNTO_ID", type="integer", nullable=true)
*/		
     		
private $puntoId;

/**
* @var integer $cantidad
*
* @ORM\Column(name="CANTIDAD", type="float", nullable=true)
*/		
     		
private $cantidad;

/**
* @var float $precioVentaFacproDetalle
*
* @ORM\Column(name="PRECIO_VENTA_FACPRO_DETALLE", type="float", nullable=true)
*/		
     		
private $precioVentaFacproDetalle;

/**
* @var integer $porcetanjeDescuentoFacpro
*
* @ORM\Column(name="PORCETANJE_DESCUENTO_FACPRO", type="float", nullable=true)
*/		
     		
private $porcetanjeDescuentoFacpro;

/**
* @var float $descuentoFacproDetalle
*
* @ORM\Column(name="DESCUENTO_FACPRO_DETALLE", type="float", nullable=true)
*/		
     		
private $descuentoFacproDetalle;

/**
* @var float $valorFacproDetalle
*
* @ORM\Column(name="VALOR_FACPRO_DETALLE", type="float", nullable=true)
*/		
     		
private $valorFacproDetalle;

/**
* @var float $costoFacproDetalle
*
* @ORM\Column(name="COSTO_FACPRO_DETALLE", type="float", nullable=true)
*/		
     		
private $costoFacproDetalle;

/**
* @var string $observacionesFacturaDetalle
*
* @ORM\Column(name="OBSERVACIONES_FACTURA_DETALLE", type="string", nullable=true)
*/		
     		
private $observacionesFacturaDetalle;

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
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=true)
*/		
     		
private $usrCreacion;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
*/		
     		
private $usrUltMod;

/**
* @var string $motivoId
*
* @ORM\Column(name="MOTIVO_ID", type="integer", nullable=true)
*/		
     		
private $motivoId;


/**
* @var integer $pagoDetId
*
* @ORM\Column(name="PAGO_DET_ID", type="integer", nullable=true)
*/		
     		
private $pagoDetId;

/**
* @var integer $servicioId
*
* @ORM\Column(name="SERVICIO_ID", type="integer", nullable=true)
*/		
     		
private $servicioId;

/**
* Get empresaId
*
* @return string
*/		
     		
public function getEmpresaId(){
	return $this->empresaId; 
}

/**
* Set empresaId
*
* @param string $empresaId
*/
public function setEmpresaId($empresaId)
{
        $this->empresaId = $empresaId;
}


/**
* Get oficinaId
*
* @return integer
*/		
     		
public function getOficinaId(){
	return $this->oficinaId; 
}

/**
* Set oficinaId
*
* @param integer $oficinaId
*/
public function setOficinaId($oficinaId)
{
        $this->oficinaId = $oficinaId;
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
* Get documentoId
*
* @return telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab
*/		
     		
public function getDocumentoId(){
	return $this->documentoId; 
}

/**
* Set documentoId
*
* @param telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab $documentoId
*/
public function setDocumentoId(\telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab $documentoId)
{
        $this->documentoId = $documentoId;
}


/**
* Get planId
*
* @return integer
*/		
     		
public function getPlanId(){
	return $this->planId; 
}

/**
* Set planId
*
* @param integer $planId
*/
public function setPlanId($planId)
{
        $this->planId = $planId;
}

/**
* Get puntoId
*
* @return integer
*/		
     		
public function getPuntoId(){
	return $this->puntoId; 
}

/**
* Set puntoId
*
* @param integer $puntoId
*/
public function setPuntoId($puntoId)
{
        $this->puntoId = $puntoId;
}


/**
* Get cantidad
*
* @return float
*/		
     		
public function getCantidad(){
	return $this->cantidad; 
}

/**
* Set cantidad
*
* @param float $cantidad
*/
public function setCantidad($cantidad)
{
        $this->cantidad = $cantidad;
}


/**
* Get precioVentaFacproDetalle
*
* @return float
*/		
     		
public function getPrecioVentaFacproDetalle(){
	return $this->precioVentaFacproDetalle; 
}

/**
* Set precioVentaFacproDetalle
*
* @param float $precioVentaFacproDetalle
*/
public function setPrecioVentaFacproDetalle($precioVentaFacproDetalle)
{
        $this->precioVentaFacproDetalle = $precioVentaFacproDetalle;
}


/**
* Get porcetanjeDescuentoFacpro
*
* @return integer
*/		
     		
public function getPorcetanjeDescuentoFacpro(){
	return $this->porcetanjeDescuentoFacpro; 
}

/**
* Set porcetanjeDescuentoFacpro
*
* @param integer $porcetanjeDescuentoFacpro
*/
public function setPorcetanjeDescuentoFacpro($porcetanjeDescuentoFacpro)
{
        $this->porcetanjeDescuentoFacpro = $porcetanjeDescuentoFacpro;
}


/**
* Get descuentoFacproDetalle
*
* @return float
*/		
     		
public function getDescuentoFacproDetalle(){
	return $this->descuentoFacproDetalle; 
}

/**
* Set descuentoFacproDetalle
*
* @param float $descuentoFacproDetalle
*/
public function setDescuentoFacproDetalle($descuentoFacproDetalle)
{
        $this->descuentoFacproDetalle = $descuentoFacproDetalle;
}


/**
* Get valorFacproDetalle
*
* @return float
*/		
     		
public function getValorFacproDetalle(){
	return $this->valorFacproDetalle; 
}

/**
* Set valorFacproDetalle
*
* @param float $valorFacproDetalle
*/
public function setValorFacproDetalle($valorFacproDetalle)
{
        $this->valorFacproDetalle = $valorFacproDetalle;
}


/**
* Get costoFacproDetalle
*
* @return float
*/		
     		
public function getCostoFacproDetalle(){
	return $this->costoFacproDetalle; 
}

/**
* Set costoFacproDetalle
*
* @param float $costoFacproDetalle
*/
public function setCostoFacproDetalle($costoFacproDetalle)
{
        $this->costoFacproDetalle = $costoFacproDetalle;
}


/**
* Get observacionesFacturaDetalle
*
* @return 
*/		
     		
public function getObservacionesFacturaDetalle(){
	return $this->observacionesFacturaDetalle; 
}

/**
* Set observacionesFacturaDetalle
*
* @param  $observacionesFacturaDetalle
*/
public function setObservacionesFacturaDetalle($observacionesFacturaDetalle)
{
        $this->observacionesFacturaDetalle = $observacionesFacturaDetalle;
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
* Get productoId
*
* @return integer 
*/		
     		
public function getProductoId(){
	return $this->productoId; 
}

/**
* Set productoId
*
* @param integer $productoId
*/
public function setProductoId($productoId)
{
        $this->productoId = $productoId;
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
* Get pagoDetId
*
* @return integer
*/		
     		
public function getPagoDetId(){
	return $this->pagoDetId; 
}

/**
* Set pagoDetId
*
* @param integer $pagoDetId
*/
public function setPagoDetId($pagoDetId)
{
        $this->pagoDetId = $pagoDetId;
}


    /**
    * Get servicioId
    *
    * @return integer
    */
    public function getServicioId()
    {
        return $this->servicioId; 
    }

    /**
    * Set servicioId
    *
    * @param integer $servicioId
    */
    public function setServicioId($servicioId)
    {
        $this->servicioId = $servicioId;
    }

}
