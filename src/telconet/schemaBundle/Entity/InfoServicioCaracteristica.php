<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoServicioCaracteristica
 *
 * @ORM\Table(name="INFO_SERVICIO_CARACTERISTICA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoServicioCaracteristicaRepository")
 */
class InfoServicioCaracteristica {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_SERVICIO_CARACTERISTICA", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_SERVICIO_CARAC", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var InfoServicio
     *
     * @ORM\ManyToOne(targetEntity="InfoServicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="SERVICIO_ID", referencedColumnName="ID_SERVICIO", nullable=false)
     * })
     */
    private $servicioId;

    /**
     * @var AdmiCaracteristica
     *
     * @ORM\ManyToOne(targetEntity="AdmiCaracteristica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CARACTERISTICA_ID", referencedColumnName="ID_CARACTERISTICA", nullable=false)
     * })
     */
    private $caracteristicaId;

    /**
     * @var string $valor
     *
     * @ORM\Column(name="VALOR", type="string", nullable=true)
     */
    private $valor;

    /**
     * @var string $feFacturacion
     *
     * @ORM\Column(name="FE_FACTURACION", type="datetime", nullable=true)
     */
    private $feFacturacion;

    /**
     * @var integer $cicloOrigenId
     *
     * @ORM\Column(name="CICLO_ORIGEN_ID", type="integer", nullable=true)
     */
    private $cicloOrigenId;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=true)
     */
    private $estado;

    /**
     * @var string $observacion
     *
     * @ORM\Column(name="OBSERVACION", type="string", nullable=true)
     */
    private $observacion;

    /**
     * @var string $feCreacion
     *
     * @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
     */
    private $feCreacion;

    /**
     * @var string $feUltMod
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
     * @var string $ipCreacion
     *
     * @ORM\Column(name="IP_CREACION", type="string", nullable=true)
     */
    private $ipCreacion;

    /**
     * @var string $ipUltMod
     *
     * @ORM\Column(name="IP_ULT_MOD", type="string", nullable=true)
     */
    private $ipUltMod;
    
    /**
     * @var integer $refIdServicioCaracteristica
     *
     * @ORM\Column(name="REF_ID_SERVICIO_CARACTERISTICA", type="integer", nullable=true)
     */
    private $refIdServicioCaracteristica;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get servicioId
     *
     * @return telconet\schemaBundle\Entity\InfoServicio
     */
    public function getServicioId() {
        return $this->servicioId;
    }

    /**
     * Set servicioId
     *
     * @param telconet\schemaBundle\Entity\InfoServicio $servicioId
     */
    public function setServicioId(\telconet\schemaBundle\Entity\InfoServicio $servicioId) {
        $this->servicioId = $servicioId;
    }

    /**
     * Get caracteristicaId
     *
     * @return telconet\schemaBundle\Entity\AdmiCaracteristica
     */
    public function getCaracteristicaId() {
        return $this->caracteristicaId;
    }

    /**
     * Set caracteristicaId
     *
     * @param telconet\schemaBundle\Entity\AdmiCaracteristica $caracteristicaId
     */
    public function setCaracteristicaId(\telconet\schemaBundle\Entity\AdmiCaracteristica $caracteristicaId) {
        $this->caracteristicaId = $caracteristicaId;
    }

    /**
     * Get valor
     *
     * @return string
     */
    public function getValor() {
        return $this->valor;
    }

    /**
     * Set valor
     *
     * @param string $valor
     */
    public function setValor($valor) {
        $this->valor = $valor;
    }

    /**
     * Get feFacturacion
     *
     * @return datetime
     */
    public function getFeFacturacion() {
        return $this->feFacturacion;
    }

    /**
     * Set feCreacion
     *
     * @param datetime $feFacturacion
     */
    public function setFeFacturacion($feFacturacion) {
        $this->feFacturacion = $feFacturacion;
    }

    /**
     * Get cicloOrigenId
     *
     * @return integer
     */
    public function getCicloOrigenId() {
        return $this->cicloOrigenId;
    }

    /**
     * Set cicloOrigenId
     *
     * @param integer $cicloOrigenId
     */
    public function setCicloOrigenId($cicloOrigenId) {
        $this->cicloOrigenId = $cicloOrigenId;
    }

    /**
     * Get feCreacion
     *
     * @return datetime
     */
    public function getFeCreacion() {
        return $this->feCreacion;
    }

    /**
     * Set feCreacion
     *
     * @param datetime $feCreacion
     */
    public function setFeCreacion($feCreacion) {
        $this->feCreacion = $feCreacion;
    }

    /**
     * Get feUltMod
     *
     * @return datetime
     */
    public function getFeUltMod() {
        return $this->feUltMod;
    }

    /**
     * Set feUltMod
     *
     * @param datetime $feUltMod
     */
    public function setFeUltMod($feUltMod) {
        $this->feUltMod = $feUltMod;
    }

    /**
     * Get usrCreacion
     *
     * @return string
     */
    public function getUsrCreacion() {
        return $this->usrCreacion;
    }

    /**
     * Set usrCreacion
     *
     * @param string $usrCreacion
     */
    public function setUsrCreacion($usrCreacion) {
        $this->usrCreacion = $usrCreacion;
    }

    /**
     * Get usrUltMod
     *
     * @return string
     */
    public function getUsrUltMod() {
        return $this->usrUltMod;
    }

    /**
     * Set usrUltMod
     *
     * @param string $usrUltMod
     */
    public function setUsrUltMod($usrUltMod) {
        $this->usrUltMod = $usrUltMod;
    }

    /**
     * Get estado
     *
     * @return string
     */
    public function getEstado() {
        return $this->estado;
    }

    /**
     * Set estado
     *
     * @param string $estado
     */
    public function setEstado($estado) {
        $this->estado = $estado;
    }

    /**
     * Get observacion
     *
     * @return string
     */
    public function getObservacion() {
        return $this->observacion;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     */
    public function setObservacion($observacion) {
        $this->observacion = $observacion;
    }

    /**
     * Get ipCreacion
     *
     * @return string
     */
    public function getIpCreacion() {
        return $this->ipCreacion;
    }

    /**
     * Set ipCreacion
     *
     * @param string $ipCreacion
     */
    public function setIpCreacion($ipCreacion) {
        $this->ipCreacion = $ipCreacion;
    }

    /**
     * Get ipUltMod
     *
     * @return string
     */
    public function getIpUltMod() {
        return $this->ipUltMod;
    }

    /**
     * Set ipUltMod
     *
     * @param string $ipUltMod
     */
    public function setIpUltMod($ipUltMod) {
        $this->ipUltMod = $ipUltMod;
    }
    
    /**
     * Get refIdServicioCaracteristica
     *
     * @return integer
     */
    public function getRefIdServicioCaracteristica()
    {
        return $this->refIdServicioCaracteristica;
    }

    /**
     * Set refIdServicioCaracteristica
     *
     * @param integer $refIdServicioCaracteristica
     */
    public function setRefIdServicioCaracteristica($refIdServicioCaracteristica)
    {
        $this->refIdServicioCaracteristica = $refIdServicioCaracteristica;
    }

}
