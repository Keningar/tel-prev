<?php

namespace telconet\financieroBundle\WebService\InfoPagoLineaWesternUnionResponse;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use telconet\financieroBundle\WebService\InfoPagoLineaWesternUnionWSController;

/**
 * Formato de la respuesta del Web Service Method ConsultaSaldos para Western Union
 * @see InfoPagoLineaWesternUnionWSController
 * @author ltama
 */
class ConsultaTestResponse {
    /**
     * @Soap\ComplexType("string")
     */
    public $documento = "";
    
    /**
     * Soap\ComplexType("string")
     */
    public $error = "";
    
    /**
     * @Soap\ComplexType("string")
     */
    public $nombreCliente = "";
    
    /**
     * @Soap\ComplexType("string")
     */
    public $numeroContrato = "";
    
    /**
     * Soap\ComplexType("string")
     */
    public $retorno = "";
    
    /**
     * Soap\ComplexType("float")
     */
    public $saldoAdeudado = 0;
    public function getDocumento() {
        return $this->documento;
    }
    public function setDocumento($documento) {
        $this->documento = $documento;
        return $this;
    }
    public function getError() {
        return $this->error;
    }
    public function setError($error) {
        $this->error = $error;
        return $this;
    }
    public function getNombreCliente() {
        return $this->nombreCliente;
    }
    public function setNombreCliente($nombreCliente) {
        $this->nombreCliente = $nombreCliente;
        return $this;
    }
    public function getNumeroContrato() {
        return $this->numeroContrato;
    }
    public function setNumeroContrato($numeroContrato) {
        $this->numeroContrato = $numeroContrato;
        return $this;
    }
    public function getRetorno() {
        return $this->retorno;
    }
    public function setRetorno($retorno) {
        $this->retorno = $retorno;
        return $this;
    }
    public function getSaldoAdeudado() {
        return $this->saldoAdeudado;
    }
    public function setSaldoAdeudado($saldoAdeudado) {
        $this->saldoAdeudado = $saldoAdeudado;
        return $this;
    }
}
