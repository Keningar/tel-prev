<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiBuffer
 *
 * @ORM\Table(name="ADMI_BUFFER")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiBufferRepository")
 */
class AdmiBuffer
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_BUFFER", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_BUFFER", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer $numeroBuffer
     *
     * @ORM\Column(name="NUMERO_BUFFER", type="integer", nullable=true)
     */
    private $numeroBuffer;

    /**
     * @var string $colorBuffer
     *
     * @ORM\Column(name="COLOR_BUFFER", type="string", nullable=true)
     */
    private $colorBuffer;

    /**
     * @var string $descripcionBuffer
     *
     * @ORM\Column(name="DESCRIPCION_BUFFER", type="string", nullable=true)
     */
    private $descripcionBuffer;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=false)
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
     * @var string $usrUltMod
     *
     * @ORM\Column(name="USR_ULT_MOD", type="string", nullable=false)
     */
    private $usrUltMod;

    /**
     * @var datetime $feUltMod
     *
     * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
     */
    private $feUltMod;

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
     * Get numeroBuffer
     *
     * @return integer
     */
    public function getNumeroBuffer()
    {
        return $this->numeroBuffer;
    }

    /**
     * Set numeroBuffer
     *
     * @param integer $numeroBuffer
     */
    public function setNumeroBuffer($numeroBuffer)
    {
        $this->numeroBuffer = $numeroBuffer;
    }

    /**
     * Get colorBuffer
     *
     * @return string
     */
    public function getColorBuffer()
    {
        return $this->colorBuffer;
    }

    /**
     * Set colorBuffer
     *
     * @param string $colorBuffer
     */
    public function setColorBuffer($colorBuffer)
    {
        $this->colorBuffer = $colorBuffer;
    }

    /**
     * Get descripcionBuffer
     *
     * @return string
     */
    public function getDescripcionBuffer()
    {
        return $this->descripcionBuffer;
    }

    /**
     * Set descripcionBuffer
     *
     * @param string $descripcionBuffer
     */
    public function setDescripcionBuffer($descripcionBuffer)
    {
        $this->descripcionBuffer = $descripcionBuffer;
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
     * Set feUltMod
     *
     * @param datetime $feUltMod
     */
    public function setFeUltMod($feUltMod)
    {
        $this->feUltMod = $feUltMod;
    }

    public function __toString()
    {
        return $this->numeroBuffer;
    }

}
