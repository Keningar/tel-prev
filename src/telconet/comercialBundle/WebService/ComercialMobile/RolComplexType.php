<?php

namespace telconet\comercialBundle\WebService\ComercialMobile;

/**
 * ComplexType: Rol con el identificador de personaEmpresaRol
 *
 * @author jlafuente
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.1 03-06-2019 Se añade la oficina de facturación.
 *
 */
class RolComplexType {
    public $personaEmpresaRolId;
    public $rol;
    
    /**
     *
     * @param array $personaEmpresaRol
     */
    public static function fromPersonaEmpresaRol(array $datosPersonaEmpresaRol) {
        $rol = new RolComplexType();
        $rol->personaEmpresaRolId = $datosPersonaEmpresaRol['personaEmpresaRolId'];
        $rol->rol = $datosPersonaEmpresaRol['rol'];
        $rol->oficinaId = $datosPersonaEmpresaRol['idOficinaFacturacion'];
        return $rol;
    }
       
}