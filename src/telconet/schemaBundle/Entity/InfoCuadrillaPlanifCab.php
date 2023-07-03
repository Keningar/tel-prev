<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoCuadrillaPlanifCab
 *
 * @ORM\Table(name="INFO_CUADRILLA_PLANIF_CAB")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoCuadrillaPlanifCabRepository")
 */
class InfoCuadrillaPlanifCab
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_CUADRILLA_PLANIF_CAB", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_CUADRILLA_PLANIF_CAB", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer $cuadrillaId
     *
     * @ORM\Column(name="CUADRILLA_ID", type="integer", nullable=false)
     */
    private $cuadrillaId;

    /**
     * @var integer $intervaloId
     *
     * @ORM\Column(name="INTERVALO_ID", type="integer", nullable=false)
     */
    private $intervaloId;
    
    /**
     * @var integer $zonaId
     *
     * @ORM\Column(name="ZONA_ID", type="integer", nullable=false)
     */
    private $zonaId;
    
    /**
     * @var string $empresaCod
     *
     * @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
     */
    private $empresaCod;
    
    /**
     * @var datetime $feTrabajo
     *
     * @ORM\Column(name="FE_TRABAJO", type="datetime", nullable=false)
     */
    private $feTrabajo;
    
    /**
     * @var string $asignadoMobile
     *
     * @ORM\Column(name="ASIGNADO_MOBILE", type="string", nullable=false)
     */
    private $asignadoMobile;

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
     * @var integer $zonaPrestadaId
     *
     * @ORM\Column(name="ZONA_PRESTADA_ID", type="integer", nullable=false)
     */
    private $zonaPrestadaId;

    /**
     * @var string $autorizaFinalizar
     *
     * @ORM\Column(name="AUTORIZA_FINALIZAR", type="string", nullable=true)
     */
    private $autorizaFinalizar;

    /**
     * @var string $autorizaAlimentacion
     *
     * @ORM\Column(name="AUTORIZA_ALIMENTACION", type="string", nullable=true)
     */
    private $autorizaAlimentacion;

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
     * Get cuadrillaId
     *
     * @return cuadrillaId
     */
    public function getCuadrillaId()
    {
        return $this->cuadrillaId;
    }

    /**
     * Set cuadrillaId
     *
     * @param integer $intCuadrillaId
     */
    public function setCuadrillaId($intCuadrillaId)
    {
        $this->cuadrillaId = $intCuadrillaId;
    }

    /**
     * Get intervaloId
     *
     * @return string
     */
    public function getIntervaloId()
    {
        return $this->intervaloId;
    }

    /**
     * Set intervaloId
     *
     * @param integer $intIntervaloId
     */
    public function setIntervaloId($intIntervaloId)
    {
        $this->intervaloId = $intIntervaloId;
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
     * @param integer $intZonaId
     */
    public function setZonaId($intZonaId)
    {
        $this->zonaId = $intZonaId;
    }
    
    /**
     * Get empresaCod
     *
     * @return string
     */
    public function getEmpresaCod()
    {
        return $this->empresaCod;
    }            

    /**
     * Set empresaCod
     *
     * @param string $strEmpresaCod
     */
    public function setEmpresaCod($strEmpresaCod)
    {
        $this->empresaCod = $strEmpresaCod;
    }
    
    /**
     * Get feTrabajo
     *
     * @return datetime
     */
    public function getFeTrabajo()
    {
        return $this->feTrabajo;
    }

    /**
     * Set feTrabajo
     *
     * @param datetime $strFeTrabajo
     */
    public function setFeTrabajo($strFeTrabajo)
    {
        $this->feTrabajo = $strFeTrabajo;
    }
    
    /**
     * Get asignadoMobile
     *
     * @return string
     */
    public function getAsignadoMobile()
    {
        return $this->asignadoMobile;
    }            

    /**
     * Set asignadoMobile
     *
     * @param string $strAsignadoMobile
     */
    public function setAsignadoMobile($strAsignadoMobile)
    {
        $this->asignadoMobile = $strAsignadoMobile;
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

    /**
     * Get zonaPrestadaId
     *
     * @return integer
     */
    public function getZonaPrestadaId()
    {
        return $this->zonaPrestadaId;
    }

    /**
     * Set zonaPrestadaId
     *
     * @param integer $intZonaPrestadaId
     */
    public function setZonaPrestadaId($intZonaPrestadaId)
    {
        $this->zonaPrestadaId = $intZonaPrestadaId;
    }

    /**
     * Get $autorizaFinalizar
     *
     * @return  string
     */ 
    public function getAutorizaFinalizar()
    {
        return $this->autorizaFinalizar;
    }

    /**
     * Set $autorizaFinalizar
     *
     * @param  string $autorizaFinalizar
     *
     */ 
    public function setAutorizaFinalizar($autorizaFinalizar)
    {
        $this->autorizaFinalizar = $autorizaFinalizar;

        return $this;
    }

    /**
     * Get $autorizaAlimentacion
     *
     * @return  string
     */ 
    public function getAutorizaAlimentacion()
    {
        return $this->autorizaAlimentacion;
    }

    /**
     * Set $autorizaAlimentacion
     *
     * @param  string $autorizaAlimentacion
     *
     */ 
    public function setAutorizaAlimentacion($autorizaAlimentacion)
    {
        $this->autorizaAlimentacion = $autorizaAlimentacion;

        return $this;
    }
}
