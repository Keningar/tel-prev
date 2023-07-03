<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\VistaServiciosTecnicos
 *
 * @ORM\Table(name="VISTA_SERVICIOS_TECNICOS")
 * @ORM\Entity
 */ 
class VistaServiciosTecnicos
{

    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_VISTA", type="integer", nullable=false)
    * @ORM\Id
    */		

    private $id;

    /**
    * @var integer $idServicio
    *
    * @ORM\Column(name="ID_SERVICIO", type="integer", nullable=false)
    */		

    private $idServicio;
    

    /**
    * @var integer $puntoId
    *
    * @ORM\Column(name="PUNTO_ID", type="integer", nullable=false)
    */		

    private $puntoId;

    /**
        * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=false)
    */		

    private $estado;

    /**
    * @var string $tipoOrden
    *
    * @ORM\Column(name="TIPO_ORDEN", type="string", nullable=false)
    */		

    private $tipoOrden;

    /**
    * @var string $cantidad
    *
    * @ORM\Column(name="CANTIDAD", type="integer", nullable=false)
    */		

    private $cantidad;

    /**
    * @var string $descripcionProducto
    *
    * @ORM\Column(name="DESCRIPCION_PRODUCTO", type="string", nullable=false)
    */		

    private $descripcionProducto;
    
    /**
    * @var string $nombreTecnico
    *
    * @ORM\Column(name="NOMBRE_TECNICO", type="string", nullable=false)
    */		

    private $nombreTecnico;
    
    /**
    * @var string $descripcionProducto1
    *
    * @ORM\Column(name="DESCRIPCION_PRODUCTO1", type="string", nullable=false)
    */		

    private $descripcionProducto1;
    
    /**
    * @var string $nombreTecnico1
    *
    * @ORM\Column(name="NOMBRE_TECNICO1", type="string", nullable=false)
    */		

    private $nombreTecnico1;

    /**
    * @var string $nombres
    *
    * @ORM\Column(name="NOMBRES", type="string", nullable=false)
    */		

    private $nombres;

    /**
    * @var string $apellidos
    *
    * @ORM\Column(name="APELLIDOS", type="string", nullable=false)
    */		

    private $apellidos;

    /**
    * @var string $razonSocial
    *
    * @ORM\Column(name="RAZON_SOCIAL", type="string", nullable=false)
    */		

    private $razonSocial;
    
    /**
    * @var string $nombrePlan
    *
    * @ORM\Column(name="NOMBRE_PLAN", type="string", nullable=false)
    */		

    private $nombrePlan;
    
    /**
    * @var string $idElemento
    *
    * @ORM\Column(name="ID_ELEMENTO", type="integer", nullable=false)
    */		

    private $idElemento;
    
    /**
    * @var string nombreElemento
    *
    * @ORM\Column(name="NOMBRE_ELEMENTO", type="string", nullable=false)
    */		

    private $nombreElemento;
    
    /**
    * @var string $nombreModeloElemento
    *
    * @ORM\Column(name="NOMBRE_MODELO_ELEMENTO", type="string", nullable=false)
    */		

    private $nombreModeloElemento;
    
    /**
    * @var string $ip
    *
    * @ORM\Column(name="IP", type="string", nullable=false)
    */		

    private $ip;
    
    /**
    * @var string $idInterfaceElemento
    *
    * @ORM\Column(name="ID_INTERFACE_ELEMENTO", type="integer", nullable=false)
    */		

    private $idInterfaceElemento;
    
    /**
    * @var string $nombreInterfaceElemento
    *
    * @ORM\Column(name="NOMBRE_INTERFACE_ELEMENTO", type="string", nullable=false)
    */		

    private $nombreInterfaceElemento;
    
    /**
    * @var string $ultimaMilla
    *
    * @ORM\Column(name="NOMBRE_TIPO_MEDIO", type="string", nullable=false)
    */		

    private $ultimaMilla;
    
    /**
    * @var string $idProducto
    *
    * @ORM\Column(name="ID_PRODUCTO", type="integer", nullable=false)
    */		

    private $idProducto;
    
    /**
    * @var string $idProducto1
    *
    * @ORM\Column(name="ID_PRODUCTO1", type="integer", nullable=false)
    */		

    private $idProducto1;
    
    /**
    * @var string $login
    *
    * @ORM\Column(name="LOGIN", type="string", nullable=false)
    */		

    private $login;
    
    /**
    * @var string $idPlan
    *
    * @ORM\Column(name="ID_PLAN", type="integer", nullable=false)
    */		

    private $idPlan;
    
