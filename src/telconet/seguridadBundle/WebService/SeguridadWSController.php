<?php

namespace telconet\seguridadBundle\WebService;

use Symfony\Component\Security\Acl\Exception\Exception;
use telconet\schemaBundle\DependencyInjection\BaseWSController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use telconet\seguridadBundle\Service\SeguridadService;

/**
 * Clase que sirve para obtener empresas y validar permisos para usar
 * la app movil
 *
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 22/07/2015
 * 
 * @author Edgar Pin Villavicencio <epin@telconet.ec>
 * @version 1.1 27/08/2018 - Se agrega constante de característica de tipo de logueo para la aplicación tm-comercial
 */
class SeguridadWSController extends BaseWSController
{
    /**
     * Código de respuesta: Respuesta valida
     */
    private static $strStatusOK = 200;

    /**
     *
     * @var \telconet\schemaBundle\Service\RestClientService
     */

    const CARACTERISTICA_TIPO_LOGUEO      = 'TIPO DE LOGUEO';
    const CARACTERISTICA_PLANIFICA_ONLINE = 'PLANIFICA ONLINE';
    
    /**
     * Funcion que sirve para procesar las opciones que vienen desde el mobil
     *
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 22/07/2015
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.1 09/09/2018 - Se coloca lógica para el login de la app cliente.
     *
     * @author Karen Rodríguez Véliz <kyrodriguez@telconet.ec>
     * @version 1.2 18/02/2020 - Se agrega opción "resetearClave"
     * 
     * @param $request
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.2 27-08-2018 - Se agrega validación para tm-comercial, se generá token para aplicación
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.3 23/12/2019 - Se agrega lógica en "AUTH_MOBILE" para el consumo del TOKEN.
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.4 12/01/2021 - Se agrega lógica para nueva estructura de arreglo de tokens.
     * 
     */
    public function procesarAction(Request $request)
    {
        $data           = json_decode($request->getContent(),true);
        $response       = null;
        $token          = "";
        $objResponse    = new Response();

        if(isset($data['op']))
        {
            $op  = $data['op'];
            if($data['op']!="" && $data['op']!='AUTH_MOBILE' && $data['op']!='ec.telconet.telcos.mobile.comercial'
                && $data['op']!='getArraySecurityTokens')
            {
                $op = $data['op'];
                if($data['ldap']['source']['name'] == 'ec.telconet.mobile.telcos.clientes')
                {
                    $arrayParametroToken = array('token'        => $data['ldap']['token'],
                                                 'source'       => $data['ldap']['source'],
                                                 'user'         => $data['ldap']['data']['uid']);
                    $token               = $this->generateTokenAppCliente($arrayParametroToken);
                }
                else
                {
                    $token = $this->validateGenerateToken($data['token'], $data['source'], $data['user']);
                }
                if(!$token)
                {
                    return new Response(json_encode(array(
                            'status' => 403,
                            'mensaje' => "token invalido"
                            )
                        )
                    );
                }
            }
        }
        else
        {
            $op = $data['source']['name'];
        }
        if($op)
        {        
            switch($op)
            {
                /********OPCIONES DE GET*************/
                case 'AUTH_MOBILE':
                    $token                  = array();
                    $response               = $this->getResultadoAuthLoginMobile($data);
                    $arrayParametrosSource  = $this->getParametrosSourceTMO();

                    $arraySource = array(
                                            "name" => $arrayParametrosSource["nameSource"],
                                            "originID"=> $arrayParametrosSource["originID"],
                                            "tipoOriginID" => $arrayParametrosSource["tipoOriginID"]
                                        );

                    $response['source'] = $arraySource;

                    if($response['status'] == $this->status['OK'])
                    {   
                        for ($intI = 0; $intI < $arrayParametrosSource["NumberOfSecurityTokens"]; $intI++) 
                        {
                            $arrayTokens[] = $this->generateToken($arraySource, $data['data']['user']);
                        }
                        $token = $arrayTokens;
                    }
                    
                    break;
                case 'ec.telconet.mobile.telcos.tecnico':
                    $perfil   = "MOBILE OPERATIVOS";
                    $response = $this->getAcceso($data, $perfil);
                    break;
                case 'ec.telconet.mobile.telcos.comercial':
                    $perfil   = "MOBILE COMERCIAL";
                    $response = $this->getAcceso($data, $perfil);
                    break;
                case 'ec.telconet.monitoreogps':
                    //no requiere de credenciales
                    $response['status']   = $this->status['OK'];
                    $response['mensaje']  = $this->mensaje['OK'];
                    break;
                case 'getEmpresas':
                    $response = $this->getEmpresas($data);
                    break;
                case 'getAuthAppClientes':
                    $data['ldap']['token'] = $token;
                    $data['ldap']['user']  = $data['ldap']['data']['uid'];
                    $response              = $this->getAuthAppClientes($data);
                    error_log("Usuario: ".$data['ldap']['data']['uid']."    ------   Metodo:  ".$data['op']."    ------   Estado:  ".$response['status']);
                    break;
                case 'ec.telconet.telcos.mobile.comercial':
                    $arraySource = array("name"         => "TELCOS_MOBILE",
                                         "originID"     => "127.0.0.1",
                                         "tipoOriginID" => "IP");
                    
                    $token = $this->generateToken($arraySource, $data['data']['user']);
                    $response = $this->getResultadoAuthLoginMobile($data);
                    break;
                 case 'resetearClave':
                    $response = $this->resetearClave($data);
                    break;
                 /* wdsanchez 20220412 INI*/ 
                 case 'resetearClaveWithoutMail':
                    $response = $this->resetearClaveWithoutMail($data);
                    break;   
                case 'resetearClaveForti':
                    $response = $this->resetearClaveForti($data);
                    break; 
                case 'cambiarClave':
                    $response = $this->cambiarClave($data);
                    break;    
                /* wdsanchez 20220412 FIN*/
                case 'getArraySecurityTokens':
                    $emComercial            = $this->getDoctrine()->getManager("telconet");
                    $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
                    $arrayParametrosSource  = $this->getParametrosSourceTMO();
                    $strMsgError            = "";
                    $arraySource            = array(
                                                    "name" => $arrayParametrosSource["nameSource"],
                                                    "originID"=> $arrayParametrosSource["originID"],
                                                    "tipoOriginID" => $arrayParametrosSource["tipoOriginID"]
                                                );
                    $token                  = array();

                    $strParametros["strTipoRol"] = 'Empleado';
                    $strParametros["strEstado"]  = 'Activo';
                    $strParametros["strLogin"]   = $data['user'];
                    $arrayEmpleados = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getEmpleadosWebService($strParametros);

                    if (empty($arrayEmpleados))
                    {
                        $arrayMsgError = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                        ->getOne('PARAMETROS_GENERALES_MOVIL', 
                                 '', 
                                 '', 
                                 '', 
                                 'MSG_ERROR_GETARRAYSECURITYTOKENS', 
                                 '', 
                                 '', 
                                 ''
                                 );
            
                        if(is_array($arrayMsgError))
                        {
                            $strMsgError  = !empty($arrayMsgError['valor2']) ? 
                                            $arrayMsgError['valor2'] : 
                                            "La persona no existe o no se encuentra Activa";
                        }

                        $response['status']   = $this->status['ERROR_PARCIAL'];
                        $response['mensaje']  = $strMsgError;
                    }
                    else
                    {
                        for ($intI = 0; $intI < $arrayParametrosSource["NumberOfSecurityTokens"]; $intI++) 
                        {
                            $arrayTokens[] = $this->generateToken($arraySource, $data['user']);
                        }
                        $token = $arrayTokens;
                        $response['status']   = $this->status['OK'];
                        $response['mensaje']  = $this->mensaje['OK'];
                    }
                    break;
                /********OPCIONES DE PUT*************/
                default:
                    $response['status']  = $this->status['METODO'];
                    $response['mensaje'] = $this->mensaje['METODO'];
            }
        }
        if(isset($response))
        {
            $response['token'] = $token;
            $objResponse->headers->set('Content-Type', 'text/json');
            $objResponse->setContent(json_encode($response));
        }
        return $objResponse;
    }
    
