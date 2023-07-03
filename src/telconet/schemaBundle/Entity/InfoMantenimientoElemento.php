<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoMantenimientoElemento
 *
 * @ORM\Table(name="INFO_MANTENIMIENTO_ELEMENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoMantenimientoElementoRepository")
 */
class InfoMantenimientoElemento
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_MANTENIMIENTO_ELEMENTO", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_MANTENIMIENTO_ELEMENT", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer $elementoId
     *
     * @ORM\Column(name="ELEMENTO_ID", type="integer", nullable=false)
     */
    private $elementoId;

    /**
     * @var integer $ordenTrabajoId
     *
        * @ORM\Column(name="ORDEN_TRABAJO_ID", type="integer", nullable=false)
     */
    private $ordenTrabajoId;

    /**
     * @var float $valorTotal
     *
     * @ORM\Column(name="VALOR_TOTAL", type="float", nullable=true)
     */
    private $valorTotal;

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
     * @var string $usrCreacion
     *
     * @ORM\Column(name="USR_CREACION", type="string", nullable=false)
     */
    private $usrCreacion;

    /**
     * @var datetime $feCreacion
     *
     * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
     */
    private $feCreacion;

    /**
     * @var string $ipCreacion
     *
     * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
     */
    private $ipCreacion;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=false)
     */
    private $estado;

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
     * Get elementoId
     *
     * @return integer
     */
    public function getElementoId()
    {
        return $this->elementoId;
    }

    /**
     * Set elementoId
     *
     * @param integer $elementoId
     */
    public function setElementoId($elementoId)
    {
        $this->elementoId = $elementoId;
    }

    /**
     * Get ordenTrabajoId
     *
     * @return integer
     */
    public function getOrdenTrabajoId()
    {
        return $this->planMantenimientoId;
    }

    /**
     * Set ordenTrabajoId
     *
     * @param integer $ordenTrabajoId
     */
    public function setOrdenTrabajoId($ordenTrabajoId)
    {
        $this->ordenTrabajoId = $ordenTrabajoId;
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
     * Get feInicio
     *
     * @return datetime
     */
    public function getFeInicio()
    {
        return $this->feInicio;
    }

    /**
     * Set feInicio
     *
     * @param datetime $feInicio
     */
    public function setFeInicio($feInicio)
    {
        $this->feInicio = $feInicio;
    }

    /**
     * Get feFin
     *
     * @return datetime
     */
    public function getFeFin()
    {
        return $this->feFin;
    }

    /**
     * Set feFin
     *
     * @param datetime $feFin
     */
    public function setFeFin($feFin)
    {
        $this->feFin = $feFin;
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
     * Get ipCreacion
     *
     * @return string
     */
    public function getIpCreacion()
    {
        return $this->ipCreacion;
    }

    /**
     * Set ipCreacion
     *
     * @param string $ipCreacion
     */
    public function setIpCreacion($ipCreacion)
    {
        $this->ipCreacion = $ipCreacion;
    }

    /**
     * Get estado
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set estado
     *
     * @param string $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

}
