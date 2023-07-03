<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\MigraArcgae
 *
 * @ORM\Table(name="MIGRA_ARCGAE")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\MigraArcgaeRepository")
 */
class MigraArcgae
{


/**
* @ORM\Column(name="NO_CIA", type="string", nullable=false)
* @ORM\Id
*/	
		
private $noCia;	

/**
* @var integer $ano
*
* @ORM\Column(name="ANO", type="integer", nullable=false)
*/	
		
private $ano;	

/**
* @var integer $mes
*
* @ORM\Column(name="MES", type="integer", nullable=false)
*/	
		
private $mes;	
	
/**
* @var string $noAsiento
*
* @ORM\Column(name="NO_ASIENTO", type="string", nullable=false)
*/		
     		
private $noAsiento;

/**
* @var string $impreso
*
* @ORM\Column(name="IMPRESO", type="string", nullable=false)
*/		
     		
private $impreso;

/**
* @var date $fecha
*
* @ORM\Column(name="FECHA", type="date", nullable=false)
*/		
     		
private $fecha;

/**
* @var string $descri1
*
* @ORM\Column(name="DESCRI1", type="string", nullable=false)
*/		
     		
private $descri1;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/		
     		
private $estado;

/**
* @var string $autorizado
*
* @ORM\Column(name="AUTORIZADO", type="string", nullable=false)
*/		
     		
private $autorizado;

/**
* @var string $origen
*
* @ORM\Column(name="ORIGEN", type="string", nullable=false)
*/		
     		
private $origen;

/**
* @var float $tDebitos
*
* @ORM\Column(name="T_DEBITOS", type="float", nullable=false)
*/		
     		
private $tDebitos;
/**
* @var float $tCreditos
*
* @ORM\Column(name="T_CREDITOS", type="float", nullable=false)
*/		
     		
private $tCreditos;

/**
* @var string $codDiario
*
* @ORM\Column(name="COD_DIARIO", type="string", nullable=false)
*/		
     		
private $codDiario;

/**
* @var string $tCambioCV
*
* @ORM\Column(name="T_CAMB_C_V", type="string", nullable=false)
*/		
     		
private $tCambCV;

/**
* @var string $tipoCambio
*
* @ORM\Column(name="TIPO_CAMBIO", type="string", nullable=false)
*/		
     		
private $tipoCambio;

/**
* @var string $tipoComprobante
*
* @ORM\Column(name="TIPO_COMPROBANTE", type="string", nullable=false)
*/		
     		
private $tipoComprobante;

/**
* @var string $anulado
*
* @ORM\Column(name="ANULADO", type="string", nullable=false)
*/		
     		
private $anulado;

/**
* @var string $usuarioCreacion
*
* @ORM\Column(name="USUARIO_CREACION", type="string", nullable=false)
*/		
     		
private $usuarioCreacion;

/**
* @var string $transferido
*
* @ORM\Column(name="TRANSFERIDO", type="string", nullable=false)
*/		
     		
private $transferido;

/**
* @var date $fechaCreacion
*
* @ORM\Column(name="FECHA_CREACION", type="date", nullable=false)
*/		
     		
private $fechaCreacion;


    /**
     * Set noCia
     *
     * @param string $noCia
     * @return MigraArcgae
     */
    public function setNoCia($noCia)
    {
        $this->noCia = $noCia;
    
    }

    /**
     * Get noCia
     *
     * @return string 
     */
    public function getNoCia()
    {
        return $this->noCia;
    }

    /**
     * Set ano
     *
     * @param integer $ano
     * @return MigraArcgae
     */
    public function setAno($ano)
    {
        $this->ano = $ano;
    
    }

    /**
     * Get ano
     *
     * @return integer 
     */
    public function getAno()
    {
        return $this->ano;
    }

    /**
     * Set mes
     *
     * @param integer $mes
     * @return MigraArcgae
     */
    public function setMes($mes)
    {
        $this->mes = $mes;
    
    }

    /**
     * Get mes
     *
     * @return integer 
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * Set noAsiento
     *
     * @param string $noAsiento
     * @return MigraArcgae
     */
    public function setNoAsiento($noAsiento)
    {
        $this->noAsiento = $noAsiento;
    
    }

    /**
     * Get noAsiento
     *
     * @return string 
     */
    public function getNoAsiento()
    {
        return $this->noAsiento;
    }

    /**
     * Set impreso
     *
     * @param string $impreso
     * @return MigraArcgae
     */
    public function setImpreso($impreso)
    {
        $this->impreso = $impreso;
    
    }

    /**
     * Get impreso
     *
     * @return string 
     */
    public function getImpreso()
    {
        return $this->impreso;
    }