    /**
     * Funcion que sirve para obtener las empresas asociadas a un usuario
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-07-2015
     * @param array $data
     * @return array $resultado
     * 
     * Validacion de la caducidad de la contrasenia
     * @author  Robinson Salgado
     * @version 1.1 17-05-2018
     * 
     */
    private function getEmpresas($data)
    {
        $mensaje        = "";
        $arrayResult    = array();
        try
        {
            $login = $data['user'];
            
            $objServiceActualizarPassword  = $this->get('admin.ActualizarPassword');            
            $boolRequiereCambioPass        = $objServiceActualizarPassword
                                              ->requiereCambioPassword(array('strLogin' => $login));
                
            if($boolRequiereCambioPass)
            {
                $arrayResponse['status']    = $this->status['CLAVE_EXPIRADA'];
                $arrayResponse['mensaje']   = $this->mensaje['CLAVE_EXPIRADA'];
                return $arrayResponse;
            }
        
            $repoPersonaEmpresaRol = $this->getManager()->getRepository('schemaBundle:InfoPersonaEmpresaRol');
            /* @var $repoPersonaEmpresaRol \telconet\schemaBundle\Repository\InfoPersonaEmpresaRolRepository */
            $arrayEmpresas = $repoPersonaEmpresaRol->getEmpresasByPersona($login, 'Empleado');
            
            if(count($arrayEmpresas)<1)
            {
                $mensaje = "No existen empresas asociadas al usuario!";
                throw new \Exception("ERROR_PARCIAL");
            }
            
            foreach ($arrayEmpresas as $value)
            {
                $arrayResult[] = array  (
                                            'codEmpresa'        => $value['CodEmpresa'],
                                            'nombreEmpresa'     => $value['nombreEmpresa'],
                                            'idOficina'         => $value['IdOficina'],
                                            'nombreOficina'     => $value['nombreOficina'],
                                            'idDepartamento'    => $value['IdDepartamento'],
                                            'nombreDepartamento'=> $value['nombreDepartamento'],
                                            'prefijoEmpresa'    => $value['prefijo']
                                        );
            }
        }
        catch(\Exception $e)
        {
            if($e->getMessage() == "ERROR_PARCIAL")
            {
                $resultado['status']    = $this->status['ERROR_PARCIAL'];
                $resultado['mensaje']   = $mensaje;
            }
            else
            {
                $resultado['status']    = $this->status['ERROR'];
                $resultado['mensaje']   = $this->mensaje['ERROR'];
            }
            
            return $resultado;
        }
        
        $resultado['empresas']  = $arrayResult;
        $resultado['status']    = $this->status['OK'];
        $resultado['mensaje']   = $this->mensaje['OK'];
        return $resultado;
    }
    
