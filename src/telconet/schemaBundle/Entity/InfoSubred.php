<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoSubred
 *
 * @ORM\Table(name="INFO_SUBRED")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoSubredRepository")
 */
class InfoSubred {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_SUBRED", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_SUBRED", allocationSize=1, initialValue=1)
     */
    private $id;

     /**
     * @var integer $redId
     *
     * @ORM\Column(name="RED_ID", type="integer", nullable=true)
     */
    private $redId;

    /**
     * @var string $subred
     *
     * @ORM\Column(name="SUBRED", type="string", nullable=true)
     */
    private $subred;

    /**
     * @var string $mascara
     *
     * @ORM\Column(name="MASCARA", type="string", nullable=true)
     */
    private $mascara;

    /**
     * @var string $gateway
     *
     * @ORM\Column(name="GATEWAY", type="string", nullable=true)
     */
    private $gateway;

    /**
     * @var string $usrCreacion
     *
     * @ORM\Column(name="USR_CREACION", type="string", nullable=true)
     */
    private $usrCreacion;

    /**
     * @var string $feCreacion
     *
     * @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
     */
    private $feCreacion;

    /**
     * @var string $ipCreacion
     *
     * @ORM\Column(name="IP_CREACION", type="string", nullable=true)
     */
    private $ipCreacion;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=true)
     */
    private $estado;

    /**
     * @var InfoElemento
     *
     * @ORM\ManyToOne(targetEntity="InfoElemento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ELEMENTO_ID", referencedColumnName="ID_ELEMENTO")
     * })
     */
    private $elementoId;

    /**
     * @var string $ipCreacion
     *
     * @ORM\Column(name="IP_INICIAL", type="string", nullable=true)
     */
    private $ipInicial;

    /**
     * @var string $ipCreacion
     *
     * @ORM\Column(name="IP_FINAL", type="string", nullable=true)
     */
    private $ipFinal;
    
     /**
     * @var string $ipDisponible
     *
     * @ORM\Column(name="IP_DISPONIBLE", type="string", nullable=true)
     */
    private $ipDisponible;

    /**
     * @var integer $notificacion
     *
     * @ORM\Column(name="NOTIFICACION", type="integer", nullable=true)
     */
    private $notificacion;
    
    /**
     * @var string $tipo
     *
     * @ORM\Column(name="TIPO", type="string", nullable=true)
     */		
    private $tipo;

    /**
     * @var string $uso
     *
     * @ORM\Column(name="USO", type="string", nullable=true)
     */		
    private $uso;

    /**
     * 
     * @var InfoSubred
     *
     * @ORM\ManyToOne(targetEntity="InfoSubred")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="SUBRED_ID", referencedColumnName="ID_SUBRED")
     * })
     */		
    private $subredId;
    
    /**
     * @var string $versionIp
     *
     * @ORM\Column(name="VERSION_IP", type="string", nullable=true)
     */		
    private $versionIp;

    /**
     * @var integer $cantonId
     *
     * @ORM\Column(name="CANTON_ID", type="integer", nullable=true)
     */		
    private $cantonId;
    
    /**
     * @var integer $prefijoId
     *
     * @ORM\Column(name="PREFIJO_ID", type="integer", nullable=true)
     */		
    private $prefijoId;

    /**
     * @var string $empresaCod
     *
     * @ORM\Column(name="EMPRESA_COD", type="string", nullable=true)
     */		
    private $empresaCod;
    
    
    /**
     * Get subredId
     *
     * @return telconet\schemaBundle\Entity\InfoSubred
     */	
    public function getSubredId()
    {
        return $this->subredId;
    }

    /**
     * Set subredId
     *
     * @param telconet\schemaBundle\Entity\InfoSubred $subredId
     */
    public function setSubredId(\telconet\schemaBundle\Entity\InfoSubred $subredId) 
    {
        $this->subredId = $subredId;
    }
    
    
    /**
     * Get versionIp
     *
     * @return string
     */		
    public function getVersionIp()
    {
        return $this->versionIp; 
    }

    /**
     * Set versionIp
     *
     * @param string $versionIp
     */
    public function setVersionIp($versionIp)
    {
        $this->versionIp = $versionIp;
    }
    
    
    /**
     * Get cantonId
     *
     * @return integer
     */		
    public function getCantonId()
    {
        return $this->cantonId; 
    }

    /**
     * Set cantonId
     *
     * @param integer $cantonId
     */
    public function setCantonId($cantonId)
    {
        $this->cantonId = $cantonId;
    }
    
    
    /**
     * Get prefijoId
     *
     * @return integer
     */		
    public function getPrefijoId()
    {
        return $this->prefijoId; 
    }

    /**
     * Set prefijoId
     *
     * @param integer $prefijoId
     */
    public function setPrefijoId($prefijoId)
    {
        $this->prefijoId = $prefijoId;
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
     * @param string $empresaCod
     */
    public function setEmpresaCod($empresaCod)
    {
        $this->empresaCod = $empresaCod;
    }
    
    

    public function getId() {
        return $this->id;
    }

    public function getRedId() {
        return $this->redId;
    }

    public function getSubred() {
        return $this->subred;
    }

    public function getMascara() {
        return $this->mascara;
    }

    public function getGateway() {
        return $this->gateway;
    }

    public function getUsrCreacion() {
        return $this->usrCreacion;
    }

    public function getFeCreacion() {
        return $this->feCreacion;
    }

    public function getIpCreacion() {
        return $this->ipCreacion;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function getElementoId() {
        return $this->elementoId;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setRedId($redId) {
        $this->redId = $redId;
    }

    public function setSubred($subred) {
        $this->subred = $subred;
    }

    public function setMascara($mascara) {
        $this->mascara = $mascara;
    }

    public function setGateway($gateway) {
        $this->gateway = $gateway;
    }

    public function setUsrCreacion($usrCreacion) {
        $this->usrCreacion = $usrCreacion;
    }

    public function setFeCreacion($feCreacion) {
        $this->feCreacion = $feCreacion;
    }

    public function setIpCreacion($ipCreacion) {
        $this->ipCreacion = $ipCreacion;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }

    public function setElementoId(InfoElemento $elementoId) {
        $this->elementoId = $elementoId;
    }

    public function getIpInicial() {
        return $this->ipInicial;
    }

    public function getIpFinal() {
        return $this->ipFinal;
    }

    public function setIpInicial($ipInicial) {
        $this->ipInicial = $ipInicial;
    }

    public function setIpFinal($ipFinal) {
        $this->ipFinal = $ipFinal;
    }
    
    public function getNotificacion() {
        return $this->notificacion;
    }

    public function setNotificacion($notificacion) {
        $this->notificacion = $notificacion;
    }
    public function getIpDisponible() {
        return $this->ipDisponible;
    }

    public function setIpDisponible($ipDisponible) {
        $this->ipDisponible = $ipDisponible;
    }
    public function setAnillo($anillo) {
        $this->anillo = $anillo;
    }
    /**
     * Get tipo
     *
     * @return string
     */		
    public function getTipo(){
            return $this->tipo; 
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     */
    public function setTipo($tipo)
    {
            $this->tipo = $tipo;
    }

    /**
     * Get uso
     *
     * @return string
     */		
    public function getUso(){
            return $this->uso; 
    }

    /**
     * Set uso
     *
     * @param string $uso
     */
    public function setUso($uso)
    {
            $this->uso = $uso;
    }
}
