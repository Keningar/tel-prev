<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoCoordinadorTurno
 *
 * @ORM\Table(name="INFO_COORDINADOR_TURNO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoCoordinadorTurnoRepository")
 */
class InfoCoordinadorTurno
{    
    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_COORDINADOR_TURNO", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_COORDINADOR_TURNO", allocationSize=1, initialValue=1)
     */
    private $id;

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
     * @var string $fechaInicio
     *
     * @ORM\Column(name="FECHA_INICIO", type="string", nullable=false)
     */		        
    private $fechaInicio;

    /**
     * @var string $horaInicio
     *
     * @ORM\Column(name="HORA_INICIO", type="string", nullable=false)
     */		        
    private $horaInicio;

    /**
     * @var string $fechaFin
     *
     * @ORM\Column(name="FECHA_FIN", type="string", nullable=false)
     */		            
    private $fechaFin;

    /**
     * @var string $horaFin
     *
     * @ORM\Column(name="HORA_FIN", type="string", nullable=false)
     */		            
    private $horaFin;

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
     * @return InfoPersonaEmpresaRol
     */
    public function getPersonaEmpresaRolId()
    {
        return $this->personaEmpresaRolId;
    }

    /**
     * @param InfoPersonaEmpresaRol $personaEmpresaRolId
     */
    public function setPersonaEmpresaRolId($personaEmpresaRolId)
    {
        $this->personaEmpresaRolId = $personaEmpresaRolId;
    }

    /**
     * @return string
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * @param string $fechaInicio
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;
    }

    /**
     * @return string
     */
    public function getHoraInicio()
    {
        return $this->horaInicio;
    }

    /**
     * @param string $horaInicio
     */
    public function setHoraInicio($horaInicio)
    {
        $this->horaInicio = $horaInicio;
    }

    /**
     * @return string
     */
    public function getFechaFin()
    {
        return $this->fechaFin;
    }

    /**
     * @param string $fechaFin
     */
    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;
    }

    /**
     * @return string
     */
    public function getHoraFin()
    {
        return $this->horaFin;
    }

    /**
     * @param string $horaFin
     */
    public function setHoraFin($horaFin)
    {
        $this->horaFin = $horaFin;
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