<?php

namespace telconet\seguridadBundle\WebService;

use telconet\schemaBundle\DependencyInjection\BaseWSController;
use telconet\seguridadBundle\Service\SeguridadService;
use telconet\seguridadBundle\Service\ExtranetService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


/**
  * RESTful Web Service Controller para las operaciones ejecutadas desde las aplicaciones moviles y la extranet
 *
 * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
 * @version 1.0 10-02-2016
 */
class ExtranetWSController extends BaseWSController {

    /**
     * Funcion que sirve para transaccionar las peticiones del WS RESTful para la extranet y las apps moviles.
     * 
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 28-12-2015
     * 
     * @param Request $requestWS          -- Request tipo Json que se genera en el cliente al momento de realizar la peticion WS
     * 
     * @return Response $responseWS       -- Response tipo Json que retorna la informacion solicitada en la peticion WS
     */
    public function procesarAction(Request $requestWS) 
    {
        $arrayData     = json_decode($requestWS->getContent(), true);
        $arrayResponse = array();
        $strToken      = "";
        $strOp         = $arrayData['op'];

        // *** =========================================== ***
        //            VALIDACION DE TOKEN SECURITY
        // *** =========================================== ***
        if ($strOp != 'getAuthentication' && $strOp != 'registrarDispositivo') 
        {
            $strToken = $this->validateGenerateToken($arrayData['token'], $arrayData['source'], $arrayData['user']);
            if (!$strToken) 
            {
                return new Response(json_encode(array('status'  => 403,
                                                      'mensaje' => "token invalido")));
            }
        } 
        else 
        {
            $strToken = $this->generateToken($arrayData['source'], $arrayData['usuario']);
        }

        // *** =========================================== ***
        //            OPERACIONES DEL WEB SERVICE
        // *** =========================================== ***
        if ($strOp) 
        {
            switch ($strOp) 
            {
                /* ******* OPCIONES DE GET ******** */
                case 'getAuthentication':
                    $arrayResponse = $this->getAuthentication($arrayData);
                    break;
                case 'registrarDispositivo':
                    $arrayResponse = $this->registrarDispositivo($arrayData);
                    break;
                /* ******* OPCIONES DEFAULT ******** */
                default:
                    $arrayResponse['status']  = $this->status['METODO'];
                    $arrayResponse['mensaje'] = $this->mensaje['METODO'];
            }
        }
        
        if (isset($arrayResponse)) 
        {
            $arrayResponse['token'] = $strToken;
        }
        $objResponseWS = new JsonResponse($arrayResponse);        
        return $objResponseWS;
    }
    /**
     * Funcion que sirve para transaccionar las peticiones del WS RESTful para la extranet y las apps moviles.
     *
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 28-12-2015
     */
    private function getAuthentication($arrData) 
    {
        $arrayResponse = array();

        $arrayResponse['status']      = $this->status['ERROR'];
        $arrayResponse['mensaje']     = $this->mensaje['CONSULTA'];
        $arrayResponse['token']       = null;
        $arrayResponse['cambioClave'] = null;
        $arrayResponse['roles']       = null;
        try 
        {
            // Obtener datos de login de usuario Extranet, mediante el servicio SeguridadService
            
            $serviceSeguridad = $this->get('seguridad.Seguridad');
            /* @var $serviceSeguridad SeguridadService */
            $arrayUsuario      = $serviceSeguridad->login($arrData['usuario'], 
                                                           $arrData['clave'], 
                                                           $arrData['empresa'], 
                                                           $arrData['dataTelefono']);


            if ($arrayUsuario['status'] == "OK") 
            {
                // devolver response incluyendo token segun estructura del metodo
                $arrayResponse['cambioClave'] = ($arrayUsuario['cambioClave'] != null) ? $arrayUsuario['cambioClave'] : "N";
                $arrayResponse['roles']       = $arrayUsuario['perfiles'];
                $arrayResponse['status']      = $this->status['OK'];
                $arrayResponse['mensaje']     = $this->mensaje['OK'];
            } 
            else 
            {
                // si el login falla, devolver response con error segun estructura del metodo
                $arrayResponse['status']  = $this->status['ERROR'];
                $arrayResponse['mensaje'] = $arrayUsuario['mensaje'];
            }
        } 
        catch (\Exception $e) 
        {
            // si ocurre algun error dentro del proceso, devolver response con error segun estructura del metodo
            $arrayResponse['status']  = $this->status['ERROR'];
            $arrayResponse['mensaje'] = $e->getMessage();
        }
        return $arrayResponse;
    }

