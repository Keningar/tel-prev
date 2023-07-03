<?php

namespace telconet\financieroBundle\WebService\InfoPagoLineaResponse;

/**
 * Response para el metodo consultarSaldo
 * @author awsamaniego <awsamaniego@telconet.ec>
 * @version 1.0 25-03-2015
 */
class ConsultarSaldoResponse
{

    public $retorno = "";
    public $error;
    public $contrapartida = "";
    public $nombreCliente;
    public $saldoAdeudado = 0;
    public $numeroCobros = 1;
    public $tipoProducto;
    public $numeroContrato;
    public $valorRetener = 0;
    public $baseImponible = 0;
    public $periodoRecaudacion;
    public $secuencialPagoInterno;
    public $identificacionCliente;
    public $formaPago;

    public function getRetorno()
    {
        return $this->retorno;
    }

    public function setRetorno($retorno)
    {
        $this->retorno = $retorno;
    }

    public function getError()
    {
        return $this->error;
    }

    public function setError($error)
    {
        $this->error = $error;
    }

    public function getContrapartida()
    {
        return $this->contrapartida;
    }

    public function setContrapartida($contrapartida)
    {
        $this->contrapartida = $contrapartida;
    }

    public function getNombreCliente()
    {
        return $this->nombreCliente;
    }

    public function setNombreCliente($nombreCliente)
    {
        $this->nombreCliente = $nombreCliente;
    }

    public function getSaldoAdeudado()
    {
        return $this->saldoAdeudado;
    }

    public function setSaldoAdeudado($saldoAdeudado)
    {
        $this->saldoAdeudado = $saldoAdeudado;
    }

    public function getNumeroCobros()
    {
        return $this->numeroCobros;
    }

    public function setNumeroCobros($numeroCobros)
    {
        $this->numeroCobros = $numeroCobros;
    }

    public function getTipoProducto()
    {
        return $this->tipoProducto;
    }

    public function setTipoProducto($tipoProducto)
    {
        $this->tipoProducto = $tipoProducto;
    }

    public function getNumeroContrato()
    {
        return $this->numeroContrato;
    }

    public function setNumeroContrato($numeroContrato)
    {
        $this->numeroContrato = $numeroContrato;
    }

    public function getValorRetener()
    {
        return $this->valorRetener;
    }

    public function setValorRetener($valorRetener)
    {
        $this->retorno = $valorRetener;
    }

    public function getBaseImponible()
    {
        return $this->baseImponible;
    }

    public function setBaseImponible($baseImponible)
    {
        $this->baseImponible = $baseImponible;
    }

    public function getPeriodoRecaudacion()
    {
        return $this->periodoRecaudacion;
    }

    public function setPeriodoRecaudacion($periodoRecaudacion)
    {
        $this->periodoRecaudacion = $periodoRecaudacion;
    }

    public function getSecuencialPagoInterno()
    {
        return $this->secuencialPagoInterno;
    }

    public function setSecuencialPagoInterno($secuencialPagoInterno)
    {
        $this->secuencialPagoInterno = $secuencialPagoInterno;
    }

    public function getIdentificacionCliente()
    {
        return $this->identificacionCliente;
    }

    public function setIdentificacionCliente($identificacionCliente)
    {
        $this->identificacionCliente = $identificacionCliente;
    }

    public function getFormaPago()
    {
        return $this->formaPago;
    }

    public function setFormaPago($strFormaPago)
    {
        $this->formaPago = $strFormaPago;
    }

}