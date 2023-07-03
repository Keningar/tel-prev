<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoNotifMasivaLogDet
 *
 * @ORM\Table(name="INFO_NOTIF_MASIVA_LOG_DET")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoNotifMasivaLogDetRepository")
 */
class InfoNotifMasivaLogDet
{

    /**
     * @var integer $intId
     *
     * @ORM\Column(name="ID_NOTIF_MASIVA_LOG_DET", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_NOTIF_MASIVA_LOG_DET", allocationSize=1, initialValue=1)
     */
    private $intId;

    /**
     * @var InfoNotifMasivaLog
     *
     * @ORM\ManyToOne(targetEntity="InfoNotifMasivaLog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="NOTIF_MASIVA_LOG_ID", referencedColumnName="ID_NOTIF_MASIVA_LOG")
     * })
     */
    private $intNotifMasivaLogId;

    /**
     * @var string $strNombres
     *
     * @ORM\Column(name="NOMBRES", type="string", nullable=false)
     */
    private $strNombres;

    /**
     * @var string $strCorreo
     *
     * @ORM\Column(name="CORREO", type="string", nullable=false)
     */
    private $strCorreo;

    /**
     * @var string $strTipoContacto
     *
     * @ORM\Column(name="TIPO_CONTACTO", type="string", nullable=false)
     */
    private $strTipoContacto;

    /**
     * @var string $strLogin
     *
     * @ORM\Column(name="LOGIN", type="string", nullable=false)
     */
    private $strLogin;

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
     * Get intNotifMasivaLogId
     *
     * @return telconet\schemaBundle\Entity\InfoNotifMasivaLog
     */
    public function getNotifMasivaId()
    {
        return $this->intNotifMasivaLogId;
    }

    /**
     * Set intNotifMasivaLogId
     *
     * @param telconet\schemaBundle\Entity\InfoNotifMasivaLog $intNotifMasivaLogId
     */
    public function setNotifMasivaId(\telconet\schemaBundle\Entity\InfoNotifMasivaLog $intNotifMasivaLogId)
    {
        $this->intNotifMasivaLogId = $intNotifMasivaLogId;
    }

    /**
     * Get strNombres
     *
     * @return string
     */
    public function getNombres()
    {
        return $this->strNombres;
    }

    /**
     * Set strNombres
     *
     * @param string $strNombres
     */
    public function setNombres($strNombres)
    {
        $this->strNombres = $strNombres;
    }

    /**
     * Get strCorreo
     *
     * @return string
     */
    public function getCorreo()
    {
        return $this->strCorreo;
    }

    /**
     * Set strCorreo
     *
     * @param string $strCorreo
     */
    public function setCorreo($strCorreo)
    {
        $this->strCorreo = $strCorreo;
    }

    /**
     * Get strTipoContacto
     *
     * @return string
     */
    public function getTipoContacto()
    {
        return $this->strTipoContacto;
    }

    /**
     * Set strTipoContacto
     *
     * @param string $strTipoContacto
     */
    public function setTipoContacto($strTipoContacto)
    {
        $this->strTipoContacto = $strTipoContacto;
    }

    /**
     * Get strLogin
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->strLogin;
    }

    /**
     * Set strLogin
     *
     * @param string $strLogin
     */
    public function setLogin($strLogin)
    {
        $this->strLogin = $strLogin;
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
     * @param string $estado
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
