<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoParteAfectadaHist
 *
 * @ORM\Table(name="INFO_PARTE_AFECTADA_HIST")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoParteAfectadaHistRepository")
 */
class InfoParteAfectadaHist
{

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
    * @var string $tipoAfectado
    *
    * @ORM\Column(name="TIPO_AFECTADO", type="string", nullable=false)
    */		
                
    private $tipoAfectado;

    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_PARTE_AFECTADA_HIST", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PARTE_AFECTADA_HIST", allocationSize=1, initialValue=1)
    */		
            
    private $id;	
        
    /**
    * @var integer $criterioAfectadoId
    *
    * @ORM\Column(name="CRITERIO_AFECTADO_ID", type="integer", nullable=false)
    */
            
    private $criterioAfectadoId;

    /**
    * @var integer $detalleId
    *
    * @ORM\Column(name="DETALLE_ID", type="integer", nullable=false)
    */
            
    private $detalleId;

    /**
    * @var integer $afectadoId
    *
    * @ORM\Column(name="AFECTADO_ID", type="integer", nullable=false)
    */		
                
    private $afectadoId;

    /**
    * @var string $afectadoNombre
    *
    * @ORM\Column(name="AFECTADO_NOMBRE", type="string", nullable=false)
    */		
                
    private $afectadoNombre;

    /**
    * @var integer $afectadoDescripcionId
    *
    * @ORM\Column(name="AFECTADO_DESCRIPCION_ID", type="integer", nullable=true)
    */		
                
    private $afectadoDescripcionId;

    /**
    * @var string $afectadoDescripcion
    *
    * @ORM\Column(name="AFECTADO_DESCRIPCION", type="string", nullable=true)
    */		
                
    private $afectadoDescripcion;

    /**
    * @var datetime $feIniIncidencia
    *
    * @ORM\Column(name="FE_INI_INCIDENCIA", type="datetime", nullable=false)
    */		
                
    private $feIniIncidencia;

    /**
    * @var datetime $feFinIncidencia
    *
    * @ORM\Column(name="FE_FIN_INCIDENCIA", type="datetime", nullable=false)
    */		
                
    private $feFinIncidencia;

    /**
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=false)
    */		
                
    private $estado;

    /**
    * @var string $usrUltMod
    *
    * @ORM\Column(name="USR_ULT_MOD", type="string", nullable=false)
    */		
                
    private $usrUltMod;

    /**
    * @var datetime $feUltMod
    *
    * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
    */		
                
    private $feUltMod;

    /**
     * @var string $ipUltMod
     *
     * @ORM\Column(name="IP_ULT_MOD", type="string", nullable=false)
     */
    private $ipUltMod;

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
    * Get tipoAfectado
    *
    * @return string
    */		
                
    public function getTipoAfectado(){
        return $this->tipoAfectado; 
    }

    /**
    * Set tipoAfectado
    *
    * @param string $tipoAfectado
    */
    public function setTipoAfectado($tipoAfectado)
    {
            $this->tipoAfectado = $tipoAfectado;
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
    * Get id
    *
    * @return integer
    */		
                
    public function getId(){
        return $this->id; 
    }

    /**
    * Get criterioAfectadoId
    *
    * @return integer
    */		
                
    public function getCriterioAfectadoId(){
        return $this->criterioAfectadoId; 
    }

    /**
    * Set criterioAfectadoId
    *
    * @param integer $criterioAfectadoId
    */
    public function setCriterioAfectadoId($criterioAfectadoId)
    {
            $this->criterioAfectadoId = $criterioAfectadoId;
    }
    /**
    * Get detalleId
    *
    * @return integer
    */		
                
    public function getDetalleId(){
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
    * Get afectadoId
    *
    * @return integer
    */		
                
    public function getAfectadoId(){
        return $this->afectadoId; 
    }

    /**
    * Set afectadoId
    *
    * @param integer $afectadoId
    */
    public function setAfectadoId($afectadoId)
    {
            $this->afectadoId = $afectadoId;
    }


    /**
    * Get afectadoNombre
    *
    * @return string
    */		
                
    public function getAfectadoNombre(){
        return $this->afectadoNombre; 
    }

    /**
    * Set afectadoNombre
    *
    * @param string $afectadoNombre
    */
    public function setAfectadoNombre($afectadoNombre)
    {
            $this->afectadoNombre = $afectadoNombre;
    }


    /**
    * Get afectadoDescripcionId
    *
    * @return integer
    */		
                
    public function getAfectadoDescripcionId(){
        return $this->afectadoDescripcionId; 
    }

    /**
    * Set afectadoDescripcionId
    *
    * @param integer $afectadoDescripcionId
    */
    public function setAfectadoDescripcionId($afectadoDescripcionId)
    {
            $this->afectadoDescripcionId = $afectadoDescripcionId;
    }


    /**
    * Get afectadoDescripcion
    *
    * @return string
    */		
                
    public function getAfectadoDescripcion(){
        return $this->afectadoDescripcion; 
    }

    /**
    * Set afectadoDescripcion
    *
    * @param string $afectadoDescripcion
    */
    public function setAfectadoDescripcion($afectadoDescripcion)
    {
            $this->afectadoDescripcion = $afectadoDescripcion;
    }


    /**
    * Get feIniIncidencia
    *
    * @return datetime
    */		
                
    public function getFeIniIncidencia(){
        return $this->feIniIncidencia; 
    }

    /**
    * Set feIniIncidencia
    *
    * @param datetime $feIniIncidencia
    */
    public function setFeIniIncidencia($feIniIncidencia)
    {
            $this->feIniIncidencia = $feIniIncidencia;
    }


    /**
    * Get feFinIncidencia
    *
    * @return datetime
    */		
                
    public function getFeFinIncidencia(){
        return $this->feFinIncidencia; 
    }

    /**
    * Set feFinIncidencia
    *
    * @param datetime $feFinIncidencia
    */
    public function setFeFinIncidencia($feFinIncidencia)
    {
            $this->feFinIncidencia = $feFinIncidencia;
    }

    /**
     * Get usrUltMod
     *
     * @return string
     */
    public function getUsrUltMod()
    {
        return $this->usrUltMod;
    }

    /**
     * Set usrUltMod
     *
     * @param string $usrUltMod
     */
    public function setUsrUltMod($usrUltMod)
    {
        $this->usrUltMod = $usrUltMod;
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
     * Get ipUltMod
     *
     * @return string
     */
    public function getIpUltMod()
    {
        return $this->ipUltMod;
    }

    /**
     * Set ipUltMod
     *
     * @param string $ipUltMod
     */
    public function setIpUltMod($ipUltMod)
    {
        $this->ipUltMod = $ipUltMod;
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
