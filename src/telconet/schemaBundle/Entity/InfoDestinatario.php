<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDestinatario
 *
 * @ORM\Table(name="INFO_DESTINATARIO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDestinatarioRepository")
 */
class InfoDestinatario
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_DESTINATARIO", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DESTINATARIO", allocationSize=1, initialValue=1)
*/		
		
private $id;	
	
/**
* @var $comunicacionId
*
* @ORM\ManyToOne(targetEntity="InfoComunicacion")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="COMUNICACION_ID", referencedColumnName="ID_COMUNICACION")
* })
*/
		
private $comunicacionId;

/**
* @var string $personaId
*
* @ORM\Column(name="PERSONA_ID", type="integer", nullable=true)
*/		
     		
private $personaId;

/**
* @var string $personaNombre
*
* @ORM\Column(name="PERSONA_NOMBRE", type="string", nullable=true)
*/		
     		
private $personaNombre;

/**
* @var integer $personaFormaContactoId
*
* @ORM\Column(name="PERSONA_FORMA_CONTACTO_ID", type="integer", nullable=true)
*/		
     		
private $personaFormaContactoId;

/**
* @var string $personaFormaContactoNombre
*
* @ORM\Column(name="PERSONA_FORMA_CONTACTO_NOMBRE", type="string", nullable=true)
*/		
     		
private $personaFormaContactoNombre;

/**
* @var integer $referenciaId
*
* @ORM\Column(name="REFERENCIA_ID", type="integer", nullable=true)
*/		
     		
private $referenciaId;

/**
* @var string $referenciaNombre
*
* @ORM\Column(name="REFERENCIA_NOMBRE", type="string", nullable=true)
*/		
     		
private $referenciaNombre;
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
     * Set personaId
     *
     * @param integer $personaId
     * @return InfoDestinatario
     */
    public function setPersonaId($personaId)
    {
        $this->personaId = $personaId;
    
        return $this;
    }

    /**
     * Get personaId
     *
     * @return integer 
     */
    public function getPersonaId()
    {
        return $this->personaId;
    }

    /**
     * Set personaNombre
     *
     * @param string $personaNombre
     * @return InfoDestinatario
     */
    public function setPersonaNombre($personaNombre)
    {
        $this->personaNombre = $personaNombre;
    
        return $this;
    }

    /**
     * Get personaNombre
     *
     * @return string 
     */
    public function getPersonaNombre()
    {
        return $this->personaNombre;
    }

    /**
     * Set personaFormaContactoId
     *
     * @param integer $personaFormaContactoId
     * @return InfoDestinatario
     */
    public function setPersonaFormaContactoId($personaFormaContactoId)
    {
        $this->personaFormaContactoId = $personaFormaContactoId;
    
        return $this;
    }

    /**
     * Get personaFormaContactoId
     *
     * @return integer 
     */
    public function getPersonaFormaContactoId()
    {
        return $this->personaFormaContactoId;
    }

    /**
     * Set estado
     *
     * @param string $estado
     * @return InfoDestinatario
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
     * @return InfoDestinatario
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
     * @return InfoDestinatario
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
     * @return InfoDestinatario
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

    /**
     * Set comunicacionId
     *
     * @param \telconet\schemaBundle\Entity\InfoComunicacion $comunicacionId
     * @return InfoDestinatario
     */
    public function setComunicacionId(\telconet\schemaBundle\Entity\InfoComunicacion $comunicacionId = null)
    {
        $this->comunicacionId = $comunicacionId;
    
        return $this;
    }

    /**
     * Get comunicacionId
     *
     * @return \telconet\schemaBundle\Entity\InfoComunicacion 
     */
    public function getComunicacionId()
    {
        return $this->comunicacionId;
    }

    /**
     * Set personaFormaContactoNombre
     *
     * @param string $personaFormaContactoNombre
     * @return InfoDestinatario
     */
    public function setPersonaFormaContactoNombre($personaFormaContactoNombre)
    {
        $this->personaFormaContactoNombre = $personaFormaContactoNombre;
    
        return $this;
    }

    /**
     * Get personaFormaContactoNombre
     *
     * @return string 
     */
    public function getPersonaFormaContactoNombre()
    {
        return $this->personaFormaContactoNombre;
    }

    /**
     * Set referenciaId
     *
     * @param integer $referenciaId
     * @return InfoDestinatario
     */
    public function setReferenciaId($referenciaId)
    {
        $this->referenciaId = $referenciaId;
    
        return $this;
    }

    /**
     * Get referenciaId
     *
     * @return integer 
     */
    public function getReferenciaId()
    {
        return $this->referenciaId;
    }

    /**
     * Set referenciaNombre
     *
     * @param string $referenciaNombre
     * @return InfoDestinatario
     */
    public function setReferenciaNombre($referenciaNombre)
    {
        $this->referenciaNombre = $referenciaNombre;
    
        return $this;
    }

    /**
     * Get referenciaNombre
     *
     * @return string 
     */
    public function getReferenciaNombre()
    {
        return $this->referenciaNombre;
    }
}