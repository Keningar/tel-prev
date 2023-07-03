<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoMantenimientoProgramado
 *
 * @ORM\Table(name="INFO_MANTENIMIENTO_PROGRAMADO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoMantenimientoProgramadoRepository")
 */

class InfoMantenimientoProgramado 
{
   /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_MANTENIMIENTO_PROGRAMADO", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_MANT_PROGRAMADO", allocationSize=1, initialValue=1)
    */		
            
    private $id;

    /**
    * @var integer $casoId
    *
    * @ORM\Column(name="CASO_ID", type="integer", nullable=false)
    */		
                
    private $casoId;

    /**
    * @var string $codEmpresa
    *
    * @ORM\Column(name="COD_EMPRESA", type="string", nullable=false)
    */		
                
    private $codEmpresa;

    /**
    * @var datetime $fechaInicio
    *
    * @ORM\Column(name="FECHA_INICIO", type="datetime", nullable=false)
    */		
                
    private $fechaInicio;

    /**
    * @var datetime $fechaFin
    *
    * @ORM\Column(name="FECHA_FIN", type="datetime", nullable=false)
    */		
                
    private $fechaFin;

    /**
    * @var string $tiempoAfectacion
    *
    * @ORM\Column(name="TIEMPO_AFECTACION", type="string", nullable=false)
    */		
                
    private $tiempoAfectacion;

    /**
    * @var string $tipoAfectacion
    *
    * @ORM\Column(name="TIPO_AFECTACION", type="string", nullable=false)
    */		
                
    private $tipoAfectacion;

    /**
    * @var string $tipoNotificacion
    *
    * @ORM\Column(name="TIPO_NOTIFICACION", type="string", nullable=false)
    */		
                
    private $tipoNotificacion;

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
    * Get casoId
    *
    * @return integer
    */		
                
    public function getCasoId(){
        return $this->casoId; 
    }

    /**
    * Set casoId
    *
    * @param integer $casoId
    */
    public function setCasoId($casoId)
    {
            $this->casoId = $casoId;
    }

    /**
    * Get codEmpresa
    *
    * @return string
    */		
                
    public function getCodEmpresa(){
        return $this->codEmpresa; 
    }

    /**
    * Set codEmpresa
    *
    * @param string $codEmpresa
    */
    public function setCodEmpresa($codEmpresa)
    {
            $this->codEmpresa = $codEmpresa;
    }

    /**
    * Get fechaInicio
    *
    * @return datetime
    */		
                
    public function getFechaInicio(){
        return $this->fechaInicio; 
    }

    /**
    * Set fechaInicio
    *
    * @param datetime $fechaInicio
    */
    public function setFechaInicio($fechaInicio)
    {
            $this->fechaInicio = $fechaInicio;
    }

    /**
    * Get fechaFin
    *
    * @return datetime
    */		
                
    public function getFechaFin(){
        return $this->fechaFin; 
    }

    /**
    * Set fechaFin
    *
    * @param datetime $fechaFin
    */
    public function setFechaFin($fechaFin)
    {
            $this->fechaFin = $fechaFin;
    }

    /**
    * Get feCreacion
    *
    * @return datetime
    */		
                
    public function getFeCreacion(){
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
    * Get tiempoAfectacion
    *
    * @return string
    */		
                
    public function getTiempoAfectacion(){
        return $this->tiempoAfectacion; 
    }

    /**
    * Set tiempoAfectacion
    *
    * @param string $tiempoAfectacion
    */
    public function setTiempoAfectacion($tiempoAfectacion)
    {
            $this->tiempoAfectacion = $tiempoAfectacion;
    }  

    /**
    * Get tipoAfectacion
    *
    * @return string
    */		
                
    public function getTipoAfectacion(){
        return $this->tipoAfectacion; 
    }

    /**
    * Set tipoAfectacion
    *
    * @param string $tipoAfectacion
    */
    public function setTipoAfectacion($tipoAfectacion)
    {
            $this->tipoAfectacion = $tipoAfectacion;
    } 

    /**
    * Get tipoNotificacion
    *
    * @return string
    */		
                
    public function getTipoNotificacion(){
        return $this->tipoNotificacion; 
    }

    /**
    * Set tipoNotificacion
    *
    * @param string $tipoNotificacion
    */
    public function setTipoNotificacion($tipoNotificacion)
    {
            $this->tipoNotificacion = $tipoNotificacion;
    } 

    /**
    * Get usrCreacion
    *
    * @return string
    */		
                
    public function getUsrCreacion(){
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
    * Get ipCreacion
    *
    * @return string
    */		
                
    public function getIpCreacion(){
        return $this->ipCreacion; 
    }

    /**
    * Set ipCreacion
    *
    * @param string $ipCreacion
    */
    public function setIpCreacion($ipCreacion)
    {
            $this->ipCreacion = $ipCreacion;
    }

}
