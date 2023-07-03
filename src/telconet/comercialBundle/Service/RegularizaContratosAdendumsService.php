<?php

namespace telconet\comercialBundle\Service;

use Doctrine\ORM\EntityManager;

use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\schemaBundle\Entity\InfoCertificado;
use telconet\schemaBundle\Entity\InfoCertificadoDocumento;
use \PHPExcel_IOFactory;

class RegularizaContratosAdendumsService {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emcom;
    private $emGeneral;
    private $serviceEnvioPlantilla;
    private $strUrlSecuridyData;

    /**
     *
     * @var type Metodo para firmar certificado digital
     */
    private $strOpcionFirmarCertificadoDigital;
    
    /**
     *
     * @var type usuario para hacer uso del WS Certificacion de Documentos
     */
    private $strLoginCertificadoDigital;

    /**
     *
     * @var type Clave para hacer uso del WS Certificacion de documentos
     */
    private $strPassCertificadoDigital;

    private $emComunicacion;

    private $serviceCrypt;
    
    private $emInfraestructura;

    private $emFirmaElect;

    private $strPathTelcos;

    private $strRutaCertificado;

    private $serviceRestClient;

    private $utilService;

    private $strWsFirmaDigital;
    private $strContratosDirectorio;

    private $strContratoDigital;

