<?php

namespace telconet\comercialBundle\WebService\ComercialMobileWSResponse;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use telconet\comercialBundle\WebService\ComercialMobileWSController;

/**
 * Entidad PersonaResponse
 * @see ComercialMobileWSController
 * @author wsanchez
 */
 
class FormaContactoResponse {
    /**
     * @Soap\ComplexType("int")
     */
    public $id;

    /**
     * @Soap\ComplexType("telconet\comercialBundle\WebService\ComercialMobileWSResponse\PersonaResponse")
     */
    public $personaId;

    /**
     * @Soap\ComplexType("int")
     */
    private $formaContactoId;
    
    /**
     * @Soap\ComplexType("string")
     */
    private $valor;
    
    /**
     * @Soap\ComplexType("string")
     */
    private $estado;

   
   public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setPersonaId($personaId)
    {
        $this->personaId = $personaId;
    }
    
    public function getPersonaId()
    {
        return $this->personaId;
    }
    
    public function setFormaContactoId($formaContactoId)
    {
        $this->formaContactoId = $formaContactoId;
    }
    
    public function getFormaContactoId()
    {
        return $this->formaContactoId;
    }
    
    public function setValor($valor)
    {
        $this->valor = $valor;
    }
    
    public function getValor()
    {
        return $this->valor;
    }
    
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
    
    public function getEstado()
    {
        return $this->estado;
    }
   
}