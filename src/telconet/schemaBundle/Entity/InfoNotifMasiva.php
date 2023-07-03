<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoNotifMasiva
 *
 * @ORM\Table(name="INFO_NOTIF_MASIVA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoNotifMasivaRepository")
 */
class InfoNotifMasiva
{

    /**
     * @var integer $intId
     *
     * @ORM\Column(name="ID_NOTIF_MASIVA", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_NOTIF_MASIVA", allocationSize=1, initialValue=1)
     */
    private $intId;

    /**
     * @var AdmiPlantilla
     *
     * @ORM\ManyToOne(targetEntity="AdmiPlantilla")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="PLANTILLA_ID", referencedColumnName="ID_PLANTILLA")
     * })
     */
    private $intPlantillaId;

    /**
     * @var string $strNombreJob
     *
     * @ORM\Column(name="NOMBRE_JOB", type="string", nullable=false)
     */
    private $strNombreJob;

    /**
     * @var string $strAsunto
     *
     * @ORM\Column(name="ASUNTO", type="string", nullable=false)
     */
    private $strAsunto;

    /**
     * @var string $strTipo
     *
     * @ORM\Column(name="TIPO", type="string", nullable=false)
     */
    private $strTipo;

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
     * Get intPlantillaId
     *
     * @return telconet\schemaBundle\Entity\AdmiPlantilla
     */
    public function getPlantillaId()
    {
        return $this->intPlantillaId;
    }

    /**
     * Set intPlantillaId
     *
     * @param telconet\schemaBundle\Entity\AdmiPlantilla $intPlantillaId
     */
    public function setPlantillaId(\telconet\schemaBundle\Entity\AdmiPlantilla $intPlantillaId)
    {
        $this->intPlantillaId = $intPlantillaId;
    }

    /**
     * Get strNombreJob
     *
     * @return string
     */
    public function getNombreJob()
    {
        return $this->strNombreJob;
    }

    /**
     * Set strNombreJob
     *
     * @param string $strNombreJob
     */
    public function setNombreJob($strNombreJob)
    {
        $this->strNombreJob = $strNombreJob;
    }

    /**
     * Get strAsunto
     *
     * @return string
     */
    public function getAsunto()
    {
        return $this->strAsunto;
    }

    /**
     * Set strAsunto
     *
     * @param string $strAsunto
     */
    public function setAsunto($strAsunto)
    {
        $this->strAsunto = $strAsunto;
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
