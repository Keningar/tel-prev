<?php

namespace telconet\comercialBundle\WebService\ComercialMobile;

/**
 * Response: Arreglo de Empresas a las que tiene acceso la persona de un login dado
 * 
 * @see telconet\comercialBundle\WebService\ComercialMobileWSController -> obtenerEmpresasAction($login)
 * @author ltama
 */
class ObtenerEmpresasResponse {
    public $arrayEmpresaPersona;
    
    /**
     *
     * @param array $value            
     * @see EmpresaPersonaComplexType
     */
    public function __construct(array $value) {
        $this->arrayEmpresaPersona = $value;
    }
    
    /**
     *
     * @return array
     * @see EmpresaPersonaComplexType
     */
    public function getArrayEmpresaPersona() {
        return $this->arrayEmpresaPersona;
    }
    
    /**
     *
     * @param array $arrayEmpresaPersona            
     * @see EmpresaPersonaComplexType
     */
    public function setArrayEmpresaPersona($arrayEmpresaPersona) {
        $this->arrayEmpresaPersona = $arrayEmpresaPersona;
        return $this;
    }
    
}