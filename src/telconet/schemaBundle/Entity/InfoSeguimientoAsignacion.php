<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoSeguimientoAsignacion
 *
 * @ORM\Table(name="INFO_SEGUIMIENTO_ASIGNACION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoSeguimientoAsignacionRepository")
 */
class InfoSeguimientoAsignacion
{

    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_SEGUIMIENTO_ASIGNACION", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_SEGUIMIENTO_ASIGN_SOL", allocationSize=1, initialValue=1)
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
    * @var string $detalle
    *
    * @ORM\Column(name="DETALLE", type="string", nullable=false)
    */		

    private $detalle;

    /**
    * @var string $procedencia
    *
    * @ORM\Column(name="PROCEDENCIA", type="string", nullable=false)
    */		

    private $procedencia;



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
    * @var string $usrGestion
    *
    * @ORM\Column(name="USR_GESTION", type="string", nullable=false)
    */		

    private $usrGestion;

    /**
    * @var datetime $feGestion
    *
    * @ORM\Column(name="FE_GESTION", type="datetime", nullable=false)
    */		

    private $feGestion;

    /**
    * @var string
    *
    * @ORM\Column(name="GESTIONADO", type="string", nullable=true)
    */

    
    private $gestionado;

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
    * @var string $ipCreacion
    *
    * @ORM\Column(name="IP_CREACION", type="string", nullable=false)
    */		

    private $ipCreacion;

    /**
    * @var integer $seguimientoAsignacionId
    *
    * @ORM\Column(name="SEGUIMIENTO_ASIGNACION_ID", type="integer", nullable=false)
    */		

    private $seguimientoAsignacionId;

    /**
    * @var integer $comunicacionId
    *
    * @ORM\Column(name="COMUNICACION_ID", type="integer", nullable=true)
    */		

    private $comunicacionId;

    /**
    * @var string
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=true)
    */

    private $estado;



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
    * Get procedencia
    *
    * @return string
    */		

    public function getProcedencia(){
            return $this->procedencia; 
    }

    /**
    * Set procedencia
    *
    * @param string $procedencia
    */
    public function setProcedencia($procedencia)
    {
            $this->procedencia = $procedencia;
    }


    /**
    * Get detalle
    *
    * @return string
    */		

    public function getDetalle(){
            return $this->detalle; 
    }

    /**
    * Set detalle
    *
    * @param string $detalle
    */
    public function setDetalle($detalle)
    {
            $this->detalle = $detalle;
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
    * Get usrGestion
    *
    * @return string
    */
    function getUsrGestion() {
        return $this->usrGestion;
    }
    /**
    * Set usrGestion
    *
    * @param string $usrGestion
    */
    function setUsrGestion($usrGestion) {
        $this->usrGestion = $usrGestion;
    }
    /**
    * Get feCreacion
    *
    * @return datetime
    */
    function getFeGestion() {
        return $this->feGestion;
    }
    /**
    * Set feGestion
    *
    * @param datetime $feGestion
    */
    function setFeGestion($feGestion) {
        $this->feGestion = $feGestion;
    }
    /**
    * Get gestionado
    *
    * @return string
    */
    function getGestionado() {
        return $this->gestionado;
    }
    /**
    * Set gestionado
    *
    * @param string $gestionado
    */
    function setGestionado($gestionado) {
        $this->gestionado = $gestionado;
    }

    /**
    * Get usrUltMod
    *
    * @return string
    */		

    public function getUsrUltMod(){
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

    public function getFeUltMod(){
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
    * Get seguimientoAsignacionId
    *
    * @return integer
    */		

    public function getSeguimientoAsignacionId(){
            return $this->seguimientoAsignacionId; 
    }

    /**
    * Set seguimientoAsignacionId
    *
    * @param integer $seguimientoAsignacionId
    */
    public function setSeguimientoAsignacionId($seguimientoAsignacionId)
    {
            $this->seguimientoAsignacionId = $seguimientoAsignacionId;
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
    * @param integer $comunicacionId
    */
    public function setComunicacionId($comunicacionId)
    {
        $this->comunicacionId = $comunicacionId;
    }

}