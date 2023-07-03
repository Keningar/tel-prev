<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPersonaRepresentante
 *
 * @ORM\Table(name="INFO_PERSONA_REPRESENTANTE")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPersonaRepresentanteRepository")
 */
class InfoPersonaRepresentante
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_PERSONA_REPRESENTANTE", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PERSONA_REPRESENTANTE", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer $personaEmpresaRolId
     *
     * @ORM\ManyToOne(targetEntity="InfoPersonaEmpresaRol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="PERSONA_EMPRESA_ROL_ID", referencedColumnName="ID_PERSONA_ROL")
     * })
     */
    private $personaEmpresaRolId;

    /**
     * @var integer $representanteEmpresaRolId
     *
     * @ORM\ManyToOne(targetEntity="InfoPersonaEmpresaRol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="REPRESENTANTE_EMPRESA_ROL_ID", referencedColumnName="ID_PERSONA_ROL")
     * })
     */
    private $representanteEmpresaRolId;

    /**
     * @var string $razonComercial
     *
     * @ORM\Column(name="RAZON_COMERCIAL", type="string", nullable=false)
     */
    private $razonComercial;

    /**
     * @var datetime $feRegistroMercantil
     *
     * @ORM\Column(name="FE_REGISTRO_MERCANTIL", type="datetime", nullable=false)
     */
    private $feRegistroMercantil;

    /**
     * @var datetime $feExpiracionNombramiento
     *
     * @ORM\Column(name="FE_EXPIRACION_NOMBRAMIENTO", type="datetime", nullable=false)
     */
    private $feExpiracionNombramiento;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=true)
     */
    private $estado;

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
     * @ORM\Column(name="IP_CREACION", type="string", nullable=true)
     */
    private $ipCreacion;

    /**
     * @var datetime $feUltMod
     *
     * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
     */
    private $feUltMod;

    /**
     * @var string $usrUltMod
     *
     * @ORM\Column(name="USR_ULT_MOD", type="string", nullable=false)
     */
    private $usrUltMod;

    /**
     * @var string $ipUltMod
     *
     * @ORM\Column(name="IP_ULT_MOD", type="string", nullable=false)
     */
    private $ipUltMod;

    /**
     * @var string $observacion
     *
     * @ORM\Column(name="OBSERVACION", type="string", nullable=false)
     */
    private $observacion;

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
     * Get personaEmpresaRolId
     *
     * @return integer
     */
    public function getPersonaEmpresaRolId()
    {
        return $this->personaEmpresaRolId;
    }

    /**
     * Set personaEmpresaRolId
     *
     * @param telconet\schemaBundle\Entity\InfoPersonaEmpresaRol $personaEmpresaRolId
     */
    public function setPersonaEmpresaRolId(\telconet\schemaBundle\Entity\InfoPersonaEmpresaRol $personaEmpresaRolId)
    {
        $this->personaEmpresaRolId = $personaEmpresaRolId;
    }

    /**
     * Get representanteEmpresaRolId
     *
     * @return integer
     */
    public function getRepresentanteEmpresaRolId()
    {
        return $this->representanteEmpresaRolId;
    }

    /**
     * Set representanteEmpresaRolId
     *
     * @param telconet\schemaBundle\Entity\InfoPersonaEmpresaRol $representanteEmpresaRolId
     */
    public function setRepresentanteEmpresaRolId(\telconet\schemaBundle\Entity\InfoPersonaEmpresaRol $representanteEmpresaRolId)
    {
        $this->representanteEmpresaRolId = $representanteEmpresaRolId;
    }

    /**
     * Get razonComercial
     *
     * @return string
     */
    public function getRazonComercial()
    {
        return $this->razonComercial;
    }

    /**
     * Set razonComercial
     *
     * @param string $razonComercial
     */
    public function setRazonComercial($razonComercial)
    {
        $this->razonComercial = $razonComercial;
    }

    /**
     * Get feRegistroMercantil
     *
     * @return datetime
     */
    public function getFeRegistroMercantil()
    {
        return $this->feRegistroMercantil;
    }

    /**
     * Set feRegistroMercantil
     *
     * @param datetime $feRegistroMercantil
     */
    public function setFeRegistroMercantil($feRegistroMercantil)
    {
        $this->feRegistroMercantil = $feRegistroMercantil;
    }

    /**
     * Get feExpiracionNombramiento
     *
     * @return datetime
     */
    public function getFeExpiracionNombramiento()
    {
        return $this->feExpiracionNombramiento;
    }

    /**
     * Set feExpiracionNombramiento
     *
     * @param datetime $feExpiracionNombramiento
     */
    public function setFeExpiracionNombramiento($feExpiracionNombramiento)
    {
        $this->feExpiracionNombramiento = $feExpiracionNombramiento;
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
     * Get ipUltMod
     *
     * @return string
     */
    public function getIpUltMod()
    {
        return $this->ipUltMod;
    }

    /**
     * Set ipUltMod
     *
     * @param string $ipUltMod
     */
    public function setIpUltMod($ipUltMod)
    {
        $this->ipUltMod = $ipUltMod;
    }

    /**
     * Get observacion
     *
     * @return string
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    }
}
