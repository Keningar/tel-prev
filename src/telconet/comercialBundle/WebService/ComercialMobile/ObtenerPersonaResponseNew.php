<?php

namespace telconet\comercialBundle\WebService\ComercialMobile;

/**
 * Response: Persona encontrada por identificacion
 * 
 * @author ltama
 * 
 * @author: Edgar Pin Villavicencio <epin@telconet.ec>
 * @version: 1.0 Se agrega array para cupos de planificacion mobile
 * 
 * Bug.- Se corrige objeto persona que estaba mal definido
 * 
 * @author Edgar Pin Villavicencio <epin@telconet.ec>
 * @version 1.1 21-01-2019
 * 
 * @author Edgar Pin Villavicencio <epin@telconet.ec>
 * @version 1.2 03-03-2019 - Se permite que el objeto persona reciba valor nulo 
 * 
 * @author Edgar Holguín <epin@telconet.ec>
 * @version 1.3 02-08-2019 - Se elimina campo codFormaContactoSitio.
 */
class ObtenerPersonaResponseNew 
{
    public $persona;
    public $planes;
    //se agrega contrato como informacion de persona
    public $fechaAgenda;
    
    /**
     *
     * @param $arrayParametros = array('objPuntos'
     *                                 'objPlanes'
     *                                 'objContrato'
     *                                 'arrayFechaCupo' 
     *                                 'strCodFormaContactoSitio' 
     * @param PersonaComplexTypeNew $objPersona
     */
    public function __construct($arrayParametros, PersonaComplexTypeNew $objPersona = null) 
    {
        $objPuntos                = $arrayParametros['objPuntos'];
        $objPlanes                = $arrayParametros['objPlanes'];
        $objContrato              = $arrayParametros['objContrato'];
        $arrayFechaCupo           = $arrayParametros['arrayFechaCupo']; 
        
        $objPersona->setPuntos($objPuntos);
        $objPersona->setContrato($objContrato);
        $this->persona = $objPersona;
        $this->planes = $objPlanes;
        //se agrega contrato como informacion de persona
        $this->fechaAgenda = $arrayFechaCupo;
    }
}
