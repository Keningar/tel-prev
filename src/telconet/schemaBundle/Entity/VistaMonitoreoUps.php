<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\VistaMonitoreoUps
 *
 * @ORM\Table(name="VISTA_MONITOREO_UPS")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\VistaMonitoreoUpsRepository")
 */ 
class VistaMonitoreoUps
{
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID", type="integer", nullable=false)
    * @ORM\Id
    */		
    private $id;

    /**
    * @var integer $idUps
    *
    * @ORM\Column(name="ID_UPS", type="integer", nullable=false)
    */		
    private $idUps;

    /**
    * @var string $nombreUps
    *
    * @ORM\Column(name="NOMBRE_UPS", type="string", nullable=false)
    */		
    private $nombreUps;

    /**
    * @var string $nombreNodo
    *
    * @ORM\Column(name="NOMBRE_NODO", type="string", nullable=false)
    */		
    private $nombreNodo;

    /**
    * @var string $ipUps
    *
    * @ORM\Column(name="IP_UPS", type="string", nullable=false)
    */	
    private $ipUps;

    /**
    * @var string $tipo
    *
    * @ORM\Column(name="TIPO", type="string", nullable=false)
    */		
    private $tipo;

    /**
    * @var string $generador
    *
    * @ORM\Column(name="GENERADOR", type="string", nullable=false)
    */	
    private $generador;

    /**
    * @var string $direccion
    *
    * @ORM\Column(name="DIRECCION", type="string", nullable=false)
    */		
    private $direccion;

    /**
    * @var string $latitud
    *
    * @ORM\Column(name="LATITUD", type="string", nullable=false)
    */		
    private $latitud;

    /**
    * @var string $longitud
    *
    * @ORM\Column(name="LONGITUD", type="string", nullable=false)
    */	
    private $longitud;

    /**
    * @var string $region
    *
    * @ORM\Column(name="REGION", type="string", nullable=false)
    */		
    private $region;

    /**
    * @var string $provincia
    *
    * @ORM\Column(name="PROVINCIA", type="string", nullable=false)
    */	
    private $provincia;

    /**
    * @var string $ciudad
    *
    * @ORM\Column(name="CIUDAD", type="string", nullable=false)
    */	
    private $ciudad;

    /**
    * @var string $severidad
    *
    * @ORM\Column(name="SEVERIDAD", type="string", nullable=false)
    */	
    private $severidad;

    /**
    * @var string $descripcionAlerta
    *
    * @ORM\Column(name="DESCRIPCION_ALERTA", type="string", nullable=false)
    */	
    private $descripcionAlerta;

    /**
    * @var string $valor
    *
    * @ORM\Column(name="VALOR", type="string", nullable=true)
    */	
    private $valor;

    /**
    * @var datetime $fechaOidActualizado
    *
    * @ORM\Column(name="FECHA_MODIFICACION", type="datetime", nullable=false)
    */	
    private $fechaModificacion;

    /**
    * @var string $estadoAlerta
    *
    * @ORM\Column(name="ESTADO_ALERTA", type="string", nullable=false)
    */	
    private $estadoAlerta;
    
    
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
    * Get idUps
    *
    * @return integer
    */	
    public function getIdUps()
    {
        return $this->idUps; 
    }

    /**
    * Get nombreUps
    *
    * @return string
    */
    public function getNombreUps()
    {
        return $this->nombreUps;
    }

    /**
    * Get nombreNodo
    *
    * @return string
    */
    public function getNombreNodo()
    {
        return $this->nombreNodo;
    }

    /**
    * Get ipUps
    *
    * @return string
    */	
    public function getIpUps()
    {
        return $this->ipUps;
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
    * Get generador
    *
    * @return string
    */	
    public function getGenerador()
    {
        return $this->generador;
    }

    /**
    * Get direccion
    *
    * @return string
    */	
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
    * Get latitud
    *
    * @return string
    */	
    public function getLatitud()
    {
        return $this->latitud;
    }

    /**
    * Get longitud
    *
    * @return string
    */	
    public function getLongitud()
    {
        return $this->longitud;
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
    * Get provincia
    *
    * @return string
    */	
    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
    * Get ciudad
    *
    * @return string
    */	
    public function getCiudad()
    {
        return $this->ciudad;
    }

    /**
    * Get severidad
    *
    * @return string
    */	
    public function getSeveridad()
    {
        return $this->severidad;
    }

    /**
    * Get descripcionAlerta
    *
    * @return string
    */	
    public function getDescripcionAlerta()
    {
        return $this->descripcionAlerta;
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
    * Get fechaModificacion
    *
    * @return datetime
    */	
    public function getFechaModificacion()
    {
        return $this->fechaModificacion;
    }

    /**
    * Get estadoAlerta
    *
    * @return string
    */	
    public function getEstadoAlerta()
    {
        return $this->estadoAlerta;
    }
}