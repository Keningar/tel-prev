<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoElemento
 *
 * @ORM\Table(name="INFO_ELEMENTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoElementoRepository")
 */
class InfoElemento
{

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=false)
     */
    private $estado;
    
    /**
     * @var string $accesoPermanente
     *
     * @ORM\Column(name="ACCESO_PERMANENTE", type="string", nullable=true)
     */
    private $accesoPermanente;

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
     * @var string $ipCreacion
     *
     * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
     */
    private $ipCreacion;

    /**
     * @var string $usrResponsable
     *
     * @ORM\Column(name="USR_RESPONSABLE", type="string", nullable=true)
     */
    private $usrResponsable;

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_ELEMENTO", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_ELEMENTO", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var AdmiModeloElemento
     *
     * @ORM\ManyToOne(targetEntity="AdmiModeloElemento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="MODELO_ELEMENTO_ID", referencedColumnName="ID_MODELO_ELEMENTO")
     * })
     */
    private $modeloElementoId;

    /**
     * @var string $nombreElemento
     *
     * @ORM\Column(name="NOMBRE_ELEMENTO", type="string", nullable=true)
     */
    private $nombreElemento;

    /**
     * @var string $descripcionElemento
     *
     * @ORM\Column(name="DESCRIPCION_ELEMENTO", type="string", nullable=true)
     */
    private $descripcionElemento;

    /**
     * @var string $serieFisica
     *
     * @ORM\Column(name="SERIE_FISICA", type="string", nullable=true)
     */
    private $serieFisica;

    /**
     * @var string $serieLogica
     *
     * @ORM\Column(name="SERIE_LOGICA", type="string", nullable=true)
     */
    private $serieLogica;

    /**
     * @var string $versionOs
     *
     * @ORM\Column(name="VERSION_OS", type="string", nullable=true)
     */
    private $versionOs;

    /**
     * @var string $funcion
     *
     * @ORM\Column(name="FUNCION", type="string", nullable=true)
     */
    private $funcion;

    /**
     * @var string $claveConfiguracion
     *
     * @ORM\Column(name="CLAVE_CONFIGURACION", type="string", nullable=true)
     */
    private $claveConfiguracion;

    /**
     * @var DATE $feFabricacion
     *
     * @ORM\Column(name="FE_FABRICACION", type="date", nullable=true)
     */
    private $feFabricacion;

    /**
     * @ORM\OneToMany(targetEntity="InfoIpElemento", mappedBy="infoElemento")
     */
    private $ipElementoId;

    /**
     * @var string $unidadRack
     *
     */
    private $unidadRack;

    /**
     * @var string $nodoElementoId
     *
     */
    private $nodoElementoId;
    
    /**
     * @var string $marca
     *
     */
    private $marcaElementoId;

    /**
     * @var string $rackElementoId
     *
     */
    private $rackElementoId;

    /**
     * @var string $claseTipoMedioId
     *
     */
    private $claseTipoMedioId;

    /**
     * @var string $ipElemento
     *
     */
    private $ipElemento;
    
    /**
     * @var integer $refElementoId
     * 
     * @ORM\Column(name="REF_ELEMENTO_ID", type="integer", nullable=true)
     *
     */
    private $refElementoId;
    
    /**
     * @var string $macElemento
     *
     */
    private $macElemento;
    
   /**
    * @var string $switchElementoId
    *
    */
    private $switchElementoId;
    
    /**
    * @var string $interfaceSwitchId
    *
    */
    private $interfaceSwitchId;
    
    /**
    * @var string $sid
    *
    */
    private $sid;
    
    /**
    * @var string $tipoElementoRed
    *
    */
    private $tipoElementoRed;
    
    /**
    * @var string $radioInicioId
    *
    */
    private $radioInicioId;
    
    /**
    * @var string $factibilidadAutomatica
    *
    */
    private $factibilidadAutomatica;
    
    /**
    * @var string $anillo
    *
    */
    private $anillo;
    
    /**nodoElementoId
    * @var integer $idEdificacion
    *
    */
    private $idEdificacion;

    /**
     * Get refElementoId
     *
     * @return integer
     */
    public function getRefElementoId()
    {
        return $this->refElementoId;
    }

    /**
     * Set refElementoId
     *
     * @param integer $refElementoId
     */
    public function setRefElementoId($refElementoId)
    {
        $this->refElementoId = $refElementoId;
    }
    
    /**
     * Get factibilidadAutomatica
     *
     * @return string
     */
    public function getFactibilidadAutomatica()
    {
        return $this->factibilidadAutomatica;
    }

    /**
     * Set factibilidadAutomatica
     *
     * @param string $factibilidadAutomatica
     */
    public function setFactibilidadAutomatica($factibilidadAutomatica)
    {
        $this->factibilidadAutomatica = $factibilidadAutomatica;
    }

    /**
     * Get ipElemento
     *
     * @return string
     */
    public function getIpElemento()
    {
        return $this->ipElemento;
    }

    /**
     * Set ipElemento
     *
     * @param string $ipElemento
     */
    public function setIpElemento($ipElemento)
    {
        $this->ipElemento = $ipElemento;
    }
    
    /**
     * Get radioInicioId
     *
     * @return string
     */
    public function getRadioInicioId()
    {
        return $this->radioInicioId;
    }

    /**
     * Set radioInicioId
     *
     * @param string $radioInicioId
     */
    public function setRadioInicioId($radioInicioId)
    {
        $this->radioInicioId = $radioInicioId;
    }
    
    /**
     * Get tipoElementoRed
     *
     * @return string
     */
    public function getTipoElementoRed()
    {
        return $this->tipoElementoRed;
    }

    /**
     * Set tipoElementoRed
     *
     * @param string $tipoElementoRed
     */
    public function setTipoElementoRed($tipoElementoRed)
    {
        $this->tipoElementoRed = $tipoElementoRed;
    }

    /**
     * Get claseTipoMedioId
     *
     * @return string
     */
    public function getClaseTipoMedioId()
    {
        return $this->claseTipoMedioId;
    }

    /**
     * Set claseTipoMedioId
     *
     * @param string $claseTipoMedioId
     */
    public function setClaseTipoMedioId($claseTipoMedioId)
    {
        $this->claseTipoMedioId = $claseTipoMedioId;
    }

    /**
     * Get nodoElementoId
     *
     * @return string
     */
    public function getNodoElementoId()
    {
        return $this->nodoElementoId;
    }

    /**
     * Set nodoElementoId
     *
     * @param string $nodoElementoId
     */
    public function setNodoElementoId($nodoElementoId)
    {
        $this->nodoElementoId = $nodoElementoId;
    }
    
      /**
     * Get marcaElementoId
     *
     * @return string
     */
    public function getMarcaElementoId()
    {
        return $this->marcaElementoId;
    }    
    
    /**
     * Get anillo
     *
     * @return string
     */
    public function getAnillo()
    {
        return $this->anillo;
    }

    /**
     * Set anillo
     *
     * @param string $anillo
     */
    public function setAnillo($anillo)
    {
        $this->anillo = $anillo;
    }
    
    /**
     * Get idEdificacion
     *
     * @return integer
     */
    public function getIdEdificacion()
    {
        return $this->idEdificacion;
    }

    /**
     * Set idEdificacion
     *
     * @param integer $idEdificacion
     */
    public function setIdEdificacion($idEdificacion)
    {
        $this->idEdificacion = $idEdificacion;
    }

    /**
     * Get switchElementoId
     *
     * @return string
     */
    public function getSwitchElementoId()
    {
        return $this->switchElementoId;
    }

    /**
     * Set switchElementoId
     *
     * @param string $switchElementoId
     */
    public function setSwitchElementoId($switchElementoId)
    {
        $this->switchElementoId = $switchElementoId;
    }
    
    /**
     * Get interfaceSwitchId
     *
     * @return string
     */
    public function getInterfaceSwitchId()
    {
        return $this->interfaceSwitchId;
    }

    /**
     * Set $interfaceSwitchId
     *
     * @param string $interfaceSwitchId
     */
    public function setInterfaceSwitchId($interfaceSwitchId)
    {
        $this->interfaceSwitchId = $interfaceSwitchId;
    }
    
    /**
     * Get sid
     *
     * @return string
     */
    public function getSid()
    {
        return $this->sid;
    }

    /**
     * Set $sid
     *
     * @param string $sid
     */
    public function setSid($sid)
    {
        $this->sid = $sid;
    }

    /**
     * Get macElemento
     *
     * @return string
     */
    public function getMacElemento()
    {
        return $this->macElemento;
    }

    /**
     * Set macElemento
     *
     * @param string $macElemento
     */
    public function setMacElemento($macElemento)
    {
        $this->macElemento = $macElemento;
    }

    /**
     * Get rackElementoId
     *
     * @return string
     */
    public function getRackElementoId()
    {
        return $this->rackElementoId;
    }

    /**
     * Set rackElementoId
     *
     * @param string $rackElementoId
     */
    public function setRackElementoId($rackElementoId)
    {
        $this->rackElementoId = $rackElementoId;
    }

    /**
     * Add ipElementoId
     *
     * @param Telconet\schembaBundle\Entity\InfoIpElemento $ipElementoId
     */
    public function addInfoIpElemento(\telconet\schemaBundle\Entity\InfoIpElemento $ipElemento)
    {
        $this->ipElementoId[] = $ipElemento;
    }

    /**
     * Get ipElementoId
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getIpElementos()
    {
        return $this->ipElementoId;
    }

    /**
     * Get accesoPermanente
     *
     * @return string
     */
    public function getAccesoPermanente()
    {
        return $this->accesoPermanente;
    }

    /**
     * Set accesoPermanente
     *
     * @param string $accesoPermanente
     */
    public function setAccesoPermanente($accesoPermanente)
    {
        $this->accesoPermanente = $accesoPermanente;
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
     * Get unidadRack
     *
     * @return string
     */
    public function getUnidadRack()
    {
        return $this->unidadRack;
    }

    /**
     * Set unidadRack
     *
     * @param string $unidadRack
     */
    public function setUnidadRack($unidadRack)
    {
        $this->unidadRack = $unidadRack;
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

    /**
     * Get usrResponsable
     *
     * @return string
     */
    public function getUsrResponsable()
    {
        return $this->usrResponsable;
    }

    /**
     * Set usrResponsable
     *
     * @param string $usrResponsable
     */
    public function setUsrResponsable($usrResponsable)
    {
        $this->usrResponsable = $usrResponsable;
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
     * Get modeloElementoId
     *
     * @return telconet\schemaBundle\Entity\AdmiModeloElemento
     */
    public function getModeloElementoId()
    {
        return $this->modeloElementoId;
    }

    /**
     * Set modeloElementoId
     *
     * @param telconet\schemaBundle\Entity\AdmiModeloElemento $modeloElementoId
     */
    public function setModeloElementoId(\telconet\schemaBundle\Entity\AdmiModeloElemento $modeloElementoId)
    {
        $this->modeloElementoId = $modeloElementoId;
    }

    /**
     * Get nombreElemento
     *
     * @return string
     */
    public function getNombreElemento()
    {
        return $this->nombreElemento;
    }

    /**
     * Set nombreElemento
     *
     * @param string $nombreElemento
     */
    public function setNombreElemento($nombreElemento)
    {
        $this->nombreElemento = $nombreElemento;
    }

    /**
     * Get descripcionElemento
     *
     * @return string
     */
    public function getDescripcionElemento()
    {
        return $this->descripcionElemento;
    }

    /**
     * Set descripcionElemento
     *
     * @param string $descripcionElemento
     */
    public function setDescripcionElemento($descripcionElemento)
    {
        $this->descripcionElemento = $descripcionElemento;
    }

    /**
     * Get serieFisica
     *
     * @return string
     */
    public function getSerieFisica()
    {
        return $this->serieFisica;
    }

    /**
     * Set serieFisica
     *
     * @param string $serieFisica
     */
    public function setSerieFisica($serieFisica)
    {
        $this->serieFisica = $serieFisica;
    }

    /**
     * Get serieLogica
     *
     * @return string
     */
    public function getSerieLogica()
    {
        return $this->serieLogica;
    }

    /**
     * Set serieLogica
     *
     * @param string $serieLogica
     */
    public function setSerieLogica($serieLogica)
    {
        $this->serieLogica = $serieLogica;
    }

    /**
     * Get versionOs
     *
     * @return string
     */
    public function getVersionOs()
    {
        return $this->versionOs;
    }

    /**
     * Set versionOs
     *
     * @param string $versionOs
     */
    public function setVersionOs($versionOs)
    {
        $this->versionOs = $versionOs;
    }

    /**
     * Get funcion
     *
     * @return string
     */
    public function getFuncion()
    {
        return $this->funcion;
    }

    /**
     * Set funcion
     *
     * @param string $funcion
     */
    public function setFuncion($funcion)
    {
        $this->funcion = $funcion;
    }

    /**
     * Get claveConfiguracion
     *
     * @return string
     */
    public function getClaveConfiguracion()
    {
        return $this->claveConfiguracion;
    }

    /**
     * Set claveConfiguracion
     *
     * @param string $claveConfiguracion
     */
    public function setClaveConfiguracion($claveConfiguracion)
    {
        $this->claveConfiguracion = $claveConfiguracion;
    }

    /**
     * Get feFabricacion
     *
     * @return 
     */
    public function getFeFabricacion()
    {
        return $this->feFabricacion;
    }

    /**
     * Set feFabricacion
     *
     * @param  $feFabricacion
     */
    public function setFeFabricacion($feFabricacion)
    {
        $this->feFabricacion = $feFabricacion;
    }

    public function __toString()
    {
        return $this->nombreElemento;
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
}