    /**
     * Funcion que sirve para verificar si el usuario tiene acceso 
     * a la funcionalidad del mobil.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 23-07-2015
     * @param array $data
     * @param string $perfil
     * @return array $resultado
     * 
     * Validacion de la caducidad de la contrasenia
     * @author  Robinson Salgado
     * @version 1.1 17-05-2018
     * 
     */
    private function getAcceso($data, $perfil)
    {
        $mensaje = "";
        try
        {
            $user           = $data['user'];
            $emSeguridad    = $this->getDoctrine()->getManager("telconet_seguridad");
            $emComercial    = $this->getDoctrine()->getManager("telconet");
            
            $objServiceActualizarPassword  = $this->get('admin.ActualizarPassword');            
            $boolRequiereCambioPass        = $objServiceActualizarPassword
                                              ->requiereCambioPassword(array('strLogin' => $user));
                
            if($boolRequiereCambioPass)
            {
                $arrayResponse['status']    = $this->status['OK'];
                $arrayResponse['mensaje']   = $this->mensaje['CLAVE_EXPIRADA'];
                return $arrayResponse;
            }

            //obtener los datos y departamento de la persona por empresa
            $datosUsuario   = $emComercial->getRepository('schemaBundle:InfoPersona')->getPersonaDepartamentoPorUser($user);

            //verificar si tiene el perfil asignado
            $arrayRespuesta = $emSeguridad->getRepository('schemaBundle:SeguPerfilPersona')
                                          ->getAccesoPorPerfilPersona($perfil, $datosUsuario['ID_PERSONA']);

            if(count($arrayRespuesta)<1)
            {
                $mensaje = "Usted no tiene privilegios para utilizar la aplicación móvil!";
                throw new \Exception("ERROR_PARCIAL");
            }
        }
        catch(\Exception $e)
        {
            if($e->getMessage() == "ERROR_PARCIAL")
            {
                $resultado['status']    = $this->status['ERROR_PARCIAL'];
                $resultado['mensaje']   = $mensaje;
            }
            else
            {
                $resultado['status']    = $this->status['ERROR'];
                $resultado['mensaje']   = $this->mensaje['ERROR'];
            }
            
            return $resultado;
        }
        
        $resultado['status']   = $this->status['OK'];
        $resultado['mensaje']  = $this->mensaje['OK'];
        return $resultado;
    }

