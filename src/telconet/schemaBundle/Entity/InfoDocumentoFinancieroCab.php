<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab
 *
 * @ORM\Table(name="INFO_DOCUMENTO_FINANCIERO_CAB")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDocumentoFinancieroCabRepository")
 */
class InfoDocumentoFinancieroCab
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_DOCUMENTO", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DOC_FINANCIERO_CAB", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer $oficinaId
     *
     * @ORM\Column(name="OFICINA_ID", type="integer", nullable=true)
     */
    private $oficinaId;

    /**
     * @var integer $puntoId
     *
     * @ORM\Column(name="PUNTO_ID", type="integer", nullable=true)
     */
    private $puntoId;

    /**
     * @var AdmiTipoDocumentoFinanciero
     *
     * @ORM\ManyToOne(targetEntity="AdmiTipoDocumentoFinanciero")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="TIPO_DOCUMENTO_ID", referencedColumnName="ID_TIPO_DOCUMENTO")
     * })
     */
    private $tipoDocumentoId;

    /**
     * @var string $numeroFacturaSri
     *
     * @ORM\Column(name="NUMERO_FACTURA_SRI", type="string", nullable=true)
     */
    private $numeroFacturaSri;

    /**
     * @var float $subtotal
     *
     * @ORM\Column(name="SUBTOTAL", type="float", nullable=true)
     */
    private $subtotal;

    /**
     * @var float $subtotalCeroImpuesto
     *
     * @ORM\Column(name="SUBTOTAL_CERO_IMPUESTO", type="float", nullable=true)
     */
    private $subtotalCeroImpuesto;

    /**
     * @var float $subtotalConImpuesto
     *
     * @ORM\Column(name="SUBTOTAL_CON_IMPUESTO", type="float", nullable=true)
     */
    private $subtotalConImpuesto;

    /**
     * @var float $subtotalDescuento
     *
     * @ORM\Column(name="SUBTOTAL_DESCUENTO", type="float", nullable=true)
     */
    private $subtotalDescuento;

    /**
     * @var float $valorTotal
     *
     * @ORM\Column(name="VALOR_TOTAL", type="float", nullable=true)
     */
    private $valorTotal;

    /**
     * @var string $entregoRetencionFte
     *
     * @ORM\Column(name="ENTREGO_RETENCION_FTE", type="string", nullable=true)
     */
    private $entregoRetencionFte;

    /**
     * @var string $estadoImpresionFact
     *
     * @ORM\Column(name="ESTADO_IMPRESION_FACT", type="string", nullable=true)
     */
    private $estadoImpresionFact;

    /**
     * @var string $observacion
     *
     * @ORM\Column(name="OBSERVACION", type="string", nullable=true)
     */
    private $observacion;

    /**
     * @var string $esAutomatica
     *
     * @ORM\Column(name="ES_AUTOMATICA", type="string", nullable=true)
     */
    private $esAutomatica;

    /**
     * @var string $prorrateo
     *
     * @ORM\Column(name="PRORRATEO", type="string", nullable=true)
     */
    private $prorrateo;

    /**
     * @var string $reactivacion
     *
     * @ORM\Column(name="REACTIVACION", type="string", nullable=true)
     */
    private $reactivacion;

    /**
     * @var string $recurrente
     *
     * @ORM\Column(name="RECURRENTE", type="string", nullable=true)
     */
    private $recurrente;

    /**
     * @var string $comisiona
     *
     * @ORM\Column(name="COMISIONA", type="string", nullable=true)
     */
    private $comisiona;

    /**
     * @var datetime $feCreacion
     *
     * @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
     */
    private $feCreacion;

    /**
     * @var datetime $feEmision
     *
     * @ORM\Column(name="FE_EMISION", type="datetime", nullable=true)
     */
    private $feEmision;
    
    /**
     * @var datetime $feAutorizacion
     *
     * @ORM\Column(name="FE_AUTORIZACION", type="datetime", nullable=true)
     */
    private $feAutorizacion;

    /**
     * @var string $usrCreacion
     *
     * @ORM\Column(name="USR_CREACION", type="string", nullable=true)
     */
    private $usrCreacion;

    /**
     * @var integer $referenciaDocumentoId
     *
     * @ORM\Column(name="REFERENCIA_DOCUMENTO_ID", type="integer", nullable=true)
     */
    private $referenciaDocumentoId;

    /**
     * @var string $numFactMigracion
     *
     * @ORM\Column(name="NUM_FACT_MIGRACION", type="string", nullable=true)
     */
    private $numFactMigracion;

    /**
     * @var string $esElectronica
     *
     * @ORM\Column(name="ES_ELECTRONICA", type="string", nullable=true)
     */
    private $esElectronica = 'N';
    
    /**
     * @var string $mesConsumo
     *
     * @ORM\Column(name="MES_CONSUMO", type="string", nullable=true)
     */
    private $mesConsumo;
    
    /**
     * @var string $anioConsumo
     *
     * @ORM\Column(name="ANIO_CONSUMO", type="string", nullable=true)
     */
    private $anioConsumo;
    
    /**
     * @var string $rangoConsumo
     *
     * @ORM\Column(name="RANGO_CONSUMO", type="string", nullable=true)
     */
    private $rangoConsumo;

    /**
     * @var float $descuentoCompensacion
     *
     * @ORM\Column(name="DESCUENTO_COMPENSACION", type="float", nullable=true)
     */
    private $descuentoCompensacion;
    
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
     * Set id
     *
     * @param integer $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Get oficinaId
     *
     * @return integer
     */
    public function getNumFactMigracion()
    {
        return $this->numFactMigracion;
    }

    /**
     * Set oficinaId
     *
     * @param integer $oficinaId
     */
    public function setNumFactMigracion($numFactMigracion)
    {
        $this->numFactMigracion = $numFactMigracion;
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
     * @param integer $oficinaId
     */
    public function setOficinaId($oficinaId)
    {
        $this->oficinaId = $oficinaId;
    }

    /**
     * Get puntoId
     *
     * @return integer
     */
    public function getPuntoId()
    {
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
     * Get tipoDocumentoId
     *
     * @return telconet\schemaBundle\Entity\AdmiTipoDocumentoFinanciero
     */
    public function getTipoDocumentoId()
    {
        return $this->tipoDocumentoId;
    }

    /**
     * Set tipoDocumentoId
     *
     * @param telconet\schemaBundle\Entity\AdmiTipoDocumentoFinanciero $tipoDocumentoId
     */
    public function setTipoDocumentoId(\telconet\schemaBundle\Entity\AdmiTipoDocumentoFinanciero $tipoDocumentoId)
    {
        $this->tipoDocumentoId = $tipoDocumentoId;
    }

    /**
     * Get numeroFacturaSri
     *
     * @return string
     */
    public function getNumeroFacturaSri()
    {
        return $this->numeroFacturaSri;
    }

    /**
     * Set numeroFacturaSri
     *
     * @param string $numeroFacturaSri
     */
    public function setNumeroFacturaSri($numeroFacturaSri)
    {
        $this->numeroFacturaSri = $numeroFacturaSri;
    }

    /**
     * Get subtotal
     *
     * @return float
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set subtotal
     *
     * @param float $subtotal
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;
    }

    /**
     * Get subtotalCeroImpuesto
     *
     * @return float
     */
    public function getSubtotalCeroImpuesto()
    {
        return $this->subtotalCeroImpuesto;
    }

    /**
     * Set subtotalCeroImpuesto
     *
     * @param float $subtotalCeroImpuesto
     */
    public function setSubtotalCeroImpuesto($subtotalCeroImpuesto)
    {
        $this->subtotalCeroImpuesto = $subtotalCeroImpuesto;
    }

    /**
     * Get subtotalConImpuesto
     *
     * @return float
     */
    public function getSubtotalConImpuesto()
    {
        return $this->subtotalConImpuesto;
    }

    /**
     * Set subtotalConImpuesto
     *
     * @param float $subtotalConImpuesto
     */
    public function setSubtotalConImpuesto($subtotalConImpuesto)
    {
        $this->subtotalConImpuesto = $subtotalConImpuesto;
    }

    /**
     * Get subtotalDescuento
     *
     * @return float
     */
    public function getSubtotalDescuento()
    {
        return $this->subtotalDescuento;
    }

    /**
     * Set subtotalDescuento
     *
     * @param float $subtotalDescuento
     */
    public function setSubtotalDescuento($subtotalDescuento)
    {
        $this->subtotalDescuento = $subtotalDescuento;
    }

    /**
     * Get valorTotal
     *
     * @return float
     */
    public function getValorTotal()
    {
        return $this->valorTotal;
    }

    /**
     * Set valorTotal
     *
     * @param float $valorTotal
     */
    public function setValorTotal($valorTotal)
    {
        $this->valorTotal = $valorTotal;
    }

    /**
     * Get entregoRetencionFte
     *
     * @return string
     */
    public function getEntregoRetencionFte()
    {
        return $this->entregoRetencionFte;
    }

    /**
     * Set entregoRetencionFte
     *
     * @param string $entregoRetencionFte
     */
    public function setEntregoRetencionFte($entregoRetencionFte)
    {
        $this->entregoRetencionFte = $entregoRetencionFte;
    }

    /**
     * Get estadoImpresionFact
     *
     * @return string
     */
    public function getEstadoImpresionFact()
    {
        return $this->estadoImpresionFact;
    }

    /**
     * Set estadoImpresionFact
     *
     * @param string $estadoImpresionFact
     */
    public function setEstadoImpresionFact($estadoImpresionFact)
    {
        $this->estadoImpresionFact = $estadoImpresionFact;
    }

    /**
     * Get esAutomatica
     *
     * @return string
     */
    public function getEsAutomatica()
    {
        return $this->esAutomatica;
    }

    /**
     * Set esAutomatica
     *
     * @param string $esAutomatica
     */
    public function setEsAutomatica($esAutomatica)
    {
        $this->esAutomatica = $esAutomatica;
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
     * @param string $observacion
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    }

    /**
     * Get prorrateo
     *
     * @return string
     */
    public function getProrrateo()
    {
        return $this->prorrateo;
    }

    /**
     * Set prorrateo
     *
     * @param string $prorrateo
     */
    public function setProrrateo($prorrateo)
    {
        $this->prorrateo = $prorrateo;
    }

    /**
     * Get reactivacion
     *
     * @return string
     */
    public function getReactivacion()
    {
        return $this->reactivacion;
    }

    /**
     * Set reactivacion
     *
     * @param string $reactivacion
     */
    public function setReactivacion($reactivacion)
    {
        $this->reactivacion = $reactivacion;
    }

    /**
     * Get recurrente
     *
     * @return string
     */
    public function getRecurrente()
    {
        return $this->recurrente;
    }

    /**
     * Set recurrente
     *
     * @param string $recurrente
     */
    public function setRecurrente($recurrente)
    {
        $this->recurrente = $recurrente;
    }

    /**
     * Get comisiona
     *
     * @return string
     */
    public function getComisiona()
    {
        return $this->comisiona;
    }

    /**
     * Set comisiona
     *
     * @param string $comisiona
     */
    public function setComisiona($comisiona)
    {
        $this->comisiona = $comisiona;
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
     * Get feEmision
     *
     * @return datetime
     */
    public function getFeEmision()
    {
        return $this->feEmision;
    }

    /**
     * Set feEmision
     *
     * @param  $feEmision
     */
    public function setFeEmision($feEmision)
    {
        $this->feEmision = $feEmision;
    }

    /**
     * Get feAutorizacion
     *
     * @return datetime
     */
    public function getFeAutorizacion()
    {
        return $this->feAutorizacion;
    }

    /**
     * Set feAutorizacion
     *
     * @param  $feAutorizacion
     */
    public function setFeAutorizacion($feAutorizacion)
    {
        $this->feAutorizacion = $feAutorizacion;
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
     * Get referenciaDocumentoId
     *
     * @return integer
     */
    public function getReferenciaDocumentoId()
    {
        return $this->referenciaDocumentoId;
    }

    /**
     * Set referenciaDocumentoId
     *
     * @param integer $referenciaDocumentoId
     */
    public function setReferenciaDocumentoId($referenciaDocumentoId)
    {
        $this->referenciaDocumentoId = $referenciaDocumentoId;
    }

    /**
     * Get esElectronica
     *
     * @return string
     */
    public function getEsElectronica()
    {
        return $this->esElectronica;
    }

    /**
     * Set esElectronica
     *
     * @param integer $esElectronica
     */
    public function setEsElectronica($esElectronica)
    {
        $this->esElectronica = $esElectronica;
    }
    
    /**
     * Get mesConsumo
     * 
     * @return string
     */
    public function getMesConsumo(){
        return $this->mesConsumo;
    }
    
    /**
     * Set mesConsumo
     * 
     * @param string $mesConsumo
     */
    public function setMesConsumo($mesConsumo){
        $this->mesConsumo = $mesConsumo;
    }
    
    /**
     * Get anioConsumo
     * 
     * @return string
     */
    public function getAnioConsumo(){
        return $this->anioConsumo;
    }
    
    /**
     * Set anioConsumo
     * 
     * @param string $anioConsumo
     */
    public function setAnioConsumo($anioConsumo){
        $this->anioConsumo = $anioConsumo;
    }
    
    /**
     * Get rangoConsumo
     * 
     * @return string
     */
    public function getRangoConsumo()
    {
        return $this->rangoConsumo;
    }
    
    /**
     * Set rangoConsumo
     * 
     * @param string $rangoConsumo
     */
    public function setRangoConsumo($rangoConsumo)
    {
        $this->rangoConsumo = $rangoConsumo;
    }
    
    
    /**
     * Get descuentoCompensacion
     *
     * @return float
     */
    public function getDescuentoCompensacion()
    {
        return $this->descuentoCompensacion;
    }

    /**
     * Set descuentoCompensacion
     *
     * @param float $descuentoCompensacion
     */
    public function setDescuentoCompensacion($descuentoCompensacion)
    {
        $this->descuentoCompensacion = $descuentoCompensacion;
    }

}
