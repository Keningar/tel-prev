<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoPlanCondicion
 * @ORM\Table(name="INFO_PLAN_CONDICION")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoPlanCondicionRepository")
 */
class InfoPlanCondicion {
    /**
     *
     * @var integer $id
     *      @ORM\Column(name="ID_PLAN_CONDICION", type="integer", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="SEQUENCE")
     *      @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PLAN_CONDICION", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     *
     * @var string $empresaCod
     *      @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
     */
    private $empresaCod;
    
    /**
     *
     * @var integer $planId
     *      @ORM\Column(name="PLAN_ID", type="integer", nullable=false)
     */
    private $planId;
    
    /**
     *
     * @var integer $formaPagoId
     *      @ORM\Column(name="FORMA_PAGO_ID", type="integer", nullable=true)
     */
    private $formaPagoId;
    
    /**
     *
     * @var integer $tipoCuentaId
     *      @ORM\Column(name="TIPO_CUENTA_ID", type="integer", nullable=true)
     */
    private $tipoCuentaId;
    
    /**
     *
     * @var integer $bancoTipoCuentaId
     *      @ORM\Column(name="BANCO_TIPO_CUENTA_ID", type="integer", nullable=true)
     */
    private $bancoTipoCuentaId;
    
    /**
     *
     * @var integer $tipoNegocioId
     *      @ORM\Column(name="TIPO_NEGOCIO_ID", type="integer", nullable=true)
     */
    private $tipoNegocioId;
    
    /**
     *
     * @var datetime $feCreacion
     *      @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
     */
    private $feCreacion;
    
    /**
     *
     * @var datetime $feUltMod
     *      @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
     */
    private $feUltMod;
    
    /**
     *
     * @var string $usrCreacion
     *      @ORM\Column(name="USR_CREACION", type="string", nullable=false)
     */
    private $usrCreacion;
    
    /**
     *
     * @var string $usrUltMod
     *      @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
     */
    private $usrUltMod;
    
    /**
     *
     * @var string $estado
     *      @ORM\Column(name="ESTADO", type="string", nullable=false)
     */
    private $estado;
    
    /**
     *
     * @var string $ipCreacion
     *      @ORM\Column(name="IP_CREACION", type="string", nullable=false)
     */
    private $ipCreacion;
    
    /**
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     *
     * @param integer $id            
     */
    public function setId($id) {
        $this->id = $id;
    }
    
    /**
     *
     * @return string
     */
    public function getEmpresaCod() {
        return $this->empresaCod;
    }
    
    /**
     *
     * @param string $empresaCod
     */
    public function setEmpresaCod($empresaCod) {
        $this->empresaCod = $empresaCod;
        return $this;
    }
	
    /**
     *
     * @return integer
     */
    public function getPlanId() {
        return $this->planId;
    }
    
    /**
     *
     * @param integer $planId            
     */
    public function setPlanId($planId) {
        $this->planId = $planId;
    }
    
    /**
     *
     * @return integer
     */
    public function getFormaPagoId() {
        return $this->formaPagoId;
    }
    
    /**
     *
     * @param integer $formaPagoId            
     */
    public function setFormaPagoId($formaPagoId) {
        $this->formaPagoId = $formaPagoId;
    }
    
    /**
     *
     * @return integer
     */
    public function getTipoCuentaId() {
        return $this->tipoCuentaId;
    }
    
    /**
     *
     * @param integer $tipoCuentaId            
     */
    public function setTipoCuentaId($tipoCuentaId) {
        $this->tipoCuentaId = $tipoCuentaId;
    }
    
    /**
     *
     * @return integer
     */
    public function getBancoTipoCuentaId() {
        return $this->bancoTipoCuentaId;
    }
    
    /**
     *
     * @param integer $bancoTipoCuentaId            
     */
    public function setBancoTipoCuentaId($bancoTipoCuentaId) {
        $this->bancoTipoCuentaId = $bancoTipoCuentaId;
    }
    
    /**
     *
     * @return integer
     */
    public function getTipoNegocioId() {
        return $this->tipoNegocioId;
    }
    
    /**
     *
     * @param integer $tipoNegocioId            
     */
    public function setTipoNegocioId($tipoNegocioId) {
        $this->tipoNegocioId = $tipoNegocioId;
    }
    
    /**
     *
     * @return \DateTime
     */
    public function getFeCreacion() {
        return $this->feCreacion;
    }
    
    /**
     *
     * @param \DateTime $feCreacion            
     */
    public function setFeCreacion(\DateTime $feCreacion) {
        $this->feCreacion = $feCreacion;
    }
    
    /**
     *
     * @return \DateTime
     */
    public function getFeUltMod() {
        return $this->feUltMod;
    }
    
    /**
     *
     * @param \DateTime $feUltMod            
     */
    public function setFeUltMod(\DateTime $feUltMod) {
        $this->feUltMod = $feUltMod;
    }
    
    /**
     *
     * @return string
     */
    public function getUsrCreacion() {
        return $this->usrCreacion;
    }
    
    /**
     *
     * @param string $usrCreacion            
     */
    public function setUsrCreacion($usrCreacion) {
        $this->usrCreacion = $usrCreacion;
    }
    
    /**
     *
     * @return string
     */
    public function getUsrUltMod() {
        return $this->usrUltMod;
    }
    
    /**
     *
     * @param string $usrUltMod            
     */
    public function setUsrUltMod($usrUltMod) {
        $this->usrUltMod = $usrUltMod;
    }
    
    /**
     *
     * @return string
     */
    public function getEstado() {
        return $this->estado;
    }
    
    /**
     *
     * @param string $estado            
     */
    public function setEstado($estado) {
        $this->estado = $estado;
    }
    
    /**
     *
     * @return string
     */
    public function getIpCreacion() {
        return $this->ipCreacion;
    }
    
    /**
     *
     * @param string $ipCreacion            
     */
    public function setIpCreacion($ipCreacion) {
        $this->ipCreacion = $ipCreacion;
    }
}