    /**
     * Funcion de autenticación creada para el aplicativo mobile
     * Esta funcion se autentica sobre el LDAP y a partir de ello
     * obtiene los datos del usuario
     * (datos como empleado y datos personales)
     *
     * @author  Wilmer Vera
     * @version 1.0 21-12-2017
     * @param $arrayData datos necesarios para el login "user/pass"
     * @return mixed
     * 
     * Validacion de la caducidad de la contrasenia
     * @author  Robinson Salgado
     * @version 1.1 17-05-2018
     * 
     * Corrección de respuesta en el array de Roles
     * @author  Robinson Salgado
     * @version 1.2 25-10-2018
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.2 27-08-2018 - Se agrega funcionalidad de tipo de logue para aplicación tm-comercial
     * 
     * Corrección de respuesta en el array por consulta no valida
     * @author  Wilmer Vera
     * @version 1.3 14-11-2018
     * 
     * Se agrega lógica para obtener los perfiles que se manejará en TMOperaciones.
     * @author  Wilmer Vera
     * @version 1.4, 17-09-2019
     * @since 1.3
     * 
     * 
     * Se agrega validación que se eliminó en pase anterior.
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.5 05-11-2019
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.6 11-11-2019 - Se agrega parámetro para enviar el rol
     * 
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.7 03-03-2020 - Restricción de acceso a TM Comercial mediante perfil
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.8 13-04-2020 - Restricción al app móvil comercial solo a empleados Megadatos
     * 
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.8 02-10-2020 - se toma la SerieLogica (publishId) de la tablet para poder
     * comparar con los dispositivos de la empresa y así poder dar paso al logeo o no.
     *
     */
    private function getResultadoAuthLoginMobile($arrayData)
    {
        $emSeguridad          = $this->getDoctrine()->getManager('telconet_seguridad');
        $serviceUtil          = $this->get('schema.Util');
        $arrayUsuario         = $arrayData['data'];
        $strUserLogin         = $arrayData['data']['user'];
        $strOp                = $arrayData['op']; 
        $strSerieLogica       = $arrayData['data']['publishId'];
        $strImei              = $arrayData['data']['imei'];
        $strCuadrillaId       = "";
        $strIdPersonaRol      = "";
        $objJsonInicioJornada = null;
        $emInfraestructura    = $this->getDoctrine()->getManager("telconet_infraestructura");
        $arrayLdap            = array( "user"    => $strUserLogin,
                                  "password"=> $arrayUsuario['password']);
        $intNumeroElementosSerie = 0;
        $intNumeroElementosImei  = 0;
        try
        {
            $emComercial   = $this->getDoctrine()->getManager();            
            $objSeguridadService  = $this->get('seguridad.Seguridad');
            $objServiceActualizarPassword  = $this->get('admin.ActualizarPassword');

            if($objSeguridadService->authLdapUsuarios($arrayLdap))
            {
                $boolRequiereCambioPass = $objServiceActualizarPassword->requiereCambioPassword(array('strLogin' => $strUserLogin));
                          
                if($boolRequiereCambioPass)
                {
                    $arrayResponse['status']    = $this->status['CLAVE_EXPIRADA'];
                    $arrayResponse['mensaje']   = $this->mensaje['CLAVE_EXPIRADA'];
                    $arrayResponse['success']   = false;                
                    return $arrayResponse;
                }
                $objInfoPersonaService  = $this->get('comercial.InfoPersona');
                $arrayPersonaLoginApp   = $objInfoPersonaService->getInfoUsuarioMobile($strUserLogin);
                $arrayPersonaLogComer   = $arrayPersonaLoginApp;
                

               
                $arrayInfoUsuarioLogin  = $objInfoPersonaService->getInfoUsuarioLogin($strUserLogin);

                if (!$arrayPersonaLoginApp)
                {
                    $arrayPersonaLoginApp   = $objInfoPersonaService->getInfoUsuarioMobile($strUserLogin, 9);
                }
                $arrayPersonaLoginTmp = $objInfoPersonaService->getInfoUsuarioMobile($strUserLogin, 9);
                
                $arrayPersonaLogComer = array_merge($arrayPersonaLogComer, $arrayPersonaLoginTmp);

                if($arrayInfoUsuarioLogin['persona_id'] == null)
                {
                    $arrayResponse['status']      = $this->status['CONSULTA'];
                    $arrayResponse['mensaje']     = "Credenciales incorrectas, por favor verificar usuario y contraseña ingresada.";
                    $arrayResponse['success']     = false;
                    $arrayDataPersona['roles']    = null;
                    $arrayDataPersona['user']     = null;
                    $arrayDataPersona['perfiles'] = null;
                    $arrayDataPersona['datosHal'] = $objJsonInicioJornada;
                    $arrayResponse['data']        = $arrayDataPersona;
                    return $arrayResponse;
                }



                $arrayCriterioBusqueda  = array('estado'    => 'Activo',
                                                'personaId' => $arrayInfoUsuarioLogin['persona_id']);

                $arrayInfoPerfilMovil = $emSeguridad->getRepository('schemaBundle:SeguPerfilPersona')
                    ->getPerfilesTMOperaciones($arrayCriterioBusqueda);            
                
                //Validación de dispositivos solo para TM-Operaciones
                if( $strOp == 'AUTH_MOBILE')
                {
                    foreach ($arrayPersonaLoginApp as $datosPerona ) 
                    {
                        if($datosPerona['cuadrilla_id'] != null)
                        {
                            $strCuadrillaId = $datosPerona['cuadrilla_id'];
                            $strIdPersonaRol = $datosPerona['id_persona_rol'];
                        }
                    }
                
                    $arrayDataWsNw   = array(
                        'id_cuadrilla'   => $strCuadrillaId,
                        'publish_id'     => $strSerieLogica,
                        'imei'           => $strImei,
                        'login'          => $strUserLogin,
                        'id_empleado'    => $strIdPersonaRol
                    );
    
                    $arrayIniciJornadaHal   = $objInfoPersonaService->getInicioJornadaHal($arrayDataWsNw);    
                    $objJsonInicioJornada   = json_decode(json_encode($arrayIniciJornadaHal),true);

                    if( isset($strSerieLogica) && $strSerieLogica != null && !empty($strSerieLogica) )
                    {
                        $objElementoSerieLogica = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                        ->findBy(array("serieLogica" => $strSerieLogica ,
                                                                        "estado"     => 'Activo'));
                        
                        $intNumeroElementosSerie = count($objElementoSerieLogica);
                    }

                    if( isset($strImei) && $strImei != null && !empty($strImei) )
                    {
                        $objElementoImei        = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                        ->findBy(array("nombreElemento" => $strImei , 
                                                                        "estado"        => 'Activo'));
                        
                        $intNumeroElementosImei  = count($objElementoImei);
                    }
                    $intNumeroDeElementos = $intNumeroElementosSerie + $intNumeroElementosImei ;
                    
                    if($objElementoSerieLogica == null && $objElementoImei == null )
                    {
                        $arrayResponse['status']        = $this->status['ERROR_PARCIAL'];
                        $arrayResponse['mensaje']       = $this->mensaje['DISPOSITIVO_NO_ENCONTRADO'];
                        $arrayResponse['success']       = false;
                        $arrayDataPersona['roles']      = null;
                        $arrayDataPersona['user']       = null;
                        $arrayDataPersona['perfiles']   = null;
                        $arrayDataPersona['datosHal']   = null;
                        $arrayResponse['data']          = null;                
                        return $arrayResponse;
                    }
                    else if( ($intNumeroDeElementos == 2  &&  $objElementoSerieLogica !== $objElementoImei) || $intNumeroDeElementos > 2)
                    {
                        $arrayResponse['status']        = $this->status['ERROR_PARCIAL'];
                        $arrayResponse['mensaje']       = $this->mensaje['DISPOSITIVO_INCONSISTENCIA'];
                        $arrayResponse['success']       = false;
                        $arrayDataPersona['roles']      = null;
                        $arrayDataPersona['user']       = null;
                        $arrayDataPersona['perfiles']   = null;
                        $arrayDataPersona['datosHal']   = null;
                        $arrayResponse['data']          = null;               
                        return $arrayResponse;
                    }
                    $arrayDataPersona['datosHal'] = $objJsonInicioJornada;
                }

                if ($strOp == 'ec.telconet.telcos.mobile.comercial')
                {

                    $strPerfil = "MOBILE COMERCIAL";
                    
                    $arrayDatosPersona = $emComercial->getRepository('schemaBundle:InfoPersona')
                        ->getPersonaDepartamentoPorUser($arrayUsuario['user']);

                    $arrayRespuestaPerfil = $emSeguridad->getRepository('schemaBundle:SeguPerfilPersona')
                        ->getAccesoPorPerfilPersona($strPerfil, $arrayDatosPersona['ID_PERSONA']);

                    if(count($arrayRespuestaPerfil) < 1)
                    {
                        $arrayMensaje = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                            ->getOne("MENSAJES_TM_COMERCIAL", "COMERCIAL", "TM_COMERCIAL", 
                                     "RESTRICCION_ACCESO", "", "", "", "", "", "");
                                     
                        
                        throw new \Exception($arrayMensaje['valor1'], 100);
                    }
                    
                    $arrayRoles = array();
                    
                    foreach($arrayPersonaLogComer as $arrayPersona)
                    {
                        if ($arrayPersona["prefijo"] == "MD" || $arrayPersona["prefijo"] == "EN")
                        {
                            array_push($arrayRoles, $arrayPersona);
                        }
                    }
                 
                    if(empty($arrayRoles))
                    {
                        $arrayMensaje = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                            ->getOne("MENSAJES_TM_COMERCIAL", "COMERCIAL", "TM_COMERCIAL", 
                                     "RESTRICCION_NO_EMPLEADO", "", "", "", "", "", "");

                        throw new \Exception($arrayMensaje['valor1'], 100);
                    }  
                    $arrayPersonaLoginApp                      = $arrayRoles;     
                }

                $arrayResponse['status']      = $this->status['OK'];
                $arrayResponse['mensaje']     = "CONSULTA EXITOSA";
                $arrayResponse['success']     = true;
                $arrayDataPersona['roles']    = $arrayPersonaLoginApp;
                $arrayDataPersona['user']     = $arrayInfoUsuarioLogin;
                $arrayDataPersona['perfiles'] = $arrayInfoPerfilMovil;
                
                $arrayResponse['data']        = $arrayDataPersona;

                return $arrayResponse;
            }
            else
            {
                $arrayResponse['status']      = $this->status['CONSULTA'];
                $arrayResponse['mensaje']     = "Credenciales incorrectas, por favor verificar usuario y contraseña ingresada.";
                $arrayResponse['success']     = false;
                $arrayDataPersona['roles']    = null;
                $arrayDataPersona['user']     = null;
                $arrayDataPersona['perfiles'] = null;
                $arrayDataPersona['datosHal'] = $objJsonInicioJornada;
                $arrayResponse['data']        = $arrayDataPersona;
                return $arrayResponse;
            }
          


        }
        catch (\Exception $e)
        {
            if($e->getMessage() == "ERROR_PARCIAL")
            {
                $arrayResponse['status']      = $this->status['ERROR_PARCIAL'];
                $arrayResponse['mensaje']     = $e->getMessage();
                $arrayResponse['success']     = false;
                $arrayDataPersona['roles']    = null;
                $arrayDataPersona['user']     = null;
                $arrayDataPersona['perfiles'] = null;
                $arrayDataPersona['datosHal'] = $objJsonInicioJornada;
                $arrayResponse['data']        = $arrayDataPersona;
            }
            else
            {
                if($e->getCode() == 100)
                {
                    $arrayResponse['mensaje'] = $e->getMessage();
                }
                else 
                {
                    $arrayResponse['mensaje'] = $this->mensaje['ERROR'];
                }
                
                $arrayResponse['status']      = $this->status['ERROR'];
                $arrayResponse['data']        = $arrayDataPersona;
                $arrayResponse['success']     = false;
                $arrayDataPersona['roles']    = null;
                $arrayDataPersona['user']     = null;
                $arrayDataPersona['datosHal'] = $objJsonInicioJornada;
                $arrayDataPersona['perfiles'] = null;
            }
            
            $serviceUtil->insertLog(array('enterpriseCode'   => $strCodEmpresa,
                                          'logType'          => '1',
                                          'logOrigin'        => 'Telcos',
                                          'application'      => basename(__FILE__),
                                          'appClass'         => basename(__CLASS__),
                                          'appMethod'        => basename(__FUNCTION__),
                                          'appAction'        => 'Login',
                                          'messageUser'      => $arrayResponse['mensaje'],
                                          'status'           => 'Fallido',
                                          'descriptionError' => $e->getMessage(),
                                          'inParameters'     => $strOp,
                                          'creationUser'     => $strUserLogin));
        }

        return $arrayResponse;
    }

