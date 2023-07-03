<?php

namespace telconet\schemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * telconet\schemaBundle\Entity\InfoAdendum
 *
 * @ORM\Table(name="INFO_ADENDUM")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="telconet\schemaBundle\Repository\InfoAdendumRepository")
 */
class InfoAdendum
{


/**
* @var integer $id
*
* @ORM\Column(name="ID_ADENDUM", type="integer", nullable=false)
* @ORM\Id
* @ORM\GeneratedValue(strategy="SEQUENCE")
* @ORM\SequenceGenerator(sequenceName="SEQ_INFO_ADENDUM", allocationSize=1, initialValue=1)
*/

private $id;

/**
* @var datetime $feCreacion
*
* @ORM\Column(name="FE_CREACION", type="datetime", nullable=false)
*/		
     		
private $feCreacion;


/**
* @var string $numero
*
* @ORM\Column(name="NUMERO", type="string", nullable=false)
*/

private $numero;

/**
* @var integer $contratoId
*
* @ORM\Column(name="CONTRATO_ID", type="integer", nullable=true)
*/		
     		
private $contratoId;


/**
* @var string $tipo
*
* @ORM\Column(name="TIPO", type="string", nullable=false)
*/

private $tipo;


/**
* @var integer $puntoId
*
* @ORM\Column(name="PUNTO_ID", type="integer", nullable=true)
*/		
     		
private $puntoId;


/**
* @var integer $servicioId
*
* @ORM\Column(name="SERVICIO_ID", type="integer", nullable=true)
*/		
     		
private $servicioId;

/**
* @var string $ipCreacion
*
* @ORM\Column(name="IP_CREACION", type="string", nullable=false)
*/

private $ipCreacion;

/**
* @var string $usrCreacion
*
* @ORM\Column(name="USR_CREACION", type="string", nullable=false)
*/

private $usrCreacion;

/**
* @var string $estado
*
* @ORM\Column(name="ESTADO", type="string", nullable=false)
*/

private $estado;


/**
* @var datetime $feModifica
*
* @ORM\Column(name="FE_MODIFICA", type="datetime", nullable=false)
*/		
     		
private $feModifica;


/**
* @var string $usrModifica
*
* @ORM\Column(name="USR_MODIFICA", type="string", nullable=false)
*/

private $usrModifica;

/**
* @var integer $formaPagoId
*
* @ORM\Column(name="FORMA_PAGO_ID", type="integer", nullable=false)
*/

private $formaPagoId;

/**
* @var integer $tipoCuentaId
*
* @ORM\Column(name="TIPO_CUENTA_ID", type="integer", nullable=false)
*/

private $tipoCuentaId;

/**
* @var integer $bancoTipoCuentaId
*
* @ORM\Column(name="BANCO_TIPO_CUENTA_ID", type="integer", nullable=false)
*/

private $bancoTipoCuentaId;

/**
* @var string $numeroCtaTarjeta
*
* @ORM\Column(name="NUMERO_CTA_TARJETA", type="string", nullable=false)
*/

private $numeroCtaTarjeta;

/**
* @var string $titularCuenta
*
* @ORM\Column(name="TITULAR_CUENTA", type="string", nullable=false)
*/

private $titularCuenta;

/**
* @var string $mesVencimiento
*
* @ORM\Column(name="MES_VENCIMIENTO", type="string", nullable=false)
*/

private $mesVencimiento;

/**
* @var string $anioVencimiento
*
* @ORM\Column(name="ANIO_VENCIMIENTO", type="string", nullable=false)
*/

private $anioVencimiento;

/**
* @var string $codigoVerificacion
*
* @ORM\Column(name="CODIGO_VERIFICACION", type="string", nullable=false)
*/

private $codigoVerificacion;

/**
* @var string $formaContrato
*
* @ORM\Column(name="FORMA_CONTRATO", type="string", nullable=true)
*/

private $formaContrato;

/**
* @var string $origen
*
* @ORM\Column(name="ORIGEN", type="string", nullable=true)
*/

private $origen;

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
* Get numero
*
* @return string
*/

public function getNumero()
{
    return $this->numero; 
}

/**
* Set numero
*
* @param string $strNumero
*/
public function setNumero($strNumero)
{
    $this->numero = $strNumero;
}

/**
* Get contratoId
*
* @return integer
*/		
     		
public function getContratoId(){
	return $this->contratoId; 
}

/**
* Set contratoId
*
* @param integer $intContratoId
*/
public function setContratoId($intContratoId)
{
        $this->contratoId = $intContratoId;
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
* Set tipo
*
* @param string $strTipo
*/
public function setTipo($strTipo)
{
    $this->tipo = $strTipo;
}

/**
* Get puntoId
*
* @return integer
*/		
     		
public function getPuntoId(){
	return $this->puntoId; 
}

/**
* Set puntoId
*
* @param integer $intPuntoId
*/
public function setPuntoId($intPuntoId)
{
        $this->puntoId = $intPuntoId;
}

/**
* Get servicioId
*
* @return integer
*/		
     		
public function getServicioId(){
	return $this->servicioId; 
}

/**
* Set servicioId
*
* @param integer $intServicioId
*/
public function setServicioId($intServicioId)
{
        $this->servicioId = $intServicioId;
}


/**
* Get ipCreacion
*
* @return string
*/

public function getIpCreacion()
{
    return $this->ipCreacion; 
}

/**
* Set ipCreacion
*
* @param string $strIpCreacion
*/
public function setIpCreacion($strIpCreacion)
{
    $this->ipCreacion = $strIpCreacion;
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
* @param string $strUsrCreacion
*/
public function setUsrCreacion($strUsrCreacion)
{
    $this->usrCreacion = $strUsrCreacion;
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
* @param string $strEstado
*/
public function setEstado($strEstado)
{
    $this->estado = $strEstado;
}


/**
* Get feModifica
*
* @return datetime
*/		
     		
public function getFeModifica(){
	return $this->feModifica; 
}

/**
* Set feModifica
*
* @param datetime $feModifica
*/
public function setFeModifica($feModifica)
{
        $this->feModifica = $feModifica;
}

/**
* Get usrModifica
*
* @return string
*/

public function getUsrModifica()
{
    return $this->usrModifica; 
}

/**
* Set usrModifica
*
* @param string $strUsrModifica
*/
public function setUsrModifica($strUsrModifica)
{
    $this->usrModifica = $strUsrModifica;
}


    /**
     * Get the value of formaPagoId
     */ 
    public function getFormaPagoId()
    {
        return $this->formaPagoId;
    }

    /**
     * Set the value of formaPagoId
     *
     * @return  self
     */ 
    public function setFormaPagoId($formaPagoId)
    {
        $this->formaPagoId = $formaPagoId;
    }

    /**
     * Get the value of tipoCuentaId
     */ 
    public function getTipoCuentaId()
    {
        return $this->tipoCuentaId;
    }

    /**
     * Set the value of tipoCuentaId
     *
     * @return  self
     */ 
    public function setTipoCuentaId($tipoCuentaId)
    {
        $this->tipoCuentaId = $tipoCuentaId;

        return $this;
    }

    /**
     * Get the value of bancoTipoCuentaId
     */ 
    public function getBancoTipoCuentaId()
    {
        return $this->bancoTipoCuentaId;
    }

    /**
     * Set the value of bancoTipoCuentaId
     *
     * @return  self
     */ 
    public function setBancoTipoCuentaId($bancoTipoCuentaId)
    {
        $this->bancoTipoCuentaId = $bancoTipoCuentaId;

        return $this;
    }

    /**
     * Get the value of numeroCtaTarjeta
     */ 
    public function getNumeroCtaTarjeta()
    {
        return $this->numeroCtaTarjeta;
    }

    /**
     * Set the value of numeroCtaTarjeta
     *
     * @return  self
     */ 
    public function setNumeroCtaTarjeta($numeroCtaTarjeta)
    {
        $this->numeroCtaTarjeta = $numeroCtaTarjeta;

        return $this;
    }

    /**
     * Get the value of titularCuenta
     */ 
    public function getTitularCuenta()
    {
        return $this->titularCuenta;
    }

    /**
     * Set the value of titularCuenta
     *
     * @return  self
     */ 
    public function setTitularCuenta($titularCuenta)
    {
        $this->titularCuenta = $titularCuenta;

        return $this;
    }

    /**
     * Get the value of mesVencimiento
     */ 
    public function getMesVencimiento()
    {
        return $this->mesVencimiento;
    }

    /**
     * Set the value of mesVencimiento
     *
     * @return  self
     */ 
    public function setMesVencimiento($mesVencimiento)
    {
        $this->mesVencimiento = $mesVencimiento;

        return $this;
    }

    /**
     * Get the value of anioVencimiento
     */ 
    public function getAnioVencimiento()
    {
        return $this->anioVencimiento;
    }

    /**
     * Set the value of anioVencimiento
     *
     * @return  self
     */ 
    public function setAnioVencimiento($anioVencimiento)
    {
        $this->anioVencimiento = $anioVencimiento;

        return $this;
    }

    /**
     * Get the value of codigoVerificacion
     */ 
    public function getCodigoVerificacion()
    {
        return $this->codigoVerificacion;
    }

    /**
     * Set the value of codigoVerificacion
     *
     * @return  self
     */ 
    public function setCodigoVerificacion($codigoVerificacion)
    {
        $this->codigoVerificacion = $codigoVerificacion;

        return $this;
    }

    /**
     * Get the value of formaContrato
     */ 
    public function getFormaContrato()
    {
        return $this->formaContrato;
    }

    /**
     * Set the value of formaContrato
     *
     * @return  self
     */ 
    public function setFormaContrato($formaContrato)
    {
        $this->formaContrato = $formaContrato;

        return $this;
    }

    /**
     * Get the value of origen
     */ 
    public function getOrigen()
    {
        return $this->origen;
    }

    /**
     * Set the value of origen
     *
     * @return  self
     */ 
    public function setOrigen($origen)
    {
        $this->origen = $origen;

        return $this;
    }
}
