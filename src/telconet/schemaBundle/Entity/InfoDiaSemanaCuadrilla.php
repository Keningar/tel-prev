<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDiaSemanaCuadrilla
 *
 * @ORM\Table(name="INFO_DIA_SEMANA_CUADRILLA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDiaSemanaCuadrillaRepository")
 */
class InfoDiaSemanaCuadrilla
{    
    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_DIA_SEMANA_CUADRILLA", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_DIA_SEMANA_CUADRILLA", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var AdmiCuadrilla
     *
     * @ORM\ManyToOne(targetEntity="AdmiCuadrilla")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CUADRILLA_ID", referencedColumnName="ID_CUADRILLA", nullable=false)
     * })
     */
    private $cuadrillaId;	

    /**
     * @var InfoPersona
     *
     * @ORM\ManyToOne(targetEntity="InfoPersona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="PERSONA_ID", referencedColumnName="ID_PERSONA", nullable=false)
     * })
     */
    private $personaId; 
    
     /**
     * @var integer $numeroDiaId
     *
     * @ORM\Column(name="NUMERO_DIA_ID", type="integer", nullable=false)
     */
    private $numeroDiaId;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=false)
     */		   
    private $estado;

    /**
     * @var string $usrCreacion
     *
     * @ORM\Column(name="USR_CREACION", type="string", nullable=true)
     */		           
    private $usrCreacion;

    /**
     * @var string $ipCreacion
     *
     * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
     */
    private $ipCreacion;

    /**
     * @var datetime $fechaCreacion
     *
     * @ORM\Column(name="FECHA_CREACION", type="datetime", nullable=false)
     */		            
    private $fechaCreacion;

    /**
     * @var string $usrUltMod
     *
     * @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
     */		             
    private $usrUltMod;

    /**
     * @var string $ipUltMod
     *
     * @ORM\Column(name="IP_ULT_MOD", type="string", nullable=true)
     */		
    private $ipUltMod;  

    /**
     * @var datetime $fechaUltMod
     *
     * @ORM\Column(name="FECHA_ULT_MOD", type="datetime", nullable=true)
     */		
    private $fechaUltMod;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return AdmiCuadrilla
     */
    public function getCuadrillaId()
    {
        return $this->cuadrillaId;
    }

    /**
     * @param AdmiCuadrilla $cuadrillaId
     */
    public function setCuadrillaId($cuadrillaId)
    {
        $this->cuadrillaId = $cuadrillaId;
    }

    /**
     * @return int
     */
    public function getPersonaId()
    {
        return $this->personaId;
    }

    /**
     * @param int $PersonaId
     */
    public function setPersonaId($personaId)
    {
        $this->personaId = $personaId;
    }

    /**
     * @return int
     */
    public function getNumeroDiaId()
    {
        return $this->numeroDiaId;
    }

    /**
     * @param int $numeroDiaId
     */
    public function setNumeroDiaId($numeroDiaId)
    {
        $this->numeroDiaId = $numeroDiaId;
    }

    /**
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param string $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * @return string
     */
    public function getUsrCreacion()
    {
        return $this->usrCreacion;
    }

    /**
     * @param string $usrCreacion
     */
    public function setUsrCreacion($usrCreacion)
    {
        $this->usrCreacion = $usrCreacion;
    }

    /**
     * @return string
     */
    public function getIpCreacion()
    {
        return $this->ipCreacion;
    }

    /**
     * @param string $ipCreacion
     */
    public function setIpCreacion($ipCreacion)
    {
        $this->ipCreacion = $ipCreacion;
    }

    /**
     * @return datetime
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * @param datetime $fechaCreacion
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;
    }

    /**
     * @return string
     */
    public function getUsrUltMod()
    {
        return $this->usrUltMod;
    }

    /**
     * @param string $usrUltMod
     */
    public function setUsrUltMod($usrUltMod)
    {
        $this->usrUltMod = $usrUltMod;
    }

    /**
     * @return string
     */
    public function getIpUltMod()
    {
        return $this->ipUltMod;
    }

    /**
     * @param string $ipUltMod
     */
    public function setIpUltMod($ipUltMod)
    {
        $this->ipUltMod = $ipUltMod;
    }

    /**
     * @return datetime
     */
    public function getFechaUltMod()
    {
        return $this->fechaUltMod;
    }

    /**
     * @param datetime $fechaUltMod
     */
    public function setFechaUltMod($fechaUltMod)
    {
        $this->fechaUltMod = $fechaUltMod;
    }
    
}