    /**
     * Función que realiza el llamado a cualquier función del web service para gestionar el Ldap
     *
     * @param type array $arrayParametrosWs array con diferentes estructuras dependiendo de la opción que se invoca en la petición al web service
     *
     * @return string $arrayResultado["status", "msj", "token"]
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.0 17-06-2018
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.1 21-12-2018 - Se envia todos los parámetros al service de Seguridad.
     */
    public function getAuthAppClientes($arrayParametros)
    {
        $arrayResultado = array();
        try
        {
            /* @var $objSeguridadService \telconet\seguridadBundle\Service\SeguridadService */
            $objSeguridadService = $this->get('seguridad.Seguridad');
            $arrayResultado      = $objSeguridadService->callWsLdapAppClientes($arrayParametros);
        }
        catch (\Exception $ex)
        {
            $arrayResultado['status']    = $this->status['ERROR'];
            $arrayResultado['mensaje']   = $this->mensaje['ERROR'];
            $arrayResultado['success']   = false;
        }
        return $arrayResultado;
    }
    
    /**
     * Función que resetea clave sobre LDAP, NMSBackbone, Forti, AAAA, 
     * TACACS según aplique
     *
     * @param type array $arrayParametrosWs array con login
     *
     * @return array $arrayResultado["strClave", "strStatus", "token"]
     *
     * @author Karen Rodríguez Véliz <kyrodriguez@telconet.ec>
     * @version 1.0 17-02-2020
     * 
     * @author Byron Anton. <banton@telconet.ec>
     * @version 1.1 06-01-2022 Se obtiene login si viene nulo
     */
    public function resetearClave($arrayParametros)
    {
        $arrayRespuesta     = array();
        $serviceActualizarPassword = $this->get('admin.ActualizarPassword');
        $arrayEmpleadoNaf   = array();
        if(empty($arrayParametros['data']['strLogin']))
        {
            $emComercial        = $this->getDoctrine()->getManager("telconet");
            $arrayEmpleadoNaf   = $emComercial->getRepository('schemaBundle:InfoPersona')->getEmpleadoNafById($arrayParametros['data']);
            $arrayParametros['data']['strLogin'] = $arrayEmpleadoNaf[0]['LOGIN_EMPLE'];
            $arrayParametros['data']['strPrefijo'] = $arrayEmpleadoNaf[0]['PREFIJO'];
            $arrayParametros['data']['strMailPersonal'] = $arrayEmpleadoNaf[0]['MAIL'];
        }
        $arrayRespuesta = $serviceActualizarPassword->resetearClave($arrayParametros['data']);

        return $arrayRespuesta;
    }



