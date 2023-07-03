<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPersonaContacto
 *
 * @ORM\Table(name="INFO_PERSONA_CONTACTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPersonaContactoRepository")
 */
class InfoPersonaContacto
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_PERSONA_CONTACTO", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PERSONA_CONTACTO", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var InfoPersonaEmpresaRol
     *
     * @ORM\ManyToOne(targetEntity="InfoPersonaEmpresaRol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="PERSONA_EMPRESA_ROL_ID", referencedColumnName="ID_PERSONA_ROL")
     * })
     */
    private $personaEmpresaRolId;

    /**
     * @var InfoPersona
     *
     * @ORM\ManyToOne(targetEntity="InfoPersona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CONTACTO_ID", referencedColumnName="ID_PERSONA")
     * })
     */
    private $contactoId;

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
     * @var InfoPersonaEmpresaRol
     *
     * @ORM\ManyToOne(targetEntity="InfoPersonaEmpresaRol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="PERSONA_ROL_ID", referencedColumnName="ID_PERSONA_ROL")
     * })
     */
    private $personaRolId;

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
     * @return telconet\schemaBundle\Entity\InfoPersonaEmpresaRol
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
     * Get contactoId
     *
     * @return telconet\schemaBundle\Entity\InfoPersona
     */
    public function getContactoId()
    {
        return $this->contactoId;
    }

    /**
     * Set contactoId
     *
     * @param telconet\schemaBundle\Entity\InfoPersona $contactoId
     */
    public function setContactoId(\telconet\schemaBundle\Entity\InfoPersona $contactoId)
    {
        $this->contactoId = $contactoId;
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
     * Get personaRolId
     *
     * @return telconet\schemaBundle\Entity\InfoPersonaEmpresaRol
     */
    public function getPersonaRolId()
    {
        return $this->personaRolId;
    }

    /**
     * Set personaRolId
     *
     * @param telconet\schemaBundle\Entity\InfoPersonaEmpresaRol $personaRolId
     */
    public function setPersonaRolId(\telconet\schemaBundle\Entity\InfoPersonaEmpresaRol $personaRolId)
    {
        $this->personaRolId = $personaRolId;
    }

}
