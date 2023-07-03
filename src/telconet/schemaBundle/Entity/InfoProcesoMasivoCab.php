<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoProcesoMasivoCab
 *
 * @ORM\Table(name="INFO_PROCESO_MASIVO_CAB")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoProcesoMasivoCabRepository")
 */
class InfoProcesoMasivoCab {
    
    /**
     *
     * @var integer $id
     *     
     *      @ORM\Column(name="ID_PROCESO_MASIVO_CAB", type="integer", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="SEQUENCE")
     *      @ORM\SequenceGenerator(sequenceName="SEQ_INFO_PROCESO_MASIVO_CAB", allocationSize=1, initialValue=1)
     */
    private $id;
    
    /**
     *
     * @var string $tipoProceso
     *     
     *      @ORM\Column(name="TIPO_PROCESO", type="string", nullable=false)
     */
    private $tipoProceso;
    
    /**
     *
     * @var integer $canalPagoLineaId
     *     
     *      @ORM\Column(name="CANAL_PAGO_LINEA_ID", type="integer", nullable=true)
     */
    private $canalPagoLineaId;
    
    /**
     *
     * @var string $empresaCod
     *     
     *      @ORM\Column(name="EMPRESA_ID", type="string", nullable=false)
     */
    private $empresaCod;
    
    /**
     *
     * @var integer $cantidadPuntos
     *     
     *      @ORM\Column(name="CANTIDAD_PUNTOS", type="integer", nullable=false)
     */
    private $cantidadPuntos;
    
    /**
     *
     * @var integer $cantidadServicios
     *     
     *      @ORM\Column(name="CANTIDAD_SERVICIOS", type="integer", nullable=true)
     */
    private $cantidadServicios;
    
    /**
     *
     * @var string $facturasRecurrentes
     *     
     *      @ORM\Column(name="FACTURAS_RECURRENTES", type="integer", nullable=true)
     */
    private $facturasRecurrentes;
    
    /**
     *
     * @var datetime $fechaEmisionFactura
     *     
     *      @ORM\Column(name="FECHA_CORTE_DESDE", type="datetime", nullable=true)
     */
    private $fechaCorteDesde;
    
    /**
     *
     * @var datetime $fechaEmisionFactura
     *     
     *      @ORM\Column(name="FECHA_CORTE_HASTA", type="datetime", nullable=true)
     */
    private $fechaCorteHasta;
    
    /**
     *
     * @var datetime $fechaEmisionFactura
     *     
     *      @ORM\Column(name="FECHA_EMISION_FACTURA", type="datetime", nullable=true)
     */
    private $fechaEmisionFactura;
    
    /**
     *
     * @var integer $valorDeuda
     *     
     *      @ORM\Column(name="VALOR_DEUDA", type="integer", nullable=true)
     */
    private $valorDeuda;
    
    /**
     *
     * @var integer $formaPagoId
     *     
     *      @ORM\Column(name="FORMA_PAGO_ID", type="integer", nullable=true)
     */
    private $formaPagoId;
    
    /**
     *
     * @var string $idsBancosTarjetas
     *     
     *      @ORM\Column(name="IDS_BANCOS_TARJETAS", type="string", nullable=true)
     */
    private $idsBancosTarjetas;
    
    /**
     *
     * @var string $idsOficinas
     *     
     *      @ORM\Column(name="IDS_OFICINAS", type="string", nullable=true)
     */
    private $idsOficinas;
    
    /**
     *
     * @var string $estado
     *     
     *      @ORM\Column(name="ESTADO", type="string", nullable=false)
     */
    private $estado;
    
    /**
     *
     * @var datetime $feCreacion
     *     
     *      @ORM\Column(name="FE_CREACION", type="datetime", nullable=true)
     */
    private $feCreacion;
    
    /**
     *
     * @var datetime $feUltMod
     *     
     *      @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
     */
    private $feUltMod;
    
    /**
     *
     * @var string $usrCreacion
     *     
     *      @ORM\Column(name="USR_CREACION", type="string", nullable=true)
     */
    private $usrCreacion;
    
    /**
     *
     * @var string $usrUltMod
     *     
     *      @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
     */
    private $usrUltMod;
    
    /**
     *
     * @var string $ipCreacion
     *     
     *      @ORM\Column(name="IP_CREACION", type="string", nullable=true)
     */
    private $ipCreacion;
    