    //#################################################################################################
    //###   METODOS QUE NO TRANSACCIONAN CON TOKEN, SE ENCUENTRAN EXPUESTOS FUERA DE procesarAction ###
    //#################################################################################################

    /**
     * Funcion que sirve para Generar Pin Security,
     * Esta funcionalidad genera un numero de 6 caracteres y lo envia por SMS al numero telefonico
     * 
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 03-12-2015
     * 
     * @param Request $objRequestWS     -- Request tipo Json que se genera en el cliente al momento de realizar la peticion WS
     * 
     * @return Response $responseWS     -- Response tipo Json que retorna la informacion solicitada en la peticion WS
     */
    public function generarPinSecurityAction(Request $objRequestWS) 
    {
        $arrayData     = json_decode($objRequestWS->getContent(), true);
        $arrayResponse = null;
        $strMensaje    = "";

        $serviceSeguridad = $this->get('seguridad.Seguridad');
        /* @var $serviceSeguridad SeguridadService */
        try 
        {
            // Generacion del PIN Security
            $arrayResponseService = $serviceSeguridad->generarPinSecurity($arrayData['usuario'], $arrayData['numero'], $arrayData['codEmpresa']);

            if (isset($arrayResponseService['status']) && $arrayResponseService['status'] == 'ERROR_SERVICE') 
            {
                $strMensaje = $arrayResponseService['mensaje'];
                throw new \Exception('ERROR_PARCIAL');
            }

            $arrayResponse['pin'] = $arrayResponseService['pin'];
        } 
        catch (\Exception $e) 
        {
            if ($e->getMessage() == "ERROR_PARCIAL") 
            {
                $arrayResponse['status']  = $this->status['ERROR_PARCIAL'];
                $arrayResponse['mensaje'] = $strMensaje;
            } 
            else 
            {
                $arrayResponse['status']  = $this->status['ERROR'];
                $arrayResponse['mensaje'] = $this->mensaje['ERROR'];
            }

            $objResponseWS = new Response();
            $objResponseWS->headers->set('Content-Type', 'text/json');
            $objResponseWS->setContent(json_encode($arrayResponse));
            return $objResponseWS;
        }

        $arrayResponse['status']  = $this->status['OK'];
        $arrayResponse['mensaje'] = $this->mensaje['OK'];

        $objResponseWS = new JsonResponse($arrayResponse);        
        return $objResponseWS;
    }

