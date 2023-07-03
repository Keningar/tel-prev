<?php

namespace telconet\financieroBundle\WebService\InfoPagoLineaResponse;

/**
 * Response para el metodo ProcesarPago
 * @author awsamaniego <awsamaniego@telconet.ec>
 * @version 1.0 25-03-2015
 */
class ProcesarPagoResponse
{

    public $retorno;
    public $error;
    public $contrapartida;
    public $nombreCliente;
    public $fechaTransaccion;
    public $secuencialPagoInterno;
    public $secuencialEntidadRecaudadora;
    public $numeroPagos = 1;
    public $valorPago;
    public $tipoProducto;
    public $observacion;
    public $mensajeVoucher;
    public $correo;
    public $saldo;

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

    public function getFechaTransaccion()
    {
        return $this->fechaTransaccion;
    }

    public function setFechaTransaccion($fechaTransaccion)
    {
        $this->fechaTransaccion = $fechaTransaccion;
    }

    public function getSecuencialPagoInterno()
    {
        return $this->secuencialPagoInterno;
    }

    public function setSecuencialPagoInterno($secuencialPagoInterno)
    {
        $this->secuencialPagoInterno = $secuencialPagoInterno;
    }

    public function getSecuencialEntidadRecaudadora()
    {
        return $this->secuencialEntidadRecaudadora;
    }

    public function setSecuencialEntidadRecaudadora($secuencialEntidadRecaudadora)
    {
        $this->secuencialEntidadRecaudadora = $secuencialEntidadRecaudadora;
    }

    public function getNumeroPagos()
    {
        return $this->numeroPagos;
    }

    public function setNumeroPagos($numeroPagos)
    {
        $this->numeroPagos = $numeroPagos;
    }

    public function getValorPago()
    {
        return $this->valorPago;
    }

    public function setValorPago($valorPago)
    {
        $this->valorPago = $valorPago;
    }

    public function getTipoProducto()
    {
        return $this->tipoProducto;
    }

    public function setTipoProducto($tipoProducto)
    {
        $this->tipoProducto = $tipoProducto;
    }

    public function getObservacion()
    {
        return $this->observacion;
    }

    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    }

    public function getMensajeVoucher()
    {
        return $this->mensajeVoucher;
    }

    public function setMensajeVoucher($mensajeVoucher)
    {
        $this->mensajeVoucher = $mensajeVoucher;
    }

    public function getSaldo()
    {
        return $this->saldo;
    }

    public function setSaldo($saldo)
    {
        $this->saldo = $saldo;
    }

    public function getCorreo()
    {
        return $this->correo;
    }

    public function setCorreo($correo)
    {
        $this->correo = $correo;
    }

}