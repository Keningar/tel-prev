<?php

    namespace telconet\schemaBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Component\Validator\Constraints as Assert;

    /**
     * telconet\schemaBundle\Entity\InfoServicio
     *
     * @ORM\Table(name="INFO_SERVICIO")
     * @ORM\Entity
     * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoServicioRepository")
     */
    class InfoServicio
    {

    /**
    * @var string $tipoOrden
    *
    * @ORM\Column(name="TIPO_ORDEN", type="string", nullable=true)
    */		

    private $tipoOrden;

    /**
    * @var string $descripcionPresentaFactura
    *
    * @ORM\Column(name="DESCRIPCION_PRESENTA_FACTURA", type="string", nullable=true)
    */		

    private $descripcionPresentaFactura;

    /**
    * @var datetime $feVigencia
    *
    * @ORM\Column(name="FE_VIGENCIA", type="datetime", nullable=true)
    */		

    private $feVigencia;

    /**
    * @var string $estado
    *
    * @ORM\Column(name="ESTADO", type="string", nullable=true)
    */		

    private $estado;

    /**
    * @var datetime $feCreacion
    *
    * @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
    */		

    private $feCreacion;

    /**
    * @var string $usrCreacion
    *
    * @ORM\Column(name="USR_CREACION", type="string", nullable=false)
    */		

    private $usrCreacion;

    /**
    * @var string $ipCreacion
    *
    * @ORM\Column(name="IP_CREACION", type="string", nullable=true)
    */		

    private $ipCreacion;

    /**
    * @var string $observacion
    *
    * @ORM\Column(name="OBSERVACION", type="string", nullable=false)
    */		

    private $observacion;

    /**
     * @var string $usrVendedor
     *
     * @ORM\Column(name="USR_VENDEDOR", type="string", nullable=false)
     */

    private $usrVendedor;

    /**
     * @var string $origen
     *
     * @ORM\Column(name="ORIGEN", type="string", nullable=true)
     */

    private $origen;


    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_SERVICIO", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_SERVICIO", allocationSize=1, initialValue=1)
    */		

    private $id;	

    /**
    * @var InfoPunto
    *
    * @ORM\ManyToOne(targetEntity="InfoPunto")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="PUNTO_ID", referencedColumnName="ID_PUNTO")
    * })
    */

    private $puntoId;

    /**
    * @var InfoPunto
    *
    * @ORM\ManyToOne(targetEntity="InfoPunto")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="PUNTO_FACTURACION_ID", referencedColumnName="ID_PUNTO")
    * })
    */

    private $puntoFacturacionId;

    /**
    * @var AdmiProducto
    *
    * @ORM\ManyToOne(targetEntity="AdmiProducto")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="PRODUCTO_ID", referencedColumnName="ID_PRODUCTO")
    * })
    */

    private $productoId;

    /**
    * @var InfoPlanCab
    *
    * @ORM\ManyToOne(targetEntity="InfoPlanCab")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="PLAN_ID", referencedColumnName="ID_PLAN")
    * })
    */

    private $planId;

    /**
    * @var InfoOrdenTrabajo
    *
    * @ORM\ManyToOne(targetEntity="InfoOrdenTrabajo")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="ORDEN_TRABAJO_ID", referencedColumnName="ID_ORDEN_TRABAJO")
    * })
    */

    private $ordenTrabajoId;

    /**
    * @var AdmiCiclo
    *
    * @ORM\ManyToOne(targetEntity="AdmiCiclo")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="CICLO_ID", referencedColumnName="ID_CICLO")
    * })
    */

    private $cicloId;

    /**
    * @var string $esVenta
    *
    * @ORM\Column(name="ES_VENTA", type="string", nullable=true)
    */		

    private $esVenta;

    /**
    * @var integer $cantidad
    *
    * @ORM\Column(name="CANTIDAD", type="integer", nullable=true)
    * @Assert\Type(type="numeric", message="La cantidad {{ value }} no es un número válido")
    * @Assert\GreaterThan(value=0, message="La cantidad debe ser mayor que {{ compared_value }}")
    */

    private $cantidad;

    /**
    * @var float $precioVenta
    *
    * @ORM\Column(name="PRECIO_VENTA", type="float", nullable=true)
    */		

    private $precioVenta;

    /**
    * @var float $costo
    *
    * @ORM\Column(name="COSTO", type="float", nullable=true)
    */		

    private $costo;

    /**
    * @var float $porcentajeDescuento
    *
    * @ORM\Column(name="PORCENTAJE_DESCUENTO", type="float", nullable=true)
    */		

    private $porcentajeDescuento;

    /**
    * @var float $valorDescuento
    *
    * @ORM\Column(name="VALOR_DESCUENTO", type="float", nullable=true)
    */		

    private $valorDescuento;

    /**
    * @var integer $diasGracia
    *
    * @ORM\Column(name="DIAS_GRACIA", type="integer", nullable=true)
    */		

    private $diasGracia;

    /**
    * @var integer $frecuenciaProducto
    *
    * @ORM\Column(name="FRECUENCIA_PRODUCTO", type="integer", nullable=true)
    */		

    private $frecuenciaProducto;

    /**
    * @var integer $mesesRestantes
    *
    * @ORM\Column(name="MESES_RESTANTES", type="integer", nullable=true)
    */		

    private $mesesRestantes;

    /**
    * @var integer $loginAux
    *
    * @ORM\Column(name="LOGIN_AUX", type="string", nullable=true)
    */		

    private $loginAux;

    /**
    * Get descripcionPresentaFactura
    *
    * @return 
    */

    /**
    * @var float $precioFormula
    *
    * @ORM\Column(name="PRECIO_FORMULA", type="float", nullable=true)
    */		

    private $precioFormula;

    /**
    * @var float $precioInstalacion
    *
    * @ORM\Column(name="PRECIO_INSTALACION", type="float", nullable=true)
    */		

    private $precioInstalacion;

    /**
    * @var float $descuentoUnitario
    *
    * @ORM\Column(name="DESCUENTO_UNITARIO", type="float", nullable=true)
    */		

    private $descuentoUnitario;

    /**
     * @var integer $refServicioId
     *
     * @ORM\Column(name="REF_SERVICIO_ID", type="integer", nullable=false)
     */
    private $refServicioId;

    /**
     * Get refServicioId
     *
     * @return string
     */
    public function getRefServicioId()
    {
        return $this->refServicioId;
    }

    /**
     * Set refServicioId
     *
     * @param integer $intRefServicioId
     */
    public function setRefServicioId($intRefServicioId)
    {
        $this->refServicioId = $intRefServicioId;
    }

    public function getDescripcionPresentaFactura(){
        return $this->descripcionPresentaFactura; 
    }

    /**
    * Set descripcionPresentaFactura
    *
    * @param  $descripcionPresentaFactura
    */
    public function setDescripcionPresentaFactura($descripcionPresentaFactura)
    {
            $this->descripcionPresentaFactura = $descripcionPresentaFactura;
    }


    /**
    * Get feVigencia
    *
    * @return datetime
    */		

    public function getFeVigencia(){
        return $this->feVigencia; 
    }

    /**
    * Set feVigencia
    *
    * @param datetime $feVigencia
    */
    public function setFeVigencia($feVigencia)
    {
            $this->feVigencia = $feVigencia;
    }


    /**
    * Get estado
    *
    * @return string
    */		

    public function getEstado(){
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
    * Get puntoId
    *
    * @return telconet\schemaBundle\Entity\InfoPunto
    */		

    public function getPuntoId(){
        return $this->puntoId; 
    }

    /**
    * Set puntoId
    *
    * @param telconet\schemaBundle\Entity\InfoPunto $puntoId
    */
    public function setPuntoId(\telconet\schemaBundle\Entity\InfoPunto $puntoId)
    {
            $this->puntoId = $puntoId;
    }


    /**
    * Get puntoFacturacionId
    *
    * @return telconet\schemaBundle\Entity\InfoPunto
    */		

    public function getPuntoFacturacionId(){
        return $this->puntoFacturacionId; 
    }

    /**
    * Set puntoFacturacionId
    *
    * @param telconet\schemaBundle\Entity\InfoPunto $puntoFacturacionId
    */
    public function setPuntoFacturacionId(\telconet\schemaBundle\Entity\InfoPunto $puntoFacturacionId)
    {
            $this->puntoFacturacionId = $puntoFacturacionId;
    }

    /**
    * Get productoId
    *
    * @return telconet\schemaBundle\Entity\AdmiProducto
    */		

    public function getProductoId(){
        return $this->productoId; 
    }

    /**
    * Set productoId
    *
    * @param telconet\schemaBundle\Entity\AdmiProducto $productoId
    */
    public function setProductoId(\telconet\schemaBundle\Entity\AdmiProducto $productoId)
    {
            $this->productoId = $productoId;
    }


    /**
    * Get planId
    *
    * @return telconet\schemaBundle\Entity\InfoPlanCab
    */		

    public function getPlanId(){
        return $this->planId; 
    }

    /**
    * Set planId
    *
    * @param telconet\schemaBundle\Entity\InfoPlanCab $planId
    */
    public function setPlanId(\telconet\schemaBundle\Entity\InfoPlanCab $planId)
    {
            $this->planId = $planId;
    }


    /**
    * Get ordenTrabajoId
    *
    * @return telconet\schemaBundle\Entity\InfoOrdenTrabajo
    */		

    public function getOrdenTrabajoId(){
        return $this->ordenTrabajoId; 
    }

    /**
    * Set ordenTrabajoId
    *
    * @param telconet\schemaBundle\Entity\InfoOrdenTrabajo $ordenTrabajoId
    */
    public function setOrdenTrabajoId(\telconet\schemaBundle\Entity\InfoOrdenTrabajo $ordenTrabajoId)
    {
            $this->ordenTrabajoId = $ordenTrabajoId;
    }


    /**
    * Get cicloId
    *
    * @return telconet\schemaBundle\Entity\AdmiCiclo
    */		

    public function getCicloId(){
        return $this->cicloId; 
    }

    /**
    * Set cicloId
    *
    * @param telconet\schemaBundle\Entity\AdmiCiclo $cicloId
    */
    public function setCicloId(\telconet\schemaBundle\Entity\AdmiCiclo $cicloId)
    {
            $this->cicloId = $cicloId;
    }


    /**
    * Get esVenta
    *
    * @return string
    */		

    public function getEsVenta(){
        return $this->esVenta; 
    }

    /**
    * Set esVenta
    *
    * @param string $esVenta
    */
    public function setEsVenta($esVenta)
    {
            $this->esVenta = $esVenta;
    }


    /**
    * Get cantidad
    *
    * @return integer
    */		

    public function getCantidad(){
        return $this->cantidad; 
    }

    /**
    * Set cantidad
    *
    * @param integer $cantidad
    */
    public function setCantidad($cantidad)
    {
            $this->cantidad = $cantidad;
    }


    /**
    * Get precioVenta
    *
    * @return float
    */		

    public function getPrecioVenta(){
        return $this->precioVenta; 
    }

    /**
    * Set precioVenta
    *
    * @param float $precioVenta
    */
    public function setPrecioVenta($precioVenta)
    {
            $this->precioVenta = $precioVenta;
    }


    /**
    * Get costo
    *
    * @return float
    */		

    public function getCosto(){
        return $this->costo; 
    }

    /**
    * Set costo
    *
    * @param float $costo
    */
    public function setCosto($costo)
    {
            $this->costo = $costo;
    }


    /**
    * Get porcentajeDescuento
    *
    * @return float
    */		

    public function getPorcentajeDescuento(){
        return $this->porcentajeDescuento; 
    }

    /**
    * Set porcentajeDescuento
    *
    * @param float $porcentajeDescuento
    */
    public function setPorcentajeDescuento($porcentajeDescuento)
    {
            $this->porcentajeDescuento = $porcentajeDescuento;
    }


    /**
    * Get valorDescuento
    *
    * @return float
    */		

    public function getValorDescuento(){
        return $this->valorDescuento; 
    }

    /**
    * Set valorDescuento
    *
    * @param float $valorDescuento
    */
    public function setValorDescuento($valorDescuento)
    {
            $this->valorDescuento = $valorDescuento;
    }


    /**
    * Get diasGracia
    *
    * @return integer
    */		

    public function getDiasGracia(){
        return $this->diasGracia; 
    }

    /**
    * Set diasGracia
    *
    * @param integer $diasGracia
    */
    public function setDiasGracia($diasGracia)
    {
            $this->diasGracia = $diasGracia;
    }


    /**
    * Get frecuenciaProducto
    *
    * @return integer
    */		

    public function getFrecuenciaProducto(){
        return $this->frecuenciaProducto; 
    }

    /**
    * Set frecuenciaProducto
    *
    * @param integer $frecuenciaProducto
    */
    public function setFrecuenciaProducto($frecuenciaProducto)
    {
            $this->frecuenciaProducto = $frecuenciaProducto;
    }


    /**
    * Get mesesRestantes
    *
    * @return integer
    */		

    public function getMesesRestantes(){
        return $this->mesesRestantes; 
    }

    /**
    * Set mesesRestantes
    *
    * @param integer $mesesRestantes
    */
    public function setMesesRestantes($mesesRestantes)
    {
            $this->mesesRestantes = $mesesRestantes;
    }

    /**
    * Get tipoOrden
    *
    * @return string
    */		

    public function getTipoOrden(){
        return $this->tipoOrden; 
    }

    /**
    * Set tipoOrden
    *
    * @param string $tipoOrden
    */
    public function setTipoOrden($tipoOrden)
    {
            $this->tipoOrden = $tipoOrden;
    }

    /**
    * Set mensaje
    *
    * @param string $observacion
    * @return InfoServicio
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

    /**
    * Get loginAux
    *
    * @return 
    */		

    public function getLoginAux()
    {
        return $this->loginAux; 
    }

    /**
    * Set loginAux
    *
    * @param  $loginAux
    */
    public function setLoginAux($loginAux)
    {
        $this->loginAux = $loginAux;
    }

    /**
    * Get precioFormula
    *
    * @return float
    */		

    public function getPrecioFormula(){
        return $this->precioFormula; 
    }

    /**
    * Set precioFormula
    *
    * @param float $precioFormula
    */
    public function setPrecioFormula($precioFormula)
    {
            $this->precioFormula = $precioFormula;
    }

    /**
    * Get precioInstalacion
    *
    * @return float
    */		

    public function getPrecioInstalacion(){
        return $this->precioInstalacion; 
    }

    /**
    * Set precioInstalacion
    *
    * @param float $precioInstalacion
    */
    public function setPrecioInstalacion($precioInstalacion)
    {
            $this->precioInstalacion = $precioInstalacion;
    }

    /**
     * Get usrVendedor
     *
     * @return string
     */
     public function getUsrVendedor()
     {
         return $this->usrVendedor;
     }

    /**
     * Set usrVendedor
     *
     * @param string $usrVendedor
     */
     public function setUsrVendedor($usrVendedor)
     {
         $this->usrVendedor = $usrVendedor;
     }

    /**
    * Get descuentoUnitario
    *
    * @return float
    */		

    public function getDescuentoUnitario(){
        return $this->descuentoUnitario; 
    }

    /**
    * Set setDescuentoUnitario
    *
    * @param float $descuentoUnitario
    */
    public function setDescuentoUnitario($descuentoUnitario)
    {
            $this->descuentoUnitario = $descuentoUnitario;
    } 

    /**
     * Get Origen
     *
     * @return string
     */
    public function getOrigen()
    {
        return $this->origen;
    }

   /**
    * Set Origen
    *
    * @param string $origen
    */
    public function setOrigen($origen)
    {
        $this->origen = $origen;
    }

}
