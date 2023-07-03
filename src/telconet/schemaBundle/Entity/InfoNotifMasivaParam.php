<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoNotifMasivaParam
 *
 * @ORM\Table(name="INFO_NOTIF_MASIVA_PARAM")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoNotifMasivaParamRepository")
 */
class InfoNotifMasivaParam
{

    /**
     * @var integer $intId
     *
     * @ORM\Column(name="ID_NOTIF_MASIVA_PARAM", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_NOTIF_MASIVA_PARAM", allocationSize=1, initialValue=1)
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
     * @var string $strTipo
     *
     * @ORM\Column(name="TIPO", type="string", nullable=false)
     */
    private $strTipo;

    /**
     * @var string $strNombre
     *
     * @ORM\Column(name="NOMBRE", type="string", nullable=false)
     */
    private $strNombre;

    /**
     * @var string $strValor
     *
     * @ORM\Column(name="VALOR", type="string", nullable=false)
     */
    private $strValor;

    /**
     * @var string $estado
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
     * Get strTipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->strTipo;
    }

    /**
     * Set strTipo
     *
     * @param string $strTipo
     */
    public function setTipo($strTipo)
    {
        $this->strTipo = $strTipo;
    }

    /**
     * Get strNombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->strNombre;
    }

    /**
     * Set strNombre
     *
     * @param string $strNombre
     */
    public function setNombre($strNombre)
    {
        $this->strNombre = $strNombre;
    }

    /**
     * Get strValor
     *
     * @return string
     */
    public function getValor()
    {
        return $this->strValor;
    }

    /**
     * Set strValor
     *
     * @param string $strValor
     */
    public function setValor($strValor)
    {
        $this->strValor = $strValor;
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
