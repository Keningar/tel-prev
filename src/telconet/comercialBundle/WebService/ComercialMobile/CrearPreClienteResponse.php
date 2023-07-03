<?php

namespace telconet\comercialBundle\WebService\ComercialMobile;

/**
 * Response: PreCliente creado
 * 
 * @author ltama
 */
class CrearPreClienteResponse 
{
    public $persona;
    public $error;
    
    /**
     *
     * @param PersonaComplexType $persona            
     */
    public function __construct(PersonaComplexType $objPersona, $strError = null) 
    {
        $this->persona = $objPersona;
        $this->error   = $strError;
    }
    
}