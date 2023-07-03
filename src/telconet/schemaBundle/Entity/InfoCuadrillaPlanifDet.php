<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoCuadrillaPlanifDet
 *
 * @ORM\Table(name="INFO_CUADRILLA_PLANIF_DET")
 * @ORM\Entity 
 */
class InfoCuadrillaPlanifDet
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_CUADRILLA_PLANIF_DET", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_CUADRILLA_PLANIF_DET", allocationSize=1, initialValue=1)
     */
    private $id;
    
    /**
     * @var integer $cuadrillaPlanifCabId
     *
     * @ORM\Column(name="CUADRILLA_PLANIF_CAB_ID", type="integer", nullable=false)
     */
    private $cuadrillaPlanifCabId;

    /**
     * @var datetime $feInicio
     *
     * @ORM\Column(name="FE_INICIO", type="datetime", nullable=false)
     */
    private $feInicio;

    /**
     * @var datetime $feFin
     *
     * @ORM\Column(name="FE_FIN", type="datetime", nullable=false)
     */
    private $feFin;
    
    /**
     * @var integer $personaEmpresaRolId
     *
     * @ORM\Column(name="PERSONA_EMPRESA_ROL_ID", type="integer", nullable=false)
     */
    private $personaEmpresaRolId;
    
    /**
     * @var integer $orden
     *
     * @ORM\Column(name="ORDEN", type="integer", nullable=false)
     */
    private $orden;
    
    /**
     * @var integer $locacion
     *
     * @ORM\Column(name="LOCACION", type="integer", nullable=false)
     */
    private $locacion;
    
    /**
     * @var integer $detalleSolicitudId
     *
     * @ORM\Column(name="DETALLE_SOLICITUD_ID", type="integer", nullable=false)
     */
    private $detalleSolicitudId;
    
    /**
     * @var integer $comunicacionId
     *
     * @ORM\Column(name="COMUNICACION_ID", type="integer", nullable=false)
     */
    private $comunicacionId;
    
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
     * @var string $usrModificacion
     *
     * @ORM\Column(name="USR_MODIFICACION", type="string", nullable=false)
     */
    private $usrModificacion;

    /**
     * @var datetime $feModificacion
     *
     * @ORM\Column(name="FE_MODIFICACION", type="datetime", nullable=false)
     */
    private $feModificacion;

    /**
     * @var string $ipModificacion
     *
     * @ORM\Column(name="IP_MODIFICACION", type="string", nullable=false)
     */
    private $ipModificacion;

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
     * Get feInicio
     *
     * @return feInicio
     */
    public function getFeInicio()
    {
        return $this->feInicio;
    }

    /**
     * Set feInicio
     *
     * @param string $strFeInicio
     */
    public function setFeInicio($strFeInicio)
    {
        $this->feInicio = $strFeInicio;
    }

    /**
     * Get feFin
     *
     * @return feFin
     */
    public function getFeFin()
    {
        return $this->feFin;
    }

    /**
     * Set feFin
     *
     * @param string $strFeFin
     */
    public function setFeFin($strFeFin)
    {
        $this->feFin = $strFeFin;
    }
    
    /**
     * Get cuadrillaPlanifCabId
     *
     * @return integer
     */
    public function getCuadrillaPlanifCabId()
    {
        return $this->cuadrillaPlanifCabId;
    }

    /**
     * Set cuadrillaPlanifCabId
     *
     * @param integer $intCuadrillaPlanifCabId
     */
    public function setCuadrillaPlanifCabId($intCuadrillaPlanifCabId)
    {
        $this->cuadrillaPlanifCabId = $intCuadrillaPlanifCabId;
    }
    
    /**
     * Get personaEmpresaRolId
     *
     * @return integer
     */
    public function getPersonaEmpresaRolId()
    {
        return $this->personaEmpresaRolId;
    }

    /**
     * Set personaEmpresaRolId
     *
     * @param integer $intPersonaEmpresaRolId
     */
    public function setPersonaEmpresaRolId($intPersonaEmpresaRolId)
    {
        $this->personaEmpresaRolId = $intPersonaEmpresaRolId;
    }
    
    /**
     * Get orden
     *
     * @return integer
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set orden
     *
     * @param integer $intOrden
     */
    public function setOrden($intOrden)
    {
        $this->orden = $intOrden;
    }
    
    /**
     * Set locacion
     *
     * @param integer $intLocacion
     */
    public function setLocacion($intLocacion)
    {
        $this->locacion = $intLocacion;
    }
    
    /**
     * Get locacion
     *
     * @return integer
     */
    public function getLocacion()
    {
        return $this->locacion;
    }   
    
    /**
     * Get detalleSolicitudId
     *
     * @return integer
     */
    public function getDetalleSolicitudId()
    {
        return $this->detalleSolicitudId;
    }

    /**
     * Set detalleSolicitudId
     *
     * @param integer $intDetalleSolicitudId
     */
    public function setDetalleSolicitudId($intDetalleSolicitudId)
    {
        $this->detalleSolicitudId = $intDetalleSolicitudId;
    }
    
    /**
     * Get comunicacionId
     *
     * @return integer
     */
    public function getComunicacionId()
    {
        return $this->comunicacionId;
    }

    /**
     * Set comunicacionId
     *
     * @param integer $intComunicacionId
     */
    public function setComunicacionId($intComunicacionId)
    {
        $this->comunicacionId = $intComunicacionId;
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
     * Get usrModificacion
     *
     * @return string
     */
    public function getUsrModificacion()
    {
        return $this->usrModificacion;
    }

    /**
     * Set usrModificacion
     *
     * @param string $usrModificacion
     */
    public function setUsrModificacion($usrModificacion)
    {
        $this->usrModificacion = $usrModificacion;
    }

    /**
     * Get feModificacion
     *
     * @return datetime
     */
    public function getFeModificacion()
    {
        return $this->feModificacion;
    }

    /**
     * Set feModificacion
     *
     * @param datetime $feModificacion
     */
    public function setFeModificacion($feModificacion)
    {
        $this->feModificacion = $feModificacion;
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
     * Set ipCreacion
     *
     * @param string $ipCreacion
     */
    public function setIpCreacion($ipCreacion)
    {
        $this->ipCreacion = $ipCreacion;
    }
        
    /**
     * Get ipModificacion
     *
     * @return string
     */
    public function getIpModificacion()
    {
        return $this->ipModificacion;
    }

    /**
     * Set ipModificacion
     *
     * @param string $ipModificacion
     */
    public function setIpModificacion($ipModificacion)
    {
        $this->ipModificacion = $ipModificacion;
    }
    
}
