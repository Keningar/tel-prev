<?php

namespace telconet\comercialBundle\WebService;

use Exception;
use telconet\schemaBundle\DependencyInjection\BaseWSController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Finder\Finder;
use telconet\schemaBundle\Service\UtilService;
use telconet\comercialBundle\WebService\ComercialMobileWSResponse\PersonaResponse;
use telconet\comercialBundle\WebService\ComercialMobile\PersonaComplexType;
use telconet\comercialBundle\WebService\ComercialMobile\ObtenerPersonaResponse;
use telconet\comercialBundle\WebService\ComercialMobile\ObtenerDatosClienteResponse;
use telconet\comercialBundle\Service\InfoContratoDigitalService;

use telconet\comercialBundle\WebService\ComercialMobileWSResponse\FormaContactoResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use telconet\comercialBundle\WebService\ComercialMobile\EmpresaPersonaComplexType;
use telconet\comercialBundle\WebService\ComercialMobile\ObtenerEmpresasResponse;
use telconet\comercialBundle\Service\ComercialService;
use telconet\schemaBundle\Entity\AdmiTipoMedio;
use telconet\schemaBundle\Entity\AdmiProducto;
use telconet\schemaBundle\Entity\InfoPlanCab;
use telconet\comercialBundle\WebService\ComercialMobile\FormaContactoComplexType;
use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\comercialBundle\WebService\ComercialMobile\CrearPreClienteResponse;
use telconet\comercialBundle\WebService\ComercialMobile\CrearPuntoResponse;

use telconet\seguridadBundle\Service\SeguridadService;
use telconet\comercialBundle\Service\InfoContratoAprobService;
use telconet\comercialBundle\Service\InfoServicioService;
use telconet\tecnicoBundle\Service\InfoElementoService;

/**
 * Clase que contiene las funciones necesarias para el funcionamiento del
 * Mobil Comercial. Se convierte el WS en rest para eliminar el GatewayComercial.
 * 
 * @author Edgar Pin Villavicencio <epin@telconet.ec>
 * @version 1.0 25-09-2018
 */
class ComercialMobileWSAppsController extends BaseWSController
{
    /**
     * Función que sirve para procesar las opciones que vienen desde el mobil comercial
     * 
     * @param $objRequest
     */
    public function procesarAction(Request $objRequest)
    {
        $arrayData      = json_decode($objRequest->getContent(),true);
        $strToken       = "";
        $objResponse    = new Response();
        $strOp          = $arrayData['op'];
        if($arrayData['source'])
        {
            
            $strToken = $this->validateGenerateToken($arrayData['token'], $arrayData['source'], $arrayData['user']);           
            if(!$strToken)
            {
                return new Response(json_encode(array(
                        'status' => 403,
                        'mensaje' => "token invalido"
                        )
                    )
                );
            }
            
        }
        if($strOp)
        {
            switch($strOp)
            {
                case 'notificar':
                    $arrayResponse = $this->notificar($arrayData['data']);
                    break;
                case 'cargarImagen':
                    $arrayResponse = $this->cargarImagen($arrayData['data']);
                    break;
                case 'crearContrato':
                    $arrayResponse = $this->crearContrato($arrayData['data']);
                    break;
                case 'crearPreCliente':
                    $arrayResponse = $this->crearPreCliente($arrayData['data']);
                    break;
                case 'crearPunto':
                    $arrayResponse = $this->crearPunto($arrayData['data']);
                    break;
                case 'crearServicio':
                    $arrayResponse = $this->crearServicio($arrayData['data']);
                    break;
                case 'generarLoginPunto':
                    $arrayResponse = $this->generarLoginPunto($arrayData['data']);
                    break;
                case 'obtenerCatalogos':
                    $arrayResponse = $this->obtenerCatalogos($arrayData['data']);
                    break;
                case 'obtenerCatalogosEmpresa':
                    $arrayResponse = $this->obtenerCatalogosEmpresa($arrayData['data']);
                    break;
                case 'obtenerEmpresas':
                    $arrayResponse = $this->obtenerEmpresas($arrayData['data']);
                    break;
                case 'obtenerPersona':
                    $arrayResponse = $this->obtenerPersona($arrayData['data']);
                    break;
                case 'obtenerPlanes':
                    $arrayResponse = $this->obtenerPlanes($arrayData['data']);
                    break;
                case 'planesPosibles':
                    $arrayResponse = $this->obtenerPlanesPosibles($arrayData['data']);
                    break;
                case 'caracteristicasProducto':
                    $arrayResponse = $this->obtenerCaracteristicasProducto($arrayData['data']);
                    break;
                case 'obtenerProductos':
                    $arrayResponse = $this->obtenerProductos($arrayData['data']);
                    break;
                case 'obtenerPuntosCliente':
                    $arrayResponse = $this->obtenerPuntosCliente($arrayData['data']);
                    break;
                case 'solicitarFactibilidadServicio':
                    $arrayResponse = $this->solicitarFactibilidadServicio($arrayData['data']);
                    break;
                case 'generarPinSecurity':
                    $arrayData["data"]["user"] = $arrayData['user'];
                    $arrayResponse = $this->generarPinSecurity($arrayData['data']);
                    break;
                case 'autorizarContratoDigital':
                    $arrayResponse = $this->autorizarContratoDigital($arrayData);
                    break;
                case 'obtenerDatosGeneral':
                    $arrayResponse = $this->obtenerDatosGeneral($arrayData['data']);
                    break;
                case 'planificarOnLine':
                    $arrayResponse = $this->planificarOnLine($arrayData['data']);
                    break;
                case 'putCotizacion':
                    $arrayResponse = $this->putCotizacion($arrayData);
                    break;
                case 'getNumeroMovilPorPunto':
                    $arrayResponse = $this->getNumeroMovilPorPunto($arrayData);
                    break;
                case 'setNumeroMovilEstadoWhatsapp':
                    $arrayResponse = $this->setNumeroMovilEstadoWhatsapp($arrayData);
                    break;
                case 'obtenerNumerosMovilPorPunto':
                    $arrayResponse = $this->obtenerNumerosMovilPorPunto($arrayData['data']);
                    break;
                case 'obtenerNumerosTecnicoPorPuntoTN':
                    $arrayResponse = $this->obtenerNumerosTecnicoPorPuntoTN($arrayData['data']);
                    break;
                case 'obtenerPuntosPorNumeroMovil':
                    $arrayResponse = $this->obtenerPuntosPorNumeroMovil($arrayData['data']);
                    break;
                case 'obtenerInformacionCliente':
                    $arrayResponse = $this->obtenerInformacionCliente($arrayData);
                    break;
                case 'obtenerFormasContacto':
                    $arrayResponse = $this->obtenerFormasContacto($arrayData);
                    break;
                case 'actualizarFormaContacto';
                    $arrayResponse = $this->actualizarFormaContacto($arrayData);
                    break;
                default:
                    $arrayResponse['status']  = $this->status['METODO'];
                    $arrayResponse['mensaje'] = $this->mensaje['METODO'];
            }
        }
        $arrayResponseFinal = null;
        if(isset($arrayResponse))
        {
            $arrayResponseFinal = $arrayResponse;
            $arrayResponseFinal['token'] = $strToken;            
            $objResponse = new Response();
            $objResponse->headers->set('Content-Type', 'text/json');
            $objResponse->setContent(json_encode($arrayResponseFinal));
        }
        if(empty($strOp))
        {
            $arrayResponse['status']  = "ERROR";
            $arrayResponse['mensaje'] = "Debe definirse la opcion";
            
            $arrayResponseFinal = $arrayResponse;
            $arrayResponseFinal['token'] = $strToken;            
            $objResponse = new Response();
            $objResponse->headers->set('Content-Type', 'text/json');
            $objResponse->setContent(json_encode($arrayResponseFinal));
        }
        return $objResponse;
    }
        
    /**
     * Obtiene los datos generarles de los clientes en una clase
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     */
    private function obtenerDatosPersonaPrv($strCodEmpresa, $strIdentificacionCliente, $strPrefijoEmpresa)
    {
        /* @var $serviceCliente \telconet\comercialBundle\Service\ClienteService */
        $serviceCliente = $this->get('comercial.Cliente');
        
        $datos_persona = $serviceCliente->obtenerDatosClientePorIdentificacion($strCodEmpresa, $strIdentificacionCliente, $strPrefijoEmpresa);
        if (!is_null($datos_persona))
        {
            $datos_persona = new PersonaComplexType($datos_persona);
            $datosFormasContacto = $serviceCliente->obtenerFormasContactoPorPersona($datos_persona->id, null, null, null, true);
            $datos_persona->setFormasContacto($datosFormasContacto['registros']);
        }
        return $datos_persona;
    }
    
    /**
     * Obtiene los datos de los planes aplicables
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     */
    private function obtenerPlanesPrv($codEmpresa, $idTipoNegocio, $idFormaPago, $idTipoCuenta, $idBancoTipoCuenta)
    {
        /* @var $serviceInfoServicio \telconet\comercialBundle\Service\InfoServicioService */
        $serviceInfoServicio = $this->get('comercial.InfoServicio');
        $planes = $serviceInfoServicio->obtenerPlanesAplicables($codEmpresa, $idTipoNegocio, $idFormaPago, $idTipoCuenta, $idBancoTipoCuenta);
        $array = array();
	    for ($i = 0; $i < count($planes); $i++)
	    {
            $planInformacionDetalles = $serviceInfoServicio->obtenerPlanInformacionDetalles($planes[$i]['idPlan'], true, true, 'k', 'v', 'c','t');
	        if (!empty($planInformacionDetalles))
	        {
                $array[] = array (
                            'k' => $planes[$i]['idPlan'],
                            'v' => $planes[$i]['nombrePlan'],
                            'p' => $planInformacionDetalles['precio'],
                            //'d' => $planInformacionDetalles['descuento'],
                            //'o' => $planInformacionDetalles['tipoOrden'],
                            'l' => $planInformacionDetalles['listado'],
                );
	        }
	    }
        return $array;
    }
    
    /**
     * Método que crea el contrato para un cliente
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     * 
     * Se Modifica el origen del contrato para que sea MOVIL
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1 07-01-2019
     * 
     * @author Edgar Pin Villavicencion <epin@telconet.ec>
     * @version 1.2 22-07-2019 - Bug .- Se valida que exista el servicio y que este en estado Factible para poder guardar el contrato
     *
     * Se modifica la lógica para realizar generación y envío de pin por separado.
     * @author Juan Romero Aguilar <jromero@telconet.ec>
     * @version 1.3 03/12/2019 
     */
    
