<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoTramite
 *
 * @ORM\Table(name="INFO_TRAMITE")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoTramiteRepository")
 */
class InfoTramite
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_TRAMITE", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_TRAMITE", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var string $nombreTramite
*
* @ORM\Column(name="NOMBRE_TRAMITE", type="string", nullable=true)
*/		
     		
private $nombreTramite;

/**
* @var string $feIniComunicacion
*
* @ORM\Column(name="FE_INI_COMUNICACION", type="date", nullable=true)
*/		
     		
private $feIniComunicacion;

/**
* @var string $feFinComunicacion
*
* @ORM\Column(name="FE_FIN_COMUNICACION", type="date", nullable=true)
*/		
     		
private $feFinComunicacion;

/**
* @var string $descripcionTramite
*
* @ORM\Column(name="DESCRIPCION_TRAMITE", type="string", nullable=true)
*/		
     		
private $descripcionTramite;

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
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
*/		
     		
private $ipCreacion;


/**
* Get id
*
* @return integer
*/		
     		
public function getId(){
	return $this->id; 
}


   

    /**
     * Set nombreTramite
     *
     * @param string $nombreTramite
     * @return InfoTramite
     */
    public function setNombreTramite($nombreTramite)
    {
        $this->nombreTramite = $nombreTramite;
    
        return $this;
    }

    /**
     * Get nombreTramite
     *
     * @return string 
     */
    public function getNombreTramite()
    {
        return $this->nombreTramite;
    }

    /**
     * Set feIniComunicacion
     *
     * @param \DateTime $feIniComunicacion
     * @return InfoTramite
     */
    public function setFeIniComunicacion($feIniComunicacion)
    {
        $this->feIniComunicacion = $feIniComunicacion;
    
        return $this;
    }

    /**
     * Get feIniComunicacion
     *
     * @return \DateTime 
     */
    public function getFeIniComunicacion()
    {
        return $this->feIniComunicacion;
    }

    /**
     * Set feFinComunicacion
     *
     * @param \DateTime $feFinComunicacion
     * @return InfoTramite
     */
    public function setFeFinComunicacion($feFinComunicacion)
    {
        $this->feFinComunicacion = $feFinComunicacion;
    
        return $this;
    }

    /**
     * Get feFinComunicacion
     *
     * @return \DateTime 
     */
    public function getFeFinComunicacion()
    {
        return $this->feFinComunicacion;
    }

    /**
     * Set descripcionTramite
     *
     * @param string $descripcionTramite
     * @return InfoTramite
     */
    public function setDescripcionTramite($descripcionTramite)
    {
        $this->descripcionTramite = $descripcionTramite;
    
        return $this;
    }

    /**
     * Get descripcionTramite
     *
     * @return string 
     */
    public function getDescripcionTramite()
    {
        return $this->descripcionTramite;
    }

    /**
     * Set estado
     *
     * @param string $estado
     * @return InfoTramite
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
     * @return InfoTramite
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
     * @return InfoTramite
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
     * Set ipCreacion
     *
     * @param string $ipCreacion
     * @return InfoTramite
     */
    public function setIpCreacion($ipCreacion)
    {
        $this->ipCreacion = $ipCreacion;
    
        return $this;
    }

    /**
     * Get ipCreacion
     *
     * @return string 
     */
    public function getIpCreacion()
    {
        return $this->ipCreacion;
    }
}