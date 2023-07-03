<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPagoLinea
 *
 * @ORM\Table(name="INFO_PAGO_LINEA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPagoLineaRepository")
 *
 * @author ltama
 */
class InfoPagoLinea {
    
    /**
     *
     * @var integer $id
     *      @ORM\Column(name="ID_PAGO_LINEA", type="integer", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="SEQUENCE")
     *      @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PAGO_LINEA", allocationSize=1, initialValue=1)
     */
    private $id;
    
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
     * @var integer $oficinaId
     *      @ORM\Column(name="OFICINA_ID", type="integer", nullable=false)
     */
    private $oficinaId;
    
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
     * @var integer $procesoMasivoId
     *      @ORM\Column(name="PROCESO_MASIVO_ID", type="integer", nullable=true)
     */
    private $procesoMasivoId;
    
    /**
     *
     * @var float $valorPagoLinea
     *      @ORM\Column(name="VALOR_PAGO_LINEA", type="float", nullable=true)
     */
    private $valorPagoLinea;
    
    /**
     *
     * @var string $numeroReferencia
     *      @ORM\Column(name="NUMERO_REFERENCIA", type="string", nullable=true)
     */
    private $numeroReferencia;
    
    /**
     *
     * @var string $estadoPagoLinea
     *      @ORM\Column(name="ESTADO_PAGO_LINEA", type="string", nullable=true)
     */
    private $estadoPagoLinea;
    
    /**
     *
     * @var string $comentarioPagoLinea
     *      @ORM\Column(name="COMENTARIO_PAGO_LINEA", type="string", nullable=true)
     */
    private $comentarioPagoLinea;
    
    /**
     *
     * @var string $usrCreacion
     *      @ORM\Column(name="USR_CREACION", type="string", nullable=true)
     */
    private $usrCreacion;
    
    /**
     *
     * @var \DateTime $feCreacion
     *      @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
     */
    private $feCreacion;
    
    /**
     *
     * @var string $usrUltMod
     *      @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
     */
    private $usrUltMod;
    
    /**
     *
     * @var \DateTime $feUltMod
     *      @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
     */
    private $feUltMod;
    
    /**
     *
     * @var string $usrEliminacion
     *      @ORM\Column(name="USR_ELIMINACION", type="string", nullable=true)
     */
    private $usrEliminacion;
    
    /**
     *
     * @var \DateTime $feEliminacion
     *      @ORM\Column(name="FE_ELIMINACION", type="datetime", nullable=true)
     */
    private $feEliminacion;
    
    /**
     *
     * @var \DateTime $feTransaccion
     *      @ORM\Column(name="FE_TRANSACCION", type="datetime", nullable=true)
     */
    private $feTransaccion;

    /**
     *
     * @var string $reversado
     *      @ORM\Column(name="REVERSADO", type="string", nullable=true)
     */
    private $reversado;
    
    /**
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     *
     * @param integer $id
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    /**
     *
     * @return AdmiCanalPagoLinea
     */
    public function getCanalPagoLinea() {
        return $this->canalPagoLinea;
    }
    
    /**
     *
     * @param AdmiCanalPagoLinea $canalPagoLinea            
     */
    public function setCanalPagoLinea(AdmiCanalPagoLinea $canalPagoLinea) {
        $this->canalPagoLinea = $canalPagoLinea;
        return $this;
    }
    
    /**
     *
     * @return string
     */
    public function getEmpresaId() {
        return $this->empresaId;
    }
    
    /**
     *
     * @param string $empresaId            
     */
    public function setEmpresaId($empresaId) {
        $this->empresaId = $empresaId;
        return $this;
    }
    
    /**
     *
     * @return integer
     */
    public function getOficinaId() {
        return $this->oficinaId;
    }
    
    /**
     *
     * @param integer $oficinaId
     */
    public function setOficinaId($oficinaId) {
        $this->oficinaId = $oficinaId;
        return $this;
    }
    
    /**
     *
     * @return InfoPersona
     */
    public function getPersona() {
        return $this->persona;
    }
    
    /**
     *
     * @param InfoPersona $persona            
     */
    public function setPersona(InfoPersona $persona) {
        $this->persona = $persona;
        return $this;
    }