    /** 
     * Función que resetea clave sobre LDAP, NMSBackbone, Forti, AAAA, 
     * TACACS según aplique Phishing
     *
     * @param type array $arrayParametrosWs array con login
     *
     * @return array $arrayResultado["strClave", "strStatus", "token"]
     *
     * @author William Sanchez <wdsanchez@telconet.ec>
     * @version 1.0 04-14-2022
     * 
     */
    public function resetearClaveWithoutMail($arrayParametros)
    {
        $arrayRespuesta     = array();
        $serviceActualizarPassword = $this->get('admin.ActualizarPassword');
        $arrayEmpleadoNaf   = array();
        if(empty($arrayParametros['data']['strLogin']))
        {
            $emComercial        = $this->getDoctrine()->getManager("telconet");
            $arrayEmpleadoNaf   = $emComercial->getRepository('schemaBundle:InfoPersona')->getEmpleadoNafById($arrayParametros['data']);
            $arrayParametros['data']['strLogin'] = $arrayEmpleadoNaf[0]['LOGIN_EMPLE'];
            $arrayParametros['data']['strPrefijo'] = $arrayEmpleadoNaf[0]['PREFIJO'];
            $arrayParametros['data']['strMailPersonal'] = $arrayEmpleadoNaf[0]['MAIL'];
        }
        $arrayRespuesta = $serviceActualizarPassword->resetearClaveWithoutMail($arrayParametros['data']);

        return $arrayRespuesta;
    }



