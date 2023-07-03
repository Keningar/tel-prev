<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoComprobanteElectronico
 *
 * @ORM\Table(name="INFO_COMPROBANTE_ELECTRONICO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoComprobanteElectronicoRepository")
 */
class InfoComprobanteElectronico
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_COMP_ELECTRONICO", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_COMP_ELECTRONICO", allocationSize=1, initialValue=1)
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
     * @var string $numeroFacturaSri
     * 
     * @ORM\Column(name="NUMERO_FACTURA_SRI", type="string", nullable=true)
     */
    private $numeroFacturaSri;

    /**
     * @var string $comprobanteElectronico
     * 
     * @ORM\Column(name="COMPROBANTE_ELECTRONICO", type="string", nullable=true)
     */
    private $comprobanteElectronico;

    /**
     * @var text $comprobanteElectronicoPdf
     * 
     * @ORM\Column(name="COMP_ELECTRONICO_PDF", type="text", nullable=true)
     */
    private $comprobanteElectronicoPdf;

    /** 	 
     * @var text $comprobanteElectDevuelto	 
     * 	 
     * @ORM\Column(name="COMPROBANTE_ELECT_DEVUELTO", type="text", nullable=true)	 
     */
    private $comprobanteElectDevuelto;

    /**
     * @var string $ruc
     *
     * @ORM\Column(name="RUC", type="string", nullable=true)
     */
    private $ruc;

    /**
     * @var string $claveAcceso
     *
     * @ORM\Column(name="CLAVE_ACCESO", type="string", nullable=true)
     */
    private $claveAcceso;

    /**
     * @var integer $estado
     *
     * @ORM\Column(name="ESTADO", type="integer", nullable=true)
     */
    private $estado;

    /**
     * @var datetime $feCreacion
     *
     * @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
     */
    private $feCreacion;
    
    /**
     * @var datetime $feCreacion
     *
     * @ORM\Column(name="FE_AUTORIZACION", type="datetime", nullable=true)
     */
    private $feAutorizacion;
    
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
     * Get documentoId
     *
     * @return telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab
     */
    public function getDocumentoId()
    {
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
     * @param  $numeroFacturaSri
     */
    public function setNumeroFacturaSri($numeroFacturaSri)
    {
        $this->numeroFacturaSri = $numeroFacturaSri;
    }
    
    /**
     * Get comprobanteElectronico	 
     * 
     * @return string 
     */
    public function getComprobanteElectronico()
    {
        return $this->comprobanteElectronico;
    }
    
    /**
     * Set comprobanteElectronico
     *
     * @param  $comprobanteElectronico
     */
    public function setComprobanteElectronico($comprobanteElectronico)
    {
        $this->comprobanteElectronico = $comprobanteElectronico;
    }
    
    /**
     * Get comprobanteElectronicoPdf	 
     * 
     * @return text 
     */
    public function getComprobanteElectronicoPdf()
    {
        return $this->comprobanteElectronicoPdf;
    }
    
    /**
     * Set comprobanteElectronicoPdf
     *
     * @param  $comprobanteElectronicoPdf
     */
    public function setComprobanteElectronicoPdf($comprobanteElectronicoPdf)
    {
        $this->comprobanteElectronicoPdf = $comprobanteElectronicoPdf;
    }
    
    /**
     * Get comprobanteElectDevuelto	 
     * 
     * @return text 
     */
    public function getComprobanteElectDevuelto()
    {
        return $this->comprobanteElectDevuelto;
    }
    
    /**
     * Set comprobanteElectDevuelto
     *
     * @param  $comprobanteElectDevuelto
     */
    public function setComprobanteElectDevuelto($comprobanteElectDevuelto)
    {
        $this->comprobanteElectDevuelto = $comprobanteElectDevuelto;
    }
    
    /**
     * Get ruc
     *
     * @return 
     */
    public function getRuc()
    {
        return $this->ruc;
    }

    /**
     * Set ruc
     *
     * @param  $ruc
     */
    public function setRuc($ruc)
    {
        $this->ruc = $ruc;
    }

    /**
     * Get claveAcceso
     *
     * @return 
     */
    public function getClaveAcceso()
    {
        return $this->claveAcceso;
    }

    /**
     * Set claveAcceso
     *
     * @param  $claveAcceso
     */
    public function setClaveAcceso($claveAcceso)
    {
        $this->claveAcceso = $claveAcceso;
    }

    /**
     * Get estado
     *
     * @return 
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set estado
     *
     * @param  $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
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
     * @param datetime $feAutorizacion
     */
    public function setFeAutorizacion($feAutorizacion)
    {
        $this->feAutorizacion = $feAutorizacion;
    }
}
