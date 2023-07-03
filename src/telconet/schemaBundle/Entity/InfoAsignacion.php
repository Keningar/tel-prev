<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoAsignacion
 *
 * @ORM\Table(name="INFO_ASIGNACION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoAsignacionRepository")
 */
class InfoAsignacion 
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_ASIGNACION", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_ASIGNACION", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var InfoPersonaEmpresaRol
     *
     * @ORM\ManyToOne(targetEntity="InfoPersonaEmpresaRol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="PERSONA_EMPRESA_ROL_ID_ASIST", referencedColumnName="ID_PERSONA_ROL", nullable=false)
     * })
     */
    private $personaEmpresaRolIdAsist;

    /**
     * @var InfoPersonaEmpresaRol
     *
     * @ORM\ManyToOne(targetEntity="InfoPersonaEmpresaRol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="PERSONA_EMPRESA_ROL_ID_VEND", referencedColumnName="ID_PERSONA_ROL", nullable=false)
     * })
     */
    private $personaEmpresaRolIdVend;

    /**
     * @var string $usrVendedor
     *
     * @ORM\Column(name="USR_VENDEDOR", type="string", nullable=false)
     */
    private $usrVendedor;

    /**
     * @var datetime $tiempoDias
     *
     * @ORM\Column(name="TIEMPO_DIAS", type="datetime", nullable=true)
     */
    private $tiempoDias;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=true)
     */
    private $estado;

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
     * @var string $ipCreacion
     *
     * @ORM\Column(name="IP_CREACION", type="string", nullable=true)
     */
    private $ipCreacion;

    /**
     * @var string $ipUltMod
     *
     * @ORM\Column(name="IP_ULT_MOD", type="string", nullable=true)
     */
    private $ipUltMod;

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
     * Get personaEmpresaRolIdAsist
     *
     * @return telconet\schemaBundle\Entity\InfoPersonaEmpresaRol
     */
    public function getPersonaEmpresaRolIdAsist() 
    {
        return $this->personaEmpresaRolIdAsist;
    }

    /**
     * Set personaEmpresaRolIdAsist
     *
     * @param telconet\schemaBundle\Entity\InfoPersonaEmpresaRol $personaEmpresaRolIdAsist
     */
    public function setPersonaEmpresaRolIdAsist(\telconet\schemaBundle\Entity\InfoPersonaEmpresaRol $personaEmpresaRolIdAsist) 
    {
        $this->personaEmpresaRolIdAsist = $personaEmpresaRolIdAsist;
    }

    /**
     * Get personaEmpresaRolIdVend
     *
     * @return telconet\schemaBundle\Entity\InfoPersonaEmpresaRol
     */
    public function getPersonaEmpresaRolIdVend() 
    {
        return $this->personaEmpresaRolIdVend;
    }

    /**
     * Set personaEmpresaRolIdVend
     *
     * @param telconet\schemaBundle\Entity\InfoPersonaEmpresaRol $personaEmpresaRolIdVend
     */
    public function setPersonaEmpresaRolIdVend(\telconet\schemaBundle\Entity\InfoPersonaEmpresaRol $personaEmpresaRolIdVend) 
    {
        $this->personaEmpresaRolIdVend = $personaEmpresaRolIdVend;
    }

    /**
     * Get usrVendedor
     *
     * @return string
     */
    public function getUsrVendedor()
    {
        return $this->usrVendedor;
    }

    /**
     * Set usrVendedor
     *
     * @param string $usrVendedor
     */
    public function setUsrVendedor($usrVendedor)
    {
        $this->usrVendedor = $usrVendedor;
    }

    /**
     * Get tiempoDias
     *
     * @return datetime
     */
    public function getTiempoDias() 
    {
        return $this->tiempoDias;
    }

    /**
     * Set tiempoDias
     *
     * @param $tiempoDias
     */
    public function setTiempoDias($tiempoDias) 
    {
        $this->tiempoDias = $tiempoDias;
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

}