    public function crearContrato($arrayData)
    {
        ini_set('max_execution_time', 16000000);
        $strClientIp = '127.0.0.1';
        $emComercial = $this->getDoctrine()->getManager('telconet');
        /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
        $serviceInfoContrato      = $this->get('comercial.InfoContrato');
        /* @var $serviceUtil \telconet\schemaBundle\Service\UtilService */
        $serviceUtil              = $this->get('schema.Util');
        /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoAprobService */
        $serviceInfoContratoAprob = $this->get('comercial.InfoContratoAprob');
        $booleanRechazaCd         = false;
        $objResult                = null;
        try
        {
            //Valido que el contrato tenga un servicio factible
            $entityServicio = $emComercial->getRepository('schemaBundle:InfoServicio')
                                     ->findOneby(array("id"     => $arrayData['servicioId'],
                                                       "estado" => "Factible"));
            if (!is_object($entityServicio))
            {
                $arrayResponse['respuesta'] = array('error' => "Error al guardar contrato - no se encuentra un servicio Factible");
                $serviceUtil->insertError(
                          'Telcos+', 
                          'ComercialMobileWSController->crearContrato', 
                          $arrayResponse['respuesta'],
                          $arrayData['usrCreacion'],
                          $strClientIp
                         );

                return $arrayResponse;
            }
                
            if ($arrayData['contrato']['feFinContratoPost'])
            {
                $arrayData['contrato']['feFinContratoPost'] = \DateTime::createFromFormat('d/m/Y H:i:s', 
                                                                                     $arrayData['contrato']['feFinContratoPost'] . ' 00:00:00');
            }
            //se agrega generacion de archivos temporales para subida de archivos de contratos
            $arrayTipoDocumentos    = array (); 
            $arrayDatosFormFiles       = array (); 
            foreach($arrayData['contrato']['files'] as $arrayFile):
                $strTipo                  = $arrayFile['tipoDocumentoGeneralId'];
                $arrayTipoDocumentos[]    = $strTipo;
                $objArchivo               = $this->writeAndGetFile($arrayFile['file']);
                $arrayDatosFormFiles[]    = $objArchivo; 
            endforeach;
            // Se configuran los parametros iniciales con los que iniciara el contrato
            // Dado que es necesario ingresar un pin para autorizar el contrato este no
            // debe iniciar con un estado PENDINTE sino con uno anterior
            $arrayData['contrato']['arrayTipoDocumentos']    = $arrayTipoDocumentos;
            $arrayData['contrato']['valorEstado']            = 'PorAutorizar'; // Estado Inicial Contrato
            $arrayData['contrato']['datos_form_files']       = array('imagenes',
                                                                $arrayDatosFormFiles);
            // Al crear el contrato se debe indicar el origen del mismo
            $arrayParametrosContrato                   = array();
            $arrayParametrosContrato['codEmpresa']     = $arrayData['codEmpresa'];
            $arrayParametrosContrato['prefijoEmpresa'] = $arrayData['prefijoEmpresa']; 
            $arrayParametrosContrato['idOficina']      = $arrayData['idOficina']; 
            $arrayParametrosContrato['usrCreacion']    = $arrayData['usrCreacion']; 
            $arrayParametrosContrato['clientIp']       = $strClientIp;
            $arrayParametrosContrato['datos_form']     = $arrayData['contrato']; 
            $arrayParametrosContrato['check']          = null; 
            $arrayParametrosContrato['clausula']       = null;
            $arrayParametrosContrato['origen']         = 'MOVIL';
            
            $objResult     = $serviceInfoContrato->crearContrato($arrayParametrosContrato);
            $strMensaje    = "";
            $strMensajeSms = "Pin No enviado";

            if(is_object($objResult))
            {

                // Se solicita la generacion de un certificado para poder firmar los documentos
                $serviceContratoDigital = $this->get('comercial.InfoContratoDigital');
                $arrayCrearCd           = $serviceContratoDigital->crearCertificado($objResult);
                //Se consulta si fue creado el certificado
                switch($arrayCrearCd['salida'])
                {
                    case '1':
                        // Si el certificado fue generado correctamente entonces procedemos a documentarlo
                        // Se envian todos los documentos de soporte a la Entidad emisora del certificado
                        $arrayDocumentarCd  = $serviceContratoDigital->documentarCertificado($objResult,$arrayData);
                        $strMensaje         = $arrayCrearCd['mensaje'];
                        //Consulta si fue documentado el certificado
                        switch($arrayDocumentarCd['salida'])
                        {
                            case '1':
                                $strMensaje .= " (".$arrayDocumentarCd['mensaje'].")";
                                $arrayResponse['respuesta']    = array( 
                                                                       'idContrato'     => $objResult->getId(), 
                                                                       'numeroContrato' => $objResult->getNumeroContrato(),
                                                                       'estadoContrato' => $objResult->getEstado(), 
                                                                       'pin'            => $arrayResponseService['pin'],
                                                                       'mensaje'        => $strMensaje,
                                                                       'mensajeSms'     => $strMensajeSms
                                                                       );

                                // Generamos el PIN con el cual se confirmara el contrato
                                /* @var $serviceSeguridad SeguridadService */
                                $serviceSeguridad     = $this->get('seguridad.Seguridad');
                                $strMensajeError = "ANTES ENVIO SMS:. Empresa: " . $arrayData['codEmpresa'] . " Telefono: " . $arrayData['contrato']['numeroTelefonico'] . 
                                                   " identificación: " . $arrayData['contrato']['documentoIdentificacion'] . 
                                                   " idContrato =>" .$objResult->getId();
                                $serviceUtil->insertError(
                                                          'Telcos+', 
                                                          'ComercialMobileWSController->crearContrato', 
                                                          $strMensajeError,
                                                          $arrayData['usrCreacion'],
                                                          $strClientIp
                                                         );
                                //Se realiza la generación del pin
                                $arrayGeneracionPin                  = array ();
                                $arrayGeneracionPin['strUsername']   = $arrayData['usrCreacion'];
                                $arrayGeneracionPin['strCodEmpresa'] = $arrayData['codEmpresa'];
                                $arrayResponseService                = $serviceSeguridad->generarPinSecurity($arrayGeneracionPin);
                                
                                if (isset($arrayResponseService['status']) && $arrayResponseService['status'] == 'ERROR_SERVICE') 
                                {
                                    $strMensajeError = "ERROR ENVIO SMS: Empresa: "        . $arrayData['codEmpresa'] . 
                                                                       " Telefono: "       . $arrayData['contrato']['numeroTelefonico'] .
                                                                       $arrayResponseService['mensaje'] . 
                                                                       " identificación: " . $arrayData['contrato']['documentoIdentificacion'] .
                                                                       " idContrato => "   . $objResult->getId();
                                    $serviceUtil->insertError(
                                                              'Telcos+', 
                                                              'ComercialMobileWSController->crearContrato', 
                                                              $strMensajeError,
                                                              $arrayData['usrCreacion'],
                                                              $strClientIp
                                                             );  
                                }
                                else if (isset($arrayResponseService['pin']))
                                {
                                    $arrayResponse['response']['pin']    = $arrayResponseService['pin'];
                                    //Preparo la data para el envío del pin
                                    $arrayDataEnvio                      = array();
                                    $arrayDataEnvio['strPin']            = $arrayResponseService['pin'];
                                    $arrayDataEnvio['strNumeroTlf']      = $arrayData['contrato']['numeroTelefonico'];
                                    $arrayDataEnvio['strIdentificacion'] = $arrayData['contrato']['documentoIdentificacion'];
                                    $arrayDataEnvio['strPersonaId']      = $arrayData['contrato']['idcliente'];
                                    $arrayDataEnvio['strUsername']       = $arrayData['usrCreacion'];
                                    $arrayDataEnvio['strCodEmpresa']     = $arrayData['codEmpresa'];
                                    //Realizo el envío del pin
                                    $arrayResponseEnvioPin   = $serviceSeguridad->enviarPinSecurity($arrayDataEnvio);
                                    if (isset($arrayResponseEnvioPin['mensaje']))
                                    {
                                        $strMensajeSms = $arrayResponseEnvioPin['mensaje'];
                                    }
                                }
                                
                                break;
                            default:
                                $strMensaje      .= $arrayDocumentarCd['mensaje']?$arrayDocumentarCd['mensaje']:", Error al documentar certificado";
                                $booleanRechazaCd = true;
                                break;
                        }
                        break;
                    default:
                        $strMensaje       = $arrayCrearCd['mensaje']?$arrayCrearCd['mensaje']:"Error al crear certificado";
                        $booleanRechazaCd = true;
                        break;
                }
            }
            else
            {
                $arrayResponse['respuesta'] = array('error' => "Error cuando se intento crear el contrato");
            }
            //Se consulta si se debe rechazar contrato o no
            if($booleanRechazaCd)
            {
                $serviceUtil->insertError(
                                          'Telcos+', 
                                          'ComercialMobileWSController->crearContrato', 
                                          $strMensaje,
                                          $arrayData['usrCreacion'],
                                          $strClientIp
                                         );
                $arrayParametrosRechazo['strUsrCreacion'] = $arrayData['usrCreacion'];
                $arrayParametrosRechazo['strIpCreacion']  = $strClientIp;
                $arrayParametrosRechazo['strMotivo']      = 'Contrato en Estado Rechazado';
                $arrayParametrosRechazo['objContrato']    = $objResult;
                //Se rechaza automticamente el contrato porque el proceso no se completo
                $serviceInfoContratoAprob->rechazarContratoPorError($arrayParametrosRechazo);
                $arrayResponse['respuesta'] = array('error' => $strMensaje);
            }
        }
        catch (\Exception $e)
        {
            $arrayResponse['respuesta'] = array('error' => $e->getMessage());
            $serviceUtil->insertError(
                                      'Telcos+', 
                                      'ComercialMobileWSController->crearContrato', 
                                      $e->getMessage(),
                                      $arrayData['usrCreacion'],
                                      $strClientIp
                                     );
            if(is_object($objResult))
            {
                $arrayParametrosRechazo['strUsrCreacion'] = $arrayData['usrCreacion'];
                $arrayParametrosRechazo['strIpCreacion']  = $strClientIp;
                $arrayParametrosRechazo['strMotivo']      = 'Contrato en Estado Rechazado';
                $arrayParametrosRechazo['objContrato']    = $objResult;
                //Se rechaza automticamente el contrato porque el proceso no se completo
                $serviceInfoContratoAprob->rechazarContratoPorError($arrayParametrosRechazo);
            }
        }
        return $arrayResponse;
    }

    /**
     * Método que realiza la creación de un pre-cliente
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.1 11-09-2019 Se modifica para que función de service reciba array de parámetros.
     */
    
    public function crearPreCliente($arrayData)
    {
        $strClientIp = '127.0.0.1';
        /* @var $arrayDatosPersona PersonaComplexType */
        $arrayDatosPersona = $this->obtenerDatosPersonaPrv($arrayData['codEmpresa'], $arrayData['persona']['identificacionCliente'], $arrayData['prefijoEmpresa']);
        if (!is_null($arrayDatosPersona) && (in_array('Pre-cliente', $arrayDatosPersona->roles) || in_array('Cliente', $arrayDatosPersona->roles)) )
        {
            // si la persona existe como pre-cliente, devolver error
            $arrayResponse['respuesta'] = new CrearPreClienteResponse($arrayDatosPersona, 'Persona ya existe como Pre-Cliente o Cliente');
            return $arrayResponse;
        }
        $persona = new InfoPersona();
        if ($arrayData['persona']['fechaNacimiento'])
        {
            $arrayData['persona']['fechaNacimiento'] = \DateTime::createFromFormat('d/m/Y H:i:s', $arrayData['persona']['fechaNacimiento'] . ' 00:00:00');
        }
        $arrayData['persona']['yaexiste'] = (empty($arrayData['persona']['id']) ? 'N' : 'S');
        /* @var $servicePreCliente \telconet\comercialBundle\Service\PreClienteService */
        $servicePreCliente = $this->get('comercial.PreCliente');
        
        $arrayParametrosPreCliente =   array('strCodEmpresa'        => $arrayData['codEmpresa'],
                                             'intOficinaId'         => $arrayData['idOficina'],
                                             'strUsrCreacion'       => $arrayData['usrCreacion'],
                                             'strClientIp'          => $strClientIp,
                                             'arrayDatosForm'       => $arrayData['persona'],
                                             'strPrefijoEmpresa'    => $arrayData['prefijoEmpresa'],
                                             'arrayFormasContacto'  => $arrayData['persona']['formasContacto']);

        $servicePreCliente->crearPreCliente($persona,$arrayParametrosPreCliente);
        
        $arrayDatosPersona = $this->obtenerDatosPersonaPrv($arrayData['codEmpresa'], $persona->getIdentificacionCliente(), $arrayData['prefijoEmpresa']);
        
        

                
                
        $arrayResponse['respuesta'] = new CrearPreClienteResponse($arrayDatosPersona);
        return $arrayResponse;
    }
    
    /**
     * Método que realiza la creación de un punto.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.1 11-09-2019 Se modifica para que función de service reciba array de parámetros.
     */
    
    public function crearPunto($arrayData)
    {
        $strClientIp = '127.0.0.1';
        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceInfoPunto = $this->get('comercial.InfoPunto');
        try
        {
            $arrayData['punto']['file'] = (empty($arrayData['punto']['file']) ? null : $this->writeAndGetFile($arrayData['punto']['file']));
            $arrayData['punto']['fileDigital'] = (empty($arrayData['punto']['fileDigital']) ? null : $this->writeAndGetFile($arrayData['punto']['fileDigital']));
            
            //se agrega codigo para validar informacion y poder crear el punto de manera correcta
            if ($arrayData['punto']['nombrePunto'])
            {
                $arrayData['punto']['nombrepunto'] = $arrayData['punto']['nombrePunto'];
            }
            if ($arrayData['punto']['descripcionPunto'])
            {
                $arrayData['punto']['descripcionpunto'] = $arrayData['punto']['descripcionPunto'];
            }
            if ($arrayData['punto']['dependeDeEdificio'])
            {
                $arrayData['punto']['dependedeedificio'] = $arrayData['punto']['dependeDeEdificio'];
            }
            if ($arrayData['punto']['puntoEdificioId'])
            {
                $arrayData['punto']['puntoedificioid'] = $arrayData['punto']['puntoEdificioId'];
            }
            if ($arrayData['punto']['esEdificio'])
            {
                $arrayData['punto']['esedificio'] = $arrayData['punto']['esEdificio'];
            }
            if ($arrayData['punto']['idCanalVenta'])
            {
                $arrayData['punto']['canal'] = $arrayData['punto']['idCanalVenta'];
            }
            if ($arrayData['punto']['idPtoVenta'])
            {
                $arrayData['punto']['punto_venta'] = $arrayData['punto']['idPtoVenta'];
            }
            if ($arrayData['idOficina'])
            {
                $arrayData['punto']['oficina'] = $arrayData['idOficina'];
            }
            if ($arrayData['punto']['origenWeb'])
            {
                $arrayData['punto']['origen_web'] = $arrayData['punto']['origenWeb'];
            }
            
            $arrayParametrosPunto =  array('strCodEmpresa'        => $arrayData['codEmpresa'],
                                           'strUsrCreacion'       => $arrayData['usrCreacion'],
                                           'strClientIp'          => $strClientIp,
                                           'arrayDatosForm'       => $arrayData['punto'],
                                           'arrayFormasContacto'  => $arrayData['punto']['formasContacto']);

            $serviceInfoPunto->crearPunto($arrayParametrosPunto);
            
            $arrayPersonaResponse = $this->obtenerPersona($arrayData);
            $arrayPersonaResponse = $arrayPersonaResponse['respuesta']; 
            $arrayResponse['respuesta'] = array('error' => null, 'personaResponse' => $arrayPersonaResponse);
        }
        catch (\Exception $e)
        {
            $arrayResponse['respuesta'] = array('error' => $e->getMessage());
        }
        return $arrayResponse;
    }

