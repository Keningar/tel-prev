<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\AdmiNumeracionHisto
 *
 * @ORM\Table(name="ADMI_NUMERACION_HISTO")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\AdmiNumeracionHistoRepository")
 */
class AdmiNumeracionHisto
{
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
    * @var string $secuenciaInicio
    *
    * @ORM\Column(name="SECUENCIA_INICIO", type="integer", nullable=true)
    */		

    private $secuenciaInicio;

    /**
    * @var string $secuenciaFin
    *
    * @ORM\Column(name="SECUENCIA_FIN", type="integer", nullable=true)
    */		

    private $secuenciaFin;    
    
    /**
    * @var datetime $feAutorizacion
    *
    * @ORM\Column(name="FE_AUTORIZACION", type="datetime", nullable=false)
    */		

    private $feAutorizacion;
    
    /**
    * @var datetime $feCaducidad
    *
    * @ORM\Column(name="FE_CADUCIDAD", type="datetime", nullable=false)
    */		

    private $feCaducidad;    
    
    /**
    * @var string $numeracionUno
    *
    * @ORM\Column(name="NUMERACION_UNO", type="string", nullable=true)
    */		

    private $numeracionUno;

    /**
    * @var string $numeracionDos
    *
    * @ORM\Column(name="NUMERACION_DOS", type="string", nullable=true)
    */		

    private $numeracionDos;
    
    /**
    * @var AdmiNumeracion
    *
    * @ORM\ManyToOne(targetEntity="AdmiNumeracion")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="NUMERACION_ID", referencedColumnName="ID_NUMERACION")
    * })
    */		

    private $numeracionId;

    /**
    * @var string $numeroAutorizacion
    *
    * @ORM\Column(name="NUMERO_AUTORIZACION", type="string", nullable=true)
    */		

    private $numeroAutorizacion;
    
    /**
    * @var string $codEstablecimiento
    *
    * @ORM\Column(name="COD_ESTABLECIMIENTO", type="string", nullable=true)
    */		

    private $codEstablecimiento;    
    
    /**
    * @var integer $id
    *
    * @ORM\Column(name="ID_NUMERACION_HISTO", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="SEQUENCE")
    * @ORM\SequenceGenerator(sequenceName="SEQ_ADMI_NUMERACION_HISTO", allocationSize=1, initialValue=1)
    */		

    private $id;
    
    
    
    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }

    public function getFeCreacion() {
        return $this->feCreacion;
    }

    public function setFeCreacion($feCreacion) {
        $this->feCreacion = $feCreacion;
    }

    public function getUsrCreacion() {
        return $this->usrCreacion;
    }

    public function setUsrCreacion($usrCreacion) {
        $this->usrCreacion = $usrCreacion;
    }

    public function getSecuenciaInicio() {
        return $this->secuenciaInicio;
    }

    public function setSecuenciaInicio($secuenciaInicio) {
        $this->secuenciaInicio = $secuenciaInicio;
    }

    public function getSecuenciaFin() {
        return $this->secuenciaFin;
    }

    public function setSecuenciaFin($secuenciaFin) {
        $this->secuenciaFin = $secuenciaFin;
    }

    public function getFeAutorizacion() {
        return $this->feAutorizacion;
    }

    public function setFeAutorizacion($feAutorizacion) {
        $this->feAutorizacion = $feAutorizacion;
    }

    public function getFeCaducidad() {
        return $this->feCaducidad;
    }

    public function setFeCaducidad($feCaducidad) {
        $this->feCaducidad = $feCaducidad;
    }

    public function getNumeracionUno() {
        return $this->numeracionUno;
    }

    public function setNumeracionUno($numeracionUno) {
        $this->numeracionUno = $numeracionUno;
    }

    public function getNumeracionDos() {
        return $this->numeracionDos;
    }

    public function setNumeracionDos($numeracionDos) {
        $this->numeracionDos = $numeracionDos;
    }

    public function getNumeracionId() {
        return $this->numeracionId;
    }

    public function setNumeracionId(AdmiNumeracion $numeracionId) {
        $this->numeracionId = $numeracionId;
    }

    public function getNumeroAutorizacion() {
        return $this->numeroAutorizacion;
    }

    public function setNumeroAutorizacion($numeroAutorizacion) {
        $this->numeroAutorizacion = $numeroAutorizacion;
    }

    public function getCodEstablecimiento() {
        return $this->codEstablecimiento;
    }

    public function setCodEstablecimiento($codEstablecimiento) {
        $this->codEstablecimiento = $codEstablecimiento;
    }

    public function getId() {
        return $this->id;
    }



    
    
}
