<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; 

/**
 * telconet\schemaBundle\Entity\InfoRecaudacionDet
 *
 * @ORM\Table(name="INFO_RECAUDACION_DET")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoRecaudacionDetRepository")
 */
class InfoRecaudacionDet
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_RECAUDACION_DET", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_RECAUDACION_DET", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer $recaudacionId
     *
     * @ORM\Column(name="RECAUDACION_ID", type="integer", nullable=true)
     */
    private $recaudacionId;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=true)
     */
    private $estado;

    /**
     * @var string $esCliente
     *
     * @ORM\Column(name="ES_CLIENTE", type="string", nullable=true)
     */
    private $esCliente;

    /**
     * @var integer $personaEmpresaRolId
     *
     * @ORM\Column(name="PERSONA_EMPRESA_ROL_ID", type="integer", nullable=true)
     */
    private $personaEmpresaRolId;


    /**
     * @var string $identificacion
     *
     * @ORM\Column(name="IDENTIFICACION", type="string", nullable=true)
     */
    private $identificacion;
    
    
    /**
     * @var string $nombre
     *
     * @ORM\Column(name="NOMBRE", type="string", nullable=true)
     */
    private $nombre;    

    
    /**
     * @var string $numeroReferencia
     *
     * @ORM\Column(name="NUMERO_REFERENCIA", type="string", nullable=true)
     */
    private $numeroReferencia;       
    
    /**
     * @var string $asignado
     *
     * @ORM\Column(name="ASIGNADO", type="string", nullable=true)
     */
    private $asignado;    
    
    /**
     * @var datetime $feCreacion
     *
     * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
     */
    private $feCreacion;

    /**
     * @var datetime $feAsignacion
     *
     * @ORM\Column(name="FE_ASIGNACION", type="datetime", nullable=false)
     */
    private $feAsignacion;

    /**
     * @var string $usrCreacion
     *
     * @ORM\Column(name="USR_CREACION", type="string", nullable=false)
     */
    private $usrCreacion;

    /**
     * @var string $ipCreacion
     *
     * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
     */
    private $ipCreacion;
   
    
    /**
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return integer
     */
    public function getRecaudacionId()
    {
        return $this->recaudacionId;
    }
    
    /**
    * Set recaudacionId
    *
    * @param integer recaudacionId
    */
    public function setRecaudacionId($recaudacionId)
    {
            $this->recaudacionId = $recaudacionId;
    }    

    /**
     *
     * @return integer
     */
    public function getPersonaEmpresaRolId()
    {
        return $this->personaEmpresaRolId;
    }

    /**
     *
     * @param integer $personaEmpresaRolId
     */
    public function setPersonaEmpresaRolId($personaEmpresaRolId)
    {
        $this->personaEmpresaRolId = $personaEmpresaRolId;
    }

    /**
     *
     * @return string
     */
    public function getIdentificacion()
    {
        return $this->identificacion;
    }

    /**
     *
     * @param string $identificacion
     */
    public function setIdentificacion($identificacion)
    {
        $this->identificacion = $identificacion;
    }
    
    /**
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     *
     * @param string $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }    

    /**
     *
     * @return string
     */
    public function getNumeroReferencia()
    {
        return $this->numeroReferencia;
    }

    /**
     *
     * @param string $numeroReferencia
     */
    public function setNumeroReferencia($numeroReferencia)
    {
        $this->numeroReferencia = $numeroReferencia;
    }      

    /**
     *
     * @return string
     */
    public function getAsignado()
    {
        return $this->asignado;
    }

    /**
     *
     * @param string $asignado
     */
    public function setAsignado($asignado)
    {
        $this->asignado = $asignado;
    }    
    
    /**
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     *
     * @param string $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     *
     * @return string
     */
    public function getEsCliente()
    {
        return $this->esCliente;
    }

    /**
     *
     * @param string $esCliente
     */
    public function setEsCliente($esCliente)
    {
        $this->esCliente = $esCliente;
    }

    /**
     *
     * @return datetime
     */
    public function getFeCreacion()
    {
        return $this->feCreacion;
    }

    /**
     *
     * @param datetime $feCreacion
     */
    public function setFeCreacion($feCreacion)
    {
        $this->feCreacion = $feCreacion;
    }

    /**
     *
     * @return datetime
     */
    public function getFeAsignacion()
    {
        return $this->feAsignacion;
    }

    /**
     *
     * @param datetime $feAsignacion
     */
    public function setFeAsignacion($feAsignacion)
    {
        $this->feAsignacion = $feAsignacion;
    }

    /**
     *
     * @return string
     */
    public function getUsrCreacion()
    {
        return $this->usrCreacion;
    }

    /**
     *
     * @param string $usrCreacion
     */
    public function setUsrCreacion($usrCreacion)
    {
        $this->usrCreacion = $usrCreacion;
    }

    /**
     *
     * @return string
     */
    public function getIpCreacion()
    {
        return $this->ipCreacion;
    }

    /**
     *
     * @param string $ipCreacion
     */
    public function setIpCreacion($ipCreacion)
    {
        $this->ipCreacion = $ipCreacion;
    }
        

}
