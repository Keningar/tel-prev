<?php

namespace telconet\financieroBundle\WebService\InfoPagoLineaResponse;

/**
 * Response para el metodo AnularPago
 * 
 * @author awsamaniego <awsamaniego@telconet.ec>
 * @version 1.0 19-10-2015
 */
class AnularPagoResponse
{

    public $retorno;
    public $error;
    public $contrapartida;
    public $fechaTransaccion;
    public $secuencialRecaudador;
    public $secuencialPagoInterno;
    public $identificacionCliente;
    public $nombreCliente;
    public $mensaje;

    public function getRetorno()
    {
        return $this->retorno;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getContrapartida()
    {
        return $this->contrapartida;
    }

    public function getFechaTransaccion()
    {
        return $this->fechaTransaccion;
    }

    public function getIdentificacionCliente()
    {
        return $this->identificacionCliente;
    }

    public function getMensaje()
    {
        return $this->mensaje;
    }

    public function setRetorno($retorno)
    {
        $this->retorno = $retorno;
    }

    public function setError($error)
    {
        $this->error = $error;
    }

    public function setContrapartida($contrapartida)
    {
        $this->contrapartida = $contrapartida;
    }

    public function setFechaTransaccion($fechaTransaccion)
    {
        $this->fechaTransaccion = $fechaTransaccion;
    }

    public function setSecuencialRecaudador($secuencialRecaudador)
    {
        $this->secuencialRecaudador = $secuencialRecaudador;
    }

    public function setSecuencialEmpresa($secuencialEmpresa)
    {
        $this->secuencialEmpresa = $secuencialEmpresa;
    }

    public function setIdentificacionCliente($identificacionCliente)
    {
        $this->identificacionCliente = $identificacionCliente;
    }

    public function setMensaje($mensaje)
    {
        $this->mensaje = $mensaje;
    }

    public function getSecuencialPagoInterno()
    {
        return $this->secuencialPagoInterno;
    }

    public function setSecuencialPagoInterno($secuencialPagoInterno)
    {
        $this->secuencialPagoInterno = $secuencialPagoInterno;
    }

    public function getNombreCliente()
    {
        return $this->nombreCliente;
    }

    public function setNombreCliente($nombreCliente)
    {
        $this->nombreCliente = $nombreCliente;
    }

}
