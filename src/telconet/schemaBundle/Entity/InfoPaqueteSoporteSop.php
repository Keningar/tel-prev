<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPaqueteSoporteSop
 *
 * @ORM\Table(name="INFO_PAQUETE_SOPORTE_SOP")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPaqueteSoporteSopRepository")
 */
class InfoPaqueteSoporteSop
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_PAQUETE_SOPORTE_SOP", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PAQUETE_SOPORTE_SOP", allocationSize=1, initialValue=1)
     */

    private $id;

    /**
     * @var string $paqueteSoporteCabId
     *
     * @ORM\Column(name="PAQUETE_SOPORTE_CAB_ID", type="string", nullable=false)
     */

    private $paqueteSoporteCabId;

    /**
     * @var integer $paqueteSoporteServId
     *
     * @ORM\Column(name="PAQUETE_SOPORTE_SERV_ID", type="integer", nullable=false)
     */

    private $paqueteSoporteServId;

    /**
     * @var integer $paqueteSoporteRecId
     *
     * @ORM\Column(name="PAQUETE_SOPORTE_REC_ID", type="integer", nullable=false)
     */

    private $paqueteSoporteRecId;

    /**
     * @var integer $tareaNumero
     *
     * @ORM\Column(name="TAREA_NUMERO", type="integer", nullable=false)
     */

    private $tareaNumero;

    /**
     * @var string $motivoSoporte
     *
     * @ORM\Column(name="MOTIVO_SOPORTE", type="string", nullable=true)
     */

    private $motivoSoporte;

    /**
     * @var string $solucion
     *
     * @ORM\Column(name="SOLUCION", type="string", nullable=true)
     */

    private $solucion;

    /**
     * @var string $observacion
     *
     * @ORM\Column(name="OBSERVACION", type="string", nullable=true)
     */

    private $observacion;

    /**
     *
     * @var \Date $feInicioGestion
     *      @ORM\Column(name="FECHA_INICIO_GESTION", type="date", nullable=false)
     */
    private $feInicioGestion;

    /**
     *
     * @var \Date $feFinGestion
     *      @ORM\Column(name="FECHA_FIN_GESTION", type="date", nullable=false)
     */
    private $feFinGestion;

    /**
     * @var integer $minutosSoporte
     *
     * @ORM\Column(name="MINUTOS_SOPORTE", type="integer", nullable=false)
     */

    private $minutosSoporte;

    /**
     * @var string $clienteSoporte
     *
     * @ORM\Column(name="CLIENTE_SOPORTE", type="string", nullable=false)
     */

    private $clienteSoporte;
 
    /**
     *
     * @var string $tecnicoSoporte
     *      @ORM\Column(name="TECNICO_SOPORTE", type="string", nullable=false)
     */
    private $tecnicoSoporte;
    
    /**
     *
     * @var string $estado
     *      @ORM\Column(name="ESTADO", type="string", nullable=false)
     */
    private $estado;
    
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
     * Get paqueteSoporteCabId
     *
     * @return integer
     */

    public function getPaqueteSoporteCabId()
    {
        return $this->paqueteSoporteCabId;
    }

    /**
     * Set paqueteSoporteCabId
     *
     * @param integer $intPaqueteSoporteCabId
     */
    public function setPaqueteSoporteCabId($intPaqueteSoporteCabId)
    {
        $this->paqueteSoporteCabId = $intPaqueteSoporteCabId;
    }
   
    /**
     * Get paqueteSoporteServId
     *
     * @return integer
     */

    public function getPaqueteSoporteServId()
    {
        return $this->paqueteSoporteServId;
    }

    /**
     * Set paqueteSoporteServId
     *
     * @param integer $intPaqueteSoporteServId
     */
    public function setPaqueteSoporteServId($intPaqueteSoporteServId)
    {
        $this->paqueteSoporteServId = $intPaqueteSoporteServId;
    }
   
    /**
     * Get paqueteSoporteRecId
     *
     * @return integer
     */

    public function getPaqueteSoporteRecId()
    {
        return $this->paqueteSoporteRecId;
    }

    /**
     * Set paqueteSoporteRecId
     *
     * @param integer $intPaqueteSoporteRecId
     */
    public function setPaqueteSoporteRecId($intPaqueteSoporteRecId)
    {
        $this->paqueteSoporteRecId = $intPaqueteSoporteRecId;
    }
   
    /**
     * Get tareaNumero
     *
     * @return integer
     */

    public function getTareaNumero()
    {
        return $this->tareaNumero;
    }

    /**
     * Set tareaNumero
     *
     * @param integer $intTareaNumero
     */
    public function setTareaNumero($intTareaNumero)
    {
        $this->tareaNumero = $intTareaNumero;
    }
   
    /**
     * Get motivoSoporte
     *
     * @return string
     */

    public function getMotivoSoporte()
    {
        return $this->motivoSoporte;
    }

    /**
     * Set motivoSoporte
     *
     * @param string $intMotivoSoporte
     */
    public function setMotivoSoporte($intMotivoSoporte)
    {
        $this->motivoSoporte = $intMotivoSoporte;
    }
   
    /**
     * Get solucion
     *
     * @return string
     */

    public function getSolucion()
    {
        return $this->solucion;
    }

    /**
     * Set solucion
     *
     * @param string $intSolucion
     */
    public function setSolucion($intSolucion)
    {
        $this->solucion = $intSolucion;
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
     * Get feInicioGestion
     * 
     * @param \Date
     */
    public function getFeInicioGestion()
    {
        return $this->feInicioGestion;
    }

    /**
     * Set feInicioGestion
     * 
     * @param \Date $feInicioGestion            
     */
    public function setFeInicioGestion($dateFeInicioGestion)
    {
        $this->feInicioGestion = $dateFeInicioGestion;
    }

    /**
     * Get feFinGestion
     * 
     * @return \Date
     */
    public function getFeFinGestion()
    {
        return $this->feFinGestion;
    }

    /**
     * Set feFinGestion
     * 
     * @param \Date $feFinGestion            
     */
    public function setFeFinGestion($dateFeFinGestion)
    {
        $this->feFinGestion = $dateFeFinGestion;
    }
   
    /**
     * Get minutosSoporte
     *
     * @return integer
     */

    public function getMinutosSoporte()
    {
        return $this->minutosSoporte;
    }

    /**
     * Set minutosSoporte
     *
     * @param integer $intMinutosSoporte
     */
    public function setMinutosSoporte($intMinutosSoporte)
    {
        $this->minutosSoporte = $intMinutosSoporte;
    }
   
    /**
     * Get clienteSoporte
     *
     * @return string
     */

    public function getClienteSoporte()
    {
        return $this->clienteSoporte;
    }

    /**
     * Set clienteSoporte
     *
     * @param string $intClienteSoporte
     */
    public function setClienteSoporte($intClienteSoporte)
    {
        $this->clienteSoporte = $intClienteSoporte;
    }
   
    /**
     * Get tecnicoSoporte
     *
     * @return string
     */

    public function getTecnicoSoporte()
    {
        return $this->tecnicoSoporte;
    }

    /**
     * Set tecnicoSoporte
     *
     * @param string $intTecnicoSoporte
     */
    public function setTecnicoSoporte($intTecnicoSoporte)
    {
        $this->tecnicoSoporte = $intTecnicoSoporte;
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
    public function setEstado($strEstado)
    {
        $this->estado = $strEstado;
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
