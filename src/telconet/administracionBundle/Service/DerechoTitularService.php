<?php

namespace telconet\administracionBundle\Service;
use Doctrine\ORM\EntityManager;

use telconet\schemaBundle\Entity\InfoPersona;

class DerechoTitularService
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
        /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $objEmCom;
    private $objEmSoporte;
    private $objEmGeneral;
    private $objEmInfraestructura;
    /**
     *
     * @var \telconet\schemaBundle\Service\RestClientService
     */
    private $objServiceRestClient;
    private $strUrlCifrarCliente;
    private $strUrlValidarClienteGeneral;
    private $strUrlValidarClienteIdentificacion;
    private $strUrlValidacionCifrado;
    private $strUrlEnvioLink;
    private $strParametroWhatsapp;
    private $objTokenCas;
    /**
     * @var \Symfony\Component\DependencyInjection\objContainerInterface
     */
    private $objContainer;

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer) 
    {
        $this->objContainer                       = $objContainer;
        $this->em                                 = $objContainer->get('doctrine.orm.telconet_entity_manager');     
        $this->objEmCom                           = $objContainer->get('doctrine.orm.telconet_entity_manager');
        $this->objEmSoporte                       = $objContainer->get('doctrine.orm.telconet_soporte_entity_manager');
        $this->objEmGeneral                       = $objContainer->get('doctrine.orm.telconet_general_entity_manager');
        $this->objEmInfraestructura               = $objContainer->get('doctrine.orm.telconet_infraestructura_entity_manager');
        $this->objServiceRestClient               = $objContainer->get('schema.RestClient');
        $this->strUrlCifrarCliente                = $objContainer->getParameter('ms-comp-credenciales-comercial_encrypt');
        $this->strUrlValidarClienteGeneral        = $objContainer->getParameter('ms-comp-credenciales-comercial_validar_general');
        $this->strUrlValidarClienteIdentificacion = $objContainer->getParameter('ms-comp-credenciales-comercial_validar_usuario');
        $this->objTokenCas                        = $objContainer->get('seguridad.TokenCas');
        $this->strUrlDesCifrarCliente             = $objContainer->getParameter('ms-comp-credenciales-comercial_unencrypt');
        $this->strUrlValidacionCifrado            = $objContainer->getParameter('ms-comp-credenciales-comercial_validar_cifrado');
        $this->strParametroWhatsapp               = $objContainer->getParameter('ms-comp-credenciales-comercial_parametro_whatsapp');
        $this->strUrlEnvioLink                    = $objContainer->getParameter('ms-comp-credenciales-comercial_envio_url');
    }

    /**
     * 
     * Documentación para el método 'crearAsignacionVehicularPredefinida'.
     *
     * Creación de una nueva asignación Vehicular Predefinida 
     *
     * @return Response 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 07-04-2016
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 23-08-2016 Se realizan ajustes para guardar los horarios de la asignación 
     * 
     */ 
    public function validarIdentificacion($arrayParametros)
    {
        $objInfoPersonaRepository = $this->objEmCom->getRepository('schemaBundle:InfoPersona');
        $objInfoPersonaCaracteristicaRepository = $this->objEmCom->getRepository(
                                                                'schemaBundle:InfoPersonaEmpresaRolCarac'
                                                            );
        $arrayPeticion = [];
        $arrayRespuesta = [];
            try
            {
                if($arrayParametros['tipoIdentificacion']=='CEDULA DE IDENTIDAD')
                {
                    $arrayPeticion['tipoIdentificacion']='CED';
                }
                if($arrayParametros['tipoIdentificacion']=='RUC')
                {
                    $arrayPeticion['tipoIdentificacion']='RUC';
                }
                if($arrayParametros['tipoIdentificacion']=='PASAPORTE')
                {
                    $arrayPeticion['tipoIdentificacion']='PAS';
                }
                $arrayPeticion['identificacion']=$arrayParametros['identificacion'];
                $arrayPersonaBuscada = $objInfoPersonaRepository->getPersonaPorIdentificacion($arrayPeticion);
                if(count($arrayPersonaBuscada)>0)
                {
                    $arrayPersonaCaracteristica = $objInfoPersonaCaracteristicaRepository->
                                                        getCaracteristicaClienteIdentificacion($arrayPeticion);
                    if(count($arrayPersonaCaracteristica['result'])<1)
                    {
                        $arrayRespuesta['valido']=true;
                        $arrayRespuesta['mensaje']='';
                    }else
                    {
                        $arrayRespuesta['valido']=false;
                        $arrayRespuesta['mensaje']='El cliente ya se encuentra eliminado/encriptado';
                    }
                }else
                {
                    $arrayRespuesta['valido']=false;
                    $arrayRespuesta['mensaje']='La identificacion no pertenece a un cliente.';
                }
            }
            catch (Exception $ex) 
            {
                $arrayRespuesta['valido']=false;
                $arrayRespuesta['mensaje']='Existio un error al validar la identificacion: '.$ex;
            }
        return $arrayRespuesta;
    }

    /**
     * 
     * Documentación para el método 'crearAsignacionVehicularPredefinida'.
     *
     * Creación de una nueva asignación Vehicular Predefinida 
     *
     * @return Response 
     * 
     * @author Eduardo Montengro <emontenegro@telconet.ec>
     * @version 1.0 16/01/2023
     *
     */ 
    public function cifrarCliente($arrayParametros)
    {
        $objInfoPersonaRepository = $this->objEmCom->getRepository('schemaBundle:InfoPersona');
        $arrayPeticion = $arrayParametros;
        $arrayRespuesta = [];
        if($arrayParametros['tipoIdentificacion']=='CEDULA DE IDENTIDAD')
        {
            $arrayPeticion['tipoIdentificacion']='CED';
        }
        if($arrayParametros['tipoIdentificacion']=='RUC')
        {
            $arrayPeticion['tipoIdentificacion']='RUC';
        }
        if($arrayParametros['tipoIdentificacion']=='PASAPORTE')
        {
            $arrayPeticion['tipoIdentificacion']='PAS';
        }
        $arrayPeticion['infoLog']['identificacion']=$objInfoPersonaRepository->
                                                    find($arrayPeticion['infoLog']['identificacion'])->
                                                    getIdentificacionCliente();
        $objOptions = array(CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                        'tokencas: '.$this->objTokenCas->generarTokenCas()['strToken'])
        );
        $strJsonRequest = json_encode($arrayPeticion);
        $arrayResponseJson  = $this->objServiceRestClient->postJSON(
                                                            $this->strUrlValidarClienteIdentificacion ,
                                                            $strJsonRequest, $objOptions);
        $arrayValidacionIdentificacion =json_decode($arrayResponseJson['result'],true)['data'];
        if(!isset($arrayValidacionIdentificacion) || 
            empty($arrayValidacionIdentificacion))
        {
            return [
                'valido'=>false,
                'mensaje'=>'No se pudo comunicar con los servicios implicados'
            ];
        }
        if($arrayValidacionIdentificacion['cliente'])
        {
            $objOptions = array(CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                            'tokencas: '.$this->objTokenCas->generarTokenCas()['strToken'])
            ); 
            $strJsonRequest = json_encode($arrayPeticion);
            $arrayResponseJson  = $this->objServiceRestClient->postJSON(
                                                                    $this->strUrlValidarClienteGeneral ,
                                                                    $strJsonRequest, $objOptions);
            $arrayValidacionesGenerales =json_decode($arrayResponseJson['result'], true)['data'];
            if($arrayValidacionesGenerales['cliente'])
            {
                if(!$arrayValidacionesGenerales['encriptado'])
                {
                    if(!$arrayValidacionesGenerales['servicios'])
                    {
                        if(!$arrayValidacionesGenerales['deudas'])
                        {
                            if(!$arrayValidacionesGenerales['entregasPendientes'])
                            {
                                $objOptions         = array(CURLOPT_SSL_VERIFYPEER => false,
                                CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                                                'tokencas: '.
                                                                $this->objTokenCas->generarTokenCas()['strToken'])
                                );
                                $strJsonRequest = json_encode($arrayPeticion);
                                $arrayResponseJson  = $this->objServiceRestClient->postJSON(
                                                                                    $this->strUrlCifrarCliente ,
                                                                                    $strJsonRequest, $objOptions);
                                $arrayCifrado = json_decode($arrayResponseJson['result'], true);
                                if($arrayCifrado['status']=='OK')
                                {
                                    $arrayRespuesta['valido']=true;
                                    $arrayRespuesta['mensaje']='El cliente se cifro correctamente';
                                }else
                                {
                                    $arrayRespuesta['valido']=false;
                                    $arrayRespuesta['mensaje']=$arrayCifrado['message'];
                                }
                            }else
                            {
                                $arrayRespuesta['valido']=false;
                                $arrayRespuesta['mensaje']=$arrayValidacionesGenerales['mensaje'];
                            }
                        }else
                        {
                            $arrayRespuesta['valido']=false;
                            $arrayRespuesta['mensaje']=$arrayValidacionesGenerales['mensaje'];
                        }
                    }else
                    {
                        $arrayRespuesta['valido']=false;
                        $arrayRespuesta['mensaje']=$arrayValidacionesGenerales['mensaje'];
                    }
                }else
                {
                    $arrayRespuesta['valido']=false;
                    $arrayRespuesta['mensaje']=$arrayValidacionesGenerales['mensaje'];
                }
            }else
            {
                $arrayRespuesta['valido']=false;
                $arrayRespuesta['mensaje']=$arrayValidacionesGenerales['mensaje'];
            }
        }else
        {
            $arrayRespuesta['valido']=false;
            $arrayRespuesta['mensaje']=$arrayValidacionIdentificacion['mensaje'];
        }
        if(!isset($arrayRespuesta['mensaje']) ||
           empty($arrayRespuesta['mensaje']))
        {
            $arrayRespuesta['mensaje']='No se pudo comunicar con los servicios implicados';
        }
        return $arrayRespuesta;
    }

    /**
     * 
     * Documentación para el método 'descifrarCliente'.
     *
     * Valida la identificacion y de ser posibla descifra al cliente
     *
     * @return Response 
     * 
     * @author Eduardo Montengro <emontenegro@telconet.ec>
     * @version 1.0 16/01/2023
     *
     */ 
    public function descifrarCliente($arrayParametros)
    {
        $objInfoPersonaRepository = $this->objEmCom->getRepository('schemaBundle:InfoPersona');
        $objInfoPersonaCaracteristicaRepository = $this->objEmCom->getRepository(
                                                                'schemaBundle:InfoPersonaEmpresaRolCarac'
                                                            );
        $arrayPeticion = $arrayParametros;
        $arrayRespuesta = [];
        if($arrayParametros['tipoIdentificacion']=='CEDULA DE IDENTIDAD')
        {
            $arrayPeticion['tipoIdentificacion']='CED';
        }
        if($arrayParametros['tipoIdentificacion']=='RUC')
        {
            $arrayPeticion['tipoIdentificacion']='RUC';
        }
        if($arrayParametros['tipoIdentificacion']=='PASAPORTE')
        {
            $arrayPeticion['tipoIdentificacion']='PAS';
        }
        $arrayPeticion['infoLog']['identificacion']=$objInfoPersonaRepository->
                                                    find($arrayPeticion['infoLog']['identificacion'])->
                                                    getIdentificacionCliente();
        $objOptions = array(CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                        'tokencas: '.$this->objTokenCas->generarTokenCas()['strToken'])
        );
        $strJsonRequest = json_encode($arrayPeticion);
        $arrayResponseJson  = $this->objServiceRestClient->postJSON(
                                                            $this->strUrlValidacionCifrado ,
                                                            $strJsonRequest ,$objOptions);
        $arrayValidacionIdentificacion =json_decode($arrayResponseJson['result'],true)['data'];
        if(!isset($arrayValidacionIdentificacion['encriptado']))
        {
            return [
                'valido'=>false,
                'mensaje'=>'Error de comunicación con los microservicios'
            ];
        }
        if($arrayValidacionIdentificacion['encriptado'])
        {
            $objOptions = array(CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                        'tokencas: '.$this->objTokenCas->generarTokenCas()['strToken'])
            );
            $strJsonRequest = json_encode($arrayPeticion);
            $arrayResponseJson  = $this->objServiceRestClient->postJSON(
                                                            $this->strUrlDesCifrarCliente,
                                                            $strJsonRequest, $objOptions);
            $arrayRespuestaDescifrar =json_decode($arrayResponseJson['result'], true);
            if(!isset($arrayRespuestaDescifrar) ||
               !isset($arrayRespuestaDescifrar['data']))
               {
                return [
                    'valido'=>false,
                    'mensaje'=>'Error de comunicacion con los microservicios'
                ];
            }
            if(isset($arrayRespuestaDescifrar["data"])
            && $arrayRespuestaDescifrar["data"]["procesoDescifrar"] )
            {
                $arrayRespuesta['valido']=true;
                $arrayRespuesta['mensaje']='Se descifro correctamente el cliente';
            }else
            {
                $arrayRespuesta['valido']=false;
                $arrayRespuesta['mensaje']=$arrayRespuestaDescifrar["data"]["mensaje"];
            }
        }else
        {
            $arrayRespuesta['valido']=false;
            $arrayRespuesta['mensaje']='La identificación no pertenece a un cliente encriptado/eliminado.';
        }
        return $arrayRespuesta;
    }

    /**
     * 
     * Documentación para el método 'getParametroWhatsapp'.
     *
     * Retorna el parametro relacionado a whastapp
     *
     * @return Response 
     * 
     * @author Eduardo Montengro <emontenegro@telconet.ec>
     * @version 1.0 16/01/2023
     *
     */ 
    public function getParametroWhatsapp()
    {
        $objOptions = array(CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                        'tokencas: '.$this->objTokenCas->generarTokenCas()['strToken'])
        );
        $strJsonRequest = json_encode($arrayPeticion);
        $arrayResponseJson  = $this->objServiceRestClient->getJSON(
                                                            $this->strParametroWhatsapp, $objOptions);
        return $arrayResponseJson;
    }

    /**
     *
     * Documentación para el método 'enviarLink'.
     *
     * Retorna el parametro relacionado a whastapp
     *
     * @return Response
     *
     * @author Eduardo Montengro <emontenegro@telconet.ec>
     * @version 1.0 16/01/2023
     *
     */
    public function enviarLink($arrayParametros)
    {
        $objInfoPersonaRepository = $this->objEmCom->getRepository('schemaBundle:InfoPersona');
        $arrayPeticion = $arrayParametros;
        $arrayRespuesta = [];
        if($arrayParametros['tipoIdentificacion']=='CEDULA DE IDENTIDAD')
        {
            $arrayPeticion['tipoIdentificacion']='CED';
        }
        if($arrayParametros['tipoIdentificacion']=='RUC')
        {
            $arrayPeticion['tipoIdentificacion']='RUC';
        }
        if($arrayParametros['tipoIdentificacion']=='PASAPORTE')
        {
            $arrayPeticion['tipoIdentificacion']='PAS';
        }
        $arrayPeticion['infoLog']['identificacion']=$objInfoPersonaRepository->
                                                    find($arrayPeticion['infoLog']['identificacion'])->
                                                    getIdentificacionCliente();
        $objOptions = array(CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                        'tokencas: '.$this->objTokenCas->generarTokenCas()['strToken'])
        );
        $strJsonRequest = json_encode($arrayPeticion);
        $arrayResponseJson  = $this->objServiceRestClient->postJSON(
                                                            $this->strUrlValidacionCifrado ,
                                                            $strJsonRequest ,$objOptions);
        $arrayValidacionIdentificacion =json_decode($arrayResponseJson['result'],true)['data'];
        if(empty($arrayValidacionIdentificacion))
        {
            return [
                'valido'=>false,
                'mensaje'=>'No se pudo contactar con los microservicios'
                ];
        }
        if($arrayValidacionIdentificacion['encriptado'])
        {
            $objOptions = array(CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                        'tokencas: '.$this->objTokenCas->generarTokenCas()['strToken'])
            );
            $strJsonRequest = json_encode($arrayPeticion);
            $arrayResponseJson  = $this->objServiceRestClient->postJSON(
                                                            $this->strUrlEnvioLink,
                                                            $strJsonRequest, $objOptions);
            $arrayRespuestaDescifrar =json_decode($arrayResponseJson['result'], true);
            //generarLink
            if($arrayRespuestaDescifrar['data']['generarLink'])
            {
                if($arrayRespuestaDescifrar['data']['enviarLink'])
                {
                    $arrayRespuesta = [
                        'valido'=>false,
                        'mensaje'=>'El link fue generado y enviado correctamente'
                    ];
                }else
                {
                    $arrayRespuesta = [
                        'valido'=>false,
                        'mensaje'=>'No se pudo enviar el link para el proceso de descifrado'
                    ];
                }
            }else
            {
                $arrayRespuesta = [
                    'valido'=>false,
                    'mensaje'=>'No se pudo generar el link para el proceso de descifrado'
                ];
            }
        }else
        {
            $arrayRespuesta = [
                'valido'=>false,
                'mensaje'=>$arrayValidacionIdentificacion['mensaje']
            ];
        }
        return $arrayRespuesta;
    }

}
