<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoServicioHistorial
 *
 * @ORM\Table(name="INFO_SERVICIO_HISTORIAL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoServicioHistorialRepository")
 */
class InfoServicioHistorial
{

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=true)
*/		
     		
private $estado;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
*/		
     		
private $feCreacion;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/		
     		
private $usrCreacion;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=true)
*/		
     		
private $ipCreacion;

/**
* @var string $motivoId
*
* @ORM\Column(name="MOTIVO_ID", type="integer", nullable=true)
*/		
     		
private $motivoId;

/**
* @var string $observacion
*
* @ORM\Column(name="OBSERVACION", type="string", nullable=true)
*/		
     		
private $observacion;

/**
* @var string $accion
*
* @ORM\Column(name="ACCION", type="string", nullable=true)
*/		
     		
private $accion;
	
/**
* @var InfoServicio
*
* @ORM\ManyToOne(targetEntity="InfoServicio")
* @ORM\JoinColumns({
*   @ORM\JoinColumn(name="SERVICIO_ID", referencedColumnName="ID_SERVICIO")
* })
*/		
     		
private $servicioId;

/**
* @var integer $id
*
* @ORM\Column(name="ID_SERVICIO_HISTORIAL", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_SERVICIO_HISTORIAL", allocationSize=1, initialValue=1)
*/		
		
private $id;	


    /**
     * Set estado
     *
     * @param string $estado
     * @return InfoServicioHistorial
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
     * Set feCreacion
     *
     * @param \DateTime $feCreacion
     * @return InfoServicioHistorial
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
     * Set usrCreacion
     *
     * @param string $usrCreacion
     * @return InfoServicioHistorial
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
     * Set ipCreacion
     *
     * @param string $ipCreacion
     * @return InfoServicioHistorial
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
    * Get servicioId
    *
    * @return telconet\schemaBundle\Entity\InfoServicio
    */		

    public function getServicioId(){
            return $this->servicioId; 
    }

    /**
    * Set servicioId
    *
    * @param telconet\schemaBundle\Entity\InfoServicio $servicioId
    */
    public function setServicioId(\telconet\schemaBundle\Entity\InfoServicio $servicioId)
    {
            $this->servicioId = $servicioId;
    }

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
     * Set observacion
     *
     * @param string $observacion
     * @return InfoServicioHistorial
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    
        return $this;
    }

    /**
     * Get observacion
     *
     * @return string 
     */
    public function getObservacion()
    {
        return $this->observacion;
    }
    
    /**
     * Set accion
     *
     * @param string $accion
     * @return InfoServicioHistorial
     */
    public function setAccion($accion)
    {
        $this->accion = $accion;
    
        return $this;
    }

    /**
     * Get accion
     *
     * @return string 
     */
    public function getAccion()
    {
        return $this->accion;
    }
    
    /**
     * Set motivoId
     *
     * @param integer $motivoId
     * @return InfoServicioHistorial
     */
    public function setMotivoId($motivoId)
    {
        $this->motivoId = $motivoId;
    
        return $this;
    }

    /**
     * Get motivoId
     *
     * @return integer 
     */
    public function getMotivoId()
    {
        return $this->motivoId;
    }
	
public function __clone() {
    $this->id = null;
}
	
}