    /**
     * Método que realiza la creación de un servicio
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     */
    public function crearServicio($arrayData)
    {
        $strClientIp = '127.0.0.1';
        /* @var $serviceInfoServicio \telconet\comercialBundle\Service\InfoServicioService */
        $serviceInfoServicio = $this->get('comercial.InfoServicio');
        try
        {
            $arrayParamsServicio = array(   "codEmpresa"        => $arrayData['codEmpresa'],
                                            "idOficina"         => $arrayData['idOficina'],
                                            "entityPunto"       => $arrayData['idPto'],
                                            "entityRol"         => null,
                                            "usrCreacion"       => $arrayData['usrCreacion'],
                                            "clientIp"          => $strClientIp,
                                            "tipoOrden"         => $arrayData['tipoOrden'],
                                            "ultimaMillaId"     => $arrayData['ultimaMillaId'],
                                            "servicios"         => $arrayData['servicios'],
                                            "strPrefijoEmpresa" => $arrayData['prefijoEmpresa'],
                                            "session"           => null
                                    );
            $serviceInfoServicio->crearServicio($arrayParamsServicio);
            $arrayPersonaResponse = $this->obtenerPersona($arrayData);
            $arrayPersonaResponse = $arrayPersonaResponse['respuesta'];
            $arrayResponse['respuesta'] = array('status' => 'OK','mensaje' => 'OK','error' => null, 'personaResponse' => $arrayPersonaResponse);
        }
        catch (\Exception $e)
        {
            $arrayResponse['respuesta'] = array('error' => $e->getMessage());
        }
        return $arrayResponse;
    }
    
    /**
     * Método que genera el login de un punto
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     */    
    public function generarLoginPunto($arrayData)
    {
        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceInfoPunto = $this->get('comercial.InfoPunto');
        $strLogin = $serviceInfoPunto->generarLogin($arrayData['codEmpresa'], $arrayData['idCanton'], $arrayData['idPersona'], 
                                                    (empty($arrayData['idTipoNegocio']) ? null : $arrayData['idTipoNegocio']));
        $arrayResponse['respuesta'] = array('login' => $strLogin);
        return $arrayResponse;
    }

    /**
     * Método obtiene las empresas para acceso desde mobile
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     * 
     * Se agrega rol de Personal externo para que pueda acceder a las empresas asignadas al usuario
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 2.0 29-11-2018
     * 
     */    
    public function obtenerEmpresas($arrayData)
    {
        $strLogin = $arrayData['login'];
        
        $objPersonaEmpresaRol = $this->getManager()->getRepository('schemaBundle:InfoPersonaEmpresaRol');
        /* @var $objPersonaEmpresaRol \telconet\schemaBundle\Repository\InfoPersonaEmpresaRolRepository */
        $arrayEmpresas = $objPersonaEmpresaRol->getEmpresasByPersona($strLogin, 'Empleado');
        if (count($arrayEmpresas) == 0)
        {
            $arrayEmpresas = $objPersonaEmpresaRol->getEmpresasByPersona($strLogin, 'Personal Externo');            
        }
        
        $arrayResult = array();
        foreach ($arrayEmpresas as $arrayValue)
        {
            $arrayResult[] = new EmpresaPersonaComplexType($arrayValue);
        }
        $arrayRespuesta["respuesta"] = new ObtenerEmpresasResponse($arrayResult);
        return $arrayRespuesta;
    }
    
    /**
     * Método que obtiene los datos asociados a una persona
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     */
    public function obtenerPersona($arrayData) 
    {
        $arrayDatosPersona         = $this->obtenerDatosPersonaPrv($arrayData['codEmpresa'], $arrayData['identificacionCliente'], 
                                                                   $arrayData['prefijoEmpresa']);
        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceInfoPunto          = $this->get('comercial.InfoPunto');
        $arrayEstadoContrato       = null;
        $arrayEstadoRol            = array();
        $arrayPuntos               = array();
        $arrayPlanes               = array();
        //se agregan variables contrato
        $objContrato               = null;
        $arrayContrato             = null;
        $strRol                    = null;
        $boolHayServicio           = false;
        $intJurisdiccionId         = 0;
        $boolCoordinada            = false;
        $objFechaPlanificada       = "";        
        try
        {
            if ($arrayDatosPersona) 
            {
                $strRol = "Cliente";
                $arrayPuntos = $serviceInfoPunto->obtenerDatosPuntosCliente($arrayData['codEmpresa'], $arrayDatosPersona->id, $strRol,
                                                                            true, true, true);
                if(!$arrayPuntos)
                {
                    $strRol = "Pre-cliente";
                    $arrayPuntos = $serviceInfoPunto->obtenerDatosPuntosCliente($arrayData['codEmpresa'], $arrayDatosPersona->id, $strRol, 
                                                                                true, true, true);
                }

                foreach ($arrayPuntos as $arrayPunto) 
                {
                    $strKey = $arrayPunto['tipoNegocioId'] . '|' . $arrayDatosPersona->formaPagoId . 
                                                             '|' . $arrayDatosPersona->tipoCuentaId . 
                                                             '|' . $arrayDatosPersona->bancoTipoCuentaId;
                    if (!isset($arrayPlanes[$strKey])) 
                    {
                        $arrayPlanes[$strKey] = $this->obtenerPlanesPrv($arrayData['codEmpresa'], $arrayPunto['tipoNegocioId'], 
                                                                        $arrayDatosPersona->formaPagoId, $arrayDatosPersona->tipoCuentaId, 
                                                                        $arrayDatosPersona->bancoTipoCuentaId);
                    }
                    if (isset($arrayPunto['servicios']))
                    {
                        foreach ($arrayPunto['servicios'] as $arrayServicio)
                        {
                            $objSolicitudPrePlanificacion = $this->getDoctrine()
                                    ->getManager("telconet")
                                    ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                    ->findOneBy(array("servicioId" => $arrayServicio['id'],
                                                      "tipoSolicitudId" => "8"));
                            if ($objSolicitudPrePlanificacion != null) 
                            {
                                $intJurisdiccionId = $arrayPunto['ptoCoberturaId'];
                                $boolHayServicio = true;
                                if (strtoupper($objSolicitudPrePlanificacion->getEstado()) <> strtoupper('PrePlanificada')) 
                                {
                                    $objSolicitudPlanificacion = $this->getDoctrine()
                                    ->getManager("telconet")
                                    ->getRepository('schemaBundle:InfoDetalleSolHist')
                                    ->findOneBy(array("detalleSolicitudId" => $objSolicitudPrePlanificacion->getId(),
                                                      "estado"=>"Planificada"));
                                    $objFechaPlanificada =  $objSolicitudPlanificacion->getFeIniPlan();
                                    $boolCoordinada = true;
                                   
                                }
                            }
                        }
                    }
                }
                //se agrega recuperacion de contrato en caso de que exista
                //obtiene rol activos o pendientes de la persona con rol Pre-cliente
                if ($strRol == "Cliente")
                {
                    $arrayEstadoContrato   = array('Activo');
                    $arrayEstadoRol        = array('Activo');
                }
                else
                {
                    $arrayEstadoContrato   = array('Pendiente','PorAutorizar');
                    $arrayEstadoRol        = array('Activo','Pendiente');
                }
                $objRol = $this->getManager()->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                               ->findPersonaEmpresaRolByParams($arrayDatosPersona->id, $arrayData['codEmpresa'],
                                                               $arrayEstadoRol, $strRol);
                if ($objRol) 
                {
                    $objContrato = $this->getManager()->getRepository('schemaBundle:InfoContrato')
                                                      ->findOneBy(array("personaEmpresaRolId" => $objRol->getId(),
                                                                        "estado"              => $arrayEstadoContrato));
                    if (is_object($objContrato)) 
                    {
                        $arrayContrato = array();
                        $arrayContrato['formaPagoId']          = $objContrato->getFormaPagoId()->getId();
                        $arrayContrato['numeroContratoEmpPub'] = $objContrato->getNumeroContratoEmpPub();
                        $arrayContrato['valorContrato']        = $objContrato->getValorContrato();
                        $arrayContrato['valorAnticipo']        = $objContrato->getValorAnticipo();
                        $arrayContrato['valorGarantia']        = $objContrato->getValorGarantia();
                        $arrayContrato['tipoContratoId']       = $objContrato->getTipoContratoId()->getId();
                        $arrayContrato['personaEmpresaRolId']  = $objRol->getId();
                        $arrayContrato['feFinContratoPost']    = $objContrato->getFeFinContrato()->format('d/m/Y');
                        $arrayContrato['origen']               = $objContrato->getOrigen();
                        $arrayContrato['idContrato']  	       = $objContrato->getId();
                        $arrayContrato['estado']               = $objContrato->getEstado();
                        $arrayContrato['numeroContrato']       = $objContrato->getNumeroContrato();
                        $infoContratoFormaPago                 = $this->getManager()
                                                                      ->getRepository('schemaBundle:InfoContratoFormaPago');

                        /* @var $infoContratoFormaPago \telconet\schemaBundle\Repository\InfoContratoFormaPago */
                        $objContratoFormaPago                  = $infoContratoFormaPago
                                                                 ->findOneBy(array('contratoId' => $objContrato->getId()));
                        if (is_object($objContratoFormaPago)) 
                        {
                            $arrayContrato['tipoCuentaId']       = $objContratoFormaPago->getTipoCuentaId()->getId();
                            $arrayContrato['bancoTipoCuentaId']  = $objContratoFormaPago->getBancoTipoCuentaId()->getId();
                            $arrayContrato['numeroCtaTarjeta']   = $objContratoFormaPago->getNumeroCtaTarjeta();
                            $arrayContrato['titularCuenta']      = $objContratoFormaPago->getTitularCuenta();
                            $arrayContrato['mesVencimiento']     = $objContratoFormaPago->getMesVencimiento();
                            $arrayContrato['anioVencimiento']    = $objContratoFormaPago->getAnioVencimiento();
                            $arrayContrato['codigoVerificacion'] = $objContratoFormaPago->getCodigoVerificacion();
                        }
                        else 
                        {
                            $arrayContrato['tipoCuentaId']       = null;
                            $arrayContrato['bancoTipoCuentaId']  = null;
                            $arrayContrato['numeroCtaTarjeta']   = null;
                            $arrayContrato['titularCuenta']      = null;
                            $arrayContrato['mesVencimiento']     = null;
                            $arrayContrato['anioVencimiento']    = null;
                            $arrayContrato['codigoVerificacion'] = null;
                        }
                        $emComunicacion           = $this->getDoctrine()->getManager('telconet_comunicacion');
                        $objInfoDocumentoRelacion = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion');
                        /* @var $objInfoDocumentoRelacion \telconet\schemaBundle\Repository\InfoDocumentoRelacion */
                        $arrayDocumentos          = $objInfoDocumentoRelacion->findBy(array('contratoId' => $objContrato->getId()));
                        if ($arrayDocumentos) 
                        {
                            $arrayContrato['numeroFiles'] = count($arrayDocumentos);
                        } 
                        else 
                        {
                            $arrayContrato['numeroFiles'] = 0;
                        }
                        if ($boolHayServicio) 
                        {
                            $objServicePlanificacion = $this->get('planificacion.Planificar');
                            $arrayPlanificacion = array();
                            if ($objFechaPlanificada)
                            {
                                $arrayPlanificacion[] = array("intervaloProgramacion" => null,
                                                              "planificacionHorarios" => null,
                                                              "estadoProgramacion"    => "Ya posee una solicitud de instalación planificada \n" . 
                                                              "con Fecha: " . $objFechaPlanificada->format('d/m/Y H:i'));
                            }
                            if (!$boolCoordinada) 
                            {
                                $arrayPlanificacion = $objServicePlanificacion->getCuposMobil(array("intJurisdiccionId" => $intJurisdiccionId));
                            }
                        }
                    }
                }
            }
        }
        catch(\Exception $ex)
        {
            error_log($ex->getMessage());
        }
        $strFormaContactoSitio = $this->container->getParameter('planificacion.mobile.codFormaContactoSitio');
        $arrayResponse = array();
        $arrayResponse['respuesta'] = new ObtenerPersonaResponse($arrayDatosPersona, $arrayPuntos, $arrayPlanes, $arrayContrato, $arrayPlanificacion, 
                                                    $strFormaContactoSitio);
        return $arrayResponse;
    }
    
    /**
     * Método que obtiene los planes disponibles
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     */
    public function obtenerPlanes($arrayData)
    {
        $array = $this->obtenerPlanesPrv($arrayData['codEmpresa'], $arrayData['idTipoNegocio'], $arrayData['idFormaPago'], $arrayData['idTipoCuenta'], $arrayData['idBancoTipoCuenta']);
        $arrayResponse['respuesta'] = array('plan' => $array);
        return $arrayResponse;
    }

    /**
     * Método que obtiene los puntos asociados a un cliente
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     */
    public function obtenerPuntosCliente($arrayData)
    {
        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
        $serviceInfoPunto = $this->get('comercial.InfoPunto');
        $array = $serviceInfoPunto->obtenerDatosPuntosCliente($arrayData['codEmpresa'], $arrayData['idPersona'], $arrayData['rol'], true, true, true);
        
        return array('puntos' => $array);
    }
    