    private $serviceCertificacionDocumentos;
    private $intLongitudPass;
    private $intDiasValidezCertificado;
    private $strStatusOk;
    private $intWsContratoDigitalTimeOut;
    private $strWsContratoDigitalUrl;
    private $strGruposPertenencia;
    private $strExtArchivo;
    private $strRutaDocCertificado;
    private $serviceServiciotecnico;
        

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emcom                                 = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emComercial                           = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emGeneral                             = $container->get('doctrine.orm.telconet_general_entity_manager');
        $this->emComunicacion                        = $container->get('doctrine.orm.telconet_comunicacion_entity_manager');
        $this->emInfraestructura                     = $container->get('doctrine.orm.telconet_infraestructura_entity_manager');
        $this->serviceEnvioPlantilla                 = $container->get('soporte.EnvioPlantilla');
        $this->strOpcionFirmarCertificadoDigital     = $container->getParameter('ws_contrato_digital_op_firmar');
        $this->strLoginCertificadoDigital            = $container->getParameter('ws_contrato_digital_id');
        $this->serviceCrypt                          = $container->get('seguridad.crypt');
        $this->emFirmaElect                          = $container->get('doctrine.orm.telconet_firmaelect_entity_manager');        
        $this->strPathTelcos                         = $container->getParameter('path_telcos');
        $this->strRutaCertificado                    = $container->getParameter('ruta_certificados_digital');
        $this->serviceRestClient                     = $container->get('schema.RestClient');
        $this->utilService                           = $container->get('schema.Util');
        $this->strWsFirmaDigital                     = $container->getParameter('ws_firma_digital');
        $this->strContratosDirectorio                = $container->getParameter('contrato_digital_ruta');        
        $this->strUrlSecuridyData                    = $container->getParameter('ws_security_data_url');        
        $this->serviceCertificacionDocumentos        = $container->get('comercial.CertificacionDocumentos');     
        $this->intDiasValidezCertificado   = $container->getParameter('certificado_num_dias_vigencia');
        $this->strStatusOk                 = $container->getParameter('ws_contrato_digital_status_ok');
        $this->intWsContratoDigitalTimeOut = $container->getParameter('ws_contrato_digital_timeout');
        $this->strWsContratoDigitalUrl     = $container->getParameter('ws_security_data_url');
        $this->strGruposPertenencia        = $container->getParameter('certificado_grupos_pertenencia');
        $this->strExtArchivo               = $container->getParameter('certificado_ext_archivo');
        $this->strRutaDocCertificado       = $container->getParameter('ruta_certificados_documentos');
        $this->intLongitudPass             = $container->getParameter('certificado_longitud_pass');
        $this->serviceServiciotecnico                = $container->get('tecnico.InfoServicioTecnico');
    }

    /**
     *
     * Método para firmar documentos que se van a regularizar 
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0
     * @since 08-07-2020
     *
     * @param array $arrayDataRequest[intIdentificacion]
     * @return object $objContrato
     */
    public function firmarDocumentosNewRegularizacion($arrayDataRequest, $output)
    {
        $objContrato                      = $arrayDataRequest['contrato'];

        $arrayRetorno                     = array();
        $arrayRetorno['salida']           = 1;
        $strOpcionFuncion                 = $this->strOpcionFirmarCertificadoDigital;
        $booleanAsync                     = false;
        $arrayEmpresaPeticion['code']     = $this->strLoginCertificadoDigital;
        $arrayEmpresaPeticion['password'] = $this->strPassCertificadoDigital;
        try
        {  

            if(!$objContrato)
            {
                $arrayRetorno['mensaje'] = "Contrato no cuenta con un documento id valido";
                $arrayRetorno['salida']  = 0;
                return $arrayRetorno;
            }
            if(is_object($objContrato))
            {
                $arrayDatos                     = $this->obtenerDatos($objContrato);
                $arrayDatos["codEmpresa"]       = $arrayDataRequest["strCodEmpresa"];
                $arrayDatos["strTipo"]          = $arrayDataRequest["strTipo"];
                $arrayDatos["strNumeroAdendum"] = $arrayDataRequest["strNumeroAdendum"];
                $arrayDatos["regulariza"]       = $arrayDataRequest["regulariza"];
                $arrayDatos["intContratoId"]    = $objContrato->getId();
                
                $arrayDatosPersona               = $this->obtenerDatosDocumentarCertificado($objContrato,$arrayDatos, $output);
                $arrayDatosPersona['pinCode']    = $arrayDataRequest['pincode'];
                $arrayDatosPersona["regulariza"] = $arrayDataRequest["regulariza"];
                $arrayContratoEmpresa            = $this->crearContratoEmp($arrayDatosPersona);
                $arrayContratoEntidadEmisora     = $this->crearContratoSd($arrayDatosPersona);
                $arrayFormularioEntidadEmisora   = $this->crearFormularioSD($arrayDatosPersona);
                $arrayDebito                     = $this->crearDebitoEmp($arrayDatosPersona);
                $arrayPagare                     = $this->crearPagareEmp($arrayDatosPersona);

                if($arrayDataRequest['strTipo'] == "AS")
                {
                    $arrayAdendumAdicional     = $this->crearAdendumAdicional($arrayDatosPersona);
                }
                
                $strRutaRubricaTMP             = explode('.', $arrayDatosPersona['rutaRubrica'])[0].'.png';            
                
                $arrayValorCertificado = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('CERTIFICADO_DIGITAL',
                                                                'COMERCIAL',
                                                                '',
                                                                'CERTIFICADO_SD',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                '18');
                if(isset($arrayValorCertificado['valor1']))
                {
                    $strCertSd = $arrayValorCertificado['valor1'];
                }
                
                $arrayValorCertMd = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('CERTIFICADO_DIGITAL',
                                                                    'COMERCIAL',
                                                                    '',
                                                                    'CERTIFICADO_MD',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '18');
                if(isset($arrayValorCertMd['valor1']))
                {
                    $strCertMd = $arrayValorCertMd['valor1'];
                }  

                $arrayParametroConsumo = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('CONFIGURACION_WS_SD',
                                                                'COMERCIAL',
                                                                '',
                                                                'PARAMSQUERY',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                '18');
                
                $arrayCertificado = $this->emFirmaElect->getRepository('schemaBundle:InfoCertificado')
                                                    ->findCertificado(array("strNumCedula"  => $arrayDatosPersona['cedula'],
                                                                            "strCodEmpresa" => '18',
                                                                            "strEstado"     => "valido")); 
                $arrayCertificadoSd = $this->emFirmaElect->getRepository('schemaBundle:InfoCertificado')
                                                        ->findCertificado(array("strNumCedula"  => $strCertSd,
                                                                                "strCodEmpresa" => '18',
                                                                                "strEstado"     => "valido")); 
                
                $arrayCertificadoMd = $this->emFirmaElect->getRepository('schemaBundle:InfoCertificado')
                                            ->findCertificado(array("strNumCedula"  => $strCertMd,
                                                                    "strCodEmpresa" => '18',
                                                                    "strEstado"     => "valido")); 
                if ($arrayCertificado < 1)
                {
                    $arrayRetorno['mensaje'] = "No se encuentra el certificado";
                    $arrayRetorno['salida']  = 0;
                    return $arrayRetorno;
                }

                $strFecha2    = $arrayCertificado[0]['feRuta'];
                $strSerial    = $arrayCertificado[0]['feRuta'];
                $strExtension = '.pfx';
                
                if($arrayParametroConsumo['estado'] && $arrayParametroConsumo['estado'] == "Activo")
                {
                    $strExtension = "." . $arrayParametroConsumo['valor3'];
                    $strSerial    = $arrayCertificado[0]['serialNumber'];
                }                        
                
                $strCertificado = $this->strPathTelcos . $this->strRutaCertificado . 
                $strFecha2 . "/" . $arrayDatosPersona['cedula'] . '_' . $strSerial .  $strExtension;

                $strCertificado64 = base64_encode(file_get_contents($strCertificado));

                $arrayCertificadoContratoSd = $this->generaPropiedadesPlantilla(array("strPlantilla"       => "contratoSecurityData",
                                                                                    "arrayCertificado"   => $arrayCertificado[0],
                                                                                    "arrayCertificadoSd" => $arrayCertificadoSd[0],
                                                                                    "strCertificado64"   => $strCertificado64)  ); 

                $arrayCertificadoFormulario = $this->generaPropiedadesPlantilla(array("strPlantilla"       => "formularioSecurityData",
                                                                                    "arrayCertificado"   => $arrayCertificado[0],
                                                                                    "arrayCertificadoSd" => $arrayCertificadoSd[0],
                                                                                    "strCertificado64"   => $strCertificado64)  ); 

                $arrayCertificadoDebito     = $this->generaPropiedadesPlantilla(array("strPlantilla"       => "debitoMegadatos",
                                                                                    "arrayCertificado"   => $arrayCertificado[0],
                                                                                    "arrayCertificadoSd" => $arrayCertificadoSd[0],
                                                                                    "strCertificado64"   => $strCertificado64)  ); 

                $arrayCertificadoPagare    = $this->generaPropiedadesPlantilla(array("strPlantilla"       => "pagareMegadatos",
                                                                                    "arrayCertificado"   => $arrayCertificado[0],
                                                                                    "arrayCertificadoSd" => $arrayCertificadoSd[0],
                                                                                    "strCertificado64"   => $strCertificado64)  ); 

                if ( $arrayDataRequest['strTipo'] == "AS")
                {
                    $arrayCertificadoContratoEmpresa = $this->generaPropiedadesPlantilla(array("strPlantilla"       => "adendumMegaDatos",
                                                                                            "arrayCertificado"   => $arrayCertificado[0],
                                                                                            "arrayCertificadoSd" => $arrayCertificadoMd[0],
                                                                                            "strCertificado64"   => $strCertificado64)  );
                    
                    $arrayData           = array(
                        'async'     => $booleanAsync,
                        'cedula'    => $arrayDatosPersona['cedula'],
                        'rubrica'   => $this->codificarDocumentos($strRutaRubricaTMP),
                        'list'      => array(
                                                $this->crearPlantillaDocumentoNew(array("arrayValores"       => $arrayAdendumAdicional,
                                                                                        "strNombrePlantilla" => "adendumMegaDatos",
                                                                                        "arrayCertificado"   => $arrayCertificadoContratoEmpresa)),
                                                $this->crearPlantillaDocumentoNew(array("arrayValores"       => $arrayContratoEntidadEmisora ,
                                                                                        "strNombrePlantilla" => "contratoSecurityData",
                                                                                        "arrayCertificado"   =>$arrayCertificadoContratoSd)),
                                                $this->crearPlantillaDocumentoNew(array("arrayValores"       => $arrayFormularioEntidadEmisora,
                                                                                        "strNombrePlantilla" => "formularioSecurityData",
                                                                                        "arrayCertificado"   => $arrayCertificadoFormulario)),
                                                $this->crearPlantillaDocumentoNew(array("arrayValores"       => $arrayPagare,
                                                                                        "strNombrePlantilla" =>  "pagareMegadatos",
                                                                                        "arrayCertificado"   => $arrayCertificadoPagare))

                                            )
                        );

                }
                else
                {

                    $arrayCertificadoContratoEmpresa = $this->generaPropiedadesPlantilla(array("strPlantilla"       => "contratoMegadatos",
                                                                                    "arrayCertificado"   => $arrayCertificado[0],
                                                                                    "arrayCertificadoSd" => $arrayCertificadoMd[0],
                                                                                    "strCertificado64"   => $strCertificado64)  ); 


                    $arrayData           = array(
                                            'async'     => $booleanAsync,
                                            'cedula'    => $arrayDatosPersona['cedula'],
                                            'rubrica'   => $this->codificarDocumentos($strRutaRubricaTMP),
                                            'list'      => array(
                                                                $this->crearPlantillaDocumentoNew(array("arrayValores"       => $arrayContratoEmpresa,
                                                                                            "strNombrePlantilla" => "contratoMegadatos",
                                                                                            "arrayCertificado" => $arrayCertificadoContratoEmpresa)),
                                                                $this->crearPlantillaDocumentoNew(array("arrayValores" => $arrayContratoEntidadEmisora ,
                                                                                            "strNombrePlantilla"    => "contratoSecurityData",
                                                                                            "arrayCertificado"      =>$arrayCertificadoContratoSd)),
                                                                $this->crearPlantillaDocumentoNew(array("arrayValores" =>$arrayFormularioEntidadEmisora,
                                                                                                "strNombrePlantilla" => "formularioSecurityData",
                                                                                                "arrayCertificado" =>$arrayCertificadoFormulario)),
                                                                $this->crearPlantillaDocumentoNew(array("arrayValores" => $arrayDebito,
                                                                                                "strNombrePlantilla" => "debitoMegadatos",
                                                                                                "arrayCertificado" => $arrayCertificadoDebito)),
                                                                $this->crearPlantillaDocumentoNew(array("arrayValores" => $arrayPagare,
                                                                                                "strNombrePlantilla" =>  "pagareMegadatos",
                                                                                                "arrayCertificado" => $arrayCertificadoPagare))
                                                                )
                                            );

                }
            
                $arrayDataFirmaDocumentos = json_encode(array(
                                                            'op'   => $strOpcionFuncion,
                                                            'data' => $arrayData
                                                            )
                                                    );
                $arrayRest                  = array();
                $arrayRest[CURLOPT_TIMEOUT] = $this->intWsContratoDigitalTimeOut;

                $arrayResponseWS            = $this->serviceRestClient->postJSON($this->strWsFirmaDigital, $arrayDataFirmaDocumentos, $arrayRest);

                $arrayRetorno['salida']  = 0;
                $arrayRetorno['mensaje'] = "No fue posible firmar documentos, se presentó un error inesperado";
                
                if ($arrayResponseWS['status'] == "200")
                {
                    $arrayStatus = (array) json_decode($arrayResponseWS['result']);
                    $arrayStatus = (array) json_decode($arrayStatus[0]);
                    $arrayEnviaDocumentos = $arrayStatus['enviaMails'];
                    
                    if ($arrayStatus['cod'] == "200" && empty($arrayStatus['err'])) 
                    {
                        

                        $arrayRetorno['salida']          = 1;
                        $arrayRetorno['arrDatos']        = $arrayDatos;
                        $arrayRetorno['arrInfo']         = $arrayDatosPersona;
                        $arrayRetorno['objContrato']     = $objContrato;
                        $arrayRetorno['documentos']      = $arrayStatus['documentos'];
                        $arrayRetorno['mensaje']         = $arrayStatus['resp'];
                        $arrayRetorno['enviaMails']      = $arrayEnviaDocumentos;    
                    }
                    else
                    {
                        throw new \Exception("Error al enviar petición a firmar documentos (ms-core-com-firma-docs)" . "\n\n" .
                            json_encode($arrayStatus['err'])); 
                    }

                }
                else
                {
                    throw new \Exception("Error al enviar petición a firmar documentos (ms-core-com-firma-docs)" . "\n\n" .
                        json_encode($arrayResponseWS)); 
                }
            }
            else
            {
                $arrayRetorno['mensaje'] = "No se ha documentado el certificado digital para el contrato consultado";
                $arrayRetorno['salida']  = 0;
            }
        }
        catch(\Exception $e)
        {

            $arrayRetorno['mensaje'] = "Error en proceso de firmar documentos, no se pudo cambiar estado al contrato";

            throw $e;
        }   
        return $arrayRetorno;

    }

    /**
     * obtenerDatos, método que obtiene todos los datos necesarios para ser enviados al webservice,
     * para la creación del certificado digital de regularizacion
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 21-07-2017
     * 
     */
    public function obtenerDatos($objContrato)
    {
        try
        {
            $arrayRespuesta               = array();

            //Arreglo de tildes y eñes
            $arrayTildes                  = array('á','é','í','ó','ú','â','ê','î','ô','û','ã','õ','ç','ñ','Á','É','Í','Ó','Ú',
                                                  'Â','Ê','Î','Ô','Û','Ã','Õ','Ç','Ñ','ä','ë','ï','ö','ü','Ä','Ë','Ï','Ö','Ü',
                                                  'à','è','ì','ò','ù','À','È','Ì','Ò','Ù');
            $arraySinTilde                = array('a','e','i','o','u','a','e','i','o','u','a','o','c','n','A','E','I','O','U',
                                                  'A','E','I','O','U','A','O','C','N','a','e','i','o','u','A','E','I','O','U',
                                                  'a','e','i','o','u','A','E','I','O','U');
    
            $arrayRespuesta['cedula']     = $objContrato->getPersonaEmpresaRolId()->getPersonaId()->getIdentificacionCliente();
            $arrayRespuesta['nombres']    = str_replace(
                                                        $arrayTildes,
                                                        $arraySinTilde,
                                                        $objContrato->getPersonaEmpresaRolId()->getPersonaId()->getNombres()
                                                       );
            $arrayApellidos               = explode(" ", $objContrato->getPersonaEmpresaRolId()->getPersonaId()->getApellidos());
            $arrayRespuesta['pApell']     = str_replace($arrayTildes,$arraySinTilde,$arrayApellidos[0]);
            $arrayRespuesta['sApell']     = str_replace($arrayTildes,$arraySinTilde,$arrayApellidos[1]);
            $arrayRespuesta['dir']        = str_replace(
                                                        $arrayTildes,
                                                        $arraySinTilde,
                                                        $objContrato->getPersonaEmpresaRolId()->getPersonaId()->getDireccionTributaria()
                                                       );
            $arrayRespuesta['fecha']      = $objContrato->getFeFinContrato();
            $arrayFormaContacto           = $this->obtenerFormasDeContactoByPersonaId($objContrato->getPersonaEmpresaRolId()->getPersonaId()->getId());
            
            if($arrayFormaContacto)
            {
                $arrayCelular                 = explode("-", $arrayFormaContacto['celular']);
                $arrayRespuesta['email']      = $arrayFormaContacto['correo'];
                $arrayRespuesta['telf']       = $arrayCelular[0];
            }
            
            if(!isset($arrayRespuesta['email']))
            {
                $arrayRespuesta['email']  = "notiene@dominio.com";
            }
            
            if(!isset($arrayRespuesta['sApell']))
            {
                $arrayRespuesta['sApell']     = "";
            }
            
            if(!isset($arrayRespuesta['telf']))
            {
                $arrayRespuesta['telf']   = "9999999999";
            }
            $intIdRol       = $objContrato->getPersonaEmpresaRolId()->getEmpresaRolId()->getRolId();
            $objRol         = $this->emComercial->getRepository('schemaBundle:AdmiRol')->findOneById($intIdRol);
            $objInfoPunto   = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                                                ->findOneBy(array('personaEmpresaRolId'=>$objContrato->getPersonaEmpresaRolId())); 
           
            $objCanton      = $this->emGeneral->getRepository('schemaBundle:AdmiCanton')
                                              ->findOneById($objInfoPunto->getSectorId()->getParroquiaId()->getCantonId()->getId());
            $arrayRespuesta['ciudad']     = $objCanton->getNombreCanton();
            $arrayRespuesta['provincia']  = $objCanton->getProvinciaId()->getNombreProvincia();
            $arrayRespuesta['pais']       = $objCanton->getProvinciaId()->getRegionId()->getPaisId()->getNombrePais();
            $arrayRespuesta['fact']       = $objContrato->getNumeroContrato();
            $arrayRespuesta['pass']       = "password";
            $arrayRespuesta['objPunto']   = $objInfoPunto;
    
        }
        catch (\Exception $ex)
        {       
            throw $ex;
        }        
        return $arrayRespuesta;
    }

    /**
     * obtenerFormasDeContactoByPersonaId, método que me retorna todos los tipos de contactos que
     * tiene una persona para regularizar contratos
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 17-07-2020
     * @param objeto $intPersonaId, Objeto de tipo Persona
     * @return array $arrayContactos, retorna un arreglo con la siguiente información:
     *         $arrayContactos['telefono'] => Cadena con los números de teléfono fijos
     *         $arrayContactos['celular']  => Cadena con los números de teléfono de las diferentes operadoras
     *                                      de celulares
     *         $arrayContactos['correo']   => Correo electrónico de la persona
     */
    private function obtenerFormasDeContactoByPersonaId($intPersonaId)
    {
        try
        {
            $arrayDataFormaContactoCliente = $this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                            ->findBy(array('personaId'    => $intPersonaId,
                                                                            'estado'       => 'Activo'));
            $arrayContactos = array();
            $arrayContactos['telefono']   = "";
            $arrayContactos['celular']    = "";
            $arrayContactos['correo']     = "";
            if($arrayDataFormaContactoCliente)
            {
                foreach($arrayDataFormaContactoCliente as $objValue)
                {
                    switch($objValue->getFormaContactoId()->getCodigo())
                    {
                        case 'TFIJ' :
                            $arrayContactos['telefono'] .= $objValue->getValor();
                            break;
                        case 'MCLA' :
                        case 'MMOV' :
                        case 'MCNT' :
                            $arrayContactos['celular'] .= $objValue->getValor() . "-";
                            break;
                        case 'MAIL' :
                            $arrayContactos['correo'] = $objValue->getValor();
                            break;
                    }
                }
            }

            if (empty($arrayContactos['celular']))
            {
                $arrayDataFormaContactoCliente = $this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                                ->findBy(array('personaId'    => $intPersonaId,
                                                                                'estado'       => 'Eliminado'));
                if($arrayDataFormaContactoCliente)
                {
                    foreach($arrayDataFormaContactoCliente as $objValue)
                    {
                        if ($objValue->getFormaContactoId()->getCodigo() == 'TMOV')
                        {
                            $arrayContactos['celular'] .= $objValue->getValor() . "-";
                        }
                    }
                }

            }

        }
        catch (\Exception $e)
        {
            throw $e;
        }
        return $arrayContactos;
    }

      /**
     * obtenerDatosDocumentarCertificado, método que nos retorna todas los parámetros para ser enviados al 
     *                                    webservice, para la documentación del certificado para regularizacion de contrato
     * @author Edgar Pin Villavicencio<epin@telconet.ec>
     * @version 1.0 17-07-2020
     * @param obj $objContrato, Objeto de tipo contrato que fue creado para el cliente
     * @param array $arrayData
     *     [
     *         cedula    => cedula de cliente
     *         nombres   => nombres del cliente
     *         pApell    => primer apellido del cliente
     *         sApell    => segundo apellido del cliente
     *         dir       => ruta de directorio
     *         email     => email del cliente
     *         telf      => telefono del cliente
     *         ciudad    => ciudad del cliente
     *         provincia => provincia del cliente
     *         pais      => pais del cliente
     *         fact      => factura del cliente
     *         pass      => pass del cliente
     *         objPunto  => objeto del punto de cliente
     *     ]
     * @return $arrayDatos retorna un arreglo con la información solicitada
     */
    private function obtenerDatosDocumentarCertificado($objContrato, $arrayData, $output)
    {
        try
        {
            $objPersonaEmpresaRol         = $objContrato->getPersonaEmpresaRolId();
            $arrayDatos                   = $this->inicializaParametros();
            $arrayDatos['numeroContrato'] = $objContrato->getNumeroContrato();
            $arrayDatos['tipoCliente']    = $objPersonaEmpresaRol->getPersonaId()->getTipoTributario();
            $arrayDatos['numeroAdendum']  = $arrayData['strNumeroAdendum'];
            $arrayDatos['regulariza']     = $arrayData['regulariza'];
            $arrayDatos['strTipo']        = $arrayData['strTipo'];
            $arrayDatos['feCreacion']     = $objContrato->getFeCreacion();
            $arrayDatos['feAprobacion']   = $objContrato->getFeAprobacion();
    
            $arrayParametros              = array();
            $arrayOrigenIngresos          = array(
                                                  'B' => 'Empleado Público',
                                                  'V' => 'Empleado Privado',
                                                  'I' => 'Independiente',
                                                  'A' => 'Ama de casa o estudiante',
                                                  'R' => 'Rentista',
                                                  'J' => 'Jubilado',
                                                  'M' => 'Remesas del exterior'
                                                 );
            if ($objContrato->getPersonaEmpresaRolId()->getPersonaId()->getOrigenIngresos()!="" 
                && $objContrato->getPersonaEmpresaRolId()->getPersonaId()->getOrigenIngresos()!= null )
            {
                $arrayDatos['origenIngresos'] = $arrayOrigenIngresos[$objContrato->getPersonaEmpresaRolId()->getPersonaId()->getOrigenIngresos()];
            }
            if ($objContrato->getPersonaEmpresaRolId()->getPersonaId()->getGenero() == 'M')
            {
                $arrayDatos['isMasculino'] = "X";
            } elseif ($objContrato->getPersonaEmpresaRolId()->getPersonaId()->getGenero() == 'F')
            {
                $arrayDatos['isFemenino'] = "X";
            }
    
            $arrayEstadoCivil = array(
                                      'S' => 'Soltero(a)',
                                      'C' => 'Casado(a)',
                                      'U' => 'Union Libre',
                                      'D' => 'Divorciado(a)',
                                      'V' => 'Viudo(a)'
                                     );
            if ($objContrato->getPersonaEmpresaRolId()->getPersonaId()->getEstadoCivil()!="" 
                && $objContrato->getPersonaEmpresaRolId()->getPersonaId()->getEstadoCivil()!= null )
            {
                $arrayDatos['estadoCivil'] = $arrayEstadoCivil[$objContrato->getPersonaEmpresaRolId()->getPersonaId()->getEstadoCivil()];
            }
    
            switch ($objPersonaEmpresaRol->getPersonaId()->getTipoTributario())
            {
            case "JUR" :
                $arrayDatos['isJuridico']         = "X";
                $arrayDatos['razonSocial']        = $objPersonaEmpresaRol->getPersonaId()->getRazonSocial();
                $arrayDatos['ruc']                = $objPersonaEmpresaRol->getPersonaId()->getIdentificacionCliente();
                $arrayDatos['direccion']          = $objPersonaEmpresaRol->getPersonaId()->getDireccionTributaria();
                $arrayDatos['representanteLegal'] = $objPersonaEmpresaRol->getPersonaId()->getRepresentanteLegal();
                if (is_object($objPersonaEmpresaRol->getPersonaId()->getTituloId()))
                {
                    $arrayDatos['actividadEconomica'] = $objPersonaEmpresaRol->getPersonaEmpresaRolId()->getPersonaId()->getTituloId()
                        ->getDescripcionTitulo();
                }
                $arrayDatos['isRuc'] = "X";
                break;
            case "NAT" :
                $arrayDatos['isNatural']     = "X";
                $arrayDatos['cedula']        = $objPersonaEmpresaRol->getPersonaId()->getIdentificacionCliente();
                $arrayDatos['nombreCliente'] = $objPersonaEmpresaRol->getPersonaId()->getNombres() . " " . $objPersonaEmpresaRol->getPersonaId()
                        ->getApellidos();
                $arrayDatos['direccion']     = $objPersonaEmpresaRol->getPersonaId()->getDireccionTributaria();
                $arrayDatos['ciudad']        = $arrayData['ciudad'];
                $arrayDatos['provincia']     = $arrayData['provincia'];
                if ($objPersonaEmpresaRol->getPersonaId()->getTipoIdentificacion() == "CED")
                {
                    $arrayDatos['isCedula'] = "X";
                } else
                {
                    $arrayDatos['isPasaporte'] = "X";
                }
                break;
            }
            $arrayDatos['strUsrCreacion'] = $objContrato->getUsrCreacion();
            $arrayDatos['strIpCreacion']  = $objContrato->getIpCreacion();
            $arrayDatos['idFormaPago']    = $objContrato->getFormaPagoId()->getId();
            $arrayDatos['nacionalidad']   = $objPersonaEmpresaRol->getPersonaId()->getNacionalidad();
            $arrayDatosFormaContacto      = $this->obtenerFormasDeContactoByPersonaId($objPersonaEmpresaRol->getPersonaId()->getId());
            $arrayDatos['telefono']       = $arrayDatosFormaContacto['telefono'];
            $arrayDatos['celular']        = substr($arrayDatosFormaContacto['celular'], 0, strlen($arrayDatosFormaContacto['celular']) - 1);
            $arrayDatos['correo']         = strtolower($arrayDatosFormaContacto['correo']);
    
            // Obtenemos la informacion de contacto del cliente
            $arrayDataClienteContacto = $this->emComercial->getRepository('schemaBundle:InfoPersonaContacto')
                                                          ->findBy(array('personaEmpresaRolId'   => $objPersonaEmpresaRol->getId()));
            if($arrayDataClienteContacto)
            {
                $intI = 1;
                foreach($arrayDataClienteContacto as $objClienteContacto)
                {
                    if ($intI <= 2)
                    {// Solo se necesitan como maximo 2 referencias
                        $arrayDatos['nombreRef'.$intI]   = $objClienteContacto->getContactoId()->getNombres() 
                                                           . " " 
                                                           . $objClienteContacto->getContactoId()->getNombres();
                        $arrayDatosFormaContactoRef      = $this->obtenerFormasDeContactoByPersonaId($objClienteContacto
                                                                                                      ->getPersonaEmpresaRolId()->getPersonaId()->getId());
                        $arrayDatos['telefonoRef'.$intI] = $arrayDatosFormaContactoRef['telefono'] . " " . $arrayDatosFormaContactoRef['celular'];
                    }
                    $intI++;
                }//end foreach
            }
    
            if($arrayData['strTipo'] != "C")
            {
    
                $objAdendum = $this->emComercial->getRepository('schemaBundle:InfoAdendum')
                    ->findOneBy(array('numero'     => $arrayData['strNumeroAdendum']),
                                array('feCreacion' => 'DESC'));
            }                    
            else
            {
    
                $objAdendum = $this->emComercial->getRepository('schemaBundle:InfoAdendum')
                    ->findOneBy(array('tipo'       => "C",
                                      'contratoId' => $objContrato->getId()));
    
            }                    
    
            if(is_object($objAdendum))
            {
                $arrayData['objPunto'] = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                    ->find($objAdendum->getPuntoId());
                
                if($arrayData['strTipo'] == "AS")
                {
                    $arrayAdendumsPunto = $this->emComercial->getRepository('schemaBundle:InfoAdendum')
                        ->findBy(array('puntoId' => $objAdendum->getPuntoId(),
                                        'estado'  => 'Activo',
                                        'tipo'    => array('C', 'AP')),
                                    array('id'      => 'ASC'));
                    
                    if(!is_null($arrayAdendumsPunto))
                    {
                        foreach ($arrayAdendumsPunto as $objAdendumPunto)
                        {
                            if (!is_null($objAdendumPunto->getServicioId()))
                            {
                                $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find(
                                        $objAdendumPunto->getServicioId());
    
                                if (is_object($objServicio) && !is_null($objServicio->getPlanId()))
                                {
                                    $arrayParametros['nombrePlan'] = trim($objServicio->getPlanId()->getNombrePlan());
                                }
                            }
                        }
                    }
                }
            }
            
            
            $objPunto = $arrayData['objPunto'];
    
            /* Buscar las rutas de los documentos(rubrica, cedula, foto del cliente), en formato pdf 
              para codificarlas */
            $arrayParametrosRuta = array("objContrato" => $objContrato,
                                         "arrayData"   => $arrayData);
            $arrayDocumentos              = $this->obtenerRutasDocumentos($arrayParametrosRuta);     
            $arrayDatos['rutaCedula']     = $arrayDocumentos['CED'];
            $arrayDatos['rutaRubrica']    = "";
            $arrayDatos['rutaFoto']       = $arrayDocumentos['FOT'];
            $arrayDatos['rutaCedulaR']    = $arrayDocumentos['CEDR'];
            
            //Obtenemos formas de pago
            $arrayFormaDePago                      = $this->obtenerFormaDePago(
                                                                               $objContrato,
                                                                               $arrayData['codEmpresa'],
                                                                               $objPersonaEmpresaRol->getPersonaId()->getIdentificacionCliente());
            //aqui
            $arrayDatos['fpTarjetaCredito']        = $arrayFormaDePago['tarjetaCredito'];
            $arrayDatos['fpEfectivo']              = $arrayFormaDePago['efectivo'];
            $arrayDatos['fpCtaAhorros']            = $arrayFormaDePago['ctaAhorros'];
            $arrayDatos['fpCtaCorriente']          = $arrayFormaDePago['ctaCorriente'];
            $arrayDatos['numeroCVV']               = $arrayFormaDePago['numeroCVV'];
            $arrayDatos['numeroCuenta']            = $arrayFormaDePago['numeroCuenta'];
            $arrayDatos['nombreTitular']           = $arrayFormaDePago['nombreTitular'];
            $arrayDatos['nombreBanco']             = $arrayFormaDePago['nombreBanco'];
            $arrayDatos['precioInstalacion']       = $arrayFormaDePago['precioInstalacion']; 
            $arrayDatos['impInstalacion']          = $arrayFormaDePago['impInstalacion'];
            $arrayDatos['subtotalInstalacion']     = $arrayFormaDePago['subtotalInstalacion'];
            $arrayDatos['totalInstalacion']        = $arrayFormaDePago["totalInstalacion"];
            $arrayDatos['fechaActualAutDebito']    = $arrayFormaDePago["fechaActualAutDebito"];
            $arrayDatos['identificacionAutDebito'] = $arrayFormaDePago["identificacionAutDebito"];
            $arrayDatos['fechaExpiracion']         = $arrayFormaDePago["fechaExpiracion"];
            
            /* Datos del servicio */
    
            if(is_object($objPunto))
            { 
            //obtener datos de promoción instalación.
                                                              
    
            if($arrayData['strTipo'] != "C")
            {
    
                $arrayServicios = $this->emComercial->getRepository('schemaBundle:InfoAdendum')
                    ->findBy(array('tipo'       => $arrayData['strTipo'],
                                   'numero'     => $arrayData['strNumeroAdendum'],
                                   'puntoId'    => $objPunto->getId()));
            }                    
            else
            {
    
                $arrayServicios = $this->emComercial->getRepository('schemaBundle:InfoAdendum')
                    ->findBy(array('tipo'       => "C",
                                   'contratoId' => $objContrato->getId()));
            }                                                            
             
            $objAdmiParametroCabPlan  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                               ->findOneBy( array('nombreParametro' => 'ESTADO_PLAN_CONTRATO', 
                                                                                  'estado'          => 'Activo') );
               
                if(is_object($objAdmiParametroCabPlan))
                {        
                    $arrayParametroDetPlan = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                      ->findBy( array ("parametroId" => $objAdmiParametroCabPlan->getId(),
                                                                      "estado"      => "Activo" ));
                    if ($arrayParametroDetPlan)
                    {
                        foreach($arrayParametroDetPlan as $objAdmiParametroDet)
                        {  
                              //Estados permitidos
                            $arrayEstadosPlanPermitidos[]=$objAdmiParametroDet->getValor1();
    
                        }
                    }
    
                }
     
                if($arrayServicios)
                { 
                    foreach($arrayServicios as $objAdendum)
                    {
                        $objValue = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($objAdendum->getServicioId());
                        if ($objValue->getPlanId())
                        {   
                            $arrayParametrosPlan = array("arrayEstadosPlan" => $arrayEstadosPlanPermitidos,
                                                        "planId"            => $objValue->getPlanId());
    
                                $arrayPlanDet =  $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                        ->getPlanesContratoDigital($arrayParametrosPlan);
    
                                foreach($arrayPlanDet as $objPlanDet)
                                {
                                    $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                        ->findOneBy(array("id" => $objPlanDet->getProductoId()));
                                    if($objProducto->getNombreTecnico() === "INTERNET")
                                    {
                                        if ($arrayData['strTipo'] == "AS") 
                                        {
                                            throw new \Exception('Adendum de servicio no puede tener asociado un plan de internet');
                                        } 
                                        if ($arrayData['strTipo'] == "AS" || $arrayData['strTipo'] == "AP")  
                                        {
                                            $arrayDatos['feCreacion']       = $objValue->getFeCreacion();
                                            $arrayDatos['feAprobacion']     = $objValue->getFeCreacion();
                                        } 
                                        $strFp = "EF";
                                        if ($objContrato->getFormaPagoId()->getId()!= 1)
                                        {
                                            //busco la forma de pago
                                            $objContratoFp = $this->emComercial->getRepository('schemaBundle:InfoContratoFormaPago')
                                                                        ->findOneBy(array("contratoId" => $objContrato->getId()));
                                            if ($objContratoFp)
                                            {
                                                if ($objContratoFp->getTipoCuentaId()->getEsTarjeta() == "S")
                                                {
                                                    $strFp = "TC";
                                                }
                                                if (substr($objContratoFp->getTipoCuentaId()->getDescripcionCuenta(),0,3) == "AHO")
                                                {
                                                    $strFp = "AH";
                                                }
                                                if (substr($objContratoFp->getTipoCuentaId()->getDescripcionCuenta(),0,3) == "COR")
                                                {
                                                    $strFp = "CC";
                                                }
    
                                            }
                                        }

                                        $strPromIns = 'PROM_INS';
                                        $arrayParametrosIns = array(
                                        'intIdPunto'               => $objPunto->getId(),
                                        'intIdServicio'            => $objValue->getId(),
                                        'strCodigoGrupoPromocion'  => $strPromIns,
                                        'intCodEmpresa'            => $arrayData['codEmpresa'],
                                        'feAprobacion'             => $arrayData['strTipo'] == "C" ?
                                                                      $objContrato->getFeAprobacion()->format('d/m/Y H:i:s') : 
                                                                      $objValue->getFeCreacion()->format('d/m/Y H:i:s'),
                                        'strFormaPago'             => $strFp                                 
                                                                    );
    
                                    $arrayContratoPromoIns[]  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                        ->getPromocionesContratoRegularizacion($arrayParametrosIns);
    
    
                                    if( isset($arrayContratoPromoIns[0]['intDescuento']) && !empty($arrayContratoPromoIns[0]['intDescuento']) )
                                    {
                                        $arrayDatos['descInstalacion']   = $arrayContratoPromoIns[0]['intDescuento'].'%';
                                        $arrayDatos['isDescInstalacion'] = 'X';
                                    }
    
                                }
                            }
                        }
    
                    }
                }
         
                
                $arrayDatos['provincia']          = $objPunto->getSectorId()->getParroquiaId()->getCantonId()->getProvinciaId()->getNombreProvincia();
                $arrayDatos['canton']             = $objPunto->getSectorId()->getParroquiaId()->getCantonId()->getNombreCanton();
                $arrayDatos['parroquia']          = $objPunto->getSectorId()->getParroquiaId()->getNombreParroquia();
                $arrayDatos['cantonServicio']     = $objPunto->getSectorId()->getParroquiaId()->getCantonId()->getNombreCanton();
                $arrayDatos['parroquiaServicio']  = $objPunto->getSectorId()->getParroquiaId()->getNombreParroquia();
                
                 //si es que la dirección del cliente es igual a la dirección del punto. 
                //no se debe llenar el campo dirección de la instalacion y marca isSi con X.
                if ($arrayDatos['direccion'] == $objPunto->getDireccion())
                {
                    $arrayDatos['isSi'] = "X";
                    $arrayDatos['isNo'] = "";
                }
                else
                {
                    $arrayDatos['isSi']               = "";
                    $arrayDatos['isNo']               = "X";
                    $arrayDatos['direccionServicio']  = $objPunto->getDireccion();   
                }
                $arrayDatos['observacionPunto']   = $objPunto->getObservacion();
                $arrayDatos['referenciaServicio'] = $objPunto->getDescripcionPunto();
                $arrayDatos['latitudServicio']    = $objPunto->getLatitud();
                $arrayDatos['longuitudServicio']  = $objPunto->getLongitud();
                $arrayDatos['sectorServicio']     = $objPunto->getSectorId()->getNombreSector();
                $arrayDatos['ciudadServicio']     = $objPunto->getSectorId()->getParroquiaId()->getCantonId()->getNombreCanton();
                $arrayTipoUbicacion               = $this->retornaTipoUbicacion($objPunto->getTipoUbicacionId());
                $arrayDatos['casaServicio']       = $arrayTipoUbicacion['casa'];
                $arrayDatos['edificioServicio']   = $arrayTipoUbicacion['edificio'];
                $arrayDatos['conjuntoServicio']   = $arrayTipoUbicacion['conjunto'];
                $arrayDatos['ciudadServicio']     = $objPunto->getSectorId()->getParroquiaId()->getCantonId()->getNombreCanton();
                $arrayVendedor                    = $this->obtenerDatosVendedorByLogin($objPunto->getUsrVendedor());
                $arrayDatos['codigoVendedor']     = $arrayVendedor['codigo'];
                $arrayDatos['nombreVendedor']     = $arrayVendedor['nombres'] . " " . $arrayVendedor['apellidos'];
                $objPuntoContacto                 = $this->emComercial->getRepository('schemaBundle:InfoPuntoContacto')
                                                                      ->findByPuntoIdYEstado($objPunto->getId(), 
                                                                                                'Activo');
                $arrayParametros['intCodEmpresa']    = $arrayData['codEmpresa'];  
                $arrayParametros['objPunto']         = $objPunto;
                $arrayParametros['strTipo']          = $arrayData['strTipo'];
                $arrayParametros['strNumeroAdendum'] = $arrayData['strNumeroAdendum'];
                $arrayParametros['contratoId']       = $objContrato->getId();
                
                $arrayDatos['arrServicios']       = $this->retornaServiciosAdendum($arrayParametros, $output);
        
                $arrayDatos['loginPunto']         = $objPunto->getLogin();
                
                // Obtenemos informacion de contacto del punto
                if(is_object($objPuntoContacto))
                {
                    $arrayDatos['contactoServicio']   = $objPuntoContacto->getContactoId()->getNombres() 
                                                        . " " . $objPuntoContacto->getContactoId()->getApellidos();
                    $arrayFormaContactoPunto          = $this->obtenerFormasDeContactoByPersonaId($objPuntoContacto->getContactoId()->getId());
                    $arrayDatos['telefonoServicio']   = $arrayFormaContactoPunto['telefono'];
                    $arrayDatos['celularServicio']    = $arrayFormaContactoPunto['celular'];
                    $arrayDatos['correoServicio']     = $arrayFormaContactoPunto['correo'];
                }
                else
                {
                    $arrayDatos['contactoServicio']   = $arrayDatos['nombreCliente'];
                    $arrayDatos['telefonoServicio']   = $arrayDatos['telefono'] ;
                    $arrayDatos['celularServicio']    = $arrayDatos['celular'];
                    $arrayDatos['correoServicio']     = $arrayDatos['correo'];        
                }
            }

    
        }
        catch (\Exception $e)
        {
            throw $e;
        }

        return $arrayDatos;
    }

    /**
     * obtenerFormaDePago, método que nos retorna la forma de pago que tiene la persona en su 
     *                     contrato de regularizacion.
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 17-07-2020
     * @param obj     $objContrato, Objeto de tipo contrato que fue creado para el cliente
     * @param integer $strCodEmpresa, id de la empresa
     * @param string  $strIdentificacion, identificación del cliente
     * 
     * @return $arrayFormaDePago retorna un arreglo con la siguiente información :
     *         $arrayFormaDePago['tarjetaCredito']    => Si la forma de pago es con tarjeta de crédito
     *         $arrayFormaDePago['efectivo']          => Si la forma de pago es en efectivo
     *         $arrayFormaDePago['ctaAhorros']        => Si la forma de pago es débito bancario de una cuenta de ahorros
     *         $arrayFormaDePago['ctaCorriente']      => Si la forma de pago es débito bancario de una cuenta corriente
     *         $arrayFormaDePago['isDescInstalacion'] => si tiene descuento de instalación
     *         $arrayFormaDePago['fechaExpiracion']   => fecha de expiracion de tarjeta de credito
     * 
     * 
     */
    public function obtenerFormaDePago($objContrato, $strCodEmpresa, $strIdentificacion)
    {
        try
        {
            $arrayFormaDePago = array();
            $arrayFormaDePago['tarjetaCredito']    = "";
            $arrayFormaDePago['efectivo']          = "";
            $arrayFormaDePago['ctaAhorros']        = "";
            $arrayFormaDePago['ctaCorriente']      = "";
            $arrayFormaDePago["isDescInstalacion"] = "";
            $arrayFormaDePago['fechaExpiracion']   = "";
            
            $strCodFormaPago   = $objContrato->getFormaPagoId()->getCodigoFormaPago();
            $strDescFormaPago  = $objContrato->getFormaPagoId()->getDescripcionFormaPago();
            
            switch($strCodFormaPago) 
            {
                case 'EFEC':
                    $arrayFormaDePago['efectivo']     = "X";
                    break;
                case 'CHEQ':
                    $arrayFormaDePago['cheque']       = "X";
                    break;
                case 'CART':
                    $arrayFormaDePago['cartera']      = "X";
                    break;
                case 'DEB':
                    $arrayTipoDebito                             = $this->obtenerFormaDePagoDebito($objContrato, $strCodEmpresa);
                    $arrayFormaDePago['ctaAhorros']              = $arrayTipoDebito['ctaAhorros'];  
                    $arrayFormaDePago['ctaCorriente']            = $arrayTipoDebito['ctaCorriente'];
                    $arrayFormaDePago['tarjetaCredito']          = $arrayTipoDebito['tarjetaCredito'];
                    $arrayFormaDePago['numeroCuenta']            = $arrayTipoDebito['numeroCuenta'];
                    $arrayFormaDePago['numeroCVV']               = isset($arrayTipoDebito['numeroCVV']) ? $arrayTipoDebito['numeroCVV'] : "";
                    $arrayFormaDePago['nombreTitular']           = $arrayTipoDebito['nombreTitular'];
                    $arrayFormaDePago['nombreBanco']             = $arrayTipoDebito['nombreBanco'];
                    $arrayFormaDePago['fechaActualAutDebito']    = date('Y-m-d');
                    $arrayFormaDePago['identificacionAutDebito'] = $strIdentificacion;
                    $strDescFormaPago                            = $arrayTipoDebito['tipoCuenta'];
                    $arrayFormaDePago['fechaExpiracion']         = $arrayTipoDebito['fechaExpiracion'];
                    break;
                case 'REC':
                    $arrayFormaDePago['recaudacion']  = "X";
                    break;
                case 'REC':
                    $arrayFormaDePago['transferencia']= "X";
                    break;
                default:
                    break;
            }
            
            $objUltimaMilla = $this->getDatosUltimaMilla($objContrato->getPersonaEmpresaRolId());
            
            $arrayInstalacion = $this->getDatosInstalacion($strCodEmpresa, 
                                                         $strDescFormaPago, 
                                                         $objUltimaMilla["data"]["codigo"], 
                                                         $objContrato->getPersonaEmpresaRolId());
            
            
            if(isset($arrayInstalacion["estado"]))
            {
                $arrayFormaDePago["precioInstalacion"]    = $arrayInstalacion["precio"];
                $arrayFormaDePago["descInstalacion"]      = $arrayInstalacion["porcentaje"]."%";
                //Si tiene porcentaje de instalacion marcamos isDescInstalacion
                if($arrayInstalacion["porcentaje"] > 0)
                {
                    $arrayFormaDePago["isDescInstalacion"]    = "X";
                }
                $arrayFormaDePago["impInstalacion"]       = round(($arrayInstalacion["impuesto"]/100)*$arrayFormaDePago["precioInstalacion"],2);
                $arrayFormaDePago["subtotalInstalacion"]  = $arrayFormaDePago["precioInstalacion"];
                $arrayFormaDePago["totalInstalacion"]     = $arrayFormaDePago["precioInstalacion"]+$arrayFormaDePago["impInstalacion"];
            }
    
        }
        catch (\Exception $ex)
        {
            throw $ex;
        }        
        return $arrayFormaDePago;
    }

 
    /**
     * inicializaParametros, método que inicializa las variables para documentar las plantillas de
     *                       los diferentes contratos digitales
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 17-07-2020
     * @return $arrayDatos retorna un arreglo inicializando las variables que se muestran
     * 
     */
    private function inicializaParametros()
    {
        $arrayDatos = array();
        $arrayDatos['isNatural']              = "";
        $arrayDatos['isJuridico']             = "";
        $arrayDatos['isNuevo']                = "X";
        $arrayDatos['isExistente']            = "";
        $arrayDatos['cedula']                 = "";
        $arrayDatos['nombreCliente']          = "";
        $arrayDatos['razonSocial']            = "";
        $arrayDatos['ruc']                    = "";
        $arrayDatos['ciudad']                 = "";
        $arrayDatos['provincia']              = "";
        $arrayDatos['telefono']               = "";
        $arrayDatos['celular']                = "";
        $arrayDatos['correo']                 = "";
        $arrayDatos['representanteLegal']     = "";
        $arrayDatos['referenciaServicio']     = "";
        $arrayDatos['sectorBarrio']           = "";
        $arrayDatos['actividadEconomica']     = "";
        $arrayDatos['nombreRef1']             = "";
        $arrayDatos['nombreRef2']             = "";
        $arrayDatos['telefonoRef1']           = "";
        $arrayDatos['telefonoRef2']           = "";
        $arrayDatos['rutaCedula']             = "";
        $arrayDatos['rutaRubrica']            = "";
        $arrayDatos['rutaFoto']               = "";
        $arrayDatos['nombreVendedor']         = "";
        $arrayDatos['codigoVendedor']         = "";
        $arrayDatos['isSi']                   = "X";
        $arrayDatos['isNo']                   = "";
        $arrayDatos['ciudadServicio']         = "";
        $arrayDatos['telefonoServicio']       = "";
        $arrayDatos['celularServicio']        = "";
        $arrayDatos['correoServicio']         = "";
        $arrayDatos['horarioServicio']        = "";
        $arrayDatos['fpTarjetaCredito']       = "";
        $arrayDatos['fpEfectivo']             = "";
        $arrayDatos['fpCtaAhorros']           = "";
        $arrayDatos['fpCtaCorriente']         = "";
        $arrayDatos['idFormaPago']            = "";
        $arrayDatos['idTipoCuenta']           = "";
        $arrayDatos['strIpCreacion']          = "";
        $arrayDatos['idFormaPago']            = "";
        $arrayDatos['isMasculino']            = "";
        $arrayDatos['isFemenino']             = "";
        $arrayDatos['estadoCivil']            = "";
        $arrayDatos['origenIngresos']         = "";
        $arrayDatos['isDescInstalacion']      = "";
        $arrayDatos['isFactElectronica']      = "X";
        $arrayDatos['fechaExpiracion']        = "X";
        $arrayDatos['isSiAutoriza']           = "X";
        $arrayDatos['isNoAutoriza']           = "";
        $arrayDatos['isSiRenueva']            = "X";
        $arrayDatos['isNoRenueva']            = "";
        $arrayDatos['isSiAcceder']            = "X";
        $arrayDatos['isNoAcceder']            = "";
        $arrayDatos['isSiMediacion']          = "X";
        $arrayDatos['isNoMediacion']          = "";
        $arrayDatos['isDiscapacitadoSi']      = "X";
        $arrayDatos['isDiscapacitadoNo']      = "";
        
        
        return $arrayDatos;
    }
 
    /**
     * obtenerRutasDocumentos, método que nos retorna las rutas donde se encuentran los documentos digitales 
     *                         para el contrato digital de regularizacion
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 17-07-2020
     * @param obj $objContrato, Objeto de tipo Contrato
     * @return $arrDocumentos retorna un arreglo con la siguiente información :
     *         $arrDocumentos['CED'] => Ruta donde se encuentra el documento digital frontal de la cédula de identidad
     *         $arrDocumentos['FOT'] => Ruta donde se encuentra el documento digital de la foto
     *         $arrDocumentos['RUB'] => Ruta donde se encuentra el documento digital de la rúbrica
     *         $arrDocumentos['CEDR']=> Ruta donde se encuentra el documento digital del reverso de la cédula de identidad
     */
    private function obtenerRutasDocumentos($arrayParametros)
    {
        $objContrato = $arrayParametros['objContrato'];
        $arrayData   = $arrayParametros['arrayData'];
        $objTipoDocumento  = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                                  ->findOneBy(array("extensionTipoDocumento"=>"PDF"));
        
        $arrayDocumentos   = $this->emComunicacion->getRepository('schemaBundle:InfoDocumento')
                                                  ->findBy(array("contratoId"       => $objContrato->getId(),
                                                                 "tipoDocumentoId"  => $objTipoDocumento->getId()));
        $arrayDocumentosRuta = array();
        if($arrayDocumentos)
        {
            foreach($arrayDocumentos as $objDataDoc)
            {
                $objTipoDoc = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                              ->findOneById($objDataDoc->getTipoDocumentoGeneralId());

                if($objTipoDoc->getCodigoTipoDocumento() === "CED"  ||
                   $objTipoDoc->getCodigoTipoDocumento() === "CEDR" ||
                   $objTipoDoc->getCodigoTipoDocumento() === "FOT"  ||
                   $objTipoDoc->getCodigoTipoDocumento() === "RUB" )
                {
                    if ($arrayData['strTipo'] === "C")
                    {
                        $arrayDocumentosRuta[$objTipoDoc->getCodigoTipoDocumento()] = $objDataDoc->getUbicacionFisicaDocumento();
                    }
                    else
                    {

                        $objDocumentoRelacion = $this->emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')
                                                                      ->findOneBy(array('documentoId'   => $objDataDoc->getId(),
                                                                                        'numeroAdendum' => $arrayData['strNumeroAdendum']));
                        if ($objDocumentoRelacion)
                        {
                            $arrayDocumentosRuta[$objTipoDoc->getCodigoTipoDocumento()] = $objDataDoc->getUbicacionFisicaDocumento();
                            
                        }
                    }
                }
            }
        }
        return $arrayDocumentosRuta;
    }

    /**
     * Metodo que devuelve el tipo de forma de pago cuando el cliente a optado por
     * realizar sus pagos a traves de debitos a una cuenta.
     * 
     * @param type $objContrato => datos del contrato
     * @return type $arrayFormaDePago['tipoCuenta']       => Indica el tipo de Cuenta
     *              $arrayFormaDePago['ctaAhorros']       => Indica si es Cta. de Ahorros  
     *              $arrayFormaDePago['ctaCorriente']     => Indica si es Cta. Corriente
     *              $arrayFormaDePago['tarjetaCredito']   => Indica si es Tarj. de Credito
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 31-07-2020
     * 
     */
    private function obtenerFormaDePagoDebito($objContrato)
    {
        $objContratoFormaPago = $this->emComercial->getRepository('schemaBundle:InfoContratoFormaPago')
                                                  ->findOneBy(array("contratoId"   => $objContrato->getId()));
        
        $strTipoCuenta        = $objContratoFormaPago->getTipoCuentaId()->getDescripcionCuenta();
        $arrayFormaDePago                     = array();        
        $arrayFormaDePago['ctaAhorros']       = "";  
        $arrayFormaDePago['ctaCorriente']     = "";
        $arrayFormaDePago['tarjetaCredito']   = "";
        $arrayFormaDePago['tipoCuenta']       = "";
        $arrayFormaDePago['tipoCuenta']       = $strTipoCuenta;
        $arrayFormaDePago['fechaExpiracion']  = "";
        switch($strTipoCuenta)
        {
            case 'AHORRO':
                $arrayFormaDePago['ctaAhorros']       = "X";
                break;
            case 'CORRIENTE':
                $arrayFormaDePago['ctaCorriente']     = "X";
                break;
            default:
                $arrayFormaDePago['tarjetaCredito']   = "X";
                $arrayFormaDePago['numeroCVV']        = "999";
                break;
        }
        $arrayFormaDePago['numeroCuenta'] = $this->serviceCrypt->descencriptar($objContratoFormaPago->getNumeroCtaTarjeta());
        $arrayFormaDePago['nombreTitular']= $objContratoFormaPago->getTitularCuenta();
        //Si es cta corriente o ahorro entonces solo se obtiene nombre banco
        //Si es tarjeta obtiene nombre tipo de cuenta con el nombre del banco
        if (strtoupper($strTipoCuenta)=="AHORRO" || strtoupper($strTipoCuenta)=="CORRIENTE")
        {
            $arrayFormaDePago['nombreBanco']  = $objContratoFormaPago->getBancoTipoCuentaId()->getBancoId()->getDescripcionBanco();
        }
        else
        {
            $arrayFormaDePago['nombreBanco']     = $objContratoFormaPago->getBancoTipoCuentaId()->getTipoCuentaId()->getDescripcionCuenta()
                                                   ." ".$objContratoFormaPago->getBancoTipoCuentaId()->getBancoId()->getDescripcionBanco();
            $arrayFormaDePago['fechaExpiracion'] = $objContratoFormaPago->getMesVencimiento()."-".$objContratoFormaPago->getAnioVencimiento();
        }
        return $arrayFormaDePago;
    }
 
     /**
     * Obtiene La ultima milla del servicio contratado
     * 
     * @param type $objPersonaEmpresaRol
     * @return type  array("status"  => Estado de la peticion,
                           "mensaje" => Mensaje en caso de error,
                           "data"    => array("id"       => Id de ultima milla,
                                              "codigo"   => Codigo de ultima milla ));
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 16-07-2020
     */
    public function getDatosUltimaMilla($objPersonaEmpresaRol)
    {
        try
        {
            $objUltimaMilla = null;
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                             ->findServicioPorEstado($objPersonaEmpresaRol->getId(), 'Factible');
            
            if(is_object($objServicio))
            {
                $objUltimaMilla = $this->serviceServiciotecnico->getUltimaMillaPorServicio($objServicio->getId());
            }
            return $objUltimaMilla;
    
        }
        catch (\Exception $e)
        {
            throw $e;
        } 
    }
    
     /**
     * Obtiene el porcentaje de descuento de instalacion de un servicio
     * 
     * @param type $strCodEmpresa
     * @param type $strFormaPago
     * @param type $strUltimaMilla
     * @param type objPersonaEmpresaRolId 
     * @return string
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 26-07-2020
     */
    public function getDatosInstalacion($strCodEmpresa,$strFormaPago,$strUltimaMilla,$objPersonaEmpresaRolId)
    {
        $arrayRespuesta = array("estado"     => false,
                           "porcentaje" => 100,
                           "impuesto"   => 0,
                           "mensaje"    => null,
                           "precio"     => 0.0);

        $objProductoInstalacion = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                    ->findOneBy(array("codigoProducto" => "INST-MD",
                                                                      "empresaCod"     => $strCodEmpresa,
                                                                      "estado"         => "Inactivo"));
        if(is_object($objProductoInstalacion))
        {
            $arrayRespuesta["impuesto"]  = $this->obtenerImpuestoInstalacion($objPersonaEmpresaRolId);
            $strFuncionPrecio            = $objProductoInstalacion->getFuncionPrecio();
            $arrayRespuesta["precio"]    = (float)trim(str_replace("PRECIO=","",$strFuncionPrecio));
        }
        else
        {
            $arrayRespuesta["mensaje"]   = "No hay precio de instalación definido.";
            return $arrayRespuesta;
        }
        
        // Obtenemos el parameto PORCENTAJE_DESCUENTO_INSTALACION que nos indica las consideraciones 
        // a tomar cuando se realiza el descuento de instalación de un servicio
        $objParametroCab        = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                  ->findOneBy(array("nombreParametro" =>"PORCENTAJE_DESCUENTO_INSTALACION",
                                                                    "estado"          => "Activo"));
        
        if(is_object($objParametroCab))
        {
            $arrayParametroDet   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                   ->findBy(array("parametroId"    =>$objParametroCab,
                                                                  "estado"         => "Activo"));
            // Obtenemos los parametros que nos indica las consideraciones 
            // a tomar cuando se realiza el descuento de instalación de un servicio
            if($arrayParametroDet)
            {
                $arrayRespuesta["estado"]    = true;
                foreach($arrayParametroDet as $parametroDet)
                {
                    if($parametroDet->getValor1() === $strUltimaMilla && 
                       $parametroDet->getValor2() === $strFormaPago)
                    {
                        $arrayRespuesta["porcentaje"]    = (int)$parametroDet->getValor3();
                        break;
                    }
                }
            }
            else
            {
                $arrayRespuesta["mensaje"] = "No existen parametros definidos para descuento en instalaciones";
            }
        }
        else
        {
            $arrayRespuesta["mensaje"] = "No existe el parametro PORCENTAJE_DESCUENTO_INSTALACION";
        }

        return $arrayRespuesta;
    }  

    /**
     * retornaTipoUbicacion, método que nos retorna el tipo de vivienda donde se instaló el punto, que tiene
     *                       el contrato
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 17-07-2020
     * @param obj $objTipoUbicacion, Objeto de tipo Ubicacion
     * @return $arrayTipoUbicacion retorna un arreglo con la siguiente información :
     *         $arrayTipoUbicacion['conjunto'] => Si el tipo de vivienda esta dentro de un conjunto residencial
     *         $arrayTipoUbicacion['edificio'] => Si el tipo es un edificio
     *         $arrayTipoUbicacion['casa']     => Si el tipo de vivienda es un casa
     *         
     */
    private function retornaTipoUbicacion($objTipoUbicacion) 
    {
        $arrayTipoUbicacion = array();
        $arrayTipoUbicacion['conjunto']   = "";
        $arrayTipoUbicacion['edificio']   = "";
        $arrayTipoUbicacion['casa']       = "";
        
        if($objTipoUbicacion)
        {
            switch($objTipoUbicacion->getCodigoTipoUbicacion())
            {
                case 'CONJ':
                    $arrayTipoUbicacion['conjunto']   = 'X';
                    break;
                case 'EDIF':
                    $arrayTipoUbicacion['edificio']   = 'X';
                    break;
                default:
                    $arrayTipoUbicacion['casa']       = 'X';
                    break;
            }
        }
        return $arrayTipoUbicacion;
    }
    
     /**
     * obtenerDatosVendedorByLogin, método que nos retorna información sobre el usuario que realizó la venta del 
     *                              servicio
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 17-07-2016
     * @param String $strLogin, login del usuario
     * @return $arrayPersona retorna un arreglo con la siguiente información :
     *         $arrayPersona['codigo']    => Código del vendedor
     *         $arrayPersona['nombres']   => Nombres del vendedor
     *         $arrayPersona['apellidos'] => Apellidos del vendedor
     *         
     */
    private function obtenerDatosVendedorByLogin($strLogin)
    {
        $objPersona   = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                          ->getDatosPersonaPorLogin($strLogin);
        $arrayPersona = array();
        if(is_object($objPersona))
        {
            $arrayPersona['codigo']       = $objPersona->getId();
            $arrayPersona['nombres']      = $objPersona->getNombres();
            $arrayPersona['apellidos']    = $objPersona->getApellidos();
        }
        return $arrayPersona;
    }

    /**
     * Método que retorna los servicios ademdum
     *
     * @author Edgar Pin Villavicencio <mailto:epin@telconet.ec>
     * @version 1.0 - 03/04/2020 
     */
    public function retornaServiciosAdendum($arrayParametros, $output)
    {
        $strCodEmpresa            = $arrayParametros['intCodEmpresa'];  
        $objPunto                 = $arrayParametros['objPunto'];

        $strTipo                  = $arrayParametros['strTipo'];
        $strNumeroAdendum         = $arrayParametros['strNumeroAdendum'];
        $intContratoId            = $arrayParametros['contratoId'];
        $strPromIns               = 'PROM_INS';
        $strPromMens              = 'PROM_MENS';
        try
        {
            
            if ($strTipo == 'C')
            {

                $arrayServ = $this->emComercial->getRepository('schemaBundle:InfoAdendum')
                                               ->findBy(array("tipo"       => $strTipo,
                                                            "contratoId" => $intContratoId
                                                            ));
            }
            else
            {
                $arrayServ = $this->emComercial->getRepository('schemaBundle:InfoAdendum')
                                               ->findBy(array("tipo"   => $strTipo,
                                                            "numero" => $strNumeroAdendum
                                                            ));
    
            }
            if ($arrayServ)
            {
                $arrayServAdendum = array();
                foreach ($arrayServ as $objAdendum)
                {
                    
                    $arrayServAdendum[] = $objAdendum->getServicioId();
                }
            }
                

                $arrayServicios = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                ->findTodosServiciosPorAdendum(array("arrayServicios" => $arrayServAdendum));
          
    
                $objAdmiParametroCabPlan  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                               ->findOneBy( array('nombreParametro' => 'ESTADO_PLAN_CONTRATO', 
                                                                                  'estado'          => 'Activo') );
               
                if(is_object($objAdmiParametroCabPlan))
                {        
                    $arrayParametroDetPlan = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                      ->findBy( array ("parametroId" => $objAdmiParametroCabPlan->getId(),
                                                                      "estado"      => "Activo" ));
                    if ($arrayParametroDetPlan)
                    {
                        foreach($arrayParametroDetPlan as $objAdmiParametroDet)
                        {  
                              //Estados permitidos
                            $arrayEstadosPlanPermitidos[]=$objAdmiParametroDet->getValor1();
    
                        }
                    }
                    $arrayEstadosPlanPermitidos[]="Inactivo";                
    
                }
    
            $objInfoOficina = $this->emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                                ->find($objPunto->getPersonaEmpresaRolId()->getOficinaId());
                        
            $arrayServicio = array(
                                 "isHome"           => "",
                                 "obsServicio"      => "",
                                 "isPyme"           => "",
                                 "isPro"            => "",
                                 "isGeponFibra"     => "",
                                 "isDslOtros"       => "",
                                 "isSimetrico"      => "",
                                 "isAsimetrico"     => "",
                                 "valorPlanLetras"  => "",
                                 "valorPlanNumeros" => "",            
                                 "detalle"          => null,
                                 "subtotal"         => 0.0,
                                 "velNacMax"        => null,
                                 "velNacMin"        => null,
                                 "velIntMax"        => null,
                                 "velIntMin"        => null,
                                 "descPlan"         => 0,
                                 "mesesDesc"        => 0,
                                 "impuestos"        => 0.0,
                                 "total"            => 0.0,
                                 "valorPlanDesc"    => 0.0, 
                                 "isPrecioPromo"    => "",
                                 "nombrePlan"        => "",
                                );
            
            $arrayServiciosContratados = array(
                                               "INTERNET"  => array("precio"   => null,
                                                                    "cantidad" => null),
                                               "IP"        => array("precio"   => null,
                                                                    "cantidad" => null),
                                               "OTROS"     => array("precio"   => null,
                                                                    "cantidad" => null));
            
            $arrayProdParametros = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                ->get("PRODUCTOS_TM_COMERCIAL", "COMERCIAL", "", "", "", "", "", "", "", $strCodEmpresa);
            
            if(!empty($arrayProdParametros))
            {
                $arrayNuevoProdParametros = array();
    
                foreach ($arrayProdParametros as $intKey => $arrayProdParametro)
                {
                    $arrayNuevoProdParametros[$arrayProdParametro['valor1']] = $arrayProdParametros[$intKey];
                    $arrayServiciosContratados[$arrayProdParametro['valor2']] = array('precio' => '', 'cantidad' => '');
                }
    
                $arrayProdParametros = $arrayNuevoProdParametros;
                unset($arrayNuevoProdParametros);
    
                $arrayServicio['arrayProdParametros'] = $arrayProdParametros;
            }
    
            $floatSubtotal = 0.0;
                
            if($arrayServicios['registros'])
            {  
                // Capacidad Nacional
                $objCaracteristicaCapcNac = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                              ->findOneBy(array("descripcionCaracteristica" => "CAPACIDAD1"));
                // Capacidad Internacional                                             
                $objCaracteristicaCapcInt = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                              ->findOneBy(array("descripcionCaracteristica" => "CAPACIDAD2"));
                
       
                foreach($arrayServicios['registros'] as $objValue)
                {
                    
                    
                    $strDescripcionParamValorEquipamiento = "";
                    if($objValue->getPlanId())
                    {
                       
                        if($objValue->getPlanId()->getTipo()==="HOME")
                        {
                            $arrayServicio["isHome"]              = "X";
                            $strDescripcionParamValorEquipamiento = "VALOR_EQUIPAMIENTO_CONTRATO_DIGITAL_HOME_MD";
                        }
                        else if($objValue->getPlanId()->getTipo()==="PYME")
                        {
                            $arrayServicio["isPyme"]              = "X";
                            $strDescripcionParamValorEquipamiento = "VALOR_EQUIPAMIENTO_CONTRATO_DIGITAL_PYME_MD";
                        }
                        else if($objValue->getPlanId()->getTipo()==="PRO")
                        {
                            $arrayServicio["isPro"]               = "X";
                            $strDescripcionParamValorEquipamiento = "VALOR_EQUIPAMIENTO_CONTRATO_DIGITAL_PRO_MD";
                        }
                        //OBTENEMOS LOS VALORES DE EQUIPAMIENTO SEGUN PLAN
                        if (is_object($objInfoOficina))
                        {
                            $arrayValorEquipamiento = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                      ->getOne('VALOR_EQUIPAMIENTO_CONTRATO_DIGITAL',
                                                                               'COMERCIAL',
                                                                               '',
                                                                               '',
                                                                               $strDescripcionParamValorEquipamiento,
                                                                               '',
                                                                               '',
                                                                               '',
                                                                               '',
                                                                               $objInfoOficina->getEmpresaId()->getId());
                            if(isset($arrayValorEquipamiento['valor2']))
                            {
                                $arrayServicio["valorPlanNumeros"] = $arrayValorEquipamiento['valor2'];
                            }                        
                            
                            if(isset($arrayValorEquipamiento['valor3']))
                            {
                                $arrayServicio["valorPlanLetras"] = $arrayValorEquipamiento['valor3'];
                            }                    
                        }
    
                        $arrayPlanDet = array();
                        if ($objValue->getPlanId()!= null)
                        {
                          $arrayParametrosPlan = array("arrayEstadosPlan" => $arrayEstadosPlanPermitidos,
                                                          "planId"            => $objValue->getPlanId());
    
                          $arrayPlanDet =  $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                               ->getPlanesContratoDigital($arrayParametrosPlan);
    
                        }                                                   
                        
                        $arrayServiciosContratados['INTERNET']["cantidad"] = $arrayServiciosContratados['INTERNET']["cantidad"]+1;
                        foreach($arrayPlanDet as $planDet)
                        {
                            $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                             ->findOneBy(array("id" => $planDet->getProductoId()));
                            
                            if($objProducto->getNombreTecnico() === "INTERNET")
                            {
                                $objContrato = $this->emComercial->getRepository('schemaBundle:InfoContrato')
                                                                 ->find($intContratoId); 
                                $strFp = "EF";
                                $output->writeln("despues de obtener contrato " );
                                $output->writeln("id " .$objContrato->getFormaPagoId()->getId());
                                if ($objContrato->getFormaPagoId()->getId()!= 1)
                                {
                                    //busco la forma de pago
                                    $objContratoFp = $this->emComercial->getRepository('schemaBundle:InfoContratoFormaPago')
                                                                ->findOneBy(array("contratoId" => $objContrato->getId()));
                                    if ($objContratoFp)
                                    {

                                        if (substr($objContratoFp->getTipoCuentaId()->getDescripcionCuenta(),0,3) == "AHO")
                                        {
                                            $strFp = "AH";
                                        }
                                        if (substr($objContratoFp->getTipoCuentaId()->getDescripcionCuenta(),0,3) == "COR")
                                        {
                                            $strFp = "CC";
                                        }
                                        if ($objContratoFp->getTipoCuentaId()->getEsTarjeta() == "S")
                                        {
                                            $strFp = "TC";
                                        }
                                    }
                                }                        
                                
                                 $arrayParametrosIns = array(
                                    'intIdPunto'               => $objPunto->getId(),
                                    'intIdServicio'            => $objValue->getId(),
                                    'strCodigoGrupoPromocion'  => $strPromIns,
                                    'intCodEmpresa'            => $strCodEmpresa,
                                    'feAprobacion'             => $objValue->getFeCreacion()->format('d/m/Y H:i:s') ,
                                    'strFormaPago'             => $strFp                                      
                                    ); 
                    
                                 $arrayParametrosMens = array(
                                    'intIdPunto'               => $objPunto->getId(),
                                    'intIdServicio'            => $objValue->getId(),
                                    'strCodigoGrupoPromocion'  => $strPromMens,
                                    'intCodEmpresa'            => $strCodEmpresa,
                                    'feAprobacion'             => $objValue->getFeCreacion()->format('d/m/Y H:i:s'),
                                    'strFormaPago'             => $strFp   
                                    ); 
    
                                $arrayContratoPromoIns[]  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->getPromocionesContratoRegularizacion($arrayParametrosIns);
    
    
                                $arrayPromocion  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->getPromocionMensualRegularizacion($objValue->getId());
    
    
                                $strNombrePlan= strtoupper($objValue->getPlanId()->getNombrePlan()." ");
                                
                                $arrayServicio["nombrePlan"] = $strNombrePlan;
                                $strObservaInstalacion       = $arrayContratoPromoIns[0]['strObservacion'];
                                $strObservaMens              = $arrayContratoPromoMens[0]['strObservacion'];
                                $strObservaMens = 'Desct. Fact. Mensual: Promoción Indefinida: NO, Tipo Periodo: UNICO, #Numero de Periodos: '. 
                                                   $arrayPromocion[0]['periodo'] . ' - Descuento: ' . $arrayPromocion[0]['porcentaje'] .'%';
                                $strAplicaCondiciones        = 'Aplica Condiciones';
                                $strObservaMens              = $this->truncarPalabrasObservacion($strObservaMens,440,' ','');
                                $strObservacionContrato      = "{$strNombrePlan}<br>{$strObservaInstalacion}<br>"
                                                             . "{$strObservaMens}<br> {$strAplicaCondiciones}";
    
    
                                $strPeriodosMens        = $arrayPromocion[0]['periodo'];
    
                                $strDescuentoMens       = $arrayPromocion[0]['porcentaje'];

                                $objProdCaracCapNac     = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                              ->findOneBy(array("productoId"       => $planDet->getProductoId(),
                                                                                "caracteristicaId" => $objCaracteristicaCapcNac));
    
                                $objProdCaracCapInt     = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                              ->findOneBy(array("productoId"       => $planDet->getProductoId(),
                                                                                "caracteristicaId" => $objCaracteristicaCapcInt));
    
                                $objServProdCaracCapNac = $this->emComercial->getRepository('schemaBundle:InfoPlanProductoCaract')
                                                              ->findOneBy(array("planDetId"                 => $planDet->getId(),
                                                                                "productoCaracterisiticaId" => $objProdCaracCapNac->getId()));
    
                                $objPlanProdCaracCapInt = $this->emComercial->getRepository('schemaBundle:InfoPlanProductoCaract')
                                                              ->findOneBy(array("planDetId"                 => $planDet->getId(),
                                                                                "productoCaracterisiticaId" => $objProdCaracCapInt->getId()));
                                
                                if(is_object($objServProdCaracCapNac))
                                {
                                    $arrayServicio["velNacMax"] = round($objServProdCaracCapNac->getValor()/1000,1);
                                    $arrayServicio["velNacMin"] = round($arrayServicio["velNacMax"]/2,1);
                                }
                                
                                if(is_object($objPlanProdCaracCapInt))
                                {
                                    $arrayServicio["velIntMax"] = round($objPlanProdCaracCapInt->getValor()/1000,1);
                                    $arrayServicio["velIntMin"] = round($arrayServicio["velNacMax"]/2,1);
                                }
                            }
    
                            
                            $arrayServiciosContratados['INTERNET']["precio"] = $arrayServiciosContratados['INTERNET']["precio"]
                                                                               +($planDet->getPrecioItem());
                            $intImpuesto                = $this->obtenerImpuesto($objProducto);
                            $floatSubtotal              = $floatSubtotal + $planDet->getPrecioItem();
                            $arrayServicio["impuestos"] = $arrayServicio["impuestos"]+($planDet->getPrecioItem()* $intImpuesto/100);
                        }
                        
                        
                        if(isset($strDescuentoMens) && !empty($strDescuentoMens))
                        {
                            $arrayServicio["descPlan"]  = $strDescuentoMens;
                            $arrayServicio["mesesDesc"] = $strPeriodosMens;
                        }
                        
                        $arrayServicio["obsServicio"]  = $strObservacionContrato;
                    }
                    else if($objValue->getProductoId())
                    {
    
                        $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                           ->findOneBy(array("id" => $objValue->getProductoId()));
                                                         
                        $arrayServicioCaracteristica = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                           ->findBy(array("servicioId" => $objValue->getId()));
                        
                        $arrayProductoCaracteristica = array();
                        
                        foreach ($arrayServicioCaracteristica as $arrayServCarac)
                        {
                            $objProductoCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                             ->findOneBy(array("id" => $arrayServCarac->getProductoCaracterisiticaId()));
                                                                                                                      
                            $objCaracteristica         = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                             ->findOneBy(array("id" => $objProductoCaracteristica->getCaracteristicaId()));
    
                            $arrayProductoCaracteristica[$objCaracteristica->getDescripcionCaracteristica()] = $arrayServCarac->getValor();
                        }
                        
                        $intImpuesto = $this->obtenerImpuesto($objProducto);
                        
                        $floatPrecio      = $this->evaluarFuncionPrecio($objProducto->getFuncionPrecio(), $arrayProductoCaracteristica);
    
                        if(!empty($arrayProdParametros) && isset($arrayProdParametros[$objProducto->getCodigoProducto()]) 
                            && !is_null($arrayProdParametros[$objProducto->getCodigoProducto()]))
                        {
                            $strNombreTecnicoServAdic = ($strTipo == 'AS')
                                ? $arrayProdParametros[trim($objProducto->getCodigoProducto())]['valor2']
                                : $arrayProdParametros[trim($objProducto->getCodigoProducto())]['valor5'];
                        }
    
                        if(!empty($strNombreTecnicoServAdic))
                        {
                            $strNombreTecnico = $strNombreTecnicoServAdic;
                        }
                        else
                        {
                            $strNombreTecnico = $objProducto->getNombreTecnico();
                        }
                        
                        $arrayServiciosContratados[$strNombreTecnico]["precio"]   = $arrayServiciosContratados[$strNombreTecnico]["precio"]
                                                                                        +$floatPrecio;
                        $arrayServiciosContratados[$strNombreTecnico]["cantidad"] = $arrayServiciosContratados[$strNombreTecnico]["cantidad"]+1;
                        $floatSubtotal = $floatSubtotal + $floatPrecio;
                                                         
                        $arrayServicio["impuestos"] = $arrayServicio["impuestos"]+($floatPrecio * $intImpuesto/100);
    
                        $arrayServicio["nombrePlan"] = $arrayParametros["nombrePlan"];
                    }
    
                    //CONSULTA LA ULTIMA MILLA
                    $objInfoServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                ->findOneBy(array("servicioId" => $objValue->getId()));
                    if (is_object($objInfoServicioTecnico))
                    {
                        $objUltimaMilla = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                  ->findOneBy(array("id" => $objInfoServicioTecnico->getUltimaMillaId()));
                        if (is_object($objUltimaMilla))
                        {
                            if ($objUltimaMilla->getCodigoTipoMedio() == 'FO')
                            {
                                $arrayServicio["isGeponFibra"] = "X";
                            }
                            else 
                            {
                                $arrayServicio["isDslOtros"] = "X";
                            }
                        }
                    }
                    //VERIFICA SI PLAN ES SIMETRICO O ASIMETRICO 
                    //(Se realiza comparacion entre valores de 
                    //caracteristicas velNacMax:CAPACIDAD1 y velIntMax:CAPACIDAD2)
                    if ($arrayServicio["velNacMax"] == $arrayServicio["velIntMax"])
                    {
                        $arrayServicio["isSimetrico"] = "X";
                    }
                    else
                    {
                        $arrayServicio["isAsimetrico"] = "X";
                    }    
                }
    
            }
            $arrayServicio["detalle"]     = $arrayServiciosContratados;
            $arrayServicio["subtotal"]    = $floatSubtotal;
            $arrayServicio["impuestos"]   = round($arrayServicio["impuestos"], 2);
            $arrayServicio["total"]       = $floatSubtotal+$arrayServicio["impuestos"];
           
            if(isset($arrayServicio["descPlan"]) && !empty($arrayServicio["descPlan"]))
            {
                $arrayServicio["valorPlanDesc"] = $floatSubtotal * ((100-(float)$arrayServicio["descPlan"])/100);
                $arrayServicio["isPrecioPromo"] = "X";
            }
        } 
        catch (\Exception $ex)
        {
            error_log("error InfoContratoDigitalService->retornaServiciosAdendum()" . $ex->getMessage(). " file " . $ex->getFile() .
                      " linea " . $ex->getLine());
            throw $ex;
        }
        return $arrayServicio;
    }

     /*
     * Retorna una cadena truncada según la cantidad de caracteres que se desee obtener.
     * llenar una plantilla para los documentos necesarios al firmar un contrato
     * @param $strCadena - recibe una cadena para truncar según la cantidad de caracteres.
     * @param $intLimite - límite que se desea dejar en ola cadena.
     * @param $strCortar - en que caracter se desea dejar el corte.
     * @param $strCaracter - concatenar caracter como por ejemplo (...)
     * @return $strCadena
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>          
     * @version 1.0 28-07-2020
     * @date    
     */
    public function truncarPalabrasObservacion($strCadena, $intLimite, $strCortar = " ", $strCaracter = "") 
    {  

        if (strlen($strCadena) <= $intLimite)
        {
            return $strCadena;
        }

        if ((false !== ($intMax = strpos($strCadena, $strCortar, $intLimite))) && ($intMax < strlen($strCadena) - 1) ) 
        {

                    $strCadena = substr($strCadena, 0, $intMax) . $strCaracter;

        }

        return $strCadena;

    }

    /**
     * Obtiene el porcentaje de un producto 
     * 
     * @param type $objProducto
     * @return int
     * 
     * @author  Edgar Pin Villavicencio <epin@telconet.ec>          
     * @version 1.0 21-07-2020
     */
    public function obtenerImpuesto($objProducto)
    {
        
        $objProductoImpuesto = $this->emComercial->getRepository('schemaBundle:InfoProductoImpuesto')
                                                 ->findOneBy(array("productoId" => $objProducto->getId()));
        if(is_object($objProductoImpuesto))
        {
            $objImpuesto     = $this->emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                               ->findOneBy(array("id" => $objProductoImpuesto->getImpuestoId()->getId()));
            
            if(is_object($objImpuesto))
            {
                return $objProductoImpuesto->getImpuestoId()->getPorcentajeImpuesto();
            }
        }
        return 0;
    }

    /**
     * crearContratoEmp, método que retorna los parámetros para crear el contrato digital del cliente
     *                   con la empresa que ofrece el servicio
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 17-07-2020
     * @param array $arrayDatosPersona, datos del cliente
     * @return array $arrayContrato, retorna un arreglo con la siguiente información: 
     *         $arrayContrato['fechaActual']                 => fecha actual
     *         $arrayContrato['diaActual']                   => día actual
     *         $arrayContrato['mesActual']                   => mes actual
     *         $arrayContrato['anioActual']                  => año actual
     *         $arrayContrato['pinCode']                     => pin code enviado para crear contrato
     *         $arrayContrato['numeroContrato']              => número de contrato
     *         $arrayContrato['isNuevo']                     => si es o no cliente nuevo
     *         $arrayContrato['isExistente']                 => si existe o no cliente
     *         $arrayContrato['isNatural']                   => si tipo de cliente es natural
     *         $arrayContrato['isJuridico']                  => si tipo de cliente es juridico
     *         $arrayContrato['nombresApellidos']            => nombre del cliente
     *         $arrayContrato['identificacion']              => identificación del cliente
     *         $arrayContrato['nacionalidad']                => nacionalidad del cliente
     *         $arrayContrato['estadoCivil']                 => estado civil del cliente
     *         $arrayContrato['origenIngresos']              => origen ingresos del cliente
     *         $arrayContrato['isMasculino']                 => es masculino el cliente
     *         $arrayContrato['isFemenino']                  => es femenino el cliente
     *         $arrayContrato['ruc']                         => ruc del cliente
     *         $arrayContrato['representanteLegal']          => representante legal del cliente
     *         $arrayContrato['ciRepresentanteLegal']        => ci del representante legal
     *         $arrayContrato['actividadEconomica']          => actividad economica del cliente
     *         $arrayContrato['direccion']                   => dirección del cliente
     *         $arrayContrato['referencia']                  => referencia del cliente
     *         $arrayContrato['longuitud']                   => longitud de coordenadas 
     *         $arrayContrato['latitud']                     => latitud de coordenadas
     *         $arrayContrato['sector']                      => sector donde vive cliente
     *         $arrayContrato['canton']                      => canton donde vive cliente
     *         $arrayContrato['cantonServicio']              => canton del servicio
     *         $arrayContrato['parroquiaServicio']           => parroquia del servicio
     *         $arrayContrato['canton']                      => canton donde vive cliente
     *         $arrayContrato['ciudadServicio']              => ciudad del servicio
     *         $arrayContrato['sectorServicio']              => sector del servicio
     *         $arrayContrato['longuitudServicio']           => longitud del servicio
     *         $arrayContrato['latitudServicio']             => latitud del servicio
     *         $arrayContrato['direccionServicio']           => direccion del servicio
     *         $arrayContrato['isCasa']                      => si es o no es casa donde vive el cliente
     *         $arrayContrato['isEdificio']                  => si es o no un edificio donde vive el cliente
     *         $arrayContrato['isConjunto']                  => si es o no un conjunto donde vive el cliente
     *         $arrayContrato['casaServicio']                => si es o no es casa donde se instala servicio
     *         $arrayContrato['edificioServicio']            => si es o no un edificio donde se instala servicio
     *         $arrayContrato['conjuntoServicio']            => si es o no un conjunto donde se instala servicio
     *         $arrayContrato['observacionPunto']            => observación del punto
     *         $arrayContrato['correoCliente']               => correo del cliente
     *         $arrayContrato['telefonoCliente']             => teléfono del cliente
     *         $arrayContrato['celularCliente']              => celular del cliente
     *         $arrayContrato['refFamiliar1']                => referencia familiar 1
     *         $arrayContrato['telefonoFamiliar1']           => telefono referencia familiar 1
     *         $arrayContrato['refFamiliar2']                => referencia familiar 2
     *         $arrayContrato['telefonoFamiliar2']           => telefono referencia familiar 2
     *         $arrayContrato['nombreVendedor']              => nombre del vendedor
     *         $arrayContrato['codigoVendedor']              => Código del vendedor
     *         $arrayContrato['isSi']                        => es si
     *         $arrayContrato['isNo']                        => es no
     *         $arrayContrato['correoContacto']              => correo del contacto
     *         $arrayContrato['personaContacto']             => persona del contacto
     *         $arrayContrato['telefonoContacto']            => teléfono del contacto
     *         $arrayContrato['celularContacto']             => celular del contacto
     *         $arrayContrato['horarioContacto']             => horario del contacto
     *         $arrayContrato['isTarjetaCredito']            => forma de pago es o no tarjeta de crédito
     *         $arrayContrato['isCuentaAhorros']             => forma de pago es o no cta de ahorros
     *         $arrayContrato['isCuentaCorriente']           => forma de pago es o no cta corriente
     *         $arrayContrato['identificacionAutDebito']     => identificación para la autorización de débito
     *         $arrayContrato['fechaActualAutDebito']        => fecha actual para la autorización de débito
     *         $arrayContrato['numeroCuenta']                => número de cuenta
     *         $arrayContrato['numeroCVV']                   => numero de CVV de la tarjeta de crédito
     *         $arrayContrato['nombreTitular']               => nombre del titular de la cuenta
     *         $arrayContrato['nombreBanco']                 => nombre del banco
     *         $arrayContrato['fechaExpiracion']             => fecha de expiracion de la tarjeta de credito
     *         $arrayContrato['isHome']                      => si es o no Home el servicio
     *         $arrayContrato['isPyme']                      => si es o no Pyme el servicio
     *         $arrayContrato['isPro']                       => si es o no Pro el servicio
     *         $arrayContrato['obsServicio']                 => observación del servicio
     *         $arrayContrato['isGeponFibra']                => ultima milla es fibra
     *         $arrayContrato['isDslOtros']                  => ultima milla es dsl u otros
     *         $arrayContrato['isSimetrico']                 => es simetrico
     *         $arrayContrato['isAsimetrico']                => es asimetrico
     *         $arrayContrato['valorPlanLetras']             => valor de plan en letras
     *         $arrayContrato['valorPlanNumeros']            => valor de plan en numeros
     *         $arrayContrato['numeroMesesPromo']            => numero de meses para aplicar promocion
     *         $arrayContrato['precioPromo']                 => precio de promocion para el plan
     *         $arrayContrato['subtotal']                    => valor subtotal
     *         $arrayContrato['impuestos']                   => valor impuestos
     *         $arrayContrato['velNacMax']                   => velocidad máxima nacional
     *         $arrayContrato['velNacMin']                   => velocidad minima nacional
     *         $arrayContrato['velIntMax']                   => velocidad máxima internacional
     *         $arrayContrato['velIntMin']                   => velocidad minima internacional
     *         $arrayContrato['descPlan']                    => descuento del plan
     *         $arrayContrato['mesesDesc']                   => meses de descuento
     *         $arrayContrato['total']                       => valor total
     *         $arrayContrato['productoInternetPrecio']      => precio del producto internet
     *         $arrayContrato['productoInternetCantidad']    => cantidad del producto internet
     *         $arrayContrato['productoOtrosPrecio']         => precio de otros productos
     *         $arrayContrato['productoOtrosCantidad']       => cantidad de otros productos
     *         $arrayContrato['productoIpPrecio']            => precio de producto ip
     *         $arrayContrato['productoIpCantidad']          => cantidad de producto ip
     *         $arrayContrato['productoWifiPrecio']          => precio de producto wifi
     *         $arrayContrato['productoWifiCantidad']        => cantidad de producto wifi
     *         $arrayContrato['productoInternetInstalacion'] => valor instalación producto internet
     *         $arrayContrato['isDescInstalacion']           => si tiene o no descuento de instalación
     *         $arrayContrato['isPrecioPromo']               => si tiene o no precio mensual de promoción
     *         $arrayContrato['isFactElectronica']           => si es o no facturación electrónica
     *         $arrayContrato['descInstalacion']             => valor descuento en instalación
     *         $arrayContrato['subtotalInstalacion']         => valor subtotal instalación
     *         $arrayContrato['totalInstalacion']            => valor total instalación
     *         $arrayContrato['impInstalacion']              => valor impuesto instalación
     *         $arrayContrato['productoIpCantidad']          => cantidad producto ip
     *         $arrayContrato['isEfectivo']                  => forma de pago es o no efectivo
     *         $arrayContrato['provincia']                   => provincia
     *         $arrayContrato['telefono']                    => número de teléfono
     *         $arrayContrato['ciudad']                      => ciudad del cliente
     *         $arrayContrato['referenciaCliente']           => referencia del cliente
     *         $arrayContrato['celular']                     => celular del cliente
     *         $arrayContrato['isAceptacionBeneficios']      => el cliente acepta beneficios
     * 
     */
    public function crearContratoEmp($arrayDatosPersona)
    {
        
        $arrayMeses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $strMes= (string)$arrayMeses[$arrayDatosPersona['feCreacion']->format('n')-1];
        $arrayContrato = array(
            array('k'   => 'fechaActual', 
                  'v'   => $arrayDatosPersona['feCreacion']->format('Y/m/d')),
            array('k'   => 'diaActual', 
                  'v'   => $arrayDatosPersona['feCreacion']->format('d')),
            array('k'   => 'mesActual', 
                  'v'   => $strMes),
            array('k'   => 'anioActual', 
                  'v'   => $arrayDatosPersona['feCreacion']->format('Y')),
            array('k'   => 'pinCode',
                  'v'   => ""),
            array('k'   => 'numeroContrato', 
                  'v'   => $arrayDatosPersona['numeroContrato']),
            array('k'   => 'isNuevo',
                  'v'   => $arrayDatosPersona['isNuevo']),
            array('k'   => 'isExistente', 
                  'v'   => $arrayDatosPersona['isExistente']),
            array('k'   => 'isNatural', 
                  'v'   => $arrayDatosPersona['isNatural']),
            array('k'   => 'isJuridico', 
                  'v'   => $arrayDatosPersona['isJuridico']),
            array('k'   => 'nombresApellidos', 
                  'v'   => strtoupper($arrayDatosPersona['nombreCliente'])),
            array('k'   => 'identificacion', 
                  'v'   => $arrayDatosPersona['cedula']),
            array('k'   => 'nacionalidad', 
                  'v'   => $arrayDatosPersona['nacionalidad']),
            array('k'   => 'razonSocial', 
                  'v'   => strtoupper($arrayDatosPersona['razonSocial'])),
            array('k'   => 'isMasculino', 
                  'v'   => strtoupper($arrayDatosPersona['isMasculino'])),
            array('k'   => 'isFemenino', 
                  'v'   => strtoupper($arrayDatosPersona['isFemenino'])),
            array('k'   => 'estadoCivil', 
                  'v'   => strtoupper($arrayDatosPersona['estadoCivil'])),
            array('k'   => 'origenIngresos', 
                  'v'   => strtoupper($arrayDatosPersona['origenIngresos'])),
            array('k'   => 'ruc', 
                  'v'   => $arrayDatosPersona['ruc']),
            array('k'   => 'representateLegal', 
                  'v'   => strtoupper($arrayDatosPersona['representanteLegal'])),
            array('k'   => 'ciRepresentanteLegal', 
                  'v'   => ''),
            array('k'   => 'actividadEconomica', 
                  'v'   => strtoupper($arrayDatosPersona['actividadEconomica'])),
            array('k'   => 'direccion', 
                  'v'   => strtoupper($arrayDatosPersona['direccion'])),
            array('k'   => 'referenciaServicio', 
                  'v'   => strtoupper($arrayDatosPersona['referenciaServicio'])),
            array('k'   => 'longuitud', 
                  'v'   => $arrayDatosPersona['longuitudServicio']),
            array('k'   => 'latitud', 
                  'v'   => $arrayDatosPersona['latitudServicio']),
            array('k'   => 'longuitudServicio', 
                  'v'   => $arrayDatosPersona['longuitudServicio']),
            array('k'   => 'latitudServicio', 
                  'v'   => $arrayDatosPersona['latitudServicio']),
            array('k'   => 'direccionServicio', 
                  'v'   => strtoupper($arrayDatosPersona['direccionServicio'])),
            array('k'   => 'sectorServicio', 
                  'v'   => strtoupper($arrayDatosPersona['sectorServicio'])),
            array('k'   => 'sector', 
                  'v'   => strtoupper($arrayDatosPersona['sectorServicio'])),
            array('k'   => 'canton', 
                  'v'   => strtoupper($arrayDatosPersona['canton'])),
            array('k'   => 'cantonServicio', 
                  'v'   => strtoupper($arrayDatosPersona['cantonServicio'])),
            array('k'   => 'parroquia', 
                  'v'   => strtoupper($arrayDatosPersona['parroquia'])),
            array('k'   => 'parroquiaServicio', 
                  'v'   => strtoupper($arrayDatosPersona['parroquiaServicio'])),
            array('k'   => 'ciudadServicio', 
                  'v'   => strtoupper($arrayDatosPersona['ciudadServicio'])),
            array('k'   => 'isCasa', 
                  'v'   => strtoupper($arrayDatosPersona['casaServicio'])),
            array('k'   => 'isEdificio', 
                  'v'   => $arrayDatosPersona['edificioServicio']),
            array('k'   => 'observacionPunto', 
                  'v'   => strtoupper($arrayDatosPersona['observacionPunto'])),
            array('k'   => 'isConjunto', 
                  'v'   => $arrayDatosPersona['conjuntoServicio']),
            array('k'   => 'casaServicio', 
                  'v'   => strtoupper($arrayDatosPersona['casaServicio'])),
            array('k'   => 'edificioServicio', 
                  'v'   => $arrayDatosPersona['edificioServicio']),
            array('k'   => 'conjuntoServicio', 
                  'v'   => $arrayDatosPersona['conjuntoServicio']),
            array('k'   => 'correoCliente', 
                  'v'   => $arrayDatosPersona['correo']),
            array('k'   => 'telefonoCliente', 
                  'v'   => $arrayDatosPersona['telefono']),
            array('k'   => 'celularCliente', 
                  'v'   => $arrayDatosPersona['celular']),
            array('k'   => 'refFamiliar1', 
                  'v'   => $arrayDatosPersona['nombreRef1']),
            array('k'   => 'telefonoFamiliar1', 
                  'v'   => $arrayDatosPersona['telefonoRef1']),
            array('k'   => 'refFamiliar2', 
                  'v'   => $arrayDatosPersona['nombreRef2']),
            array('k'   => 'telefonoFamiliar2', 
                  'v'   => $arrayDatosPersona['telefonoRef2']),
            array('k'   => 'nombreVendedor', 
                  'v'   => strtoupper($arrayDatosPersona['nombreVendedor'])),
            array('k'   => 'codigoVendedor', 
                  'v'   => $arrayDatosPersona['codigoVendedor']),
            array('k'   => 'isSi', 
                  'v'   => $arrayDatosPersona['isSi']),
            array('k'   => 'isNo', 
                  'v'   => $arrayDatosPersona['isNo']),
            array('k'   => 'correoContacto', 
                  'v'   => $arrayDatosPersona['correoServicio']),
            array('k'   => 'personaContacto', 
                  'v'   => $arrayDatosPersona['contactoServicio']),
            array('k'   => 'telefonoContacto', 
                  'v'   => $arrayDatosPersona['telefonoServicio']),
            array('k'   => 'celularContacto', 
                  'v'   => $arrayDatosPersona['celularServicio']),
            array('k'   => 'horarioContacto', 
                  'v'   => $arrayDatosPersona['horarioServicio']),
            array('k'   => 'isTarjetaCredito', 
                  'v'   => $arrayDatosPersona['fpTarjetaCredito']),
            array('k'   => 'isCuentaAhorros', 
                  'v'   => $arrayDatosPersona['fpCtaAhorros']),
            array('k'   => 'isCuentaCorriente', 
                  'v'   => $arrayDatosPersona['fpCtaCorriente']),
            array('k'   => 'identificacionAutDebito', 
                  'v'   => $arrayDatosPersona['identificacionAutDebito']),
            array('k'   => 'fechaActualAutDebito', 
                  'v'   => $arrayDatosPersona['fechaActualAutDebito']),
            array('k'   => 'numeroCuenta', 
                  'v'   => $arrayDatosPersona['numeroCuenta']),
            array('k'   => 'numeroCVV', 
                  'v'   => $arrayDatosPersona['numeroCVV']),
            array('k'   => 'nombreTitular', 
                  'v'   => strtoupper($arrayDatosPersona['nombreTitular'])),
            array('k'   => 'nombreBanco', 
                  'v'   => strtoupper($arrayDatosPersona['nombreBanco'])),
            array('k'   => 'fechaExpiracion', 
                  'v'   => $arrayDatosPersona['fechaExpiracion']),
            array('k'   => 'isHome', 
                  'v'   => $arrayDatosPersona['arrServicios']['isHome']),
            array('k'   => 'isPyme', 
                  'v'   => $arrayDatosPersona['arrServicios']['isPyme']),
            array('k'   => 'isPro', 
                  'v'   => $arrayDatosPersona['arrServicios']['isPro']),
            array('k'   => 'obsServicio', 
                  'v'   => $arrayDatosPersona['arrServicios']['obsServicio']),
            array('k'   => 'isGeponFibra', 
                  'v'   => $arrayDatosPersona['arrServicios']['isGeponFibra']),
            array('k'   => 'isDslOtros', 
                  'v'   => $arrayDatosPersona['arrServicios']['isDslOtros']),
            array('k'   => 'isSimetrico', 
                  'v'   => $arrayDatosPersona['arrServicios']['isSimetrico']),
            array('k'   => 'isAsimetrico', 
                  'v'   => $arrayDatosPersona['arrServicios']['isAsimetrico']),
            array('k'   => 'valorPlanLetras', 
                  'v'   => $arrayDatosPersona['arrServicios']['valorPlanLetras']),
            array('k'   => 'valorPlanNumeros', 
                  'v'   => $arrayDatosPersona['arrServicios']['valorPlanNumeros']),
            array('k'   => 'subtotal', 
                  'v'   => $arrayDatosPersona['arrServicios']['subtotal']),
            array('k'   => 'impuestos', 
                  'v'   => $arrayDatosPersona['arrServicios']['impuestos']),
            array('k'   => 'velNacMax', 
                  'v'   => $arrayDatosPersona['arrServicios']['velNacMax']),
            array('k'   => 'velNacMin', 
                  'v'   => $arrayDatosPersona['arrServicios']['velNacMin']),
            array('k'   => 'velIntMax', 
                  'v'   => $arrayDatosPersona['arrServicios']['velIntMax']),
            array('k'   => 'velIntMin', 
                  'v'   => $arrayDatosPersona['arrServicios']['velIntMin']),
            array('k'   => 'descPlan', 
                  'v'   => $arrayDatosPersona['arrServicios']['descPlan']),
            array('k'   => 'mesesDesc', 
                  'v'   => $arrayDatosPersona['arrServicios']['mesesDesc']),
            array('k'   => 'numeroMesesPromo', 
                  'v'   => $arrayDatosPersona['arrServicios']['mesesDesc']),
            array('k'   => 'precioPromo', 
                  'v'   => $arrayDatosPersona['arrServicios']['valorPlanDesc']),
            array('k'   => 'isPrecioPromo', 
                  'v'   => $arrayDatosPersona['arrServicios']['isPrecioPromo']),
            array('k'   => 'total',
                  'v'   => $arrayDatosPersona['arrServicios']['total']),
            array('k'   => 'productoInternetPrecio', 
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['INTERNET']['precio']),
            array('k'   => 'productoInternetCantidad', 
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['INTERNET']['cantidad']),
            array('k'   => 'productoOtrosPrecio', 
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['OTROS']['precio']),
            array('k'   => 'productoOtrosCantidad',
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['OTROS']['cantidad']),
            array('k'   => 'productoNetLifeZonePrecio', 
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['NETWIFI']['precio']),
            array('k'   => 'productoNetLifeZoneCantidad',
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['NETWIFI']['cantidad']),
            array('k'   => 'productoNetLifeCamPrecio', 
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['NETLIFECAM']['precio']),
            array('k'   => 'productoNetLifeCamCantidad',
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['NETLIFECAM']['cantidad']),
            array('k'   => 'productoWifiPrecio', 
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['APWIFI']['precio']),
            array('k'   => 'productoWifiCantidad',
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['APWIFI']['cantidad']),
            array('k'   => 'productoInternetInstalacion',
                  'v'   => $arrayDatosPersona['precioInstalacion']),
            array('k'   => 'descInstalacion',
                  'v'   => $arrayDatosPersona['descInstalacion']),
            array('k'   => 'isDescInstalacion',
                  'v'   => $arrayDatosPersona['isDescInstalacion']),
            array('k'   => 'isFactElectronica',
                  'v'   => $arrayDatosPersona['isFactElectronica']),
            array('k'   => 'subtotalInstalacion',
                  'v'   => $arrayDatosPersona['subtotalInstalacion']),
            array('k'   => 'totalInstalacion',
                  'v'   => $arrayDatosPersona['totalInstalacion']),
            array('k'   => 'impInstalacion',
                  'v'   => $arrayDatosPersona['impInstalacion']),
            array('k'   => 'isEfectivo', 
                  'v'   => $arrayDatosPersona['fpEfectivo']),
            array('k'   => 'provincia', 
                  'v'   => $arrayDatosPersona['provincia']),
            array('k'   => 'telefono', 
                  'v'   => $arrayDatosPersona['telefono']),
            array('k'   => 'ciudad', 
                  'v'   => $arrayDatosPersona['ciudad']),
            array('k'   => 'referenciaCliente', 
                  'v'   => $arrayDatosPersona['referenciaCliente']),
            array('k'   => 'celular', 
                  'v'   => $arrayDatosPersona['celular']),
            array('k'   => 'isAceptacionBeneficios',
                  'v'   => "X"),
            array('k'   => 'isSiAutoriza', 
                  'v'   => "X"),
            array('k'   => 'isNoAutoriza',
                  'v'   => ""),
            array('k'   => 'isSiRenueva', 
                  'v'   => "X"),
            array('k'   => 'isNoRenueva',
                  'v'   => ""),
            array('k'   => 'isSiAcceder', 
                  'v'   => "X"),
            array('k'   => 'isNoAcceder',
                  'v'   => ""), 
            array('k'   => 'isSiMediacion', 
                  'v'   => "X"),
            array('k'   => 'isNoMediacion',
                  'v'   => ""),
            array('k'   => 'isDiscapacitadoSi', 
                  'v'   => ""),
            array('k'   => 'isDiscapacitadoNo',
                  'v'   => "X"),
            array('k'   => 'is2a1',
                  'v'   => "X"),                  
            array('k'   => 'loginPunto',
                  'v'   => $arrayDatosPersona['loginPunto']),
            array('k'   => 'isCedula',
                  'v'   => $arrayDatosPersona['isCedula']),
            array('k'   => 'isRuc',
                  'v'   => $arrayDatosPersona['isRuc']),
            array('k'   => 'isPasaporte',
                  'v'   => $arrayDatosPersona['isPasaporte']),
            array('k'   => 'numeroAdendum',
                  'v'   => ($arrayDatosPersona['strTipo'] == "C")
                              ? $arrayDatosPersona['numeroContrato']
                              : $arrayDatosPersona['numeroAdendum']),
            array('k'   => 'verNumeroAdendum',
                  'v'   => ($arrayDatosPersona['strTipo'] == "C")
                              ? "display: none;"
                              : ""),
            array('k'   => 'verLeyenda',
                  'v'   => ($arrayDatosPersona['regulariza'])
                              ? ""
                              : "display: none;"),
            
            );
        
        return $arrayContrato;
    }

    /**
     * crearContratoSd, método que retorna los parámetros para crear el contrato digital del cliente
     *                  security data
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 17-07-2020
     *          
     * @return array $arrayContrato, retorna un arreglo con la siguiente información: 
     *         $arrayContrato['nombresApellidos'] => Nombres del cliente
     *         $arrayContrato['identificacion']   => Identificación de cliente
     *         $arrayContrato['ruc']              => Ruc del cliente
     *         $arrayContrato['fechaActual']      => Fecha del sistema
     * 
     */
    public function crearContratoSd($arrayDatosPersona)
    {
        $arrayContrato = array(
            array('k'   => 'nombresApellidos', 
                  'v'   => strtoupper($arrayDatosPersona['nombreCliente'])),
            array('k'   => 'identificacion', 
                  'v'   => $arrayDatosPersona['cedula']),
            array('k'   => 'ruc', 
                  'v'   => $arrayDatosPersona['ruc']),
            array('k'   => 'fechaActual',
                  'v'   => date('Y/m/d')),
            array('k'   => 'isPersonaNatural',
                  'v'   => $arrayDatosPersona['isNatural']));

        return $arrayContrato;
    }
    
    /**
     * crearAdendumAdicional, Método que retorna los parámetros necesarios para el adendum de servicio del cliente
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 18-07-2020
     * @param  array $arrayDatosPersona, datos del cliente
     * @return array $arrayAdendumAdicional, retorna un arreglo con la siguiente información:
     *         $arrayDatosPersona['fechaActual']         => fecha actual
     *         $arrayDatosPersona['numeroContrato']      => número del contrato
     *         $arrayDatosPersona['numeroAdendum']       => número del adendum
     *         $arrayDatosPersona['nombresApellidos']    => nombres del cliente
     *         $arrayDatosPersona['cedula']              => cédula del cliente
     *         $arrayDatosPersona['razonSocial']         => razón social del cliente
     *         $arrayDatosPersona['ruc']                 => ruc del cliente
     *         $arrayDatosPersona['direccion']           => dirección del cliente
     *         $arrayDatosPersona['referenciaServicio']  => referencia del servicio
     *         $arrayDatosPersona['correoCliente']       => correo del cliente
     *         $arrayDatosPersona['telefonoCliente']     => teléfono del cliente
     *         $arrayDatosPersona['celularCliente']      => celular del cliente
     *         $arrayDatosPersona['correoContacto']      => correo del contacto
     *         $arrayDatosPersona['contactoServicio']    => nombres del contacto
     *         $arrayDatosPersona['telefonoServicio']    => teléfono del contacto
     *         $arrayDatosPersona['celularServicio']     => celular del contacto
     *         $arrayDatosPersona['obsServicio']         => observación del servicio
     *         $arrayDatosPersona['descPlan']            => descripción del plan del punto
     *         $arrayDatosPersona['nombrePlan']          => nombre del plan del punto
     *         $arrayDatosPersona['loginPunto']          => login del punto
     *         $arrayDatosPersona['subtotal']            => subtotal de los servicios
     *         $arrayDatosPersona['impuestos']           => impuestos de los servicios
     *         $arrayDatosPersona['total']               => total de los servicios
     *         $arrayDatosPersona['detalle']             => detalle de servicios adicionales
     *         $arrayDatosPersona['arrayProdParametros'] => servicios adicionales parametrizados
     */
    public function crearAdendumAdicional($arrayDatosPersona)
    {


        $arrayAdendumAdicional = array(
            array('k'   => 'fechaActual',
                  'v'   => $arrayDatosPersona['feCreacion']->format('Y/m/d')),
            array('k'   => 'numeroContrato',
                  'v'   => $arrayDatosPersona['numeroContrato']),
            array('k'   => 'numeroAdendum',
                  'v'   => $arrayDatosPersona['numeroAdendum']),
            array('k'   => 'nombresApellidos',
                  'v'   => strtoupper($arrayDatosPersona['nombreCliente'])),
            array('k'   => 'cedula',
                  'v'   => ($arrayDatosPersona['isPasaporte'] == "X")
                              ? $arrayDatosPersona['cedula'] . " (Pasaporte)"
                              : $arrayDatosPersona['cedula']),
            array('k'   => 'razonSocial',
                  'v'   => strtoupper($arrayDatosPersona['razonSocial'])),
            array('k'   => 'ruc',
                  'v'   => $arrayDatosPersona['ruc']),
            array('k'   => 'direccion',
                  'v'   => strtoupper($arrayDatosPersona['direccion'])),
            array('k'   => 'referenciaServicio',
                  'v'   => strtoupper($arrayDatosPersona['referenciaServicio'])),
            array('k'   => 'correoCliente',
                  'v'   => $arrayDatosPersona['correo']),
            array('k'   => 'telefonoCliente',
                  'v'   => $arrayDatosPersona['telefono']),
            array('k'   => 'celularCliente',
                  'v'   => $arrayDatosPersona['celular']),
            array('k'   => 'correoContacto',
                  'v'   => $arrayDatosPersona['correoServicio']),
            array('k'   => 'obsServicio',
                  'v'   => $arrayDatosPersona['arrServicios']['obsServicio']),
            array('k'   => 'subtotal',
                  'v'   => $arrayDatosPersona['arrServicios']['subtotal']),
            array('k'   => 'impuestos',
                  'v'   => $arrayDatosPersona['arrServicios']['impuestos']),
            array('k'   => 'descPlan',
                  'v'   => $arrayDatosPersona['arrServicios']['descPlan']),
            array('k'   => 'nombrePlan',
                  'v'   => $arrayDatosPersona['arrServicios']['nombrePlan']),
            array('k'   => 'total',
                  'v'   => $arrayDatosPersona['arrServicios']['total']),
            array('k'   => 'loginPunto',
                  'v'   => $arrayDatosPersona['loginPunto']),
            array('k'   => 'verLeyenda',
                  'v'   => ($arrayDatosPersona['regulariza'])
                              ? ""
                              : "display: none;"),

        );
        
        if(empty($arrayDatosPersona['contactoServicio']))
        {
            array_push($arrayAdendumAdicional, array('k' => 'contactoServicio',
                                                            'v' => strtoupper($arrayDatosPersona['nombreCliente'])),
                                                      array('k' => 'celularServicio',
                                                            'v' => strtoupper($arrayDatosPersona['celular'])),
                                                      array('k' => 'telefonoServicio',
                                                            'v' => strtoupper($arrayDatosPersona['telefono'])));
        }
        else
        {
            array_push($arrayAdendumAdicional, array('k' => 'contactoServicio',
                                                            'v' => strtoupper($arrayDatosPersona['contactoServicio'])),
                                                      array('k' => 'celularServicio',
                                                            'v' => strtoupper($arrayDatosPersona['celularServicio'])),
                                                      array('k' => 'telefonoServicio',
                                                            'v' => strtoupper($arrayDatosPersona['telefonoServicio'])));
        }

        if(isset($arrayDatosPersona['arrServicios']['arrayProdParametros']))
        {
            foreach($arrayDatosPersona['arrServicios']['arrayProdParametros'] as $arrayProdParametro)
            {
                array_push($arrayAdendumAdicional,
                    array('k' => $arrayProdParametro['valor2'] . 'Precio',
                          'v' => $arrayDatosPersona['arrServicios']['detalle'][$arrayProdParametro['valor2']]['precio']),
                    array('k' => $arrayProdParametro['valor2'] . 'Cantidad',
                          'v' => $arrayDatosPersona['arrServicios']['detalle'][$arrayProdParametro['valor2']]['cantidad']));
            }
        }

        $arrayAdendumAdicional['arrayProdParametros'] = $arrayDatosPersona['arrServicios']['arrayProdParametros'];

        return $arrayAdendumAdicional;
    }

    /**
     * crearFormularioSD, método que retorna los parámetros para crear el formulario que debe
     *                    llenar el cliente con security data
     * @author Edgar Pin Villavicenio <epin@telconet.ec>
     * @version 1.0 17-07-2020
     * 
     * @param array $arrayDatosPersona, datos del cliente. Se obtiene la siguiente información:
     *          $arrayDatosPersona['nombreCliente'] => nombre del cliente
     *          $arrayDatosPersona['cedula']        => cedula del cliente
     *          $arrayDatosPersona['nacionalidad']  => nacionalidad del cliente
     *          $arrayDatosPersona['correo']        => correo del cliente
     *          $arrayDatosPersona['direccion']     => direccion del cliente
     *          $arrayDatosPersona['provincia']     => provincia del cliente
     *          $arrayDatosPersona['telefono']      => telefono del cliente
     *          $arrayDatosPersona['ciudad']        => ciudad del cliente
     *          $arrayDatosPersona['celular']       => celular del cliente
     * 
     * @return array $arrayFormulario, retorna un arreglo con la siguiente información: 
     *         $arrayFormulario['nombresApellidos'] => Nombres del cliente
     *         $arrayFormulario['identificacion']   => Identificación de cliente
     *         $arrayFormulario['nacionalidad']     => Nacionalidad del cliente
     *         $arrayFormulario['email']            => Correo electrónico del cliente
     *         $arrayFormulario['direccion']        => Dirección del cliente
     *         $arrayFormulario['provincia']        => Provincia donde se encuentra el punto instalado
     *         $arrayFormulario['ciudad']           => Ciudad donde se encuentra el punto instalado
     *         $arrayFormulario['celular']          => Número celular de cliente
     *         $arrayFormulario['fechaActual']      => Fecha del sistema
     */
    public function crearFormularioSD($arrayDatosPersona)
    {
        $arrayFormulario = array(
            array('k' => 'nombresApellidos', 
                  'v' => strtoupper($arrayDatosPersona['nombreCliente'])),
            array('k' => 'identificacion', 
                  'v' => $arrayDatosPersona['cedula']),
            array('k' => 'nacionalidad', 
                  'v' => $arrayDatosPersona['nacionalidad']),
            array('k' => 'emailCliente', 
                  'v' => $arrayDatosPersona['correo']),
            array('k' => 'direccion', 
                  'v' => strtoupper($arrayDatosPersona['direccion'])),
            array('k' => 'provincia', 
                  'v' => $arrayDatosPersona['provincia']),
            array('k' => 'telefono', 
                  'v' => $arrayDatosPersona['telefono']),
            array('k' => 'ciudad', 
                  'v' => strtoupper($arrayDatosPersona['ciudad'])),
            array('k' => 'celular', 
                  'v' => $arrayDatosPersona['celular']),
            array('k' => 'fechaActual', 
                  'v' => date('Y/m/d')));
        return $arrayFormulario;
    }

    /**
     * crearDebitoEmp, método que retorna los parámetros para crear el documento de debito del cliente
     *                   con la empresa que ofrece el servicio
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 28-07-2020
     * @param array $arrayDatosPersona, datos del cliente
     * @return array $arrayDebito, retorna un arreglo con la información del debito bancario 
     */
    public function crearDebitoEmp($arrayDatosPersona)
    {
        $arrayDebito = array(
            array('k'   => 'numeroContrato', 
                  'v'   => $arrayDatosPersona['numeroContrato']),
            array('k'   => 'nombreBanco', 
                  'v'   => strtoupper($arrayDatosPersona['nombreBanco'])),
            array('k'   => 'fechaActualAutDebito', 
                  'v'   => $arrayDatosPersona['fechaActualAutDebito']),
            array('k'   => 'nombreTitular', 
                  'v'   => strtoupper($arrayDatosPersona['nombreTitular'])),
            array('k'   => 'identificacionAutDebito', 
                  'v'   => $arrayDatosPersona['identificacionAutDebito']),
            array('k'   => 'isTarjetaCredito', 
                  'v'   => $arrayDatosPersona['fpTarjetaCredito']),
            array('k'   => 'isCuentaAhorros', 
                  'v'   => $arrayDatosPersona['fpCtaAhorros']),
            array('k'   => 'isCuentaCorriente', 
                  'v'   => $arrayDatosPersona['fpCtaCorriente']),
            array('k'   => 'numeroCuenta', 
                  'v'   => $arrayDatosPersona['numeroCuenta']),
            array('k'   => 'fechaExpiracion', 
                  'v'   => $arrayDatosPersona['fechaExpiracion']));
        return $arrayDebito;
    }
    
    /**
     * crearPagareEmp, método que retorna los parámetros para crear el Pagare del cliente
     *                   con la empresa que ofrece el servicio
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 28-07-2020
     * @param array $arrayDatosPersona, datos del cliente
     * @return array $arrayDebito, retorna un arreglo con la información del Pagare 
     */    
    public function crearPagareEmp($arrayDatosPersona)
    {
        $arrayPagare = array(
            array('k'   => 'numeroContrato', 
                  'v'   => $arrayDatosPersona['numeroContrato']),
            array('k'   => 'valorPlanLetras', 
                  'v'   => $arrayDatosPersona['arrServicios']['valorPlanLetras']),
            array('k'   => 'valorPlanNumeros', 
                  'v'   => $arrayDatosPersona['arrServicios']['valorPlanNumeros']),
            array('k'   => 'ciudad', 
                  'v'   => $arrayDatosPersona['ciudad']),
            array('k'   => 'diaActual', 
                  'v'   => date('d')),
            array('k'   => 'mesActual', 
                  'v'   => date('m')),
            array('k'   => 'anioActual', 
                  'v'   => date('Y')),
            array('k'   => 'nombresApellidos', 
                  'v'   => strtoupper($arrayDatosPersona['nombreCliente'])));
        return $arrayPagare;
    }
    /**
     * Genera las propiedades necesarias sobra la ubicación de la firma en el documento
     * @param type $arrayParametros [strPlantilla, strCertificado64, arrayCertificado]
     * @return type $arrayCertificadoContratoEmpresa [cerftificadoPfx, plantilla, enviaMail, certificado, propiedades, codigo, firmas] 
     * 
     * @author  Edgar Pin Villavicencio <epin@telconet.ec>          
     * @version 1.0 21-07-2020
     *
     */
    public function generaPropiedadesPlantilla($arrayParametros)
    {

        $objEmpresaParametro = $this->emFirmaElect->getRepository('schemaBundle:AdmEmpresaParametro')
                                                  ->findOneByValor($arrayParametros['strPlantilla']);
        $objEmpresaPlantilla = $this->emFirmaElect->getRepository('schemaBundle:AdmEmpresaPlantilla')
                                                  ->findByCodPlantilla($arrayParametros['strPlantilla']);

        $arrayCertificadoContratoEmpresa = array();

        if(!is_null($objEmpresaParametro))
        {
            $arrayCertificadoContratoEmpresa['enviaMail'] = $objEmpresaParametro->getEnviaPorMail();
        }

        if(!is_null($objEmpresaPlantilla))
        {
            $arrayPropiedades  = $this->emFirmaElect->getRepository('schemaBundle:AdmEmpPlantCert')
                ->findBy(array("plantillaId" => $objEmpresaPlantilla[0]->getId()));
            
            $arrayCertificadoContratoEmpresa['plantilla'] = $objEmpresaPlantilla[0]->getHtml();
        }
        
        $strCertificado = $this->strPathTelcos . $this->strRutaCertificado . 
                          $arrayParametros['arrayCertificadoSd']['numCedula'] .  ".pfx";
        $strCertificado64 = base64_encode(file_get_contents($strCertificado));
        
        if(!is_null($arrayPropiedades))
        {
            foreach ($arrayPropiedades as $objPropiedades)
            {
                $arrayFirma = array();
                if ($objPropiedades->getTipo() == "cliente")
                {
                    $arrayFirma['certificadoPfx'] = $arrayParametros['strCertificado64'];
                    $arrayFirma['certificado']    = $arrayParametros['arrayCertificado'];
                } else
                {
                    $arrayFirma['certificadoPfx'] = $strCertificado64;
                    $arrayFirma['certificado']    = $arrayParametros['arrayCertificadoSd'];
                }
                $arrayFirma['propiedades']                   = $objPropiedades->getPropiedades();
                $arrayFirma['codigo']                        = $objPropiedades->getCodigo();
                $arrayCertificadoContratoEmpresa['firmas'][] = $arrayFirma;
            }
        }
        return $arrayCertificadoContratoEmpresa;
    }
    
    /**
     * codificarDocumentos, método que codifica un archivo a base 64.
     * Se debe considerar que este metodo debe ser invocado de forma controlada
     * para evitar local file inclusion.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 17-07-2020
     * @param String $strRutaArchivo, ruta donde se encuentra el archivo
     * @return $archivoCodificado, retorna la decodificación de archivo        
     */
    private function codificarDocumentos($strRutaArchivo)
    {
        $strArchivoCodificado = "";
        $strRutaArchivo = $this->strPathTelcos.$strRutaArchivo;
        if(file_exists($strRutaArchivo))
        {
            $strArchivoCodificado = base64_encode(file_get_contents($strRutaArchivo));
        }
        return $strArchivoCodificado;
    }

    /**
     * Retorna un arreglo que repesenta a la plantilla y las variables necesarias para
     * llenar una plantilla para los documentos necesarios al firmar un contrato
     *
     * @param type $arrayParametros
     * @param type $strNombrePlantilla
     *
     * @return type array(
     *                    cod : Nombre de plantilla en Certificacion de Documentos 
     *                    listVariables : Arreglo de parametros con que se llenara la plantilla del contrato
     *                    ) 
     * 
     * @author  Edgar Pin Villavicencio <epin@telconet.ec>          
     * @version 1.0 21-07-2020
     *
     */
    public function crearPlantillaDocumentoNew($arrayParametros)
    {

        if($arrayParametros['strNombrePlantilla'] == "adendumMegaDatos")
        {
            if(isset($arrayParametros['arrayValores']['arrayProdParametros']) && 
                !empty($arrayParametros['arrayValores']['arrayProdParametros']))
            {

                $strHTMLProductos = "";

                foreach ($arrayParametros['arrayValores']['arrayProdParametros'] as $arrayProdParametro)
                {
                    if (!empty($arrayProdParametro))
                    {
                        $strHTMLProductos .= '<tr>
                                                <td class="line-height labelGris">' . $arrayProdParametro['descripcion'] . '</td>
                                                <td class="line-height textCenter">$!' . $arrayProdParametro['valor2'] . 'Cantidad</td>
                                                <td class="line-height textCenter">$!' . $arrayProdParametro['valor2'] . 'Instalacion</td>
                                                <td class="line-height textCenter">$!' . $arrayProdParametro['valor2'] . 'Precio</td>
                                                <td class="line-height textCenter">$!' . $arrayProdParametro['valor2'] . 'Precio</td>
                                                <td class="line-height textCenter">$!' . $arrayProdParametro['valor2'] . 'Observaciones</td>
                                             </tr>';
                    }
                }

                $arrayParametros['arrayCertificado']['plantilla'] = str_replace('{{listaProductos}}', 
                    $strHTMLProductos, $arrayParametros['arrayCertificado']['plantilla']);
            }
            else
            {
                $arrayParametros['arrayCertificado']['plantilla'] = str_replace('{{listaProductos}}', '', 
                    $arrayParametros['arrayCertificado']['plantilla']);
            }
        }
        
        unset($arrayParametros['arrayValores']['arrayProdParametros']);
        
        return array(
                    "cod"             => $arrayParametros['strNombrePlantilla'],
                    "listVariables"   => $arrayParametros['arrayValores'],
                    "objetos"         => $arrayParametros['arrayCertificado']
                    );
    }

    /**
     * documentarCertificadoNew, método que genera la documentación de un certificado digital
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 27-07-2020
     * @param obj $objContrato, Objeto de tipo contrato que fue creado para el cliente
     * @param $arrayDatos
     *        [
     *            codEmpresa     => id empresa,
     *            prefijoEmpresa => prefijo de empresa,
     *            idOficina      => id de oficina
     *            usrCreacion    => usuario de creación
     *            contrato       => datos del contrato
     *        ]
     * @return arrayRespuesta retorna un arreglo con la siguiente información:
     *         $arrayRespuesta['salida'] = 0 => Error en la creación del certificado
     *         $arrayRespuesta['salida'] = 1 => Certificado creado correctamente
     *         $arrayRespuesta['mensaje']    => Un mensaje de éxito o error
     * 
     * 
     */
    public function documentarCertificadoNew($objContrato,$arrayDatos, $output)
    {
        $arrayDatosCliente                     = $this->obtenerDatos($objContrato);
        $arrayDatosCliente["codEmpresa"]       = $arrayDatos['codEmpresa'];
        $arrayDatosCliente['strTipo']          = $arrayDatos['strTipo'];
        $arrayDatosCliente['strNumeroAdendum'] = $arrayDatos['strNumeroAdendum'];
        $arrayDatosCliente['contratoId']       = $objContrato->getId();

        $arrayDatosPersona              = $this->obtenerDatosDocumentarCertificado($objContrato,$arrayDatosCliente, $output);
     
        $arrayContratoEmp      = $this->crearContratoEmp($arrayDatosPersona);
        $arrayContratoSd       = $this->crearContratoSd($arrayDatosPersona);
        $arrayFormularioSd     = $this->crearFormularioSD($arrayDatosPersona);

        $strRutaRubricaTMP     = explode('.', $arrayDatosPersona['rutaRubrica'])[0].'.png';
        $strRutaFotoTMP        = explode('.', $arrayDatosPersona['rutaFoto'])[0].'.png';
        $strRutaCedulaTMP      = $this->strPathTelcos.explode('.', $arrayDatosPersona['rutaCedula'])[0].'.png';
        $strRutaCedulaRevTMP   = $this->strPathTelcos.explode('.', $arrayDatosPersona['rutaCedulaR'])[0].'.png';
        $strRutaCedulaCompTMP  = $this->mergeImagesIntoPDF("documento_digital_", array($strRutaCedulaTMP,$strRutaCedulaRevTMP));

        $arrayPathCedula       = explode('/', $strRutaCedulaCompTMP);

        $objTipoDocumentoGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                   ->findOneBy(array(
                                                                     'codigoTipoDocumento' => "CED"
                                                                    )
                                                              );
        $arrayParametrosDocumento                              = array();
        $arrayParametrosDocumento['strNombreDocumento']        = $arrayPathCedula[count($arrayPathCedula)-1];
        $arrayParametrosDocumento['strUbicacionFisicaDoc']     = $strRutaCedulaCompTMP;  
        $arrayParametrosDocumento['strUsrCreacion']            = $arrayDatos['usrCreacion'];
        $arrayParametrosDocumento['strClienteIp']              = $arrayDatos['ip'];
        $arrayParametrosDocumento['strMensaje']                = "Archivo agregado al contrato # ".$objContrato->getNumeroContrato();
        $arrayParametrosDocumento['strCodEmpresa']             = $arrayDatos["codEmpresa"];
        $arrayParametrosDocumento['intTipoDocumentoGeneralId'] = $objTipoDocumentoGeneral->getId();
        $arrayParametrosDocumento['intContratoId']             = $objContrato->getId();
        $arrayParametrosDocumento['strTipoArchivo']            = "PDF";
        $arrayParametrosDocumento['strModulo']                 = 'COMERCIAL';

        $this->guardarDocumento($arrayParametrosDocumento);

        $arrayDataParametros     = array('strCodEmpresa' => $arrayDatos['codEmpresa'],
                                         'cedula'        => $arrayDatosPersona['cedula'],
                                         'rubrica'       => $this->codificarDocumentos($strRutaRubricaTMP),
                                         'documentos'    => array(
                                                                  'fotoCliente'   => $this->codificarDocumentos($strRutaFotoTMP),
                                                                  'fotoCedula1'   => $this->codificarDocumentos($strRutaCedulaCompTMP),
                                                                  'contratoEMP'   => $arrayContratoEmp,
                                                                  'contratoSD'    => $arrayContratoSd,
                                                                  'formularioSD'  => $arrayFormularioSd
                                                             ),
                                         'strIp'       => $arrayDatos['ip'],
                                         'strUsuario'  => $arrayDatos['usrCreacion']
                                      );

        $arrayRespuesta = $this->serviceCertificacionDocumentos->documentarCertificado($arrayDataParametros);

        if(!isset($arrayRespuesta))
        {
            $arrayRespuesta['status']  = $this->strStatusError;
            $arrayRespuesta['mensaje'] = 'Error, no hay respuesta del service para documentar el certificado';
        }
        return $arrayRespuesta;
    }
 
    /**
     * Genera un pin ya validado para la regularizacion de contrato
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 27-07-2020
     * @param obj $objContrato, Objeto de tipo contrato que fue creado para el cliente
     * @param $arrayDatos
     *        [
     *            codEmpresa     => id empresa,
     *            prefijoEmpresa => prefijo de empresa,
     *            idOficina      => id de oficina
     *            usrCreacion    => usuario de creación
     *            contrato       => datos del contrato
     *        ]
     * @return arrayRespuesta retorna un arreglo con la siguiente información:
     *         $arrayRespuesta['salida'] = 0 => Error en la creación del certificado
     *         $arrayRespuesta['salida'] = 1 => Certificado creado correctamente
     *         $arrayRespuesta['mensaje']    => Un mensaje de éxito o error
     * 
     * 
     */    
    public function generarPinValidado($arrayData) 
    {

        $arrayResponse            = array();
        $arrayResponse['status']  = 'ERROR_SERVICE';
        $arrayResponse['mensaje'] = 'OCURRIÓ UN PROBLEMA INESPERADO AL GENERAR EL PIN';
        //======================================================================
        //Generacion de PIN
        //______________________________________________________________________
        $strPin = '';
        for ($intI = 0; $intI < 6; $intI++) 
        {
            $strPin .= mt_rand(0, 9);
        }
        $strPin          .= '';
        if (!empty($strPin))
        {
            $arrayResponse['pin']     = $strPin;
            $arrayResponse['status']  = "OK";
            $arrayResponse['mensaje'] = 'PIN GENERADO DE FORMA EXITOSA';
            //Inserto el pin en la base
            try
            {
                /*$arrayParametrosLog['enterpriseCode']   = isset($arrayData['strCodEmpresa']) ? $arrayData['strCodEmpresa'] : '18';
                $arrayParametrosLog['logType']          = "0";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['latitude']         = "";
                $arrayParametrosLog['longitude']        = "";
                $arrayParametrosLog['deviceImei']       = "";
                $arrayParametrosLog['deviceModel']      = "";
                $arrayParametrosLog['appVersion']       = "";
                $arrayParametrosLog['softwareVersion']  = "";
                $arrayParametrosLog['conectionType']    = "";
                $arrayParametrosLog['signalStrength']   = "";                
                $arrayParametrosLog['application']      = "ComercialMobileWSControllerRest";
                $arrayParametrosLog['appClass']         = "SeguridadService";
                $arrayParametrosLog['appMethod']        = "generarPinSecurity";
                $arrayParametrosLog['appAction']        = "guardarPinSecurity";
                $arrayParametrosLog['messageUser']      = "null";
                $arrayParametrosLog['status']           = "Exitoso";
                $arrayParametrosLog['descriptionError'] = "Pin generado: ".$strPin;
                $arrayParametrosLog['inParameters']     = implode(";", $arrayData);
                $arrayParametrosLog['creationUser']     = isset($arrayData['strUsername']) ? $arrayData['strUsername'] : "TELCOS";
                $this->utilService->insertLog($arrayParametrosLog);*/

                $objAdmiCaracteristicaRepo = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica');
                $objAdmiCaractLogin        = $objAdmiCaracteristicaRepo->findOneBy(array('descripcionCaracteristica' => 'USUARIO',
                                                                                         'tipo' => 'TECNICA',
                                                                                         'estado' => 'Activo'));
                if (is_object($objAdmiCaractLogin))
                {
                    // Obtenemos la infoPersonaEmpresaRolCaracteristica [USUARIO], asignada para el ususario y para la empresa indicada
                    $objPersonaEmpresaRolCaracRepo = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac');
                    $objRolCarac                   = $objPersonaEmpresaRolCaracRepo->findCaracteristicaPorCriterios
                                                     (
                                                      array(
                                                            'empresaCod'       => "18",
                                                            'caracteristicaId' => $objAdmiCaractLogin->getId(),
                                                            'valor'            => $arrayData['strUsername'],
                                                            'estado'           => 'Activo',
                                                            'intStart'         => 0,
                                                            'intLimit'         => 1
                                                           )
                                                     );
                }


                $this->emComercial->getConnection()->beginTransaction();

                // Obtenemos la admi_caracteristica [TELEFONO]
                $objCaractTel    = $objAdmiCaracteristicaRepo->findOneBy(array('descripcionCaracteristica' => 'TELEFONO',
                                                                                'tipo'                      => 'TECNICA',
                                                                                'estado'                    => 'Activo'));

                // Obtenemos la admi_caracteristica [PIN]
                $objCaractPin    = $objAdmiCaracteristicaRepo
                                   ->findOneBy(array('descripcionCaracteristica' => 'PIN',
                                                     'tipo'                      => 'TECNICA',
                                                     'estado'                    => 'Activo'));   
                $arrayPinActivos = $objPersonaEmpresaRolCaracRepo
                                    ->getCaracteristicasByParametros(
                                               array('empresaCod'          => "18",
                                                     'personaEmpresaRolId' => $objRolCarac->getPersonaEmpresaRolId(),
                                                     'caracteristicaId'    => $objCaractPin->getId(),
                                                     'estado'              => 'Activo'));                       
                // Cancelar InfoPersonaEmpresaRolCarac de tipo PIN si existen mas registros Activos.       
                if ($arrayPinActivos) 
                {
                    foreach ($arrayPinActivos as $objRowPin):
                        if ($objRowPin->getEstado('Activo')) 
                        {
                            $objRowPin->setEstado('Cancelado');
                            $this->emComercial->persist($objRowPin);
                            $this->emComercial->flush();
                        }
                    endforeach;
                }

                //Se Guarda la caracteristica pin.
                $objPersonaEmpresaRolCarac  = new InfoPersonaEmpresaRolCarac();
                $objPersonaEmpresaRolCarac->setValor($strPin);
                $objPersonaEmpresaRolCarac->setCaracteristicaId($objCaractPin);
                $objPersonaEmpresaRolCarac->setPersonaEmpresaRolId($objRolCarac->getPersonaEmpresaRolId());
                $objPersonaEmpresaRolCarac->setPersonaEmpresaRolCaracId($objRolCarac->getId());
                $objPersonaEmpresaRolCarac->setUsrCreacion('MobileNetlife');
                $objPersonaEmpresaRolCarac->setFeCreacion(new \Datetime('now'));
                $objPersonaEmpresaRolCarac->setIpCreacion('127.0.0.1');
                $objPersonaEmpresaRolCarac->setEstado('validado');
                $this->emComercial->persist($objPersonaEmpresaRolCarac);
                $this->emComercial->flush();

                //Se Guarda la caracteristica Telefono asociado al pin al que fue enviado.
                $objPersonaEmpresaRolCaracTelf  = new InfoPersonaEmpresaRolCarac();
                $objPersonaEmpresaRolCaracTelf->setValor("9999999999");
                $objPersonaEmpresaRolCaracTelf->setCaracteristicaId($objCaractTel);
                $objPersonaEmpresaRolCaracTelf->setPersonaEmpresaRolId($objRolCarac->getPersonaEmpresaRolId());
                $objPersonaEmpresaRolCaracTelf->setPersonaEmpresaRolCaracId($objPersonaEmpresaRolCarac->getId());
                $objPersonaEmpresaRolCaracTelf->setUsrCreacion('MobileNetlife');
                $objPersonaEmpresaRolCaracTelf->setFeCreacion(new \Datetime('now'));
                $objPersonaEmpresaRolCaracTelf->setIpCreacion('127.0.0.1');
                $objPersonaEmpresaRolCaracTelf->setEstado('Activo');
                $this->emComercial->persist($objPersonaEmpresaRolCaracTelf);
                $this->emComercial->flush();

                $arrayResponse['status']  = "OK";
                $arrayResponse['mensaje'] = "Pin enviado al número {$arrayData['strNumeroTlf']}";
                $this->emComercial->getConnection()->commit();   



            } 
            catch (\Exception $ex) 
            {
                throw $ex;
            }
        }
        //========================================================================
        return $arrayResponse;
    }


    /**
     * Metodo que guarda los documentos digitales firmados tanto física como logicamente
     * 
     * @author Edgar Pin Villavicenio <vcarrasco@telconet.ec>
     * @version 1.0 27-07-2020
     * @param type $arrayParametros, datos del contrato
     * @param type $arrDocumentos[array[obj[k = clave ,v = valor]]]
     * @return array
     * 
     */
    public function guardarDocumentos($arrayParametros,$arrDocumentos)
    {         
        try{
            $arrayRutaArchivos      = array();
            $boleanEsTCreditoCtaCte = false;
            $strEstadoServicio      = "Factible";
            $objContrato            = $this->emComercial->getRepository('schemaBundle:InfoContrato')
                                                        ->find($arrayParametros['intContratoId']);
    
            $arrayDocumentosAux = $arrDocumentos;
            $arrayDocumentoEnviar = array_keys($arrayParametros['enviaMails']);
            
            $arrayDocumentoEnviar = array();
            foreach($arrayParametros['enviaMails'] as $envia)
            {
                $arrayDocumentoEnviar [] = $envia->k;            
            }
            
            // Guardamos Archivos Fisicamente
            foreach($arrayDocumentosAux as $objDocumento)
            {
                
                $arrayParametrosDocumento = array();
                $strNombreArchivo     = "FE_ERRATAS_" . $objDocumento->k."-".$objContrato->getNumeroContrato().date('YmdHis').".pdf";
                $strArchivoCodificado = $objDocumento->v;
                
                $strRuta = $this->decodificarDocumentos($strArchivoCodificado, $strNombreArchivo);
                
                if($objDocumento->k == 'contratoMegadatos' || $objDocumento->k == 'adendumMegaDatos')
                {
                    $strTipoDocumentoGeneral = 'CONT';
                    $objTipoDocumentoGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                    ->findOneBy(array(
                                                    'codigoTipoDocumento' => $strTipoDocumentoGeneral));
    
                    $arrayParametrosDocumento['strNombreDocumento']         = $strNombreArchivo;
                    $arrayParametrosDocumento['strUbicacionFisicaDoc']      = $strRuta;  
                    $arrayParametrosDocumento['strUsrCreacion']             = $objContrato->getUsrCreacion();
                    $arrayParametrosDocumento['strClienteIp']               = $objContrato->getIpCreacion();
                    $arrayParametrosDocumento['strMensaje']                 = "Archivo agregado al contrato # ".$objContrato->getNumeroContrato();
                    $arrayParametrosDocumento['strCodEmpresa']              = $arrayParametros['codEmpresa'];
                    $arrayParametrosDocumento['intTipoDocumentoGeneralId']  = $objTipoDocumentoGeneral->getId();
                    $arrayParametrosDocumento['intContratoId']              = $arrayParametros['intContratoId'];
                    $arrayParametrosDocumento['strTipoArchivo']             = $arrayParametros['strTipoArchivo'];
                    $arrayParametrosDocumento['strModulo']                  = 'COMERCIAL';
                    $arrayParametrosDocumento['strTipo']                    = $arrayParametros['strTipo'];
                    $arrayParametrosDocumento['strNumeroAdendum']           = $arrayParametros['strNumeroAdendum'];
    
                    $this->guardarDocumento($arrayParametrosDocumento);
    
                    $strExtension = "";
                    if (isset($arrayParametros['strExtension']))
                    {
                        $strExtension = $arrayParametros['strExtension'];    
                    }
    
                    if( in_array( $objDocumento->k . $strExtension, $arrayDocumentoEnviar))
                    {
                        array_push($arrayRutaArchivos,$strRuta);
                    }
    
                }
                else
                {
                    $strTipoDocumentoGeneral = 'OTR';
                }
       
            }
    
        }
        catch(\Exception $ex)
        {

            throw $ex;
        }

        return $arrayRutaArchivos;
    }

    /**
     * Guarda las referencias de los documentos en la INFO_DOCUMENTO
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 27-07-2020
     * @param type $arrayParametros
     *                              array('strNombreDocumento'       => Nombre archivo
     *                                    'strUbicacionFisicaDoc'    => Ubicacion fisica del documento
     *                                    'strUsrCreacion'           => Usuario que creo el documento
     *                                    'strClienteIp'             => Ip del cliente
     *                                    'strMensaje'               => Mensaje
     *                                    'strCodEmpresa'            => Codigo de Empresa
     *                                    'intTipoDocumentoGeneralId'=> Tipo documento general id
     *                                    'intContratoId'            => Contrato id
     *                                    'strTipoArchivo'           => Tipo Archivo
     *                                    'strModulo'                => Modulo al que corresponde el documento)
     */
    public function guardarDocumento($arrayParametros)
    {
        $this->emComunicacion->getConnection()->beginTransaction();
        try
        {
            $strNombreDocumento   = $arrayParametros['strNombreDocumento'];
            $strUbiFisicaDoc      = $arrayParametros['strUbicacionFisicaDoc'];
            $strUsrCreacion       = $arrayParametros['strUsrCreacion'];
            $strClientIp          = $arrayParametros['strClienteIp'];
            $strMensaje           = $arrayParametros['strMensaje'];
            $strCodEmpresa        = $arrayParametros['strCodEmpresa'];
            $intDocGeneralId      = (int) ($arrayParametros['intTipoDocumentoGeneralId']);
            $intContratoId        = (int) ($arrayParametros['intContratoId']);
            $strTipoArchivo       = $arrayParametros['strTipoArchivo'];
            $strModulo            = $arrayParametros['strModulo'];
            $strTipo              = $arrayParametros['strTipo'];
            $strNumeroAdendum     = $arrayParametros['strNumeroAdendum'];

            $objFechaCreacion     = new \DateTime('now');

            $objInfDocumento = new InfoDocumento();    
            $objInfDocumento->setNombreDocumento($strNombreDocumento);
            $objInfDocumento->setUbicacionLogicaDocumento($strNombreDocumento);
            $objInfDocumento->setUbicacionFisicaDocumento($strUbiFisicaDoc);                                
            $objInfDocumento->setUploadDir(substr($this->strFileRoot, 0, -1));
            $objInfDocumento->setFechaDocumento( $objFechaCreacion );                                                                 
            $objInfDocumento->setUsrCreacion( $strUsrCreacion );
            $objInfDocumento->setFeCreacion( $objFechaCreacion );
            $objInfDocumento->setIpCreacion( $strClientIp );
            $objInfDocumento->setEstado( 'Activo' );                                                           
            $objInfDocumento->setMensaje( $strMensaje );                                        
            $objInfDocumento->setEmpresaCod( $strCodEmpresa );
            $objInfDocumento->setTipoDocumentoGeneralId($intDocGeneralId);

            $objTipoDoc = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                               ->findOneByExtensionTipoDocumento($strTipoArchivo);

            if(is_object($objTipoDoc))
            {

                $objInfDocumento->setTipoDocumentoId($objTipoDoc);  
            }

            $objInfDocumento->setContratoId($intContratoId);

            $this->emComunicacion->persist($objInfDocumento);
            $this->emComunicacion->flush();

            $objInfoDocRelacion = new InfoDocumentoRelacion(); 
            $objInfoDocRelacion->setDocumentoId($objInfDocumento->getId());                    
            $objInfoDocRelacion->setModulo($strModulo); 
            $objInfoDocRelacion->setContratoId($intContratoId);        
            $objInfoDocRelacion->setEstado('Activo');
            $objInfoDocRelacion->setFeCreacion($objFechaCreacion);                        
            $objInfoDocRelacion->setUsrCreacion($strUsrCreacion);
            if ($strTipo !== "C")
            {
                $objInfoDocRelacion->setNumeroAdendum($strNumeroAdendum);
            }
            
            $this->emComunicacion->persist($objInfoDocRelacion);                        
            $this->emComunicacion->flush();

            $this->emComunicacion->getConnection()->commit();
        } 
        catch(\Exception $ex)
        {

            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
            }
            $this->emComunicacion->getConnection()->close();            
            throw $ex;
        }

    }

    /**
     * decodificarDocumentos, método que decodifica un archivo en formato base64
     * Se debe considerar que este metodo debe ser invocado de forma controlada
     * para evitar local file inclusion.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 27-07-2020
     * 
     * @param String $strNombreArchivo, nombre del archivo y su extension
     * @param String $strArchivoCodificado, archivo codificado     
     */
    private function decodificarDocumentos($strArchivoCodificado, $strNombreArchivo)
    {
        $strRutaArchivoFisica  = $this->strPathTelcos.$this->strContratosDirectorio.$strNombreArchivo;
        $strRutaArchivoVirtual = $this->strContratosDirectorio.$strNombreArchivo;
        if(!file_exists($strRutaArchivoFisica))
        {
            //Decode content
            $strPDFDecoded = base64_decode($strArchivoCodificado);
            //Write data back to pdf file
            $objPDF         = fopen($strRutaArchivoFisica,'w');
            fwrite ($objPDF,$strPDFDecoded);
            //close output file
            fclose ($objPDF);
        }
        return $strRutaArchivoVirtual;
    }

    /**
     * evaluarFuncionPrecio, Evalua la funcion de precio en base a unos parametros dados y retorna el precio
     * 
     * Nota: En esta función se usa eval() el cual es muy peligroso porque permite la ejecución de 
     * código de PHP arbitrario. Se debe validar correctamente la información ingresada 
     * directamente por el usuario antes de ser usada por eval.
     * 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 01-04-2016
     * 
     * @param string $strFuncionPrecio Funcion de precio a evaluar
     * @param array $arrayProductoCaracteristicasValores Arreglo con los valores a ser reemplazados
     * @return float Retorna el precio obtenido de la evaluacion
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 08-07-2016 - Se reemplazan las funciones JS usadas en la funcion de precio para que php pueda evaluarlas
     *                           correctamente
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.2 05-08-2016 - Se verifica si el ultimo caracter de la funcion de precio es numerico para añadir ';' de ser necesario
     * 
     */
    private function evaluarFuncionPrecio($strFuncionPrecio, $arrayProductoCaracteristicasValores)
    {
        $floatPrecio        = 0;        
        $arrayFunctionJs    = array('Math.ceil','Math.floor','Math.pow',"}");
        $arrayFunctionPhp   = array('ceil','floor','pow',';}');
        $strFuncionPrecio   = str_replace($arrayFunctionJs, $arrayFunctionPhp, $strFuncionPrecio);
        
        foreach($arrayProductoCaracteristicasValores as $strClave => $strValor)
        {
            $strFuncionPrecio = str_replace("[" . $strClave . "]", '"'. $strValor . '"', $strFuncionPrecio);
        }
        $strFuncionPrecio      = str_replace('PRECIO', '$floatPrecio', $strFuncionPrecio);
        $strDigitoVerificacion = substr($strFuncionPrecio, -1, 1);
        if(is_numeric($strDigitoVerificacion))
        {
            $strFuncionPrecio = $strFuncionPrecio . ";";
        }
        eval($strFuncionPrecio);
        return $floatPrecio;
    }

    /**
     * Obtiene el porcentaje del producto Instalacion ya que este no cuenta
     * con un porcentaje de IVA asociado
     * 
     * @param object $objPersonaEmpresaRolId
     * @return int
     * 
     * @author  Veronica Carrasco <vcarrasco@telconet.ec>          
     * @version 1.0 21-07-2016
     */
    public function obtenerImpuestoInstalacion($objPersonaEmpresaRolId)
    {
        // Porcentaje Descuento
        $objCaracContSolDesc   = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                   ->findOneBy(array("descripcionCaracteristica" => "CONTRIBUCION_SOLIDARIA"));
        
        $objPerEmpRolCarac     = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                   ->findOneBy(array("personaEmpresaRolId" => $objPersonaEmpresaRolId,
                                                                     "caracteristicaId"    => $objCaracContSolDesc));
        
        $strEstado = "";
        //Si cuenta con la caracteristica CONTRIBUCION_SOLIDARIA entonces hay que hacer uso del 12% de IVA
        if(is_object($objPerEmpRolCarac))
        {
            $strEstado = "Inactivo";
        }
        else
        {
            $strEstado = "Activo";
        }
        $objImpuesto     = $this->emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                           ->findOneBy(array("tipoImpuesto" => "IVA",
                                                             "estado"       => $strEstado));
            
        if(is_object($objImpuesto))
        {
            return $objImpuesto->getPorcentajeImpuesto();
        }
        return 0;
    }

    public function regularizaContrato($strCodEmpresa, $strUsrCreacion, $strIpCreacion, $strArchivo, $output)
    {
        //Para probar en desarrollo

        $arrayData['strArchivo'] = $strArchivo;
        //Para probar en desarrollo

        ini_set('max_execution_time', 400000);
        $strMensaje             = "";
        $arrayResponse          = array();
        $strUsrCreacion         = $strUsrCreacion;
        $arrayData['contrato'] = "";

        $objInputFileType   = PHPExcel_IOFactory::identify($strArchivo);
        $objReader          = PHPExcel_IOFactory::createReader($objInputFileType);
        $objPHPExcel        = $objReader->load($strArchivo);
        $objSheet           = $objPHPExcel->getSheet(0); 
        $intHighestRow      = $objSheet->getHighestRow(); 
        $objHighestColumn   = $objSheet->getHighestColumn();

        for ($intRow = 2; $intRow <= $intHighestRow; $intRow++)
        {
            try
            {
    
                $arrayData['usuario'] = $objSheet->getCell("A".$intRow)->getValue();
                $arrayData['strTipo'] = $objSheet->getCell("D".$intRow)->getValue() == "Contrato" ? "C" : 
                                        $objSheet->getCell("D".$intRow)->getValue() == "Punto Adicional" ? "AP" : "AS";
                                        
                $arrayData['strTipo'] = "AS";
                if(strtolower(trim($objSheet->getCell("D".$intRow)->getValue())) == "contrato")
                {
                    $arrayData['strTipo'] = "C";
                }                       
                if(strtolower(trim($objSheet->getCell("D".$intRow)->getValue())) == "punto adicional")
                {
                    $arrayData['strTipo'] = "AP";
                }                       


            
                $arrayData['strNumeroContrato'] = $objSheet->getCell("E".$intRow)->getValue();
                $arrayData['strNumeroAdendum'] = $objSheet->getCell("F".$intRow)->getValue();
                $arrayData['regulariza'] = "S";
                $arrayData["strCodEmpresa"] = $strCodEmpresa;
                $output->writeln("cliente: " . $arrayData['usuario'] . " reg " . $intRow   . "/" . $intHighestRow ) ;
                
                //Obtener id_contrato a regularizar
                $arrayContrato = $this->emComercial
                                    ->getRepository('schemaBundle:InfoContrato')
                                    ->findBy(array(
                                                    "numeroContrato"  => $arrayData['strNumeroContrato']
                                                  
                                                ),
                                                array('feCreacion' => 'DESC')); 
                $intCount = 0;                                
                $boolContrato = false;
                foreach ($arrayContrato as $objCon)
                {
                    if ($objCon->getEstado() != "Rechazado")
                    {
                        if (!$boolContrato)
                        {
                            $objContrato = $objCon;
                            $boolContrato = true;
                        }
                    }
                    if ($objCon->getEstado() == "Activo")
                    {
                        $intCount++;
                    }
                }                   
                if ($intCount > 1 && $arrayData['strTipo'] == 'C')
                {
                    throw new \Exception("Existen 2 contrato activos no se pudo regularizar cliente: " . $arrayData['usuario']);                         
                }             
                if ($objContrato)
                {   
                    //valido que el contrato o adendum no sea por regularizacion o web
                    if ($arrayData['strTipo'] == 'C' && $objContrato->getOrigen() == 'WEB')
                    {
                        continue;    
                    }
                    if ($arrayData['strTipo'] != 'C')
                    {
                        $objAdendum = $this->emComercial->getRepository('schemaBundle:InfoAdendum')
                                                        ->findOneBy(array('numero'     => $arrayData['strNumeroAdendum']),
                                                                    array('feCreacion' => 'DESC'));
                        if ($objAdendum)
                        {
                            if ($objAdendum->getUsrCreacion() == 'TELCOS_MIGRA' || $objAdendum->getUsrCreacion() == 'TELCOS_MIGRACION')
                            {
                                continue;
                            }                                             
                            $arrayData['strTipo'] = $objAdendum->getTipo(); 
                            $objContrato = $this->emComercial
                                                ->getRepository('schemaBundle:InfoContrato')
                                                ->find($objAdendum->getContratoId());

                        }                                             
        
                    }
                    //valido que no haya sido regularizado el adendum

                    if ($arrayData['strTipo'] == 'C')
                    {
                        $objDocumentos = $this->emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')
                                                        ->findBy(array('contratoId'     => $objContrato->getId()),
                                                                    array('feCreacion' => 'DESC'));
                    }
                    else
                    {
                        $objDocumentos = $this->emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')
                                                        ->findBy(array('contratoId'     => $objContrato->getId(),
                                                                          'numeroAdendum'  => $arrayData['strNumeroAdendum']),
                                                                    array('feCreacion' => 'DESC'));

                    }                                          
                    $boolRegularizado = false;      
                    if ($objDocumentos)
                    {
                        foreach ($objDocumentos as $objDocumento)
                        {
                            $objDoc = $this->emComunicacion->getRepository('schemaBundle:InfoDocumento')
                                                        ->find($objDocumento->getDocumentoId());
                            if (substr($objDoc->getNombreDocumento(),0,10) == 'FE_ERRATAS')
                            {
                                $output->writeln("Documento ya ha sido regularizado");
                                $boolRegularizado = true;
                                break;
                            }                                             
                        }
                    }                                             

                    if ($boolRegularizado)
                    {
                        continue;
                    }
                    
                    $arrayContrato["strCodEmpresa"] = $strCodEmpresa;
                    $arrayContrato["contrato"]      = $objContrato;
                    $arrayContrato["strIp"]         = $strIpCreacion;
                    $arrayContrato["strUsuario"]    = $strUsrCreacion;
                    
                    $arrayCrearCd           = $this->crearCertificadoNew($arrayContrato);                    
                    $objCertificado = $this->emComercial->getRepository('schemaBundle:InfoCertificado')
                                        ->findOneBy(array("numCedula" => $arrayData['usuario'],
                                                            "estado"    => "valido"));
            
                    //Se consulta si fue creado el certificado
                    if ($arrayCrearCd['salida'] == '1' && count($objCertificado) > 0)
                    {
                        //Genero un pin ya validado
                        $arrayResponse    = array();
                        $strMensaje       = "";
                        $arrayGeneracionPin                  = array ();
                        $arrayGeneracionPin['strUsername']   = $arrayData['usuario'];
                        $arrayGeneracionPin['strCodEmpresa'] = $strCodEmpresa;
                        $arrayResponseService = $this->generarPinValidado($arrayGeneracionPin);
                        if (isset($arrayResponseService['status']) && $arrayResponseService['status'] == 'ERROR_SERVICE')
                        {
                            $strMensaje = $arrayResponseService['mensaje'];
                            throw new \Exception('ERROR_PARCIAL');
                        }
                    }    

                    // Se solicita la generacion de un certificado para poder firmar los documentos
        
                    // ========================================================================
                    // Autorizar el contrato digitalmente
                    // ========================================================================
                    // ========================================================================
                    // Enviar a firmar los documentos 
                    // ========================================================================
                    //Obtener datos contrato
        
                    $intIdContrato = $objContrato->getId(); 
                    $arrayData['contrato'] = $objContrato;
                    $arrayData['pinCode']  = ($arrayResponseService['pin']) ? $arrayResponseService['pin'] : "";  
                    $arrayDocumentosFirmados = $this->firmarDocumentosNewRegularizacion($arrayData, $output);
                    
                    if ($arrayDocumentosFirmados['salida']  == '0')
                    {
                        throw new \Exception($arrayDocumentosFirmados['mensaje']); 
                    }

                    if ($arrayDocumentosFirmados['salida'] == '1')
                    {                                        
                        $arrayDocumentos    = $arrayDocumentosFirmados['documentos'];
                        $arrayParametrosDocumentos['codEmpresa']     = $strCodEmpresa;
                        $arrayParametrosDocumentos['strUsrCreacion'] = $strUsrCreacion;
                        $arrayParametrosDocumentos['strTipoArchivo'] = 'PDF';
                        $arrayParametrosDocumentos['intContratoId']  = $intIdContrato;
                        $arrayParametrosDocumentos['enviaMails']     = $arrayDocumentosFirmados['enviaMails'];
                        $arrayParametrosDocumentos['strExtension']   = '.pdf';
                        $arrayParametrosDocumentos['strTipo']        = $arrayData['strTipo'];
                        $arrayParametrosDocumentos['strNumeroAdendum'] = $arrayData['strNumeroAdendum'];
                        $this->guardarDocumentos($arrayParametrosDocumentos,$arrayDocumentos);
                        $arrayResponse['status']                  = "200";                    

                    }
                        
                }  
                else   
                {
                    error_log("no hay contrato"); 
                    throw new \Exception("No se encontro contrato para regularizar");                         
                }
            
            }
            catch (\Exception $e)
            {
                if ($e->getMessage() == 'ERROR_PARCIAL')
                {
                    $arrayResponse['status']  = $this->status['ERROR_PARCIAL'];
                    $arrayResponse['message'] = $strMensaje;
                    
                }
                else
                {
                    $strMensaje               = $e->getMessage();
                    $arrayResponse['status']  = "500";
                    $arrayResponse['message'] = $strMensaje;
                }
                
                $arrayParametrosLog['enterpriseCode']   = $strCodEmpresa;
                $arrayParametrosLog['logType']          = "1";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['latitude']         = "";
                $arrayParametrosLog['longitude']        = "";
                $arrayParametrosLog['deviceImei']       = "";
                $arrayParametrosLog['deviceModel']      = "";
                $arrayParametrosLog['appVersion']       = "";
                $arrayParametrosLog['softwareVersion']  = "";
                $arrayParametrosLog['conectionType']    = "";
                $arrayParametrosLog['signalStrength']   = "";
    
        
                $arrayParametrosLog['application']      = "ComercialMobileWSControllerRest";
                $arrayParametrosLog['appClass']         = "ComercialMobileWSControllerRest";
                $arrayParametrosLog['appMethod']        = "putRegularizaContratosAdendums";
                $arrayParametrosLog['appAction']        = "putRegularizaContratosAdendums";
                $arrayParametrosLog['messageUser']      = "ERROR";
                $arrayParametrosLog['status']           = "Fallido";
                $arrayParametrosLog['descriptionError'] = $strMensaje;
                $arrayParametrosLog['inParameters']     = json_encode($arrayData);
                $arrayParametrosLog['creationUser']     = "REGULARIZACION";  
                    
                $this->utilService->insertLog($arrayParametrosLog);              
    
            }
        }   
        return $arrayResponse;
    }

    /**
     * crearCertificadoNew, método que envía la petición desde un webservice para la creación de un
     * certificado digital
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 15-02-2019
     * @param obj $objContrato, Objeto contrato que fue creado para el cliente
     * @return arrayRespuesta retorna un arreglo con la siguiente información:
     *         $arrayRespuesta['salida'] = '0' => Error en la creación del certificado
     *         $arrayRespuesta['salida'] = '1' => Certificado creado correctamente
     *         $arrayRespuesta['mensaje']      => Un mensaje de éxito o error
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.4 19-01-2020 Envio de usuario creador para persistencia en InfoLog,
     *                         Reemplazo de función implode por json_encode para evitar errores si
     *                         existiesen instancias date dentro del array a convertir.
     *
     */
    public function crearCertificadoNew($arrayContrato)
    {
        try
        {
            $objContrato = $arrayContrato['contrato'];
            $arrayDatosPersona = $this->obtenerDatos($objContrato);
            $strOp             = $this->strUrlSecuridyData;
    
            $arrayData = array('op'                => $strOp,
                               'strCodEmpresa'     => $arrayContrato['strCodEmpresa'],
                               'arrayDatosPersona' => $arrayDatosPersona,
                               'strIp'             => $arrayContrato['strIp'],
                               'strUsuario'        => $arrayContrato['strUsuario']
                               );
    
            //WIP: Llamar primero al certificado
            
            $arrayResponse = $this->llenarCertificado($arrayData);
            
            $arrayRespuesta['salida']   = '0';
            
            if ($arrayResponse['status'] == "200")
            {
                $arrayRespuesta['mensaje']    = $arrayResponse['mensaje'];
                $arrayRespuesta['salida']     = '1';
                $arrayRespuesta['datos']      = $arrayDatosPersona;
            }
            else
            {
                $arrayRespuesta['mensaje'] = $arrayResponse['mensaje'];
            }
    
        }
        catch (\Exception $ex)
        {
            throw $ex;
        }                
        return $arrayRespuesta;
    }

    /**
      * Método llenarCertificado, permite llenar un certificado verifcando si existe uno lo trae y si no existe se crea un nuevo
      * certificado
      *
      * @param array $arrayParametros [arrayDatosPersona, strIp, strCodEmpresa]
      * @return array $arrayRespuesta [mensaje, status, message, success]
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 27-03-2019
      * 
      * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
      * @version 1.3 19-01-2020 Correción en el manejo de excepciones para persistencia en InfoLog,
      *                         Reemplazo de función implode por json_encode para evitar errores si
      *                         existiesen instancias date dentro del array a convertir.
      */

      public function llenarCertificado($arrayParametros)
      {
          try
          {
              $arrayDatosPersona                  = $arrayParametros["arrayDatosPersona"];
              $arrayDatosPersona['strIp']         = $arrayParametros['strIp'];
              $arrayDatosPersona['strCodEmpresa'] = $arrayParametros['strCodEmpresa'];
              $arrayDatosPersona['strUsuario']    = $arrayParametros['strUsuario'];
  
              if (is_null($arrayDatosPersona))
              {
                  throw new \Exception('No hay datos de certificado');
              }
              $arrayCertificado = $this->emFirmaElect->getRepository('schemaBundle:InfoCertificado')
                                                     ->findCertificado(array("strNumCedula"  => $arrayDatosPersona['cedula'],
                                                                             "strCodEmpresa" => $arrayParametros['strCodEmpresa'],
                                                                             "strEstado"     => "valido"));
              if (count($arrayCertificado) > 0)
              {
                  $entityCertificado = $this->emFirmaElect->getRepository('schemaBundle:InfoCertificado')
                                            ->find($arrayCertificado[0]["id"]);
  
                  if (!is_object($entityCertificado))
                  {
                     throw new \Exception('Inconsistencia en datos de certificado', 206);
                  }
  
                  if ($this->isValidoCertificado($entityCertificado))
                  {
                      $entityCertificado->setRecuperado("S");
                      $arrayRespuesta["status"] = "200";
                      $arrayRespuesta["mensaje"] = "Certificado valido, se reutilizará certificado";
                  }
                  else
                  {
                      $arrayRespuesta = $this->crearCertificado($arrayDatosPersona);
                  }
              }
              else
              {
                  $arrayRespuesta = $this->crearCertificado($arrayDatosPersona);
              }
          }
          catch (\Exception $ex)
          {
  
              $arrayRespuesta["mensaje"] = $ex->getMessage();
              $arrayRespuesta['status']  = 500;
              $arrayRespuesta['message'] = 'INTERNAL ERROR';
              $arrayRespuesta['success'] = false;
  
              throw $ex;
          }
          return $arrayRespuesta;
      }
  
      public function crearCertificado($arrayParametros)
      {
          try
          {
              $strResponse = "";
              $arrayParametrosPass["intLongitud"] = $this->intLongitudPass;
  
              $intDias          = $this->intDiasValidezCertificado;
              $objFecha         = new \DateTime('now');
              $strFecha         = $objFecha->format("dmyHis");
              $strFechaFile     = $objFecha->format("dmY");
              $strPassword      = $this->utilService-> generarPassword($arrayParametrosPass);
              $strGrupos        = $this->strGruposPertenencia . "|" . ucwords(mb_strtolower($arrayParametros['provincia'], "UTF-8"));
              $entityAdmEmpresa = $this->emFirmaElect->getRepository('schemaBundle:AdmEmpresa')
                                                     ->findByReferenciaEmpresa($arrayParametros['strCodEmpresa'])[0];
  
              $arrayParametroConsumo = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                      ->getOne('CONFIGURACION_WS_SD',
                                                              'COMERCIAL',
                                                              '',
                                                              'PARAMSQUERY',
                                                              '',
                                                              '',
                                                              '',
                                                              '',
                                                              '',
                                                              '18');
              $strEstadoPar = 'Inactivo';
              if(isset($arrayParametroConsumo['estado']))
              {
                  $strEstadoPar = $arrayParametroConsumo['estado'];
              }                                                   
  
              //Creo la entidad certificado para guardarla
              $entityCertificado = new InfoCertificado();
              $entityCertificado->setEmpresaId($entityAdmEmpresa->getId());
              $entityCertificado->setSerialNumber($strFecha);
              $entityCertificado->setEmail($arrayParametros['email']);
              $entityCertificado->setNumCedula($arrayParametros['cedula']);
              $entityCertificado->setNombres($arrayParametros['nombres']);
              $entityCertificado->setPrimerApellido($arrayParametros['pApell']);
              $entityCertificado->setSegundoApellido($arrayParametros['sApell']);
              $entityCertificado->setDireccion($arrayParametros['dir']);
              $entityCertificado->setTelefono($arrayParametros['telf']);
              $entityCertificado->setCiudad($arrayParametros['ciudad']);
              $entityCertificado->setPais($arrayParametros['pais']);
              $entityCertificado->setProvincia($arrayParametros['provincia']);
              $entityCertificado->setNumFactura($arrayParametros['fact']);
              $entityCertificado->setNumSerieToken($this->strExtArchivo);
              $entityCertificado->setPassword($strPassword);
              $entityCertificado->setEnterprise("ENTERPRISE");
              $entityCertificado->setPersonaNatural("PERSONA NATURAL");
              $entityCertificado->setNumDiasVigencia($intDias);
              $entityCertificado->setGruposPertenencia($strGrupos);
              $entityCertificado->setEstado("valido");
              $entityCertificado->setUsrCreacion("Megadatos");
              $entityCertificado->setFeCreacion($objFecha);
              $entityCertificado->setIpCreacion($arrayParametros['strIp']);
              $entityCertificado->setRecuperado("N");
              $entityCertificado->setDocumentado("N");
              $entityCertificado->setFechaCreacion($objFecha);
              $entityCertificado->setUsuarioCreacion("MEGADATOS"); 
  
              //Lleno el formulario URL Encoded
              $strFormulario = "";
              if ($strEstadoPar == 'Activo')
              {
                  $strFormulario .= "serial="      . urlencode($strFecha)                   . "&";
                  $strFormulario .= "email="             . urlencode($arrayParametros['email'])   . "&";
                  $strFormulario .= "cedula="         . urlencode($arrayParametros['cedula'])  . "&";
                  $strFormulario .= "nombres="           . urlencode($arrayParametros['nombres']) . "&";
                  $strFormulario .= "ap1="    . urlencode($arrayParametros['pApell'])  . "&";
                  $strFormulario .= "ap2="   . urlencode($arrayParametros['sApell'])  . "&";
                  $strFormulario .= "direccion="         . urlencode($arrayParametros['dir'])     . "&";
                  $strFormulario .= "telefono="          . urlencode($arrayParametros['telf'])    . "&";
                  $strFormulario .= "ciudad="            . urlencode($arrayParametros['ciudad'])  . "&";
                  $strFormulario .= "provincia="            . urlencode($arrayParametros['provincia'])  . "&";
                  $strFormulario .= "pais="              . urlencode($arrayParametros['pais'])    . "&";
                  $strFormulario .= "usuario="           . urlencode($arrayParametroConsumo['valor1'])    . "&";
                  $strFormulario .= "factura="        . urlencode($arrayParametros['fact'])    . "&";
                  $strFormulario .= "password="          . urlencode($strPassword);
                  $arrayRest[CURLOPT_TIMEOUT] = $this->intWsContratoDigitalTimeOut;
                  $arrayData = array('strUrl'  => $arrayParametroConsumo['valor2'],
                                     'strData' => $strFormulario); 
  
                  $arrayResponseWS = $this->serviceRestClient->postQueryParams($arrayData, $arrayRest);
                  $arrayResp       = json_decode($arrayResponseWS['result'], true);
  
                  $strResponse =  $arrayResp["resp"] == "EXITO(1)" ? "success" : $arrayResp["resp"];
              }
              else
              {
                  $strFormulario .= "serialNumber="      . urlencode($strFecha)                   . "&";
                  $strFormulario .= "email="             . urlencode($arrayParametros['email'])   . "&";
                  $strFormulario .= "numCedula="         . urlencode($arrayParametros['cedula'])  . "&";
                  $strFormulario .= "nombres="           . urlencode($arrayParametros['nombres']) . "&";
                  $strFormulario .= "primerApellido="    . urlencode($arrayParametros['pApell'])  . "&";
                  $strFormulario .= "segundoApellido="   . urlencode($arrayParametros['sApell'])  . "&";
                  $strFormulario .= "direccion="         . urlencode($arrayParametros['dir'])     . "&";
                  $strFormulario .= "telefono="          . urlencode($arrayParametros['telf'])    . "&";
                  $strFormulario .= "ciudad="            . urlencode($arrayParametros['ciudad'])  . "&";
                  $strFormulario .= "pais="              . urlencode($arrayParametros['pais'])    . "&";
                  $strFormulario .= "numFactura="        . urlencode($arrayParametros['fact'])    . "&";
                  $strFormulario .= "password="          . urlencode($strPassword) . "&";
                  $arrayGruposPertenencia = explode("|", $strGrupos);
                  foreach ($arrayGruposPertenencia as $strValor)
                  {
                      $strFormulario .= "gruposPertenencia=" . urldecode($strValor) . "&";
                  }
                  $strFormulario .= "numDiasVigencia="   . urldecode($intDias);
                  $arrayRest[CURLOPT_TIMEOUT] = $this->intWsContratoDigitalTimeOut;
  
                  $arrayResponseWS            = $this->serviceRestClient->putFormURLEncoded($this->strWsContratoDigitalUrl, $strFormulario, $arrayRest);
                  $strResponse = str_replace("<result>", "", $arrayResponseWS['result']);
                  $strResponse = str_replace("</result>", "", $strResponse);        
              }
  
              $entityCertificado->setRespuesta($arrayResponseWS['result']);
  
              if ($strResponse == "success")
              {
                  if ($strEstadoPar == 'Activo')
                  {
                      $arrayParametrosFtp['strArchivoRemoto'] = $arrayParametros['cedula'] . "_" . $strFecha . "." . 
                      strtolower(trim($arrayParametroConsumo['valor3']));
                      $arrayParametrosFtp['strDirDia'] = $strFechaFile;
                      $arrayParametrosFtp['strDirRemoto'] = $arrayParametroConsumo['valor4'];
                      $arrayParametrosFtp['strHost']      = $arrayParametroConsumo['valor5'];
                      $arrayParametrosFtp['strUsuario']   = $arrayParametroConsumo['valor6'];
                      $arrayParametrosFtp['strPassword']  = $arrayParametroConsumo['valor7'];
                  }
                  else
                  {
                      $arrayParametrosFtp['strArchivoRemoto'] = $arrayParametros['cedula'] . "_" . $strFechaFile . "." . 
                      strtolower(trim($this->strExtArchivo));
                      $arrayParametrosFtp['strDirDia'] = $strFechaFile;
  
                  }
  
                  if (!$this->utilService->hasRecibirArchivoSftp($arrayParametrosFtp))
                  {
                      throw new \Exception('Problemas en la comunicación SFTP con Security Data', 206);
                  }
                  $entityCertificado->setRecuperado("S");
  
                  $this->emFirmaElect->persist($entityCertificado);
                  $this->emFirmaElect->flush();
              }
              else
              {
                  throw new \Exception('Problemas de comunicación con Security Data', 206);
              }
              $arrayRespuesta["status"] = "200";
              $arrayRespuesta["mensaje"] = "Consulta Exitosa!";
          } 
          catch (\Exception $ex) 
          {
              throw $ex;
          }
          return $arrayRespuesta; 
      }
  
      private function isValidoCertificado($entityCertificado)
      {
          $boolRetorno = true;
          $intDias = $this->intDiasValidezCertificado;
          try
          {
              $objFecha      = new \DateTime('now');
              $objFeCreacion = $entityCertificado->getFeCreacion();
              $objFeCreacion->add(new \DateInterval("P".$intDias."D"));
  
              if ($objFecha > $objFeCreacion)
              {
                  $entityCertificado->setEstado('expirado');
                  $this->emFirmaElect->persist($entityCertificado);
                  $this->emFirmaElect->flush();
                  $boolRetorno = false;
              }
              
          }
          catch (\Exception $ex) 
          {            
              throw $ex;
          }
          return $boolRetorno;
      }
  

}
