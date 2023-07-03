<?php

namespace telconet\comercialBundle\WebService\ComercialMobile;

/**
 * Response: Punto creado
 * 
 * @author ltama
 */
class CrearPuntoResponse {
    public $error;
    
    /**
     *
     * @param $punto            
     */
    public function __construct($error) {
        $this->error = $error;
    }
    
}