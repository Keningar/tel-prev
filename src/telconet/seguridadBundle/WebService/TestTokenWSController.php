<?php

namespace telconet\seguridadBundle\WebService;

use telconet\schemaBundle\DependencyInjection\BaseWSController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * RESTful Web Service Controller para prueba de procesos con tokens de seguridad
 * @author ltama
 */
class TestTokenWSController extends BaseWSController
{

    /**
     * Metodo de ejemplo que simula un login y devuelve respuesta incluyendo un token generado
     * @param Request $request
     * @return Response
     */
    public function testLoginAction(Request $request)
    {
        // simular validacion de usuario y clave para luego generar un token
        $data = json_decode($request->getContent(), true);
        if (!empty($data['username']) && !empty($data['password']) &&
            (($data['username'] == 'SomeUser' && $data['password'] == 'SomePass') ||
            ($data['username'] == 'AnotherUser' && $data['password'] == 'AnotherPass'))
        )
        {
    	    // luego de login exitoso, generar token desde un source especifico
    	    $token = $this->generateToken('SomeApp', $data['username']);
    	    if (!$token)
    	    {
    	        // si la generacion de token devuelve false, devolver response con error segun estructura del metodo
    	        return new Response(json_encode(array(
                    'msg' => 'Error al iniciar sesion',
                    'token' => null
    	        )));
    	    }
	        // devolver response incluyendo token segun estructura del metodo
	        return new Response(json_encode(array(
                'msg' => 'Sesion iniciada exitosamente',
                'token' => $token
	        )));
        }
        else
        {
    	    // si el login falla, devolver response con error segun estructura del metodo
    	    return new Response(json_encode(array(
                'msg' => 'Credenciales incorrectas',
                'token' => null
	        )));
        }
    }

    /**
     * Metodo de ejemplo que valida el token, ejecuta un proceso y devuelve el resultado, sin nuevo token
     * @param Request $request
     * @return Response
     */
    public function testProcessAction(Request $request)
    {
        // obtener data del request como array asociativo
        $data = json_decode($request->getContent(), true);
        // primero validar el token sin generar siguiente token
        $token = $this->validateToken($data['token']);
        if (!$token)
        {
            // si la validacion devuelve false, devolver response con error segun estructura del metodo
            return new Response(json_encode(array(
                'msg' => 'No se pudo elevar el valor al cuadrado',
                'result' => null
            )));
        }
        // una vez validado el token, ejecutar proceso (elevar al cuadrado el value dado)
        $square = pow($data['value'], 2);
        // devolver response segun estructura del metodo
        return new Response(json_encode(array(
            'msg' => 'Valor elevado al cuadrado exitosamente',
            'result' => $square
        )));
    }

    /**
     * Metodo de ejemplo que valida el token, genera un token nuevo, ejecuta un proceso y devuelve el token generado
     * @param Request $request
     * @return Response
     */
    public function testProcessGenerateAction(Request $request)
    {
        // obtener data del request como array asociativo
        $data = json_decode($request->getContent(), true);
        // primero validar el token y generar el siguiente
        $token = $this->validateGenerateToken($data['token'], 'SomeApp', $data['user']);
        if (!$token)
        {
            // si la validacion devuelve false, devolver response con error segun estructura del metodo
            return new Response(json_encode(array(
                'msg' => 'No se pudo elevar el valor al cuadrado',
                'result' => null,
                'token' => null
            )));
        }
        // una vez validado el token, ejecutar proceso (elevar al cuadrado el value dado)
        $square = pow($data['value'], 2);
        // devolver response y nuevo token segun estructura del metodo
        return new Response(json_encode(array(
            'msg' => 'Valor elevado al cuadrado exitosamente',
            'result' => $square,
            'token' => $token
        )));
    }

}
