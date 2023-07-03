<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiHilo
 *
 * @ORM\Table(name="ADMI_HILO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiHiloRepository")
 */
class AdmiHilo
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_HILO", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_HILO", allocationSize=1, initialValue=1)
     */
    private $id;
    
    /**
     * @var AdmiClaseTipoMedio
     *
     * @ORM\ManyToOne(targetEntity="AdmiClaseTipoMedio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CLASE_TIPO_MEDIO_ID", referencedColumnName="ID_CLASE_TIPO_MEDIO")
     * })
     */
    private $claseTipoMedioId;

    /**
     * @var integer $numeroHilo
     *
     * @ORM\Column(name="NUMERO_HILO", type="integer", nullable=true)
     */
    private $numeroHilo;

    /**
     * @var string $colorHilo
     *
     * @ORM\Column(name="COLOR_HILO", type="string", nullable=true)
     */
    private $colorHilo;

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
     * Get claseTipoMedioId
     *
     * @return telconet\schemaBundle\Entity\AdmiClaseTipoMedio
     */
    public function getClaseTipoMedioId()
    {
        return $this->claseTipoMedioId;
    }

    /**
     * Set claseTipoMedioId
     *
     * @param telconet\schemaBundle\Entity\AdmiClaseTipoMedio $claseTipoMedioId
     */
    public function setClaseTipoMedioId(\telconet\schemaBundle\Entity\AdmiClaseTipoMedio $claseTipoMedioId)
    {
        $this->claseTipoMedioId = $claseTipoMedioId;
    }

    /**
     * Get numeroHilo
     *
     * @return integer
     */
    public function getNumeroHilo()
    {
        return $this->numeroHilo;
    }

    /**
     * Set numeroHilo
     *
     * @param integer $numeroHilo
     */
    public function setNumeroHilo($numeroHilo)
    {
        $this->numeroHilo = $numeroHilo;
    }

    /**
     * Get colorHilo
     *
     * @return string
     */
    public function getColorHilo()
    {
        return $this->colorHilo;
    }

    /**
     * Set colorHilo
     *
     * @param string $colorHilo
     */
    public function setColorHilo($colorHilo)
    {
        $this->colorHilo = $colorHilo;
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
        return $this->colorHilo;
    }

}
