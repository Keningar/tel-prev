<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\EstadoCuentaCliente
 *
 * @ORM\Table(name="ESTADO_CUENTA_CLIENTE")
 * @ORM\Entity
 */
class EstadoCuentaCliente
{


    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_DOCUMENTO", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_ESTADO_CUENTA_CLIENTE", allocationSize=1, initialValue=1)
    */		

    private $id;	

    /**
    * @var string $puntoId
    *
    * @ORM\Column(name="PUNTO_ID", type="integer", nullable=false)
    */		

    private $puntoId;

    /**
    * @var string $oficinaId
    *
    * @ORM\Column(name="OFICINA_ID", type="integer", nullable=false)
    */		

    private $oficinaId;

    /**
    * @var string $numeroFacturaSri
    *
    * @ORM\Column(name="NUMERO_FACTURA_SRI", type="string", nullable=false)
    */		

    private $numeroFacturaSri;

    /**
    * @var datetime $tipoDocumentoId
    *
    * @ORM\Column(name="TIPO_DOCUMENTO_ID", type="integer", nullable=false)
    */		

    private $tipoDocumentoId;

    /**
    * @var string $valorTotal
    *
    * @ORM\Column(name="VALOR_TOTAL", type="float", nullable=false)
    */		

    private $valorTotal;

    /**
    * @var datetime $feCreacion
    *
    * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
    */		

    private $feCreacion;
    
    /**
    * @var string $fecCreacion
    *
    * @ORM\Column(name="FEC_CREACION", type="string", nullable=false)
    */		

    private $fecCreacion;
    
    /**
    * @var string $fecEmision
    *
    * @ORM\Column(name="FEC_EMISION", type="string", nullable=false)
    */		

    private $fecEmision;
    
    /**
    * @var string $fecAutorizacion
    *
    * @ORM\Column(name="FEC_AUTORIZACION", type="string", nullable=false)
    */		

    private $fecAutorizacion;
    
    /**
    * @var string $referencia
    *
    * @ORM\Column(name="REFERENCIA", type="string", nullable=false)
    */		

    private $referencia;

    /**
    * @var datetime $estadoImpresionFact
    *
    * @ORM\Column(name="ESTADO_IMPRESION_FACT", type="string", nullable=false)
    */		

    private $estadoImpresionFact;

    /**
    * @var string $codigoFormaPago
    *
    * @ORM\Column(name="CODIGO_FORMA_PAGO", type="string", nullable=false)
    */		

    private $codigoFormaPago;

    /**
    * @var string $numeroReferencia
    *
    * @ORM\Column(name="NUMERO_REFERENCIA", type="string", nullable=false)
    */		

    private $numeroReferencia;

    /**
    * @var string $numeroCuentaBanco
    *
    * @ORM\Column(name="NUMERO_CUENTA_BANCO", type="string", nullable=false)
    */		

    private $numeroCuentaBanco;

    /**
    * @var string $referenciaId
    *
    * @ORM\Column(name="REFERENCIA_ID", type="integer", nullable=false)
    */		

    private $referenciaId;

    /**
    * @var datetime $migracion
    *
    * @ORM\Column(name="MIGRACION", type="integer", nullable=false)
    */		

    private $migracion;
    
     /**
    * @var string $refAnticipoId
    *
    * @ORM\Column(name="REF_ANTICIPO_ID", type="string", nullable=false)
    */		

    private $refAnticipoId;

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
     * Set puntoId
     *
     * @param integer $puntoId
     */
    public function setPuntoId($puntoId)
    {
        $this->puntoId = $puntoId;
    
        return $this;
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
     * Set oficinaId
     *
     * @param integer $oficinaId
     */
    public function setOficinaId($oficinaId)
    {
        $this->oficinaId = $oficinaId;
    
        return $this;
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
     * Set numeroFacturaSri
     *
     * @param string $numeroFacturaSri
     */
    public function setNumeroFacturaSri($numeroFacturaSri)
    {
        $this->numeroFacturaSri = $numeroFacturaSri;
    
        return $this;
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
     * Set tipoDocumentoId
     *
     * @param integer $tipoDocumentoId
     */
    public function setTipoDocumentoId($tipoDocumentoId)
    {
        $this->tipoDocumentoId = $tipoDocumentoId;
    
        return $this;
    }

    /**
     * Get tipoDocumentoId
     *
     * @return integer 
     */
    public function getTipoDocumentoId()
    {
        return $this->tipoDocumentoId;
    }

    /**
     * Set valorTotal
     *
     * @param float $valorTotal
     */
    public function setValorTotal($valorTotal)
    {
        $this->valorTotal = $valorTotal;
    
        return $this;
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
     * Set feCreacion
     *
     * @param \DateTime $feCreacion
     */
    public function setFeCreacion($feCreacion)
    {
        $this->feCreacion = $feCreacion;
    
        return $this;
    }

    /**
     * Get feCreacion
     *
     * @return \DateTime 
     */
    public function getFeCreacion()
    {
        return $this->feCreacion;
    }

    /**
     * Set fecCreacion
     *
     * @param string $fecCreacion
     */
    public function setFecCreacion($fecCreacion)
    {
        $this->fecCreacion = $fecCreacion;
    
        return $this;
    }

    /**
     * Get fecCreacion
     *
     * @return string 
     */
    public function getFecCreacion()
    {
        return $this->fecCreacion;
    }
    
    /**
     * Set fecEmision
     *
     * @param string $fecEmision
     */
    public function setFecEmision($fecEmision)
    {
        $this->fecEmision= $fecEmision;
    
        return $this;
    }

    /**
     * Get fecEmision
     *
     * @return string 
     */
    public function getFecEmision()
    {
        return $this->fecEmision;
    }
    
     /**
     * Set fecAutorizacion
     *
     * @param string $fecAutorizacion
     */
    public function setFecAutorizacion($fecAutorizacion)
    {
        $this->fecAutorizacion= $fecAutorizacion;
    
        return $this;
    }

    /**
     * Get fecAutorizacion
     *
     * @return string
     */
    public function getFecAutorizacion()
    {
        return $this->fecAutorizacion;
    }
 
    /**
     * Set estadoImpresionFact
     *
     * @param string $estadoImpresionFact
     */
    public function setEstadoImpresionFact($estadoImpresionFact)
    {
        $this->estadoImpresionFact = $estadoImpresionFact;
    
        return $this;
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
     * Set referencia
     *
     * @param string $referencia
     */
    public function setReferencia($referencia)
    {
        $this->referencia = $referencia;
    
        return $this;
    }

    /**
     * Get referencia
     *
     * @return string 
     */
    public function getReferencia()
    {
        return $this->referencia;
    }
    
    /**
     * Set codigoFormaPago
     *
     * @param string $codigoFormaPago
     */
    public function setCodigoFormaPago($codigoFormaPago)
    {
        $this->codigoFormaPago = $codigoFormaPago;
    
        return $this;
    }

    /**
     * Get codigoFormaPago
     *
     * @return string 
     */
    public function getCodigoFormaPago()
    {
        return $this->codigoFormaPago;
    }
    
    /**
     * Set numeroReferencia
     *
     * @param string $numeroReferencia
     */
    public function setNumeroReferencia($numeroReferencia)
    {
        $this->numeroReferencia = $numeroReferencia;
    
        return $this;
    }

    /**
     * Get numeroReferencia
     *
     * @return string 
     */
    public function getNumeroReferencia()
    {
        return $this->numeroReferencia;
    }
    
    /**
     * Set numeroCuentaBanco
     *
     * @param string $numeroCuentaBanco
     */
    public function setNumeroCuentaBanco($numeroCuentaBanco)
    {
        $this->numeroCuentaBanco = $numeroCuentaBanco;
    
        return $this;
    }

    /**
     * Get numeroCuentaBanco
     *
     * @return string 
     */
    public function getNumeroCuentaBanco()
    {
        return $this->numeroCuentaBanco;
    }
    
    /**
     * Set referenciaId
     *
     * @param string $referenciaId
     */
    public function setReferenciaId($referenciaId)
    {
        $this->referenciaId = $referenciaId;
    
        return $this;
    }

    /**
     * Get referenciaId
     *
     * @return string 
     */
    public function getReferenciaId()
    {
        return $this->referenciaId;
    }
    
    /**
     * Set migracion
     *
     * @param integer $migracion
     */
    public function setMigracion($migracion)
    {
        $this->migracion = $migracion;
    
        return $this;
    }

    /**
     * Get migracion
     *
     * @return integer 
     */
    public function getMigracion()
    {
        return $this->migracion;
    }
    
    /**
     * Set refAnticipoId
     *
     * @param string $refAnticipoId
     */
    public function setRefAnticipoId($refAnticipoId)
    {
        $this->refAnticipoId = $refAnticipoId;
    
        return $this;
    }

    /**
     * Get refAnticipoId
     *
     * @return string 
     */
    public function getRefAnticipoId()
    {
        return $this->refAnticipoId;
    }


}
