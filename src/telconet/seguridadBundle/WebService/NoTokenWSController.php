<?php

namespace telconet\seguridadBundle\WebService;

use telconet\schemaBundle\DependencyInjection\BaseWSController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use telconet\seguridadBundle\Service\TokenValidatorService;

/**
 * RESTful Web Service Controller para prueba de generacion y validacion tokens de seguridad
 * @author ltama
 */
class NoTokenWSController extends BaseWSController
{

    /**
     * Metodo de ejemplo de generacion de token de seguridad
     * @param array $data
     * @return array
     */
    private function noGeneratePrv(array $data)
    {
        // se valida los campos
        if (!empty($data['source']) && $data['source'] == 'SomeApp' &&
            !empty($data['gateway']) && $data['gateway'] == 'Telcos' &&
            !empty($data['service']) && $data['service'] == 'TestTokenWSController' &&
            !empty($data['method']) && ($data['method'] == 'testLoginAction' || $data['method'] == 'testProcessGenerateAction') &&
            !empty($data['user']) && $data['user'] == 'SomeUser'
        )
        {
            // simular token generado correctamente con codigo status de exito
            $response = array(
                'token' => 'VALID-TOKEN','status' => TokenValidatorService::$TOKEN_OK
            );
        }
        else
        {
            // simular token no generado con codigo status de invalidez
            $response = array(
                'token' => null,'status' => TokenValidatorService::$TOKEN_INVALID
            );
        }
        // retornar respuesta
        return $response;
    }

    /**
     * Metodo de ejemplo de validacion de token de seguridad
     * @param array $data
     * @return array
     */
    private function noValidatePrv(array $data)
    {
        // se valida el token
        switch (empty($data['token']) ? null : $data['token'])
        {
        	case 'VALID-TOKEN':
        	    // simular token valido, se devuelve codigo status de exito
        	    return array('status' => TokenValidatorService::$TOKEN_OK);
        	case 'ERROR-TOKEN':
        	    // simular condicion de error, se comunica con codigo status de error
        	    return array('status' => TokenValidatorService::$TOKEN_ERROR);
        	default:
        	    // simular que cualquier otro token es invalido, se comunica con codigo status de invalidez
        	    return array('status' => TokenValidatorService::$TOKEN_INVALID);
        }
    }

    /**
     * Metodo de ejemplo de generacion de token de seguridad.
     * Contenido del mensaje debe tener formato JSON
     * {"source":"SomeApp", "gateway":"Telcos", "service":"SomeWSController", "method":"someAction"}
     * @param Request $request
     * @return Response
     */
    public function noGenerateAction(Request $request)
    {
        // se obtiene el contenido del request, se lo convierte en JSON
        $data = json_decode($request->getContent(), true);
        // se valida los campos y se genera token
        $response = $this->noGeneratePrv($data);
        // retornar respuesta, con HTTP Status 200 OK por default
        return new Response(json_encode($response));
    }

    /**
     * Metodo de ejemplo de validacion de token de seguridad.
     * Contenido del mensaje debe tenero formato JSON
     * {"token":"THE-TOKEN"}
     * @param Request $request
     * @return Response
     */
    public function noValidateAction(Request $request)
    {
        // se obtiene el contenido del request, se lo convierte en JSON
        $data = json_decode($request->getContent(), true);
        // se valida el token
        $response = $this->noValidatePrv($data);
        // retornar respuesta, con HTTP Status 200 OK por default
        return new Response(json_encode($response));
    }

    /**
     * Metodo de ejemplo de validacion de token de seguridad y generacion de nuevo token de seguridad.
     * Contenido del mensaje debe tenero formato JSON
     * {"token":"THE-TOKEN", "source":"SomeApp", "gateway":"Telcos", "service":"SomeWSController", "method":"someAction"}
     * @param Request $request
     * @return Response
     */
    public function noValidateGenerateAction(Request $request)
    {
        // se obtiene el contenido del request, se lo convierte en JSON
        $data = json_decode($request->getContent(), true);
        // se valida el token
        $response = $this->noValidatePrv($data);
        if ($response['status'] !== TokenValidatorService::$TOKEN_OK)
        {
            // si el token no fue valido, invalidar los campos
            $data = array();
        }
        // se valida los campos y se genera token
        $response = $this->noGeneratePrv($data);
        // retornar respuesta, con HTTP Status 200 OK por default
        return new Response(json_encode($response));
    }

}
