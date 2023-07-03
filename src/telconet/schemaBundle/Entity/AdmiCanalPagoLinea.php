<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiCanalPagoLinea
 *
 * @ORM\Table(name="ADMI_CANAL_PAGO_LINEA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiCanalPagoLineaRepository")
 *
 * @author ltama
 */
class AdmiCanalPagoLinea {
    
    /**
     *
     * @var integer $id
     *      @ORM\Column(name="ID_CANAL_PAGO_LINEA", type="integer", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="SEQUENCE")
     *      @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_CANAL_PAGO_LINEA", allocationSize=1, initialValue=1)
     */
    private $id;
    
    /**
     *
     * @var AdmiFormaPago $formaPago
     *      @ORM\ManyToOne(targetEntity="AdmiFormaPago")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="FORMA_PAGO_ID", referencedColumnName="ID_FORMA_PAGO", nullable=false)
     *      })
     */
    private $formaPago;
    
    /**
     *
     * @var integer $bancoTipoCuentaId
     *      @ORM\Column(name="BANCO_TIPO_CUENTA_ID", type="integer", nullable=true)
     */
    private $bancoTipoCuentaId;
    
    /**
     *
     * @var integer $bancoCtaContableId
     *      @ORM\Column(name="BANCO_CTA_CONTABLE_ID", type="integer", nullable=true)
     */
    private $bancoCtaContableId;
    
    /**
     *
     * @var $codigoCanalPagoLinea @ORM\Column(name="CODIGO_CANAL_PAGO_LINEA", type="string", nullable=false)
     */
    private $codigoCanalPagoLinea;
    
    /**
     *
     * @var $descripcionCanalPagoLinea @ORM\Column(name="DESCRIPCION_CANAL_PAGO_LINEA", type="string", nullable=false)
     */
    private $descripcionCanalPagoLinea;
    
    /**
     *
     * @var $cuenta @ORM\Column(name="ESTADO_CANAL_PAGO_LINEA", type="string", nullable=false)
     */
    private $estadoCanalPagoLinea;
    
    /**
     *
     * @var $nombreCanalPagoLinea @ORM\Column(name="NOMBRE_CANAL_PAGO_LINEA", type="string", nullable=true)
     */
    private $nombreCanalPagoLinea;
    
    /**
     *
     * @var $usuarioCanalPagoLinea @ORM\Column(name="USUARIO_CANAL_PAGO_LINEA", type="string", nullable=true)
     */
    private $usuarioCanalPagoLinea;
    
    /**
     *
     * @var $claveCanalPagoLinea @ORM\Column(name="CLAVE_CANAL_PAGO_LINEA", type="string", nullable=true)
     */
    private $claveCanalPagoLinea;
    
    /**
     *
     * @var $usrCreacion @ORM\Column(name="USR_CREACION", type="string", nullable=false)
     */
    private $usrCreacion;
    
    /**
     *
     * @var $feCreacion @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
     */
    private $feCreacion;
    
    /**
     *
     * @var $usrUltMod @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
     */
    private $usrUltMod;
    
    /**
     *
     * @var $feUltMod @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
     */
    private $feUltMod;
    
    /**
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     *
     * @param
     *            $id
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    /**
     *
     * @return AdmiFormaPago
     */
    public function getFormaPago() {
        return $this->formaPago;
    }
    
