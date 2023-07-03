<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDocumentoRelacion
 *
 * @ORM\Table(name="INFO_DOCUMENTO_RELACION")
 * @ORM\Entity
 */
class InfoDocumentoRelacion
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_DOCUMENTO_RELACION", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DOCUMENTO_RELACION", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer $documentoId
     *
     * @ORM\Column(name="DOCUMENTO_ID", type="integer", nullable=true)
     */
    private $documentoId;

    /**
     * @var string $modulo
     *
     * @ORM\Column(name="MODULO", type="string", nullable=true)
     */
    private $modulo;

    /**
     * @var integer $encuestaId
     *
     * @ORM\Column(name="ENCUESTA_ID", type="integer", nullable=true)
     */
    private $encuestaId;

    /**
     * @var integer $servicioId
     *
     * @ORM\Column(name="SERVICIO_ID", type="integer", nullable=true)
     */
    private $servicioId;

    /**
     * @var integer $puntoId
     *
     * @ORM\Column(name="PUNTO_ID", type="integer", nullable=true)
     */
    private $puntoId;

    /**
     * @var integer $personaEmpresaRolId
     *
     * @ORM\Column(name="PERSONA_EMPRESA_ROL_ID", type="integer", nullable=true)
     */
    private $personaEmpresaRolId;

    /**
     * @var integer $contratoId
     *
     * @ORM\Column(name="CONTRATO_ID", type="integer", nullable=true)
     */
    private $contratoId;

    /**
     * @var integer $documentoFinancieroId
     *
     * @ORM\Column(name="DOCUMENTO_FINANCIERO_ID", type="integer", nullable=true)
     */
    private $documentoFinancieroId;

    /**
     * @var integer $casoId
     *
     * @ORM\Column(name="CASO_ID", type="integer", nullable=false)
     */
    private $casoId;

    /**
     * @var integer $actividadId
     *
     * @ORM\Column(name="ACTIVIDAD_ID", type="integer", nullable=false)
     */
    private $actividadId;

    /**
     * @var integer $tipoElementoId
     *
     * @ORM\Column(name="TIPO_ELEMENTO_ID", type="integer", nullable=false)
     */
    private $tipoElementoId;

    /**
     * @var integer $modeloElementoId
     *
     * @ORM\Column(name="MODELO_ELEMENTO_ID", type="integer", nullable=false)
     */
    private $modeloElementoId;

    /**
     * @var integer $elementoId
     *
     * @ORM\Column(name="ELEMENTO_ID", type="integer", nullable=false)
     */
    private $elementoId;

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
     * @var integer $detalleId
     *
     * @ORM\Column(name="DETALLE_ID", type="integer", nullable=false)
     */
    private $detalleId;

    /**
     * @var integer $mantenimientoElementoId
     *
     * @ORM\Column(name="MANTENIMIENTO_ELEMENTO_ID", type="integer", nullable=true)
     */
    private $mantenimientoElementoId;

    /**
     * @var integer $ordenTrabajoId
     *
     * @ORM\Column(name="ORDEN_TRABAJO_ID", type="integer", nullable=true)
     */
    private $ordenTrabajoId;

    /**
     * @var string $strEstadoEvaluacion
     *
     * @ORM\Column(name="ESTADO_EVALUACION", type="string", nullable=true)
     */
    private $strEstadoEvaluacion;

    /**
     * @var string $strEvaluacionTrabajo
     *
     * @ORM\Column(name="EVALUACION_TRABAJO", type="string", nullable=true)
     */
    private $strEvaluacionTrabajo;

    /**
     * @var datetime $feInicioEvaluacion
     *
     * @ORM\Column(name="FE_INICIO_EVALUACION", type="datetime", nullable=true)
     */
    private $objFeInicioEvaluacion;
    
    /**
     * @var string $strUsrEvaluacion
     *
     * @ORM\Column(name="USR_EVALUACION", type="string", nullable=true)
     */
    private $strUsrEvaluacion;
    
    /**
     * @var integer $pagaDatosId
     *
     * @ORM\Column(name="PAGO_DATOS_ID", type="integer", nullable=true)
     */
    private $pagaDatosId;
    

    /**
     * @var float $floatPorcentajeEvaluacionBase
     *
     * @ORM\Column(name="PORCENTAJE_EVALUACION_BASE", type="float", nullable=true)
     */
    private $floatPorcentajeEvaluacionBase;

    /**
     * @var float $floatPorcentajeEvaluado
     *
     * @ORM\Column(name="PORCENTAJE_EVALUADO", type="float", nullable=true)
     */
    private $floatPorcentajeEvaluado;

    /** 
     * @var string $numeroAdendum
     *
     * @ORM\Column(name="NUMERO_ADENDUM", type="string", nullable=true)
     */
    private $numeroAdendum;


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
     * Get documentoId
     *
     * @return integer
     */
    public function getDocumentoId()
    {
        return $this->documentoId;
    }

    /**
     * Set documentoId
     *
     * @param integer documentoId
     */
    public function setDocumentoId($documentoId)
    {
        $this->documentoId = $documentoId;
    }

