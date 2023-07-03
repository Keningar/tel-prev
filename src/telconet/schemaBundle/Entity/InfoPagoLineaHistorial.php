<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPagoLineaHistorial
 *
 * @ORM\Table(name="INFO_PAGO_LINEA_HISTORIAL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPagoLineaHistorialRepository")
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 1.0 21-09-2015
 */
class InfoPagoLineaHistorial
{

    /**
     *
     * @var integer $id
     *      @ORM\Column(name="ID_PAGO_LINEA_HIST", type="integer", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="SEQUENCE")
     *      @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PAGO_LINEA_HIST", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     *
     * @var InfoPagoLinea $pagoLinea
     *      @ORM\ManyToOne(targetEntity="InfoPagoLinea")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="PAGO_LINEA_ID", referencedColumnName="ID_PAGO_LINEA", nullable=false)
     *      })
     */
    private $pagoLinea;

    /**
     *
     * @var AdmiCanalPagoLinea $canalPagoLinea
     *      @ORM\ManyToOne(targetEntity="AdmiCanalPagoLinea")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="CANAL_PAGO_LINEA_ID", referencedColumnName="ID_CANAL_PAGO_LINEA", nullable=false)
     *      })
     */
    private $canalPagoLinea;

    /**
     *
     * @var string $empresaId
     *      @ORM\Column(name="EMPRESA_ID", type="string", nullable=false)
     */
    private $empresaId;

    /**
     *
     * @var InfoPersona $persona
     *      @ORM\ManyToOne(targetEntity="InfoPersona")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="PERSONA_ID", referencedColumnName="ID_PERSONA", nullable=false)
     *      })
     */
    private $persona;

    /**
     *
     * @var float $valorPagoLinea
     *      @ORM\Column(name="VALOR_PAGO_LINEA", type="float", nullable=false)
     */
    private $valorPagoLinea;

    /**
     *
     * @var string $numeroReferencia
     *      @ORM\Column(name="NUMERO_REFERENCIA", type="string", nullable=false)
     */
    private $numeroReferencia;

    /**
     *
     * @var string $estadoPagoLinea
     *      @ORM\Column(name="ESTADO_PAGO_LINEA", type="string", nullable=false)
     */
    private $estadoPagoLinea;

    /**
     *
     * @var string $observacion
     *      @ORM\Column(name="OBSERVACION", type="string", nullable=true)
     */
    private $observacion;

    /**
     *
     * @var string $proceso
     *      @ORM\Column(name="PROCESO", type="string", nullable=true)
     */
    private $proceso;

    /**
     *
     * @var string $usrCreacion
     *      @ORM\Column(name="USR_CREACION", type="string", nullable=false)
     */
    private $usrCreacion;

    /**
     *
     * @var \DateTime $feCreacion
     *      @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
     */
    private $feCreacion;

    /**
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return InfoPagoLinea
     */
    public function getPagoLinea()
    {
        return $this->pagoLinea;
    }

    /**
     *
     * @param InfoPagoLinea $pagoLinea            
     */
    public function setPagoLinea(InfoPagoLinea $pagoLinea)
    {
        $this->pagoLinea = $pagoLinea;
    }

    /**
     *
     * @return AdmiCanalPagoLinea
     */
    public function getCanalPagoLinea()
    {
        return $this->canalPagoLinea;
    }

    /**
     *
     * @param AdmiCanalPagoLinea $canalPagoLinea            
     */
    public function setCanalPagoLinea(AdmiCanalPagoLinea $canalPagoLinea)
    {
        $this->canalPagoLinea = $canalPagoLinea;
    }

    /**
     *
     * @return string
     */
    public function getEmpresaId()
    {
        return $this->empresaId;
    }

    /**
     *
     * @param string $empresaId
     */
    public function setEmpresaId($empresaId)
    {
        $this->empresaId = $empresaId;
    }

    /**
     *
     * @return InfoPersona
     */
    public function getPersona()
    {
        return $this->persona;
    }

    /**
     *
     * @param InfoPersona $persona
     */
    public function setPersona(InfoPersona $persona)
    {
        $this->persona = $persona;
    }

    /**
     *
     * @return float
     */
    public function getValorPagoLinea()
    {
        return $this->valorPagoLinea;
    }

    /**
     *
     * @param float $valorPagoLinea            
     */
    public function setValorPagoLinea($valorPagoLinea)
    {
        $this->valorPagoLinea = $valorPagoLinea;
    }

    /**
     *
     * @return string
     */
    public function getNumeroReferencia()
    {
        return $this->numeroReferencia;
    }

    /**
     *
     * @param string $numeroReferencia            
     */
    public function setNumeroReferencia($numeroReferencia)
    {
        $this->numeroReferencia = $numeroReferencia;
    }

    /**
     *
     * @return string
     */
    public function getEstadoPagoLinea()
    {
        return $this->estadoPagoLinea;
    }

    /**
     *
     * @param string $estadoPagoLinea            
     */
    public function setEstadoPagoLinea($estadoPagoLinea)
    {
        $this->estadoPagoLinea = $estadoPagoLinea;
    }

    /**
     *
     * @return string
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     *
     * @param string $observacion
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    }

    /**
     *
     * @return string
     */
    public function getProceso()
    {
        return $this->proceso;
    }

    /**
     *
     * @param string $proceso
     */
    public function setProceso($proceso)
    {
        $this->proceso = $proceso;
    }

    /**
     *
     * @return string
     */
    public function getUsrCreacion()
    {
        return $this->usrCreacion;
    }

    /**
     *
     * @param string $usrCreacion
     */
    public function setUsrCreacion($usrCreacion)
    {
        $this->usrCreacion = $usrCreacion;
    }

    /**
     *
     * @return \DateTime
     */
    public function getFeCreacion()
    {
        return $this->feCreacion;
    }

    /**
     *
     * @param \DateTime $feCreacion            
     */
    public function setFeCreacion(\DateTime $feCreacion)
    {
        $this->feCreacion = $feCreacion;
        return $this;
    }

}
