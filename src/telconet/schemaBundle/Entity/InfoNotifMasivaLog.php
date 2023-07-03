<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoNotifMasivaLog
 *
 * @ORM\Table(name="INFO_NOTIF_MASIVA_LOG")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoNotifMasivaLogRepository")
 */
class InfoNotifMasivaLog
{

    /**
     * @var integer $intId
     *
     * @ORM\Column(name="ID_NOTIF_MASIVA_LOG", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_NOTIF_MASIVA_LOG", allocationSize=1, initialValue=1)
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
     * @var integer $intNumEnviados
     *
     * @ORM\Column(name="NUM_ENVIADOS", type="integer", nullable=true)
     */
    private $intNumEnviados;

    /**
     * @var integer $intNumNoEnviados
     *
     * @ORM\Column(name="NUM_NO_ENVIADOS", type="integer", nullable=true)
     */
    private $intNumNoEnviados;

    /**
     * @var integer $intNumProcesados
     *
     * @ORM\Column(name="NUM_PROCESADOS", type="integer", nullable=true)
     */
    private $intNumProcesados;

    /**
     * @var string $strEstado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=false)
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
     * Get intNumEnviados
     *
     * @return integer
     */
    public function getNumEnviados()
    {
        return $this->intNumEnviados;
    }

    /**
     * Set intNumEnviados
     *
     * @param integer $intNumEnviados
     */
    public function setNumEnviados($intNumEnviados)
    {
        $this->intNumEnviados = $intNumEnviados;
    }

    /**
     * Get intNumNoEnviados
     *
     * @return integer
     */
    public function getNumNoEnviados()
    {
        return $this->intNumNoEnviados;
    }

    /**
     * Set intNumNoEnviados
     *
     * @param integer $intNumNoEnviados
     */
    public function setNumNoEnviados($intNumNoEnviados)
    {
        $this->intNumNoEnviados = $intNumNoEnviados;
    }

    /**
     * Get intNumProcesados
     *
     * @return integer
     */
    public function getNumProcesados()
    {
        return $this->intNumProcesados;
    }

    /**
     * Set intNumProcesados
     *
     * @param string $intNumProcesados
     */
    public function setNumProcesados($intNumProcesados)
    {
        $this->intNumProcesados = $intNumProcesados;
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
