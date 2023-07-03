<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiCuadrillaHistorial
 *
 * @ORM\Table(name="ADMI_CUADRILLA_HISTORIAL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiCuadrillaHistorialRepository")
 */
class AdmiCuadrillaHistorial
{
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_CUADRILLA_HISTORIAL", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_CUADRILLA_HISTORIAL", allocationSize=1, initialValue=1)
    */		
    private $id;	

    /**
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=false)
    */		
    private $estado;

    /**
    * @var string $observacion
    *
    * @ORM\Column(name="OBSERVACION", type="string", nullable=true)
    */		
    private $observacion;

    /**
    * @var string $usrCreacion
    *
    * @ORM\Column(name="USR_CREACION", type="string", nullable=false)
    */		
    private $usrCreacion;

    /**
    * @var datetime $feCreacion
    *
    * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
    */		
    private $feCreacion;

    
    /**
    * @var integer $motivoId
    *
    * @ORM\Column(name="MOTIVO_ID", type="integer", nullable=true)
    */
    private $motivoId;

    /**
    * @var AdmiCuadrilla
    *
    * @ORM\ManyToOne(targetEntity="AdmiCuadrilla", cascade={"persist"})
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="CUADRILLA_ID", referencedColumnName="ID_CUADRILLA")
    * })
    */
    private $cuadrillaId;
    

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
    * Get estado
    *
    * @return string
    */		
    public function getEstado()
    {
        return $this->estado; 
    }

    /**
    * Set estado
    *
    * @param string $estado
    */
    public function setEstado($estado)
    {
        $this->estado = $estado;
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
    * Set usrCreacion
    *
    * @param string $usrCreacion
    */
    public function setUsrCreacion($usrCreacion)
    {
        $this->usrCreacion = $usrCreacion;
    }


    /**
    * Get feCreacion
    *
    * @return datetime
    */		
    public function getFeCreacion()
    {
        return $this->feCreacion; 
    }

    /**
    * Set feCreacion
    *
    * @param datetime $feCreacion
    */
    public function setFeCreacion($feCreacion)
    {
        $this->feCreacion = $feCreacion;
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
    * Set observacion
    *
    * @param string $observacion
    */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
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

    /**
    * Set motivoId
    *
    * @param integer $motivoId
    */
    public function setMotivoId($motivoId)
    {
        $this->motivoId = $motivoId;
    }
    
    /**
    * Get cuadrillaId
    *
    * @return telconet\schemaBundle\Entity\AdmiCuadrilla
    */		

    public function getCuadrillaId()
    {
        return $this->cuadrillaId; 
    }

    /**
    * Set cuadrillaId
    *
    * @param telconet\schemaBundle\Entity\AdmiCuadrilla $cuadrillaId
    */
    public function setCuadrillaId(\telconet\schemaBundle\Entity\AdmiCuadrilla $cuadrillaId)
    {
        $this->cuadrillaId = $cuadrillaId;
    }
}