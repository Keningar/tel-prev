<?php

namespace telconet\comercialBundle\WebService\ComercialMobile;

/**
 * Request: 
 * 
 * @author ltama
 */
class CrearPreClienteRequest {
    public $codEmpresa;
    public $idOficina;
    public $usrCreacion;
    public $persona;
    
    public function __construct($codEmpresa, $idOficina, $usrCreacion, PersonaComplexType $persona)
    {
        $this->codEmpresa = $codEmpresa;
        $this->idOficina = $idOficina;
        $this->usrCreacion = $usrCreacion;
        if ($persona instanceof PersonaComplexType)
        {
            $this->persona = $persona;
        }
        else if (is_array($persona))
        {
            $this->persona = new PersonaComplexType($persona);
        }
    }
    
}