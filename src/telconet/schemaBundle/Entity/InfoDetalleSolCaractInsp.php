<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDetalleSolCaract
 *
 * @ORM\Table(name="INFO_DETALLE_SOL_CARACT_DET")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDetalleSolCaractRepository")
 */
class InfoDetalleSolCaractInsp
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_SOLICITUD_CARACTERISTICA", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DET_SOL_CARACT", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var AdmiCaracteristica
     *
     * @ORM\ManyToOne(targetEntity="AdmiCaracteristica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CARACTERISTICA_ID", referencedColumnName="ID_CARACTERISTICA")
     * })
     */
    private $caracteristicaId;

    /**
     * @var InfoDetalleSolicitud
     *
     * @ORM\ManyToOne(targetEntity="InfoDetalleSolicitud")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="DETALLE_SOLICITUD_ID", referencedColumnName="ID_DETALLE_SOLICITUD")
     * })
     */
    private $detalleSolicitudId;

    /**
     * @var string $valor
     *
     * @ORM\Column(name="VALOR", type="string", nullable=true)
     */
    private $valor;

    /**
     * @var datetime $feCreacion
     *
     * @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
     */
    private $feCreacion;

    /**
     * @var datetime $feUltMod
     *
     * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
     */
    private $feUltMod;

    /**
     * @var string $usrCreacion
     *
     * @ORM\Column(name="USR_CREACION", type="string", nullable=true)
     */
    private $usrCreacion;

    /**
     * @var string $usrUltMod
     *
     * @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
     */
    private $usrUltMod;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=true)
     */
    private $estado;
    
    /**
     * @var integer $detalleSolCaractId
     *
     * @ORM\Column(name="DETALLE_SOL_CARACT_ID", type="integer", nullable=true)
     */
    private $detalleSolCaractId;

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
     * Set id
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * Get detalleSolicitudId
     *
     * @return telconet\schemaBundle\Entity\InfoDetalleSolicitud
     */
    public function getDetalleSolicitudId()
    {
        return $this->detalleSolicitudId;
    }

    /**
     * Set detalleSolicitudId
     *
     * @param telconet\schemaBundle\Entity\InfoDetalleSolicitud $detalleSolicitudId
     */
    public function setDetalleSolicitudId(\telconet\schemaBundle\Entity\InfoDetalleSolicitud $detalleSolicitudId)
    {
        $this->detalleSolicitudId = $detalleSolicitudId;
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
     * @param string $valor
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
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
     * Get feUltMod
     *
     * @return datetime
     */
    public function getFeUltMod()
    {
        return $this->feUltMod;
    }

    /**
     * Set feUltMod
     *
     * @param datetime $feUltMod
     */
    public function setFeUltMod($feUltMod)
    {
        $this->feUltMod = $feUltMod;
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
     * Get usrUltMod
     *
     * @return string
     */
    public function getUsrUltMod()
    {
        return $this->usrUltMod;
    }

    /**
     * Set usrUltMod
     *
     * @param string $usrUltMod
     */
    public function setUsrUltMod($usrUltMod)
    {
        $this->usrUltMod = $usrUltMod;
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
     * Get detalleSolCaractId
     *
     * @return integer
     */
    public function getDetalleSolCaractId()
    {
        return $this->detalleSolCaractId;
    }

    /**
     * Set detalleSolCaractId
     *
     * @param integer $detalleSolCaractId
     */
    public function setDetalleSolCaractId($detalleSolCaractId)
    {
        $this->detalleSolCaractId = $detalleSolCaractId;
    }

}
