<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoContrasena
 *
 * @ORM\Table(name="INFO_CONTRASENA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoContrasenaRepository")
 */
class InfoContrasena
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_CONTRASENA", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_CONTRASENA", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $contrasena
*
* @ORM\Column(name="CONTRASENA", type="string", nullable=true)
* })
*/
		
private $contrasena;

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
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
*/		
     		
private $feCreacion;

/**
* @var string $usrUltMod
*
* @ORM\Column(name="USR_ULT_MOD", type="string", nullable=false)
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
     		
public function getId(){
	return $this->id; 
}
   

    /**
     * Get contrasena
     *
     * @return string 
     */
    public function getContrasena()
    {
        return $this->contrasena;
    }

    /**
     * Set contrasena
     *
     * @param string $contrasena     
     */
    public function setContrasena($contrasena)
    {
        $this->contrasena = $contrasena;
    
        return $this;
    }

  

    /**
     * Set estado
     *
     * @param string $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    
        return $this;
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
     * Set usrCreacion
     *
     * @param string $usrCreacion     
     */
    public function setUsrCreacion($usrCreacion)
    {
        $this->usrCreacion = $usrCreacion;
    
        return $this;
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
     * Set feCreacion
     *
     * @param \DateTime $feCreacion     
     */
    public function setFeCreacion($feCreacion)
    {
        $this->feCreacion = $feCreacion;
    
        return $this;
    }

    /**
     * Get feCreacion
     *
     * @return \DateTime 
     */
    public function getFeCreacion()
    {
        return $this->feCreacion;
    }

    
    
    
     /**
     * Set usrUltMod
     *
     * @param string $usrUltMod     
     */
    public function setUsrUltMod($usrUltMod)
    {
        $this->usrUltMod = $usrUltMod;
    
        return $this;
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
     * Set feUltMod
     *
     * @param \DateTime $feUltMod   
     */
    public function setFeUltMod($feUltMod)
    {
        $this->feUltMod = $feUltMod;
    
        return $this;
    }

    /**
     * Get feUltMod  
     * @return \DateTime 
     */
    public function getFeUltMod()
    {
        return $this->feUltMod;
    }
    
    
    
}