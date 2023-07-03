<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiCuadrilla
 *
 * @ORM\Table(name="ADMI_CUADRILLA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiCuadrillaRepository")
 */
class AdmiCuadrilla
{
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_CUADRILLA", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_CUADRILLA", allocationSize=1, initialValue=1)
    */		

    private $id;	

    /**
    * @var string $nombreCuadrilla
    *
    * @ORM\Column(name="NOMBRE_CUADRILLA", type="string", nullable=false)
    */		

    private $nombreCuadrilla;

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
    * @ORM\Column(name="IP_CREACION", type="string", nullable=true)
    */		

    private $ipCreacion;

    /**
    * @var integer $coordinadorPrestadoId
    *
    * @ORM\Column(name="COORDINADOR_PRINCIPAL_ID", type="integer", nullable=true)
    */

    private $coordinadorPrincipalId;

    /**
    * @var integer $coordinadorPrestadoId
    *
    * @ORM\Column(name="COORDINADOR_PRESTADO_ID", type="integer", nullable=true)
    */

    private $coordinadorPrestadoId;

    
    /**
    * @var integer $departamentoId
    *
    * @ORM\Column(name="DEPARTAMENTO_ID", type="integer", nullable=true)
    */	
    private $departamentoId;

    
    /**
    * @var integer $zonaId
    *
    * @ORM\Column(name="ZONA_ID", type="integer", nullable=true)
    */	

    private $zonaId;

    
    /**
    * @var integer $tareaId
    *
    * @ORM\Column(name="TAREA_ID", type="integer", nullable=true)
    */	
    private $tareaId;
    

    /**
    * @var string $codigo
    *
    * @ORM\Column(name="CODIGO", type="string", nullable=true)
    */		

    private $codigo;
    
    /**
    * @var string $usrModificacion
    *
    * @ORM\Column(name="USR_MODIFICACION", type="string", nullable=true)
    */		

    private $usrModificacion;

    /**
    * @var datetime $feUltMod
    *
    * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
    */		

    private $feUltMod;

    /**
    * @var integer $vehiculoId
    *
    * @ORM\Column(name="VEHICULO_ID", type="integer", nullable=true)
    */
    private $vehiculoId;
    
    /**
    * @var string $turnoInicio
    *
    * @ORM\Column(name="TURNO_INICIO", type="string", nullable=true)
    */
    
    private $turnoInicio;
    
    /**
    * @var string $turnoFin
    *
    * @ORM\Column(name="TURNO_FIN", type="string", nullable=true)
    */
    private $turnoFin;

    /**
    * @var string $turnoHoraInicio
    *
    * @ORM\Column(name="TURNO_HORA_INICIO", type="string", nullable=true)
    */
    private $turnoHoraInicio;
    
    /**
    * @var string $turnoHoraFin
    *
    * @ORM\Column(name="TURNO_HORA_FIN", type="string", nullable=true)
    */
    private $turnoHoraFin;
    
    /**
     * @var string $estaLibre
     *
     * @ORM\Column(name="ESTA_LIBRE", type="string", nullable=true)
     */
    private $estaLibre;

    /**
     * @var string $esHal
     *
     * @ORM\Column(name="ES_HAL", type="string", nullable=true)
     */
    private $esHal;

    /**
     * @var string $preferencia
     *
     * @ORM\Column(name="PREFERENCIA", type="string", nullable=true)
     */
    private $preferencia;

    /**
     * @var string $esSatelite
     *
     * @ORM\Column(name="ES_SATELITE", type="string", nullable=true)
     */
    private $esSatelite;

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
    * Get nombreCuadrilla
    *
    * @return string
    */		

    public function getNombreCuadrilla()
    {
        return $this->nombreCuadrilla; 
    }

    /**
    * Set nombreCuadrilla
    *
    * @param string $nombreCuadrilla
    */
    public function setNombreCuadrilla($nombreCuadrilla)
    {
        $this->nombreCuadrilla = $nombreCuadrilla;
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

    public function __toString()
    {
        return $this->nombreCuadrilla;
    }
    
    /**
    * Get coordinadorPrincipalId
    *
    * @return integer
    */		

    public function getCoordinadorPrincipalId()
    {
        return $this->coordinadorPrincipalId; 
    }

    /**
    * Set coordinadorPrincipalId
    *
    * @param integer $coordinadorPrincipalId
    */
    public function setCoordinadorPrincipalId($coordinadorPrincipalId)
    {
        $this->coordinadorPrincipalId = $coordinadorPrincipalId;
    }
    
    /**
    * Get coordinadorPrestadoId
    *
    * @return integer
    */		

    public function getCoordinadorPrestadoId()
    {
        return $this->coordinadorPrestadoId; 
    }

    /**
    * Set coordinadorPrestadoId
    *
    * @param integer $coordinadorPrestadoId
    */
    public function setCoordinadorPrestadoId($coordinadorPrestadoId)
    {
        $this->coordinadorPrestadoId = $coordinadorPrestadoId;
    }
    
    /**
    * Get departamentoId
    *
    * @return integer
    */		
    public function getDepartamentoId()
    {
        return $this->departamentoId; 
    }

    /**
    * Set departamentoId
    *
    * @param integer $departamentoId
    */
    public function setDepartamentoId($departamentoId)
    {
        $this->departamentoId = $departamentoId;
    }
    
    
    /**
    * Get zonaId
    *
    * @return integer
    */		
    public function getZonaId()
    {
        return $this->zonaId; 
    }

    /**
    * Set zonaId
    *
    * @param integer $zonaId
    */
    public function setZonaId($zonaId)
    {
        $this->zonaId = $zonaId;
    }
    
    
    /**
    * Get tareaId
    *
    * @return integer
    */		
    public function getTareaId()
    {
        return $this->tareaId; 
    }

    
    /**
    * Set tareaId
    *
    * @param integer $tareaId
    */
    public function setTareaId($tareaId)
    {
        $this->tareaId = $tareaId;
    }
    
    
    
    /**
    * Get codigo
    *
    * @return string
    */		

    public function getCodigo()
    {
        return $this->codigo; 
    }

    /**
    * Set estado
    *
    * @param string $codigo
    */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
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
    * Get feUltMod
    *
    * @return datetime
    */		

    public function getFeUltMod()
    {
        return $this->feUltMod; 
    }

    /**
    * Set feUltMod
    *
    * @param datetime $feUltMod
    */
    public function setFeUltMod($feUltMod)
    {
        $this->feUltMod = $feUltMod;
    }

    /**
    * Set vehiculoId
    *
    * @param integer $vehiculoId
    */
    public function setVehiculoId($vehiculoId)
    {
        $this->vehiculoId = $vehiculoId;
    }
    
    /**
    * Get vehiculoId
    *
    * @return integer
    */		

    public function getVehiculoId()
    {
        return $this->vehiculoId; 
    }

    /**
    * Get turnoInicio
    *
    * @return string
    */		

    public function getTurnoInicio()
    {
        return $this->turnoInicio; 
    }

    /**
    * Set estado
    *
    * @param string $turnoInicio
    */
    public function setTurnoInicio($turnoInicio)
    {
        $this->turnoInicio = $turnoInicio;
    }
    
    /**
    * Get turnoFin
    *
    * @return string
    */		

    public function getTurnoFin()
    {
        return $this->turnoFin; 
    }

    /**
    * Set turnoFin
    *
    * @param string $turnoFin
    */
    public function setTurnoFin($turnoFin)
    {
        $this->turnoFin = $turnoFin;
    }
    
    /**
    * Get turnoHoraInicio
    *
    * @return string
    */		

    public function getTurnoHoraInicio()
    {
        return $this->turnoHoraInicio; 
    }

    /**
    * Set estado
    *
    * @param string $turnoHoraInicio
    */
    public function setTurnoHoraInicio($turnoHoraInicio)
    {
        $this->turnoHoraInicio = $turnoHoraInicio;
    }
    
    /**
    * Get turnoHoraFin
    *
    * @return string
    */		

    public function getTurnoHoraFin()
    {
        return $this->turnoHoraFin; 
    }

    /**
    * Set turnoHoraFin
    *
    * @param string $turnoHoraFin
    */
    public function setTurnoHoraFin($turnoHoraFin)
    {
        $this->turnoHoraFin = $turnoHoraFin;
    }
    
    /**
     * Get estaLibre
     *
     * @return string
     */
    public function getEstaLibre()
    {
        return $this->estaLibre; 
    }

    /**
    * Set estaLibre
    *
    * @param string $estaLibre
    */
    public function setEstaLibre($estaLibre)
    {
        $this->estaLibre = $estaLibre;
    }

    /**
     * Get esHal
     *
     * @return string
     */
    public function getEsHal()
    {
        return $this->esHal;
    }

    /**
    * Set esHal
    *
    * @param string $esHal
    */
    public function setEsHal($esHal)
    {
        $this->esHal = $esHal;
    }

        /**
     * Get preferencia
     *
     * @return string
     */
    public function getPreferencia()
    {
        return $this->preferencia;
    }

    /**
    * Set preferencia
    *
    * @param string $preferencia
    */
    public function setPreferencia($preferencia)
    {
        $this->preferencia = $preferencia;
    }

    /**
     * Get esSatelite
     *
     * @return string
     */
    public function getEsSatelite()
    {
        return $this->esSatelite;
    }

    /**
     * Set esSatelite
     *
     * @param string $esSatelite
     */
    public function setEsSatelite($esSatelite)
    {
        $this->esSatelite = $esSatelite;
    }

}