    /**
     *
     * @return integer
     */
    public function getProcesoMasivoId()
    {
        return $this->procesoMasivoId;
    }

    /**
     *
     * @param integer $procesoMasivoId
     */
    public function setProcesoMasivoId($procesoMasivoId)
    {
        $this->procesoMasivoId = $procesoMasivoId;
        return $this;
    }
    
    /**
     *
     * @return float
     */
    public function getValorPagoLinea() {
        return $this->valorPagoLinea;
    }
    
    /**
     *
     * @param float $valorPagoLinea            
     */
    public function setValorPagoLinea($valorPagoLinea) {
        $this->valorPagoLinea = $valorPagoLinea;
        return $this;
    }
    
    /**
     *
     * @return string
     */
    public function getNumeroReferencia() {
        return $this->numeroReferencia;
    }
    
    /**
     *
     * @param string $numeroReferencia            
     */
    public function setNumeroReferencia($numeroReferencia) {
        $this->numeroReferencia = $numeroReferencia;
        return $this;
    }
    
    /**
     *
     * @return string
     */
    public function getEstadoPagoLinea() {
        return $this->estadoPagoLinea;
    }
    
    /**
     *
     * @param string $estadoPagoLinea            
     */
    public function setEstadoPagoLinea($estadoPagoLinea) {
        $this->estadoPagoLinea = $estadoPagoLinea;
        return $this;
    }
    
    /**
     *
     * @return string
     */
    public function getComentarioPagoLinea() {
        return $this->comentarioPagoLinea;
    }
    
    /**
     *
     * @param string $comentarioPagoLinea            
     */
    public function setComentarioPagoLinea($comentarioPagoLinea) {
        $this->comentarioPagoLinea = $comentarioPagoLinea;
        return $this;
    }
	    
    /**
     *
     * @return string
     */
    public function getUsrCreacion() {
        return $this->usrCreacion;
    }
    
    /**
     *
     * @param string $usrCreacion            
     */
    public function setUsrCreacion($usrCreacion) {
        $this->usrCreacion = $usrCreacion;
        return $this;
    }
    
    /**
     *
     * @return \DateTime
     */
    public function getFeCreacion() {
        return $this->feCreacion;
    }
    
    /**
     *
     * @param \DateTime $feCreacion            
     */
    public function setFeCreacion(\DateTime $feCreacion) {
        $this->feCreacion = $feCreacion;
        return $this;
    }
    
    /**
     *
     * @return string
     */
    public function getUsrUltMod() {
        return $this->usrUltMod;
    }
    
    /**
     *
     * @param string $usrUltMod            
     */
    public function setUsrUltMod($usrUltMod) {
        $this->usrUltMod = $usrUltMod;
        return $this;
    }
    
    /**
     *
     * @return \DateTime
     */
    public function getFeUltMod() {
        return $this->feUltMod;
    }
    
    /**
     *
     * @param \DateTime $feUltMod            
     */
    public function setFeUltMod(\DateTime $feUltMod) {
        $this->feUltMod = $feUltMod;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getReversado() {
        return $this->reversado;
    }
    
    /**
     *
     * @param string $reversado            
     */
    public function setReversado($reversado) {
        $this->reversado = $reversado;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getUsrEliminacion() {
        return $this->usrEliminacion;
    }
    
    /**
     *
     * @param string $usrEliminacion            
     */
    public function setUsrEliminacion($usrEliminacion) {
        $this->usrEliminacion = $usrEliminacion;
        return $this;
    }
    
    /**
     *
     * @return \DateTime
     */
    public function getFeEliminacion() {
        return $this->feEliminacion;
    }
    
    /**
     *
     * @param \DateTime $feEliminacion            
     */
    public function setFeEliminacion(\DateTime $feEliminacion) {
        $this->feEliminacion = $feEliminacion;
        return $this;
    }
    
    /**
     *
     * @return \DateTime
     */
    public function getFeTransaccion() {
        return $this->feTransaccion;
    }
    
    /**
     *
     * @param \DateTime $feEliminacion            
     */
    public function setFeTransaccion(\DateTime $feTransaccion) {
        $this->feTransaccion = $feTransaccion;
        return $this;
    }
}
