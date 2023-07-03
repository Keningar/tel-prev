<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoReporteHistorico
 *
 * @ORM\Table(name="INFO_REPORTE_HISTORICO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoReporteHistoricoRepository")
 */
class InfoReporteHistorico
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_REPORTE_HISTORICO", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_REPORTE_HISTORICO", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var $caracteristicaId
     *
     * @ORM\ManyToOne(targetEntity="AdmiCaracteristica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CARACTERISTICA_ID", referencedColumnName="ID_CARACTERISTICA", nullable=false)
     * })
     */
    private $caracteristicaId;

    /**
     * @var string $parametros
     *
     * @ORM\Column(name="PARAMETROS", type="string", nullable=false)
     */
    private $parametros;

    /**
     * @var integer $cantidadRegistros
     *
     * @ORM\Column(name="CANTIDAD_REGISTROS", type="integer", nullable=false)
     */
    private $cantidadRegistros;

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
     * Get parametros
     *
     * @return string
     */
    public function getParametros()
    {
        return $this->parametros;
    }

    /**
     * Set setParametros
     *
     * @param string $parametros
     */
    public function setParametros($parametros)
    {
        $this->parametros = $parametros;
    }

    /**
     * Get cantidadRegistros
     *
     * @return integer
     */
    public function getCantidadRegistros()
    {
        return $this->cantidadRegistros;
    }

    /**
     * Set cantidadRegistros
     *
     * @param integer $cantidadRegistros
     */
    public function setCantidadRegistros($cantidadRegistros)
    {
        $this->cantidadRegistros = $cantidadRegistros;
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
     * @param  datetime $feCreacion
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
        return $this->parametros;
    }

}
