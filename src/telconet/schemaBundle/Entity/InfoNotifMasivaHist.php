<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoNotifMasivaHist
 *
 * @ORM\Table(name="INFO_NOTIF_MASIVA_HIST")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoNotifMasivaHistRepository")
 */
class InfoNotifMasivaHist
{

    /**
     * @var integer $intId
     *
     * @ORM\Column(name="ID_NOTIF_MASIVA_HIST", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_NOTIF_MASIVA_HIST", allocationSize=1, initialValue=1)
     */
    private $intId;

    /**
     * @var InfoNotifMasiva
     *
     * @ORM\ManyToOne(targetEntity="InfoNotifMasiva")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="NOTIF_MASIVA_ID", referencedColumnName="ID_NOTIF_MASIVA")
     * })
     */
    private $intNotifMasivaId;

    /**
     * @var string $strObservacion
     *
     * @ORM\Column(name="OBSERVACION", type="string", nullable=false)
     */
    private $strObservacion;

    /**
     * @var string $strAccion
     *
     * @ORM\Column(name="ACCION", type="string", nullable=false)
     */
    private $strAccion;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO_ENVIO_MASIVO", type="string", nullable=false)
     */
    private $strEstado;

    /**
     * @var string $usrCreacion
     *
     * @ORM\Column(name="USR_CREACION", type="string", nullable=false)
     */
    private $strUsrCreacion;

    /**
     * @var string $objFeCreacion
     *
     * @ORM\Column(name="FE_CREACION", type="string", nullable=false)
     */
    private $objFeCreacion;

    /**
     * @var string $strIpCreacion
     *
     * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
     */
    private $strIpCreacion;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->intId;
    }

    /**
     * Get intNotifMasivaId
     *
     * @return telconet\schemaBundle\Entity\InfoNotifMasiva
     */
    public function getNotifMasivaId()
    {
        return $this->intNotifMasivaId;
    }

    /**
     * Set intNotifMasivaId
     *
     * @param telconet\schemaBundle\Entity\InfoNotifMasiva $intNotifMasivaId
     */
    public function setNotifMasivaId(\telconet\schemaBundle\Entity\InfoNotifMasiva $intNotifMasivaId)
    {
        $this->intNotifMasivaId = $intNotifMasivaId;
    }

    /**
     * Get strObservacion
     *
     * @return string
     */
    public function getObservacion()
    {
        return $this->strObservacion;
    }

    /**
     * Set strObservacion
     *
     * @param string $strObservacion
     */
    public function setObservacion($strObservacion)
    {
        $this->strObservacion = $strObservacion;
    }

    /**
     * Get strAccion
     *
     * @return string
     */
    public function getAccion()
    {
        return $this->strAccion;
    }

    /**
     * Set strAccion
     *
     * @param string $strAccion
     */
    public function setAccion($strAccion)
    {
        $this->strAccion = $strAccion;
    }

    /**
     * Get strEstado
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->strEstado;
    }

    /**
     * Set strEstado
     *
     * @param string $strEstado
     */
    public function setEstado($strEstado)
    {
        $this->strEstado = $strEstado;
    }

    /**
     * Get strUsrCreacion
     *
     * @return string
     */
    public function getUsrCreacion()
    {
        return $this->strUsrCreacion;
    }

    /**
     * Set strUsrCreacion
     *
     * @param string $strUsrCreacion
     */
    public function setUsrCreacion($strUsrCreacion)
    {
        $this->strUsrCreacion = $strUsrCreacion;
    }

    /**
     * Get objFeCreacion
     *
     * @return 
     */
    public function getFeCreacion()
    {
        return $this->objFeCreacion;
    }

    /**
     * Set objFeCreacion
     *
     * @param string $objFeCreacion
     */
    public function setFeCreacion($objFeCreacion)
    {
        $this->objFeCreacion = $objFeCreacion;
    }

    /**
     * Get strIpCreacion
     *
     * @return string
     */
    public function getIpCreacion()
    {
        return $this->strIpCreacion;
    }

    /**
     * Set strIpCreacion
     *
     * @param string $strIpCreacion
     */
    public function setIpCreacion($strIpCreacion)
    {
        $this->strIpCreacion = $strIpCreacion;
    }

}
