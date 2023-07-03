<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoTareaCaracteristica
 *
 * @ORM\Table(name="INFO_TAREA_CARACTERISTICA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoTareaCaracteristicaRepository")
 */
class InfoTareaCaracteristica
{
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_TAREA_CARACTERISTICA", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_TAREA_CARACTERISTICA", allocationSize=1, initialValue=1)
    */
    private $id;

    /**
     * @var integer $tareaId
     *
     * @ORM\Column(name="TAREA_ID", type="integer", nullable=false)
     */
    private $tareaId;

    /**
     * @var integer $detalleId
     *
     * @ORM\Column(name="DETALLE_ID", type="integer", nullable=false)
     */
    private $detalleId;

    /**
     * @var integer $caracteristicaId
     *
     * @ORM\Column(name="CARACTERISTICA_ID", type="integer", nullable=false)
     */
    private $caracteristicaId;

    /**
    * @var string $valor
    *
    * @ORM\Column(name="VALOR", type="string", nullable=false)
    */
    private $valor;

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
     * @var string $ipCreacion
     *
     * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
     */
    private $ipCreacion;

    /**
     * @var datetime $feModificacion
     *
     * @ORM\Column(name="FE_MODIFICACION", type="datetime", nullable=false)
     */
    private $feModificacion;

    /**
     * @var string $usrModificacion
     *
     * @ORM\Column(name="USR_MODIFICACION", type="string", nullable=false)
     */
    private $usrModificacion;

    /**
     * @var string $ipModificacion
     *
     * @ORM\Column(name="IP_MODIFICACION", type="string", nullable=false)
     */
    private $ipModificacion;

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
     * Get tareaId
     *
     * @return integer
     */
    public function getTareaId()
    {
        return $this->tareaId;
    }

    /**
     * Set tareaId
     *
     * @param integer $intTareaId
     */
    public function setTareaId($intTareaId)
    {
        $this->tareaId = $intTareaId;
    }

    /**
     * Get detalleId
     *
     * @return integer
     */
    public function getDetalleId()
    {
        return $this->detalleId;
    }

    /**
     * Set detalleId
     *
     * @param integer $intDetalleId
     */
    public function setDetalleId($intDetalleId)
    {
        $this->detalleId = $intDetalleId;
    }

    /**
     * Get caracteristicaId
     *
     * @return integer
     */
    public function getCaracteristicaId()
    {
        return $this->caracteristicaId;
    }

    /**
     * Set caracteristicaId
     *
     * @param integer $intCaracteristicaId
     */
    public function setCaracteristicaId($intCaracteristicaId)
    {
        $this->caracteristicaId = $intCaracteristicaId;
    }

    /**
     * Get valor
     *
     * @return string
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set valor
     *
     * @param string $strValor
     */
    public function setValor($strValor)
    {
        $this->valor = $strValor;
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
     * @param datetime $dateFeCreacion
     */
    public function setFeCreacion($dateFeCreacion)
    {
        $this->feCreacion = $dateFeCreacion;
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
     * @param string $strUsrCreacion
     */
    public function setUsrCreacion($strUsrCreacion)
    {
        $this->usrCreacion = $strUsrCreacion;
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
     * @param string $strIpCreacion
     */
    public function setIpCreacion($strIpCreacion)
    {
        $this->ipCreacion = $strIpCreacion;
    }

    /**
     * Get feModificacion
     *
     * @return datetime
     */
    public function getFeModificacion()
    {
        return $this->feModificacion;
    }

    /**
     * Set feModificacion
     *
     * @param datetime $dateFeModificacion
     */
    public function setFeModificacion($dateFeModificacion)
    {
        $this->feModificacion = $dateFeModificacion;
    }

    /**
     * Get usrModificacion
     *
     * @return string
     */
    public function getUsrModificacion()
    {
        return $this->usrModificacion;
    }

    /**
     * Set usrModificacion
     *
     * @param string $strUsrModificacion
     */
    public function setUsrModificacion($strUsrModificacion)
    {
        $this->usrModificacion = $strUsrModificacion;
    }

    /**
     * Get ipModificacion
     *
     * @return string
     */
    public function getIpModificacion()
    {
        return $this->ipModificacion;
    }

    /**
     * Set ipModificacion
     *
     * @param string $strIpModificacion
     */
    public function setIpModificacion($strIpModificacion)
    {
        $this->ipModificacion = $strIpModificacion;
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
     * @param string $strEstado
     */
    public function setEstado($strEstado)
    {
        $this->estado = $strEstado;
    }
}
