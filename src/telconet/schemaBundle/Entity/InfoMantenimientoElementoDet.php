<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoMantenimientoElementoDet
 *
 * @ORM\Table(name="INFO_MANTENIMIENTO_ELEMENT_DET")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoMantenimientoElementoDetRepository")
 */
class InfoMantenimientoElementoDet
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_MANTENIMIENTO_ELEMENT_DET", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_MANTENIMIENTO_ELE_DET", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer $mantenimientoElementoId
     *
     * @ORM\Column(name="MANTENIMIENTO_ELEMENTO_ID", type="integer", nullable=false)
     */
    private $mantenimientoElementoId;

    /**
     * @var integer $categoriaId
     *
     * @ORM\Column(name="CATEGORIA_ID", type="integer", nullable=false)
     */
    private $categoriaId;

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
     * @var float $valorTotal
     *
     * @ORM\Column(name="VALOR_TOTAL", type="float", nullable=true)
     */
    private $valorTotal;


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
     * Get mantenimientoElementoId
     *
     * @return integer
     */
    public function getMantenimientoElementoId()
    {
        return $this->mantenimientoElementoId;
    }

    /**
     * Set mantenimientoElementoId
     *
     * @param integer $mantenimientoElementoId
     */
    public function setMantenimientoElementoId($mantenimientoElementoId)
    {
        $this->mantenimientoElementoId = $mantenimientoElementoId;
    }

    /**
     * Get categoriaId
     *
     * @return integer
     */
    public function getCategoriaId()
    {
        return $this->categoriaId;
    }

    /**
     * Set categoriaId
     *
     * @param integer $categoriaId
     */
    public function setCategoriaId($categoriaId)
    {
        $this->categoriaId = $categoriaId;
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


}