    /** 
     * Función que resetea clave sobre  Forti
     * TACACS según aplique Phishing
     *
     * @param type array $arrayParametrosWs array con login
     *
     * @return array $arrayResultado["strClave", "strStatus", "token"]
     *
     * @author William Sanchez <wdsanchez@telconet.ec>
     * @version 1.0 04-14-2022
     * 
     */
    public function resetearClaveForti($arrayParametros)
    {
        $arrayRespuesta     = array();
        $serviceActualizarPassword = $this->get('admin.ActualizarPassword');
        $arrayEmpleadoNaf   = array();
        if(empty($arrayParametros['data']['strLogin']))
        {
            $emComercial        = $this->getDoctrine()->getManager("telconet");
            $arrayEmpleadoNaf   = $emComercial->getRepository('schemaBundle:InfoPersona')->getEmpleadoNafById($arrayParametros['data']);
            $arrayParametros['data']['strLogin'] = $arrayEmpleadoNaf[0]['LOGIN_EMPLE'];
            $arrayParametros['data']['strPrefijo'] = $arrayEmpleadoNaf[0]['PREFIJO'];
            $arrayParametros['data']['strMailPersonal'] = $arrayEmpleadoNaf[0]['MAIL'];
        }
        $arrayRespuesta = $serviceActualizarPassword->resetearClaveForti($arrayParametros['data']);

        return $arrayRespuesta;
    }


