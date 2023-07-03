<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiVehiculo
 *
 * @ORM\Table(name="ADMI_VEHICULO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiVehiculoRepository")
 */
class AdmiVehiculo
{
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_VEHICULO", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_VEHICULO", allocationSize=1, initialValue=1)
    */		
    private $id;	

    /**
    * @var string $telefono
    *
    * @ORM\Column(name="TELEFONO", type="string", nullable=true)
    */		
    private $telefono;

    /**
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=false)
    */		
    private $estado;

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
    * @var integer $conductorId
    *
    * @ORM\Column(name="CONDUCTOR_ID", type="integer", nullable=true)
    */
    private $conductorId;

    /**
    * @var integer $modeloId
    *
    * @ORM\Column(name="MODELO_ID", type="integer", nullable=true)
    */
    private $modeloId;
    
    /**
    * @var string $disco
    *
    * @ORM\Column(name="DISCO", type="string", nullable=true)
    */		
    private $disco;

    /**
    * @var string $placa
    *
    * @ORM\Column(name="PLACA", type="string", nullable=true)
    */		
    private $placa;

    /**
    * @var string $ciudadAsignada
    *
    * @ORM\Column(name="CIUDAD_ASIGNADA", type="string", nullable=true)
    */		
    private $ciudadAsignada;
    
    /**
    * @var string $tipo
    *
    * @ORM\Column(name="TIPO", type="string", nullable=true)
    */		
    private $tipo;

    /**
    * @var string $anio
    *
    * @ORM\Column(name="ANIO", type="string", nullable=true)
    */		
    private $anio;

    /**
    * @var string $region
    *
    * @ORM\Column(name="REGION", type="string", nullable=true)
    */		
    private $region;

    /**
    * @var string $departamento
    *
    * @ORM\Column(name="DEPARTAMENTO", type="string", nullable=true)
    */		
    private $departamento;
    
    /**
    * @var string $usrUltMod
    *
    * @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
    */		
    private $usrUltMod;

    /**
    * @var datetime $feUltMod
    *
    * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
    */		
    private $feUltMod;
    

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
    * Get telefono
    *
    * @return string
    */	
    public function getTelefono()
    {
        return $this->telefono;
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
    * Get usrCreacion
    *
    * @return string
    */	
    public function getUsrCreacion()
    {
        return $this->usrCreacion;
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
    * Get conductorId
    *
    * @return integer
    */	
    public function getConductorId()
    {
        return $this->conductorId;
    }

    /**
    * Get modeloId
    *
    * @return integer
    */	
    public function getModeloId()
    {
        return $this->modeloId;
    }

    /**
    * Get disco
    *
    * @return string
    */	
    public function getDisco()
    {
        return $this->disco;
    }

    /**
    * Get placa
    *
    * @return string
    */	
    public function getPlaca()
    {
        return $this->placa;
    }

    /**
    * Get ciudadAsignada
    *
    * @return string
    */	
    public function getCiudadAsignada()
    {
        return $this->ciudadAsignada;
    }

    /**
    * Get tipo
    *
    * @return string
    */	
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
    * Get anio
    *
    * @return string
    */	
    public function getAnio()
    {
        return $this->anio;
    }

    /**
    * Get region
    *
    * @return string
    */	
    public function getRegion()
    {
        return $this->region;
    }

    /**
    * Get departamento
    *
    * @return string
    */	
    public function getDepartamento()
    {
        return $this->departamento;
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
    * Get feUltMod
    *
    * @return datetime
    */	
    public function getFeUltMod()
    {
        return $this->feUltMod;
    }
    

    /**
    * Set telefono
    *
    * @param string $telefono
    */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
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
    * Set usrCreacion
    *
    * @param string $usrCreacion
    */
    public function setUsrCreacion($usrCreacion)
    {
        $this->usrCreacion = $usrCreacion;
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
    * Set conductorId
    *
    * @param integer $conductorId
    */
    public function setConductorId($conductorId)
    {
        $this->conductorId = $conductorId;
    }

    /**
    * Set modeloId
    *
    * @param integer $modeloId
    */
    public function setModeloId($modeloId)
    {
        $this->modeloId = $modeloId;
    }

    /**
    * Set disco
    *
    * @param string $disco
    */
    public function setDisco($disco)
    {
        $this->disco = $disco;
    }

    /**
    * Set placa
    *
    * @param string $placa
    */
    public function setPlaca($placa)
    {
        $this->placa = $placa;
    }

    /**
    * Set ciudadAsignada
    *
    * @param string $ciudadAsignada
    */
    public function setCiudadAsignada($ciudadAsignada)
    {
        $this->ciudadAsignada = $ciudadAsignada;
    }

    /**
    * Set tipo
    *
    * @param string $tipo
    */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
    * Set anio
    *
    * @param string $anio
    */
    public function setAnio($anio)
    {
        $this->anio = $anio;
    }

    /**
    * Set region
    *
    * @param string $region
    */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
    * Set departamento
    *
    * @param string $departamento
    */
    public function setDepartamento($departamento)
    {
        $this->departamento = $departamento;
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
    * Set feUltMod
    *
    * @param datetime $feUltMod
    */
    public function setFeUltMod($feUltMod)
    {
        $this->feUltMod = $feUltMod;
    }
    
    
    public function __toString()
    {
        return $this->placa;
    }
}