    /**
     * Set fecha
     *
     * @param \Date $fecha
     * @return MigraArcgae
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    
    }

    /**
     * Get fecha
     *
     * @return \Date 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set descri1
     *
     * @param string $descri1
     * @return MigraArcgae
     */
    public function setDescri1($descri1)
    {
        $this->descri1 = $descri1;
    
    }

    /**
     * Get descri1
     *
     * @return string 
     */
    public function getDescri1()
    {
        return $this->descri1;
    }

    /**
     * Set estado
     *
     * @param string $estado
     * @return MigraArcgae
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    
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
     * Set autorizado
     *
     * @param string $autorizado
     * @return MigraArcgae
     */
    public function setAutorizado($autorizado)
    {
        $this->autorizado = $autorizado;
    
    }

    /**
     * Get autorizado
     *
     * @return string 
     */
    public function getAutorizado()
    {
        return $this->autorizado;
    }

    /**
     * Set origen
     *
     * @param string $origen
     * @return MigraArcgae
     */
    public function setOrigen($origen)
    {
        $this->origen = $origen;
    
    }

    /**
     * Get origen
     *
     * @return string 
     */
    public function getOrigen()
    {
        return $this->origen;
    }

    /**
     * Set tDebitos
     *
     * @param float $tDebitos
     * @return MigraArcgae
     */
    public function setTDebitos($tDebitos)
    {
        $this->tDebitos = $tDebitos;
    
    }

    /**
     * Get tDebitos
     *
     * @return float 
     */
    public function getTDebitos()
    {
        return $this->tDebitos;
    }

    /**
     * Set tCreditos
     *
     * @param float $tCreditos
     * @return MigraArcgae
     */
    public function setTCreditos($tCreditos)
    {
        $this->tCreditos = $tCreditos;
    
    }

    /**
     * Get tCreditos
     *
     * @return float 
     */
    public function getTCreditos()
    {
        return $this->tCreditos;
    }

    /**
     * Set codDiario
     *
     * @param string $codDiario
     * @return MigraArcgae
     */
    public function setCodDiario($codDiario)
    {
        $this->codDiario = $codDiario;
    
    }

    /**
     * Get codDiario
     *
     * @return string 
     */
    public function getCodDiario()
    {
        return $this->codDiario;
    }

    /**
     * Set tCambCV
     *
     * @param string $tCambioCV
     * @return MigraArcgae
     */
    public function setTCambCV($tCambCV)
    {
        $this->tCambCV = $tCambCV;
    
    }

    /**
     * Get tCambCV
     *
     * @return string 
     */
    public function getTCambCV()
    {
        return $this->tCambCV;
    }

    /**
     * Set tipoCambio
     *
     * @param string $tipoCambio
     * @return MigraArcgae
     */
    public function setTipoCambio($tipoCambio)
    {
        $this->tipoCambio = $tipoCambio;
    
    }

    /**
     * Get tipoCambio
     *
     * @return string 
     */
    public function getTipoCambio()
    {
        return $this->tipoCambio;
    }

    /**
     * Set tipoComprobante
     *
     * @param string $tipoComprobante
     * @return MigraArcgae
     */
    public function setTipoComprobante($tipoComprobante)
    {
        $this->tipoComprobante = $tipoComprobante;
    
    }

    /**
     * Get tipoComprobante
     *
     * @return string 
     */
    public function getTipoComprobante()
    {
        return $this->tipoComprobante;
    }

    /**
     * Set anulado
     *
     * @param string $anulado
     * @return MigraArcgae
     */
    public function setAnulado($anulado)
    {
        $this->anulado = $anulado;
    
    }

    /**
     * Get anulado
     *
     * @return string 
     */
    public function getAnulado()
    {
        return $this->anulado;
    }

    /**
     * Set usuarioCreacion
     *
     * @param string $usuarioCreacion
     * @return MigraArcgae
     */
    public function setUsuarioCreacion($usuarioCreacion)
    {
        $this->usuarioCreacion = $usuarioCreacion;
    
    }

    /**
     * Get usuarioCreacion
     *
     * @return string 
     */
    public function getUsuarioCreacion()
    {
        return $this->usuarioCreacion;
    }

    /**
     * Set transferido
     *
     * @param string $transferido
     * @return MigraArcgae
     */
    public function setTransferido($transferido)
    {
        $this->transferido = $transferido;
    
    }

    /**
     * Get transferido
     *
     * @return string 
     */
    public function getTransferido()
    {
        return $this->transferido;
    }

    /**
     * Set fechaCreacion
     *
     * @param \Date $fechaCreacion
     * @return MigraArcgae
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;
    
    }

    /**
     * Get fechaCreacion
     *
     * @return \Date 
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }
}