     /** 
     * Función que resetea clave sobre LDAP, NMSBackbone, Forti, AAAA, 
     * TACACS según aplique Phishing
     *
     * @param type array $arrayParametrosWs array con login
     *
     * @return array $arrayResultado["strClave", "strStatus", "token"]
     *
     * @author William Sanchez <wdsanchez@telconet.ec>
     * @version 1.0 04-12-2022
     * 
     */
    public function cambiarClave($arrayParametros)
    {
        $arrayRespuesta     = array();
        $serviceActualizarPassword = $this->get('admin.ActualizarPassword');
        $arrayEmpleadoNaf   = array();
        if(empty($arrayParametros['data']['strLogin']))
        {
            $emComercial        = $this->getDoctrine()->getManager("telconet");
            $arrayEmpleadoNaf   = $emComercial->getRepository('schemaBundle:InfoPersona')->getEmpleadoNafById($arrayParametros['data']);
            $arrayParametros['data']['strLogin'] = $arrayEmpleadoNaf[0]['LOGIN_EMPLE'];
            $arrayParametros['data']['strPrefijo'] = $arrayEmpleadoNaf[0]['PREFIJO'];
            $arrayParametros['data']['strMailPersonal'] = $arrayEmpleadoNaf[0]['MAIL'];
        }
        $arrayRespuesta = $serviceActualizarPassword->actualizarPassword($arrayParametros['data']);

        if ($arrayRespuesta['salida']  == 1)
        {
            $arrayRespuesta['strStatus']    = 'true';
            $arrayRespuesta['strLogin']      = $arrayParametros['data']['strLogin'];
        }else
        {
            $arrayRespuesta['strStatus']    = 'false';
            $arrayRespuesta['strLogin']      = $arrayParametros['data']['strLogin'];
        }
        return $arrayRespuesta;
    }

    /**
     * Función que obtiene los parámetros para armar source y generar token para TM-OPERACIONES
     *
     *
     * @return array $arrayResultado[
     *                                  "nameSource", 
     *                                  "originID", 
     *                                  "tipoOriginID",
     *                                  "NumberOfSecurityTokens"
     *                              ]
     *
     * @author Jean Pierre Nazareno. <jnazareno@telconet.ec>
     * @version 1.0 10-04-2020
     * 
     * Se inicializa variable para el guardado de logs.
     * @author Wilmer Vera. <wvera@telconet.ec>
     * @version 1.1 28-09-2021
     *
     */
    public function getParametrosSourceTMO()
    {
        try
        {
            $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
            $strNameSource          = "";
            $strOriginIdMovil       = "";
            $strTipoOriginId        = "";

            $arrayNameSource = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('PARAMETROS_GENERALES_MOVIL', 
                     '', 
                     '', 
                     '', 
                     'NOMBRE_SOURCE_MOVIL', 
                     '', 
                     '', 
                     ''
                     );

            if(is_array($arrayNameSource))
            {
                $strNameSource     = !empty($arrayNameSource['valor2']) ? $arrayNameSource['valor2'] : "";
            }

            $arrayOriginIdMovil = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('PARAMETROS_GENERALES_MOVIL', 
                     '', 
                     '', 
                     '', 
                     'ORIGIN_ID_MOVIL', 
                     '', 
                     '', 
                     ''
                     );

            if(is_array($arrayOriginIdMovil))
            {
                $strOriginIdMovil     = $arrayOriginIdMovil['valor2'];
            }

            $arrayTipoOriginId = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('PARAMETROS_GENERALES_MOVIL', 
                     '', 
                     '', 
                     '', 
                     'TIPO_ORIGIN_ID', 
                     '', 
                     '', 
                     ''
                     );

            if(is_array($arrayTipoOriginId))
            {
                $strTipoOriginId     = !empty($arrayTipoOriginId['valor2']) ? $arrayTipoOriginId['valor2'] : "";
            }

            $arrayNumberOfSecurityTokens = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('PARAMETROS_GENERALES_MOVIL', 
                     '', 
                     '', 
                     '', 
                     'CANTIDAD_TOKEN_SEGURIDAD', 
                     '', 
                     '', 
                     ''
                     );

            if(is_array($arrayNumberOfSecurityTokens))
            {
                $intNumberOfSecurityTokens     = !empty($arrayNumberOfSecurityTokens['valor2']) ? $arrayNumberOfSecurityTokens['valor2'] : 0;
            }

            $arrayResultado = array(
                                        "nameSource"                => $strNameSource,
                                        "originID"                  => $strOriginIdMovil,
                                        "tipoOriginID"              => $strTipoOriginId,
                                        "NumberOfSecurityTokens"    => intval($intNumberOfSecurityTokens)
                                    );
        }
        catch (\Exception $objException)
        {
            $serviceUtil = $this->get('schema.Util');
            $serviceUtil->insertLog(
                array(
                    'enterpriseCode'   => '10',
                    'logType'          => 1,
                    'logOrigin'        => 'TELCOS',
                    'application'      => __FILE__,
                    'appClass'         => __CLASS__,
                    'appMethod'        => __FUNCTION__,
                    'descriptionError' => $objException->getMessage(),
                    'status'           => 'Fallido',
                    'inParameters'     => '',
                    'creationUser'     => 'Telcos+'
                )
            );

            $arrayResultado = array(
                                        "nameSource"                => "",
                                        "originID"                  => "",
                                        "tipoOriginID"              => "",
                                        "NumberOfSecurityTokens"    => 0
                                    );
        }
        return $arrayResultado;
    }
}
