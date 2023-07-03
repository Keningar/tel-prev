<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\class InfoSeguimientoServicio
 *
 * @ORM\Table(name="INFO_SEGUIMIENTO_SERVICIO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoSeguimientoServRepository")
 */
class InfoSeguimientoServicio
{
   /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_SEGUIMIENTO_SERVICIO", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_SEGUIMIENTO_SERVICIO", allocationSize=1, initialValue=1)
    */	
    
    private $id;
    
    /**
    * @var InfoServicio
    *
    * @ORM\ManyToOne(targetEntity="InfoServicio")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="SERVICIO_ID", referencedColumnName="ID_SERVICIO")
    * })
    */		

    private $servicioId;
    
    /**
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=true)
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
    * @var datetime $feModificacion
    *
    * @ORM\Column(name="FE_MODIFICACION", type="datetime", nullable=false)
    */		

    private $feModificacion;
    
    /**
    * @var string $ipCreacion
    *
    * @ORM\Column(name="IP_CREACION", type="string", nullable=true)
    */		

    private $ipCreacion;
    
    /**
    * @var string $departamento
    *
    * @ORM\Column(name="DEPARTAMENTO", type="string", nullable=false)
    */		

    private $departamento;
    
    /**
    * @var integer $tiempoEstimado
    *
    * @ORM\Column(name="TIEMPO_ESTIMADO", type="integer", nullable=true)
    */		

    private $tiempoEstimado;
    
    /**
    * @var integer $tiempoTranscurrido
    *
    * @ORM\Column(name="TIEMPO_TRANSCURRIDO", type="integer", nullable=true)
    */		

    private $tiempoTranscurrido;
    
    /**
    * @var integer $diasTranscurrido
    *
    * @ORM\Column(name="DIAS_TRANSCURRIDO", type="integer", nullable=true)
    */		

    private $diasTranscurrido;
    
    /**
    * @var string $observacion
    *
    * @ORM\Column(name="OBSERVACION", type="string", nullable=false)
    */		

    private $observacion;
    
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
    * Get servicioId
    *
    * @return telconet\schemaBundle\Entity\InfoServicio
    */		

    public function getServicioId(){
            return $this->servicioId; 
    }

    /**
    * Set servicioId
    *
    * @param telconet\schemaBundle\Entity\InfoServicio $servicioId
    */
    public function setServicioId(\telconet\schemaBundle\Entity\InfoServicio $servicioId)
    {
            $this->servicioId = $servicioId;
    }
    
    /**
     * Set estado
     *
     * @param string $estado
     * @return InfoSeguimientoServicio
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    
        return $this;
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
     * Set usrCreacion
     *
     * @param string $usrCreacion
     * @return InfoSeguimientoServicio
     */
    public function setUsrCreacion($usrCreacion)
    {
        $this->usrCreacion = $usrCreacion;
    
        return $this;
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
     * Set feCreacion
     *
     * @param \DateTime $feCreacion
     * @return InfoSeguimientoServicio
     */
    public function setFeCreacion($feCreacion)
    {
        $this->feCreacion = $feCreacion;
    
        return $this;
    }

    /**
     * Get feCreacion
     *
     * @return \DateTime 
     */
    public function getFeCreacion()
    {
        return $this->feCreacion;
    }
    
    /**
     * Set feCreacion
     *
     * @param \DateTime $feModificacion
     * @return InfoSeguimientoServicio
     */
    public function setFeModificacion($feModificacion)
    {
        $this->feModificacion = $feModificacion;
    
        return $this;
    }

    /**
     * Get feModificacion
     *
     * @return \DateTime 
     */
    public function getFeModificacion()
    {
        return $this->feModificacion;
    }
    
    /**
     * Set ipCreacion
     *
     * @param string $ipCreacion
     * @return InfoSeguimientoServicio
     */
    public function setIpCreacion($ipCreacion)
    {
        $this->ipCreacion = $ipCreacion;
    
        return $this;
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
     * Set departamento
     *
     * @param string $departamento
     * @return InfoSeguimientoServicio
     */
    public function setDepartamento($departamento)
    {
        $this->departamento = $departamento;
    
        return $this;
    }

    /**
     * Get departamento
     *
     * @return string 
     */
    public function getDepartamento()
    {
        return $this->departamento;
    }
    
    /**
    * Get tiempoEstimado
    *
    * @return integer
    */		

    public function getTiempoEstimado(){
        return $this->tiempoEstimado; 
    }

    /**
    * Set tiempoEstimado
    *
    * @param integer $tiempoEstimado
    */
    public function setTiempoEstimado($tiempoEstimado)
    {
            $this->tiempoEstimado = $tiempoEstimado;
    }
    
    /**
    * Get tiempoTranscurrido
    *
    * @return integer
    */		

    public function getTiempoTranscurrido(){
        return $this->tiempoTranscurrido; 
    }

    /**
    * Set tiempoTranscurrido
    *
    * @param integer $tiempoTranscurrido
    */
    public function setTiempoTranscurrido($tiempoTranscurrido)
    {
            $this->tiempoTranscurrido = $tiempoTranscurrido;
    }
    
    /**
    * Get diasTranscurrido
    *
    * @return float
    */		

    public function getDiasTranscurrido(){
        return $this->diasTranscurrido; 
    }

    /**
    * Set diasTranscurrido
    *
    * @param float $diasTranscurrido
    */
    public function setDiasTranscurrido($diasTranscurrido)
    {
            $this->diasTranscurrido = $diasTranscurrido;
    }
    
    /**
     * Set observacion
     *
     * @param string $observacion
     * @return InfoSeguimientoServicio
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    
        return $this;
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
    
    public function __clone() {
    $this->id = null;
}
}
