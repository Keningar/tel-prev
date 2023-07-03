<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto
 *
 * @ORM\Table(name="INFO_PERSONA_EMPRESA_ROL_HISTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPersonaEmpresaRolHistoRepository")
 */
class InfoPersonaEmpresaRolHisto
{
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_PERSONA_EMPRESA_ROL_HISTO", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PERSONA_EMPRESA_ROL_H", allocationSize=1, initialValue=1)
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
    * @var string $usrCreacion
    *
    * @ORM\Column(name="USR_CREACION", type="string", nullable=true)
    */		

    private $usrCreacion;

    /**
    * @var datetime $feCreacion
    *
    * @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
    */		

    private $feCreacion;

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
    * @var string $observacion
    *
    * @ORM\Column(name="OBSERVACION", type="string", nullable=true)
    */		

    private $observacion;

    /**
    * @var integer $motivoId
    *
    * @ORM\Column(name="MOTIVO_ID", type="integer", nullable=true)
    */		

    private $motivoId;

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


    /**
    * Get motivoId
    *
    * @return integer
    */		
    public function getMotivoId()
    {
        return $this->motivoId; 
    }

    /**
    * Set motivoId
    *
    * @param integer $motivoId
    */
    public function setMotivoId($motivoId)
    {
        $this->motivoId = $motivoId;
    }
}