    /**
     *
     * @param AdmiFormaPago $formaPago            
     */
    public function setFormaPago(AdmiFormaPago $formaPago) {
        $this->formaPago = $formaPago;
        return $this;
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
     * @param
     *            $bancoTipoCuentaId
     */
    public function setBancoTipoCuentaId($bancoTipoCuentaId) {
        $this->bancoTipoCuentaId = $bancoTipoCuentaId;
        return $this;
    }
    
    /**
     *
     * @return integer
     */
    public function getBancoCtaContableId() {
        return $this->bancoCtaContableId;
    }
    
    /**
     *
     * @param
     *            $bancoCtaContableId
     */
    public function setBancoCtaContableId($bancoCtaContableId) {
        $this->bancoCtaContableId = $bancoCtaContableId;
        return $this;
    }
    
    /**
     *
     * @return $codigoCanalPagoLinea
     */
    public function getCodigoCanalPagoLinea() {
        return $this->codigoCanalPagoLinea;
    }
    
    /**
     *
     * @param
     *            $codigoCanalPagoLinea
     */
    public function setCodigoCanalPagoLinea($codigoCanalPagoLinea) {
        $this->codigoCanalPagoLinea = $codigoCanalPagoLinea;
        return $this;
    }
    
    /**
     *
     * @return $descripcionCanalPagoLinea
     */
    public function getDescripcionCanalPagoLinea() {
        return $this->descripcionCanalPagoLinea;
    }
    
    /**
     *
     * @param
     *            $descripcionCanalPagoLinea
     */
    public function setDescripcionCanalPagoLinea($descripcionCanalPagoLinea) {
        $this->descripcionCanalPagoLinea = $descripcionCanalPagoLinea;
        return $this;
    }
    
    /**
     *
     * @return $cuenta
     */
    public function getEstadoCanalPagoLinea() {
        return $this->estadoCanalPagoLinea;
    }
    
    /**
     *
     * @param
     *            $estadoCanalPagoLinea
     */
    public function setEstadoCanalPagoLinea($estadoCanalPagoLinea) {
        $this->estadoCanalPagoLinea = $estadoCanalPagoLinea;
        return $this;
    }
    
    /**
     *
     * @return $nombreCanalPagoLinea
     */
    public function getNombreCanalPagoLinea() {
        return $this->nombreCanalPagoLinea;
    }
    
    /**
     *
     * @param
     *            $nombreCanalPagoLinea
     */
    public function setNombreCanalPagoLinea($nombreCanalPagoLinea) {
        $this->nombreCanalPagoLinea = $nombreCanalPagoLinea;
        return $this;
    }
    
    /**
     *
     * @return $usuarioCanalPagoLinea
     */
    public function getUsuarioCanalPagoLinea() {
        return $this->usuarioCanalPagoLinea;
    }
    
    /**
     *
     * @param
     *            $usuarioCanalPagoLinea
     */
    public function setUsuarioCanalPagoLinea($usuarioCanalPagoLinea) {
        $this->usuarioCanalPagoLinea = $usuarioCanalPagoLinea;
        return $this;
    }
    
    /**
     *
     * @return $claveCanalPagoLinea
     */
    public function getClaveCanalPagoLinea() {
        return $this->claveCanalPagoLinea;
    }
    
    /**
     *
     * @param
     *            $claveCanalPagoLinea
     */
    public function setClaveCanalPagoLinea($claveCanalPagoLinea) {
        $this->claveCanalPagoLinea = $claveCanalPagoLinea;
        return $this;
    }
    
    /**
     *
     * @return $usrCreacion
     */
    public function getUsrCreacion() {
        return $this->usrCreacion;
    }
    
    /**
     *
     * @param
     *            $usrCreacion
     */
    public function setUsrCreacion($usrCreacion) {
        $this->usrCreacion = $usrCreacion;
        return $this;
    }
    
    /**
     *
     * @return $feCreacion
     */
    public function getFeCreacion() {
        return $this->feCreacion;
    }
    
    /**
     *
     * @param
     *            $feCreacion
     */
    public function setFeCreacion($feCreacion) {
        $this->feCreacion = $feCreacion;
        return $this;
    }
    
    /**
     *
     * @return $usrUltMod
     */
    public function getUsrUltMod() {
        return $this->usrUltMod;
    }
    
    /**
     *
     * @param
     *            $usrUltMod
     */
    public function setUsrUltMod($usrUltMod) {
        $this->usrUltMod = $usrUltMod;
        return $this;
    }
    
    /**
     *
     * @return $feUltMod
     */
    public function getFeUltMod() {
        return $this->feUltMod;
    }
    
    /**
     *
     * @param
     *            $feUltMod
     */
    public function setFeUltMod($feUltMod) {
        $this->feUltMod = $feUltMod;
        return $this;
    }
}
