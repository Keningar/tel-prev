<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoDebitoDet
 *
 * @ORM\Table(name="INFO_DEBITO_DET")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoDebitoDetRepository")
 */
class InfoDebitoDet
{


    /**
     * @var string $empresaId
     *
     * @ORM\Column(name="EMPRESA_ID", type="string", nullable=true)
     */
    private $empresaId;

    /**
     * @var integer $oficinaId
     *
     * @ORM\Column(name="OFICINA_ID", type="integer", nullable=true)
     */
    private $oficinaId;

    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_DEBITO_DET", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_INFO_DEBITO_DET", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer $debitoCabId
     *
     * @ORM\Column(name="DEBITO_CAB_ID", type="integer", nullable=true)
     */
    private $debitoCabId;

    /**
     * @var integer $personaEmpresaRolId
     *
     * @ORM\Column(name="PERSONA_EMPRESA_ROL_ID", type="integer", nullable=true)
     */
    private $personaEmpresaRolId;

    /**
     * @var string $numeroTarjetaCuenta
     *
     * @ORM\Column(name="NUMERO_TARJETA_CUENTA", type="string", nullable=true)
     */
    private $numeroTarjetaCuenta;

    /**
     * @var integer $puntoId
     *
     * @ORM\Column(name="PUNTO_ID", type="integer", nullable=true)
     */
    private $puntoId;

    /**
     * @var float $valorTotal
     *
     * @ORM\Column(name="VALOR_TOTAL", type="float", nullable=true)
     */
    private $valorTotal;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=true)
     */
    private $estado;

    /**
     * @var string $observacionRechazo
     *
     * @ORM\Column(name="OBSERVACION_RECHAZO", type="string", nullable=true)
     */
    private $observacionRechazo;

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
     * @var string $referencia
     *
     * @ORM\Column(name="REFERENCIA", type="string", nullable=true)
     */
    private $referencia;

    /**
     * @var float $valorDebitado
     *
     * @ORM\Column(name="VALOR_DEBITADO", type="float", nullable=true)
     */
    private $valorDebitado;

    /**
     * Get empresaId
     *
     * @return string
     */
    public function getEmpresaId()
    {
        return $this->empresaId;
    }

    /**
     * Set empresaId
     *
     * @param string $empresaId
     */
    public function setEmpresaId($empresaId)
    {
        $this->empresaId = $empresaId;
    }

    /**
     * Get oficinaId
     *
     * @return integer
     */
    public function getOficinaId()
    {
        return $this->oficinaId;
    }

    /**
     * Set oficinaId
     *
     * @param integer $oficinaId
     */
    public function setOficinaId($oficinaId)
    {
        $this->oficinaId = $oficinaId;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get debitoCabId
     *
     * @return integer
     */
    public function getDebitoCabId()
    {
        return $this->debitoCabId;
    }

    /**
     * Set debitoCabId
     *
     * @param integer $debitoCabId
     */
    public function setDebitoCabId($debitoCabId)
    {
        $this->debitoCabId = $debitoCabId;
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
     * Get numeroTarjetaCuenta
     *
     * @return string
     */
    public function getNumeroTarjetaCuenta()
    {
        return $this->numeroTarjetaCuenta;
    }

    /**
     * Set numeroTarjetaCuenta
     *
     * @param string $numeroTarjetaCuenta
     */
    public function setNumeroTarjetaCuenta($numeroTarjetaCuenta)
    {
        $this->numeroTarjetaCuenta = $numeroTarjetaCuenta;
    }

    /**
     * Get puntoId
     *
     * @return integer
     */
    public function getPuntoId()
    {
        return $this->puntoId;
    }

    /**
     * Set puntoId
     *
     * @param integer $puntoId
     */
    public function setPuntoId($puntoId)
    {
        $this->puntoId = $puntoId;
    }

    /**
     * Get valorTotal
     *
     * @return float
     */
    public function getValorTotal()
    {
        return $this->valorTotal;
    }

    /**
     * Set valorTotal
     *
     * @param float $valorTotal
     */
    public function setValorTotal($valorTotal)
    {
        $this->valorTotal = $valorTotal;
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
     * Get observacionRechazo
     *
     * @return string
     */
    public function getObservacionRechazo()
    {
        return $this->observacionRechazo;
    }

    /**
     * Set observacionRechazo
     *
     * @param string $observacionRechazo
     */
    public function setObservacionRechazo($observacionRechazo)
    {
        $this->observacionRechazo = $observacionRechazo;
    }

    /**
     * Get feCreacion
     *
     * @return datetime
     */
    public function getFeCreacion()
    {
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
     * Get usrCreacion
     *
     * @return string
     */
    public function getUsrCreacion()
    {
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
     * Get referencia
     *
     * @return string
     */
    public function getReferencia()
    {
        return $this->referencia;
    }

    /**
     * Set referencia
     *
     * @param string $referencia
     */
    public function setReferencia($referencia)
    {
        $this->referencia = $referencia;
    }

    /**
     * Get valorDebitado
     *
     * @return float
     */
    public function getValorDebitado()
    {
        return $this->valorDebitado;
    }

    /**
     * Set valorDebitado
     *
     * @param string $valorDebitado
     */
    public function setValorDebitado($valorDebitado)
    {
        $this->valorDebitado = $valorDebitado;
    }

}