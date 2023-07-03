<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoCupoPlanificacion
 *
 * @ORM\Table(name="INFO_CUPO_PLANIFICACION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoCupoPlanificacionRepository")
 */
class InfoCupoPlanificacion {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_CUPO_PLANIFICACION", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_CUPO_PLANIFICACION", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var datetime $feInicio
     *
     * @ORM\Column(name="FE_INICIO", type="datetime", nullable=false)
     */
    private $feInicio;

    /**
     * @var datetime $feFin
     *
     * @ORM\Column(name="FE_FIN", type="datetime", nullable=false)
     */
    private $feFin;

    /**
     * @var integer $solicitudId
     *
     * @ORM\Column(name="SOLICITUD_ID", type="integer", nullable=true)
     */
    private $solicitudId;

    /**
     * @var integer $cuadrillaId
     *
     * @ORM\Column(name="CUADRILLA_ID", type="integer", nullable=true)
     */
    private $cuadrillaId;

    /**
     * @var datetime $feCreacion
     *
     * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
     */
    private $feCreacion;

    /**
     * @var string $usrCreacion
     *
     * @ORM\Column(name="USR_CREACION", type="string", nullable=false)
     */
    private $usrCreacion;

    /**
     * @var string $ipCreacion
     *
     * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
     */
    private $ipCreacion;

    /**
     * @var datetime $feModificacion
     *
     * @ORM\Column(name="FE_MODIFICACION", type="datetime", nullable=true)
     */
    private $feModificacion;

    /**
     * @var string $usrModificacion
     *
     * @ORM\Column(name="USR_MODIFICACION", type="string", nullable=true)
     */
    private $usrModificacion;

    /**
     * @var integer $jurisdiccionId
     *
     * @ORM\Column(name="JURISDICCION_ID", type="integer", nullable=true)
     */
    private $jurisdicionId;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get feInicio
     *
     * @return datetime
     */
    public function getFeInicio() {
        return $this->feInicio;
    }

    /**
     * Set feInicio
     *
     * @param datetime $feInicio
     */
    public function setFeInicio($feInicio) {
        $this->feInicio = $feInicio;
    }

    /**
     * Get feFin
     *
     * @return datetime
     */
    public function getFeFin() {
        return $this->feFin;
    }

    /**
     * Set feFin
     *
     * @param datetime $feFin
     */
    public function setFeFin($feFin) {
        $this->feFin = $feFin;
    }

    /**
     * Get solicitudId
     *
     * @return integer
     */
    public function getSolicitudId() {
        return $this->solicitudId;
    }

    /**
     * Set solicitudId
     *
     * @param integer $solicitudId
     */
    public function setSolicitudId($solicitudId) {
        $this->solicitudId = $solicitudId;
    }

    /**
     * Get cuadrillaId
     *
     * @return integer
     */
    public function getCuadrillaId() {
        return $this->cuadrillaId;
    }

    /**
     * Set cuadrillaId
     *
     * @param integer $cuadrillaId
     */
    public function setCuadrillaId($cuadrillaId) {
        $this->cuadrillaId = $cuadrillaId;
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
     * Get feModificacion
     *
     * @return datetime
     */
    public function getFeModificacion() {
        return $this->feModificacion;
    }

    /**
     * Set feModificacion
     *
     * @param datetime $feModificacion
     */
    public function setFeModficacion($feModificacion) {
        $this->feModificacion = $feModificacion;
    }

    /**
     * Get usrModificacion
     *
     * @return string
     */
    public function getUsrModificacion() {
        return $this->usrModificacion;
    }

    /**
     * Set usrModificacion
     *
     * @param string $usrModificacion
     */
    public function setUsrModificacion($usrModificacion) {
        $this->usrModificacion = $usrModificacion;
    }

    /**
     * Get jurisdiccionId
     *
     * @return integer
     */
    public function getJurisdiccionId() {
        return $this->jurisdiccionId;
    }

    /**
     * Set jurisdiccionId
     *
     * @param integer $jurisdiccionId
     */
    public function setJurisdiccionId($jurisdiccionId) {
        $this->jurisdiccionId = $jurisdiccionId;
    }

}