    /**
     * Obtiene el listado de planes de internet considerando el tipo de negocio, forma de pago
     * y valor del plan.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.0 10-06-2019
     * 
     * 
     * Obtiene los productos por cada plan y las caracteristicas de cada producto.
     * 
     * @author Macjhony Vargas <mmvargas@telconet.ec>
     * @version 1.1 
     * 
     */
    public function obtenerPlanesPosibles($arrayData)
    {       
        $emComercial = $this->getDoctrine()->getManager();
        $arrayParametros  = array();
        $arrayParametros['tipoNegocio'] = $arrayData['tipoNegocio'];
        $arrayParametros['formaPago']   = $arrayData['formaPago'];
        $arrayParametros['valorPlan']   = $arrayData['valorPlan'];
        

        /* @var $serviceInfoServicio \telconet\comercialBundle\Service\InfoServicioService */
        $serviceInfoServicio = $this->get('comercial.InfoServicio');
        $arrayPlanes = $serviceInfoServicio->obtenerPlanesPosiblesWs($arrayParametros);
       
        if($arrayPlanes)
            {
                foreach($arrayPlanes as &$arrayPlan) 
                {
                    $intIdPlan = $arrayPlan['id_plan'];

                    $arrayParametros = array();
                    $arrayParametros['id_plan'] = $intIdPlan;
                    $arrayProductos = $emComercial->getRepository("schemaBundle:InfoPlanCab")
                                                  ->findByCondicionesProducto($arrayParametros);
                
                    foreach($arrayProductos as &$arrayProducto) 
                    {
                         if(!empty($arrayProducto))
                         {
                            $arrayParametros = array();
                            $arrayParametros['id_plan']     = $intIdPlan;
                            $arrayParametros['id_producto'] = $arrayProducto['id_producto'];

                            $arrayCaracteristica = $emComercial->getRepository("schemaBundle:InfoPlanCab")
                                                               ->findByCondicionesCaracteristicas($arrayParametros);
                            $arrayProducto['caracteristicas'] = $arrayCaracteristica;
                            unset($arrayProducto['id_producto']);
                         }
                         else
                         {
                            $arrayProductos['status']  = "ERROR";
                            $arrayProductos['mensaje'] = "No se obtuvieron los productos del plan:". $intIdPlan;
                            $arrayProducto['caracteristicas'] = array();
                         }
                    }
                    $arrayPlan['producto'] = $arrayProductos;
                }
            
                $arrayResponse['opcion']  = $arrayPlanes;
                $arrayResponse['status']  = "OK";
                $arrayResponse['mensaje'] = "OK";
            }
            else 
            {
                $arrayResponse['opcion']  = array();
                $arrayResponse['status']  = "ERROR";
                $arrayResponse['mensaje'] = "No se obtuvieron los planes.";
            }
        return $arrayResponse;
    }      
    
    /**
     * Documentación del método obtenerCaracteristicasProducto
     * 
     * Función que se encarga de obtener las caracteristicas de un producto
     * 
     * @param array $arrayData [ 'idProducto' => 'Id del producto' ]
     * @return array $arrayResponse [ 'respuesta' => 'Informacion del Producto con sus Caracteristicas,
     *                                'status'    => 'Ok o ERROR'
     *                                'mensaje'   => 'OK o detalle del error presentado' ]
     * 
     * @author José Bedón Sánchez <jobedon@telconet.ec>
     * @version 1.0
     * 
     */
    public function obtenerCaracteristicasProducto($arrayData)
    {
        $arrayParametros               = array();
        $arrayParametros['idProducto'] = $arrayData['idProducto'];
        $arrayResponse                 = array();
        $serviceAdminProducto          = $this->get('comercial.AdmiProducto');

        try
        {

            $arrayCaracteristicasProducto = $serviceAdminProducto->obteberCaracteristicasProducto($arrayParametros);

            $arrayResponse['respuesta'] = $arrayCaracteristicasProducto;
            $arrayResponse['status']    = "OK";
            $arrayResponse['mensaje']   = "OK";
        }
        catch (Exception $ex)
        {
            $arrayResponse['status']  = "ERROR";
            $arrayResponse['mensaje'] = $ex->getMessage();
        }

        return $arrayResponse;

    }
    
    /**
     * Método que obtiene la factibilidad de un punto mediante sus coordenadas
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     */    
    public function solicitarFactibilidadServicio($arrayData)
    {
        $strClientIp = '127.0.0.1';
        /* @var $serviceInfoServicio \telconet\comercialBundle\Service\InfoServicioService */
        $serviceInfoServicio = $this->get('comercial.InfoServicio');
        try
        {
            $arrayResult = $serviceInfoServicio->solicitarFactibilidadServicio($arrayData['codEmpresa'], $arrayData['prefijoEmpresa'], $arrayData['idServicio'], $arrayData['usrCreacion'], $strClientIp);
            $arrayPersonaResponse = $this->obtenerPersona($arrayData);
            $arrayPersonaResponse = $arrayPersonaResponse['respuesta']; 
            $arrayResponse['respuesta'] = array('result' => $arrayResult, 'personaResponse' => $arrayPersonaResponse);
        }
        catch (\Exception $e)
        {
            $arrayResponse['respuesta'] = array('error' => $e->getMessage());
        }
        return $arrayResponse;
    }
    
    /**
     * Método que genera un pin de seguridad para la autorización de un contrato digital
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     * 
     * Se modifica la lógica para realizar generación y envío de pin por separado.
     * @author Juan Romero Aguilar <jromero@telconet.ec>
     * @version 1.1 03/12/2019
     */        
    private function generarPinSecurity($arrayData) {
        $arrayResponse    = array();
        $strMensaje       = "";         
        $serviceSeguridad = $this->get('seguridad.Seguridad');
        /* @var $serviceSeguridad SeguridadService */
        try
        {
            // Generacion del PIN Security
            $arrayGeneracionPin                  = array ();
            $arrayGeneracionPin['strUsername']   = $arrayData['user'];
            $arrayGeneracionPin['strCodEmpresa'] = $arrayData['codEmpresa'];
            $arrayResponseService                = $serviceSeguridad->generarPinSecurity($arrayGeneracionPin);
            if (isset($arrayResponseService['status']) && $arrayResponseService['status'] == 'ERROR_SERVICE')
            {
                $strMensaje = $arrayResponseService['mensaje'];
                throw new \Exception('ERROR_PARCIAL');
            }
            if (isset($arrayResponseService['pin']))
            {
                $arrayResponse['respuesta']['pin']    = $arrayResponseService['pin'];
                //Preparo la data para el envío del pin
                $arrayDataEnvio                      = array();
                $arrayDataEnvio['strPin']            = $arrayResponseService['pin'];
                $arrayDataEnvio['strNumeroTlf']      = $arrayData['numero'];
                $arrayDataEnvio['strIdentificacion'] = $arrayData['usuario'];
                $arrayDataEnvio['strPersonaId']      = $arrayData['idcliente'];
                $arrayDataEnvio['strUsername']       = $arrayData['user'];
                $arrayDataEnvio['strCodEmpresa']     = $arrayData['codEmpresa'];
                //Realizo el envío del pin
                $arrayResponseEnvioPin   = $serviceSeguridad->enviarPinSecurity($arrayDataEnvio);
                $arrayResponse['respuesta']['status'] = $arrayResponseEnvioPin['status'];
                if (isset($arrayResponseEnvioPin['mensaje']))
                {
                    $arrayResponse['respuesta']['mensaje'] = $arrayResponseEnvioPin['mensaje'];
                }
            }
        }
        catch (\Exception $e)
        {
            if ($e->getMessage() == "ERROR_PARCIAL")
            {
                $arrayResponse['respuesta']['status']     = $this->status['ERROR_PARCIAL'];
                $arrayResponse['respuesta']['mensaje']    = $strMensaje;
            }
            else
            {
                $arrayResponse['respuesta']['status']     = $this->status['ERROR'];
                $arrayResponse['respuesta']['mensaje']    = $this->mensaje['ERROR'];
            }
            return $arrayResponse;
        }
        return $arrayResponse;
    }
    
