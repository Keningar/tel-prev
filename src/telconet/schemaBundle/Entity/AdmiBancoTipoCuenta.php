<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiBancoTipoCuenta
 *
 * @ORM\Table(name="ADMI_BANCO_TIPO_CUENTA")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiBancoTipoCuentaRepository")
 */
class AdmiBancoTipoCuenta
{


    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID_BANCO_TIPO_CUENTA", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_BANCO_TIPO_CUENTA", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var AdmiBanco
     *
     * @ORM\ManyToOne(targetEntity="AdmiBanco")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="BANCO_ID", referencedColumnName="ID_BANCO")
     * })
     */
    private $bancoId;

    /**
     * @var AdmiTipoCuenta
     *
     * @ORM\ManyToOne(targetEntity="AdmiTipoCuenta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="TIPO_CUENTA_ID", referencedColumnName="ID_TIPO_CUENTA")
     * })
     */
    private $tipoCuentaId;

    /**
     * @var integer $totalCaracteres
     *
     * @ORM\Column(name="TOTAL_CARACTERES", type="integer", nullable=true)
     */
    private $totalCaracteres;

    /**
     * @var integer $totalCodseguridad
     *
     * @ORM\Column(name="TOTAL_CODSEGURIDAD", type="integer", nullable=true)
     */
    private $totalCodseguridad;

    /**
     * @var string $caracterEmpieza
     *
     * @ORM\Column(name="CARACTER_EMPIEZA", type="string", nullable=true)
     */
    private $caracterEmpieza;

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
     * @var string $usrUltMod
     *
     * @ORM\Column(name="USR_ULT_MOD", type="string", nullable=true)
     */
    private $usrUltMod;

    /**
     * @var datetime $feUltMod
     *
     * @ORM\Column(name="FE_ULT_MOD", type="datetime", nullable=true)
     */
    private $feUltMod;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="ESTADO", type="string", nullable=false)
     */
    private $estado;

    /**
     * @var string $esTarjeta
     *
     * @ORM\Column(name="ES_TARJETA", type="string", nullable=true)
     */
    private $esTarjeta;

    /**
     * @var string $nombreArchivoFormato
     *
     * @ORM\Column(name="NOMBRE_ARCHIVO_FORMATO", type="string", nullable=true)
     */
    private $nombreArchivoFormato;

    /**
     * @var string $tipoArchivoFormato
     *
     * @ORM\Column(name="TIPO_ARCHIVO_FORMATO", type="string", nullable=true)
     */
    private $tipoArchivoFormato;

    /**
     * @var string $separadorColumna
     *
     * @ORM\Column(name="SEPARADOR_COLUMNA", type="string", nullable=true)
     */
    private $separadorColumna;

    /**
     * @var string $consultarPor
     *
     * @ORM\Column(name="CONSULTAR_POR", type="string", nullable=true)
     */
    private $consultarPor;

    /**
     * @var integer $totalCaracteresMinimo
     *
     * @ORM\Column(name="TOTAL_CARACTERES_MINIMO", type="integer", nullable=true)
     */
    private $totalCaracteresMinimo;

    /**
     * @var string $caracterNoEmpieza
     *
     * @ORM\Column(name="CARACTER_NO_EMPIEZA", type="string", nullable=true)
     */
    private $caracterNoEmpieza;

    /**
     * @var string $formatoCodSeguridad
     *
     * @ORM\Column(name="FORMATO_COD_SEGURIDAD", type="string", nullable=true)
     */
    private $formatoCodSeguridad;

    /**
     * @var string $visibleEn
     *
     * @ORM\Column(name="VISIBLE_EN", type="string", nullable=true)
     */
    private $visibleEn;

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
     * Get bancoId
     *
     * @return \telconet\schemaBundle\Entity\AdmiBanco
     */
    public function getBancoId()
    {
        return $this->bancoId;
    }

    /**
     * Set bancoId
     *
     * @param telconet\schemaBundle\Entity\AdmiBanco $bancoId
     */
    public function setBancoId(\telconet\schemaBundle\Entity\AdmiBanco $bancoId)
    {
        $this->bancoId = $bancoId;
    }

    /**
     * Get tipoCuentaId
     *
     * @return telconet\schemaBundle\Entity\AdmiTipoCuenta
     */
    public function getTipoCuentaId()
    {
        return $this->tipoCuentaId;
    }

    /**
     * Set tipoCuentaId
     *
     * @param telconet\schemaBundle\Entity\AdmiTipoCuenta $tipoCuentaId
     */
    public function setTipoCuentaId(\telconet\schemaBundle\Entity\AdmiTipoCuenta $tipoCuentaId)
    {
        $this->tipoCuentaId = $tipoCuentaId;
    }

    /**
     * Get totalCaracteres
     *
     * @return integer
     */
    public function getTotalCaracteres()
    {
        return $this->totalCaracteres;
    }

    /**
     * Set totalCaracteres
     *
     * @param integer $totalCaracteres
     */
    public function setTotalCaracteres($totalCaracteres)
    {
        $this->totalCaracteres = $totalCaracteres;
    }

    /**
     * Get totalCodseguridad
     *
     * @return integer
     */
    public function getTotalCodseguridad()
    {
        return $this->totalCodseguridad;
    }

    /**
     * Set totalCodseguridad
     *
     * @param integer $totalCodseguridad
     */
    public function setTotalCodseguridad($totalCodseguridad)
    {
        $this->totalCodseguridad = $totalCodseguridad;
    }

    /**
     * Get caracterEmpieza
     *
     * @return string
     */
    public function getCaracterEmpieza()
    {
        return $this->caracterEmpieza;
    }

    /**
     * Set caracterEmpieza
     *
     * @param string $caracterEmpieza
     */
    public function setCaracterEmpieza($caracterEmpieza)
    {
        $this->caracterEmpieza = $caracterEmpieza;
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
     * Get esTarjeta
     *
     * @return string
     */
    public function getEsTarjeta()
    {
        return $this->esTarjeta;
    }

    /**
     * Set esTarjeta
     *
     * @param string $esTarjeta
     */
    public function setEsTarjeta($esTarjeta)
    {
        $this->esTarjeta = $esTarjeta;
    }

    /**
     * Get nombreArchivoFormato
     *
     * @return string
     */
    public function getNombreArchivoFormato()
    {
        return $this->nombreArchivoFormato;
    }

    /**
     * Set nombreArchivoFormato
     *
     * @param string $nombreArchivoFormato
     */
    public function setNombreArchivoFormato($nombreArchivoFormato)
    {
        $this->nombreArchivoFormato = $nombreArchivoFormato;
    }

    /**
     * Get tipoArchivoFormato
     *
     * @return string
     */
    public function getTipoArchivoFormato()
    {
        return $this->tipoArchivoFormato;
    }

    /**
     * Set tipoArchivoFormato
     *
     * @param string $tipoArchivoFormato
     */
    public function setTipoArchivoFormato($tipoArchivoFormato)
    {
        $this->tipoArchivoFormato = $tipoArchivoFormato;
    }

    /**
     * Get separadorColumna
     *
     * @return string
     */
    public function getSeparadorColumna()
    {
        return $this->separadorColumna;
    }

    /**
     * Set separadorColumna
     *
     * @param string $separadorColumna
     */
    public function setSeparadorColumna($separadorColumna)
    {
        $this->separadorColumna = $separadorColumna;
    }

    /**
     * Get consultarPor
     *
     * @return string
     */
    public function getConsultarPor()
    {
        return $this->consultarPor;
    }

    /**
     * Set consultarPor
     *
     * @param string $consultarPor
     */
    public function setConsultarPor($consultarPor)
    {
        $this->consultarPor = $consultarPor;
    }

    public function __toString()
    {
        return $this->bancoId->getDescripcionBanco();
    }

    /**
     * Get totalCaracteresMinimo
     *
     * @return integer
     */
    public function getTotalCaracteresMinimo()
    {
        return $this->totalCaracteresMinimo;
    }

    /**
     * Set totalCaracteresMinimo
     *
     * @param integer $totalCaracteresMinimo
     */
    public function setTotalCaracteresMinimo($totalCaracteresMinimo)
    {
        $this->totalCaracteresMinimo = $totalCaracteresMinimo;
    }

    /**
     * Get caracterNoEmpieza
     *
     * @return string
     */
    public function getCaracterNoEmpieza()
    {
        return $this->caracterNoEmpieza;
    }

    /**
     * Set caracterNoEmpieza
     *
     * @param string $caracterNoEmpieza
     */
    public function setCaracterNoEmpieza($caracterNoEmpieza)
    {
        $this->caracterNoEmpieza = $caracterNoEmpieza;
    }

    /**
     * Get formatoCodSeguridad
     *
     * @return string
     */
    public function getFormatoCodSeguridad()
    {
        return $this->formatoCodSeguridad;
    }

    /**
     * Set formatoCodSeguridad
     *
     * @param string $formatoCodSeguridad
     */
    public function setFormatoCodSeguridad($formatoCodSeguridad)
    {
        $this->formatoCodSeguridad = $formatoCodSeguridad;
    }

    /**
     * Get visibleEn
     *
     * @return string
     */
    public function getVisibleEn()
    {
        return $this->visibleEN;
    }

    /**
     * Set visibleEn
     *
     * @param string $visibleEn
     */
    public function setVisibleEn($visibleEn)
    {
        $this->visibleEn = $visibleEn;
    }

}