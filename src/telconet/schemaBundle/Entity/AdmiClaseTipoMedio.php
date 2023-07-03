<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiClaseTipoMedio
 *
 * @ORM\Table(name="ADMI_CLASE_TIPO_MEDIO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiClaseTipoMedioRepository")
 */
class AdmiClaseTipoMedio
{

    /**
     * @var datetime $feUltMod
     *
     * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
     */
    private $feUltMod;

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_CLASE_TIPO_MEDIO", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_CLASE_TIPO_MEDIO", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var AdmiTipoMedio
     *
     * @ORM\ManyToOne(targetEntity="AdmiTipoMedio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="TIPO_MEDIO_ID", referencedColumnName="ID_TIPO_MEDIO")
     * })
     */
    private $tipoMedioId;

    /**
     * @var string $nombreClaseTipoMedio
     *
     * @ORM\Column(name="NOMBRE_CLASE_TIPO_MEDIO", type="string", nullable=false)
     */
    private $nombreClaseTipoMedio;

    /**
     * @var string $descripcionClaseTipoMedio
     *
     * @ORM\Column(name="DESCRIPCION_CLASE_TIPO_MEDIO", type="string", nullable=true)
     */
    private $descripcionClaseTipoMedio;

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
     * Get tipoMedioId
     *
     * @return telconet\schemaBundle\Entity\AdmiTipoMedio
     */
    public function getTipoMedioId()
    {
        return $this->tipoMedioId;
    }

    /**
     * Set tipoMedioId
     *
     * @param telconet\schemaBundle\Entity\AdmiTipoMedio $tipoMedioId
     */
    public function setTipoMedioId(\telconet\schemaBundle\Entity\AdmiTipoMedio $tipoMedioId)
    {
        $this->tipoMedioId = $tipoMedioId;
    }

    /**
     * Get nombreClaseTipoMedio
     *
     * @return string
     */
    public function getNombreClaseTipoMedio()
    {
        return $this->nombreClaseTipoMedio;
    }

    /**
     * Set nombreClaseTipoMedio
     *
     * @param string $nombreClaseTipoMedio
     */
    public function setNombreClaseTipoMedio($nombreClaseTipoMedio)
    {
        $this->nombreClaseTipoMedio = $nombreClaseTipoMedio;
    }

    /**
     * Get descripcionClaseTipoMedio
     *
     * @return string
     */
    public function getDescripcionClaseTipoMedio()
    {
        return $this->descripcionClaseTipoMedio;
    }

    /**
     * Set descripcionClaseTipoMedio
     *
     * @param string $descripcionClaseTipoMedio
     */
    public function setDescripcionClaseTipoMedio($descripcionClaseTipoMedio)
    {
        $this->descripcionClaseTipoMedio = $descripcionClaseTipoMedio;
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

    public function __toString()
    {
        return $this->nombreClaseTipoMedio;
    }

}
