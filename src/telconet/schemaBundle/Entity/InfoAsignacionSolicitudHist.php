<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoAsignacionSolicitudHist
 *
 * @ORM\Table(name="INFO_ASIGNACION_SOLICITUD_HIST")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoAsignacionSolicitudHistRepository")
 */
class InfoAsignacionSolicitudHist
{

    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_ASIGNACION_SOLICITUD_HIST", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_ASIGNACION_SOL_HIST", allocationSize=1, initialValue=1)
    */
    private $id;	

    /**
    * @var $asignacionSolicitudId
    *
    * @ORM\ManyToOne(targetEntity="InfoAsignacionSolicitud")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="ASIGNACION_SOLICITUD_ID", referencedColumnName="ID_ASIGNACION_SOLICITUD")
    * })
    */
    private $asignacionSolicitudId;

    /**
    * @var string $tipo
    *
    * @ORM\Column(name="TIPO", type="string", nullable=false)
    */
    private $tipo;

    /**
    * @var string $usrAsignado
    *
    * @ORM\Column(name="USR_ASIGNADO", type="string", nullable=false)
    */
    private $usrAsignado;



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
    * @var string
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=true)
    */
    private $estado;

    /**
    * @var datetime $feCambioTurno
    *
    * @ORM\Column(name="FE_CAMBIO_TURNO", type="datetime", nullable=false)
    */
    private $feCambioTurno;



    /**
    * Get id
    *
    * @return integer
    */
    public function getId(){
            return $this->id; 
    }


    /**
      * Set asignacionSolicitudId
      *
      * @param \telconet\schemaBundle\Entity\InfoAsignacionSolicitud $asignacionSolicitudId
      * 
      */
    public function setAsignacionSolicitudId(\telconet\schemaBundle\Entity\InfoAsignacionSolicitud $asignacionSolicitudId = null)
    {
        $this->asignacionSolicitudId = $asignacionSolicitudId;  
    }

    /**
      * Get asignacionSolicitudId
      *
      * @return \telconet\schemaBundle\Entity\InfoAsignacionSolicitud
      */
    public function getAsignacionSolicitudId()
    {
        return $this->asignacionSolicitudId;
    }



    /**
    * Get tipo
    *
    * @return string
    */
    public function getTipo(){
            return $this->tipo; 
    }

    /**
    * Set tipo
    *
    * @param string $tipo
    */
    public function setTipo($tipo)
    {
            $this->tipo = $tipo;
    }

    /**
    * Get usrAsignado
    *
    * @return string
    */
    public function getUsrAsignado(){
            return $this->usrAsignado; 
    }

    /**
    * Set usrAsignado
    *
    * @param string $usrAsignado
    */
    public function setUsrAsignado($usrAsignado)
    {
            $this->usrAsignado = $usrAsignado;
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
    * Get feCambioTurno
    *
    * @return datetime
    */
    public function getFeCambioTurno()
    {
        return $this->feCambioTurno; 
    }

    /**
    * Set feCambioTurno 
    *
    * @param datetime $feCambioTurno
    */
    public function setFeCambioTurno($feCambioTurno)
    {
        $this->feCambioTurno = $feCambioTurno;
    }


}