<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\VistaElementos
 *
 * @ORM\Table(name="VISTA_ELEMENTOS")
 * @ORM\Entity
 */ 
class VistaElementos
{

    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_ELEMENTO", type="integer", nullable=false)
    * @ORM\Id
    */		

    private $id;


    /**
    * @var integer $idTipoElemento
    *
    * @ORM\Column(name="ID_TIPO_ELEMENTO", type="integer", nullable=false)
    */		

    private $idTipoElemento;

    /**
        * @var string $nombreTipoElemento
    *
    * @ORM\Column(name="NOMBRE_TIPO_ELEMENTO", type="string", nullable=false)
    */		

    private $nombreTipoElemento;

    /**
    * @var string $nombreElemento
    *
    * @ORM\Column(name="NOMBRE_ELEMENTO", type="string", nullable=false)
    */		

    private $nombreElemento;
    
    /**
    * @var string $serieFisica
    *
    * @ORM\Column(name="SERIE_FISICA", type="string", nullable=false)
    */		

    private $serieFisica;

    /**
    * @var string $ip
    *
    * @ORM\Column(name="IP", type="string", nullable=false)
    */		

    private $ip;

    /**
    * @var string $idJurisdiccion
    *
    * @ORM\Column(name="ID_JURISDICCION", type="integer", nullable=false)
    */		

    private $idJurisdiccion;
    
    /**
    * @var string $nombreJurisdiccion
    *
    * @ORM\Column(name="NOMBRE_JURISDICCION", type="string", nullable=false)
    */		

    private $nombreJurisdiccion;

    /**
    * @var string $idCanton
    *
    * @ORM\Column(name="ID_CANTON", type="integer", nullable=false)
    */		

    private $idCanton;

    /**
    * @var string $nombreCanton
    *
    * @ORM\Column(name="NOMBRE_CANTON", type="string", nullable=false)
    */		

    private $nombreCanton;

    /**
    * @var string $idMarcaElemento
    *
    * @ORM\Column(name="ID_MARCA_ELEMENTO", type="integer", nullable=false)
    */		

    private $idMarcaElemento;
    
    /**
    * @var string $nombreMarcaElemento
    *
    * @ORM\Column(name="NOMBRE_MARCA_ELEMENTO", type="string", nullable=false)
    */		

    private $nombreMarcaElemento;
    
    /**
    * @var string $idModeloElemento
    *
    * @ORM\Column(name="ID_MODELO_ELEMENTO", type="integer", nullable=false)
    */		

    private $idModeloElemento;
    
    /**
    * @var string nombreModeloElemento
    *
    * @ORM\Column(name="NOMBRE_MODELO_ELEMENTO", type="string", nullable=false)
    */		

    private $nombreModeloElemento;
    
    /**
    * @var string $estadoElemento
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=false)
    */		

    private $estadoElemento;
    
    /**
    * @var string $empresaCod
    *
    * @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
    */		

    private $empresaCod;
    
    /**
    * @var string $idElementoB
    *
    * @ORM\Column(name="ID_ELEMENTO_B", type="integer", nullable=false)
    */		

    private $idElementoB;
    
    /**
    * @var string $nombreElementoB
    *
    * @ORM\Column(name="NOMBRE_ELEMENTO_B", type="string", nullable=false)
    */		

    private $nombreElementoB;
    
    /**
    * @var string $longitud
    *
    * @ORM\Column(name="LONGITUD", type="float", nullable=false)
    */		

    private $longitud;
    
    /**
    * @var string $latitud
    *
    * @ORM\Column(name="LATITUD", type="float", nullable=false)
    */		

    private $latitud;
    
    
    
    /**
    * Get id
    *
    * @return integer
    */	

    public function getId(){
            return $this->id; 
    }
    
    /**
    * Get idTipoElemento
    *
    * @return integer
    */	

    public function getIdTipoElemento(){
            return $this->idTipoElemento; 
    }
    
    /**
    * Get nombreTipoElemento
    *
    * @return string
    */	

    public function getNombreTipoElemento(){
            return $this->nombreTipoElemento; 
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
    * Get serieFisica
    *
    * @return string
    */		

    public function getSerieFisica(){
            return $this->serieFisica; 
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
    * Get ip
    *
    * @return string
    */		

    public function getIp(){
            return $this->ip; 
    }
    
    /**
    * Get idJurisdiccion
    *
    * @return integer
    */		

    public function getIdJurisdiccion(){
            return $this->idJurisdiccion; 
    }
    
    /**
    * Get nombreJurisdiccion
    *
    * @return string
    */		

    public function getNombreJurisdiccion(){
            return $this->nombreJurisdiccion; 
    }
    
    /**
    * Get idCanton
    *
    * @return integer
    */		

    public function getIdCanton(){
            return $this->idCanton; 
    }

    /**
    * Get nombreCanton
    *
    * @return string
    */		

    public function getNombreCanton(){
            return $this->nombreCanton; 
    }

    /**
    * Get idMarcaElemento
    *
    * @return integer
    */		

    public function getIdMarcaElemento(){
            return $this->idMarcaElemento; 
    }
    
    /**
    * Get nombreMarcaElemento
    *
    * @return string
    */		

    public function getNombreMarcaElemento(){
            return $this->nombreMarcaElemento; 
    }
    
    /**
    * Get idModeloElemento
    *
    * @return integer
    */		

    public function getIdModeloElemento(){
            return $this->idModeloElemento; 
    }
    
    /**
    * Get estadoElemento
    *
    * @return string
    */		

    public function getEstadoElemento(){
            return $this->estadoElemento; 
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
    * Get idElementoB
    *
    * @return integer
    */		

    public function getIdElementoB(){
            return $this->idElementoB; 
    }
    
    /**
    * Get nombreElementoB
    *
    * @return string
    */		

    public function getNombreElementoB(){
            return $this->nombreElementoB; 
    }
    
    /**
    * Get longitud
    *
    * @return float
    */		

    public function getLongitud(){
            return $this->longitud; 
    }
    
    /**
    * Get latitud
    *
    * @return float
    */		

    public function getLatitud(){
            return $this->latitud; 
    }

}