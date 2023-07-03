<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\MigraArcgal
 *
 * @ORM\Table(name="MIGRA_ARCGAL")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\MigraArcgalRepository")
 */
class MigraArcgal
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
* @var integer $noLinea
*
* @ORM\Column(name="NO_LINEA", type="integer", nullable=false)
*/		
     		
private $noLinea;

/**
* @var string $cuenta
*
* @ORM\Column(name="CUENTA", type="string", nullable=false)
*/		
     		
private $cuenta;

/**
* @var string $descri
*
* @ORM\Column(name="DESCRI", type="string", nullable=false)
*/		
     		
private $descri;

/**
* @var string $codDiario
*
* @ORM\Column(name="COD_DIARIO", type="string", nullable=false)
*/		
     		
private $codDiario;

/**
* @var string $moneda
*
* @ORM\Column(name="MONEDA", type="string", nullable=false)
*/		
     		
private $moneda;

/**
* @var integer $tipoCambio
*
* @ORM\Column(name="TIPO_CAMBIO", type="integer", nullable=false)
*/		
     		
private $tipoCambio;


/**
* @var float $monto
*
* @ORM\Column(name="MONTO", type="float", nullable=false)
*/		
     		
private $monto;

/**
* @var string $centroCosto
*
* @ORM\Column(name="CENTRO_COSTO", type="string", nullable=false)
*/		
     		
private $centroCosto;

/**
* @var string $tipo
*
* @ORM\Column(name="TIPO", type="string", nullable=false)
*/		
     		
private $tipo;

/**
* @var float $montoDol
*
* @ORM\Column(name="MONTO_DOL", type="float", nullable=false)
*/		
     		
private $montoDol;

/**
* @var string $cc1
*
* @ORM\Column(name="CC_1", type="string", nullable=false)
*/		
     		
private $cc1;

/**
* @var string $cc2
*
* @ORM\Column(name="CC_2", type="string", nullable=false)
*/		
     		
private $cc2;

/**
* @var string $cc3
*
* @ORM\Column(name="CC_3", type="string", nullable=false)
*/		
     		
private $cc3;

/**
* @var string $lineaAjustePrecision
*
* @ORM\Column(name="LINEA_AJUSTE_PRECISION", type="string", nullable=false)
*/		
     		
private $lineaAjustePrecision;



    /**
     * Set noCia
     *
     * @param string $noCia
     * @return MigraArcgal
     */
    public function setNoCia($noCia)
    {
        $this->noCia = $noCia;
    
        return $this;
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
     * @return MigraArcgal
     */
    public function setAno($ano)
    {
        $this->ano = $ano;
    
        return $this;
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
     * @return MigraArcgal
     */
    public function setMes($mes)
    {
        $this->mes = $mes;
    
        return $this;
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
     * @return MigraArcgal
     */
    public function setNoAsiento($noAsiento)
    {
        $this->noAsiento = $noAsiento;
    
        return $this;
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
     * Set noLinea
     *
     * @param integer $noLinea
     * @return MigraArcgal
     */
    public function setNoLinea($noLinea)
    {
        $this->noLinea = $noLinea;
    
        return $this;
    }

    /**
     * Get noLinea
     *
     * @return integer 
     */
    public function getNoLinea()
    {
        return $this->noLinea;
    }

    /**
     * Set cuenta
     *
     * @param string $cuenta
     * @return MigraArcgal
     */
    public function setCuenta($cuenta)
    {
        $this->cuenta = $cuenta;
    
        return $this;
    }

    /**
     * Get cuenta
     *
     * @return string 
     */
    public function getCuenta()
    {
        return $this->cuenta;
    }

    /**
     * Set descri
     *
     * @param string $descri
     * @return MigraArcgal
     */
    public function setDescri($descri)
    {
        $this->descri = $descri;
    
        return $this;
    }

    /**
     * Get descri
     *
     * @return string 
     */
    public function getDescri()
    {
        return $this->descri;
    }

    /**
     * Set codDiario
     *
     * @param string $codDiario
     * @return MigraArcgal
     */
    public function setCodDiario($codDiario)
    {
        $this->codDiario = $codDiario;
    
        return $this;
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
     * Set moneda
     *
     * @param string $moneda
     * @return MigraArcgal
     */
    public function setMoneda($moneda)
    {
        $this->moneda = $moneda;
    
        return $this;
    }

    /**
     * Get moneda
     *
     * @return string 
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    /**
     * Set tipoCambio
     *
     * @param integer $tipoCambio
     * @return MigraArcgal
     */
    public function setTipoCambio($tipoCambio)
    {
        $this->tipoCambio = $tipoCambio;
    
        return $this;
    }

    /**
     * Get tipoCambio
     *
     * @return integer 
     */
    public function getTipoCambio()
    {
        return $this->tipoCambio;
    }

    /**
     * Set monto
     *
     * @param float $monto
     * @return MigraArcgal
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;
    
        return $this;
    }

    /**
     * Get monto
     *
     * @return float 
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set centroCosto
     *
     * @param string $centroCosto
     * @return MigraArcgal
     */
    public function setCentroCosto($centroCosto)
    {
        $this->centroCosto = $centroCosto;
    
        return $this;
    }

    /**
     * Get centroCosto
     *
     * @return string 
     */
    public function getCentroCosto()
    {
        return $this->centroCosto;
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     * @return MigraArcgal
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    
        return $this;
    }

    /**
     * Get tipo
     *
     * @return string 
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set montoDol
     *
     * @param float $montoDol
     * @return MigraArcgal
     */
    public function setMontoDol($montoDol)
    {
        $this->montoDol = $montoDol;
    
        return $this;
    }

    /**
     * Get montoDol
     *
     * @return float 
     */
    public function getMontoDol()
    {
        return $this->montoDol;
    }

    /**
     * Set cc1
     *
     * @param string $cc1
     * @return MigraArcgal
     */
    public function setCc1($cc1)
    {
        $this->cc1 = $cc1;
    
        return $this;
    }

    /**
     * Get cc1
     *
     * @return string 
     */
    public function getCc1()
    {
        return $this->cc1;
    }

    /**
     * Set cc2
     *
     * @param string $cc2
     * @return MigraArcgal
     */
    public function setCc2($cc2)
    {
        $this->cc2 = $cc2;
    
        return $this;
    }

    /**
     * Get cc2
     *
     * @return string 
     */
    public function getCc2()
    {
        return $this->cc2;
    }

    /**
     * Set cc3
     *
     * @param string $cc3
     * @return MigraArcgal
     */
    public function setCc3($cc3)
    {
        $this->cc3 = $cc3;
    
        return $this;
    }

    /**
     * Get cc3
     *
     * @return string 
     */
    public function getCc3()
    {
        return $this->cc3;
    }

    /**
     * Set lineaAjustePrecision
     *
     * @param string $lineaAjustePrecision
     * @return MigraArcgal
     */
    public function setLineaAjustePrecision($lineaAjustePrecision)
    {
        $this->lineaAjustePrecision = $lineaAjustePrecision;
    
        return $this;
    }

    /**
     * Get lineaAjustePrecision
     *
     * @return string 
     */
    public function getLineaAjustePrecision()
    {
        return $this->lineaAjustePrecision;
    }
}