    /**
     *
     * @var integer $planId
     *
     *      @ORM\Column(name="PLAN_ID", type="integer", nullable=true)
     */
    private $planId;
    
    /**
     *
     * @var string $planValor
     *
     *      @ORM\Column(name="PLAN_VALOR", type="string", nullable=true)
     */
    private $planValor;
    
    /**
     *
     * @var integer $pagoId
     *
     *      @ORM\Column(name="PAGO_ID", type="integer", nullable=true)
     */
    private $pagoId;
    
    /**
     *
     * @var integer $pagoLineaId
     *
     *      @ORM\Column(name="PAGO_LINEA_ID", type="integer", nullable=true)
     */
    private $pagoLineaId;
    
    /**
     *
     * @var integer $recaudacionId
     *
     *      @ORM\Column(name="RECAUDACION_ID", type="integer", nullable=true)
     */
    private $recaudacionId;
    
    /**
     *
     * @var integer $debitoId
     *
     *      @ORM\Column(name="DEBITO_ID", type="integer", nullable=true)
     */
    private $debitoId;
    
    /**
     *
     * @var integer $elementoId
     *
     *      @ORM\Column(name="ELEMENTO_ID", type="integer", nullable=true)
     */
    private $elementoId;
    
    /**
     * @var integer solicitudId
     *
     * @ORM\Column(name="SOLICITUD_ID", type="integer", nullable=true)
     */
    private $solicitudId;
    
    // ********************************************************************************************************
    // Getters And Setters
    // ********************************************************************************************************
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get tipoProceso
     *
     * @return string
     */
    public function getTipoProceso() {
        return $this->tipoProceso;
    }

    /**
     * Set tipoProceso
     *
     * @param string $tipoProceso            
     */
    public function setTipoProceso($tipoProceso) {
        $this->tipoProceso = $tipoProceso;
    }

    /**
     * Get empresaCod
     *
     * @return string
     */
    public function getEmpresaCod() {
        return $this->empresaCod;
    }

    /**
     * Set empresaCod
     *
     * @param string $empresaCod            
     */
    public function setEmpresaCod($empresaCod) {
        $this->empresaCod = $empresaCod;
    }

    /**
     * Get canalPagoLineaId
     *
     * @return integer
     */
    public function getCanalPagoLineaId() {
        return $this->canalPagoLineaId;
    }

    /**
     * Set canalPagoLineaId
     *
     * @param integer
     */
    public function setCanalPagoLineaId($canalPagoLineaId) {
        $this->canalPagoLineaId = $canalPagoLineaId;
    }

    /**
     * Get cantidadPuntos
     *
     * @return integer
     */
    public function getCantidadPuntos() {
        return $this->cantidadPuntos;
    }

    /**
     * Set canalPagoLineaId
     *
     * @param integer $cantidadPuntos            
     */
    public function setCantidadPuntos($cantidadPuntos) {
        $this->cantidadPuntos = $cantidadPuntos;
    }

    /**
     * Get cantidadServicios
     *
     * @return integer
     */
    public function getCantidadServicios() {
        return $this->cantidadServicios;
    }

    /**
     * Set cantidadServicios
     *
     * @param integer $cantidadServicios            
     */
    public function setCantidadServicios($cantidadServicios) {
        $this->cantidadServicios = $cantidadServicios;
    }

    /**
     * Get facturasRecurrentes
     *
     * @return integer
     */
    public function getFacturasRecurrentes() {
        return $this->facturasRecurrentes;
    }

    /**
     * Set facturasRecurrentes
     *
     * @param integer $facturasRecurrentes            
     */
    public function setFacturasRecurrentes($facturasRecurrentes) {
        $this->facturasRecurrentes = $facturasRecurrentes;
    }

    /**
     * Get fechaEmisionFactura
     *
     * @return datetime
     */
    public function getFechaEmisionFactura() {
        return $this->fechaEmisionFactura;
    }

    /**
     * Set fechaEmisionFactura
     *
     * @param datetime $fechaEmisionFactura            
     */
    public function setFechaEmisionFactura($fechaEmisionFactura) {
        $this->fechaEmisionFactura = $fechaEmisionFactura;
    }

