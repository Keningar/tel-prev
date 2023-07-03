<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoAsignacionSolicitudReg
 *
 * @ORM\Table(name="INFO_ASIGNACION_SOLICITUD_REG")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoAsignacionSolicitudRegRepository")
 */
class InfoAsignacionSolicitudReg
{

    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_ASIGNACION_SOLICITUD_REG", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_ASIGNACION_SOL_REG", allocationSize=1, initialValue=1)
    */
    private $id;	

    /**
    * @var string $personaEmpresaRolId
    *
    * @ORM\Column(name="PERSONA_EMPRESA_ROL_ID", type="integer", nullable=false)
    */
    private $personaEmpresaRolId;

    /**
    * @var datetime $feConexion
    *
    * @ORM\Column(name="FE_CONEXION", type="datetime", nullable=false)
    */
    private $feConexion;

    /**
    * @var string $estadoConexion
    *
    * @ORM\Column(name="ESTADO_CONEXION", type="string", nullable=true)
    */
    private $estadoConexion;

    /**
    * @var string $extension
    *
    * @ORM\Column(name="EXTENSION", type="string", nullable=true)
    */
    private $extension;

    /**
    * @var string $usrCreacion
    *
    * @ORM\Column(name="USR_CREACION", type="string", nullable=false)
    */
    private $usrCreacion;

    /**
    * @var datetime $feCreacion
    *
    * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
    */
    private $feCreacion;


    /**
    * @var string $ipCreacion
    *
    * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
    */
    private $ipCreacion;

    /**
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=true)
    */
    private $estado;


    /**
    * Get id
    *
    * @return integer
    */
    public function getId(){
            return $this->id; 
    }


    /**
    * Get personaEmpresaRolId
    *
    * @return string
    */
    public function getPersonaEmpresaRolId(){
            return $this->personaEmpresaRolId; 
    }

    /**
    * Set personaEmpresaRolId
    *
    * @param string $personaEmpresaRolId
    */
    public function setPersonaEmpresaRolId($personaEmpresaRolId)
    {
            $this->personaEmpresaRolId = $personaEmpresaRolId;
    }

    /**
    * Get feConexion
    *
    * @return datetime
    */
    public function getFeConexion(){
        return $this->feConexion; 
    }

    /**
    * Set feConexion
    *
    * @param datetime $feConexion
    */
    public function setFeConexion($feConexion)
    {
        $this->feConexion = $feConexion;
    }


    /**
    * Get estadoConexion
    *
    * @return string
    */
    public function getEstadoConexion()
    {
            return $this->estadoConexion; 
    }

    /**
    * Set estadoConexion
    *
    * @param string $estadoConexion
    */
    public function setEstadoConexion($estadoConexion)
    {
        $this->estadoConexion = $estadoConexion;
    }

    /**
    * Get extension
    *
    * @return string
    */
    public function getExtension()
    {
            return $this->extension; 
    }

    /**
    * Set extension
    *
    * @param string $extension
    */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
    * Get usrCreacion
    *
    * @return string
    */
    public function getUsrCreacion(){
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
    public function getFeCreacion(){
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
    public function getIpCreacion(){
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


}