<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\Arinum
 *
 * @ORM\Table(name="ARINUM")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\ArinumRepository")
 */
class Arinum
{
    
/**
* @var string $noCia
*
* @ORM\Column(name="NO_CIA", type="string", nullable=false)
* @ORM\Id
*/		
     		
private $noCia;

/**
* @var string $unidad
*
* @ORM\Column(name="UNIDAD", type="string", nullable=false)
* @ORM\Id
*/		
     		
private $unidad;

/**
* @var string $nombre
*
* @ORM\Column(name="NOM", type="string", nullable=true)
*/		
     		
private $nombre;

/**
* @var string $tipo
*
* @ORM\Column(name="TIPO", type="string", nullable=false)
*/		
     		
private $tipo;

/**
* @var string $indFactCantidad
*
* @ORM\Column(name="IND_FACT_CANTIDAD", type="string", nullable=false)
*/		
     		
private $indFactCantidad;

/**
* @var string $abreviatura
*
* @ORM\Column(name="ABREVIATURA", type="string", nullable=true)
*/		
     		
private $abreviatura;

/**
* @var string $activo
*
* @ORM\Column(name="ACTIVO", type="string", nullable=true)
*/		
     		
private $activo;

    /**
     * Set NoCia
     *
     * @param string $noCia
     */
    public function setNoCia($noCia)
    {
        $this->noCia = $noCia;    
    }

    /**
     * Get NoCia
     *
     * @return string 
     */
    public function getNoCia()
    {
        return $this->noCia;
    }
    
    
    /**
     * Set Unidad
     *
     * @param string $unidad
     */
    public function setUnidad($unidad)
    {
        $this->unidad = $unidad;    
    }

    /**
     * Get Unidad
     *
     * @return string 
     */
    public function getUnidad()
    {
        return $this->unidad;
    }
    
    
    /**
     * Set Nombre
     *
     * @param string $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;    
    }

    /**
     * Get Nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }
    
    
    /**
     * Set Tipo
     *
     * @param string $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;    
    }

    /**
     * Get Tipo
     *
     * @return string 
     */
    public function getTipo()
    {
        return $this->tipo;
    }
    
    
    
    /**
     * Set IndFactCantidad
     *
     * @param string $indFactCantidad
     */
    public function setIndFactCantidad($indFactCantidad)
    {
        $this->indFactCantidad = $indFactCantidad;    
    }

    /**
     * Get IndFactCantidad
     *
     * @return string 
     */
    public function getIndFactCantidad()
    {
        return $this->indFactCantidad;
    }
    
    
    
    /**
     * Set Abreviatura
     *
     * @param string $abreviatura
     */
    public function setAbreviatura($abreviatura)
    {
        $this->abreviatura = $abreviatura;    
    }

    /**
     * Get Abreviatura
     *
     * @return string 
     */
    public function getAbreviatura()
    {
        return $this->abreviatura;
    }
    
    
    /**
     * Set Activo
     *
     * @param string $activo
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;    
    }

    /**
     * Get Activo
     *
     * @return string 
     */
    public function getActivo()
    {
        return $this->activo;
    }    

}
