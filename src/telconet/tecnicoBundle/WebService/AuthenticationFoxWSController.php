<?php

namespace telconet\tecnicoBundle\WebService;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use telconet\schemaBundle\DependencyInjection\BaseWSController;

/**
 * Clase que contiene las funciones necesarias para la autenticacion de usuario
 * y contrasena de FOX
 * 
 * @author Sofia Fernandez <sfernandez@telconet.ec>
 * @version 1.0 22-06-2018
 */
class AuthenticationFoxWSController extends BaseWSController {

    /**
     * procesarAction, invoca el metodo.
     * @param type array $objRequest
     * @return type array $arrayRespuesta
     * @author Sofía Fernández <sfernandez@telconet.ec>          
     * @version 1.0 22-06-2018
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1
     * @since 05-12-2018
     * Se modifica el método de la petición de GET a POST.
     * Se reciben los parámetros desde el cuerpo de la petición en JSON.
     * Se modifican los valores "true", "false" a booleanos.
     * Se realiza la autenticación desde el telcos.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.2
     * @since 11-09-2020
     * Se agrega parametro producto para determinar al servicio que debe de apuntar, fox premium, paramount o noggin.
     * Se modifica mensaje de error cuando el producto no se encuentra ingresado correctamente.
     * 
     */
    public function procesarAction(Request $objRequest) 
    {
        $arrayParametros = json_decode($objRequest->getContent(),true);

        if(empty($arrayParametros) || 
        (!isset($arrayParametros['username']) || !isset($arrayParametros['password']) || !isset($arrayParametros['producto'])) || 
        (empty($arrayParametros['username']) || empty($arrayParametros['password']) || empty($arrayParametros['producto'])))
        {
            $arrayError['errorCode'] = 1;
            $arrayError['details'] = "Usuario, contraseña y/o producto inválidos.";
            $arrayRespuesta['access'] = false;
            $arrayRespuesta['error'] = $arrayError;
            return new Response(json_encode($arrayRespuesta));
        }

        return new Response($this->get('tecnico.FoxPremium')->autenticacionFox($arrayParametros));
    }

}
