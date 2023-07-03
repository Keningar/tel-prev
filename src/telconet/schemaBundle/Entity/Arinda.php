<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\Arinda
 *
 * @ORM\Table(name="ARINDA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\ArindaRepository")
 */
class Arinda
{

    /**
    * @var string $noCia
    *
    * @ORM\Column(name="NO_CIA", type="string", nullable=false)
    * @ORM\Id
    */		

    private $noCia;

    /**
    * @var string $clase
    *
    * @ORM\Column(name="CLASE", type="string", nullable=true)    
    */		

    private $clase;

    /**
    * @var string $categoria
    *
    * @ORM\Column(name="CATEGORIA", type="string", nullable=true)
    */		

    private $categoria;

    /**
    * @var string $noArti
    *
    * @ORM\Column(name="NO_ARTI", type="string", nullable=false)
    * @ORM\Id     
    */		

    private $noArti;

    /**
    * @var string $descripcion
    *
    * @ORM\Column(name="DESCRIPCION", type="string", nullable=true)
    */		

    private $descripcion;

    /**
    * @var string $unidad
    *
    * @ORM\Column(name="UNIDAD", type="string", nullable=false)
    */		

    private $unidad;

    /**
    * @var integer $peso
    *
    * @ORM\Column(name="PESO", type="integer", nullable=true)
    */		

    private $peso;

    
    
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
     * Set Clase
     *
     * @param string $clase
     */
    public function setCLase($clase)
    {
        $this->clase = $clase;    
    }

    /**
     * Get Clase
     *
     * @return string 
     */
    public function getClase()
    {
        return $this->clase;
    }  
    
    
     /**
     * Set Categoria
     *
     * @param string $categoria
     */
    public function setCategoria($categoria)
    {
        $this->categoria = $categoria;    
    }

    /**
     * Get Categoria
     *
     * @return string 
     */
    public function getCategoria()
    {
        return $this->categoria;
    } 
    
    
     /**
     * Set NoArti
     *
     * @param string $noArti
     */
    public function setNoArti($noArti)
    {
        $this->noArti = $noArti;    
    }

    /**
     * Get NoArti
     *
     * @return string 
     */
    public function getNoArti()
    {
        return $this->noArti;
    }     
    
    
     /**
     * Set Descripcion
     *
     * @param string $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;    
    }

    /**
     * Get Descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
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
     * Set Peso
     *
     * @param integer $peso
     */
    public function setPeso($peso)
    {
        $this->peso = $peso;    
    }

    /**
     * Get Peso
     *
     * @return integer 
     */
    public function getPeso()
    {
        return $this->peso; 
    }         
    
    
}
