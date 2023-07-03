<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiTag
 *
 * @ORM\Table(name="ADMI_TAG")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiTagRepository")
 */
class AdmiTag
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_TAG", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_TAG", allocationSize=1, initialValue=1)
     */
    private $id;
    private $elementoId;

    /**
     * @var string $descripcion
     *
     * @ORM\Column(name="DESCRIPCION", type="string", nullable=true)
     */
    private $descripcion;

    /**
     * @var string $observacion
     *
     * @ORM\Column(name="OBSERVACION", type="string", nullable=true)
     */
    private $observacion;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=true)
     */
    private $estado;

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
     * @var string $usrUltMod
     *
     * @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
     */
    private $usrUltMod;

    /**
     * @var datetime $feUltMod
     *
     * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
     */
    private $feUltMod;

    /**
     * @var datetime $ipUltMod
     *
     * @ORM\Column(name="IP_ULT_MOD", type="string", nullable=true)
     */
    private $ipUltMod;

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
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Get elementoId
     *
     * @return string
     */
    public function getElementoId()
    {
        return $this->elementoId;
    }

    /**
     * Set descripcion
     *
     * @param string $elementoId
     */
    public function setElementoId($elementoId)
    {
        $this->elementoId = $elementoId;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
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
     * Get IpCreacion
     *
     * @return string
     */
    public function getIpCreacion()
    {
        return $this->IpCreacion;
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
     * Get usrUltMod
     *
     * @return string
     */
    public function getUsrUltMod()
    {
        return $this->usrUltMod;
    }

    /**
     * Set usrUltMod
     *
     * @param string $usrUltMod
     */
    public function setUsrUltMod($usrUltMod)
    {
        $this->usrUltMod = $usrUltMod;
    }

    /**
     * Get feUltMod
     *
     * @return datetime
     */
    public function getFeUltMod()
    {
        return $this->feUltMod;
    }

    /**
     * Set ipUltMod
     *
     * @param string $ipUltMod
     */
    public function setIpUltMod($ipUltMod)
    {
        $this->ipUltMod = $ipUltMod;
    }

    /**
     * Get ipUltMod
     *
     * @return string
     */
    public function getIpUltMod()
    {
        return $this->ipUltMod;
    }

    /**
     * Set feUltMod
     *
     * @param datetime $feUltMod
     */
    public function setFeUltMod($feUltMod)
    {
        $this->feUltMod = $feUltMod;
    }

}
