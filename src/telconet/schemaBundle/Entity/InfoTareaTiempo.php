<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoTareaTiempo
 *
 * @ORM\Table(name="INFO_TAREA_TIEMPO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoTareaTiempoRepository")
 */
class InfoTareaTiempo
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_TAREA_TIEMPO", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_TAREA_TIEMPO", allocationSize=1, initialValue=1)
     */

    private $id;
    /**
     * @var InfoDetalle
     *
     * @ORM\ManyToOne(targetEntity="InfoDetalle")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="DETALLE_ID", referencedColumnName="ID_DETALLE")
     * })
     */

    private $detalleId;

    /**
     * @var integer $totalTiempoAsignada
     *
     * @ORM\Column(name="TOTAL_TIEMPO_ASIGNADA", type="integer", nullable=true)
     */

    private $totalTiempoAsignada;

    /**
     * @var string $totalTiempoAceptada
     *
     * @ORM\Column(name="TOTAL_TIEMPO_ACEPTADA", type="integer", nullable=true)
     */

    private $totalTiempoAceptada;

    /**
     * @var string totalTiempoPausada
     * @ORM\Column(name="TOTAL_TIEMPO_PAUSADA",type="integer",nullable=true)
     *
     */
    private $totalTiempoPausada;

    /**
     * @var string totalTiempoReprogramada
     * @ORM\Column(name="TOTAL_TIEMPO_REPROGRAMADA",type="integer",nullable=true)
     *
     */
    private $totalTiempoReprogramada;

    /**
     * @var string detHistorialIdUltAsignadda
     * @ORM\Column(name="DET_HIST_ID_ULT_ASIGNADA",type="integer",nullable=true)
     *
     */
    private $detHistorialIdUltAsignadda;

    /**
     * @var string $detHistorialIdUltAceptada
     * @ORM\Column(name="DET_HIST_ID_ULT_ACEPTADA",type="integer",nullable=true)
     *
     */
    private $detHistorialIdUltAceptada;

    /**
     * @var string $detHistorialIdUltPausada
     * @ORM\Column(name="DET_HIST_ID_ULT_PAUSADA",type="integer",nullable=true)
     *
     */
    private $detHistorialIdUltPausada;

    /**
     * @var string $detHistorialIdUltReprogramada
     * @ORM\Column(name="DET_HIST_ID_ULT_REPROGRAMADA",type="integer",nullable=true)
     *
     */
    private $detHistorialIdUltReprogramada;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=false)
     */

    private $estado;


    /**
     * @var string usrCreacion
     * @ORM\Column(name="USR_CREACION",type="string",nullable=true)
     *
     */
    private $usrCreacion;

    /**
     * @var string usrUltMod
     * @ORM\Column(name="USR_ULT_MOD",type="string",nullable=true)
     *
     */
    private $usrUltMod;


    /**
     * @var datetime $feCreacion
     *
     * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
     */

    private $feCreacion;


    /**
     * @var datetime $feUltMod
     *
     * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
     */

    private $feUltMod;

    /**
     * @var string $ipCreacion
     *
     * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
     */
    private $ipCreacion;

    /**
     * @var string $ipUltMod
     *
     * @ORM\Column(name="IP_ULT_MOD", type="string", nullable=true)
     */
    private $ipUltMod;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return InfoDetalle
     */
    public function getDetalleId()
    {
        return $this->detalleId;
    }

    /**
     * @param InfoDetalle $detalleId
     */
    public function setDetalleId($detalleId)
    {
        $this->detalleId = $detalleId;
    }

    /**
     * @return int
     */
    public function getTotalTiempoAsignada()
    {
        return $this->totalTiempoAsignada;
    }

    /**
     * @param int $totalTiempoAsignada
     */
    public function setTotalTiempoAsignada($totalTiempoAsignada)
    {
        $this->totalTiempoAsignada = $totalTiempoAsignada;
    }

    /**
     * @return string
     */
    public function getTotalTiempoAceptada()
    {
        return $this->totalTiempoAceptada;
    }

    /**
     * @param string $totalTiempoAceptada
     */
    public function setTotalTiempoAceptada($totalTiempoAceptada)
    {
        $this->totalTiempoAceptada = $totalTiempoAceptada;
    }

    /**
     * @return string
     */
    public function getTotalTiempoPausada()
    {
        return $this->totalTiempoPausada;
    }

    /**
     * @param string $totalTiempoPausada
     */
    public function setTotalTiempoPausada($totalTiempoPausada)
    {
        $this->totalTiempoPausada = $totalTiempoPausada;
    }

    /**
     * @return string
     */
    public function getTotalTiempoReprogramada()
    {
        return $this->totalTiempoReprogramada;
    }

    /**
     * @param string $totalTiempoReprogramada
     */
    public function setTotalTiempoReprogramada($totalTiempoReprogramada)
    {
        $this->totalTiempoReprogramada = $totalTiempoReprogramada;
    }

    /**
     * @return string
     */
    public function getDetHistorialIdUltAsignadda()
    {
        return $this->detHistorialIdUltAsignadda;
    }

    /**
     * @param string $detHistorialIdUltAsignadda
     */
    public function setDetHistorialIdUltAsignadda($detHistorialIdUltAsignadda)
    {
        $this->detHistorialIdUltAsignadda = $detHistorialIdUltAsignadda;
    }

    /**
     * @return string
     */
    public function getDetHistorialIdUltAceptada()
    {
        return $this->detHistorialIdUltAceptada;
    }

    /**
     * @param string $detHistorialIdUltAceptada
     */
    public function setDetHistorialIdUltAceptada($detHistorialIdUltAceptada)
    {
        $this->detHistorialIdUltAceptada = $detHistorialIdUltAceptada;
    }

    /**
     * @return string
     */
    public function getDetHistorialIdUltPausada()
    {
        return $this->detHistorialIdUltPausada;
    }

    /**
     * @param string $detHistorialIdUltPausada
     */
    public function setDetHistorialIdUltPausada($detHistorialIdUltPausada)
    {
        $this->detHistorialIdUltPausada = $detHistorialIdUltPausada;
    }

    /**
     * @return string
     */
    public function getDetHistorialIdUltReprogramada()
    {
        return $this->detHistorialIdUltReprogramada;
    }

    /**
     * @param string $detHistorialIdUltReprogramada
     */
    public function setDetHistorialIdUltReprogramada($detHistorialIdUltReprogramada)
    {
        $this->detHistorialIdUltReprogramada = $detHistorialIdUltReprogramada;
    }

    /**
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param string $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * @return string
     */
    public function getUsrCreacion()
    {
        return $this->usrCreacion;
    }

    /**
     * @param string $usrCreacion
     */
    public function setUsrCreacion($usrCreacion)
    {
        $this->usrCreacion = $usrCreacion;
    }

    /**
     * @return string
     */
    public function getUsrUltMod()
    {
        return $this->usrUltMod;
    }

    /**
     * @param string $usrUltMod
     */
    public function setUsrUltMod($usrUltMod)
    {
        $this->usrUltMod = $usrUltMod;
    }

    /**
     * @return datetime
     */
    public function getFeCreacion()
    {
        return $this->feCreacion;
    }

    /**
     * @param datetime $feCreacion
     */
    public function setFeCreacion($feCreacion)
    {
        $this->feCreacion = $feCreacion;
    }

    /**
     * @return datetime
     */
    public function getFeUltMod()
    {
        return $this->feUltMod;
    }

    /**
     * @param datetime $feUltMod
     */
    public function setFeUltMod($feUltMod)
    {
        $this->feUltMod = $feUltMod;
    }

    /**
     * @return string
     */
    public function getIpCreacion()
    {
        return $this->ipCreacion;
    }

    /**
     * @param string $ipCreacion
     */
    public function setIpCreacion($ipCreacion)
    {
        $this->ipCreacion = $ipCreacion;
    }

    /**
     * @return string
     */
    public function getIpUltMod()
    {
        return $this->ipUltMod;
    }

    /**
     * @param string $ipUltMod
     */
    public function setIpUltMod($ipUltMod)
    {
        $this->ipUltMod = $ipUltMod;
    }

}