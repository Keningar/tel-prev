<?php

namespace telconet\tecnicoBundle\WebService;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use telconet\schemaBundle\DependencyInjection\BaseWSController;

/**
 * Clase que contiene las funciones necesarias para la autorizacion de servico
 * Fox.
 * 
 * @author Sofia Fernandez <sfernandez@telconet.ec>
 * @version 1.0 25-06-2018
 */
class AuthorizationFoxWSController extends BaseWSController {
    
    /**
     * procesarAction, invoca el service de autorizacion.
     * @param type array $objRequest
     * @return type array $arrayRespuesta
     * @author Sofía Fernández <sfernandez@telconet.ec>          
     * @version 1.0 25-06-2018
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1
     * @since 05-12-2018
     * Se modifica el método de la petición de GET a POST.
     * Se reciben los parámetros desde el cuerpo de la petición en JSON.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.2
     * @since 22-09-2020
     * Se modifican validaciones para los valores no numéricos en el subscriber_id
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 07-08-2021 Se elimina validación del envío del parámetro resource_id en el request debido a que el canal del fútbol
     *                         no usa este valor y se coloca dicha validación dentro del service
     * 
     */
    public function procesarAction(Request $objRequest)
    {
        $arrayParametros = json_decode($objRequest->getContent(),true);

        if(empty($arrayParametros) || (!is_numeric($arrayParametros['subscriber_id'])) || 
            (!isset($arrayParametros['subscriber_id']) || !isset($arrayParametros['country_code'])) || 
            (empty($arrayParametros['subscriber_id']) || empty($arrayParametros['country_code']))) 
        {
            $arrayRespuesta['access'] = false;
            $arrayRespuesta['rating'] = $this->getParameter("fox.authorization.rating_error");
            $arrayRespuesta['ttl']    = $this->getParameter("fox.authorization.ttl_error");
            $arrayStatus["details"]   = 'Existen parámetros vacíos o inválidos';
            $arrayRespuesta['error']  = $arrayStatus;
            return new Response(json_encode($arrayRespuesta));
        }
        /* @var $serviceAuthorization \telconet\tecnicoBundle\Service\AuthorizationFoxService */
        $serviceAuthorization = $this->get('tecnico.AuthorizationFox');
        $arrayRespuestaAuthorization = $serviceAuthorization->autorizacionFox($arrayParametros);
        return new Response(json_encode($arrayRespuestaAuthorization));
    }
}