    /**
     * Funcion que sirve para Validar Pin Security
     * 
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 28-12-2015
     * 
     * @param Request $objRequestWS        -- Request tipo Json que se genera en el cliente al momento de realizar la peticion WS
     * 
     * @return Response $responseWS     -- Response tipo Json que retorna la informacion solicitada en la peticion WS
     */
    public function validarPinSecurityAction(Request $objRequestWS) 
    {
        $arrayData     = json_decode($objRequestWS->getContent(), true);
        $arrayResponse = null;
        $strMensaje    = "";

        // *** =========================================== ***
        //            INJECCION DE SERVICES
        // *** =========================================== ***
        $serviceExtranet  = $this->get('seguridad.Extranet');
        /* @var $serviceExtranet ExtranetService */
        
        $serviceSeguridad = $this->get('seguridad.Seguridad');
        /* @var $serviceSeguridad SeguridadService */
        
        try 
        {
            // Validamos el PIN Security
            $arrayResponseService = $serviceSeguridad->validarPinSecurity($arrayData['pin'],
                                                                          $arrayData['usuario'], 
                                                                          $arrayData['codEmpresa']);

            if (isset($arrayResponseService['status']) && $arrayResponseService['status'] == 'ERROR_SERVICE') 
            {
                $strMensaje = $arrayResponseService['mensaje'];
                throw new \Exception('ERROR_PARCIAL');
            } 
            else 
            {
                if (isset($arrayResponseService['idIPERCaracPin'])) 
                {
                    // Procesamos el PIN Security para registrar el dispositivo.
                    $serviceSeguridad->procesarPinSecurity($arrayResponseService['idIPERCaracPin']);
                    
                    // Asociamos el dispositivo con el usuario y la data del dispositivo
                    $arrayResponseService = $serviceExtranet->asociarDispositivo($arrayData['usuario'], 
                                                                               $arrayData['codEmpresa'], 
                                                                               $arrayData['dataTelefono']);
                    
                    if (isset($arrayResponseService['status']) && $arrayResponseService['status'] == 'ERROR_SERVICE') 
                    {
                        $strMensaje = $arrayResponseService['mensaje'];
                        throw new \Exception('ERROR_PARCIAL');
                    }

                    $arrayResponse['status']  = $this->status['OK'];
                    $arrayResponse['mensaje'] = $this->mensaje['OK'];
                }
            }
        } 
        catch (\Exception $e) 
        {
            if ($e->getMessage() == "ERROR_PARCIAL") 
            {
                $arrayResponse['status']  = $this->status['ERROR_PARCIAL'];
                $arrayResponse['mensaje'] = $strMensaje;
            } else {
                $arrayResponse['status']  = $this->status['ERROR'];
                $arrayResponse['mensaje'] = $this->mensaje['ERROR'];
            }
        }

        $objResponseWS = new JsonResponse($arrayResponse);
        return $objResponseWS;
    }

    /**
     * Funcion que sirve para verificar si el dispositivo movil se encuentra registrado.
     * 
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 30-12-2015
     * 
     * @param Request $objRequestWS     -- Request tipo Json que se genera en el cliente al momento de realizar la peticion WS
     * 
     * @return Response $responseWS     -- Response tipo Json que retorna la informacion solicitada en la peticion WS
     */
    public function verificarDispositivoAction(Request $objRequestWS)
    {
        $arrayData     = json_decode($objRequestWS->getContent(), true);
        $arrayResponse = null;
        $strMensaje    = "";
        // *** =========================================== ***
        //            INJECCION DE SERVICES
        // *** =========================================== ***
        $serviceExtranet = $this->get('seguridad.Extranet');
        /* @var $serviceExtranet ExtranetService */
        
        try
        {
            // Asociamos el dispositivo con el usuario y la data del dispositivo
            $arrayResponseService = $serviceExtranet->verificarDispositivo($arrayData['dataTelefono']);
            
            if (isset($arrayResponseService['status']) && $arrayResponseService['status'] == 'ERROR_SERVICE') 
            {
                $strMensaje = $arrayResponseService['mensaje'];
                throw new \Exception('ERROR_PARCIAL');
            }
            
            $arrayResponse['status']      = $this->status['OK'];
            $arrayResponse['mensaje']     = $this->mensaje['OK'];
            $arrayResponse['dispositivo'] = $arrayResponseService['dispositivo'];
        } 
        catch(\Exception $ex)
        {
            if ($ex->getMessage() == "ERROR_PARCIAL") 
            {
                $arrayResponse['status']  = $this->status['ERROR_PARCIAL'];
                $arrayResponse['mensaje'] = $strMensaje;
            } 
            else 
            {
                $arrayResponse['status']  = $this->status['ERROR'];
                $arrayResponse['mensaje'] = $this->mensaje['ERROR'];
            }
        }
        $objResponseWS = new JsonResponse($arrayResponse);
        return $objResponseWS;
    }

}
