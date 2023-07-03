<?php

namespace telconet\financieroBundle\WebService\InfoPagoLineaWesternUnionResponse;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use telconet\financieroBundle\WebService\InfoPagoLineaWesternUnionWSController;

/**
 * Formato de la respuesta del Web Service Method ProcesaPago para Western Union
 * @see InfoPagoLineaWesternUnionWSController
 * @author ltama
 */
class ProcesaPagoResponse {
    /**
     * @Soap\ComplexType("string")
     */
    public $error = "";
    
    /**
     * @Soap\ComplexType("string")
     */
    public $observacion = "";
    
    /**
     * @Soap\ComplexType("string")
     */
    public $retorno = "";
    
    /**
     * @Soap\ComplexType("float")
     */
    public $saldo = 0;
    public function getError() {
        return $this->error;
    }
    public function setError($error) {
        $this->error = $error;
        return $this;
    }
    public function getObservacion() {
        return $this->observacion;
    }
    public function setObservacion($observacion) {
        $this->observacion = $observacion;
        return $this;
    }
    public function getRetorno() {
        return $this->retorno;
    }
    public function setRetorno($retorno) {
        $this->retorno = $retorno;
        return $this;
    }
    public function getSaldo() {
        return $this->saldo;
    }
    public function setSaldo($saldo) {
        $this->saldo = $saldo;
        return $this;
    }
}