    /**
    * @var string $idPersona
    *
    * @ORM\Column(name="ID_PERSONA", type="integer", nullable=false)
    */		

    private $idPersona;
    
    /**
    * @var string $empresaCod
    *
    * @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
    */		

    private $empresaCod;
    
    /**
    * @var string $numeroOrdenTrabajo
    *
    * @ORM\Column(name="NUMERO_ORDEN_TRABAJO", type="string", nullable=false)
    */		

    private $numeroOrdenTrabajo;
    
    
    
    /**
    * Get id
    *
    * @return integer
    */	

    public function getId(){
            return $this->id; 
    }
    
    /**
    * Get idServicio
    *
    * @return integer
    */	

    public function getIdServicio(){
            return $this->idServicio; 
    }
    
    /**
    * Get idProducto
    *
    * @return integer
    */	

    public function getIdProducto(){
            return $this->idProducto; 
    }
    
    /**
    * Get idProducto1
    *
    * @return integer
    */	

    public function getIdProducto1(){
            return $this->idProducto1; 
    }

    /**
    * Get puntoId
    *
    * @return integer
    */		

    public function getPuntoId(){
            return $this->puntoId; 
    }
    
    /**
    * Get idPlan
    *
    * @return integer
    */		

    public function getIdPlan(){
            return $this->idPlan; 
    }
    
    /**
    * Get idPersona
    *
    * @return integer
    */		

    public function getIdPersona(){
            return $this->idPersona; 
    }
    
    /**
    * Get login
    *
    * @return string
    */		

    public function getLogin(){
            return $this->login; 
    }
    
    /**
    * Get empresaCod
    *
    * @return string
    */		

    public function getEmpresaCod(){
            return $this->empresaCod; 
    }

    /**
    * Get ip
    *
    * @return string
    */		

    public function getIp(){
            return $this->ip; 
    }


    /**
    * Get estado
    *
    * @return string
    */		

    public function getEstado(){
            return $this->estado; 
    }

    /**
    * Get tipoOrden
    *
    * @return string
    */		

    public function getTipoOrden(){
            return $this->tipoOrden; 
    }
    
    /**
    * Get cantidad
    *
    * @return integer
    */		

    public function getCantidad(){
            return $this->cantidad; 
    }
    
    /**
    * Get descripcionProducto
    *
    * @return string
    */		

    public function getDescripcionProducto(){
            return $this->descripcionProducto; 
    }
    
    /**
    * Get nombreTecnico
    *
    * @return string
    */		

    public function getNombreTecnico(){
            return $this->nombreTecnico; 
    }
    
    /**
    * Get descripcionProducto1
    *
    * @return string
    */		

    public function getDescripcionProducto1(){
            return $this->descripcionProducto1; 
    }
    
    /**
    * Get nombreTecnico1
    *
    * @return string
    */		

    public function getNombreTecnico1(){
            return $this->nombreTecnico1; 
    }
    
    /**
    * Get nombres
    *
    * @return string
    */		

    public function getNombres(){
            return $this->nombres; 
    }
    
    /**
    * Get apellidos
    *
    * @return string
    */		

    public function getApellidos(){
            return $this->apellidos; 
    }
    
    /**
    * Get razonSocial
    *
    * @return string
    */		

    public function getRazonSocial(){
            return $this->razonSocial; 
    }
    
    /**
    * Get nombrePlan
    *
    * @return string
    */		

    public function getNombrePlan(){
            return $this->nombrePlan; 
    }
    
    /**
    * Get idElemento
    *
    * @return integer
    */		

    public function getIdElemento(){
            return $this->idElemento; 
    }
    
    /**
    * Get nombreElemento
    *
    * @return string
    */		

    public function getNombreElemento(){
            return $this->nombreElemento; 
    }
    
    /**
    * Get nombreModeloElemento
    *
    * @return string
    */		

    public function getNombreModeloElemento(){
            return $this->nombreModeloElemento; 
    }
    
    /**
    * Get idInterfaceElemento
    *
    * @return integer
    */		

    public function getIdInterfaceElemento(){
            return $this->idInterfaceElemento; 
    }
    
    /**
    * Get nombreInterfaceElemento
    *
    * @return string
    */		

    public function getNombreInterfaceElemento(){
            return $this->nombreInterfaceElemento; 
    }
    
    /**
    * Get ultimaMilla
    *
    * @return string
    */		

    public function getUltimaMilla(){
            return $this->ultimaMilla; 
    }
    
    /**
    * Get numeroOrdenTrabajo
    *
    * @return string
    */		

    public function getNumeroOrdenTrabajo(){
            return $this->numeroOrdenTrabajo; 
    }

}