//------------------------------------------------------------

    /**
     * Get encuestaId
     *
     * @return encuestaId
     */
    public function getEncuestaId()
    {
        return $this->encuestaId;
    }

    /**
     * Set encuestaId
     *
     * @param integer $encuestaId
     */
    public function setEncuestaId($encuestaId)
    {
        $this->encuestaId = $encuestaId;
    }

//------------------------------------------------------------

    /**
     * Get servicioId
     *
     * @return integer
     */
    public function getServicioId()
    {
        return $this->servicioId;
    }

    /**
     * Set servicioId
     *
     * @param integer $servicioId
     */
    public function setServicioId($servicioId)
    {
        $this->servicioId = $servicioId;
    }

//------------------------------------------------------------

    /**
     * Get puntoId
     *
     * @return integer
     */
    public function getPuntoId()
    {
        return $this->puntoId;
    }

    /**
     * Set puntoId
     *
     * @param integer $puntoId
     */
    public function setPuntoId($puntoId)
    {
        $this->puntoId = $puntoId;
    }

//------------------------------------------------------------

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
     * @param integer $personaEmpresaRolId
     */
    public function setPersonaEmpresaRolId($personaEmpresaRolId)
    {
        $this->personaEmpresaRolId = $personaEmpresaRolId;
    }

//------------------------------------------------------------

    /**
     * Get contratoId
     *
     * @return integer
     */
    public function getContratoId()
    {
        return $this->contratoId;
    }

    /**
     * Set contratoId
     *
     * @param integer $contratoId
     */
    public function setContratoId($contratoId)
    {
        $this->contratoId = $contratoId;
    }

//------------------------------------------------------------

    /**
     * Get documentoFinancieroId
     *
     * @return integer
     */
    public function getDocumentoFinancieroId()
    {
        return $this->documentoFinancieroId;
    }

    /**
     * Set documentoFinancieroId
     *
     * @param integer $documentoFinancieroId
     */
    public function setDocumentoFinancieroId($documentoFinancieroId)
    {
        $this->documentoFinancieroId = $documentoFinancieroId;
    }