    /**
     * Método que permite la autorización de un contrato digital
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1 04-10-2018 - Se corrige el response cuando es error o error_parcial, para la aplicación TM-COMERCIAL
     * 
     * @author Edgar Pin Villavicencion <epin@telconet.ec>
     * @version 1.2 22-07-2019 - Bug .- Se valida que exista el servicio y que este en estado Factible para poder autorizar el contrato
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.3 19-08-2019 Regularizacion.- se modifica variable para el envío de mensaje de error.
     * 
     */        
    private function autorizarContratoDigital($arrayData)
    {
        ini_set('max_execution_time', 400000);
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $strMensaje             = "";
        $arrayResponse          = array();
        $arrayDataRequest       = $arrayData['data']; // idContrato y Pin Security
        $strUsrCreacion         = $arrayData['user'];
        /* @var $serviceUtil \telconet\schemaBundle\Service\UtilService */
        $serviceUtil            = $this->get('schema.Util');
        /* @var $serviceSeguridad SeguridadService */
        $serviceSeguridad       = $this->get('seguridad.Seguridad');
        $objServiceCrypt        = $this->get('seguridad.crypt');
        
        /* @var $serviceContratoAprob InfoContratoAprobService */
        $serviceContratoAprob   = $this->get('comercial.InfoContratoAprob');
        $serviceContrato        = $this->get('comercial.InfoContrato');
        $strHayCertificado      = "S";
        
        try
        {
            $entityServicio = $emComercial->getRepository('schemaBundle:InfoServicio')
                                     ->findOneby(array("id"     => $arrayDataRequest['servicioId'],
                                                       "estado" => "Factible"));
            if (!is_object($entityServicio))
            {
                $strMensaje = "Error no se encuentra un servicio Factible";
                throw new \Exception('ERROR_PARCIAL');
            }

            $objCertificado = $this->getManager("telconet")->getRepository('schemaBundle:InfoCertificado')
                   ->findOneBy(array("numCedula" => $arrayDataRequest['usuario']));
            if(count($objCertificado) <= 0)
            {
                $strHayCertificado = "N";
                $objContrato = $this->getManager("telconet")->getRepository('schemaBundle:InfoContrato')
                                                            ->findOneBy(array(
                                                                          "id" => $arrayDataRequest['contrato']
                                                                         ));
                $strMensaje =  'No se encuentra certificado, se reversa el contrato digital. Por favor volver a consultar cliente';           
                $arrayParametros['objContrato']    = $objContrato;
                $arrayParametros['strUsrCreacion'] = $strUsrCreacion;
                $arrayParametros['strIpCreacion']  = "127.0.01";
                $arrayParametros['strMotivo']      = $strMensaje;
                $objPersonaEmpresaRol     = $serviceContratoAprob->rechazarContratoPorCertificado($arrayParametros);
                $arrayResponse['mensaje'] = $strMensaje;
                throw new \Exception('ERROR_PARCIAL');
            }
            // ========================================================================
            // Validacion del PinCode
            // ========================================================================       
            $arrayResponseService    = $serviceSeguridad->validarPinSecurity($arrayDataRequest['pincode'],
                                                                             $arrayDataRequest['usuario'],
                                                                             $arrayDataRequest['codEmpresa']);

            if (isset($arrayResponseService['status']) && $arrayResponseService['status'] == 'ERROR_SERVICE') 
            {
                $strMensaje = $arrayResponseService['mensaje'];
                $serviceUtil->insertError(
                                          'Telcos+',
                                          'ComercialMobileWSController->autorizarContratoDigital', 
                                          $strMensaje,
                                          $strUsrCreacion,
                                          '127.0.0.1'
                                         );
                throw new \Exception('ERROR_PARCIAL');
            }
            // ========================================================================
            // Autorizar el contrato digitalmente
            // ========================================================================          
            // ========================================================================
            // Enviar a firmar los documentos 
            // ========================================================================
            //Obtener datos contrato
            $arrayDataRequest['hayCertificado'] = $strHayCertificado;
            $serviceContratoDigital  = $this->get('comercial.InfoContratoDigital');
            $arrayDocumentosFirmados = $serviceContratoDigital->firmarDocumentos($arrayDataRequest);
            $strMensaje              = $arrayDocumentosFirmados['mensaje'];            
            switch($arrayDocumentosFirmados['salida'])
            {
                case '0':
                    $arrayResponse['status']     = $this->status['ERROR_PARCIAL'];
                    $arrayResponse['mensaje']    = $arrayDocumentosFirmados['mensaje'];
                    break;
                case '1':
                    $intIdContrato      = $arrayDataRequest['contrato'];
                    $objContrato        = $arrayDocumentosFirmados['objContrato'];
                    $arrayDocumentos    = $arrayDocumentosFirmados['documentos'];
                    if(is_object($objContrato))
                    {
                        $strPrefixMensaje = 'El contrato: ' . $objContrato->getNumeroContrato();
                        $arrayResponseService['strObservacionHistorial'] = $strPrefixMensaje . ' ' . $arrayResponseService['strObservacionHistorial'];
                        
                        // ================================================================================
                        $objPersonaEmpresaRol   = $serviceContratoAprob->getDatosPersonaEmpresaRolId($objContrato->getPersonaEmpresaRolId()->getId());
                        $arrayUltimaMilla       = $serviceContratoDigital->getDatosUltimaMilla($objPersonaEmpresaRol);

                        // Obtenemos la forma de pago que nos servira para determinar el valor de la instalacion
                        // de los servicios contratados por el cliente
                        $strTipoFormaPago       = $objContrato->getFormaPagoId() ? 
                                                  $objContrato->getFormaPagoId()->getDescripcionFormaPago() : null;

                        
                        if($strTipoFormaPago != "EFECTIVO")
                        {
                            $objContratoFormaPago = $serviceContratoAprob->getDatosContratoFormaPagoId($intIdContrato);
                            if(is_object($objContratoFormaPago))
                            {
                                $strTipoFormaPago = $objContratoFormaPago->getTipoCuentaId()->getDescripcionCuenta();
                            }
                            else
                            {
                                $strTipoFormaPago = null;
                            }
                        }
                        
                        // Verificamos que tengamos una forma de pago y la ultima milla del servicio contratado
                        if($strTipoFormaPago && !empty($arrayUltimaMilla))
                        {
                            // Obtenemos la informacion de la instalacion
                            $objInstalacion = $serviceContratoDigital->getDatosInstalacion($arrayDataRequest["codEmpresa"],
                                                                                           $strTipoFormaPago,
                                                                                           $arrayUltimaMilla["data"]["codigo"],
                                                                                           $objContrato->getPersonaEmpresaRolId());
                            if($objInstalacion["estado"])
                            {
                                // Si el porcentaje de descuesto es el 100% entonces se debe activar el contrato
                                // automaticamente
                                $intPorcentaje = $objInstalacion["porcentaje"];
                                if($intPorcentaje == 100)
                                {
                                    /**
                                    * Obtiene los servicios de la persona empresa rol
                                    */ 
                                    $arrayServiciosEncontrados = array();

                                    if($arrayDataRequest['prefijoEmpresa']=='TN')
                                    {
                                        $arrayEstado        = array( 'Rechazado', 
                                                                     'Rechazada', 
                                                                     'Cancelado', 
                                                                     'Anulado', 
                                                                     'Cancel', 
                                                                     'Eliminado', 
                                                                     'Reubicado', 
                                                                     'Trasladado' );
                                        $arrayResultadoTmp  = $serviceContratoAprob->getTodosServiciosXEstadoTn( $objPersonaEmpresaRol->getId(), 
                                                                                                                 0, 
                                                                                                                 10000, 
                                                                                                                 $arrayEstado );
                                    }
                                    else
                                    {
                                        $strEstadoTmp       = 'Factible';
                                        $arrayResultadoTmp  = $serviceContratoAprob->getTodosServiciosXEstado( $objPersonaEmpresaRol->getId(),
                                                                                                               0, 
                                                                                                               10000, 
                                                                                                               $strEstadoTmp );
                                    }

                                    $arrayRegistrosServicios = $arrayResultadoTmp['registros'];

                                    if( !empty($arrayRegistrosServicios) )
                                    {
                                        foreach($arrayRegistrosServicios as $objServicioTmp)
                                        {
                                            $arrayServiciosEncontrados[] = $objServicioTmp->getId();
                                        }
                                    }
                                    // --------------------------------------------------------------------------
                                    //obtener el arreglo de los datos de formas de pago
                                    $arrayFormaPago       = array();
                                    $intIdTipoCuenta      = 0;
                                    $intIdFormaPago       = $objContrato->getFormaPagoId() ? $objContrato->getFormaPagoId()->getId() : 0;
                                    
                                    if($objContratoFormaPago)
                                    {
                                        $arrayFormaPago['bancoTipoCuentaId']    = $objContratoFormaPago->getBancoTipoCuentaId() ?
                                                                                  $objContratoFormaPago->getBancoTipoCuentaId()->getId() : 0;
                                        $arrayFormaPago['numeroCtaTarjeta']     = $objServiceCrypt
                                                                                        ->descencriptar($objContratoFormaPago->getNumeroCtaTarjeta());
                                        $arrayFormaPago['mesVencimiento']       = $objContratoFormaPago->getMesVencimiento();
                                        $arrayFormaPago['anioVencimiento']      = $objContratoFormaPago->getAnioVencimiento();
                                        $arrayFormaPago['codigoVerificacion']   = $objContratoFormaPago->getCodigoVerificacion();
                                        $arrayFormaPago['titularCuenta']        = $objContratoFormaPago->getTitularCuenta();
                                        $intIdTipoCuenta                        = $objContratoFormaPago->getTipoCuentaId() ? 
                                                                                  $objContratoFormaPago->getTipoCuentaId()->getId() : 0;
                                    }
                                    $arrayParametros = array();
                                    $arrayParametros['intIdContrato']           = $intIdContrato;
                                    $arrayParametros['arrayPersona']            = array();
                                    $arrayParametros['arrayPersonaExtra']       = array();
                                    $arrayParametros['arrayFormasContacto']     = array();
                                    $arrayParametros['arrayFormaPago']          = $arrayFormaPago;
                                    $arrayParametros['intIdFormaPago']          = $intIdFormaPago;
                                    $arrayParametros['intIdTipoCuenta']         = $intIdTipoCuenta;
                                    $arrayParametros['arrayServicios']          = $arrayServiciosEncontrados;
                                    $arrayParametros['strUsrCreacion']          = $strUsrCreacion;
                                    $arrayParametros['strIpCreacion']           = '127.0.0.1';
                                    $arrayParametros['strPrefijoEmpresa']       = $arrayDataRequest['prefijoEmpresa'];
                                    $arrayParametros['strOrigen']               = 'MOVIL';
                                    $arrayParametros['strObservacionHistorial'] = $arrayResponseService['strObservacionHistorial'];
                                    $arrayParametros['strEmpresaCod']           = $arrayDataRequest['codEmpresa'];
                                    
                                    // Aprobacion del contrato
                                    $arrayGuardarProceso = $serviceContratoAprob->guardarProcesoAprobContrato($arrayParametros);
                                    
                                    if($arrayGuardarProceso 
                                       && array_key_exists('status',$arrayGuardarProceso) 
                                       && $arrayGuardarProceso['status']=='ERROR_SERVICE')
                                    {
                                        $strMensaje = $arrayGuardarProceso['mensaje'];
                                        throw new \Exception('ERROR_PARCIAL');
                                    }
                                    $arrayPlanificacion = $arrayGuardarProceso['arrayPlanificacion'];
                                    
                                }
                                else
                                {
                                    $arrayParametrosContrato                            = array();
                                    $arrayParametrosContrato["intIdContrato"]           = $intIdContrato;
                                    $arrayParametrosContrato['strObservacionHistorial'] = $arrayResponseService['strObservacionHistorial'];
                                    $arrayParametrosContrato['strIpCreacion']           = '127.0.0.1';
                                    $arrayParametrosContrato["strOrigen"]               = "MOVIL";
                                    
                                    $serviceContrato->setearDatosContrato($arrayParametrosContrato);
                                }

                            }
                        }

                        // Guardamos los documentos firmados
                        $arrayParametrosDocumentos['codEmpresa']         = $arrayDataRequest['codEmpresa'];
                        $arrayParametrosDocumentos['strUsrCreacion']        = $arrayData['user'];
                        $arrayParametrosDocumentos['strTipoArchivo']        = 'PDF';
                        $arrayParametrosDocumentos['intContratoId']         = $intIdContrato;
                        $arrayParametrosDocumentos['enviaMails']             = $arrayDocumentosFirmados['enviaMails'];

                        $serviceContratoDigital->guardarDocumentos($arrayParametrosDocumentos,$arrayDocumentos);

                        $arrayResponse['respuesta']['status']     = $this->status['OK'];
                        $arrayResponse['respuesta']['mensaje']    = $this->mensaje['OK'];
                        $arrayResponse['respuesta']['fechaAgenda'] = $arrayPlanificacion;
                    }
                    else
                    {
                        $arrayResponse['respuesta']['status']     = $this->status['ERROR_PARCIAL'];
                        $arrayResponse['respuesta']['mensaje']    = "Numero de contrato invalido.";
                    }
                    break;
                default:
                    $arrayResponse['respuesta']['status']     = $this->status['ERROR_PARCIAL'];
                    $arrayResponse['respuesta']['mensaje']    = "Firma de documentos no pudo ser ejecutada. Verificar parametros";
                    $strMensaje                  = $arrayDocumentosFirmados['mensaje'];
                    break;
            }            
        }
        catch (\Exception $e)
        {
            if ($e->getMessage() == 'ERROR_PARCIAL')
            {
                $arrayResponse['respuesta']['status']  = $this->status['ERROR_PARCIAL'];
                $arrayResponse['respuesta']['mensaje'] = $strMensaje;
            }
            else
            {
                $arrayResponse['respuesta']['status']  = $this->status['ERROR'];
                $arrayResponse['respuesta']['mensaje'] = $this->mensaje['ERROR'];
            }
            return $arrayResponse;
        }
        return $arrayResponse;
    }
    
    /**
     * Método que obtiene los datos generales para un contrato digital
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     */        
    private function obtenerDatosGeneral($arrayData)
    {
        $objPersona  = $this->obtenerDatosPersonaPrv($arrayData['codEmpresa'], $arrayData['identificacionCliente'], $arrayData['prefijoEmpresa']);
        $serviceInfoPunto = $this->get('comercial.InfoPunto');
        $arrayPuntos = array();
        try
        {
            if ($objPersona) 
            {
                $strRol = "Cliente";
                $arrayPuntos = $serviceInfoPunto->obtenerDatosPuntosCliente($arrayData['codEmpresa'], $objPersona->id, $strRol, true, true, true);
                
                if(!$arrayPuntos)
                {
                    $strRol = "Pre-cliente";
                    $arrayPuntos = $serviceInfoPunto->obtenerDatosPuntosCliente($arrayData['codEmpresa'], $objPersona->id, $strRol, true, true, true);
                }
                
                $arrayPuntosAux = array();
                $boolEsInternet = false;
                foreach ($arrayPuntos as $objPunto)
                {
                   if($objPunto["estado"] == 'Activo')
                    {
                        $arrayServicios = array();
                        foreach($objPunto['servicios'] as $objServicio)
                        {
                            
                            if($objServicio["planId"] != null)
                            {
                                $arrayParametros = array("strCodEmpresa" => $arrayData['codEmpresa'],
                                                         "strGrupo"      => $arrayData['grupo'], 
                                                         "idPlan"        => $objServicio["planId"]);

                                $boolObj = $this->getDoctrine()->getManager("telconet")
                                                               ->getRepository('schemaBundle:InfoPlanCab')
                                                               ->isPlanesByGrupo($arrayParametros);

                                if($boolObj)
                                {
                                  $boolEsInternet = true;  
                                }
                            }

                            if(
                                 $objServicio["descripcionProducto"] != null && 
                                 $objServicio["descripcionProducto"] == 'NETVOICE'
                             )
                             {

                                $arrayCarcteristicas = $this->getDoctrine()->getManager("telconet")
                                                           ->getRepository('schemaBundle:InfoServicioProdCaract')
                                                           ->getCaracteristicasServicios($objServicio["id"], $objServicio["estado"]);
                                $arrayCarcteristicasAux = array();
                                foreach($arrayCarcteristicas as $caracteristica)
                                {
                                    if($caracteristica['DESCRIPCION_CARACTERISTICA'] != null && 
                                            $caracteristica['DESCRIPCION_CARACTERISTICA'] == 'NUMERO')
                                    {
                                        $arrayCarcteristicasAux[] = $caracteristica;
                                    }
                                }
                                $objServicio['caracteristicas'] = $arrayCarcteristicasAux ;                           
                             }                        
                             $arrayServicios[] = $objServicio;
                        }
                        $objPunto['servicios'] = $arrayServicios;
                        $arrayPuntosAux[] = $objPunto;   
                    }
                   
                }
                if($boolEsInternet)
                {
                   $arrayPuntos = $arrayPuntosAux; 
                }
                else
                {
                   $arrayPuntos = null;  
                }                                
            }
        }catch(\Exception $ex)
        {
            error_log($ex->getMessage());
        }
        $arrayResponse['respuesta'] = new ObtenerDatosClienteResponse($arrayPuntos, $objPersona);
        return $arrayResponse; 
    }


    /**
     * Método que obtiene los números de teléfono técnicos de un punto
     * arrayData: [
     *     login   => Login del punto para consultar los números de teléfono movil.
     * ]
     * @author Wilmer Vera G. <wvera@telconet.ec>
     * @version 1.0 31-03-2020
     */
    private function obtenerNumerosTecnicoPorPuntoTN($arrayData)
    {
        $arrayResponse['respuesta'] = array();
        $arrayFormasContactoPunto   = array();
        $strUser                    = $arrayData['user'];
        $strIp                      = $arrayData['ip'];

        try
        {
            if ($arrayData['login'] != null)
            {
                $arrayParametros['strLogin']    = $arrayData['login'];
                $arrayParametros['strTipoRol']  = 'Contacto';
                $arrayFormasContactoPunto                              = $this->getDoctrine()->getManager("telconet")
                                                                                             ->getRepository('schemaBundle:AdmiFormaContacto')
                                                                                             ->getContactosTNPorLogin(
                                                                                                                        $arrayParametros
                                                                                                                       );
                if ($arrayFormasContactoPunto != null)
                {   
                    $arrayRespuesta = array();
                    $arrayRespuestaContactoTecnico   = array();
                    $arrayRespuestaContactoEscalable = array();
                    $arrayRespuestaContactoOtros     = array();
                    
                    $intContadorContactoTecnico    = 0;
                    $intContadorContactoEscalable  = 0;
                    $intContadorOtros              = 0;

                    foreach($arrayFormasContactoPunto as $equipo)
                    {
                        if($equipo['formaContacto'] == "Contacto Tecnico")
                        {
                            $arrayRespuestaContactoTecnico[] = $equipo;
                            $intContadorContactoTecnico = $intContadorContactoTecnico + 1;
                        }
                        else if($equipo['formaContacto'] == "Contacto Seguridad Escalable") 
                        {
                            $arrayRespuestaContactoEscalable[] = $equipo;
                            $intContadorContactoEscalable = $intContadorContactoEscalable + 1;
                        }
                        else
                        {
                            $arrayRespuestaContactoOtros[] = $equipo;
                            $intContadorOtros = $intContadorOtros + 1;
                        }
                    }
                    
                    if($intContadorContactoTecnico > 0)
                    {
                        $arrayRespuesta = $arrayRespuestaContactoTecnico;
                    }
                    else if ($intContadorContactoEscalable > 0)
                    {
                        $arrayRespuesta = $arrayRespuestaContactoEscalable;
                    }
                    else
                    {
                        $arrayRespuesta = $arrayRespuestaContactoOtros;
                    }


                    $arrayResponse['respuesta'] = $arrayRespuesta;
                    $arrayResponse['status']    = 200;
                }
                else
                {
                    $arrayResponse['mensaje'] = 'No se encontró información para mostrar';
                    $arrayResponse['status']  = 500;
                }
            }
            else
            {
                $arrayResponse['mensaje'] = 'No se encontró login en el sistema';
                $arrayResponse['status']  = 500;

            }
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+',
            'ComercialMobileWSAppsController->obtenerNumerosTecnicoPorPuntoTN',
             $ex->getMessage(),
             $strUser,
             $strIp);

            $arrayResponse['mensaje'] = 'ocurrio un error interno en el webservices consultado';
            $arrayResponse['status']  = 500;
        }

