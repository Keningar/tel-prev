<?php

namespace telconet\comercialBundle\WebService\ComercialMobile;

/**
 * Response: PreCliente creado
 * 
 * @author ltama
 */
class CrearPreClienteResponseNew 
{
    public $persona;
    public $error;
    
    /**
     *
     * @param PersonaComplexTypeNew $persona            
     */
    public function __construct(PersonaComplexTypeNew $objPersona, $strError = null) 
    {
        $this->persona = $objPersona;
        $this->error   = $strError;
    }
    
}
