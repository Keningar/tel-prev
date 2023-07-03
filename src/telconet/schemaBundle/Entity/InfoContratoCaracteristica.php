<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoContratoCaracteristica
 *
 * @ORM\Table(name="INFO_CONTRATO_CARACTERISTICA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoContratoCaracteristicaRepository")
 */
class InfoContratoCaracteristica
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_CONTRATO_CARACTERISTICA", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_CONTRATO_CARAC", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var InfoContrato
     *
     * @ORM\ManyToOne(targetEntity="InfoContrato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CONTRATO_ID", referencedColumnName="ID_CONTRATO")
     * })
     */
    private $contratoId;

    /**
     * @var AdmiCaracteristica
     *
     * @ORM\ManyToOne(targetEntity="AdmiCaracteristica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CARACTERISTICA_ID", referencedColumnName="ID_CARACTERISTICA")
     * })
     */
    private $caracteristicaId;

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
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=false)
     */
    private $estado;

    /**
     * @var string $valor1
     *
     * @ORM\Column(name="VALOR1", type="string", nullable=true)
     */
    private $valor1;

    /**
     * @var string $valor2
     *
     * @ORM\Column(name="VALOR2", type="string", nullable=true)
     */
    private $valor2;

    /**
     * @var string $ipCreacion
     *
     * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
     */
    private $ipCreacion;

    /**
     * @var datetime $feUltMod
     *
     * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
     */
    private $feUltMod;

    /**
     * @var string $usrUltMod
     *
     * @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
     */
    private $usrUltMod;

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
     * Get contratoId
     *
     * @return telconet\schemaBundle\Entity\InfoContrato
     */
    public function getContratoId()
    {
        return $this->contratoId;
    }

    /**
     * Set contratoId
     *
     * @param telconet\schemaBundle\Entity\InfoContrato $contratoId
     */
    public function setContratoId(\telconet\schemaBundle\Entity\InfoContrato $contratoId)
    {
        $this->contratoId = $contratoId;
    }

    /**
     * Get caracteristicaId
     *
     * @return telconet\schemaBundle\Entity\AdmiCaracteristica
     */
    public function getCaracteristicaId()
    {
        return $this->caracteristicaId;
    }

    /**
     * Set caracteristicaId
     *
     * @param telconet\schemaBundle\Entity\AdmiCaracteristica $caracteristicaId
     */
    public function setCaracteristicaId(\telconet\schemaBundle\Entity\AdmiCaracteristica $caracteristicaId)
    {
        $this->caracteristicaId = $caracteristicaId;
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
     * Get valor1
     *
     * @return string
     */
    public function getValor1()
    {
        return $this->valor1;
    }

    /**
     * Set valor1
     *
     * @param string $valor1
     */
    public function setValor1($valor1)
    {
        $this->valor1 = $valor1;
    }

    /**
     * Get valor2
     *
     * @return string
     */
    public function getValor2()
    {
        return $this->valor2;
    }

    /**
     * Set valor2
     *
     * @param string $valor2
     */
    public function setValor2($valor2)
    {
        $this->valor2 = $valor2;
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

}