        return $arrayResponse;
    }

    /**
     * Método que obtiene los números de teléfono técnicos de un punto
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.0 21-07-2020
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.1 14-08-2020 - Se agrega la descripción del producto
     * @since 1.0
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.1 14-08-2020 - Se agrega el rol precliente y se mejora la busqueda
     *                           del teléfono y correo del cliente.
     * @since 1.0
     * 
     * @param arrayRequest
     * [
     *     CI/RUC   => Cédula/RUC del cliente.
     * ]
     *
     */
    private function obtenerInformacionCliente($arrayRequest)
    {
        $arrayResponse              = array();
        $arrayIdsPersonaRol         = array();
        $serviceUtil                = $this->get('schema.Util');
        $arrayData                  = $arrayRequest['data'];
        $strUser                    = $arrayRequest['user'];
        $strIp                      = $arrayRequest['ip'];
        $strNoIdentificacion        = $arrayData['noIdentificacion'];
        $intEmpresaCod              = $arrayData['empresaCod'];
        $strTipoPersona             = $arrayData['tipoPersona'] ? $arrayData['tipoPersona'] : array('cliente','pre-cliente');

        try
        {
            if (isset($strNoIdentificacion) && !empty($strNoIdentificacion))
            {
                $arrayParametros = array('identificacion'  => $strNoIdentificacion,
                                         'idEmpresa'       => $intEmpresaCod,
                                         'tipo_persona'    => $strTipoPersona
                                        );
                                
                $arrayPersona    =  $this->getManager()->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                       ->findPersonasPorCriterios($arrayParametros);
               
                if(!isset($arrayPersona['total']) || empty($arrayPersona['total']) || $arrayPersona['total'] < 1)
                {
                    throw new \Exception('No se encontró el cliente');
                }

                foreach($arrayPersona['registros'] as $objPersona)
                {
                    array_push($arrayIdsPersonaRol,$objPersona["id"]);
                }

                $objClientePersona      = $arrayPersona['registros'][0];

                if(!empty($objClientePersona["razon_social"]))
                {
                    $strNombreCliente  = $objClientePersona["razon_social"];
                }
                else
                {
                    $strNombreCliente  = $objClientePersona["nombres"]." ".$objClientePersona["apellidos"];
                }

                $arrayParametros      = array('intIdPersonaRol'        => $arrayIdsPersonaRol,
                                              'strDescripcionProducto' => "INTERNET DEDICADO");
                                              
                $arrayVendePuntoServ  =  $this->getManager()->getRepository('schemaBundle:InfoServicio')
                                              ->getVendedorPorServivioYPersona($arrayParametros);
                
                $arrayVendePunto = array();                              
                foreach($arrayVendePuntoServ as $objVendPuntoServ)
                {
                    $strLogin = $objVendPuntoServ['login'];
                    $objPunto = $this->getManager()->getRepository('schemaBundle:InfoPunto')
                                     ->findOneByLogin($strLogin);

                    $entityPuntoDatoAdicional = $this->getManager()->getRepository('schemaBundle:InfoPuntoDatoAdicional')
                                     ->findOneByPuntoId($objPunto->getId());

                    $arrayParamContac = array();                 
                    $arrayParamContac['strEstado']            = "Activo";
                    $arrayParamContac['intIdPunto']           = $objPunto->getId();
                    $arrayParamContac['strDescFormaContacto'] = "Correo Electronico";

                    $arrayCorreos = $this->getManager()->getRepository('schemaBundle:InfoPersonaContacto')
                                         ->getEmailComercialCD($arrayParamContac);

                    if(empty($arrayCorreos) && is_object($entityPuntoDatoAdicional))
                    {
                        $strContactoEmail = $entityPuntoDatoAdicional->getEmailEnvio();
                        if(!empty($strContactoEmail))
                        {
                            $arrayEmailCliente = preg_split('#(?<!\\\)\;#', $strContactoEmail);
                            foreach($arrayEmailCliente as $strEmail)
                            {
                                $arrayClienteEmail = array(
                                                    "strFormaContacto" => $strEmail
                                                );
                                array_push($arrayCorreos,$arrayClienteEmail); 
                            }
                        }
                    }

                    $arrayParamContac['strDescFormaContacto'] = array(
                                                       "Telefono Fijo",
                                                        "Telefono Fijo Referencia IPCC",
                                                        "Telefono Internacional",
                                                        "Telefono Movil",
                                                        "Telefono Movil Cable and Wireless",
                                                        "Telefono Movil Claro",
                                                        "Telefono Movil CNT",
                                                        "Telefono Movil Digicel",
                                                        "Telefono Movil Movistar",
                                                        "Telefono Movil Referencia IPCC",
                                                        "Telefono Movil Tuenti",
                                                        "Telefono Traslado");

                    $arrayTelefonos = $this->getManager()->getRepository('schemaBundle:InfoPersonaContacto')
                                           ->getEmailComercialCD($arrayParamContac);

                    if(empty($arrayTelefonos) && is_object($entityPuntoDatoAdicional))
                    {
                        $strContactoTelefono = $entityPuntoDatoAdicional->getTelefonoEnvio();
                        if(!empty($strContactoTelefono))
                        {
                            $arrayTelefonosCliente = preg_split('#(?<!\\\)\;#', $strContactoTelefono);
                            foreach($arrayTelefonosCliente as $strTelefono)
                            {
                                $arrayTelfCliente = array(
                                                    "strFormaContacto" => $strTelefono
                                                );
                                array_push($arrayTelefonos,$arrayTelfCliente); 
                            } 
                        }
                    }
                    $objVendPuntoServ['correos']   =  $arrayCorreos;
                    $objVendPuntoServ['telefonos'] =  $arrayTelefonos;
                    array_push($arrayVendePunto,$objVendPuntoServ);             
                }

                $arrayRespuesta =  array(
                                        "NombreCliente" => $strNombreCliente,
                                        "Puntos"        => $arrayVendePunto
                                    );

                $arrayResponse['data']      = $arrayRespuesta;
                $arrayResponse['status']    = 200;
            }
            else
            {
                $arrayResponse['mensaje'] = 'Parámetro de búsqueda (noIdentificacion) no encontrado';
                $arrayResponse['status']  = 500;

            }
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+',
                                    'ComercialMobileWSAppsController->obtenerInformacionCliente',
                                    $ex->getMessage(),
                                    $strUser,
                                    $strIp);

            $arrayResponse['data']    = null;
            $arrayResponse['mensaje'] = $ex->getMessage();
            $arrayResponse['status']  = 500;
        }

        return $arrayResponse;
    }


    /**
     * Método que obtiene los números de teléfono Movil de un punto
     * arrayData: [
     *     login   => Login del punto para consultar los números de teléfono movil.
     * ]
     * @author Andrés Montero Holguin <amontero@telconet.ec>
     * @version 1.0 25-10-2019
     */
    private function obtenerNumerosMovilPorPunto($arrayData)
    {
        $arrayResponse['respuesta'] = array();
        $arrayFormasContactoPunto   = array();
        try
        {
            $objIdPunto = $this->getDoctrine() ->getManager("telconet")->getRepository('schemaBundle:InfoPunto')
                                                                       ->findOneBy(array(
                                                                                         'estado' => 'Activo',
                                                                                         'login'  => $arrayData['login']
                                                                                        )
                                                                                  );
            if (is_object($objIdPunto))
            {
                $arrayParametrosFormasContacto['intIdPunto']           = $objIdPunto->getId();
                $arrayParametrosFormasContacto['strTipoFormaContacto'] = 'MOVIL';
                $arrayFormasContactoPunto                              = $this->getDoctrine()->getManager("telconet")
                                                                                             ->getRepository('schemaBundle:AdmiFormaContacto')
                                                                                             ->getFormasContactoByPunto(
                                                                                                                        $arrayParametrosFormasContacto
                                                                                                                       );
                if (isset($arrayFormasContactoPunto[0]['datos']))
                {
                    $arrayTelefonos = explode(';',$arrayFormasContactoPunto[0]['datos']);
                    $intIndice      = 0;
                    $arrayRespuesta = array();
                    foreach ($arrayTelefonos as $strTelefono)
                    {
                        $arrayInfo = explode(':',$strTelefono);
                        $arrayRespuesta[$intIndice]['formaContacto'] = $arrayInfo[0];
                        $arrayRespuesta[$intIndice]['valor'] = $arrayInfo[1];
                        $intIndice++;
                    }
                    $arrayResponse['respuesta'] = $arrayRespuesta;
                    $arrayResponse['status']    = 200;
                }
                else
                {
                    $arrayResponse['mensaje'] = 'No se encontró información para mostrar';
                    $arrayResponse['status']  = 500;
                }
            }
            else
            {
                $arrayResponse['mensaje'] = 'No se encontró login en el sistema';
                $arrayResponse['status']  = 500;

            }
        }
        catch(\Exception $ex)
        {
            error_log($ex->getMessage());
            $arrayResponse['mensaje'] = 'ocurrio un error interno en el webservices consultado';
            $arrayResponse['status']  = 500;
        }

        return $arrayResponse;
    }

    /**
     * Método que obtiene los números de teléfono Movil de un punto
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 06-02-2020
     *
     * @param  Array $arrayParametros
     * @return Array $arrayRespuesta
     */
    private function getNumeroMovilPorPunto($arrayParametros)
    {
        $emComercial = $this->getDoctrine()->getManager("telconet");
        $serviceUtil = $this->get('schema.Util');
        $arrayData   = $arrayParametros['data'];
        $strUser     = $arrayParametros['user'] ? $arrayParametros['user'] : 'Telcos+';
        $strIp       = $arrayParametros['ip']   ? $arrayParametros['ip']   : "127.0.0.1";

        try
        {
            if ( (!isset($arrayData['idPunto']) || empty($arrayData['idPunto'])) &&
                 (!isset($arrayData['loginPunto']) || empty($arrayData['loginPunto'])) )
            {
                throw new \Exception('Error : Ingresar el valor de consulta en el atributo *idPunto* o '.
                                     'en el atributo *loginPunto*');
            }

            $arrayRespuesta = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                    ->getNumeroMovilPorPunto(array('serviceUtil'       => $serviceUtil,
                                                   'intIdPunto'        => $arrayData['idPunto'],
                                                   'strLogin'          => $arrayData['loginPunto'],
                                                   'strEstadoContacto' => $arrayData['estadoContacto'],
                                                   'strEstadoWs'       => $arrayData['estadoWs'],
                                                   'strValor'          => $arrayData['numeroTelefono'],
                                                   'strUser'           => $strUser,
                                                   'strIp'             => $strIp));
        }
        catch (\Exception $objException)
        {
            $strMessage = 'Error en el WebService ComercialMobileWSAppsController->getNumeroMovilPorPunto';

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ',$objException->getMessage())[1];
            }

            $serviceUtil->insertError('Telcos+',
                                      'ComercialMobileWSAppsController->getNumeroMovilPorPunto',
                                       $objException->getMessage(),
                                       $strUser,
                                       $strIp);

            $arrayRespuesta = array ('status' => 'fail', 'message' => $strMessage);
        }

        return $arrayRespuesta;
    }

    /**
     * Método que permite actualizar el estado de uso del contacto del cliente o del punto cliente
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 06-02-2020
     *
     * @param  Array $arrayParametros
     * @return Array $arrayRespuesta
     */
    private function setNumeroMovilEstadoWhatsapp($arrayParametros)
    {
        $serviceUtil      = $this->get('schema.Util');
        $serviceComercial = $this->get('comercial.Comercial');
        $arrayData        = $arrayParametros['data'];
        $strUser          = $arrayParametros['user'] ? $arrayParametros['user'] : 'Telcos+';
        $strIp            = $arrayParametros['ip']   ? $arrayParametros['ip']   : "127.0.0.1";

        try
        {
            if (empty($arrayData))
            {
                throw new \Exception("Error : No existen datos para actualizar el estado de los contactos de whatsapp del cliente.");
            }

            if (!is_array($arrayData))
            {
                throw new \Exception("Error : Los datos para actualizar no están dentro de un array.");
            }

            $arrayRespuesta['status'] = 'ok';

            foreach($arrayData as $arrayValue)
            {
                $arrayResult = $serviceComercial->setNumeroMovilEstadoWhatsapp(array('intIdContacto'    => $arrayValue['idContacto'],
                                                                                     'strTipoContacto'  => $arrayValue['tipoContacto'],
                                                                                     'strEstadoWs'      => $arrayValue['estadoWs'],
                                                                                     'strFechaEstadoWs' => $arrayValue['fechaEstadoWs'],
                                                                                     'strUser'          => $strUser,
                                                                                     'strIp'            => $strIp));
                $arrayRespuesta['result'][] = $arrayResult;
            }
        }
        catch (\Exception $objException)
        {
            $strMessage = 'Error en el WebService ComercialMobileWSAppsController->setNumeroMovilEstadoWhatsapp';

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ',$objException->getMessage())[1];
            }

            $serviceUtil->insertError('Telcos+',
                                      'ComercialMobileWSAppsController->setNumeroMovilEstadoWhatsapp',
                                       $objException->getMessage(),
                                       $strUser,
                                       $strIp);

            $arrayRespuesta = array ('status' => 'fail', 'message' => $strMessage);
        }

        return $arrayRespuesta;
    }

    /**
     * Obtiene los puntos que contengan el número de teléfono Movil enviado por parametro
     * arrayData: [
     *     telefono   => Número de teléfono movil a consultar
     *     codEmpresa => Código de la empresa que se desea consultar
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 22-02-2019
     * @since 1.0
     * @return JsonResponse
     */
    public function obtenerPuntosPorNumeroMovil($arrayData)
    {
        $objServiceComercial         = $this->get('comercial.Comercial');
        $objServiceUtil              = $this->get('schema.Util');
        $strValor                    = $arrayData['telefono'];
        $strCodEmpresa               = $arrayData['codEmpresa'];
        $arrayRespuesta['respuesta'] = array();
        try
        {
            //Consultamos los seguimientos por usuario
            $arrayParametros                          = array();
            $arrayParametros['strCodEmpresa']         = $strCodEmpresa;
            $arrayParametros['strValorFormaContacto'] = $strValor;
            $arrayParametros['strTipoFormaContacto']  = 'MOVIL';
            $arrayDatos                               = $objServiceComercial->getPuntoByFormasContacto($arrayParametros);
            if( !empty($arrayDatos) )
            {
                foreach($arrayDatos as $arrayDato)
                {
                    $arrayPuntos[] = $arrayDato;
                }
                $arrayRespuesta['respuesta'] = $arrayPuntos;
                $arrayRespuesta['status']    = 200;
            }
            else
            {
                $arrayRespuesta['mensaje'] = 'No se encontró información para mostrar';
                $arrayRespuesta['status']  = 500;
            }
        }
        catch(\Exception $e)
        {
            error_log('ComercialBundle.ComercialMobileWSAppsController.obtenerPuntosPorNumeroMovil: '.$e->getMessage());
            $objServiceUtil->insertError( 'Telcos+',
                                          'ComercialBundle.ComercialMobileWSAppsController.obtenerPuntosPorNumeroMovil',
                                          'Error al consultar los logins por forma de contacto. '.$e->getMessage(),
                                          'telcos',
                                          '127.0.0.1' );
            $arrayRespuesta['mensaje'] = 'ocurrio un error interno en el webservices consultado';
            $arrayRespuesta['status']  = 500;
        }
        return $arrayRespuesta;
    }

    /**
     * Método que permite la planificación de instalación de un servicio desde la aplicación comercial
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 25-09-2018
     */    
    private function planificarOnLine($arrayData) 
    {
        $objSolicitudPrePlanificacion = $this->getDoctrine() 
                ->getManager("telconet")
                ->getRepository('schemaBundle:InfoDetalleSolicitud')
                ->findOneBy(array("servicioId" => $arrayData['servicioId'],
            "tipoSolicitudId" => "8"));
        $arrayFechaProgramacion = explode("T", $arrayData['fechaDesde']);
        $arrayFecha             = explode("T", $arrayData['fechaDesde']);
        $arrayF                 = explode("-", $arrayFechaProgramacion[0]);
        $arrayHoraF             = explode(":", $arrayFecha[1]);
        
        $arrayFechaInicio       = date("Y/m/d H:i", strtotime($arrayF[2] . "-" . $arrayF[1] . "-" . $arrayF[0] . " " . $arrayFecha[1]));
        
        $arrayFechaI = date_create(date('Y/m/d', strtotime($arrayFecha[0])));
        $arrayFechaI = $arrayFechaI->format("Y-m-d");
        

        $arrayFecha2 = explode("T", $arrayData['fechaHasta']);
        $arrayF2 = explode("-", $arrayFechaProgramacion[0]);
        $arrayHoraF2 = explode(":", $arrayFecha2[1]);
        $arrayFechaInicio2 = date("Y/m/d H:i", strtotime($arrayF2[2] . "-" . $arrayF2[1] . "-" . $arrayF2[0] . " " . $arrayFecha2[1]));
        $arrayFechaSms = date("d/m/Y", strtotime($arrayF2[2] . "-" . $arrayF2[1] . "-" . $arrayF2[0] . " " . $arrayFecha2[1]));

        $strHoraInicioServicio = $arrayHoraF;
        $strHoraFinServicio = $arrayHoraF2;
        $strHoraInicio = $arrayHoraF;
        $strHoraFin = $arrayHoraF2;
        $strNombreContacto = $arrayData['nombreContactoSitio'];
        $strNumeroTelefono = $arrayData['numeroContactoSitio'];

        $arrayParametros['strOrigen'] = "MOVIL";
        $arrayParametros['intIdFactibilidad'] = $objSolicitudPrePlanificacion->getId();
        $arrayParametros['dateF'] = $arrayF;
        $arrayParametros['dateFecha'] = $arrayFecha;
        $arrayParametros['strFechaInicio'] = $arrayFechaInicio;
        $arrayParametros['strFechaFin'] = $arrayFechaInicio2;
        $arrayParametros['strHoraInicioServicio'] = $strHoraInicioServicio;
        $arrayParametros['strHoraFinServicio'] = $strHoraFinServicio;
        $arrayParametros['dateFechaProgramacion'] = $arrayFechaI;
        $arrayParametros['strHoraInicio'] = $strHoraInicio;
        $arrayParametros['strHoraFin'] = $strHoraFin; 
        $arrayParametros['strObservacionServicio'] = "Contacto: " .  $strNombreContacto . " Teléfono Contacto: " . $strNumeroTelefono;
        $arrayParametros['strIpCreacion'] = "127.0.0.1";
        $arrayParametros['strUsrCreacion'] = "MOBILE";
        $arrayParametros['strObservacionSolicitud'] = "Contacto: " .  $strNombreContacto . " Teléfono Contacto: " . $strNumeroTelefono;
        $arrayParametros['strNumeroTelefonico'] = $arrayData['numeroTelefonico'];
        $arrayParametros['arrayFechaSms'] = $arrayFechaSms;
        $arrayParametros['strEmail'] = $arrayData['eMail'];
      
        //valido si hay cupo disponible
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $entityDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                     ->findOneById($arrayParametros['intIdFactibilidad']);
        $strFechaPar = substr($arrayFechaInicio, 0, -1);
        $strFechaPar .= "1";
        $strFechaPar = str_replace("-", "/", $strFechaPar);
        
        $strFechaPar2 = str_replace("-", "/", $arrayFechaInicio);
        

        $intJurisdicionId = $entityDetalleSolicitud->getServicioId()
                        ->getPuntoId()
                        ->getPuntoCoberturaId()->getId();

        $intHoraCierre = $this->container->getParameter('planificacion.mobile.hora_cierre');

        $arrayPar    = array(
            "strFecha" => $strFechaPar,
            "intJurisdiccion" =>  $intJurisdicionId,
            "intHoraCierre" => $intHoraCierre);
        
        $arrayCount  = $emComercial
            ->getRepository('schemaBundle:InfoCupoPlanificacion')
            ->getCountOcupados($arrayPar);
        $intCuantos = $arrayCount['CUANTOS'];        
        
        $arrayParAgenda = array("strFechaDesde" => $strFechaPar2,
                                "intJurisdiccionId" => $intJurisdicionId);
        
        $entityAgendaDetalle  = $emComercial
             ->getRepository('schemaBundle:InfoAgendaCupoDet')
             ->getDetalleAgenda($arrayParAgenda);        

        $intCupoMobile = 0;
        foreach ($entityAgendaDetalle['registros'] as $entityDet)
        {
          $intCupoMobile += $entityDet->getCuposMovil();
        }
        if ($intCuantos >= $intCupoMobile)
        {
            $objServicePlanificacion = $this->get('planificacion.Planificar');
            $arrayPlanificacion = $objServicePlanificacion->getCuposMobil(array("intJurisdiccionId" => $intJurisdicionId));
            $arrayResultado = array();
            $arrayResultado['respuesta']['codigoRespuesta'] = 1; 
            $arrayResultado['respuesta']['listProgramacionInstalacion'] = $arrayPlanificacion;    
            $arrayResultado['respuesta']['mensaje'] = "No hay cupo disponible para este horario, seleccione otro horario por favor!";
           //codigoRespuesta 
            return $arrayResultado;           
        }        
     
        $objServicePlanificacion = $this->get('planificacion.Planificar');
        $arrayResultado['respuesta'] = $objServicePlanificacion->coordinarPlanificacion($arrayParametros);

        return $arrayResultado;
    }

    /**
     * Método que consulta los datos de contacto una persona o punto (desde middleware a Telcos) 
     * 
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0 14/04/2021
     *
     * @param type $arrayRequest
     */
    private function obtenerFormasContacto($arrayRequest) 
    {
            $arrayResponse              = array();
            $serviceUtil                = $this->get('schema.Util');
            $arrayData                  = $arrayRequest['data'];
            $strOrigen                  = $arrayData['origen'];
            $strIdentificacion          = $arrayData['identificacion'];
            $strIpCreacion              = "127.0.0.1";
            $strLogin                   = $arrayData['login'];  
            $strUser                    = $arrayData['user'];
            $emComercial                = $this->getDoctrine()->getManager();
            $servicePlantilla           = $this->get('soporte.EnvioPlantilla');
            
            try
            {
                //Validar Campos del request
                if (empty($strOrigen))
                {
                   throw new \Exception("Falta la variable origen en el request");
                }
                
                if (empty($arrayData['codEmpresa']))
                {
                   throw new \Exception("Falta la variable codEmpresa en el request");
                }
                
                if (empty($arrayData['canal']))
                {
                   throw new \Exception("Falta la variable canal en el request");
                }
                
                if($strOrigen != "PUNTO" && $strOrigen != "PERSONA")
                {
                    throw new \Exception("la variable origen a ingresar debe estar detallada"
                                       . " como PUNTO o PERSONA");
                }

                $arrayParametroCanal = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                   ->getOne('COMERCIAL_GENERICO',
                                                        '',
                                                        '',
                                                        'CANALES_WS_FORMA_CONTACTO',
                                                        '',
                                                        '',
                                                        '',
                                                        ''
                                                        );
        
                if (is_array($arrayParametroCanal))
                {
                    $strCanales = !empty($arrayParametroCanal['valor1']) ? $arrayParametroCanal['valor1'] : "";
                }

                $arrayCanales = explode(",", $strCanales); 
                
                if(!in_array(strtoupper($arrayData['canal']),$arrayCanales))
                {
                    throw new \Exception("El canal enviado no se encuentra parametrizado");                    
                }                             
                
                if ($strOrigen=="PERSONA")
                {
                
                    if (empty($strIdentificacion))
                    {
                       throw new \Exception("Falta la variable identificacion en el request");
                    }
                    
                    
                    $entityPersona  = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                  ->findOneBy(array('identificacionCliente'  => $strIdentificacion));
                    
                    if(is_object($entityPersona))
                    {
                       
                       $intIdPersonaRol   =  $this->getManager()->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                                ->getPersonaRol(array('intIdPersona'  => $entityPersona->getId(),
                                                                                      'strEmpresaCod' => $arrayData['codEmpresa']));
                        
                       
                       if(empty($intIdPersonaRol))
                       {
                           throw new \Exception('No existen datos de contacto para la identificación '.$strIdentificacion.' ingresada ');
                       }
                       
                        
                       $arrayContactoPersona    =  $this->getManager()->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                                       ->getFormasContacto($entityPersona->getId());

                       if(empty($arrayContactoPersona))
                       {
                            throw new \Exception('No existen datos de contacto para la identificación '.$strIdentificacion.' ingresada ');
                       }
                       
                       
                       $arrayFormaContacto  = array();
                       foreach ($arrayContactoPersona as $array) 
                       {
                         
                         $arrayRespuesta["idPersonaFormaContacto"] = $array['idPersonaFormaContacto'];
                         $arrayRespuesta["formaContactoId"] = $array['formaContactoId'];
                         $arrayRespuesta["formaContacto"] = $array['formaContacto'];
                         $arrayRespuesta["valor"] = $array['valor'];
                         $arrayRespuesta["feCreacion"] = $array['feCreacion'];
                         $arrayRespuesta["usrCreacion"] = $array['usrCreacion'];
                        
                         
                         array_push($arrayFormaContacto, $arrayRespuesta);
                       }
                       
                       $arrayResponse['status']     = "OK";
                       $arrayResponse['mensaje']    = "Proceso realizado correctamente";
                       $arrayResponse['campos']  = $arrayFormaContacto;
                       
                       
                    }
                    else
                    {
                        throw new \Exception('No existen datos de contacto para la identificación '.$strIdentificacion.' ingresada ');
                    }
                }
                elseif($strOrigen=="PUNTO")
                {
                    if (empty($strLogin))
                    {
                       throw new \Exception("Falta la variable login en el request");
                    }
                    
                    $entityPunto    =  $emComercial->getRepository('schemaBundle:InfoPunto')
                                                   ->findOneBy(array('login' => $strLogin)); 
                    
                    
                    if(is_object($entityPunto))
                    {
                        
                        $intIdPersonaRol = $entityPunto->getPersonaEmpresaRolId();
                        
                        $objPersonaRol   =  $this->getManager()->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                 ->getPersonaRol(array('intIdPersonaRol'  => $entityPunto->getPersonaEmpresaRolId(),
                                                                                       'strEmpresaCod'    => $arrayData['codEmpresa']));
                        
                       
                        if(empty($objPersonaRol))
                        {
                           throw new \Exception('No existen datos de contacto para el login '.$strLogin.' ingresado ');
                        }
                    
                        $intIdPersona = $objPersonaRol[0]['idPersona'];
                        
                        
                        if(empty($arrayData['identificacion']))
                        {
                          //obtener identificación por medio del login
                    
                          $objPersona = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                    ->findOneById($intIdPersona);
                    
                          if (!is_object($objPersona))
                          {
                             throw new \Exception('No se ha podido obtener la información de la persona ');
                          }
                          
                          $arrayData['identificacion'] =  $objPersona->getIdentificacionCliente();
                    
                        }
                        
                        
                        $arrayContactoPunto    =  $this->getManager()->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                     ->getFormasContactoPunto($entityPunto->getId());

                        if(empty($arrayContactoPunto))
                        {
                            throw new \Exception('No existen datos de contacto para el login '.$strLogin.' ingresado ');
                        }
                        
                        $arrayFormaContacto  = array();
                        foreach ($arrayContactoPunto as $array) 
                        {
                         
                          $arrayRespuesta["idPuntoFormaContacto"] = $array['idPuntoFormaContacto'];
                          $arrayRespuesta["formaContactoId"] = $array['formaContactoId'];
                          $arrayRespuesta["formaContacto"] = $array['formaContacto'];
                          $arrayRespuesta["valor"] = $array['valor'];
                          $arrayRespuesta["feCreacion"] = $array['feCreacion'];
                          $arrayRespuesta["usrCreacion"] = $array['usrCreacion'];
                        
                         
                          array_push($arrayFormaContacto, $arrayRespuesta);
                        }
                       
                        $arrayResponse['status']     = "OK";
                        $arrayResponse['mensaje']    = "Proceso realizado correctamente";
                        $arrayResponse['campos']  = $arrayFormaContacto;
                        
                        
                        
                    }
                    else
                    {
                        throw new \Exception('No existen datos de contacto para el login '.$strLogin.' ingresado ');
                    }
                                                           
                    
                }
                
            }
            catch (\Exception $ex) 
            {
                $serviceUtil->insertError('Telcos+',
                                        'ComercialMobileWSAppsController->obtenerFormasContacto',
                                        $ex->getMessage(),
                                        $strUser,
                                        $strIpCreacion);
                
                
                if($arrayData['origen']=="PUNTO")
                {
                   $strAsuntoCorreo = "Error al consultar los datos “Contacto Punto” del cliente";
                   $strWS = "Consultar Forma Contacto Punto op: obtenerFormasContacto";
                   $strDescripcion = 'WS que consulta en Telcos los datos “Contacto Punto” del cliente';
                }
                elseif($arrayData['origen']=="PERSONA")
                {
                   $strAsuntoCorreo = "Error al consultar los datos “Contacto persona” del cliente";
                   $strWS = "Consultar Forma Contacto Persona op:obtenerFormasContacto ";
                   $strDescripcion = 'WS que consulta en Telcos los datos “Contacto Persona” del cliente';
                }
                
                
                $arrayParametros = array('strCanal'          => $arrayData['canal'],
                                         'strWS'             => $strWS,
                                         'strDescripcion'    => $strDescripcion,
                                         'strIdentificacion' => $arrayData['identificacion'],
                                         'strOrigen'         => $arrayData['origen'],
                                         'strLogin'          => $arrayData['login'],
                                         'strError'          => $ex->getMessage(),
                                         'strTipoCorreo'     => 'CONTACTO');
                
                $servicePlantilla->generarEnvioPlantilla($strAsuntoCorreo, 
                                                         array(), 
                                                         "NOTIF_EXT_CONT", 
                                                         $arrayParametros, 
                                                         $arrayData['codEmpresa'], 
                                                         null, 
                                                         '', 
                                                         null,
                                                         false,
                                                         null);
                
    
                $arrayResponse['mensaje'] = $ex->getMessage();
                $arrayResponse['status']  = "ERROR";
                
            }
    
            return $arrayResponse;
    }
    
    
    /**
     * Método que crea,actualiza y elimina una forma de contacto, para un punto y una persona.
     * 
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0 14/04/2021
     * @param type $arrayRequest
     */
    private function actualizarFormaContacto($arrayRequest)
    {
        $arrayRespuesta                  = array();
        $serviceUtil                     = $this->get('schema.Util');
        $arrayData                       = $arrayRequest['data'];
        $strIpCreacion                   = "127.0.0.1";
        $serviceInfoPersonaFormaContacto = $this->get('comercial.InfoPersonaFormaContacto');
        $servicePlantilla                = $this->get('soporte.EnvioPlantilla');
        $emComercial                     = $this->getDoctrine()->getManager();
        
        try 
        {
            
            //Validar Campos del request
            if(empty($arrayData['proceso']))
            {
                throw new \Exception("Falta la variable proceso en el request");
            }
            
            if(empty($arrayData['origen']))
            {
                throw new \Exception("Falta la variable origen en el request");
            }
            
            if (empty($arrayData['codEmpresa']))
            {
                throw new \Exception("Falta la variable codEmpresa en el request");
            }
                
            if (empty($arrayData['canal']))
            {
                throw new \Exception("Falta la variable canal en el request");
            }
            
            
            if($arrayData['proceso'] != "CREAR" && $arrayData['proceso'] != "ACTUALIZAR" 
               && $arrayData['proceso'] != "ELIMINAR")
            {
                throw new \Exception("La variable proceso a ingresar debe estar detallada como"
                                   . " CREAR, ACTUALIZAR o ELIMINAR");
            }
            
            
            if($arrayData['origen'] != "PUNTO" && $arrayData['origen'] != "PERSONA")
            {
                throw new \Exception("la variable origen a ingresar debe estar detallada"
                                   . " como PUNTO o PERSONA");
            }
            
            
            if ($arrayData['origen']=="PERSONA" && empty($arrayData['identificacion']))
            {
                throw new \Exception("Falta la variable identificacion en el request");
            }
            
            if ($arrayData['origen']=="PUNTO" && empty($arrayData['login']))
            {
                throw new \Exception("Falta la variable login en el request");
            }
            
             $arrayParametroCanal = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                    ->getOne('COMERCIAL_GENERICO',
                                             '',
                                             '',
                                             'CANALES_WS_FORMA_CONTACTO',
                                             '',
                                             '',
                                             '',
                                             ''
                                            );
                            
            if (is_array($arrayParametroCanal))
            {
                $strCanales = !empty($arrayParametroCanal['valor1']) ? $arrayParametroCanal['valor1'] : "";
            }

            $arrayCanales = explode(",", $strCanales);          
            
            if(!in_array(strtoupper($arrayData['canal']),$arrayCanales))
            {
                throw new \Exception("El canal enviado no se encuentra parametrizado");                                                   
            }
            
            if($arrayData['origen']=="PERSONA")
            {
                //validar empresa
                $entityPersona  = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                  ->findOneBy(array('identificacionCliente'  => $arrayData['identificacion']));
                    
                if(!is_object($entityPersona))
                {
                    throw new \Exception("La cedula ".$arrayData['identificacion']." ingresada no existe");  
                }
                
                
                $intIdPersonaRol   =  $this->getManager()->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                                ->getPersonaRol(array('intIdPersona'  => $entityPersona->getId(),
                                                                                      'strEmpresaCod' => $arrayData['codEmpresa']));
                        
                if(empty($intIdPersonaRol))
                {
                    throw new \Exception('No existen datos de contacto para la identificación '.$arrayData['identificacion'].' ingresada ');
                }
                
                
            }
            elseif($arrayData['origen']=="PUNTO") 
            {
                $entityPunto    =  $emComercial->getRepository('schemaBundle:InfoPunto')
                                                   ->findOneBy(array('login' => $arrayData['login'])); 
                
                if (!is_object($entityPunto))
                {
                    throw new \Exception('El login '.$arrayData['login'].' ingresado no existe ');
                }
                
                $objPersonaRol   =  $this->getManager()->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                 ->getPersonaRol(array('intIdPersonaRol'  => $entityPunto->getPersonaEmpresaRolId(),
                                                                                       'strEmpresaCod'    => $arrayData['codEmpresa']));
                        
                       
                if(empty($objPersonaRol))
                {
                    throw new \Exception('No existen datos de contacto para el login '.$arrayData['login'].' ingresado ');
                }
                    
                $intIdPersona = $objPersonaRol[0]['idPersona'];
                
                if(empty($arrayData['identificacion']))
                {
                   $objPersona = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->findOneById($intIdPersona);
                
                   if(!is_object($objPersona))
                   {
                     throw new \Exception("No se ha podido obtener la información de la persona ");
                   }
                  
                   $arrayData['identificacion'] =  $objPersona->getIdentificacionCliente();
                }
                
                
            }
            
            $arrayResultado  =  $serviceInfoPersonaFormaContacto->procesoFormasContacto($arrayData);
            $arrayRespuesta = $arrayResultado;
            
        } 
        catch (\Exception $ex) 
        {
            
            $serviceUtil->insertError('Telcos+',
                                      'ComercialMobileWSAppsController->actualizarFormaContacto',
                                      $ex->getMessage(),
                                      $arrayData['usuario'],
                                      $strIpCreacion);
    
            if($arrayData['origen']=="PUNTO")
            {
                $strAsuntoCorreo = "Error al actualizar los datos “Contacto punto” del cliente";
                $strWS = "Cambios en la forma de contacto Punto , op: actualizarFormaContacto ";
                $strDescripcion = 'WS que actualiza en Telcos los datos “Contacto persona" del cliente';
            }
            elseif($arrayData['origen']=="PERSONA")
            {
                $strAsuntoCorreo = "Error al actualizar los datos “Contacto persona” del cliente";
                $strWS = "Cambios en la forma de contacto Persona , op: actualizarFormaContacto";
                $strDescripcion = 'WS que actualiza en Telcos los datos “Contacto Punto" del cliente';
            }
                
                
                $arrayParametros = array('strCanal'          => $arrayData['canal'],
                                         'strWS'             => $strWS,
                                         'strDescripcion'    => $strDescripcion,
                                         'strIdentificacion' => $arrayData['identificacion'],
                                         'strOrigen'         => $arrayData['origen'],
                                         'strLogin'          => $arrayData['login'],
                                         'strError'          => $ex->getMessage(),
                                         'strTipoCorreo'     => 'CONTACTO');
                
                $servicePlantilla->generarEnvioPlantilla($strAsuntoCorreo, 
                                                         array(), 
                                                         "NOTIF_EXT_CONT", 
                                                         $arrayParametros, 
                                                         $arrayData['codEmpresa'], 
                                                         null, 
                                                         '', 
                                                         null,
                                                         false,
                                                         null);
                
    
                $arrayRespuesta['mensaje'] = $ex->getMessage();
                $arrayRespuesta['status']  = "ERROR";
            
        }
        
       return $arrayRespuesta;
        
    }
    
        
        
        

    /**
     * Escribe un archivo temporal con la data que debe estar encodada en base64,
     * devuelve la ruta del archivo creado en /tmp, con prefijo "telcosws_"
     * @param string $data (encodado en base64)
     * @return string ruta del archivo creado
     */
    private function writeTempFile($data)
    {
        $path = tempnam('/tmp', 'telcosws_');
        $ifp = fopen($path, "wb");
        if (strpos($data,',') !== false)
        {
            $data = explode(',', $data)[1];
        }
        fwrite($ifp, base64_decode($data));
        fclose($ifp);
        return $path;
    }

    /**
     * Devuelve una referencia a un archivo existente en el servidor segun la ruta dada
     * @param string $path
     * @return \Symfony\Component\HttpFoundation\File\File
     */
    private function getFile($path)
    {
        $file = new File($path);
        return $file;
    }

    /**
     * Escribe un archivo con la data base64 y devuelve una referencia al mismo
     * @param string $data (encodado en base64)
     * @return \Symfony\Component\HttpFoundation\File\File
     */
    private function writeAndGetFile($data)
    {
        if (empty($data))
        {
            return null;
        }
        return $this->getFile($this->writeTempFile($data));
    }
    
}
