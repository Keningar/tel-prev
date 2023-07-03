<?php

namespace telconet\financieroBundle\WebService\InfoPagoLineaResponse;

/**
 * Response para el metodo ConciliarPago
 * @author awsamaniego <awsamaniego@telconet.ec>
 * @version 1.0 25-03-2015
 */
class ConciliarPagoResponse
{

    public $retorno = "";
    public $error;
    public $secuencialRecaudador;
    public $secuencialPagoInterno;
    public $fechaConciliacion;
    public $fechaTransaccionPago;
    public $mensaje;

    public function getRetorno()
    {
        return $this->retorno;
    }

    public function setRetorno($retorno)
    {
        $this->retorno = $retorno;
    }

    public function getMensaje()
    {
        return $this->mensaje;
    }

    public function setMensaje($mensaje)
    {
        $this->mensaje = $mensaje;
    }

    public function getSecuencialRecaudador()
    {
        return $this->secuencialRecaudador;
    }

    public function setSecuencialRecaudador($secuencialRecaudador)
    {
        $this->secuencialRecaudador = $secuencialRecaudador;
    }

    public function getSecuencialPagoInterno()
    {
        return $this->secuencialPagoInterno;
    }

    public function setSecuencialPagoInterno($secuencialPagoInterno)
    {
        $this->secuencialPagoInterno = $secuencialPagoInterno;
    }

    public function getError()
    {
        return $this->error;
    }

    public function setError($error)
    {
        $this->error = $error;
    }

    public function getFechaConciliacion()
    {
        return $this->fechaConciliacion;
    }

    public function setFechaConciliacion($fechaConciliacion)
    {
        $this->fechaConciliacion = $fechaConciliacion;
    }

    public function getFechaTransaccionPago()
    {
        return $this->fechaTransaccionPago;
    }

    public function setFechaTransaccionPago($fechaTransaccionPago)
    {
        $this->fechaTransaccionPago = $fechaTransaccionPago;
    }

}