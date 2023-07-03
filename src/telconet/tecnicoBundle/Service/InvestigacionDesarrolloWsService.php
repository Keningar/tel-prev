<?php

namespace telconet\tecnicoBundle\Service;

use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
class InvestigacionDesarrolloWsService
{
    /**
     *
     * @var \telconet\schemaBundle\Service\RestClientService
     */
    private $serviceRestClient;

    //Entity Manager
    private $emComercial;
    private $emGeneral;
    private $serviceUtil;
    private $serviceTecnico;
    private $objContainer;
    private $strUrlSECreacion;
    private $strUrlSEActivacionCancelacion;
    public function setDependencies(Container $objContainer)
    {
        $this->objContainer                  = $objContainer;
        $this->emComercial                   = $objContainer->get('doctrine')->getManager('telconet');
        $this->emGeneral                     = $objContainer->get('doctrine')->getManager('telconet_general');
        $this->serviceUtil                   = $objContainer->get('schema.Util');
        $this->serviceRestClient             = $objContainer->get('schema.RestClient');
        $this->strUrlSECreacion              = $objContainer->getParameter('ws_safe_entry_url_creacion');
        $this->strUrlSEActivacionCancelacion = $objContainer->getParameter('ws_safe_entry_url_activacion');
        $this->serviceTecnico                = $objContainer->get('tecnico.InfoServicioTecnico');

    }
        
