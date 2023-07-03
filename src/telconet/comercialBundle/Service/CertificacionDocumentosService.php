<?php
namespace telconet\comercialBundle\Service;
use telconet\schemaBundle\Entity\InfoCertificado;
use telconet\schemaBundle\Entity\InfoCertificadoDocumento;
use Imagick;

class CertificacionDocumentosService
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emFirmaElect;

    /**
     * @var \telconet\soporteBundle\Service\EnvioPlantillaService
     */    
    private $serviceEnvioMail;

    /**
     * @var \telconet\schemaBundle\Service\UtilService
     */        
    private $serviceUtil;

    /**
     * @var \telconet\schemaBundle\Service\RestClientService
     */            
    private $serviceRestClient;
    
    /**
     *
     * @var type Path de telcos
     */
    private $strPathTelcos;
    
    /**
     *
     * @var type días de validez del certificado digital
     */
    private $intDiasValidezCertificado;
    
    /**
     *
     * @var type Valor de status OK
     */
    private $strStatusOk;
    
    /**
     *
     * @var type Tiempo de Espera para peticiones hacia el WS Contrato Digital
     */
    private $intWsContratoDigitalTimeOut;
    
    /**
     *
     * @var type url para ws de contrato digital
     */
    private $strWsContratoDigitalUrl; 
    
    /**
     *
     * @var type Grupos de pertenencia de los certificados digitales
     */
    private $strGruposPertenencia;
    
    /**
     *
     * @var type extensión del archivo con el que se guardará el certificado digital
     */
    private $strExtArchivo;
    
    /**
     *
     * @var type Ruta física donde se almacenarán los certificados digitales
     */    
    private $strRutaDocCertificado;
    
    /**
     *
     * @var type Objeto que permite la generación de un pdf
     */    
    private $objGeneraPdf;

    private $intLongitudPass;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emGeneral;    
    
    /**
     *
     * @var type service InfoContratoDigital
     */
    private $serviceInfoContratoDigital;

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer)
    {
        $this->emFirmaElect                = $objContainer->get('doctrine.orm.telconet_firmaelect_entity_manager');
        $this->serviceEnvioMail            = $objContainer->get('soporte.EnvioPlantilla');
        $this->serviceUtil                 = $objContainer->get('schema.Util');
        $this->serviceRestClient           = $objContainer->get('schema.RestClient');
        $this->strPathTelcos               = $objContainer->getParameter('path_telcos');
        $this->intDiasValidezCertificado   = $objContainer->getParameter('certificado_num_dias_vigencia');
        $this->strStatusOk                 = $objContainer->getParameter('ws_contrato_digital_status_ok');
        $this->intWsContratoDigitalTimeOut = $objContainer->getParameter('ws_contrato_digital_timeout');
        $this->strWsContratoDigitalUrl     = $objContainer->getParameter('ws_security_data_url');
        $this->strGruposPertenencia        = $objContainer->getParameter('certificado_grupos_pertenencia');
        $this->strExtArchivo               = $objContainer->getParameter('certificado_ext_archivo');
        $this->strRutaDocCertificado       = $objContainer->getParameter('ruta_certificados_documentos');
        $this->objGeneraPdf                = $objContainer->get('knp_snappy.pdf');
        $this->intLongitudPass             = $objContainer->getParameter('certificado_longitud_pass');
        $this->emGeneral                   = $objContainer->get('doctrine.orm.telconet_general_entity_manager');        
        $this->serviceInfoContratoDigital  = $objContainer->get('comercial.InfoContratoDigital');

    }

    /**
      * Método crearCertificado, permite crear un certificado digital desde Security Data
      *
      * @param array $arrayParametros [nombres, pApell, sApell, dir, fecha, email, telf, ciudad, provincia, pais, fact, pass, objPunto]
      * @return array $arrayRespuesta [mensaje, status, message, success]
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 27-03-2019
      *
      * @author Edgar Pin Villavicencio
      * @version 1.1 11-11-2019 - Se envía provincia en el grupo de pertenencia requerido por SD [Sólo se puede enviar provincia o 
      *                           capitales provinciales]
      *
      * @author Edgar Pin villavicencio
      * @version 1.2 27-11-2019 - Se corrige el envío de la letra Ñ, función strtolower no convierte esa letra en minuscula, se utiliza 
      *                           ahora la funcion mb_strtolower que trabaja bien con todos los caracteres.
      * 
      * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
      * @version 1.3 19-01-2020 Correción en el manejo de excepciones para persistencia en InfoLog,
      *                         Reemplazo de función implode por json_encode para evitar errores si
      *                         existiesen instancias date dentro del array a convertir.
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.4 02-05-2020 Se crea opción a traves de parametros para crear certificado digital sea por consumo de webservice rest o soap
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.5 11-05-2020 Se requiere enviar el numero de factura para crear certificado digital en ws por consumo rest
      *
      * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
      * @version 1.6 10-06-2020 Implementación de persona jurídica
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.7 24-10-2020 Se corrige el timeout del consumo de web service
      *
      * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
      * @version 1.8 26-10-2020 - Se registra la creación de la firma en la estructura de la INFO_DOCUMENTO
      *
      * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
      * @version 1.9 23-11-2020 - Se almacena el certificado Digital en la estructura de la infoDocumentoCertificado y ya no en la 
      *                           infoDocumento.
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 2.0 30-12-2020 - Se valida los datos antes de almacenarse en el certificado
      *
      * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
      * @version 2.1 15-01-2021 - Se agrega lógica para nuevo consumo a Security Data.
      *
      */
    public function crearCertificado($arrayParametros)
    {
        try
        {
            $strResponse = "";
            $arrayParametrosPass["intLongitud"] = $this->intLongitudPass;

            $arrayResponseWS  = array();
            $intDias          = $this->intDiasValidezCertificado;
            $objFecha         = new \DateTime('now');
            $strFecha         = $objFecha->format("dmyHis");
            $strFechaFile     = $objFecha->format("dmY");
            $strPassword      = $this->serviceUtil->generarPasswordSinCharEspecial($arrayParametrosPass);
            $strGrupos        = $this->strGruposPertenencia . "|" . ucwords(mb_strtolower($arrayParametros['provincia'], "UTF-8"));
            $entityAdmEmpresa = $this->emFirmaElect->getRepository('schemaBundle:AdmEmpresa')
                                                   ->findByReferenciaEmpresa($arrayParametros['strCodEmpresa'])[0];
            $objContrato      = $arrayParametros['objContrato'];

            $arrayBanderaNuevoConsumo = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('CONFIGURACION_WS_SD', 'COMERCIAL', '', 'BANDERA_NUEVO_CONSUMO_WS', '', '', '', '', '', '18');

            if ($arrayParametros['strTipoTributario'] == 'NAT')
            {
                $strTipoParametro   = 'PARAMSQUERY';
                $strTipoPersona     = 'PERSONA NATURAL';
            }
            else if ($arrayParametros['strTipoTributario'] == 'JUR')
            {
                $strTipoParametro   = 'PARAMSQUERYJUR';
                $strTipoPersona     = 'PERSONA JURIDICA';
            }

            if($arrayBanderaNuevoConsumo['valor1'] == "S")
            {
                $strTipoParametro   = $strTipoParametro.'_NEW';
            }

            $arrayParametroConsumo = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                     ->getOne('CONFIGURACION_WS_SD', 'COMERCIAL', '', $strTipoParametro, '', '', '', '', '', '18');
            $strEstadoPar = 'Inactivo';
            if(isset($arrayParametroConsumo['estado']))
            {
                $strEstadoPar = $arrayParametroConsumo['estado'];
            }
            //valido los datos

            if (!($arrayParametros['email']))
            {
                throw new \Exception('email no puede ser nulo para crear certificado', 1);
            }
            if (!($arrayParametros['cedula']))
            {
                throw new \Exception('cédula no puede ser nulo para crear certificado', 1);
            }  
            if (!($arrayParametros['nombres']))
            {
                throw new \Exception('nombres no puede ser nulo para crear certificado', 1);
            }                       
            if (!($arrayParametros['pApell']))
            {
                throw new \Exception('primer apellido no puede ser nulo para crear certificado', 1);
            }             
            if (!($arrayParametros['dir']))
            {
                throw new \Exception('dirección no puede ser nulo para crear certificado', 1);
            } 
            if (!($arrayParametros['telf']))
            {
                throw new \Exception('teléfono no puede ser nulo para crear certificado', 1);
            }                        
            if (!($arrayParametros['ciudad']))
            {
                throw new \Exception('ciudad no puede ser nulo para crear certificado', 1);
            }             
            if (!($arrayParametros['pais']))
            {
                throw new \Exception('pais no puede ser nulo para crear certificado', 1);
            }       

            //Creo la entidad certificado para guardarla
            $entityCertificado = new InfoCertificado();
            $entityCertificado->setEmpresaId($entityAdmEmpresa->getId());
            if($arrayBanderaNuevoConsumo['valor1'] == "S")
            {
                $entityCertificado->setSerialNumber("");
            }
            else
            {
                $entityCertificado->setSerialNumber($strFecha);
            }
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
            $entityCertificado->setPersonaNatural($strTipoPersona);
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
                if($arrayBanderaNuevoConsumo['valor1'] != "S")
                {
                    $strFormulario .= "serial="    . urlencode($strFecha)                        . "&";
                }
                $strFormulario .= "email="     .urlencode($arrayParametros['email'])         . "&";
                $strFormulario .= "cedula="    . urlencode($arrayParametros['cedula'])       . "&";
                $strFormulario .= "nombres="   . urlencode($arrayParametros['nombres'])      . "&";
                $strFormulario .= "ap1="       . urlencode($arrayParametros['pApell'])       . "&";
                $strFormulario .= "ap2="       . urlencode($arrayParametros['sApell'])       . "&";
                $strFormulario .= "direccion=" . urlencode($arrayParametros['dir'])          . "&";
                $strFormulario .= "telefono="  . urlencode($arrayParametros['telf'])         . "&";
                $strFormulario .= "ciudad="    . urlencode($arrayParametros['ciudad'])       . "&";
                $strFormulario .= "provincia=" . urlencode($arrayParametros['provincia'])    . "&";
                $strFormulario .= "pais="      . urlencode($arrayParametros['pais'])         . "&";
                $strFormulario .= "usuario="   . urlencode($arrayParametroConsumo['valor1']) . "&";
                $strFormulario .= "factura="   . urlencode($arrayParametros['fact'])         . "&";

                if ($arrayParametros['strTipoTributario'] == 'JUR')
                {
                    $strFormulario .= "ruc="         . urlencode($arrayParametros['strRuc'])         . "&";
                    $strFormulario .= "razonSocial=" . urlencode($arrayParametros['strRazonSocial']) . "&";
                    $strFormulario .= "cargo="       . urlencode($arrayParametros['strCargo'])       . "&";
                }

                $strFormulario .= "password=" . urlencode($strPassword);

                $arrayRest[CURLOPT_TIMEOUT_MS] = $this->intWsContratoDigitalTimeOut;
                $arrayData = array('strUrl'  => $arrayParametroConsumo['valor2'],
                                   'strData' => $strFormulario);

                $arrayResponseWS = $this->serviceRestClient->postQueryParams($arrayData, $arrayRest);
                $arrayResp       = json_decode($arrayResponseWS['result'], true);

                if($arrayBanderaNuevoConsumo['valor1'] == "S")
                {
                    if(($arrayResp['serial'] != null && !empty($arrayResp['serial'])) &&
                        ($arrayResp['resp'] != null && !empty($arrayResp['resp'])))
                    {
                        $arrayResp["status"] = "success";
                    }
    
                    $strResponse =  $arrayResp["status"] != null ? $arrayResp["status"] : $arrayResp['resp'];
                }
                else
                {
                    $strResponse =  $arrayResp["resp"] == "EXITO(1)" ? "success" : $arrayResp["resp"];
                }
            }
            else
            {
                throw new \Exception('No se pudo cargar parámetros para el consumo del ws de SecurityData', 1);      
            }

            $strRespuesta = "";

            if($arrayBanderaNuevoConsumo['valor1'] == "S")
            {
                if($strResponse == "success")
                {
                    $strRespuesta = $strResponse;
                }
                else
                {
                    $strRespuesta = $arrayResponseWS['result'];
                }
            }
            else
            {
                $strRespuesta = $arrayResponseWS['result'];
            }

            $entityCertificado->setRespuesta($strRespuesta);

            if ($strResponse == "success")
            {
                if ($strEstadoPar == 'Activo')
                {
                    if($arrayBanderaNuevoConsumo['valor1'] == "S")
                    {
                        $entityCertificado->setSerialNumber($arrayResp['serial']);
                    }

                    $arrayParametrosFtp['strArchivoRemoto'] = $arrayParametros['cedula'] . "_" . $strFecha . "." . 
                        strtolower(trim($arrayParametroConsumo['valor3']));
                    $arrayParametrosFtp['strDirDia'] = $strFechaFile;
                    $arrayParametrosFtp['strDirRemoto'] = $arrayParametroConsumo['valor4'];
                    $arrayParametrosFtp['strHost']      = $arrayParametroConsumo['valor5'];
                    $arrayParametrosFtp['strUsuario']   = $arrayParametroConsumo['valor6'];
                    $arrayParametrosFtp['strPassword']  = $arrayParametroConsumo['valor7'];
                }

                //Descarga del certificado
                $arrayParametrosFtp['strArchivoRemotoAbsoluto'] = $arrayParametrosFtp['strDirRemoto'] . '/' . $arrayParametrosFtp['strArchivoRemoto'];

                $arrayParamsDescargaCert = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('CONFIGURACION_WS_SD', 'COMERCIAL', '', 'MEDIO_DESCARGA_CERTIFICADO', '', '', '', '', '', '18');

                $arrayParametrosFtp['strMedioDescarga']  = $arrayParamsDescargaCert['valor1'];
                $arrayParametrosFtp['strServidorRemoto'] = $arrayParamsDescargaCert['valor2'];
                $arrayParametrosFtp['strIdentificacion'] = ($arrayParametros['strTipoTributario'] == 'JUR')
                                                               ? $arrayParametros['strRuc']
                                                               : $arrayParametros['cedula'];
                $arrayParametrosFtp['strCodEmpresa']     = $arrayParametros['strCodEmpresa'];
                $arrayParametrosFtp['strUsrCreacion']    = $arrayParametros['strUsuario'];

                if($arrayParametros['bandNfs'])
                {
                    $arrayParametrosFtp['objPerEmpRol']   = $arrayParametros['objPerEmpRol'];
                    $arrayParametrosFtp['prefijoEmpresa'] = $arrayParametros['prefijoEmpresa'];
                    $arrayParametrosFtp['strApp']         = $arrayParametros['strApp'];

                    if($arrayBanderaNuevoConsumo['valor1'] == "S")
                    {
                        $strIdentificacion      = is_object($arrayParametros['objPerEmpRol']->getPersonaId()) ?
                        $arrayParametros['objPerEmpRol']->getPersonaId()->getIdentificacionCliente()
                        : 'SIN_IDENTIFICACION';
        
                        $arrayParametrosFtp['arrayPathAdicional'][] = array('key' => $strIdentificacion);
                        $arrayParametrosFtp['strBase64'] = $arrayResp['resp'];
                        $arrayParametrosFtp['strNombreArchivo'] = $arrayParametrosFtp['strArchivoRemoto'];
                        $arrayParametrosFtp['strSubModulo'] = 'ContratoDigital';

                        $arrayRespSaveFileNfs =  $this->serviceUtil->guardarArchivosNfs($arrayParametrosFtp);
                        if($arrayRespSaveFileNfs['intStatus']  == 200)
                        {
                            $strUrlArchivo  = $arrayRespSaveFileNfs['strUrlArchivo'];
                        }
                        else
                        {
                            $arrayParametrosLog['enterpriseCode']   = $arrayParametros['strCodEmpresa'];
                            $arrayParametrosLog['logType']          = "1";
                            $arrayParametrosLog['logOrigin']        = "TELCOS";
                            $arrayParametrosLog['application']      = "TELCOS";
                            $arrayParametrosLog['appClass']         = "CertificacionDocumentosService";
                            $arrayParametrosLog['appMethod']        = "crearCertificado";
                            $arrayParametrosLog['appAction']        = "guardarArchivosNfs";
                            $arrayParametrosLog['messageUser']      = "ERROR";
                            $arrayParametrosLog['status']           = "Fallido";
                            $arrayParametrosLog['descriptionError'] = json_encode($arrayRespSaveFileNfs, 128);
                            $arrayParametrosLog['inParameters']     = json_encode($arrayParametrosFtp, 128);
                            $arrayParametrosLog['creationUser']     = $arrayParametros['strUsuario'];
                
                            $this->serviceUtil->insertLog($arrayParametrosLog);

                            throw new \Exception('Problemas al guardar archivo por NFS.', 1);
                        }
                    }
                    else
                    {
                        $arrayRespCertificado = $this->serviceUtil->hasNfsRecibirArchivoSftp($arrayParametrosFtp);
                        if($arrayRespCertificado['intStatus']  == 200)
                        {
                            $strUrlArchivo  = $arrayRespCertificado['strUrlArchivo'];
                        }
                    }
                }
                else
                {
                    if($arrayBanderaNuevoConsumo['valor1'] == "S")
                    {
                        throw new \Exception('Problemas al guardar archivo por NFS.', 1);
                    }
                    else
                    {
                        if (!$this->serviceUtil->hasRecibirArchivoSftp($arrayParametrosFtp))
                        {
                            throw new \Exception('Problemas en la comunicación SFTP con Security Data', 1);
                        }
                    }
                }

                $entityCertificado->setRecuperado("S");
                $this->emFirmaElect->persist($entityCertificado);
                $this->emFirmaElect->flush();

                if($arrayParametros['bandNfs'])
                {
                    $arrayParametrosDoc = array('intIdCertificado' => $entityCertificado->getId(),
                                                'strIp'            => $arrayParametros['strIp'],
                                                'strUsuario'       => $arrayParametros['strUsuario'],
                                                'strTipo'          => $arrayParametros['strTipo'],
                                                'strTipoDocumento' => 'certificadoDigital',
                                                'strSrc'           => $strUrlArchivo
                                            );
                    $this->guardarDocumentos($arrayParametrosDoc);
                }
            }
            else
            {
                throw new \Exception('Problemas de comunicación con Security Data', 1);
            }
            $arrayRespuesta["status"] = "200";
            $arrayRespuesta["mensaje"] = "Consulta Exitosa!";
        } 
        catch (\Exception $ex) 
        {
            $arrayRespuesta['status']  = 500;
            $arrayRespuesta['success'] = false;
            $arrayRespuesta["mensaje"] = ($ex->getCode() == 1)
                                            ? $ex->getMessage()
                                            : 'INTERNAL ERROR';
            $arrayRespuesta['message'] = ($ex->getCode() == 1)
                                            ? $ex->getMessage()
                                            : 'INTERNAL ERROR';

            $arrayParametrosLog['enterpriseCode']   = $arrayParametros['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $ex->getMessage() .  " " . $strResponse . "\n\n" . json_encode($arrayResponseWS, 128);
            $arrayParametrosLog['inParameters']     = json_encode($arrayParametros, 128);
            $arrayParametrosLog['creationUser']     = $arrayParametros['strUsuario'];

            $this->serviceUtil->insertLog($arrayParametrosLog);
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
      *
      * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
      * @version 1.4 22-09-2020 Almacenar el certificado en el servidor nfs.
      */

    public function llenarCertificado($arrayParametros)
    {
        try
        {
            $arrayDatosPersona                      = $arrayParametros["arrayDatosPersona"];
            $arrayDatosPersona['strIp']             = $arrayParametros['strIp'];
            $arrayDatosPersona['strCodEmpresa']     = $arrayParametros['strCodEmpresa'];
            $arrayDatosPersona['strUsuario']        = $arrayParametros['strUsuario'];
            $arrayDatosPersona['bandNfs']           = $arrayParametros['bandNfs'];
            $arrayDatosPersona['strApp']            = $arrayParametros['strApp'];
            $arrayDatosPersona['objContrato']       = $arrayParametros['objContrato'];
            $arrayDatosPersona['objPerEmpRol']      = $arrayParametros['objPerEmpRol'];
            $arrayDatosPersona['prefijoEmpresa']    = $arrayParametros['prefijoEmpresa'];

            if (is_null($arrayDatosPersona))
            {
                throw new \Exception('No hay datos de certificado', 1);
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
                   throw new \Exception('Inconsistencia en datos de certificado', 1);
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
            $arrayRespuesta['status']  = 500;
            $arrayRespuesta['success'] = false;

            $arrayRespuesta["mensaje"] = ($ex->getCode() == 1)
                                             ? $ex->getMessage()
                                             : 'INTERNAL ERROR';
            $arrayRespuesta['message'] = ($ex->getCode() == 1)
                                             ? $ex->getMessage()
                                             : 'INTERNAL ERROR';

            $arrayParametrosLog['enterpriseCode']   = $arrayParametros['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = $arrayRespuesta["mensaje"];
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $ex->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayParametros, 128);
            $arrayParametrosLog['creationUser']     = $arrayParametros['strUsuario'];

            $this->serviceUtil->insertLog($arrayParametrosLog);
        }
        return $arrayRespuesta;
    }

    /**
      * Método isValidoCertificado, verifica la validez de un certificado digital
      *
      * @param entity $entityCertificado
      * @return boolean 
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 27-03-2019
      * 
      * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
      * @version 1.1 19-01-2020 Correción en el manejo de excepciones para persistencia en InfoLog.
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.2 20-02-2020 Corrección de catch se quita la varibale de retorno en el catch 
      *
      */
    public function isValidoCertificado($entityCertificado)
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
            $arrayParametrosLog['enterpriseCode']   =  ($entityCertificado) ? $entityCertificado->getEmpresaId() == 1 ? "18" : "10" : "18"; 
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = "CertificacionDocumentosService";
            $arrayParametrosLog['appClass']         = "CertificacionDocumentosService";
            $arrayParametrosLog['appMethod']        = "isValidoCertificado";
            $arrayParametrosLog['appAction']        = "isValidoCertificado";
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $ex->getMessage();
            $arrayParametrosLog['inParameters']     = "";
            $arrayParametrosLog['creationUser']     = "telcos";

            $this->serviceUtil->insertLog($arrayParametrosLog);
            
        }
        return $boolRetorno;
    }

    /**
      * Método documentarCertificado, Una vez que se haya creado un nuevo certificado o se reutilice uno, se deben enviar los
      * documentos asociados al mismo, el envío de estos documentos es posterior a su creación porque deben ser firmados de 
      * manera electrónica para que sean validos. Actualmente los documentos obligatorios son:
      * Foto del cliente tomada en el momento
      * Foto de la cédula
      * Contrato security Data firmado por el cliente
      * Contrato de servicios entre Megadatos y el cliente firmado electrónicamente
      *
      * @param array $arrayParametros [strCodEmpresa, cedula, rubrica, documentos, strIp, strUsuario]
      * @return array $arrayRespuesta [status, message, success]
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 27-03-2019
      * 
      * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
      * @version 1.1 19-01-2020 Correción en el manejo de excepciones para persistencia en InfoLog,
      *                         Reemplazo de función implode por json_encode para evitar errores si
      *                         existiesen instancias date dentro del array a convertir.
      *
      * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
      * @version 1.2 10-06-2020 Implementación de persona jurídica
      *
      * @author Walther Joao Gaibor <wgaibor@telconet.ec>
      * @version 1.3 23-05-2020 Se recibe por parametro la ruta donde se debe crear los archivos.
      *
      * @author Walther Joao Gaibor <wgaibor@telconet.ec>
      * @version 1.4 20-11-2020 Almacenar los documentos del certificado en el NFS.
      */
    public function documentarCertificado($arrayParametros)
    {
        $strRutaDocumentos = $this->strPathTelcos . $this->strRutaDocCertificado;
        $objFecha          = new \DateTime('now');
        $strFecha          = $objFecha->format("dmyHis");
        $strFecha2         = $objFecha->format("dmy");

        try
        {
            if (!isset($arrayParametros['documentos']))
            {
                throw new \Exception("Documentación inválida", 1);
            }
            //Consulto el certificado
            $arrayCertificado = $this->emFirmaElect->getRepository('schemaBundle:InfoCertificado')
                                   ->findCertificado(array("strNumCedula"  => ($arrayParametros['persona']['strTipoTributario'] == 'NAT')
                                                                                ? $arrayParametros['cedula']
                                                                                : $arrayParametros['persona']['representanteLegal']['identificacion'],
                                                           "strCodEmpresa" => $arrayParametros['strCodEmpresa'],
                                                           "strEstado"     => "valido"));
            if (count($arrayCertificado) > 0)
            {
                if(!isset($arrayParametros['bandNfs']) && !$arrayParametros['bandNfs'])
                {
                    $arrayParametrosMK['strPath'] = $strRutaDocumentos;
                    $this->serviceUtil->creaDirectorio($arrayParametrosMK);
                    $strRutaDocumentos .= $arrayParametros['cedula'] . "/";
                    $arrayParametrosMK['strPath'] = $strRutaDocumentos;
                    $this->serviceUtil->creaDirectorio($arrayParametrosMK);
                    $strRutaDocumentos .= $arrayCertificado[0]['serialNumber'] . "_" . $strFecha . "/";
                    $arrayParametrosMK['strPath'] = $strRutaDocumentos;
                    $this->serviceUtil->creaDirectorio($arrayParametrosMK);
                }
                else
                {
                    $strIdentificacion      = is_object($arrayParametros['objPerEmpRol']->getPersonaId()) ?
                                                $arrayParametros['objPerEmpRol']->getPersonaId()->getIdentificacionCliente()
                                                : 'SIN_IDENTIFICACION';
                    $arrayPathAdicional     = null;
                    $arrayPathAdicional[]   = array('key' => $strIdentificacion);
                }

                //Agrego los documentos
                //Agrego la foto del cliente
                $strNombreArchivo   = "fotocliente_". $arrayParametros['cedula'] . "_" . $strFecha . ".jpeg";
                $arrayParametrosDoc = array('intIdCertificado' => $arrayCertificado[0]["id"],
                                            'strIp'            => $arrayParametros['strIp'],
                                            'strUsuario'       => $arrayParametros['strUsuario'],
                                            'strTipo'          => $arrayParametros['strTipo'],
                                            'strNumeroAdendum' => $arrayParametros['strNumeroAdendum']
                                        );
                if(!isset($arrayParametros['bandNfs']) && !$arrayParametros['bandNfs'])
                {
                    file_put_contents($strRutaDocumentos . $strNombreArchivo, base64_decode($arrayParametros['documentos']['fotoCliente']) );
                    $arrayParametrosDoc['strTipoDocumento'] = 'fotocliente';
                    $arrayParametrosDoc['strSrc']           = $strRutaDocumentos . $strNombreArchivo;
                }
                else
                {
                    $objImage   = base64_decode($arrayParametros['documentos']['fotoCliente']);
                    $objImagick = new Imagick();
                    $objImagick->readimageblob($objImage);
                    $objImagick->setImageFormat('jpeg');
                    $objImgBlob = $objImagick->getimageblob();
                    $strImgBs64 = base64_encode($objImgBlob);
                    $arrayParamNfs   = array(
                                                'prefijoEmpresa'       => $arrayParametros['prefijoEmpresa'],
                                                'strApp'               => $arrayParametros['strApp'],
                                                'arrayPathAdicional'   => $arrayPathAdicional,
                                                'strBase64'            => $strImgBs64,
                                                'strNombreArchivo'     => $strNombreArchivo,
                                                'strUsrCreacion'       => $arrayParametros['strUsuario'],
                                                'strSubModulo'         => 'ContratoDigital');
                    $arrayParamNfs['strBase64'] = $strImgBs64;

                    $arrayRespNfsPdf = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
                    if(isset($arrayRespNfsPdf) && $arrayRespNfsPdf['intStatus'] == 200)
                    {
                        $arrayParametrosDoc['strTipoDocumento'] = 'fotocliente';
                        $arrayParametrosDoc['strSrc']           = $arrayRespNfsPdf['strUrlArchivo'];
                    }
                    else
                    {
                        throw new \Exception($arrayRespNfsPdf['strMensaje'].'  -> documentarCertificado()');
                    }
                }
                $this->guardarDocumentos($arrayParametrosDoc);
                //Agrego la cedula del cliente
                $strNombreArchivo = "cedula1_". $arrayParametros['cedula'] . "_" . $strFecha . ".pdf";
                if(!isset($arrayParametros['bandNfs']) && !$arrayParametros['bandNfs'])
                {
                    file_put_contents($strRutaDocumentos . $strNombreArchivo, base64_decode($arrayParametros['documentos']['fotoCedula1']) );
                    $arrayParametrosDoc['strTipoDocumento'] = 'cedula1';
                    $arrayParametrosDoc['strSrc']           = $strRutaDocumentos . $strNombreArchivo;
                }
                else
                {
                    $arrayParamNfs   = array(
                                            'prefijoEmpresa'       => $arrayParametros['prefijoEmpresa'],
                                            'strApp'               => $arrayParametros['strApp'],
                                            'arrayPathAdicional'   => $arrayPathAdicional,
                                            'strBase64'            => $arrayParametros['documentos']['fotoCedula1'],
                                            'strNombreArchivo'     => $strNombreArchivo,
                                            'strUsrCreacion'       => $arrayParametros['strUsuario'],
                                            'strSubModulo'         => 'ContratoDigital');

                    $arrayRespNfsPdf = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
                    if(isset($arrayRespNfsPdf) && $arrayRespNfsPdf['intStatus'] == 200)
                    {
                        $arrayParametrosDoc['strTipoDocumento'] = 'cedula1';
                        $arrayParametrosDoc['strSrc']           = $arrayRespNfsPdf['strUrlArchivo'];
                    }
                    else
                    {
                        throw new \Exception($arrayRespNfsPdf['strMensaje'].'  -> documentarCertificado()');
                    }
                }

                $this->guardarDocumentos($arrayParametrosDoc);
                //Genero las plantillas
                $objPlantillaContrato = $this->emFirmaElect->getRepository('schemaBundle:AdmEmpresaPlantilla')
                                                           ->findOneBy(array("empresaId"  => 1,
                                                                             "codPlantilla" => "contratoMegadatos",
                                                                             "estado"     => "Activo"));
                $objPlantillaPagare = $this->emFirmaElect->getRepository('schemaBundle:AdmEmpresaPlantilla')
                                                         ->findOneBy(array("empresaId"  => 1,
                                                                           "codPlantilla" => "pagareMegadatos",
                                                                           "estado"     => "Activo"));
                $objPlantillaDebito = $this->emFirmaElect->getRepository('schemaBundle:AdmEmpresaPlantilla')
                                                         ->findOneBy(array("empresaId"  => 1,
                                                                           "codPlantilla" => "debitoMegadatos",
                                                                           "estado"     => "Activo"));

                $strPlantillaContrato = str_replace("$!", "$", $objPlantillaContrato->getHtml());
                $strPlantillaPagare   = str_replace("$!", "$", $objPlantillaPagare->getHtml());
                $strPlantillaDebito   = str_replace("$!", "$", $objPlantillaDebito->getHtml());
                foreach($arrayParametros['documentos']['contratoEMP'] as $arrayDocumento)
                {
                   $strPlantillaContrato = str_replace("$" . $arrayDocumento['k'], $arrayDocumento['v'], $strPlantillaContrato); 
                   $strPlantillaPagare   = str_replace("$" . $arrayDocumento['k'], $arrayDocumento['v'], $strPlantillaPagare); 
                   $strPlantillaDebito   = str_replace("$" . $arrayDocumento['k'], $arrayDocumento['v'], $strPlantillaDebito);
                }

                $strNombreArchivo = "certificado_contratoempresa_" . $arrayParametros['cedula'] . "_" . $strFecha . ".pdf";
                $strNombrePagare  = "certificado_pagareempresa_"   . $arrayParametros['cedula'] . "_" . $strFecha . ".pdf";
                $strNombreDebito  = "certificado_debitoempresa_"    . $arrayParametros['cedula'] . "_" . $strFecha . ".pdf";

                $this->objGeneraPdf->generateFromHtml($strPlantillaContrato, $strRutaDocumentos . $strNombreArchivo);
                $this->objGeneraPdf->generateFromHtml($strPlantillaPagare  , $strRutaDocumentos . $strNombrePagare);
                $this->objGeneraPdf->generateFromHtml($strPlantillaDebito  , $strRutaDocumentos . $strNombreDebito);
                if(!isset($arrayParametros['bandNfs']) && !$arrayParametros['bandNfs'])
                {
                    $arrayParametrosDoc['strTipoDocumento'] = 'contratoempresa';
                    $arrayParametrosDoc['strSrc']           = $strRutaDocumentos . $strNombreArchivo;
                    $this->guardarDocumentos($arrayParametrosDoc);

                    $arrayParametrosDoc['strTipoDocumento'] = 'pagareempresa';
                    $arrayParametrosDoc['strSrc']           = $strRutaDocumentos . $strNombrePagare;
                    $this->guardarDocumentos($arrayParametrosDoc);

                    $arrayParametrosDoc['strTipoDocumento'] = 'debitoempresa';
                    $arrayParametrosDoc['strSrc']           = $strRutaDocumentos . $strNombreDebito;
                    $this->guardarDocumentos($arrayParametrosDoc);
                }
                else
                {
                    $objFile         = file_get_contents($strRutaDocumentos . $strNombreArchivo);
                    $arrayParamNfs   = array(
                                            'prefijoEmpresa'       => $arrayParametros['prefijoEmpresa'],
                                            'strApp'               => $arrayParametros['strApp'],
                                            'arrayPathAdicional'   => $arrayPathAdicional,
                                            'strBase64'            => base64_encode($objFile),
                                            'strNombreArchivo'     => $strNombreArchivo,
                                            'strUsrCreacion'       => $arrayParametros['strUsuario'],
                                            'strSubModulo'         => 'ContratoDigital');
                    $arrayRespNfsPdf = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
                    if(isset($arrayRespNfsPdf) && $arrayRespNfsPdf['intStatus'] == 200)
                    {
                        unlink($strRutaDocumentos . $strNombreArchivo);
                        $arrayParametrosDoc['strTipoDocumento'] = 'contratoempresa';
                        $arrayParametrosDoc['strSrc']           = $arrayRespNfsPdf['strUrlArchivo'];
                    }
                    else
                    {
                        throw new \Exception($arrayRespNfsPdf['strMensaje'].'  -> documentarCertificado()');
                    }
                    $this->guardarDocumentos($arrayParametrosDoc);

                    $objFile         = file_get_contents($strRutaDocumentos . $strNombrePagare);
                    $arrayParamNfs   = array(
                                            'prefijoEmpresa'       => $arrayParametros['prefijoEmpresa'],
                                            'strApp'               => $arrayParametros['strApp'],
                                            'arrayPathAdicional'   => $arrayPathAdicional,
                                            'strBase64'            => base64_encode($objFile),
                                            'strNombreArchivo'     => $strNombrePagare,
                                            'strUsrCreacion'       => $arrayParametros['strUsuario'],
                                            'strSubModulo'         => 'ContratoDigital');
                    $arrayRespNfsPdf = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
                    if(isset($arrayRespNfsPdf) && $arrayRespNfsPdf['intStatus'] == 200)
                    {
                        unlink($strRutaDocumentos . $strNombrePagare);
                        $arrayParametrosDoc['strTipoDocumento'] = 'pagareempresa';
                        $arrayParametrosDoc['strSrc']           = $arrayRespNfsPdf['strUrlArchivo'];
                    }
                    else
                    {
                        throw new \Exception($arrayRespNfsPdf['strMensaje'].'  -> documentarCertificado()');
                    }
                    $this->guardarDocumentos($arrayParametrosDoc);

                    $objFile         = file_get_contents($strRutaDocumentos . $strNombreDebito);
                    $arrayParamNfs   = array(
                                            'prefijoEmpresa'       => $arrayParametros['prefijoEmpresa'],
                                            'strApp'               => $arrayParametros['strApp'],
                                            'arrayPathAdicional'   => $arrayPathAdicional,
                                            'strBase64'            => base64_encode($objFile),
                                            'strNombreArchivo'     => $strNombreDebito,
                                            'strUsrCreacion'       => $arrayParametros['strUsuario'],
                                            'strSubModulo'         => 'ContratoDigital');
                    $arrayRespNfsPdf = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
                    if(isset($arrayRespNfsPdf) && $arrayRespNfsPdf['intStatus'] == 200)
                    {
                        unlink($strRutaDocumentos . $strNombreDebito);
                        $arrayParametrosDoc['strTipoDocumento'] = 'debitoempresa';
                        $arrayParametrosDoc['strSrc']           = $arrayRespNfsPdf['strUrlArchivo'];
                    }
                    else
                    {
                        throw new \Exception($arrayRespNfsPdf['strMensaje'].'  -> documentarCertificado()');
                    }
                    $this->guardarDocumentos($arrayParametrosDoc);
                }

                $objPlantillaContrato = $this->emFirmaElect->getRepository('schemaBundle:AdmEmpresaPlantilla')
                                                           ->findOneBy(array("empresaId"  => 1,
                                                                             "codPlantilla" => "contratoSecurityData",
                                                                             "estado"     => "Activo"));
                $strPlantillaContrato = str_replace("$!", "$", $objPlantillaContrato->getHtml());

                foreach($arrayParametros['documentos']['contratoSD'] as $arrayDocumento)
                {
                   $strPlantillaContrato = str_replace("$" . $arrayDocumento['k'], $arrayDocumento['v'], $strPlantillaContrato);
                }
                $strNombreArchivo = "certificado_contratosd_" . $arrayParametros['cedula'] . "_" . $strFecha . ".pdf";
                $this->objGeneraPdf->generateFromHtml($strPlantillaContrato, $strRutaDocumentos . $strNombreArchivo);
                if(!isset($arrayParametros['bandNfs']) && !$arrayParametros['bandNfs'])
                {
                    $arrayParametrosDoc['strTipoDocumento'] = 'contratosd';
                    $arrayParametrosDoc['strSrc']           = $strRutaDocumentos . $strNombreArchivo;
                }
                else
                {
                    $objFile         = file_get_contents($strRutaDocumentos . $strNombreArchivo);
                    $arrayParamNfs   = array(
                                            'prefijoEmpresa'       => $arrayParametros['prefijoEmpresa'],
                                            'strApp'               => $arrayParametros['strApp'],
                                            'arrayPathAdicional'   => $arrayPathAdicional,
                                            'strBase64'            => base64_encode($objFile),
                                            'strNombreArchivo'     => $strNombreArchivo,
                                            'strUsrCreacion'       => $arrayParametros['strUsuario'],
                                            'strSubModulo'         => 'ContratoDigital');
                    $arrayRespNfsPdf = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
                    if(isset($arrayRespNfsPdf) && $arrayRespNfsPdf['intStatus'] == 200)
                    {
                        unlink($strRutaDocumentos . $strNombreArchivo);
                        $arrayParametrosDoc['strTipoDocumento'] = 'contratosd';
                        $arrayParametrosDoc['strSrc']           = $arrayRespNfsPdf['strUrlArchivo'];
                    }
                    else
                    {
                        throw new \Exception($arrayRespNfsPdf['strMensaje'].'  -> documentarCertificado()');
                    }
                }
                $this->guardarDocumentos($arrayParametrosDoc);

                $objPlantillaContrato = $this->emFirmaElect->getRepository('schemaBundle:AdmEmpresaPlantilla')
                                                           ->findOneBy(array("empresaId"  => 1,
                                                                             "codPlantilla" => "formularioSecurityData",
                                                                             "estado"     => "Activo"));
                $strPlantillaContrato = str_replace("$!", "$", $objPlantillaContrato->getHtml());

                foreach($arrayParametros['documentos']['formularioSD'] as $arrayDocumento)
                {
                   $strPlantillaContrato = str_replace("$" . $arrayDocumento['k'], $arrayDocumento['v'], $strPlantillaContrato); 
                }
                $strNombreArchivo = "certificado_formulariosd_" . $arrayParametros['cedula'] . "_" . $strFecha . ".pdf";
                $this->objGeneraPdf->generateFromHtml($strPlantillaContrato, $strRutaDocumentos . $strNombreArchivo);
                if(!isset($arrayParametros['bandNfs']) && !$arrayParametros['bandNfs'])
                {
                    $arrayParametrosDoc['strTipoDocumento'] = 'formulariosd';
                    $arrayParametrosDoc['strSrc']           = $strRutaDocumentos . $strNombreArchivo;
                }
                else
                {
                    $objFile = file_get_contents($strRutaDocumentos . $strNombreArchivo);
                    $arrayParamNfs   = array(
                                            'prefijoEmpresa'       => $arrayParametros['prefijoEmpresa'],
                                            'strApp'               => $arrayParametros['strApp'],
                                            'arrayPathAdicional'   => $arrayPathAdicional,
                                            'strBase64'            => base64_encode($objFile),
                                            'strNombreArchivo'     => $strNombreArchivo,
                                            'strUsrCreacion'       => $arrayParametros['strUsuario'],
                                            'strSubModulo'         => 'ContratoDigital');
                    $arrayRespNfsPdf = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
                    if(isset($arrayRespNfsPdf) && $arrayRespNfsPdf['intStatus'] == 200)
                    {
                        unlink($strRutaDocumentos . $strNombreArchivo);
                        $arrayParametrosDoc['strTipoDocumento'] = 'formulariosd';
                        $arrayParametrosDoc['strSrc']           = $arrayRespNfsPdf['strUrlArchivo'];
                    }
                    else
                    {
                        throw new \Exception($arrayRespNfsPdf['strMensaje'].'  -> documentarCertificado()');
                    }
                }
                $this->guardarDocumentos($arrayParametrosDoc);

                $arrayRespuesta["status"]  = "200";
                $arrayRespuesta["mensaje"] = "Certificado Documentado de manera correcta";
            }
            else
            {
                throw new \Exception("Certificado no Encontrado", 1);
            }
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta['status']  = 500;
            $arrayRespuesta['success'] = false;
            $arrayRespuesta['message'] = ($objException->getCode() == 1)
                                             ? $objException->getMessage()
                                             : 'INTERNAL ERROR';

            $arrayParametrosLog['enterpriseCode']   = $arrayParametros['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = $arrayRespuesta['message'];
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $objException->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayParametros, 128);
            $arrayParametrosLog['creationUser']     = $arrayParametros['strUsuario'];

            $this->serviceUtil->insertLog($arrayParametrosLog);
        }
        return $arrayRespuesta;
    }

    /**
      * Método guardarDocumentos, Sirve para guardar la ruta de los documentos del contrato digital en la base de datos
      *
      * @param array $arrayParametros [intIdCertificado, strSrc, strTipoDocumento, strIp, strUsuario]
      * @return array $arrayRespuesta [status, message, success]
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 29-03-2019
      * 
      * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
      * @version 1.1 19-01-2020 Correción en el manejo de excepciones para persistencia en InfoLog,
      *                         Reemplazo de función implode por json_encode para evitar errores si
      *                         existiesen instancias date dentro del array a convertir.
      */
    private function guardarDocumentos($arrayParametros)
    {
        try
        {
            $entityDocumento = new InfoCertificadoDocumento();
            $entityDocumento->setCertificadoId($arrayParametros['intIdCertificado']);
            $entityDocumento->setSrc($arrayParametros['strSrc']);
            $entityDocumento->setTipoDocumento($arrayParametros['strTipoDocumento']);
            $entityDocumento->setUsrCreacion($arrayParametros['strUsuario']);
            $entityDocumento->setFeCreacion(new \DateTime('now'));
            $entityDocumento->setIpCreacion($arrayParametros['strIp']);
            $entityDocumento->setDocumentado("S");
            $this->emFirmaElect->persist($entityDocumento);
            $this->emFirmaElect->flush();
        }
        catch (\Exception $ex) 
        {
            $arrayRespuesta['status']  = 500;
            $arrayRespuesta['message'] = 'INTERNAL ERROR';
            $arrayRespuesta['success'] = false;

            $arrayParametrosLog['enterpriseCode']   = $arrayParametros['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $ex->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayParametros, 128);
            $arrayParametrosLog['creationUser']     = $arrayParametros['strUsuario'];

            $this->serviceUtil->insertLog($arrayParametrosLog);
        }
    }
    /**
      * Método isValidoCertificado, verifica la validez de un certificado digital
      *
      * @param entity $entityCertificado
      * @return boolean 
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.0 27-03-2019
      * 
      * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
      * @version 1.1 19-01-2020 Correción en el manejo de excepciones para persistencia en InfoLog.
      *
      * @author Edgar Pin Villavicencio <epin@telconet.ec>
      * @version 1.2 20-02-2020 Corrección de catch se quita la varibale de retorno en el catch 
      *
      */
      public function isValidoCertificadoId($intIdCertificado)
      {
        try
        {            
          $entityCertificado = $this->emFirmaElect->getRepository('schemaBundle:InfoCertificado')
                                    ->find($intIdCertificado);
          $boolRetorno = $this->isValidoCertificado($entityCertificado);
              
        }
        catch (\Exception $ex) 
        {            
            $arrayParametrosLog['enterpriseCode']   =  ($entityCertificado) ? $entityCertificado->getEmpresaId() == 1 ? "18" : "10" : "18"; 
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = "CertificacionDocumentosService";
            $arrayParametrosLog['appClass']         = "CertificacionDocumentosService";
            $arrayParametrosLog['appMethod']        = "isValidoCertificado";
            $arrayParametrosLog['appAction']        = "isValidoCertificado";
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $ex->getMessage();
            $arrayParametrosLog['inParameters']     = "";
            $arrayParametrosLog['creationUser']     = "telcos";

            $this->serviceUtil->insertLog($arrayParametrosLog);
            
        }
        return $boolRetorno;
    }
  
}
