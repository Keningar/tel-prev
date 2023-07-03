<?php

namespace telconet\comercialBundle\WebService\ComercialMobile;

/**
 * ComplexType: Forma de contacto de una persona o de un punto
 *
 * @author ltama
 *
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 * @version 1.1 18-06-2020 - Se adiciona la descripción de la forma de contacto
 * 
 * @author Walther Joao Gaibor <wgaibor@telconet.ec>
 * @version 1.2 29-07-2022 - Se adiciona la idPersonaFormaContacto de la forma de contacto
 */
class FormaContactoComplexType {
    public $idFormaContacto;
    public $descripcionFormaContacto;
    public $valor;
    public $idPersonaFormaContacto;
    
    /**
     *
     * @param array $datosPersonaFormaContacto
     */
    public static function fromPersonaFormaContacto(array $datosPersonaFormaContacto) {
        $formaContacto = new FormaContactoComplexType();
        $formaContacto->idFormaContacto          = $datosPersonaFormaContacto['idFormaContacto'];
        $formaContacto->descripcionFormaContacto = $datosPersonaFormaContacto['formaContacto'];
        $formaContacto->valor                    = $datosPersonaFormaContacto['valor'];
        $formaContacto->idPersonaFormaContacto   = $datosPersonaFormaContacto['idPersonaFormaContacto'];
        return $formaContacto;
    }
    
    /**
     *
     * @param array $datosPuntoFormaContacto
     */
    public static function fromPuntoFormaContacto(array $datosPuntoFormaContacto) {
        $formaContacto = new FormaContactoComplexType();
        $formaContacto->idFormaContacto          = $datosPuntoFormaContacto['idFormaContacto'];
        $formaContacto->descripcionFormaContacto = $datosPuntoFormaContacto['formaContacto'];
        $formaContacto->valor                    = $datosPuntoFormaContacto['valor'];
        $formaContacto->idPersonaFormaContacto   = $datosPuntoFormaContacto['idPersonaFormaContacto'];
        return $formaContacto;
    }
    
}