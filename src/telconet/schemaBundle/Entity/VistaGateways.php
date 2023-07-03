<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\VistaGateways
 *
 * @ORM\Table(name="VISTA_GATEWAYS")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\VistaGatewaysRepository")
 */ 
class VistaGateways
{

    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_ELEMENTO", type="integer", nullable=false)
    * @ORM\Id
    */		

    private $id;


    /**
    * @var string $nombreMarcaElemento
    *
    * @ORM\Column(name="NOMBRE_MARCA_ELEMENTO", type="string", nullable=false)
    */		

    private $nombreMarcaElemento;

    /**
        * @var string $nombreModeloElemento
    *
    * @ORM\Column(name="NOMBRE_MODELO_ELEMENTO", type="string", nullable=false)
    */		

    private $nombreModeloElemento;

    /**
    * @var string $nombreElemento
    *
    * @ORM\Column(name="NOMBRE_ELEMENTO", type="string", nullable=false)
    */		

    private $nombreElemento;

    /**
    * @var string $ip
    *
    * @ORM\Column(name="IP", type="string", nullable=false)
    */		

    private $ip;

    /**
    * @var string $nombreUsuarioAcceso
    *
    * @ORM\Column(name="NOMBRE_USUARIO_ACCESO", type="string", nullable=false)
    */		

    private $nombreUsuarioAcceso;
    
    /**
    * @var string $contrasena
    *
    * @ORM\Column(name="CONTRASENA", type="string", nullable=false)
    */		

    private $contrasena;

    /**
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=false)
    */		

    private $estado;
    
    /**
    * @var string $empresaCod
    *
    * @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
    */		

    private $empresaCod;

   
    
    /**
    * Get id
    *
    * @return integer
    */	

    public function getId(){
            return $this->id; 
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
    * Get nombreModeloElemento
    *
    * @return string
    */	

    public function getNombreModeloElemento(){
            return $this->nombreModeloElemento; 
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
    * Get ip
    *
    * @return string
    */		

    public function getIp(){
            return $this->ip; 
    }
    
    /**
    * Get nombreUsuarioAcceso
    *
    * @return string
    */		

    public function getNombreUsuarioAcceso(){
            return $this->nombreUsuarioAcceso; 
    }
    
    /**
    * Get contrasena
    *
    * @return string
    */		

    public function getContrasena(){
            return $this->contrasena; 
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
    * Get empresaCod
    *
    * @return string
    */		

    public function getEmpresaCod(){
            return $this->empresaCod; 
    }

}