<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * telconet\schemaBundle\Entity\VistaProcesoMasivoReactivacion
 *
 * @ORM\Table(name="VISTA_PM_REACTIVACION")
 * @ORM\Entity
 */
class VistaProcesoMasivoReactivacion {

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
     * @var integer $idPersona
     *     
     *      @ORM\Column(name="ID_PERSONA", type="integer", nullable=false)
     */
    private $idPersona;

    /**
     *
     * @var string $nombreCliente
     *     
     *      @ORM\Column(name="NOMBRE_CLIENTE", type="string", nullable=false)
     */
    private $nombreCliente;

    /**
     *
     * @var string $oficinaId
     *     
     *      @ORM\Column(name="OFICINA_ID", type="integer", nullable=false)
     */
    private $oficinaId;

    /**
     *
     * @var string $login
     *     
     *      @ORM\Column(name="LOGIN", type="string", nullable=false)
     */
    private $login;

    /**
     *
     * @var string $nombreOficina
     *     
     *      @ORM\Column(name="NOMBRE_OFICINA", type="string", nullable=false)
     */
    private $nombreOficina;

    /**
     *
     * @var string $formaPagoId
     *     
     *      @ORM\Column(name="FORMA_PAGO_ID", type="integer", nullable=false)
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
     * @var string $saldo
     *     
     *      @ORM\Column(name="SALDO", type="float", nullable=false)
     */
    private $saldo;

    /**
     *
     * @var string $empresaCod
     *     
     *      @ORM\Column(name="EMPRESA_COD", type="string", nullable=false)
     */
    private $empresaCod;


    /**
     *
     * @var datetime $fechaUltimaModificacion
     *     
     *      @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=false)
     */
    private $fechaUltimaModificacion;
    
    /**
     *
     * @var string $rol
     *     
     *      @ORM\Column(name="ROL", type="string", nullable=false)
     */
    private $rol;
    
     /**
     *
     * @var string $ultimaMilla
     *
     * @ORM\Column(name="ULTIMA_MILLA", type="string", nullable=false)
     */
    private $ultimaMilla;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
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
     * Get login
     *
     * @return string
     */
    public function getLogin() {
        return $this->login;
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
     * Get oficinaId
     *
     * @return integer
     */
    public function getOficinaId() {
        return $this->oficinaId;
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
     * Get formaPagoId
     *
     * @return integer
     */
    public function getFormaPagoId() {
        return $this->formaPagoId;
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
     * Get saldo
     *
     * @return float
     */
    public function getSaldo() {
        return $this->saldo;
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
     * Get fechaUltimaModificacion
     *
     * @return datetime
     */
    public function getFechaUltimaModificacion() {
        return $this->fechaUltimaModificacion;
    }
    
     /**
     * Get rol
     *
     * @return string
     */
    public function getRol() {
        return $this->rol;
    }
    
     /**
     * Get ultimaMilla
     *
     * @return string
     */
    public function getUltimaMilla() {
        return $this->ultimaMilla;
    }

}
?>
