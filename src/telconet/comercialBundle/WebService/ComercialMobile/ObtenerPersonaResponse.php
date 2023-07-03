<?php

namespace telconet\comercialBundle\WebService\ComercialMobile;

/**
 * Response: Persona encontrada por identificacion
 * 
 * @author ltama
 * 
 * @author: Edgar Pin Villavicencio <epin@telconet.ec>
 * @version: 1.1 Se agrega array para cupos de planificacion mobile
 */
class ObtenerPersonaResponse {
    public $persona;
    public $puntos;
    public $planes;
    //se agrega contrato como informacion de persona
    public $contrato;
    public $fechaAgenda; 
    public $codFormaContactoSitio;
    
    /**
     *
     * @param PersonaComplexType $persona
     * @param array $puntos
     * @param array $planes
     * @param array $contrato 
     * @param array $arrayFechaCupo 
     */
    public function __construct(PersonaComplexType $persona = null, $puntos, $planes, $contrato, $arrayFechaCupo, $codFormaContactoSitio) 
    {
        
        $this->persona = $persona;
        $this->puntos = $puntos;
        $this->planes = $planes;
        //se agrega contrato como informacion de persona
        $this->contrato = $contrato;
        $this->fechaAgenda = $arrayFechaCupo; 
        $this->codFormaContactoSitio = $codFormaContactoSitio;
    }
    
}
