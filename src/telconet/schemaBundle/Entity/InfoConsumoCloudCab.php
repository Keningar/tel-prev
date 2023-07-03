<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoConsumoCloudCab
 *
 * @ORM\Table(name="INFO_CONSUMO_CLOUD_CAB")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoConsumoCloudCabRepository")
 */
class InfoConsumoCloudCab
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_CONSUMO_CLOUD_CAB", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_CONSUMO_CLOUD_CAB", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string $strNombre
     *
     * @ORM\Column(name="NOMBRE", type="string", nullable=true)
     */
    private $strNombre;

    /**
     * @var string $strObservacion
     *
     * @ORM\Column(name="OBSERVACION", type="string", nullable=true)
     */
    private $strObservacion;

    /**
     * @var integer $intPuntoId
     *
     * @ORM\Column(name="PUNTO_ID", type="integer", nullable=true)
     */
    private $intPuntoId;

    /**
     * @var integer $intPuntoFacturacionId
     *
     * @ORM\Column(name="PUNTO_FACTURACION_ID", type="integer", nullable=true)
     */
    private $intPuntoFacturacionId;

    /**
     * @var integer $$intServicioId
     *
     * @ORM\Column(name="SERVICIO_ID", type="integer", nullable=true)
     */
    private $intServicioId;

    /**
     * @var datetime $objFeConsumo
     *
     * @ORM\Column(name="FE_CONSUMO", type="datetime", nullable=true)
     */
    private $objFeConsumo;

    /**
     * @var string $strEstado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=true)
     */
    private $strEstado;

    /**
     * @var datetime $objFeCreacion
     *
     * @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
     */
    private $objFeCreacion;

    /**
     * @var string $objUsrCreacion
     *
     * @ORM\Column(name="USR_CREACION", type="string", nullable=true)
     */
    private $objUsrCreacion;

    /**
     * @var string $strIpCreacion
     *
     * @ORM\Column(name="IP_CREACION", type="string", nullable=true)
     */
    private $strIpCreacion;

    /**
     * @var datetime $objFeUltMod
     *
     * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
     */
    private $objFeUltMod;

    /**
     * @var string $strUsrUltMod
     *
     * @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
     */
    private $strUsrUltMod;

    /**
     * @var string $strIpUltMod
     *
     * @ORM\Column(name="IP_ULT_MOD", type="string", nullable=true)
     */
    private $strIpUltMod;

    /**
     * @var integer $intProcesoMasivoCabId
     *
     * @ORM\Column(name="PROCESO_MASIVO_CAB_ID", type="integer", nullable=true)
     */
    private $intProcesoMasivoCabId;

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
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->strNombre;
    }

    /**
     * Get observacion
     *
     * @return string
     */
    public function getObservacion()
    {
        return $this->strObservacion;
    }

    /**
     * Get puntoId
     *
     * @return integer
     */
    public function getPuntoId()
    {
        return $this->intPuntoId;
    }

    /**
     * Get puntoFacturacionId
     *
     * @return integer
     */
    public function getPuntoFacturacionId()
    {
        return $this->intPuntoFacturacionId;
    }

    /**
     * Get servicioId
     *
     * @return integer
     */
    public function getServicioId()
    {
        return $this->intServicioId;
    }

    /**
     * Get feConsumo
     *
     * @return 
     */
    public function getFeConsumo()
    {
        return $this->objFeConsumo;
    }

    /**
     * Get estado
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->strEstado;
    }

    /**
     * Get feCreacion
     *
     * @return 
     */
    public function getFeCreacion()
    {
        return $this->objFeCreacion;
    }

    /**
     * Get usrCreacion
     *
     * @return string
     */
    public function getUsrCreacion()
    {
        return $this->objUsrCreacion;
    }

    /**
     * Get ipCreacion
     *
     * @return string
     */
    public function getIpCreacion()
    {
        return $this->strIpCreacion;
    }

    /**
     * Get feUltMod
     *
     * @return 
     */
    public function getFeUltMod()
    {
        return $this->objFeUltMod;
    }

    /**
     * Get usrUltMod
     *
     * @return string
     */
    public function getUsrUltMod()
    {
        return $this->strUsrUltMod;
    }

    /**
     * Get ipUltMod
     *
     * @return string
     */
    public function getIpUltMod()
    {
        return $this->strIpUltMod;
    }

    /**
     * Get procesoMasivoCabId
     *
     * @return integer
     */
    public function getProcesoMasivoCabId()
    {
        return $this->intProcesoMasivoCabId;
    }

    /**
     * Set nombre
     * @param string $strNombre
     */
    public function setNombre($strNombre)
    {
        $this->strNombre = $strNombre;
    }

    /**
     * Set observacion
     *
     * @param string $strObservacion
     */
    public function setObservacion($strObservacion)
    {
        $this->strObservacion = $strObservacion;
    }

    /**
     * Set puntoId
     *
     * @param integer $intPuntoId
     */
    public function setPuntoId($intPuntoId)
    {
        $this->intPuntoId = $intPuntoId;
    }

    /**
     * Set puntoFacturacionId
     *
     * @param integer $intPuntoFacturacionId
     */
    public function setPuntoFacturacionId($intPuntoFacturacionId)
    {
        $this->intPuntoFacturacionId = $intPuntoFacturacionId;
    }

    /**
     * Set servicioId
     *
     * @param integer $intServicioId
     */
    public function setServicioId($intServicioId)
    {
        $this->intServicioId = $intServicioId;
    }

    /**
     * Set feConsumo
     *
     * @param  $objFeConsumo
     */
    public function setFeConsumo($objFeConsumo)
    {
        $this->objFeConsumo = $objFeConsumo;
    }

    /**
     * Set estado
     *
     * @param string $strEstado
     */
    public function setEstado($strEstado)
    {
        $this->strEstado = $strEstado;
    }

    /**
     * Set feCreacion
     *
     * @param  $objFeCreacion
     */
    public function setFeCreacion($objFeCreacion)
    {
        $this->objFeCreacion = $objFeCreacion;
    }

    /**
     * Set usrCreacion
     *
     * @param string $objUsrCreacion
     */
    public function setUsrCreacion($objUsrCreacion)
    {
        $this->objUsrCreacion = $objUsrCreacion;
    }

    /**
     * Set ipCreacion
     *
     * @param string $strIpCreacion
     */
    public function setIpCreacion($strIpCreacion)
    {
        $this->strIpCreacion = $strIpCreacion;
    }

    /**
     * Set feUltMod
     *
     * @param  $objFeUltMod
     */
    public function setFeUltMod(\DateTime $objFeUltMod)
    {
        $this->objFeUltMod = $objFeUltMod;
    }

    /**
     * Set usrUltMod
     *
     * @param string $strUsrUltMod
     */
    public function setUsrUltMod($strUsrUltMod)
    {
        $this->strUsrUltMod = $strUsrUltMod;
    }

    /**
     * Set ipUltMod
     *
     * @param string $strIpUltMod
     */
    public function setIpUltMod($strIpUltMod)
    {
        $this->strIpUltMod = $strIpUltMod;
    }

    /**
     * Set procesoMasivoCabId
     *
     * @param integer $intProcesoMasivoCabId
     */
    public function setProcesoMasivoCabId($intProcesoMasivoCabId)
    {
        $this->intProcesoMasivoCabId = $intProcesoMasivoCabId;
    }

}