    /**
     * Get fechaCorteDesde
     *
     * @return datetime
     */
    public function getFechaCorteDesde() {
        return $this->fechaCorteDesde;
    }

    /**
     * Set fechaCorteDesde
     *
     * @param datetime $fechaCorteDesde            
     */
    public function setFechaCorteDesde($fechaCorteDesde) {
        $this->fechaCorteDesde = $fechaCorteDesde;
    }

    /**
     * Get fechaCorteHasta
     *
     * @return datetime
     */
    public function getFechaCorteHasta() {
        return $this->fechaCorteHasta;
    }

    /**
     * Set fechaCorteHasta
     *
     * @param datetime $fechaCorteHasta            
     */
    public function setFechaCorteHasta($fechaCorteHasta) {
        $this->fechaCorteHasta = $fechaCorteHasta;
    }

    /**
     * Get valorDeuda
     *
     * @return integer
     */
    public function getValorDeuda() {
        return $this->valorDeuda;
    }

    /**
     * Set valorDeuda
     *
     * @param integer $valorDeuda            
     */
    public function setValorDeuda($valorDeuda) {
        $this->valorDeuda = $valorDeuda;
    }

    /**
     * Get formaPagoId
     *
     * @return integer
     */
    public function getFormaPagoId() {
        return $this->formaPagoId;
    }

    /**
     * Set formaPagoId
     *
     * @param integer $formaPagoId            
     */
    public function setFormaPagoId($formaPagoId) {
        $this->formaPagoId = $formaPagoId;
    }

    /**
     * Get idsBancosTarjetas
     *
     * @return integer
     */
    public function getIdsBancosTarjetas() {
        return $this->idsBancosTarjetas;
    }

    /**
     * Set idsBancosTarjetas
     *
     * @param integer $idsBancosTarjetas            
     */
    public function setIdsBancosTarjetas($idsBancosTarjetas) {
        $this->idsBancosTarjetas = $idsBancosTarjetas;
    }

    /**
     * Get idsOficinas
     *
     * @return integer
     */
    public function getIdsOficinas() {
        return $this->idsOficinas;
    }

    /**
     * Set idsOficinas
     *
     * @param integer $idsOficinas            
     */
    public function setIdsOficinas($idsOficinas) {
        $this->idsOficinas = $idsOficinas;
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
     * Set estado
     *
     * @param string $estado            
     */
    public function setEstado($estado) {
        $this->estado = $estado;
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
     * Get planId
     *
     * @return integer
     */
    public function getPlanId() {
        return $this->planId;
    }
    
    /**
     * Set planId
     *
     * @param integer $planId
     */
    public function setPlanId($planId) {
        $this->planId = $planId;
    }
    
    /**
     * Get planValor
     *
     * @return string
     */
    public function getPlanValor() {
        return $this->planValor;
    }
    
    /**
     * Set planValor
     *
     * @param string $planValor
     */
    public function setPlanValor($planValor) {
        $this->planValor = $planValor;
    }

    /**
     *
     * @return integer
     */
    public function getPagoId()
    {
        return $this->pagoId;
    }

    /**
     *
     * @param integer $pagoId
     */
    public function setPagoId($pagoId)
    {
        $this->pagoId = $pagoId;
    }

    /**
     *
     * @return integer
     */
    public function getPagoLineaId()
    {
        return $this->pagoLineaId;
    }

    /**
     *
     * @param integer $pagoLineaId
     */
    public function setPagoLineaId($pagoLineaId)
    {
        $this->pagoLineaId = $pagoLineaId;
    }

    /**
     *
     * @return integer
     */
    public function getRecaudacionId()
    {
        return $this->recaudacionId;
    }

    /**
     *
     * @param integer $recaudacionId
     */
    public function setRecaudacionId($recaudacionId)
    {
        $this->recaudacionId = $recaudacionId;
    }

    /**
     *
     * @return integer
     */
    public function getDebitoId()
    {
        return $this->debitoId;
    }

    /**
     *
     * @param integer $elementoId
     */
    public function setDebitoId($elementoId)
    {
        $this->elementoId = $elementoId;
    }
    
    /**
     *
     * @return integer
     */
    public function getElementoId()
    {
        return $this->elementoId;
    }

    /**
     *
     * @param integer $elementoId
     */
    public function setElementoId($elementoId)
    {
        $this->elementoId = $elementoId;
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
}