//------------------------------------------------------------

    /**
     * Get casoId
     *
     * @return integer
     */
    public function getCasoId()
    {
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

//------------------------------------------------------------

    /**
     * Get actividadId
     *
     * @return integer
     */
    public function getActividadId()
    {
        return $this->actividadId;
    }

    /**
     * Set actividadId
     *
     * @param integer $actividadId
     */
    public function setActividadId($actividadId)
    {
        $this->actividadId = $actividadId;
    }

//------------------------------------------------------------

    /**
     * Get tipoElementoId
     *
     * @return integer
     */
    public function getTipoElementoId()
    {
        return $this->tipoElementoId;
    }

    /**
     * Set tipoElementoId
     *
     * @param integer $tipoElementoId
     */
    public function setTipoElementoId($tipoElementoId)
    {
        $this->tipoElementoId = $tipoElementoId;
    }

//------------------------------------------------------------

    /**
     * Get modeloElementoId
     *
     * @return integer
     */
    public function getModeloElementoId()
    {
        return $this->modeloElementoId;
    }

    /**
     * Set modeloElementoId
     *
     * @param integer $modeloElementoId
     */
    public function setModeloElementoId($modeloElementoId)
    {
        $this->modeloElementoId = $modeloElementoId;
    }

//------------------------------------------------------------

    /**
     * Get elementoId
     *
     * @return integer
     */
    public function getElementoId()
    {
        return $this->elementoId;
    }

    /**
     * Set elementoId
     *
     * @param integer $elementoId
     */
    public function setElementoId($elementoId)
    {
        $this->elementoId = $elementoId;
    }

    /**
     * Get modulo
     *
     * @return string
     */
    public function getModulo()
    {
        return $this->modulo;
    }

    /**
     * Set modulo
     *
     * @param string $modulo
     */
    public function setModulo($modulo)
    {
        $this->modulo = $modulo;
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
     * Get detalleId
     *
     * @return integer
     */
    public function getDetalleId()
    {
        return $this->detalleId;
    }

    /**
     * Set detalleId
     *
     * @param integer $detalleId
     */
    public function setDetalleId($detalleId)
    {
        $this->detalleId = $detalleId;
    }

    /**
     * Get mantenimientoElementoId
     *
     * @return integer
     */
    public function getMantenimientoElementoId()
    {
        return $this->mantenimientoElementoId;
    }

    /**
     * Set mantenimientoElementoId
     *
     * @param integer $mantenimientoElementoId
     */
    public function setMantenimientoElementoId($mantenimientoElementoId)
    {
        $this->mantenimientoElementoId = $mantenimientoElementoId;
    }

    /**
     * Get ordenTrabajoId
     *
     * @return integer
     */
    public function getOrdenTrabajoId()
    {
        return $this->ordenTrabajoId;
    }

    /**
     * Set ordenTrabajoId
     *
     * @param integer $ordenTrabajoId
     */
    public function setOrdenTrabajoId($ordenTrabajoId)
    {
        $this->ordenTrabajoId = $ordenTrabajoId;
    }

    /**
     * Get strEstadoEvaluacion
     * 
     * @return string
     */
    public function getEstadoEvaluacion()
    {
        return $this->strEstadoEvaluacion;
    }

    /**
     * Set strEstadoEvaluacion
     * 
     * @param string $strEstadoEvaluacion
     */
    public function setEstadoEvaluacion($strEstadoEvaluacion)
    {
        $this->strEstadoEvaluacion = $strEstadoEvaluacion;
    }

    /**
     * Get strEvaluacionTrabajo
     * 
     * @return string
     */
    public function getEvaluacionTrabajo()
    {
        return $this->strEvaluacionTrabajo;
    }

    /**
     * Set strEvaluacionTrabajo
     * 
     * @param string $strEvaluacionTrabajo
     */
    public function setEvaluacionTrabajo($strEvaluacionTrabajo)
    {
        $this->strEvaluacionTrabajo = $strEvaluacionTrabajo;
    }

    /**
     * Get objFeInicioEvaluacion
     *
     * @return datetime
     */
    public function getFeInicioEvaluacion()
    {
        return $this->objFeInicioEvaluacion;
    }

    /**
     * Set objFeInicioEvaluacion
     *
     * @param datetime $objFeInicioEvaluacion
     */
    public function setFeInicioEvaluacion($objFeInicioEvaluacion)
    {
        $this->objFeInicioEvaluacion = $objFeInicioEvaluacion;
    }
    
    /**
     * Get strUsrEvaluacion
     *
     * @return string
     */
    public function getUsrEvaluacion()
    {
        return $this->strUsrEvaluacion;
    }

    /**
     * Set strUsrEvaluacion
     *
     * @param string $strUsrEvaluacion
     */
    public function setUsrEvaluacion($strUsrEvaluacion)
    {
        $this->strUsrEvaluacion = $strUsrEvaluacion;
    }

    /**
     * Get $floatPorcentajeEvaluacionBase
     *
     * @return  float
     */ 
    public function getPorcentajeEvaluacionBase()
    {
        return $this->floatPorcentajeEvaluacionBase;
    }

    /**
     * Set $floatPorcentajeEvaluacionBase
     *
     * @param  float  $floatPorcentajeEvaluacionBase
     *
     * @return  self
     */ 
    public function setPorcentajeEvaluacionBase($floatPorcentajeEvaluacionBase)
    {
        $this->floatPorcentajeEvaluacionBase = $floatPorcentajeEvaluacionBase;

        return $this;
    }

    /**
     * Get $strPorcentajeEvaluado
     *
     * @return  float
     */ 
    public function getPorcentajeEvaluado()
    {
        return $this->floatPorcentajeEvaluado;
    }

    /**
     * Set $floatPorcentajeEvaluado
     *
     * @param  float  $floatPorcentajeEvaluado  $floatPorcentajeEvaluado
     *
     * @return  self
     */ 
    public function setPorcentajeEvaluado($floatPorcentajeEvaluado)
    {
        $this->floatPorcentajeEvaluado = $floatPorcentajeEvaluado;

        return $this;
    }
    /** 
     * Get strNumeroAdendum
     *
     * @return string
     */
    public function getNumeroAdendum()
    {
        return $this->numeroAdendum;
    }

    /**
     * Set strNumeroAdendum
     *
     * @param string $strNumeroAdendum
     */
    public function setNumeroAdendum($strNumeroAdendum)
    {
        $this->numeroAdendum = $strNumeroAdendum;
    }

    /**
     * Get pagaDatosId
     *
     * @return pagaDatosId
     */
    public function getPagaDatosId() {
        return $this->pagaDatosId;
    }

    /**
     * Set pagaDatosId
     *
     * @param integer $pagaDatosId
     */
    public function setPagaDatosId($pagaDatosId) {
        $this->pagaDatosId = $pagaDatosId;
    }
}
