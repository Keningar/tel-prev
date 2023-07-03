<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoProcesoMasivoDet
 *
 * @ORM\Table(name="INFO_PROCESO_MASIVO_DET")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoProcesoMasivoDetRepository")
 */
class InfoProcesoMasivoDet {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_PROCESO_MASIVO_DET", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PROCESO_MASIVO_DET", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var InfoProcesoMasivoCab
     *
     * @ORM\ManyToOne(targetEntity="InfoProcesoMasivoCab")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="PROCESO_MASIVO_CAB_ID", referencedColumnName="ID_PROCESO_MASIVO_CAB")
     * })
     */
    private $procesoMasivoCabId;

    /**
     * @var InfoPunto
     *
     * @ORM\Column(name="PUNTO_ID", type="string", nullable=false)
     */
    private $puntoId;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=false)
     */
    private $estado;
    
   /**
     * @var string $observacion
     *
     * @ORM\Column(name="OBSERVACION", type="string", nullable=true)
     */
    private $observacion;

    /**
     * @var datetime $feCreacion
     *
     * @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
     */
    private $feCreacion;

    /**
     * @var datetime $feUltMod
     *
     * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
     */
    private $feUltMod;

    /**
     * @var string $usrCreacion
     *
     * @ORM\Column(name="USR_CREACION", type="string", nullable=true)
     */
    private $usrCreacion;

    /**
     * @var string $usrUltMod
     *
     * @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
     */
    private $usrUltMod;

    /**
     * @var string $ipCreacion
     *
     * @ORM\Column(name="IP_CREACION", type="string", nullable=true)
     */
    private $ipCreacion;

    /**
     * @var servicioId
     *
     * @ORM\Column(name="SERVICIO_ID", type="integer", nullable=false)
     */
    private $servicioId;
    
    /**
     * @var solicitudId
     *
     * @ORM\Column(name="SOLICITUD_ID", type="integer", nullable=true)
     */
    private $solicitudId;
    
     /**
     * @var login
     *
     * @ORM\Column(name="LOGIN", type="string", nullable=true)
     */
    private $login;

    /**
     * @var serieFisica
     *
     * @ORM\Column(name="SERIE_FISICA", type="string", nullable=true)
     */
    private $serieFisica;


    /**
    * @var integer $personaEmpresaRolId
    *
    * @ORM\Column(name="PERSONA_EMPRESA_ROL_ID", type="integer", nullable=true)
    */

    private $personaEmpresaRolId;

    /**
    * @var integer $pagoId
    *
    * @ORM\Column(name="PAGO_ID", type="integer", nullable=true)
    */

    private $pagoId;

    //...
    // Getters And Setters
    //..

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get procesoMasivoCabId
     *
     * @return telconet\schemaBundle\Entity\InfoProcesoMasivoCab
     */
    public function getProcesoMasivoCabId() {
        return $this->procesoMasivoCabId;
    }

    /**
     * Set procesoMasivoCabId
     *
     * @param telconet\schemaBundle\Entity\InfoProcesoMasivoCab $procesoMasivoCabId
     */
    public function setProcesoMasivoCabId(\telconet\schemaBundle\Entity\InfoProcesoMasivoCab $procesoMasivoCabId) {
        $this->procesoMasivoCabId = $procesoMasivoCabId;
    }

    /**
     * Get puntoId
     *
     * @return integer
     */
    public function getPuntoId() {
        return $this->puntoId;
    }

    /**
     * Set puntoId
     *
     * @param integer $puntoId
     */
    public function setPuntoId($puntoId) {
        $this->puntoId = $puntoId;
    }   
    
    /**
     * Get estado
     *
     * @return string
     */
    public function getEstado() {
        return $this->estado;
    }

    
    /**
     * Get observacion
     *
     * @return string
     */
    public function getObservacion() {
        return $this->observacion;
    }
    
    /**
     * Set estado
     *
     * @param string $estado
     */
    public function setEstado($estado) {
        $this->estado = $estado;
    }
    
    /**
     * Set observacion
     *
     * @param string $observacion
     */
    public function setObservacion($observacion) {
        $this->observacion = $observacion;
    }

    /**
     * Get feCreacion
     *
     * @return datetime
     */
    public function getFeCreacion() {
        return $this->feCreacion;
    }

    /**
     * Set feCreacion
     *
     * @param datetime $feCreacion
     */
    public function setFeCreacion($feCreacion) {
        $this->feCreacion = $feCreacion;
        return $this;
    }

    /**
     * Get feUltMod
     *
     * @return datetime
     */
    public function getFeUltMod() {
        return $this->feUltMod;
    }

    /**
     * Set feUltMod
     *
     * @param datetime $feUltMod
     */
    public function setFeUltMod($feUltMod) {
        $this->feUltMod = $feUltMod;
        return $this;
    }

    /**
     * Get usrCreacion
     *
     * @return string
     */
    public function getUsrCreacion() {
        return $this->usrCreacion;
    }

    /**
     * Set usrCreacion
     *
     * @param string $usrCreacion
     */
    public function setUsrCreacion($usrCreacion) {
        $this->usrCreacion = $usrCreacion;
    }

    /**
     * Get usrUltMod
     *
     * @return string
     */
    public function getUsrUltMod() {
        return $this->usrUltMod;
    }

    /**
     * Set usrUltMod
     *
     * @param string $usrUltMod
     */
    public function setUsrUltMod($usrUltMod) {
        $this->usrUltMod = $usrUltMod;
    }

    /**
     * Get ipCreacion
     *
     * @return string
     */
    public function getIpCreacion() {
        return $this->ipCreacion;
    }

    /**
     * Set ipCreacion
     *
     * @param string $ipCreacion
     */
    public function setIpCreacion($ipCreacion) {
        $this->ipCreacion = $ipCreacion;
    }
    
    /**
     * Get servicioId
     *
     * @return integer
     */
    public function getServicioId() {
        return $this->servicioId;
    }
    
    /**
     * Set servicioId
     *
     * @param integer $servicioId
     */
    public function setServicioId($servicioId) {
        $this->servicioId = $servicioId;
    }

    /**
     * Get solicitudId
     *
     * @return integer
     */
    public function getSolicitudId() 
    {
        return $this->solicitudId;
    }
    
    /**
     * Set solicitudId
     *
     * @param integer $solicitudId
     */
    public function setSolicitudId($solicitudId) 
    {
        $this->solicitudId = $solicitudId;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin() {
        return $this->login;
    }

    /**
     * Set login
     *
     * @param string $login
     */
    public function setLogin($login) {
        $this->login = $login;
    }

    /**
     * Get serieFisica
     *
     * @return string
     */
    public function getSerieFisica() {
        return $this->serieFisica;
    }

    /**
     * Set serieFisica
     *
     * @param string $serieFisica
     */
    public function setSerieFisica($serieFisica) {
        $this->serieFisica = $serieFisica;
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
    * @param integer $personaEmpresaRolId
    */
    public function setPersonaEmpresaRolId($personaEmpresaRolId)
    {
        $this->personaEmpresaRolId = $personaEmpresaRolId;
    }


    /**
    * Get pagoId
    *
    * @return integer
    */		

    public function getPagoId()
    {
        return $this->pagoId; 
    }

    /**
    * Set pagoId
    *
    * @param integer $pagoId
    */
    public function setPagoId($pagoId)
    {
        $this->pagoId = $pagoId;
    }

}
