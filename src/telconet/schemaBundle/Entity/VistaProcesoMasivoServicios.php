<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * telconet\schemaBundle\Entity\VistaProcesoMasivoServicios
 *
 * @ORM\Table(name="VISTA_PM_SERVICIOS")
 * @ORM\Entity
 */
class VistaProcesoMasivoServicios {
    
    /**
     *
     * @var integer $id
     *     
     *      @ORM\Column(name="ID_VISTA", type="integer", nullable=false)
     *      @ORM\Id
     */
    private $id;
    
    /**
     *
     * @var integer $personaId
     *     
     *      @ORM\Column(name="ID_PERSONA", type="integer", nullable=false)
     */
    private $personaId;
    
    /**
     *
     * @var string $nombreCliente
     *     
     *      @ORM\Column(name="NOMBRE_CLIENTE", type="string", nullable=false)
     */
    private $nombreCliente;
    
    /**
     *
     * @var string $empresaCod
     *     
     *      @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
     */
    private $empresaCod;
    
    /**
     *
     * @var string $oficinaId
     *     
     *      @ORM\Column(name="ID_OFICINA", type="integer", nullable=false)
     */
    private $oficinaId;
    
    /**
     *
     * @var string $nombreOficina
     *     
     *      @ORM\Column(name="NOMBRE_OFICINA", type="string", nullable=false)
     */
    private $nombreOficina;
    
    /**
     *
     * @var integer $formaPagoId
     *     
     *      @ORM\Column(name="ID_FORMA_PAGO", type="integer", nullable=false)
     */
    private $formaPagoId;
    
    /**
     *
     * @var string $descripcionFormaPago
     *     
     *      @ORM\Column(name="DESCRIPCION_FORMA_PAGO", type="string", nullable=false)
     */
    private $descripcionFormaPago;
    
    /**
     *
     * @var integer $puntoId
     *     
     *      @ORM\Column(name="ID_PUNTO", type="integer", nullable=false)
     */
    private $puntoId;
    
    /**
     *
     * @var string $login
     *     
     *      @ORM\Column(name="LOGIN", type="string", nullable=false)
     */
    private $login;
    
    /**
     *
     * @var string $planId
     *     
     *      @ORM\Column(name="ID_PLAN", type="string", nullable=false)
     */
    private $planId;
    
    /**
     *
     * @var string $nombrePlan
     *     
     *      @ORM\Column(name="NOMBRE_PLAN", type="string", nullable=false)
     */
    private $nombrePlan;
    
    /**
     *
     * @var integer $servicioId
     *     
     *      @ORM\Column(name="ID_SERVICIO", type="integer", nullable=false)
     */
    private $servicioId;
    
    /**
     *
     * @var string $estadoServicio
     *     
     *      @ORM\Column(name="ESTADO", type="string", nullable=false)
     */
    private $estadoServicio;
    
    /**
     *
     * @var string $precioVenta
     *     
     *      @ORM\Column(name="PRECIO_VENTA", type="string", nullable=false)
     */
    private $precioVenta;
    
    /**
     *
     * @var string $descripcionProducto
     *     
     *      @ORM\Column(name="DESCRIPCION_PRODUCTO", type="string", nullable=false)
     */
    private $descripcionProducto;

    /**
     * *****************************************************************************************
     * *****************************************************************************************
     */
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get personaId
     *
     * @return integer
     */
    public function getPersonaId() {
        return $this->personaId;
    }

    /**
     * Get nombreCliente
     *
     * @return string
     */
    public function getNombrecliente() {
        return $this->nombreCliente;
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
     * Get oficinaId
     *
     * @return integer
     */
    public function getOficinaId() {
        return $this->oficinaId;
    }

    /**
     * Get nombreOficina
     *
     * @return string
     */
    public function getNombreOficina() {
        return $this->nombreOficina;
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
     * Get descripcionFormaPago
     *
     * @return string
     */
    public function getDescripcionFormaPago() {
        return $this->descripcionFormaPago;
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
     * Get login
     *
     * @return string
     */
    public function getLogin() {
        return $this->login;
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
     * Get nombrePlan
     *
     * @return string
     */
    public function getNombrePlan() {
        return $this->nombrePlan;
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
     * Get estadoServicio
     *
     * @return string
     */
    public function getEstadoServicio() {
        return $this->estadoServicio;
    }

    /**
     * Get precioVenta
     *
     * @return float
     */
    public function getPrecioVenta() {
        return $this->precioVenta;
    }

    /**
     * Get descripcionProducto
     *
     * @return string
     */
    public function getDescripcionProducto() {
        return $this->descripcionProducto;
    }

}
?>
