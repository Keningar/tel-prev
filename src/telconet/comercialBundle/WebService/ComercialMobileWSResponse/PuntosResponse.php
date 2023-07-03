<?php

namespace telconet\comercialBundle\WebService\ComercialMobileWSResponse;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use telconet\comercialBundle\WebService\ComercialMobileWSController;

/**
 * Entidad PuntosResponse
 * @see ComercialMobileWSController
 * @author epin
 */
 
class PuntosResponse
{
    /**
     * @Soap\ComplexType("int")
     */
    public $idPto;

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

   
   public function setId($intId)
    {
        $this->id = $intId;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setPersonaId($intPersonaId)
    {
        $this->personaId = $intPersonaId;
    }
    
    public function getPersonaId()
    {
        return $this->personaId;
    }
    
    public function setFormaContactoId($objFormaContactoId)
    {
        $this->formaContactoId = $objFormaContactoId;
    }
    
    public function getFormaContactoId()
    {
        return $this->formaContactoId;
    }
    
    public function setValor($intValor)
    {
        $this->valor = $intValor;
    }
    
    public function getValor()
    {
        return $this->valor;
    }
    
    public function setEstado($strEstado)
    {
        $this->estado = $strEstado;
    }
    
    public function getEstado()
    {
        return $this->estado;
    }   
}
