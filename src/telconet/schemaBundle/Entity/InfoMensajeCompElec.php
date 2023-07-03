<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoMensajeCompElec
 *
 * @ORM\Table(name="INFO_MENSAJE_COMP_ELEC")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoMensajeCompElecRepository")
 */
class InfoMensajeCompElec
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_MSN_COMP_ELEC", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_MENSAJE_COMP_ELEC", allocationSize=1, initialValue=1)
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
     * @var string $tipo
     *
     * @ORM\Column(name="TIPO", type="string", nullable=true)
     */
    private $tipo;

    /**
     * @var string $mensaje
     *
     * @ORM\Column(name="MENSAJE", type="string", nullable=true)
     */
    private $mensaje;

    /**
     * @var string $informacionAdicional
     *
     * @ORM\Column(name="INFORMACION_ADICIONAL", type="string", nullable=true)
     */
    private $informacionAdicional;

    /**
     * @var datetime $feCreacion
     *
     * @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
     */
    private $feCreacion;

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
     * Get tipo
     *
     * @return 
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set tipo
     *
     * @param  $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * Get mensaje
     *
     * @return 
     */
    public function getMensaje()
    {
        return $this->mensaje;
    }

    /**
     * Set mensaje
     *
     * @param  $mensaje
     */
    public function setMensaje($mensaje)
    {
        $this->mensaje = $mensaje;
    }

    /**
     * Get informacionAdicional
     *
     * @return 
     */
    public function getInformacionAdicional()
    {
        return $this->informacionAdicional;
    }

    /**
     * Set informacionAdicional
     *
     * @param  informacionAdicional
     */
    public function setInformacionAdicional($informacionAdicional)
    {
        $this->informacionAdicional = $informacionAdicional;
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

}