    /**
     * Funcion que permite el consumo de la API de investigacion y desarrollo para el servicio SAFE ENTRY
     * 
     * @param array [objServicio
     *               strProceso => 'Creacion | Activacion | Cancelacion',
     *               strUsrCreacionn   
     *          ]
     * 
     * @return array [status => 'OK | ERROR'
     *                mensaje]
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 1.0 09-12-2022 - Version inicial
     * 
     */
    //consumoSEApp
    public function consumoSafeEntryIDWs($arrayParametros)
    {
        $objServicio    = $arrayParametros['objServicio'];
        $strProceso     = $arrayParametros['strProceso'];
        $strUsrCreacion = $arrayParametros['strUser'];
        $strIpCreacion  = $arrayParametros['strIpCreacion'];
        $strIpISB       = $arrayParametros['strIpISB'];
        $intIdEmpresa   = $arrayParametros['intIdEmpresa'];

        $objPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($objServicio->getPuntoId());
        $objPersona = $objPunto->getPersonaEmpresaRolId()->getPersonaId();
        try
        {
            if($strProceso == 'Creacion')
            {
                $strUrl = $this->strUrlSECreacion;

                //Se obtiene el CODIGO SGI
                $objServProdCarCodigo = $this->serviceTecnico->getServicioProductoCaracteristica($objServicio,
                                        'CODIGO PUNTO SGI',$objServicio->getProductoId());
                //Se obtiene el NOMBRE TORRE SAFE ENTRY
                $objServProdCarTorre = $this->serviceTecnico->getServicioProductoCaracteristica($objServicio,
                                       'NOMBRE TORRE SAFE ENTRY',$objServicio->getProductoId());
                //Se obtienen los parametros para el contacto del punto
                $arrayParametrosSafe = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->getOne('CONFIG SAFE ENTRY','COMERCIAL','',
                                                'CONTACTO_SAFE_ENTRY','','','','','',$intIdEmpresa);
                
                $arrayRoles = json_decode($arrayParametrosSafe['valor1']);
                $strIdentificacionPersona  = '';
                $strNombrePersona          = '';
                $strApellidosPersona       = '';
                $strCorreoPersona          = '';
                $strTelefonoPersona        = '';
                $arrayPersonasContacto     = array();
                $arrayDataPersonasContacto = $this->emComercial->getRepository('schemaBundle:InfoPuntoContacto')
                                             ->getPersonaContactosPorPunto(array('intIdPunto' => $objServicio->getPuntoId()->getId()));
                
                if(isset($arrayDataPersonasContacto) && !empty($arrayDataPersonasContacto) && isset($arrayDataPersonasContacto[0]))
                {
                    $arrayPersonasContacto = $arrayDataPersonasContacto[0];
                    foreach($arrayDataPersonasContacto as $arrayItemPerContacto)
                    {
                        //Se valida que de los contactos registrados en el punto, tomaremos uno de ellos que poseea los datos necesarios
                        if(in_array($arrayItemPerContacto['rol'], $arrayRoles ) && isset($arrayItemPerContacto['identificacion']))
                        {
                            $arrayPersonasContacto = $arrayItemPerContacto;
                            break;
                        }
                    }
                    $strIdentificacionPersona  = isset($arrayPersonasContacto['identificacion']) ? $arrayPersonasContacto['identificacion'] : '';
                    $strNombrePersona          = isset($arrayPersonasContacto['nombres']) ? $arrayPersonasContacto['nombres'] : '';
                    $strApellidosPersona       = isset($arrayPersonasContacto['apellidos']) ? $arrayPersonasContacto['apellidos'] : '';
                    
                    //Se obtiene el correo del contacto del punto
                    $arrayCorreoPersona =  $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                        ->getContactosByIdPersonaAndFormaContacto(array('intIdPersona'     => $arrayPersonasContacto['idPersona'],
                                                                                        'strFormaContacto' => $arrayParametrosSafe['valor2']));
                    if(isset($arrayCorreoPersona) && !empty($arrayCorreoPersona) && isset($arrayCorreoPersona[0]))
                    {
                        $strCorreoPersona = $arrayCorreoPersona[0]['valor'];
                    }
                    //Se obtiene el numero telefonico del contacto del punto
                    $arrayTelefonoPersona =  $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                        ->getContactosByIdPersonaAndFormaContacto(array('intIdPersona'     => $arrayPersonasContacto['idPersona'],
                                                                                        'strFormaContacto' => $arrayParametrosSafe['valor3']));
                    if(isset($arrayTelefonoPersona) && !empty($arrayTelefonoPersona) && isset($arrayTelefonoPersona[0]))
                    {
                        $strTelefonoPersona = $arrayTelefonoPersona[0]['valor'];
                    }
                }

                //Datos del request
                $arrayDatosConsumo = array(
                    'enterprise' => array(
                        'enterprise_name' => $objPersona->getRazonSocial() ? $objPersona->getRazonSocial()
                                             : $objPersona->getNombres().' '.$objPersona->getApellidos(),
                        'enterprise_description' => '',
                        'enterprise_ruc' => $objPersona->getIdentificacionCliente(),
                        'enterprise_address' => $objPersona->getDireccion() ? $objPersona->getDireccion() : '',
                        'user' => $strUsrCreacion
                    ),
                    'person' => array(
                        'person_identification' => $strIdentificacionPersona,
                        'person_names' => $strNombrePersona,
                        'person_surnames' => $strApellidosPersona,
                        'person_email' => $strCorreoPersona,
                        'person_phone' => $strTelefonoPersona,
                    ),
                    'company' => array(
                        'company_name' => $objPunto->getNombrePunto(),
                        'company_address' => $objPunto->getDireccion(),
                        'company_latitude' => strval($objPunto->getLatitud()),
                        'company_longitude' => strval($objPunto->getLongitud()),
                        'company_login' => $objPunto->getLogin(),
                    ),
                    'tower' => array(array(
                        'tower_location' => is_object($objServProdCarTorre) ? $objServProdCarTorre->getValor() : '',
                        'tower_latitude' => strval($objPunto->getLatitud()),
                        'tower_longitude' => strval($objPunto->getLongitud()),
                        'ip_address' => $strIpISB,
                        'tower_description' => $objPunto->getDescripcionPunto(),
                        'cod_sgi' => is_object($objServProdCarCodigo) ? intval($objServProdCarCodigo->getValor()) : 0,
                    ))
                );
            }
            if($strProceso == 'Activacion' || $strProceso == 'Cancelacion')
            {
                $strUrl = $this->strUrlSEActivacionCancelacion;
                //Datos del request
                $arrayDatosConsumo = array(
                    'enterprise' => array(
                        'enterprise_name' => $objPersona->getRazonSocial(),
                        'user' => $strUsrCreacion
                    ),
                    'company' => array(
                        'company_login' => $objPunto->getLogin(),
                        'state' => $strProceso == 'Activacion' ? 1 : 0
                    )
                );
            }
            $arrayOptions      = array(CURLOPT_SSL_VERIFYPEER => false,);
            
            $strJsonData   = json_encode($arrayDatosConsumo);
            //Request al WS de ID
            $arrayRespuestaSEAPP    = $this->serviceRestClient->postJSON($strUrl, $strJsonData, $arrayOptions);
            
            $arrayJsonRespuesta  = json_decode($arrayRespuestaSEAPP['result'], true);
            
            //Definicion del estado en base a la respuesta retornada del WS    
            if(is_array($arrayJsonRespuesta) )
            {  
                $strStatus = $arrayJsonRespuesta['success'] ? 'OK' : 'ERROR';
                $strMensaje = $arrayJsonRespuesta['message'];
            }
            else
            {
                $strStatus = "ERROR";
                $strMensaje = 'Ocurrio un problema al obtener la respuesta del WS de Investigacion y Desarrollo';
            }

            if($strStatus == 'ERROR')
            {
                //Se ingresa los datos enviados al request
                $this->serviceUtil->insertError('Telcos+',
                    'InvestigacionDesarrolloWsService->consumoSafeEntryIDWs',
                    'Datos request: '.$strJsonData,
                    $strUsrCreacion,
                    $strIpCreacion);
                //Se ingresa la respuesta recibida del WS de ID 
                $this->serviceUtil->insertError('Telcos+',
                    'InvestigacionDesarrolloWsService->consumoSafeEntryIDWs',
                    'Respuesta: '.json_encode($arrayRespuestaSEAPP),
                    $strUsrCreacion,
                    $strIpCreacion);
            }
        }
        catch(Exception $e)
        {
            $strStatus = 'ERROR';
            $strMensaje = $e->getMessage();
            $this->serviceUtil->insertError('Telcos+',
                                            'InvestigacionDesarrolloWsService->consumoSafeEntryIDWs',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);


        }
        return (array('status' => $strStatus, 'mensaje' => $strMensaje));
    }

}