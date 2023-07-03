<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiClaseTipoMedio
 *
 * @ORM\Table(name="INFO_BUFFER_HILO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoBufferHiloRepository")
 */
class InfoBufferHilo
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_BUFFER_HILO", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_BUFFER_HILO", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var AdmiHilo
     *
     * @ORM\ManyToOne(targetEntity="AdmiHilo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="HILO_ID", referencedColumnName="ID_HILO")
     * })
     */
    private $hiloId;
    
    /**
     * @var AdmiBuffer
     *
     * @ORM\ManyToOne(targetEntity="AdmiBuffer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="BUFFER_ID", referencedColumnName="ID_BUFFER")
     * })
     */
    private $bufferId;

    /**
     * @var string $empresaCod
     *
     * @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
     */
    private $empresaCod;

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
     * @var string $ipCreacion
     *
     * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
     */
    private $ipCreacion;

    
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
     * Get hiloId
     *
     * @return telconet\schemaBundle\Entity\AdmiHilo
     */
    public function getHiloId()
    {
        return $this->hiloId;
    }

    /**
     * Set hiloId
     *
     * @param telconet\schemaBundle\Entity\AdmiHilo $hiloId
     */
    public function setHiloId(\telconet\schemaBundle\Entity\AdmiHilo $hiloId)
    {
        $this->hiloId = $hiloId;
    }
    
    /**
     * Get bufferId
     *
     * @return telconet\schemaBundle\Entity\AdmiBuffer
     */
    public function getBufferId()
    {
        return $this->bufferId;
    }

    /**
     * Set bufferId
     *
     * @param telconet\schemaBundle\Entity\AdmiBuffer $bufferId
     */
    public function setBufferId(\telconet\schemaBundle\Entity\AdmiBuffer $bufferId)
    {
        $this->bufferId = $bufferId;
    }

    /**
     * Get empresaCod
     *
     * @return string
     */
    public function getEmpresaCod()
    {
        return $this->empresaCod;
    }

    /**
     * Set empresaCod
     *
     * @param string $empresaCod
     */
    public function setEmpresaCod($empresaCod)
    {
        $this->empresaCod = $empresaCod;
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

    public function __toString()
    {
        return $this->id;
    }

}
