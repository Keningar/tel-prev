<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPlanProductoCaract
 *
 * @ORM\Table(name="INFO_PERSONA_EMPRESA_ROL_CARAC")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPersonaEmpresaRolCaracRepository")
 */
class InfoPersonaEmpresaRolCarac
{	
    
    /**
    * @var integer $personaEmpresaRolCaracId
    *
    * @ORM\Column(name="PERSONA_EMPRESA_ROL_CARAC_ID", type="integer", nullable=true)
    */		

    private $personaEmpresaRolCaracId;

    /**
     * @var InfoPersonaEmpresaRol
     *
     * @ORM\ManyToOne(targetEntity="InfoPersonaEmpresaRol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="PERSONA_EMPRESA_ROL_ID", referencedColumnName="ID_PERSONA_ROL", nullable=false)
     * })
     */
    private $personaEmpresaRolId;

    /**
     * @var AdmiCaracteristica
     *
     * @ORM\ManyToOne(targetEntity="AdmiCaracteristica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CARACTERISTICA_ID", referencedColumnName="ID_CARACTERISTICA", nullable=false)
     * })
     */	
    private $caracteristicaId;

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
     * @var string $ipCreacion
     *
     * @ORM\Column(name="IP_CREACION", type="string", nullable=true)
     */
    private $ipCreacion;

    /**
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=true)
    */		

    private $estado;

    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_PERSONA_EMPRESA_ROL_CARACT", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PERSONA_EMP_ROL_CARAC", allocationSize=1, initialValue=1)
    */		

    private $id;
    
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
    * Get personaEmpresaRolCaracId
    *
    * @return integer
    */		

    public function getPersonaEmpresaRolCaracId()
    {
        return $this->personaEmpresaRolCaracId; 
    }

    /**
    * Set personaEmpresaRolCaracId
    *
    * @param string $personaEmpresaRolCaracId
    */
    public function setPersonaEmpresaRolCaracId($personaEmpresaRolCaracId)
    {
        $this->personaEmpresaRolCaracId = $personaEmpresaRolCaracId;
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
    * Get id
    *
    * @return integer
    */		

    public function getId()
    {
        return $this->id; 
    }
}