<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPaqueteSoporteCab
 *
 * @ORM\Table(name="INFO_PAQUETE_SOPORTE_CAB")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPaqueteSoporteCabRepository")
 */
class InfoPaqueteSoporteCab
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_PAQUETE_SOPORTE_CAB", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PAQUETE_SOPORTE_CAB", allocationSize=1, initialValue=1)
     */

    private $id;

    /**
     * @var string $uuidPaqueteSoporteCab
     *
     * @ORM\Column(name="UUID_PAQUETE_SOPORTE_CAB", type="string", nullable=false)
     */

    private $uuidPaqueteSoporteCab;

    /**
     * @var integer $personaEmpresaRolId
     *
     * @ORM\Column(name="PERSONA_EMPRESA_ROL_ID", type="integer", nullable=false)
     */

    private $personaEmpresaRolId;

    /**
     * @var string $loginPuntoPaquete
     *
     * @ORM\Column(name="LOGIN_PUNTO_PAQUETE", type="string", nullable=false)
     */

    private $loginPuntoPaquete;

    /**
     * @var integer $servicioId
     *
     * @ORM\Column(name="SERVICIO_ID", type="integer", nullable=false)
     */

    private $servicioId;

    /**
     * @var string $loginServicioPaquete
     *
     * @ORM\Column(name="LOGIN_SERVICIO_PAQUETE", type="string", nullable=false)
     */

    private $loginServicioPaquete;

    /**
     * @var string $empresaCod
     *
     * @ORM\Column(name="EMPRESA_COD", type="string", nullable=true)
     */

    private $empresaCod;

    /**
     * @var integer $minutosAcumulados
     *
     * @ORM\Column(name="MINUTOS_ACUMULADOS", type="integer", nullable=false)
     */

    private $minutosAcumulados;

    /**
     * @var integer $minutosVigentes
     *
     * @ORM\Column(name="MINUTOS_VIGENTES", type="integer", nullable=false)
     */

    private $minutosVigentes;

    /**
     * @var integer $minutosTotales
     *
     * @ORM\Column(name="MINUTOS_TOTALES", type="integer", nullable=false)
     */

    private $minutosTotales;

    /**
     *
     * @var \Date $feInicioPaquete
     *      @ORM\Column(name="FECHA_INICIO_PAQUETE", type="date", nullable=false)
     */
    private $feInicioPaquete;

    /**
     *
     * @var \Date $feFinPaquete
     *      @ORM\Column(name="FECHA_FIN_PAQUETE", type="date", nullable=false)
     */
    private $feFinPaquete;
    
    /**
     *
     * @var \Date $feNotificacionXminuto
     *      @ORM\Column(name="FECHA_NOTIFICACION_X_MINUTOS", type="date", nullable=true)
     */
    private $feNotificacionXminuto;
    
    /**
     *
     * @var \Date $feNotificacionXfecha
     *      @ORM\Column(name="FECHA_NOTIFICACION_X_FECHA", type="date", nullable=true)
     */
    private $feNotificacionXfecha;
    
    /**
     *
     * @var string $observacion
     *      @ORM\Column(name="OBSERVACION", type="string", nullable=true)
     */
    private $observacion;
    
    /**
     *
     * @var string $usuarioCreacion
     *      @ORM\Column(name="USUARIO_CREACION", type="string", nullable=false)
     */
    private $usuarioCreacion;
    
    /**
     *
     * @var \Date $fechaCreacion
     *      @ORM\Column(name="FECHA_CREACION", type="date", nullable=false)
     */
    private $fechaCreacion;
    
    /**
     *
     * @var string $usuarioModificacion
     *      @ORM\Column(name="USUARIO_MODIFICACION", type="string", nullable=true)
     */
    private $usuarioModificacion;
    
    /**
     *
     * @var \Date $fechaModificacion
     *      @ORM\Column(name="FECHA_MODIFICACION", type="date", nullable=true)
     */
    private $fechaModificacion;









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
     * Get uuidPaqueteSoporteCab
     *
     * @return string
     */

    public function getUuIdPaqueteSoporteCab()
    {
        return $this->uuidPaqueteSoporteCab;
    }

    /**
     * Set uuidPaqueteSoporteCab
     *
     * @param string $intUuIdPaqueteSoporteCab
     */
    public function setUuIdPaqueteSoporteCab($intUuIdPaqueteSoporteCab)
    {
        $this->uuidPaqueteSoporteCab = $intUuIdPaqueteSoporteCab;
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
     * Get loginPuntoPaquete
     *
     * @return string
     */

    public function getLoginPuntoPaquete()
    {
        return $this->loginPuntoPaquete;
    }

    /**
     * Set loginPuntoPaquete
     *
     * @param string $strLoginPuntoPaquete
     */
    public function setLoginPuntoPaquete($strLoginPuntoPaquete)
    {
        $this->loginPuntoPaquete = $strLoginPuntoPaquete;
    }

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
     * @param integer $strServicioId
     */
    public function setServicioId($strServicioId)
    {
        $this->servicioId = $strServicioId;
    }

    /**
     * Get loginServicioPaquete
     *
     * @return string
     */

    public function getLoginServicioPaquete()
    {
        return $this->loginServicioPaquete;
    }

    /**
     * Set loginServicioPaquete
     *
     * @param string $strLoginServicioPaquete
     */
    public function setLoginServicioPaquete($strLoginServicioPaquete)
    {
        $this->loginServicioPaquete = $strLoginServicioPaquete;
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
     * Get minutosAcumulados
     *
     * @return integer
     */

    public function getMinutosAcumulados()
    {
        return $this->minutosAcumulados;
    }

    /**
     * Set minutosAcumulados
     *
     * @param integer $strMinutosAcumulados
     */
    public function setMinutosAcumulados($strMinutosAcumulados)
    {
        $this->minutosAcumulados = $strMinutosAcumulados;
    }

    /**
     * Get minutosVigentes
     *
     * @return integer
     */

    public function getMinutosVigentes()
    {
        return $this->minutosVigentes;
    }

    /**
     * Set minutosVigentes
     *
     * @param integer $strMinutosVigentes
     */
    public function setMinutosVigentes($strMinutosVigentes)
    {
        $this->minutosVigentes = $strMinutosVigentes;
    }

    /**
     * Get minutosTotales
     *
     * @return integer
     */

    public function getMinutosTotales()
    {
        return $this->minutosTotales;
    }

    /**
     * Set minutosTotales
     *
     * @param integer $strMinutosTotales
     */
    public function setMinutosTotales($strMinutosTotales)
    {
        $this->minutosTotales = $strMinutosTotales;
    }

    /**
     * Get feInicioPaquete
     * 
     * @param \Date
     */
    public function getFeInicioPaquete()
    {
        return $this->feInicioPaquete;
    }

    /**
     * Set feInicioPaquete
     * 
     * @param \Date $feInicioPaquete            
     */
    public function setFeInicioPaquete($dateFeInicioPaquete)
    {
        $this->feInicioPaquete = $dateFeInicioPaquete;
    }

    /**
     * Get feFinPaquete
     * 
     * @return \Date
     */
    public function getFeFinPaquete()
    {
        return $this->feFinPaquete;
    }

    /**
     * Set feFinPaquete
     * 
     * @param \Date $feFinPaquete            
     */
    public function setFeFinPaquete($dateFeFinPaquete)
    {
        $this->feFinPaquete = $dateFeFinPaquete;
    }

    /**
     * Get feNotificacionXminuto
     * 
     * @return \Date
     */
    public function getFeNotificacionXminuto()
    {
        return $this->feNotificacionXminuto;
    }

    /**
     * Set feNotificacionXminuto
     * 
     * @param \Date $feNotificacionXminuto            
     */
    public function setFeNotificacionXminuto($dateFeNotificacionXminuto)
    {
        $this->feNotificacionXminuto = $dateFeNotificacionXminuto;
    }

    /**
     * Get feNotificacionXfecha
     * 
     * @return \Date
     */
    public function getFeNotificacionXfecha()
    {
        return $this->feNotificacionXfecha;
    }

    /**
     * Set feNotificacionXfecha
     * 
     * @param \Date $feNotificacionXfecha            
     */
    public function setFeNotificacionXfecha($dateFeNotificacionXfecha)
    {
        $this->feNotificacionXfecha = $dateFeNotificacionXfecha;
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
    public function setObservacion($strObservacion)
    {
        $this->observacion = $strObservacion;
    }

    /**
     * Get usuarioCreacion
     * 
     * @return string
     */
    public function getUsuarioCreacion()
    {
        return $this->usuarioCreacion;
    }

    /**
     * Set usuarioCreacion
     * 
     * @param string $usuarioCreacion            
     */
    public function setUsuarioCreacion($strUsuarioCreacion)
    {
        $this->usuarioCreacion = $strUsuarioCreacion;
    }

    /**
     * Get fechaCreacion
     * 
     * @return \Date
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set fechaCreacion
     * 
     * @param \Date
     */
    public function setFechaCreacion($strFechaCreacion)
    {
        $this->fechaCreacion = $strFechaCreacion;
    }


    /**
     * Get usuarioModificacion
     * 
     * @return string
     */
    public function getUsuarioModificacion()
    {
        return $this->usuarioModificacion;
    }

    /**
     * Set usuarioModificacion
     * 
     * @param string $usuarioModificacion            
     */
    public function setUsuarioModificacion($strUsuarioModificacion)
    {
        $this->usuarioModificacion = $strUsuarioModificacion;
    }

    /**
     * Get fechaModificacion
     * 
     * @return \Date
     */
    public function getFechaModificacion()
    {
        return $this->fechaModificacion;
    }

    /**
     * Set fechaModificacion
     * 
     * @param \Date
     */
    public function setFechaModificacion($strFechaModificacion)
    {
        $this->fechaModificacion = $strFechaModificacion;
    }
}
