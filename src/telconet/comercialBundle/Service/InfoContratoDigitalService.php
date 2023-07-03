<?php

namespace telconet\comercialBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use telconet\soporteBundle\Service\EnvioPlantillaService;

/**
 * Documentación para la clase 'InfoContratoDigitalService'.
 *
 * La clase InfoContratoDigitalService contiene los métodos para la creación de un certificado
 * digital desde TelcosMobile a SecurityData
 * 
 * @author Fabricio Bermeo <fbermeo@telconet.ec>
 * @version 1.0 17-07-2016
 */

class InfoContratoDigitalService
{

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $emComercial;

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $emGeneral;
    
    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $emComunicacion;

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $emInfraestructura;
    
    
    /**
     *
     * @var \telconet\schemaBundle\Service\RestClientService
     */
    private $serviceRestClient;

    /**
     *
     * @var \telconet\schemaBundle\Service\MailerService
     */
    private $serviceMailer;
    
    private $serviceEnvioPlantilla;

    /**
     *
     * @var \telconet\schemaBundle\Service\SerializerService
     */
    private $serviceSerializer;
    
    /**
     *
     * @var type service CertificacionDocumentos
     */
    private $serviceCertificacionDocumentos;

    /**
     *
     * @var type directorio donde se almacenan los contratos
     */
    private $strContratosDirectorio;
    /**
     *
     * @var type URL del WS Contrato Digital
     */
    private $strWsContratoDigitalUrl;
    /**
     *
     * @var type Tiempo de Espera para peticiones hacia el WS Contrato Digital
     */
    private $intWsContratoDigitalTimeOut;
    /**
     *
     * @var type Path de la localizacion de TELCOS
     */
    private $strPathTelcos;
    /**
     *
     * @var type Estado de transaccion OK
     */
    private $strStatusOk;
    /**
     *
     * @var type Estado de transaccion ERROR
     */
    private $strStatusError;
    /**
     *
     * @var type Ruta donde se almacenan los documentos del Cliente
     */
    private $strRutaDocCliente;
    /**
     *
     * @var type Ruta para almacenar documentos
     */
    private $strFileRoot;

    /**
     *
     * @var type Metodo para crear un certificado digital
     */
    private $strOpcionCrearCertificadoDigital;
    /**
     *
     * @var type Metodo para documentar un certificado
     */
    private $strOpcionDocumentarCertificadoDigital;
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
    /**
     *
     * @var type Servicio InfoPunto
     */
    private $serviceInfoPunto;
    /**
     *
     * @var type Servicio InfoServicioTecnico
     */
    private $serviceServiciotecnico;
    
    /**
     *
     * Objeto Servicio Encriptacion
     */
    private $serviceCrypt;
   
    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $emFirmaElect;


    /**
     * *
     * @var type string url de ws de securityData
     */
    private $strUrlSecuridyData;
    
    /**
     * *
     * @var type string ruta donde se almacenan los certificados en telcos
     */    
    private $strRutaCertificado;
    
    private $strWsFirmaDigital;

    /**
     * *
     * @var type string ruta remota para el envío de documentos digitales
     */
    private $strWsContratoDigitalSftpProcesar;

    /**
     * @var \telconet\schemaBundle\Service\UtilService
     */        
    private $serviceUtil;
    
    /**
     * @var \telconet\schemaBundle\Service\ComercialMobileService
     */
    private $serviceComercialMobile;

    private $serviceInfoServicio;
    
    /** 
    * Documentación para el método 'setDependencies'.
    * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
    * @author  telcos
    * @version 1.0
    * @author Andres Montero <amontero@telconet.ec>
    * @version 1.1 20-03-2017 - Se agregan las variables 'serviceUtil'
    * 
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.2 05-02-2019 - Se agrega ruta de documentos digitales en telcos 'strRutaDocCertificado'
    * 
    */
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emComercial                           = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emGeneral                             = $container->get('doctrine.orm.telconet_general_entity_manager');
        $this->emComunicacion                        = $container->get('doctrine.orm.telconet_comunicacion_entity_manager');
        $this->emInfraestructura                     = $container->get('doctrine.orm.telconet_infraestructura_entity_manager');
        $this->serviceRestClient                     = $container->get('schema.RestClient');
        $this->serviceSerializer                     = $container->get('schema.Serializer');
        $this->serviceEnvioPlantilla                 = $container->get('soporte.EnvioPlantilla');
        $this->serviceMailer                         = $container->get('mailer');
        $this->strPathTelcos                         = $container->getParameter('path_telcos');
        $this->serviceCrypt                          = $container->get('seguridad.crypt');
        // ...Services
        $this->serviceInfoPunto                      = $container->get('comercial.InfoPunto');
        $this->serviceServiciotecnico                = $container->get('tecnico.InfoServicioTecnico');
        $this->serviceUtil                           = $container->get('schema.Util');
        $this->serviceCertificacionDocumentos        = $container->get('comercial.CertificacionDocumentos');
        $this->serviceComercialMobile                = $container->get('comercial.ComercialMobile');
        $this->emFirmaElect                          = $container->get('doctrine.orm.telconet_firmaelect_entity_manager');       
        $this->serviceInfoServicio                   = $container->get('comercial.InfoServicio'); 
        
        // ...Parameters
        $this->strWsContratoDigitalUrl               = $container->getParameter('ws_contrato_digital_url');
        $this->intWsContratoDigitalTimeOut           = $container->getParameter('ws_contrato_digital_timeout');
        $this->strStatusOk                           = $container->getParameter('ws_contrato_digital_status_ok');
        $this->strStatusError                        = $container->getParameter('ws_contrato_digital_status_error');
        $this->strRutaDocCliente                     = $container->getParameter('ws_contrato_digital_ruta_doc_cliente');
        $this->strFileRoot                           = $container->getParameter('ruta_upload_documentos');
        $this->strLoginCertificadoDigital            = $container->getParameter('ws_contrato_digital_id');
        $this->strPassCertificadoDigital             = $container->getParameter('ws_contrato_digital_password');
        $this->strContratosDirectorio                = $container->getParameter('contrato_digital_ruta');
        $this->strOpcionCrearCertificadoDigital      = $container->getParameter('ws_contrato_digital_op_crear');
        $this->strOpcionDocumentarCertificadoDigital = $container->getParameter('ws_contrato_digital_op_documentar');
        $this->strOpcionFirmarCertificadoDigital     = $container->getParameter('ws_contrato_digital_op_firmar');
        $this->strUrlSecuridyData                    = $container->getParameter('ws_security_data_url');
        $this->strRutaCertificado                    = $container->getParameter('ruta_certificados_digital');
        $this->strWsFirmaDigital                     = $container->getParameter('ws_firma_digital');   
        $this->strWsContratoDigitalSftpProcesar      = $container->getParameter('ws_contrato_digital_sftp_procesar');   
    }

    /**
     * crearCertificado, método que envía la petición desde un webservice para la creación de un
     * certificado digital
     * @author Fabricio Bermeo <fbermeo@telconet.ec>
     * @version 1.0 17-07-2016
     * @param obj $objContrato, Objeto contrato que fue creado para el cliente
     * @return arrayRespuesta retorna un arreglo con la siguiente información:
     *         $arrayRespuesta['salida'] = '0' => Error en la creación del certificado
     *         $arrayRespuesta['salida'] = '1' => Certificado creado correctamente
     *         $arrayRespuesta['mensaje']    => Un mensaje de éxito o error       
     * 
     * Se valida si entra en catch que la respuesta sea 0, y no se vaya por 1
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1 12/01/2018          
     */
    public function crearCertificado($objContrato)
    {
        $arrayParamEmpresa['code']        = $this->strLoginCertificadoDigital;
        $arrayParamEmpresa['password']    = "" . $this->strPassCertificadoDigital;
        
        $arrayDatosPersona                = $this->obtenerDatos($objContrato);
        $strOp                            = $this->strOpcionCrearCertificadoDigital;
        
        
        
        $arrayData = json_encode(array('op'          => $strOp,
                                       'emp'         => $arrayParamEmpresa,
                                       'certificado' => $arrayDatosPersona
                                      )
                                );
        
        $arrayRest[CURLOPT_TIMEOUT] = $this->intWsContratoDigitalTimeOut;
        $arrayResponseWS            = $this->serviceRestClient->postJSON($this->strWsContratoDigitalUrl, $arrayData, $arrayRest);
        $arrayRespuesta['salida']   = '0';
        
        switch($arrayResponseWS['status'])
        {
            case $this->strStatusOk:
                $arrayStatus = (array) json_decode($arrayResponseWS['result']);
                switch($arrayStatus['cod'])
                {
                    case $this->strStatusOk:
                        $arrayRespuesta['mensaje']    = $arrayStatus['resp'];
                        $arrayRespuesta['salida']     = '1';
                        $arrayRespuesta['datos']      = $arrayDatosPersona;
                        $this->emComercial->getConnection()->beginTransaction();                        
                        try
                        {
                            $this->emComercial->persist($objContrato);
                            $this->emComercial->flush();

                            $this->emComercial->getConnection()->commit();

                        }
                        catch(\Exception $e)
                        {
                            if ($this->emComercial->getConnection()->isTransactionActive())
                            {
                                $this->emComercial->getConnection()->rollback();
                            }
                            $this->emComercial->getConnection()->close();
                            $arrayRespuesta['salida']   = '0';
                            $arrayRespuesta['mensaje']  = 'No se pudo guardar el Certificado';
                        }
                        break;
                    case $this->strStatusError:
                        $arrayRespuesta['mensaje'] = $arrayStatus['err'];
                        break;
                    default:
                        $arrayRespuesta['mensaje'] = $arrayStatus['err'];

                        break;
                }
                break;
            default:
                $arrayRespuesta['mensaje'] = $arrayResponseWS['error'];
                break;
        }
        return $arrayRespuesta;
    }

    /**
     * documentarCertificado, método que envía la petición desde un webservice para la documentación 
     * de un certificado digital
     * @author Fabricio Bermeo <fbermeo@telconet.ec>
     * @version 1.0 17-07-2016
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
     * Se envía el código de la empresa en el array de datos Cliente.
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.1 14/11/2019
     */
    public function documentarCertificado($objContrato,$arrayDatos)
    {
        $arrayDatosCliente              = $this->obtenerDatos($objContrato);
        $arrayDatosCliente["codEmpresa"]= $arrayDatos['codEmpresa'];
        $arrayDatosPersona              = $this->obtenerDatosDocumentarCertificado($objContrato,$arrayDatosCliente);
        $arrayParamEmpresa['code']      = $this->strLoginCertificadoDigital;
        $arrayParamEmpresa['password']  = $this->strPassCertificadoDigital;
        
        $strOp                 = $this->strOpcionDocumentarCertificadoDigital;
        
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
        $arrayParametrosDocumento                               = array();    
        $arrayParametrosDocumento['strNombreDocumento']         = $arrayPathCedula[count($arrayPathCedula)-1];
        $arrayParametrosDocumento['strUbicacionFisicaDoc']      = $strRutaCedulaCompTMP;  
        $arrayParametrosDocumento['strUsrCreacion']             = $objContrato->getUsrCreacion();
        $arrayParametrosDocumento['strClienteIp']               = $objContrato->getIpCreacion();
        $arrayParametrosDocumento['strMensaje']                 = "Archivo agregado al contrato # ".$objContrato->getNumeroContrato();
        $arrayParametrosDocumento['strCodEmpresa']              = $arrayDatos["codEmpresa"];
        $arrayParametrosDocumento['intTipoDocumentoGeneralId']  = $objTipoDocumentoGeneral->getId();
        $arrayParametrosDocumento['intContratoId']              = $objContrato->getId();
        $arrayParametrosDocumento['strTipoArchivo']             = "PDF";
        $arrayParametrosDocumento['strModulo']                  = 'COMERCIAL';
            
        $this->guardarDocumento($arrayParametrosDocumento);
        
        $arrayDataParametros     = array('cedula'      => $arrayDatosPersona['cedula'],
                                         'rubrica'     => $this->codificarDocumentos($strRutaRubricaTMP),
                                         'documentos'  => array(
                                                                'fotoCliente'   => $this->codificarDocumentos($strRutaFotoTMP),
                                                                'fotoCedula1'   => $this->codificarDocumentos($strRutaCedulaCompTMP),
                                                                'contratoEMP'   => $arrayContratoEmp,
                                                                'contratoSD'    => $arrayContratoSd,
                                                                'formularioSD'  => $arrayFormularioSd
                                                             )
                                      );
        
        $arrayDataDocumentar      = json_encode(array('op'      => $strOp,
                                                      'emp'     => $arrayParamEmpresa,
                                                      'data'    => $arrayDataParametros));
        
        $arrayResponseDocumentar[CURLOPT_TIMEOUT] = $this->intWsContratoDigitalTimeOut;
        $arrayResponseWS                          = $this->serviceRestClient->postJSON($this->strWsContratoDigitalUrl, 
                                                                                       $arrayDataDocumentar, 
                                                                                       $arrayResponseDocumentar);
        
        $arrayRespuesta['salida']     = '0';
        
        if(isset($arrayResponseWS['result']))
        {
            //Decodificamos la respuesta del WS
            $arrayStatus = (array)json_decode($arrayResponseWS['result']);
            
            switch($arrayStatus['cod'])
            {
                case $this->strStatusOk:
                    $arrayRespuesta['mensaje'] = $arrayStatus['resp'];
                    $arrayRespuesta['salida']  = '1';
                    break;
                case $this->strStatusError:
                    $arrayRespuesta['mensaje'] = $arrayStatus['err'];
                    break;
                default:
                    $arrayRespuesta['mensaje'] = $arrayStatus['err'];
                    break;
            }
        }
        else
        {
            $arrayRespuesta['mensaje'] = 'Error, no hay respuesta del WS para documentar el certificado';
        }
        return $arrayRespuesta;
    }

     /**
     * obtenerDatosDocumentarCertificado, método que nos retorna todas los parámetros para ser enviados al 
     *                                    webservice, para la documentación del certificado
     * @author Fabricio Bermeo <fbermeo@telconet.ec>
     * @version 1.0 17-07-2016
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
     *
     * 
     * Actualización: Se agrega en arreglo $arrayDatos de retorno el item isDescInstalacion
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 09-02-2017
     * 
     * Actualización: 
      * - Se agrega en arreglo $arrayDatos de retorno el item fechaExpiracion
      * - Se corrige forma de obtener numero de celulares
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.2 09-03-2017
     *
     * Actualización: Se agrega la provincia de emisión del contrato
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.3 30-01-2019
     * 
     * Bug: Se corrige al momento de traer los datos de contactod de la persona 
     *      se estaba llamando de manera equivocada a la funcion, que retornaba las formas de contacto
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.4 09-07-2019 
     *  
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.1 28-10-2019 - Se obtiene información de Instalación por pormociones.
     * @version 1.4 09-07-2019
     * 
     * Actualización: Se agrega variables para adendum de servicios [Puntos Adicionales]
     *     loginPunto  => login del punto al que se le esta creando el adendum
     *     isCedula    => Saber si la identificación del cliente es cédula
     *     isRuc       => Saber si la identificación del cliente es ruc
     *     isPasaporte => Saber si la identificación del cliente es pasaporte
     * @author Edgar Pin Villavicencio
     * @version 1.5 02-11-2019 
     * 
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.6 04-03-2020 - Corrección al obtener información del punto por adendum
     * 
     *  
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.7 11-03-2020 - Se obtieneparámetros de estados de planes para contrato digital.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.8 06-04-2020 - Se envia el contratoId al momento de extraer los servicios de ademdum.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.9 10-04-2020 - Obtención del nombre del plan del punto para adendums de servicio.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.10 18-06-2020 - Implementación para persona jurídica
     * 
     * @author Edgar Pin Villavicencio
     * @version 1.11 29-10-2020 - Se agrega para enviar la fecha del contrato o del adendum para generar el pdf
     *          
     */
    private function obtenerDatosDocumentarCertificado($objContrato, $arrayData)
    {
        $objPersonaEmpresaRol         = $objContrato->getPersonaEmpresaRolId();
        $arrayDatos                   = $this->inicializaParametros();
        $arrayDatos['numeroContrato'] = $objContrato->getNumeroContrato();
        $arrayDatos['tipoCliente']    = $objPersonaEmpresaRol->getPersonaId()->getTipoTributario();
        $arrayDatos['numeroAdendum']  = $arrayData['strNumeroAdendum'];
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
            $arrayDatos['representanteLegal']               = $arrayData['representanteLegal']['nombres'] . ' ' . 
                                                              $arrayData['representanteLegal']['apellidos'];
            $arrayDatos['representanteLegalIdentificacion'] = $arrayData['representanteLegal']['identificacion'];
            $arrayDatos['representanteLegalNacionalidad']   = $arrayData['representanteLegal']['nacionalidad'];
            $arrayDatos['representanteLegalDireccion']      = $arrayData['representanteLegal']['direccion'];
            $arrayDatos['representanteLegalCargo']          = $arrayData['representanteLegal']['cargo'];
            
            $arrayDatosFormaContactoRepLegal                = $this->obtenerFormasDeContactoByPersonaId(
                                                                  $arrayData['representanteLegal']['idPersona']);
            
            $arrayDatos['representanteLegalTelefono']       = $arrayDatosFormaContactoRepLegal['telefono'];
            $arrayDatos['representanteLegalCelular']        = substr($arrayDatosFormaContactoRepLegal['celular'], 0, 
                                                           strlen($arrayDatosFormaContactoRepLegal['celular']) - 1);
            $arrayDatos['representanteLegalCorreo']         = strtolower($arrayDatosFormaContactoRepLegal['correo']);
            
            if (is_object($objPersonaEmpresaRol->getPersonaId()->getTituloId()) && 
                is_object($objPersonaEmpresaRol->getPersonaEmpresaRolId()) && 
                is_object($objPersonaEmpresaRol->getPersonaEmpresaRolId()->getPersonaId()) && 
                is_object($objPersonaEmpresaRol->getPersonaEmpresaRolId()->getPersonaId()->getTituloId()))
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

            if(is_object($objAdendum))
            {
                $arrayDatos['feCreacion'] = $objAdendum->getFeCreacion();
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
        }
      
        $objPunto = $arrayData['objPunto'];

        /* Buscar las rutas de los documentos(rubrica, cedula, foto del cliente), en formato pdf 
          para codificarlas */
        $arrayParametrosRuta = array("objContrato" => $objContrato,
                                     "arrayData"   => $arrayData);
        $arrayDocumentos              = $this->obtenerRutasDocumentos($arrayParametrosRuta);     
        $arrayDatos['rutaCedula']     = $arrayDocumentos['CED'];
        $arrayDatos['rutaRubrica']    = $arrayDocumentos['RUB'];
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
        $arrayServicios['registros'] = null;
        if ($arrayData['strTipo'] == "AP" || $arrayData['strTipo'] == "C")
        {                                                                        
            for ($intContador=0; $intContador < count($arrayData['servicios']); $intContador++)
            {
                $arrayData['servicios'][$intContador] = (int) $arrayData['servicios'][$intContador];
            }
            $arrayServicios['registros'] = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                ->findBy(array("id"     => $arrayData['servicios'],
                                                            "estado" => "Factible"));

            $objAdmiParametroCabPlan  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                            ->findOneBy( array('nombreParametro' => 'ESTADO_PLAN_CONTRATO', 
                                                                                'estado'          => 'Activo') );
        }
           
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

        if($arrayServicios['registros'])
        { 
            foreach($arrayServicios['registros'] as $objValue)
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
                        $strPromIns = 'PROM_INS';
                        $arrayParametrosIns = array(
                            'intIdPunto'               => $objPunto->getId(),
                            'intIdServicio'            => $objValue->getId(),
                            'strCodigoGrupoPromocion'  => $strPromIns,
                            'intCodEmpresa'            => $arrayData['codEmpresa']
                         );
                        $arrayContratoPromoIns[]  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                      ->getPromocionesContrato($arrayParametrosIns);

                       
                        if( isset($arrayContratoPromoIns[0]['intDescuento']) && !empty($arrayContratoPromoIns[0]['intDescuento']) )
                        {
                            $arrayDatos['descInstalacion']   = $arrayContratoPromoIns[0]['intDescuento'].'%';
                            $arrayDatos['isDescInstalacion'] = 'X';
                        }
                        
                   }
                }
            }
        }
     
            
            $arrayDatos['provincia']          = $objPunto->getSectorId()->getParroquiaId()->getCantonId()->getProvinciaId()->getNombreProvincia();
            $arrayDatos['canton']             = $objPunto->getSectorId()->getParroquiaId()->getCantonId()->getNombreCanton();
            $arrayDatos['ciudad']             = $objPunto->getSectorId()->getParroquiaId()->getCantonId()->getNombreCanton();
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
            $arrayParametros['servicios']        = $arrayData['servicios'];
            
            $arrayDatos['arrServicios']       = $this->retornaServiciosAdendum($arrayParametros);
    
            $arrayDatos['loginPunto']         = $objPunto->getLogin();
            $arrayDatos['strTipo']            = $arrayData['strTipo'];
            
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
        }
        return $arrayDatos;
    }

    /**
     * obtenerFormaDePago, método que nos retorna la forma de pago que tiene la persona en su 
     *                     contrato.
     * @author Fabricio Bermeo <fbermeo@telconet.ec>
     * @version 1.0 17-07-2016
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
     * Actualización: Se agrega en arreglo $arrayFormaDePago de retorno el item isDescInstalacion y se valida que si tiene
     * porcentaje de descuento de instalacion marque la variable con X
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 09-02-2017
     * 
     * Actualización:
     * - Se agrega fechaExpiracion de la tarjeta de credito en arreglo $arrayFormaDePago
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 10-03-2017
     */
    public function obtenerFormaDePago($objContrato, $strCodEmpresa, $strIdentificacion)
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
                $arrayFormaDePago['numeroCVV']               = $arrayTipoDebito['numeroCVV'];
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
        
        return $arrayFormaDePago;
    }

    /**
     * 
     * 
     * Actualización: 
     * - Se cambia "/" por "-" en el formato de la fecha de expiracion de tarjeta 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 16-03-2017
     * 
     * Actualización: 
     * - Se valida que el nombre del banco se llene segun el tipo de cuenta. 
     *   Si es tarjeta agrega tipo de cuenta mas nombre banco y si es cta solo se pone nombre del banco
     * - Se agrega fecha de expiracion de la tarjeta de credito
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 09-03-2017
     * 
     * Metodo que devuelve el tipo de forma de pago cuando el cliente a optado por
     * realizar sus pagos a traves de debitos a una cuenta.
     * 
     * @param type $objContrato => datos del contrato
     * @return type $arrayFormaDePago['tipoCuenta']       => Indica el tipo de Cuenta
     *              $arrayFormaDePago['ctaAhorros']       => Indica si es Cta. de Ahorros  
     *              $arrayFormaDePago['ctaCorriente']     => Indica si es Cta. Corriente
     *              $arrayFormaDePago['tarjetaCredito']   => Indica si es Tarj. de Credito
     * 
     * @author Veronica Carrasco <vcarrasco@telconet.ec>
     * @version 1.0 31-08-2016
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
     * retornaTipoUbicacion, método que nos retorna el tipo de vivienda donde se instaló el punto, que tiene
     *                       el contrato
     * @author Fabricio Bermeo <fbermeo@telconet.ec>
     * @version 1.0 17-07-2016
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
     * obtenerRutasDocumentos, método que nos retorna las rutas donde se encuentran los documentos digitales 
     *                         para el contrato digital
     * @author Fabricio Bermeo <fbermeo@telconet.ec>
     * @version 1.0 17-07-2016
     * @param obj $objContrato, Objeto de tipo Contrato
     * @return $arrDocumentos retorna un arreglo con la siguiente información :
     *         $arrDocumentos['CED'] => Ruta donde se encuentra el documento digital frontal de la cédula de identidad
     *         $arrDocumentos['FOT'] => Ruta donde se encuentra el documento digital de la foto
     *         $arrDocumentos['RUB'] => Ruta donde se encuentra el documento digital de la rúbrica
     *         $arrDocumentos['CEDR']=> Ruta donde se encuentra el documento digital del reverso de la cédula de identidad
     *         
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1 15-11-2019 - Se agrega funcionalidad para obtener las rutas de las imágenes cuando es un adendum
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.1 28-09-2020 - Se agrega nuevo parámetro para filtrar por tipo de documento
     *
     */
    private function obtenerRutasDocumentos($arrayParametros)
    {
        $objContrato = $arrayParametros['objContrato'];
        $arrayData   = $arrayParametros['arrayData'];


        $strExtensionDocumento  = isset($arrayData['strExtensionDocumento']) ?
                                  $arrayData['strExtensionDocumento']: "PDF";

        $objTipoDocumento  = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                                  ->findOneBy(array("extensionTipoDocumento"=>$strExtensionDocumento));
        
        $arrayDocumentos   = $this->emComunicacion->getRepository('schemaBundle:InfoDocumento')
                                                  ->findBy(array("contratoId"       => $objContrato->getId(),
                                                                 "tipoDocumentoId"  => $objTipoDocumento->getId()),
                                                                 array('id'  => 'ASC') );
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
     * obtenerDatosVendedorByLogin, método que nos retorna información sobre el usuario que realizó la venta del 
     *                              servicio
     * @author Fabricio Bermeo <fbermeo@telconet.ec>
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
     * inicializaParametros, método que inicializa las variables para documentar las plantillas de
     *                       los diferentes contratos digitales
     * @author Fabricio Bermeo <fbermeo@telconet.ec>
     * @version 1.0 17-07-2016
     * @return $arrayDatos retorna un arreglo inicializando las variables que se muestran
     * 
     * 
     * Actualización: Se agrega en arreglo $arrayDatos de retorno el item isDescInstalacion e isFactElectronica
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 09-02-2017
     * 
     * Actualización: Se agrega en arreglo $arrayDatos de retorno el item fechaExpiracion
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.2 09-03-2017
     * 
     * Actualización: Se agrega en arreglo $arrayDatos de retorno de los items dia, mes, isSiAutoriza, isNoAutoriza, isSiRenueva, isNoRenueva, 
     *                isSiAcceder, isNoAcceder, isSiMediacion, isNoMediacion, isDiscapacitadoSi, isDiscapacitadoNo, provincia
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.3 30-01-2019
     * 
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
     * 
     * Actualización: 
     * 
     * Se agrega en list de documentos a enviar a firmar el documento de debito bancario y el pagare
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.2 19-10-2017
     * - Se agrega validacion que si la respuesta del ws es ok pero si tiene mensaje de error
     *   entonces no procese e inserte en bd el error
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 20-03-2017
     * 
     * @param array $arrayDataRequest - parametros con datos para firma de documento
     * @return $arrayRetorno retorna un arreglo con la siguiente información:
     *         $arrayRetorno['salida'] = 0 => Error en la firma del contrato
     *         $arrayRetorno['salida'] = 1 => Documentos firmados
     *         $arrayRetorno['mensaje']    => Un mensaje de éxito o error               
     * 
     * @author Veronica Carrasco Idrovo <fbermeo@telconet.ec>
     * @version 1.0 17-07-2016
     * 
     */
    public function firmarDocumentos($arrayDataRequest)
    {
        $intIdContrato                    = $arrayDataRequest['contrato'];
        $arrayRetorno                     = array();
        $arrayRetorno['salida']           = 1;
        $strOpcionFuncion                 = $this->strOpcionFirmarCertificadoDigital;
        $booleanAsync                     = false;
        $arrayEmpresaPeticion['code']     = $this->strLoginCertificadoDigital;
        $arrayEmpresaPeticion['password'] = $this->strPassCertificadoDigital;
        if($intIdContrato)
        {
            $objContrato            = $this->emComercial->getRepository('schemaBundle:InfoContrato')
                                                        ->findOneBy(array(
                                                                          "id"     => $intIdContrato,
                                                                          "estado" => "PorAutorizar"
                                                                         ));            
        }
        else
        {
            $arrayRetorno['mensaje'] = "Contrato no cuenta con un documento id valido";
            $arrayRetorno['salida']  = 0;
            return $arrayRetorno;
        }
        if(is_object($objContrato))
        {
            $arrayDatos                    = $this->obtenerDatos($objContrato);
            $arrayDatos["codEmpresa"]      = $arrayDataRequest["codEmpresa"];
            //falta definir arrDatos
            $arrayDatosPersona             = $this->obtenerDatosDocumentarCertificado($objContrato,$arrayDatos);
            $arrayDatosPersona['pinCode']  = $arrayDataRequest['pincode'];
            $arrayContratoEmpresa          = $this->crearContratoEmp($arrayDatosPersona);
            $arrayContratoEntidadEmisora   = $this->crearContratoSd($arrayDatosPersona);
            $arrayFormularioEntidadEmisora = $this->crearFormularioSD($arrayDatosPersona);
            $arrayDebito                   = $this->crearDebitoEmp($arrayDatosPersona);
            $arrayPagare                   = $this->crearPagareEmp($arrayDatosPersona);
            $strRutaRubricaTMP             = explode('.', $arrayDatosPersona['rutaRubrica'])[0].'.png';            
            $arrayCertificado = $this->emFirmaElect->getRepository('schemaBundle:InfoCertificado')
                                                   ->findCertificado(array("strNumCedula"  => $arrayDatosPersona['cedula'],
                                                                           "strCodEmpresa" => '1',
                                                                           "strEstado"     => "valido")); 
            if ($arrayCertificado < 1)
            {
                $arrayRetorno['mensaje'] = "No se encuentra el certificado";
                $arrayRetorno['salida']  = 0;
                return $arrayRetorno;
            }
            //Corregir para que apunte a ruta rúbrica
            $arrayData           = array(
                                         'async'     => $booleanAsync,
                                         'cedula'    => $arrayDatosPersona['cedula'],
                                         'rubrica'   => $this->codificarDocumentos($strRutaRubricaTMP),
                                         'list'      => array(
                                                              $this->crearPlantillaDocumento($arrayContratoEmpresa,"contratoMegadatos"),
                                                              $this->crearPlantillaDocumento($arrayContratoEntidadEmisora ,"contratoSecurityData"),
                                                              $this->crearPlantillaDocumento($arrayFormularioEntidadEmisora,"formularioSecurityData"),
                                                              $this->crearPlantillaDocumento($arrayDebito,"debitoMegadatos"),
                                                              $this->crearPlantillaDocumento($arrayPagare,"pagareMegadatos")
                                                             )
                                        );
            
            $arrayDataFirmaDocumentos = json_encode(array(
                                                          'op'   => $strOpcionFuncion,
                                                          'emp'  => $arrayEmpresaPeticion,
                                                          'data' => $arrayData
                                                         )
                                                   );
            $arrayRest                  = array();
            $arrayRest[CURLOPT_TIMEOUT] = $this->intWsContratoDigitalTimeOut;
            $arrayResponseWS            = $this->serviceRestClient->postJSON($this->strWsContratoDigitalUrl, $arrayDataFirmaDocumentos, $arrayRest);
            
            switch($arrayResponseWS['status'])
            {
                case $this->strStatusOk:
                    $arrayStatus = (array) json_decode($arrayResponseWS['result']);
                    $arrayEnviaDocumentos = $arrayStatus['enviaMails'];

                    switch($arrayStatus['cod'])
                    {
                        case $this->strStatusOk:

                            if ($arrayStatus['err']=="")
                            {
                                $this->emComercial->getConnection()->beginTransaction();                            
                                try
                                {
                                    $objContrato->setEstado('Pendiente');
                                    $this->emComercial->persist($objContrato);
                                    $this->emComercial->flush();

                                    $this->emComercial->getConnection()->commit();

                                    $arrayRetorno['salida']          = 1;
                                    $arrayRetorno['arrDatos']        = $arrayDatos;
                                    $arrayRetorno['arrInfo']         = $arrayDatosPersona;
                                    $arrayRetorno['objContrato']     = $objContrato;
                                    $arrayRetorno['documentos']      = $arrayStatus['documentos'];
                                    $arrayRetorno['mensaje']         = $arrayStatus['resp'];
                                    $arrayRetorno['enviaMails']      = $arrayEnviaDocumentos;
                                }

                                catch(\Exception $e)
                                {
                                    if ($this->emComercial->getConnection()->isTransactionActive())
                                    {
                                        $this->emComercial->getConnection()->rollback();
                                    }
                                    $this->emComercial->getConnection()->close();
                                    $this->serviceUtil->insertError(
                                                                    'Telcos+', 
                                                                    'InfoContratoDigitalService->firmarDocumentos', 
                                                                    $e->getMessage(),
                                                                    $arrayDataRequest['usuario'],
                                                                    '127.0.0.1'
                                                                   );
                                    $arrayRetorno['mensaje'] = "Error en proceso de firmar documentos, no se pudo cambiar estado al contrato";
                                    $arrayRetorno['salida']  = 0;
                                }
                            }
                            else
                            {
                                if ($this->emComercial->getConnection()->isTransactionActive())
                                {
                                    $this->emComercial->getConnection()->rollback();
                                }
                                $this->emComercial->getConnection()->close();
                                $this->serviceUtil->insertError(
                                                                'Telcos+', 
                                                                'InfoContratoDigitalService->firmarDocumentos', 
                                                                $arrayStatus['err'],
                                                                $arrayDataRequest['usuario'],
                                                                '127.0.0.1'
                                                               );
                                $arrayRetorno['salida']  = 0;
                                $arrayRetorno['mensaje'] = $arrayStatus['err'];
                            }
                            break;
                            
                        case $this->strStatusError:
                            $arrayRetorno['salida']  = 0;
                            $arrayRetorno['mensaje'] = $arrayStatus['err'];
                            break;
                        default:
                            $arrayRetorno['salida']  = 0;
                            $arrayRetorno['mensaje'] = $arrayStatus['err'];
                            break;
                    }
                    break;
                default:
                    $arrayRetorno['salida']  = 0;
                    $arrayRetorno['mensaje'] = "No fue posible firmar documentos, se presentó un error inesperado";
                    $this->serviceUtil->insertError(
                                                    'Telcos+', 
                                                    'InfoContratoDigitalService->firmarDocumentos', 
                                                    $arrayResponseWS['error'],
                                                    $arrayDataRequest['usuario'],
                                                    '127.0.0.1'
                                                   );
                    break;
            }
        }
        else
        {
            $arrayRetorno['mensaje'] = "No se ha documentado el certificado digital para el contrato consultado";
            $arrayRetorno['salida']  = 0;
        }
        return $arrayRetorno;
    }
    
    /**
     * codificarDocumentos, método que codifica un archivo a base 64.
     * Se debe considerar que este metodo debe ser invocado de forma controlada
     * para evitar local file inclusion.
     * 
     * @author Fabricio Bermeo <fbermeo@telconet.ec>
     * @version 1.0 17-07-2016
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
     * Método que codifica un archivo que se encuentra en el servidor NFS a base 64.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0
     * @since 23/09/2020
     */
    private function codificarNfsDocumentos($arrayParametro)
    {
        return base64_encode(file_get_contents($arrayParametro['strUrl']));
    }
    /**
     * crearContratoEmp, método que retorna los parámetros para crear el contrato digital del cliente
     *                   con la empresa que ofrece el servicio
     * @author Fabricio Bermeo <fbermeo@telconet.ec>
     * @version 1.0 17-07-2016
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
     * 
     * Actualización: Se agregan nuevas variables para el formato del contrato en arreglo $arrayContrato:
     *     isDescInstalacion
     *     isFactElectronica
     *     isPrecioPromo 
     *     isAceptacionBeneficios
     * @author Andres Montero<amontero@telconet.ec>
     * @version 1.2 09-02-2017
     * 
     * 
     * Actualización: Se agregan nueva variable para el formato del contrato en arreglo $arrayContrato:
     *     fechaExpiracion
     * @author Andres Montero<amontero@telconet.ec>
     * @version 1.3 09-03-2017
     * 
     * Actualizacion: Se agregan nuevas variables para el formato del contrato aprobado por la arcotel 
     *     isSiAutoriza
     *     isNoAutoriza
     *     isSiRenueva
     *     isNoRenueva
     *     isSiAcceder
     *     isNoAcceder
     *     isSiMediacion
     *     isNoMediacion
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.4 30-01-2019
     * 
     * Actualización: Bug - Se agrega variable para determinar la compartición del servicio de internet, estaba saliendo el casillero en blanco en 
     *                      el contrato digital.
     *   is2a1
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.5 07-01-2020
     * 
     * Actualización: Se agregan nuevas variables para el formato de adendum de servicio [Puntos adicionales]
     *     loginPunto  => Login del punto al que se esta creando el adendum
     *     isCedula    => Saber si la identificación del cliente es cédula
     *     isRuc       => Saber si la identificación del cliente es ruc
     *     isPasaporte => Saber si la identificación del cliente es pasaporte
     *     isPro       => Saber si el tipo de servicio es PRO
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.6 02-11-2019
     *          
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.7 16-04-2020 Se asigna o numero de contrato o número de adendum de acuerdo al tipo.
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
                  'v'   => $arrayDatosPersona['pinCode']),
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
                  'v'   => $arrayDatosPersona['tipoCliente'] == 'NAT'
                           ? strtoupper($arrayDatosPersona['nombreCliente'])
                           : strtoupper($arrayDatosPersona['razonSocial'])),
            array('k'   => 'identificacion', 
                  'v'   => $arrayDatosPersona['tipoCliente'] == 'NAT'
                               ? $arrayDatosPersona['cedula']
                               : $arrayDatosPersona['ruc']),
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
            array('k'   => 'representanteLegal',
                  'v'   => strtoupper($arrayDatosPersona['representanteLegal'])),
            array('k'   => 'ciRepresentanteLegal', 
                  'v'   => strtoupper($arrayDatosPersona['representanteLegalIdentificacion'])),
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
            array('k'   => 'productoOtrosInstalacion', 
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['OTROS']['instalacion']),
            array('k'   => 'productoOtrosPrecio', 
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['OTROS']['precio']),
            array('k'   => 'productoOtrosCantidad',
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['OTROS']['cantidad']),
            array('k'   => 'productoOtrosSubtotal',
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['OTROS']['subtotal']),
            array('k'   => 'productoNetLifeZonePrecio', 
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['NETWIFI']['precio']),
            array('k'   => 'productoNetLifeZoneCantidad',
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['NETWIFI']['cantidad']),
            array('k'   => 'productoNetLifeZoneSubtotal',
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['NETWIFI']['subtotal']),                  
            array('k'   => 'productoNetLifeCamPrecio', 
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['NETLIFECAM']['precio']),
            array('k'   => 'productoNetLifeCamCantidad',
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['NETLIFECAM']['cantidad']),
            array('k'   => 'productoNetLifeCamSubtotal',
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['NETLIFECAM']['subtotal']),                  
            array('k'   => 'productoWifiPrecio', 
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['APWIFI']['precio']),
            array('k'   => 'productoWifiCantidad',
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['APWIFI']['cantidad']),
            array('k'   => 'productoWifiSubtotal',
                  'v'   => $arrayDatosPersona['arrServicios']['detalle']['APWIFI']['subtotal']),
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
                  'v'   => "display: none;")
        );
        
        return $arrayContrato;
    }

    /**
     * crearContratoSd, método que retorna los parámetros para crear el contrato digital del cliente
     *                  security data
     * @author Fabricio Bermeo <fbermeo@telconet.ec>
     * @version 1.0 17-07-2016
     *          
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.1 15-04-2020 Se agrega dato 'isPersonaNatural' del cliente
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.2 18-06-2020 - Implementación para persona jurídica
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.3 29-01-2021 - Se quita espacio a string para realizar correctamente
     * validación de tipoCliente
     *          
     * @param array $arrayDatosPersona, datos del cliente. se obtiene la siguiente información:
     *          $arrayDatosPersona['nombreCliente'] => nombre del cliente
     *          $arrayDatosPersona['cedula']        => cedula del cliente
     *          $arrayDatosPersona['ruc']           => ruc del cliente
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
                  'v'   => $arrayDatosPersona['tipoCliente'] == 'NAT'
                               ? strtoupper($arrayDatosPersona['nombreCliente'])
                               : strtoupper($arrayDatosPersona['razonSocial'])),
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
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 10-04-2016
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
                  'v'   => date('Y/m/d')),
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
                  'v'   => "display: none;")                  
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
                          'v' => $arrayDatosPersona['arrServicios']['detalle'][$arrayProdParametro['valor2']]['cantidad']),
                    array('k' => $arrayProdParametro['valor2'] . 'Instalacion',
                          'v' => $arrayDatosPersona['arrServicios']['detalle'][$arrayProdParametro['valor2']]['instalacion']),
                    array('k' => $arrayProdParametro['valor2'] . 'SubTotal',
                          'v' => $arrayDatosPersona['arrServicios']['detalle'][$arrayProdParametro['valor2']]['subtotal'])
                        );

            }
        }
        $arrayAdendumAdicional['arrayProdParametros'] = $arrayDatosPersona['arrServicios']['arrayProdParametros'];

        return $arrayAdendumAdicional;
    }

    /**
     * crearFormularioSD, método que retorna los parámetros para crear el formulario que debe
     *                    llenar el cliente con security data
     * @author Fabricio Bermeo <fbermeo@telconet.ec>
     * @version 1.0 17-07-2016
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.1 18-06-2020 - Implementación para persona jurídica
     *
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
                  'v' => $arrayDatosPersona['tipoCliente'] == 'NAT'
                             ? strtoupper($arrayDatosPersona['nombreCliente'])
                             : strtoupper($arrayDatosPersona['representanteLegal'])),
            array('k' => 'identificacion', 
                  'v' => $arrayDatosPersona['tipoCliente'] == 'NAT'
                             ? $arrayDatosPersona['cedula']
                             : $arrayDatosPersona['representanteLegalIdentificacion']),
            array('k' => 'nacionalidad', 
                  'v' => $arrayDatosPersona['nacionalidad']),
            array('k' => 'emailCliente', 
                  'v' => $arrayDatosPersona['tipoCliente'] == 'NAT'
                             ? $arrayDatosPersona['correo']
                             : $arrayDatosPersona['representanteLegalCorreo']),
            array('k' => 'direccion', 
                  'v' => $arrayDatosPersona['tipoCliente'] == 'NAT'
                             ? strtoupper($arrayDatosPersona['direccion'])
                             : strtoupper($arrayDatosPersona['representanteLegalDireccion'])),
            array('k' => 'provincia', 
                  'v' => $arrayDatosPersona['provincia']),
            array('k' => 'telefono', 
                  'v' => $arrayDatosPersona['tipoCliente'] == 'NAT'
                             ? $arrayDatosPersona['telefono']
                             : $arrayDatosPersona['representanteLegalTelefono']),
            array('k' => 'ciudad', 
                  'v' => strtoupper($arrayDatosPersona['ciudad'])),
            array('k' => 'celular', 
                  'v' => $arrayDatosPersona['tipoCliente'] == 'NAT'
                             ? $arrayDatosPersona['celular']
                             : $arrayDatosPersona['representanteLegalCelular']),
            array('k' => 'fechaActual', 
                  'v' => date('Y/m/d')));
        return $arrayFormulario;
    }

    /**
     * obtenerDatos, método que obtiene todos los datos necesarios para ser enviados al webservice,
     * para la creación del certificado digital
     * @author Fabricio Bermeo <fbermeo@telconet.ec>
     * @version 1.0 17-07-2016
     * @param objeto $objContrato, Objeto de tipo Contrato que pertenece al cliente ingresado
     * @return array arrRespuesta, retorna un arreglo con la siguiente información:
     *         $arrayRespuesta['cedula']  => Identifiación del cliente
     *         $arrayRespuesta['nombres'] => Nombres del cliente
     *         $arrayRespuesta['pApell']  => Apellido paterno del cliente
     *         $arrayRespuesta['sApell']  => Apellido materno del cliente
     *         $arrayRespuesta['dir']     => Dirección del cliente
     *         $arrayRespuesta['telf']    => Un número de contacto del cliente
     *         $arrayRespuesta['email']   => Correo electrónico del cliente
     *         $arrayRespuesta['ciudad']  => Ciudad donde se instala el punto del cliente
     *         $arrayRespuesta['pais']    => País perteneciente a la ciudad
     *         $arrayRespuesta['fact']    => Número de factura
     *         $arrayRespuesta['pass']    => Contraseña para la creación del certificado
     * 
     * Actualización: Se reemplazan caracteres especiales para grabar el certificado sin acentos ni eñes
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 21-04-2017
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.2 18-06-2020 - Implementación para persona jurídica
     *
     * 
     */
    public function obtenerDatos($objContrato)
    {
        $arrayRespuesta               = array();

        //Arreglo de tildes y eñes
        $arrayTildes                  = array('á','é','í','ó','ú','â','ê','î','ô','û','ã','õ','ç','ñ','Á','É','Í','Ó','Ú',
                                              'Â','Ê','Î','Ô','Û','Ã','Õ','Ç','Ñ','ä','ë','ï','ö','ü','Ä','Ë','Ï','Ö','Ü',
                                              'à','è','ì','ò','ù','À','È','Ì','Ò','Ù');
        $arraySinTilde                = array('a','e','i','o','u','a','e','i','o','u','a','o','c','n','A','E','I','O','U',
                                              'A','E','I','O','U','A','O','C','N','a','e','i','o','u','A','E','I','O','U',
                                              'a','e','i','o','u','A','E','I','O','U');

        $arrayRespuesta['strTipoTributario'] = $objContrato->getPersonaEmpresaRolId()->getPersonaId()->getTipoTributario();
        $strCodEmpresa                       = $objContrato->getPersonaEmpresaRolId()->getEmpresaRolId()->getEmpresaCod()->getId();
        $strPrefijoEmpresa                   = $objContrato->getPersonaEmpresaRolId()->getEmpresaRolId()->getEmpresaCod()->getPrefijo();
        $strCedula                           = $objContrato->getPersonaEmpresaRolId()->getPersonaId()->getIdentificacionCliente();
        $strTipoIdentificacion               = $objContrato->getPersonaEmpresaRolId()->getPersonaId()->getTipoIdentificacion();
        
        if($arrayRespuesta['strTipoTributario'] == 'NAT')
        {
            
            $strNombres         = str_replace($arrayTildes, $arraySinTilde, $objContrato->getPersonaEmpresaRolId()->getPersonaId()->getNombres());
            $arrayApellidos     = explode(" ", $objContrato->getPersonaEmpresaRolId()->getPersonaId()->getApellidos());
            $strPApell          = str_replace($arrayTildes,$arraySinTilde,$arrayApellidos[0]);
            $strSApell          = str_replace($arrayTildes,$arraySinTilde,$arrayApellidos[1]);
            $arrayFormaContacto = $this->obtenerFormasDeContactoByPersonaId($objContrato->getPersonaEmpresaRolId()->getPersonaId()->getId());

        }
        else if($arrayRespuesta['strTipoTributario'] == 'JUR')
        {
            $arrayRespuestaRepLegal = $this->serviceComercialMobile
                                        ->getRepresentanteLegalPersonaJuridica(array('strTipoIdentificacion'   => $strTipoIdentificacion,
                                                                                     'strIdentificacion'       => $strCedula,
                                                                                     'strCodEmpresa'           => $strCodEmpresa,
                                                                                     'strPrefijoEmpresa'       => $strPrefijoEmpresa,
                                                                                     'usrCreacion'             => 'InfoContratoDigitalService',
                                                                                     'booleanOrigenGetPersona' => true));

            //Datos del representante legal
            $strTipoIdentificacion      = $arrayRespuestaRepLegal['response']['tipoIdentificacion'];
            $strCedula                  = $arrayRespuestaRepLegal['response']['identificacion'];
            $strNombres                 = str_replace($arrayTildes, $arraySinTilde, $arrayRespuestaRepLegal['response']['nombres']);
            $arrayApellidos             = explode(" ", $arrayRespuestaRepLegal['response']['apellidos']);
            $strPApell                  = str_replace($arrayTildes, $arraySinTilde, $arrayApellidos[0]);
            $strSApell                  = str_replace($arrayTildes, $arraySinTilde, $arrayApellidos[1]);
            $arrayRespuesta['strCargo'] = $arrayRespuestaRepLegal['response']['cargo'];
            $arrayRespuesta['fecha']    = $objContrato->getFeFinContrato();
            $arrayFormaContacto         = $this->obtenerFormasDeContactoByPersonaId($arrayRespuestaRepLegal['response']['idPersona']);
        }

        $arrayRespuesta['cedula']             = $strCedula;
        $arrayRespuesta['nombres']            = $strNombres;
        $arrayRespuesta['pApell']             = $strPApell;
        $arrayRespuesta['sApell']             = $strSApell;
        $arrayRespuesta['strRuc']             = $objContrato->getPersonaEmpresaRolId()->getPersonaId()->getIdentificacionCliente();
        $arrayRespuesta['strRazonSocial']     = $objContrato->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial();
        $arrayRespuesta['dir']                = str_replace($arrayTildes, $arraySinTilde, $objContrato->getPersonaEmpresaRolId()->getPersonaId()
                                                    ->getDireccionTributaria());
        $arrayRespuesta['fecha']              = $objContrato->getFeFinContrato();
        $arrayRespuesta['representanteLegal'] = $arrayRespuestaRepLegal['response'];
        
        if($arrayFormaContacto)
        {
            $arrayCelular                 = explode("-", $arrayFormaContacto['celular']);
            $arrayRespuesta['email']      = $arrayFormaContacto['correo'];
            $arrayRespuesta['telf']       = $arrayCelular[0];
        }
        
        if(!($arrayRespuesta['email']))
        {
            $arrayRespuesta['email']  = "notiene@dominio.com";
        }
        
        if(!isset($arrayRespuesta['sApell']))
        {
            $arrayRespuesta['sApell']     = "";
        }
        
        if(!($arrayRespuesta['telf']))
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

        if (!$arrayRespuesta['email'])
        {
            $arrayContactosCorreosPunto     = $this->emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                ->findContactosByPunto($objPunto->getLogin(), 'Correo Electronico');
            foreach ($arrayContactosCorreosPunto as $arrayContactoMail) 
            {
                $arrayRespuesta['email'] = $arrayContactoMail['valor'];
            break;
            }
        }
        if (!$arrayRespuesta['telf'])
        {
            $arrayFpTelf = array("Telefono Movil", "Telefono Movil Claro", "Telefono Movil Movistar", "Telefono Movil CNT");
            foreach ($arrayFpTelf as $strFp)
            {
                $arrayContactosTelfPunto     = $this->emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                    ->findContactosByPunto($objPunto->getLogin(), $strFp);

                if($arrayContactosTelfPunto)
                {
                    foreach($arrayContactosTelfPunto as $arrayContactoTelefono)
                    {
                        $arrayRespuesta['telf'] = $arrayContactoTelefono['valor'];
                        break;    
                    }
                }
            }    
        }        
        return $arrayRespuesta;
    }

    /**
     * obtenerFormasDeContactoByPersonaId, método que me retorna todos los tipos de contactos que
     * tiene una persona
     * @author Fabricio Bermeo <fbermeo@telconet.ec>
     * @version 1.0 17-07-2016
     * @param objeto $intPersonaId, Objeto de tipo Persona
     * @return array $arrayContactos, retorna un arreglo con la siguiente información:
     *         $arrayContactos['telefono'] => Cadena con los números de teléfono fijos
     *         $arrayContactos['celular']  => Cadena con los números de teléfono de las diferentes operadoras
     *                                      de celulares
     *         $arrayContactos['correo']   => Correo electrónico de la persona
     */
    public function obtenerFormasDeContactoByPersonaId($intPersonaId)
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
        $objAdmiParametroEstado  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                    ->findOneBy( array('nombreParametro' => 'ESTADOS_CONTRATO_DIGITAL', 
                                                                    'estado'          => 'Activo') );

        if(is_object($objAdmiParametroEstado))
        {        
            $arrayParametroDetEstado = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                       ->findBy( array ("parametroId" => $objAdmiParametroEstado->getId(),
                                                                        "estado"      => "Activo" ));
            $arrayEstados = array();   
            $arrayMovil = array();                                                                     
            if ($arrayParametroDetEstado)
            {
                foreach($arrayParametroDetEstado as $objAdmiParametroDet)
                {  
                    if ($objAdmiParametroDet->getDescripcion() == 'FORMA_CONTACTO')
                    {
                        $arrayEstados = explode(",",$objAdmiParametroDet->getValor1());
                    }
                    if ($objAdmiParametroDet->getDescripcion() == 'CODIGO_TELEFONO_MOVIL')
                    {
                        $arrayMovil = explode(",",$objAdmiParametroDet->getValor1());
                    }                    

                }
            }

        }

        if (empty($arrayContactos['celular']))
        {
            $arrayDataFormaContactoCliente = $this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                               ->findBy(array('personaId'    => $intPersonaId,
                                                                              'estado'       => $arrayEstados));
            if($arrayDataFormaContactoCliente)
            {
                foreach($arrayDataFormaContactoCliente as $objValue)
                {
                    if (in_array($objValue->getFormaContactoId()->getCodigo(), $arrayMovil))
                    {
                        $arrayContactos['celular'] .= $objValue->getValor() . "-";
                    }
                }
            }
            

        }
        if (empty($arrayContactos['correo']))
        {
            $arrayDataFormaContactoCliente = $this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                               ->findBy(array('personaId'    => $intPersonaId,
                                                                              'estado'       => $arrayEstados));
            if($arrayDataFormaContactoCliente)
            {
                foreach($arrayDataFormaContactoCliente as $objValue)
                {
                    if ($objValue->getFormaContactoId()->getCodigo() == "MAIL")
                    {
                        $arrayContactos['correo'] .= $objValue->getValor() . "-";
                    }
                }
            }

        }
        return $arrayContactos;
    }

    /**
     * Retorna un arreglo que repesenta a la plantilla y las variables necesarias para
     * llenar una plantilla para los documentos necesarios al firmar un contrato
     * @param type $arrayParametros
     * @param type $strNombrePlantilla
     * @return type array(
     *                    cod : Nombre de plantilla en Certificacion de Documentos 
     *                    listVariables : Arreglo de parametros con que se llenara la plantilla del contrato
     *                    ) 
     * 
     * @author  Veronica Carrasco <vcarrasco@telconet.ec>          
     * @version 1.0 21-07-2016
     * @date    
     */
    public function crearPlantillaDocumento($arrayParametros, $strNombrePlantilla)
    {
        return array(
                    "cod"             => $strNombrePlantilla,
                    "listVariables"   => $arrayParametros
                    );
    }
    
    /**
     * retornaServicios, Retorna un arreglo con campos del servicio por punto
     * @param type $objPunto          => objeto del punto del cliente
     * @param type $strEstadoServicio => estado del servicio para consultar servicios del punto
     * @return type array(
     *                    idServicio : Id del servicio
     *                    idPlan : id del plan
     *                    ) 
     * 
     * @author  Fabricio Bermeo <fbermeo@telconet.ec>          
     * @version 1.0 21-07-2016
     * 
     *  
     * Actualización: Se agrega en arreglo $arrayServicio de retorno el item isPrecioPromo
     * Se llena $arrayServicio['isPrecioPromo'] cuando tenga precio promoción de plan
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 09-02-2017
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.2 26-10-2019 - Se obtiene observación, descuento y periodos según la promoción de
     * Instalación o Mensualidad.
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.3 11-03-2020 - Se obtiene parámetros de estados de planes para contrato digital.
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.4 06-04-2020 - Se mejora el catch en la presente función.
     */
    public function retornaServicios($objPunto, $strEstadoServicio, $intCodEmpresa)
    //public function retornaServicios($arrayParametros)
    {
        $strCodEmpresa            = $intCodEmpresa;  
        $strPromIns               = 'PROM_INS';
        $strPromMens              = 'PROM_MENS';
        try
        { 

            $arrayServicios  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
            ->findTodosServiciosXEstado($objPunto->getPersonaEmpresaRolId(),
                                        0,
                                        1000,
                                        $strEstadoServicio);


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
                    
                             $arrayParametrosIns = array(
                                'intIdPunto'               => $objPunto->getId(),
                                'intIdServicio'            => $objValue->getId(),
                                'strCodigoGrupoPromocion'  => $strPromIns,
                                'intCodEmpresa'            => $strCodEmpresa
                                ); 
                
                             $arrayParametrosMens = array(
                                'intIdPunto'               => $objPunto->getId(),
                                'intIdServicio'            => $objValue->getId(),
                                'strCodigoGrupoPromocion'  => $strPromMens,
                                'intCodEmpresa'            => $strCodEmpresa
                                ); 

                            $arrayContratoPromoIns[]  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                        ->getPromocionesContrato($arrayParametrosIns);


                            $arrayContratoPromoMens[]  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                        ->getPromocionesContrato($arrayParametrosMens);



                            $strNombrePlan= strtoupper($arrayServicio["obsServicio"].$objValue->getPlanId()->getNombrePlan()." ");
                            
                            $arrayServicio["nombrePlan"] = $strNombrePlan;
                            $strObservaInstalacion       = $arrayContratoPromoIns[0]['strObservacion'];
                            $strObservaMens              = $arrayContratoPromoMens[0]['strObservacion'];
                            $strAplicaCondiciones        = 'Aplica Condiciones';
                            $strObservaMens              = $this->truncarPalabrasObservacion($strObservaMens,440,' ','');
                            $strObservacionContrato      = "{$strNombrePlan}<br>{$strObservaInstalacion}<br>"
                                                         . "{$strObservaMens}<br> {$strAplicaCondiciones}";

                            $strPeriodosMens        = $arrayContratoPromoMens[0]['intCantPeriodo'];

                            $strDescuentoMens       = $arrayContratoPromoMens[0]['intDescuento'];

                            $objProdCaracCapNac     = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                        ->findOneBy(array("productoId"       => $planDet->getProductoId(),
                                                                                          "caracteristicaId" => $objCaracteristicaCapcNac));

                            $objProdCaracCapInt     = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                        ->findOneBy(array("productoId"       => $planDet->getProductoId(),
                                                                                          "caracteristicaId" => $objCaracteristicaCapcInt));

                            $objServProdCaracCapNac = $this->emComercial->getRepository('schemaBundle:InfoPlanProductoCaract')
                                                                        ->findOneBy(array("planDetId"                 => $planDet->getId(),
                                                                                          "productoCaracterisiticaId" => $objProdCaracCapNac));

                            $objPlanProdCaracCapInt = $this->emComercial->getRepository('schemaBundle:InfoPlanProductoCaract')
                                                                        ->findOneBy(array("planDetId"                 => $planDet->getId(),
                                                                                          "productoCaracterisiticaId" => $objProdCaracCapInt));
                            
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
                                                     
                                                     //INFO_SERVICIO_PROD_CARACT
                    $arrayServicioCaracteristica = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                        ->findBy(array("servicioId" => $objValue->getId()));
                    $arrayProductoCaracteristica = array();
                    foreach ($arrayServicioCaracteristica as $arrayServCarac)
                    {
                        $objProductoCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                                                                  
                                                                       ->findOneBy(array("id" => $arrayServCarac->getProductoCaracterisiticaId()));
                                                                                                                  
                        $objCaracteristica         = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                       ->findOneBy(array("id" => $objProductoCaracteristica->getCaracteristicaId()));

                        $arrayProductoCaracteristica[] = array($objCaracteristica->getDescripcionCaracteristica => $arrayServCarac->getValor());

                    }
                    $intImpuesto = $this->obtenerImpuesto($objProducto);
                    
                    $floatPrecio      = $this->evaluarFuncionPrecio($objProducto->getFuncionPrecio(), $arrayProductoCaracteristica);
                    $strNombreTecnico = $objProducto->getNombreTecnico();
                    
                    $arrayServiciosContratados[$strNombreTecnico]["precio"]   = $arrayServiciosContratados[$strNombreTecnico]["precio"]
                                                                                    +$floatPrecio;
                    $arrayServiciosContratados[$strNombreTecnico]["cantidad"] = $arrayServiciosContratados[$strNombreTecnico]["cantidad"]+1;
                    $floatSubtotal = $floatSubtotal + $floatPrecio;
                                                     
                    $arrayServicio["impuestos"] = $arrayServicio["impuestos"]+($floatPrecio * $intImpuesto/100);
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
            error_log("error InfoContratoDigitalService->retornaServicios()" . $ex->getMessage(). " file " . $ex->getFile() .
                      " linea " . $ex->getLine());
            throw $ex;
        }
        return $arrayServicio;
    }
    
    /**
     * Obtiene el porcentaje de un producto 
     * 
     * @param type $objProducto
     * @return int
     * 
     * @author  Veronica Carrasco <vcarrasco@telconet.ec>          
     * @version 1.0 21-07-2016
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
    
    /**
     * Metodo que guarda los documentos digitales firmados tanto física como logicamente
     * 
     * @author Veronica Carrasco <vcarrasco@telconet.ec>
     * @version 1.0 07-08-2016
     * @param type $arrayParametros, datos del contrato
     * @param type $arrDocumentos[array[obj[k = clave ,v = valor]]]
     * @return array
     * 
     * Actualización: Se corrige validación donde se consulta si 
     * la forma de pago es tarjeta de credito o cuenta corriente
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 09-02-2017 
     * 
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.2 20-04-2018 Se agrega envío de parámetro que indica el ciclo de facturación del cliente. 
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.3 13-09-2019 Se corrige la extension del documento para contrato digital
     *  
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.4 28-10-2019 - Se cambia observación por nombre del plan.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.5 24-07-2020 - Obtención de la razón social de la persona jurídica al enviar correo.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.6 28-09-2020 - Se almacenan los documentos en el servidor NFS.
     */
    public function guardarDocumentos($arrayParametros,$arrDocumentos)
    {         
        $arrayRutaArchivos      = array();
        $boleanEsTCreditoCtaCte = false;
        $strEstadoServicio      = "Factible";
        $objContrato            = $this->emComercial->getRepository('schemaBundle:InfoContrato')
                                                    ->find($arrayParametros['intContratoId']);

        if(is_object($objContrato))
        {
            
            $arrayFormaDePago = $this->obtenerFormaDePago(
                                                          $objContrato,
                                                          $arrayParametros['codEmpresa'],
                                                          $objContrato->getPersonaEmpresaRolId()->getPersonaId()->getIdentificacionCliente());
            if ((isset($arrayFormaDePago["tarjetaCredito"]) && !empty($arrayFormaDePago["tarjetaCredito"])) ||
                (isset($arrayFormaDePago["ctaCorriente"]) && !empty($arrayFormaDePago["ctaCorriente"])))
            {
                $boleanEsTCreditoCtaCte = true;
            }
            
        }
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
            $strNombreArchivo     = $objDocumento->k."-".$objContrato->getNumeroContrato().date('YmdHis').".pdf";
            $strArchivoCodificado = $objDocumento->v;

            if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'])
            {
                $arrayParamNfs = array('strNombreArchivo'       => $strNombreArchivo,
                                       'strArchivoCodificado'   => $strArchivoCodificado,
                                       'prefijoEmpresa'         => $arrayParametros['prefijoEmpresa'],
                                       'arrayPathAdicional'     => $arrayParametros['arrayPathAdicional'],
                                       'strApp'                 => $arrayParametros['strApp'],
                                       'strUsrCreacion'         => $arrayParametros['strUsrCreacion']);
                $arrayRespNfs  = $this->guardarNfsDocumentos($arrayParamNfs);
                $strRuta       = $arrayRespNfs['strUrlArchivo'];
            }
            else
            {
                $strRuta = $this->decodificarDocumentos($strArchivoCodificado, $strNombreArchivo);
            }
            
            
            if($objDocumento->k == 'contratoMegadatos')
            {
                $strTipoDocumentoGeneral = 'CONT';
            }
            else
            {
                $strTipoDocumentoGeneral = 'OTR';
            }
   
            $objTipoDocumentoGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                       ->findOneBy(array(
                                                                         'codigoTipoDocumento' => $strTipoDocumentoGeneral));
            
            $arrayParametrosDocumento['strNombreDocumento']         = $strNombreArchivo;
            $arrayParametrosDocumento['strUbicacionFisicaDoc']      = $strRuta;  
            $arrayParametrosDocumento['strUsrCreacion']             = $arrayParametros['strUsrCreacion'];
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
        //Envio de correo
       
        $arrayParametrosContacto = array("intIdPersona"     =>  $objContrato->getPersonaEmpresaRolId()->getPersonaId()->getId(),
                                         "strFormaContacto" => 'Correo Electronico');
        $arrayFormasContactoCliente = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                           ->getContactosByIdPersonaAndFormaContacto($arrayParametrosContacto);

        foreach ($arrayFormasContactoCliente as $arrayContactoMail) 
        {
            $arrayContactosCorreosPuntoMail[] = $arrayContactoMail['valor'];
        }
        $objPunto   = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                                        ->findOneBy(array("personaEmpresaRolId" => $objContrato->getPersonaEmpresaRolId()->getId()));
        if (!$arrayContactosCorreosPuntoMail)
        {
            $arrayContactosCorreosPunto     = $this->emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                ->findContactosByPunto($objPunto->getLogin(), 'Correo Electronico');
            foreach ($arrayContactosCorreosPunto as $arrayContactoMail) 
            {
                $arrayContactosCorreosPuntoMail[] = $arrayContactoMail['valor'];
            }
        }

        $objPersona = $objContrato->getPersonaEmpresaRolId()->getPersonaId();
        $strZipName = "documentos_digitales-".$objContrato->getNumeroContrato().date("dmYHis").".zip";
        // Creamos un .zip de los archivos a enviar en el correo
        if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'])
        {
            $arrayParmZipNfs = array('strArchivoZip'        => $strZipName,
                                     'arrayRutaArchivos'    => $arrayRutaArchivos,
                                     'prefijoEmpresa'       => $arrayParametros['prefijoEmpresa'],
                                     'arrayPathAdicional'   => $arrayParametros['arrayPathAdicional'],
                                     'strApp'               => $arrayParametros['strApp'],
                                     'strUsrCreacion'       => $arrayParametros['strUsrCreacion']);
            $arrayRespZipNfs = $this->zipNfsFiles($arrayParmZipNfs);
            $strRutaZip      = $arrayRespZipNfs['strUrlArchivo'];
        }
        else
        {
            $strRutaZip = $this->zipFiles($strZipName, $arrayRutaArchivos);
        }
        // Enviar correo con documentos firmados
        $arrayParametrosBody = array("nombrecliente"=>"","nombrePlan"=>"", "nombreCicloFacturacion"=>"");

        if($objPersona->getTipoTributario() == 'NAT')
        {
            $arrayParametrosBody["nombrecliente"] = trim($objPersona->getNombres() . " " . $objPersona->getApellidos());
        }
        else if($objPersona->getTipoTributario() == 'JUR')
        {
            $arrayParametrosBody["nombrecliente"] = trim($objPersona->getRazonSocial());
        }

        if ($boleanEsTCreditoCtaCte)
        {
            $strEstadoServicio = "PrePlanificada";
        }

        $arrayParametrosBody["nombrePlan"]            = $this->retornaServicios(
                                                                                $objPunto,
                                                                                $strEstadoServicio,
                                                                                $arrayParametros['codEmpresa']
                                                                               )["nombrePlan"];

        $objPersonaEmpresaRol = $objContrato->getPersonaEmpresaRolId();
        
        if(is_object($objPersonaEmpresaRol))
        { 
            $arrayParamCiclo                     = array();
            $arrayParamCiclo['intIdPersonaRol']  = $objPersonaEmpresaRol->getId();
            $arrayPersEmpRolCaracCicloPreCliente = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                     ->getCaractCicloFacturacion($arrayParamCiclo);
            if( isset($arrayPersEmpRolCaracCicloPreCliente['intIdPersonaEmpresaRolCaract'])
                    && !empty($arrayPersEmpRolCaracCicloPreCliente['intIdPersonaEmpresaRolCaract']) )
            {
                $arrayParametrosBody["nombreCicloFacturacion"]  = $arrayPersEmpRolCaracCicloPreCliente['strNombreCiclo'];
            }
        }

        $arrayParametrosCorreo                        = array();
        $arrayParametrosCorreo['strAsunto']           = 'Netlife ha registrado el contrato de tu servicio de Ultra Alta Velocidad.';
        $arrayParametrosCorreo['arrayDestino']        = $arrayContactosCorreosPuntoMail;
        $arrayParametrosCorreo['strAdjunto']          = $strRutaZip;
        $arrayParametrosCorreo['strCodigoPlantilla']  = 'CONTDIGITAL_NEW';
        $arrayParametrosCorreo['arrayParametersBody'] = $arrayParametrosBody;
        $arrayParametrosCorreo['strCPrefijoEmpresa']  = $arrayParametros['intContratoId'];
        $arrayParametrosCorreo['strRemitente']        = 'notificacionesnetlife@netlife.info.ec';

        $this->sendMail($arrayParametrosCorreo);

        return $arrayRutaArchivos;
    }
    
    
    
    /**
     * Guarda las referencias de los documentos en la INFO_DOCUMENTO
     * 
     * @author Veronica Carrasco <vcarrasco@telconet.ec>
     * @version 1.0 07-08-2016
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
            $strClientIp          = ($arrayParametros['strClienteIp']) ? $arrayParametros['strClienteIp'] : '127.0.0.1';
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
        }

    }
    
    
    /**
     * decodificarDocumentos, método que decodifica un archivo en formato base64
     * Se debe considerar que este metodo debe ser invocado de forma controlada
     * para evitar local file inclusion.
     * 
     * @author Veronica Carrasco <vcarrasco@telconet.ec>
     * @version 1.0 07-08-2016
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
     * Método que almacena un archivo en formato base64 en el servidor NFS.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0
     * @since 25/09/2020
     *
     * @param array $arrayParametro[
     *                              'strNombreArchivo'      String  Nombre del archivo.
     *                              'prefijoEmpresa'        String  Prefijo empresa.
     *                              'strApp'                String  Nombre de la aplicación.
     *                              'strArchivoCodificado'  String  Archivo codificado en base64.
     *                              'strUsrCreacion'        String  Usuario de creación
     *                             ]
     * @return array $arrayRespuesta
     */
    private function guardarNfsDocumentos($arrayParametro)
    {
        $arrayParamNfs = array(
                                'prefijoEmpresa'       => $arrayParametro['prefijoEmpresa'],
                                'strApp'               => $arrayParametro['strApp'],
                                'arrayPathAdicional'   => $arrayParametro['arrayPathAdicional'],
                                'strBase64'            => $arrayParametro['strArchivoCodificado'],
                                'strNombreArchivo'     => $arrayParametro['strNombreArchivo'],
                                'strUsrCreacion'       => $arrayParametro['strUsrCreacion'],
                                'strSubModulo'         => 'ContratoDigital');

        $arrayRespNfsPdf = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
        if(isset($arrayRespNfsPdf) && $arrayRespNfsPdf['intStatus'] == 200)
        {
            $arrayRespuesta = array('strNombreDocumento'    => $strNombreArchivo,
                                    'strUrlArchivo'         => $arrayRespNfsPdf['strUrlArchivo']);
        }
        else
        {
            throw new \Exception($arrayRespuesta['strMensaje'].'  -> guardarNfsDocumentos()');
        }
        return $arrayRespuesta;
    }

    /**
     * Metodo que envia un correo electronico el utiliza
     * una plantilla definida en el sistema
     * 
     * @param type array $arrayParametros[
     *                                    strAsunto           => asunto del correo
     *                                    arrayDestino        => cuenta de correo del destinatario
     *                                    strCodigoPlantilla  => codigo de la plantilla del correo
     *                                    arrayParametersBody => Parametros que se utilizaran para cuerpo del correo
     *                                    strCPrefijoEmpresa  => Codigo Empresa
     *                                    strAdjunto          => path de archivo adjunto
     *                                    strRemitente        => cuenta de correo del remitente 
     *                                   ]
     * 
     * @author Veronica Carrasco <vcarrasco@telconet.ec>
     * @version 1.0 07-08-2016
     */
    public function sendMail($arrayParametros)
    {
        $this->serviceEnvioPlantilla->generarEnvioPlantilla($arrayParametros['strAsunto'],
                                                            $arrayParametros['arrayDestino'],
                                                            $arrayParametros['strCodigoPlantilla'],                
                                                            $arrayParametros['arrayParametersBody'],
                                                            $arrayParametros['strCPrefijoEmpresa'],
                                                            '',
                                                            '',
                                                            $arrayParametros['strAdjunto'],
                                                            false,
                                                            $arrayParametros['strRemitente']                
                                                           );
    }
    
    /**
     * Metodo que crea un archivo zip
     * 
     * @param type $zipName
     * @param type $arrayRutaArchivos
     * @return type
     * 
     * @author Veronica Carrasco <vcarrasco@telconet.ec>
     * @version 1.0 07-08-2016
     * 
     */
    public function zipFiles($strZipName , $arrayRutaArchivos)
    {
        $strRutaFisica = $this->strPathTelcos.$this->strContratosDirectorio.$strZipName;
        $objZip        = new \ZipArchive;	
        if ($arrayRutaArchivos && $objZip->open($strRutaFisica, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE)===TRUE) 
        {
            foreach($arrayRutaArchivos as $strRutaArchivo)
            {
                $arrPathFile = explode("/",$strRutaArchivo);
                $objZip->addFile($this->strPathTelcos.$strRutaArchivo,end($arrPathFile));		
            }
            $objZip->close();
        }
        else
        {
            $strRutaFisica = null;
        }
        return $strRutaFisica;
    }
    
    /**
     * Metodo que crea un archivo zip y lo almacena en el servidor NFS.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0
     * @since 28/09/2020
     *
     * @param array $arrayParametros[
     *                                  'strArchivoZip'         String  Nombre del archivo .zip
     *                                  'arrayRutaArchivos'     Array   Arreglo de url a comprimir
     *                                  'prefijoEmpresa'        String  Prefijo de la empresa
     *                                  'arrayPathAdicional'    Array   Arreglo de path adicionales
     *                                  'strApp'                String  Nombre de la app
     *                                  'strUsrCreacion'        String  Usuario de creación
     *                              ]
     */
    public function zipNfsFiles($arrayParametros)
    {
        $arrayRespuesta= array();
        $objZip        = new \ZipArchive;
        $objTmpFile    = tempnam('.', '');
        if ($arrayParametros['arrayRutaArchivos'] && $objZip->open($objTmpFile, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE)===true)
        {
            foreach($arrayParametros['arrayRutaArchivos'] as $strRutaArchivo)
            {
                $objFile = file_get_contents($strRutaArchivo);
                $objZip->addFromString(basename($strRutaArchivo), $objFile);
            }
            $objZip->close();
        }
        $strFile         = base64_encode(file_get_contents($objTmpFile));
        unlink($objTmpFile);
        $arrayParamNfs   = array(
                                'prefijoEmpresa'       => $arrayParametros['prefijoEmpresa'],
                                'strApp'               => $arrayParametros['strApp'],
                                'arrayPathAdicional'   => $arrayParametros['arrayPathAdicional'],
                                'strBase64'            => $strFile,
                                'strNombreArchivo'     => $arrayParametros['strArchivoZip'],
                                'strUsrCreacion'       => $arrayParametros['strUsrCreacion'],
                                'strSubModulo'         => 'ContratoDigital');

        $arrayRespNfsPdf = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
        if(isset($arrayRespNfsPdf) && $arrayRespNfsPdf['intStatus'] == 200)
        {
            $arrayRespuesta = array('strUrlArchivo'  => $arrayRespNfsPdf['strUrlArchivo']);
        }
        else
        {
            throw new \Exception($arrayRespNfsPdf['strMensaje'].'  -> zipNfsFiles()');
        }
        return $arrayRespuesta;
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
     * @author Veronica Carrasco <vcarrasco@telconet.ec>
     * @version 1.0 16-08-2016
     */
    public function getDatosUltimaMilla($objPersonaEmpresaRol)
    {
        $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                         ->findServicioPorEstado($objPersonaEmpresaRol->getId(), 'Factible');
        
        if(is_object($objServicio))
        {
            $objUltimaMilla = $this->serviceServiciotecnico->getUltimaMillaPorServicio($objServicio->getId());
        }
        return $objUltimaMilla;
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
     * @author Veronica Carrasco <vcarrasco@telconet.ec>
     * @version 1.0 16-08-2016
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
     * Funcion que permite convertir un grupo de imagenes en un PDF
     * @param type $strFileURL Ruta del archivo a generarse con extension .PDF
     * @param type $arrayImagenes Arreglo de URLs de las imagenes
     * 
     * @author Veronica Carrasco <vcarrasco@telconet.ec>
     * @version 1.0 16-08-2016
     * 
     */
    public function mergeImagesIntoPDF($strFileURL,$arrayImagenes)
    {
        $strPathParcial  = $this->strContratosDirectorio.$strFileURL.date("YmdHis").".pdf";
        $strPathCompleto = $this->strPathTelcos.$strPathParcial;
        $objPDF = new \Imagick($arrayImagenes);
        $objPDF->setImageFormat('pdf');
        $objPDF->writeImages($strPathCompleto, true); 
        return $strPathParcial;
    }

    /**
     * Método que permite convertir un grupo de imagenes en un PDF y almacenarlo en el NFS
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0
     * @since 23/09/2020
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 - 06/06/2021 - Se crea el archivo de manera local y posterior se envia al servidor NFS.
     *
     * @param array $arrayParametro[
     *                              'strNombreDocumento'    String  Nombre del documento
     *                              'arrayDocumentos'       Array   Arreglo de imagenes.
     *                              'prefijoEmpresa'        String  Prefijo de la empresa.
     *                              'strApp'                String  Nombre de la aplicación.
     *                              'objPerEmpRol'          Object  Objeto persona empresa rol.
     *                              'strUsrCreacion'        String  Usuario de creación.
     *                              ]
     * @return array $arrayRespuesta
    */
    public function mergeNfsImagesIntoPDF($arrayParametro)
    {
        $arrayRespuesta         = array();
        $arrayImagenes          = null;
        $strIdentificacion      = is_object($arrayParametro['objPerEmpRol']->getPersonaId()) ?
                                    $arrayParametro['objPerEmpRol']->getPersonaId()->getIdentificacionCliente()
                                    : 'SIN_IDENTIFICACION';

        $arrayPathAdicional     = null;
        $arrayPathAdicional[]   = array('key' => $strIdentificacion);

        $strNombreArchivo= $arrayParametro['strNombreDocumento'].date("YmdHis").".pdf";
        $strRuta         = $this->strPathTelcos.$this->strContratosDirectorio.$strIdentificacion.'/';
        $strPathParcial  = $this->strContratosDirectorio.$strIdentificacion.'/'.$strNombreArchivo;
        mkdir($strRuta, 0777, true);
        $strPathCompleto = $this->strPathTelcos.$strPathParcial;

        $objArchivo1     = file_get_contents($arrayParametro['arrayDocumentos'][0]);
        $strArchivo1     = $strRuta.basename($arrayParametro['arrayDocumentos'][0]);
        file_put_contents( $strArchivo1,  $objArchivo1);
        $objArchivo2     = file_get_contents($arrayParametro['arrayDocumentos'][1]);
        $strArchivo2     = $strRuta.basename($arrayParametro['arrayDocumentos'][1]);
        file_put_contents( $strArchivo2,  $objArchivo2);

        $arrayImagenes   = array($strArchivo1,$strArchivo2);

        $objPDF          = new \Imagick($arrayImagenes);
        $objPDF->setImageFormat('pdf');

        $objPDF->writeImages($strPathCompleto, true);

        $strArchivoBas   = base64_encode(file_get_contents($strPathCompleto));
        $arrayParamNfs   = array(
                                 'prefijoEmpresa'       => $arrayParametro['prefijoEmpresa'],
                                 'strApp'               => $arrayParametro['strApp'],
                                 'arrayPathAdicional'   => $arrayPathAdicional,
                                 'strBase64'            => $strArchivoBas,
                                 'strNombreArchivo'     => $strNombreArchivo,
                                 'strUsrCreacion'       => $arrayParametro['strUsrCreacion'],
                                 'strSubModulo'         => 'ContratoDigital');

        $arrayRespNfsPdf = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
        if(isset($arrayRespNfsPdf) && $arrayRespNfsPdf['intStatus'] == 200)
        {
            $arrayRespuesta = array('strNombreDocumento'    => $strNombreArchivo,
                                    'strUrlArchivo'         => $arrayRespNfsPdf['strUrlArchivo']);
        }
        else
        {
            throw new \Exception($arrayRespuesta['strMensaje'].'  -> mergeNfsImagesIntoPDF()');
        }
        $this->borrarDirectorio(array('strDirectorio' => $strRuta));
        return $arrayRespuesta;
    }

    /**
     * Método para eliminar un directorio
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0
     * @since 23/09/2020
     * 
     * @param array $arrayParametro[
     *                              'strDirectorio'    String  Directorio
     *                              ]
     * @return array $arrayRespuesta
     */
    private function borrarDirectorio($arrayParametro)
    {
        $strDirectorio  = $arrayParametro['strDirectorio'];
        $objFiles       = scandir($strDirectorio);
        foreach ($objFiles as $objFile)
        {
            if($objFile != '.' && $objFile != '..')
            {
                unlink($strDirectorio.'/'.$objFile);
            }
        }
        rmdir($strDirectorio);
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
        $strFuncionPrecio   = str_replace('"[', '[', $strFuncionPrecio);
        $strFuncionPrecio   = str_replace(']"', ']', $strFuncionPrecio);
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
        
        error_log("funcion " . $strFuncionPrecio);
        eval($strFuncionPrecio);
        return $floatPrecio;
    }

    /**
     * crearDebitoEmp, método que retorna los parámetros para crear el documento de debito del cliente
     *                   con la empresa que ofrece el servicio
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 13-Oct-2017
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
     * @version 1.0 13-Oct-2017
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.1 18-06-2020 - Implementación para persona jurídica
     *
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
                  'v'   => $arrayDatosPersona['tipoCliente'] == 'NAT'
                               ? strtoupper($arrayDatosPersona['nombreCliente'])
                               : strtoupper($arrayDatosPersona['razonSocial'])));
        return $arrayPagare;
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
        $this->emComercial->getConnection()->beginTransaction();
        
        try
        {
            $objContrato = $arrayContrato['contrato'];
            
            $arrayDatosPersona = $this->obtenerDatos($objContrato);
            $strOp             = $this->strUrlSecuridyData;
    
            $arrayData = array('op'                => $strOp,
                               'strCodEmpresa'     => $arrayContrato['strCodEmpresa'],
                               'arrayDatosPersona' => $arrayDatosPersona,
                               'strIp'             => $arrayContrato['strIp'],
                               'strUsuario'        => $arrayContrato['strUsuario'],
                               'bandNfs'           => $arrayContrato['bandNfs'],
                               'strApp'            => $arrayContrato['strApp'],
                               'objContrato'       => $objContrato,
                               'objPerEmpRol'      => $arrayContrato['objPerEmpRol'],
                               'prefijoEmpresa'    => $arrayContrato['prefijoEmpresa']    
                               );
    
            //WIP: Llamar primero al certificado
            
            $arrayResponse = $this->serviceCertificacionDocumentos->llenarCertificado($arrayData);
            
            $arrayRespuesta['salida']   = '0';
            
            if ($arrayResponse['status'] == $this->strStatusOk)
            {
                $arrayRespuesta['mensaje']    = $arrayResponse['mensaje'];
                $arrayRespuesta['salida']     = '1';
                $arrayRespuesta['datos']      = $arrayDatosPersona;
                
                    $this->emComercial->persist($objContrato);
                    $this->emComercial->flush();
                    $this->emComercial->getConnection()->commit();
            }
            else
            {
                $arrayRespuesta['mensaje'] = $arrayResponse['mensaje'];
            }
        }
        catch(\Exception $objException)
        {
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            $arrayRespuesta['salida']   = '0';
            $arrayRespuesta['mensaje']  = 'No se pudo guardar el Certificado';

            $arrayParametrosLog['enterpriseCode']   = $arrayContrato['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $objException->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayContrato, 128);
            $arrayParametrosLog['creationUser']     = $arrayContrato['strUsuario'];

            $this->serviceUtil->insertLog($arrayParametrosLog);
        }

        if ($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->close();
        }
        
        return $arrayRespuesta;
    }

    /**
     * documentarCertificadoNew, método que genera la documentación de un certificado digital
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 27-03-2019
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
     * Se envía el código de la empresa en el array de datos Cliente.
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.1 14/11/2019
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.2 16-11-2019 Se agrega tipo y numero de adendum en arrayDatos
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.3 08-02-2020 Corrección del usuario e ip creador en documentos de adendum
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.4 18-06-2020 - Implementación para persona jurídica
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.5 20-05-2021 - Se agrega el punto que viene en el contrato
     * 
     */
    public function documentarCertificadoNew($objContrato,$arrayDatos)
    {
        try
        {
            $arrayDatosCliente                     = $this->obtenerDatos($objContrato);
            $arrayDatosCliente["codEmpresa"]       = $arrayDatos['codEmpresa'];
            $arrayDatosCliente['strTipo']          = $arrayDatos['strTipo'];
            $arrayDatosCliente['strNumeroAdendum'] = $arrayDatos['strNumeroAdendum'];
            $arrayDatosCliente['contratoId']       = $objContrato->getId();
        
            $arrayDatosCliente["objPunto"]         = $arrayDatos['objPunto']; 
            if(isset($arrayDatos['bandNfs']) && $arrayDatos['bandNfs'])
            {
                $arrayDatosCliente['strExtensionDocumento'] = 'PNG';
            }

            $arrayDatosPersona = $this->obtenerDatosDocumentarCertificado($objContrato, $arrayDatosCliente);

            $arrayContratoEmp  = $this->crearContratoEmp($arrayDatosPersona);
            $arrayContratoSd   = $this->crearContratoSd($arrayDatosPersona);
            $arrayFormularioSd = $this->crearFormularioSD($arrayDatosPersona);

            $strRutaRubricaTMP    = explode('.', $arrayDatosPersona['rutaRubrica'])[0] . '.png';
            $strRutaFotoTMP       = explode('.', $arrayDatosPersona['rutaFoto'])[0] . '.png';
            $strRutaCedulaTMP     = $this->strPathTelcos . explode('.', $arrayDatosPersona['rutaCedula'])[0] . '.png';
            $strRutaCedulaRevTMP  = $this->strPathTelcos . explode('.', $arrayDatosPersona['rutaCedulaR'])[0] . '.png';
            if(isset($arrayDatos['bandNfs']) && $arrayDatos['bandNfs'])
            {
                $strRutaRubricaTMP  = $arrayDatosPersona['rutaRubrica'];
                $strRutaFotoTMP     = $arrayDatosPersona['rutaFoto'];
                $strRutaCedulaTMP   = $arrayDatosPersona['rutaCedula'];
                $strRutaCedulaRevTMP= $arrayDatosPersona['rutaCedulaR'];
                $arrayParamNfs      = array('strNombreDocumento'    => "documento_digital_",
                                            'arrayDocumentos'       => array($strRutaCedulaTMP,$strRutaCedulaRevTMP),
                                            'prefijoEmpresa'        => $arrayDatos['prefijoEmpresa'],
                                            'strApp'                => $arrayDatos['strApp'],
                                            'objPerEmpRol'          => $arrayDatos['objPerEmpRol'],
                                            'strUsrCreacion'        => $arrayDatos['usrCreacion']);
                $arrayRespNfs       = $this->mergeNfsImagesIntoPDF($arrayParamNfs);
            }
            else
            {
                $strRutaCedulaCompTMP  = $this->mergeImagesIntoPDF("documento_digital_", array($strRutaCedulaTMP,$strRutaCedulaRevTMP));
                $arrayPathCedula       = explode('/', $strRutaCedulaCompTMP);
            }

            $objTipoDocumentoGeneral                               =
                $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')->findOneBy(array('codigoTipoDocumento' => "CED"));
            $arrayParametrosDocumento                              = array();
            if($arrayDatos['bandNfs'] && isset($arrayRespNfs))
            {
                $arrayParametrosDocumento['strNombreDocumento']        = $arrayRespNfs['strNombreDocumento'];
                $arrayParametrosDocumento['strUbicacionFisicaDoc']     = $arrayRespNfs['strUrlArchivo'];
                $strRutaCedulaCompTMP                                  = $arrayRespNfs['strUrlArchivo'];
            }
            else
            {
                $arrayParametrosDocumento['strNombreDocumento']        = $arrayPathCedula[count($arrayPathCedula)-1];
                $arrayParametrosDocumento['strUbicacionFisicaDoc']     = $strRutaCedulaCompTMP;
            }
            $arrayParametrosDocumento['strUsrCreacion']            = $arrayDatos['usrCreacion'];
            $arrayParametrosDocumento['strClienteIp']              = $arrayDatos['ip'];
            $arrayParametrosDocumento['strMensaje']                = "Archivo agregado al contrato # " . $objContrato->getNumeroContrato();
            $arrayParametrosDocumento['strCodEmpresa']             = $arrayDatos["codEmpresa"];
            $arrayParametrosDocumento['intTipoDocumentoGeneralId'] = $objTipoDocumentoGeneral->getId();
            $arrayParametrosDocumento['intContratoId']             = $objContrato->getId();
            $arrayParametrosDocumento['strTipoArchivo']            = "PDF";
            $arrayParametrosDocumento['strModulo']                 = 'COMERCIAL';
            $arrayParametrosDocumento['strTipo']                   = $arrayDatosCliente['strTipo'];
            $arrayParametrosDocumento['strNumeroAdendum']          = $arrayDatosCliente['strNumeroAdendum'];

            $this->guardarDocumento($arrayParametrosDocumento);

            if($arrayDatos['bandNfs'] && isset($arrayRespNfs))
            {
                $arrayParamNFSCodf['strUrl']    = $strRutaRubricaTMP;
                $strRubricaBase64               = $this->codificarNfsDocumentos($arrayParamNFSCodf);

                $arrayParamNFSCodf['strUrl']    = $strRutaFotoTMP;
                $strFotoCliente                 = $this->codificarNfsDocumentos($arrayParamNFSCodf);

                $arrayParamNFSCodf['strUrl']    = $strRutaCedulaCompTMP;
                $strFotoCedula1                 = $this->codificarNfsDocumentos($arrayParamNFSCodf);

                $arrayDataParametros            = array('strCodEmpresa'     => $arrayDatos['codEmpresa'],
                                                        'cedula'            => !empty($arrayDatosPersona['cedula'])
                                                                               ? $arrayDatosPersona['cedula'] : $arrayDatosPersona['ruc'],
                                                        'rubrica'           => $strRubricaBase64,
                                                        'documentos'        => array(
                                                                                    'fotoCliente'   => $strFotoCliente,
                                                                                    'fotoCedula1'   => $strFotoCedula1,
                                                                                    'contratoEMP'   => $arrayContratoEmp,
                                                                                    'contratoSD'    => $arrayContratoSd,
                                                                                    'formularioSD'  => $arrayFormularioSd
                                                                                ),
                                                        'strIp'             => $arrayDatos['ip'],
                                                        'strUsuario'        => $arrayDatos['usrCreacion'],
                                                        'bandNfs'           => $arrayDatos['bandNfs'],
                                                        'objPerEmpRol'      => $arrayDatos['objPerEmpRol'],
                                                        'prefijoEmpresa'    => $arrayDatos['prefijoEmpresa'],
                                                        'strApp'            => $arrayDatos['strApp'],
                                                        'strTipo'           => isset($arrayDatosCliente['strTipo']) ? $arrayDatosCliente['strTipo']
                                                                                : null,
                                                        'strNumeroAdendum'  => isset($arrayDatosCliente['strNumeroAdendum']) ?
                                                                                $arrayDatosCliente['strNumeroAdendum'] : null,
                                                        'persona'           => $arrayDatosCliente
                                                        );
            }
            else
            {
                $arrayDataParametros = array('strCodEmpresa' => $arrayDatos['codEmpresa'], 'cedula' => !empty($arrayDatosPersona['cedula'])
                    ? $arrayDatosPersona['cedula'] : $arrayDatosPersona['ruc'], 'rubrica' => $this->codificarDocumentos($strRutaRubricaTMP),
                                            'documentos' => array('fotoCliente'  => $this->codificarDocumentos($strRutaFotoTMP),
                                                                'fotoCedula1'  => $this->codificarDocumentos($strRutaCedulaCompTMP),
                                                                'contratoEMP'  => $arrayContratoEmp, 'contratoSD' => $arrayContratoSd,
                                                                'formularioSD' => $arrayFormularioSd), 'strIp' => $arrayDatos['ip'],
                                            'strUsuario' => $arrayDatos['usrCreacion'], 'persona' => $arrayDatosCliente);
            }
            $arrayRespuesta = $this->serviceCertificacionDocumentos->documentarCertificado($arrayDataParametros);
        } 
        catch(\Exception $objException) 
        {
            $arrayParametrosLog['enterpriseCode']   = $arrayDatos['codEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $objException->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayDatos, 128);
            $arrayParametrosLog['creationUser']     = $arrayData['usrCreacion'];
            $this->serviceUtil->insertLog($arrayParametrosLog);
        }

        if(!isset($arrayRespuesta))
        {
            $arrayRespuesta['status']  = $this->strStatusError;
            $arrayRespuesta['mensaje'] = 'Error, no hay respuesta del service para documentar el certificado';
        }
        return $arrayRespuesta;
    }
    
   /**
     * Método que me genera la data para consumir web service que firma digitalmente un archivo
     *
     * @param array $arrayDataRequest - parametros con datos para firma de documento
     *
     * @return $arrayRetorno retorna un arreglo con la siguiente información:
     *         $arrayRetorno['salida'] = 0 => Error en la firma del contrato
     *         $arrayRetorno['salida'] = 1 => Documentos firmados
     *         $arrayRetorno['mensaje']    => Un mensaje de éxito o error               
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 17-04-2019
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.1 03-08-2019 Se parametriza números de identificación usados al obtener certificado digital.
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.1 03-08-2019 Se modifica método para abarcar adendums.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.2 21-04-2020 Se modific la extensión del certificado para que coja desde un parameter
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.3 23-04-2020 Se nuevo método para obtener datos del adendum de servicio, mejora en el control de
     *                         errores al enviar a firmar documento.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.4 18-06-2020 - Implementación para persona jurídica
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.5 23-11-2020 -Se lee el certificado desde la estructura infoCertificadoDocumento.
     *
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.6 18-12-2020 -Se agrega el objeto info_punto al arrayDatos.
     */
    public function firmarDocumentosNew($arrayDataRequest)
    {
        $intIdContrato                    = $arrayDataRequest['contrato'];
        $arrayRetorno                     = array();
        $arrayRetorno['salida']           = 1;
        $strOpcionFuncion                 = $this->strOpcionFirmarCertificadoDigital;
        $booleanAsync                     = false;
        $arrayEmpresaPeticion['code']     = $this->strLoginCertificadoDigital;
        $arrayEmpresaPeticion['password'] = $this->strPassCertificadoDigital;
        $intIdPunto                       = $arrayDataRequest['puntoId'];
        
        try
        {

            if($intIdContrato)
            {

                $objContrato = $this->emComercial->getRepository('schemaBundle:InfoContrato')
                                   ->findOneBy(array("id"     => $intIdContrato,
                                                     "estado" => $arrayDataRequest['strTipo'] == "C" ? "PorAutorizar" : "Activo"));

            } else
            {
                $arrayRetorno['mensaje'] = "Contrato no cuenta con un documento id valido";
                $arrayRetorno['salida']  = 0;

                return $arrayRetorno;
            }
            if(is_object($objContrato))
            {
                $arrayDatos                     = $this->obtenerDatos($objContrato);
                $arrayDatos["codEmpresa"]       = $arrayDataRequest["codEmpresa"];
                $arrayDatos["strTipo"]          = $arrayDataRequest["strTipo"];
                $arrayDatos["strNumeroAdendum"] = $arrayDataRequest["numeroAdendum"];
                $arrayDatos["intContratoId"]    = $objContrato->getId();
                $arrayDatos["objPunto"]         = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                                                                    ->find($intIdPunto); 
                $arrayDatos["servicios"]        = $arrayDataRequest["servicios"];                                                      

                $arrayDatosPersona             = $this->obtenerDatosDocumentarCertificado($objContrato, $arrayDatos);
                $arrayDatosPersona['pinCode']  = $arrayDataRequest['pincode'];
                $arrayContratoEmpresa          = $this->crearContratoEmp($arrayDatosPersona);
                $arrayContratoEntidadEmisora   = $this->crearContratoSd($arrayDatosPersona);
                $arrayFormularioEntidadEmisora = $this->crearFormularioSD($arrayDatosPersona);
                $arrayDebito                   = $this->crearDebitoEmp($arrayDatosPersona);
                $arrayPagare                   = $this->crearPagareEmp($arrayDatosPersona);

                if($arrayDataRequest['strTipo'] == "AS")
                {
                    $arrayAdendumAdicional = $this->crearAdendumAdicional($arrayDatosPersona);
                }

                $strRutaRubricaTMP = explode('.', $arrayDatosPersona['rutaRubrica'])[0] . '.png';

                $arrayValorCertificado = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('CERTIFICADO_DIGITAL', 'COMERCIAL', '', 'CERTIFICADO_SD', '', '', '', '', '', '18');
                if(isset($arrayValorCertificado['valor1']))
                {
                    $strCertSd = $arrayValorCertificado['valor1'];
                }

                $arrayValorCertMd = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('CERTIFICADO_DIGITAL', 'COMERCIAL', '', 'CERTIFICADO_MD', '', '', '', '', '', '18');
                if(isset($arrayValorCertMd['valor1']))
                {
                    $strCertMd = $arrayValorCertMd['valor1'];
                }

                $arrayParametroConsumo = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('CONFIGURACION_WS_SD', 'COMERCIAL', '', ($arrayDatos['strTipoTributario'] == 'NAT') ? 'PARAMSQUERY' : 'PARAMSQUERYJUR',
                        '', '', '', '', '', '18');

                $arrayCertificado   = $this->emFirmaElect->getRepository('schemaBundle:InfoCertificado')
                    ->findCertificado(array("strNumCedula" => ($arrayDatos['strTipoTributario'] == 'NAT') ? $arrayDatosPersona['cedula']
                        : $arrayDatos['representanteLegal']['identificacion'], "strCodEmpresa" => $arrayDataRequest['codEmpresa'],
                                            "strEstado" => "valido"));
                $arrayCertificadoSd = $this->emFirmaElect->getRepository('schemaBundle:InfoCertificado')
                                          ->findCertificado(array("strNumCedula"  => $strCertSd,
                                                                  "strCodEmpresa" => $arrayDataRequest['codEmpresa'],
                                                                  "strEstado"     => "valido"));

                $arrayCertificadoMd = $this->emFirmaElect->getRepository('schemaBundle:InfoCertificado')
                                          ->findCertificado(array("strNumCedula"  => $strCertMd,
                                                                  "strCodEmpresa" => $arrayDataRequest['codEmpresa'],
                                                                  "strEstado"     => "valido"));
                if($arrayCertificado < 1)
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

                if(isset($arrayDataRequest['bandNfs']) && $arrayDataRequest['bandNfs'])
                {

                    $objDocumentos     = $this->emFirmaElect->getRepository('schemaBundle:InfoCertificadoDocumento')
                                                           ->findOneBy(array("certificadoId"    => $arrayCertificado[0]['id'],
                                                                             "tipoDocumento"    => 'certificadoDigital')); 
                    if(is_object($objDocumentos))
                    {
                        $strCertificado = $objDocumentos->getSrc();
                    }
                    else
                    {
                        throw new \Exception('No se ha encontrado el certificado digital, comunicarse con sistemas');
                    }
                }
                else
                {
                    $strCertificado = $this->strPathTelcos . $this->strRutaCertificado . $strFecha2 . "/" .
                                        (($arrayDatos['strTipoTributario'] == 'NAT') ? $arrayDatosPersona['cedula']
                                            : $arrayDatos['representanteLegal']['identificacion']) . '_' . $strSerial . $strExtension;
                }
            
                $strCertificado64 = base64_encode(file_get_contents($strCertificado));

                $arrayCertificadoContratoSd =
                    $this->generaPropiedadesPlantilla(array("strPlantilla"       => "contratoSecurityData",
                                                            "arrayCertificado"   => $arrayCertificado[0],
                                                            "arrayCertificadoSd" => $arrayCertificadoSd[0], "strCertificado64" => $strCertificado64));

                $arrayCertificadoFormulario =
                    $this->generaPropiedadesPlantilla(array("strPlantilla"       => "formularioSecurityData",
                                                            "arrayCertificado"   => $arrayCertificado[0],
                                                            "arrayCertificadoSd" => $arrayCertificadoSd[0], "strCertificado64" => $strCertificado64));

                $arrayCertificadoDebito =
                    $this->generaPropiedadesPlantilla(array("strPlantilla"       => "debitoMegadatos", "arrayCertificado" => $arrayCertificado[0],
                                                            "arrayCertificadoSd" => $arrayCertificadoSd[0], "strCertificado64" => $strCertificado64));

                $arrayCertificadoPagare =
                    $this->generaPropiedadesPlantilla(array("strPlantilla"       => "pagareMegadatos", "arrayCertificado" => $arrayCertificado[0],
                                                            "arrayCertificadoSd" => $arrayCertificadoSd[0], "strCertificado64" => $strCertificado64));

                if($arrayDataRequest['strTipo'] == "AS")
                {
                    $arrayCertificadoContratoEmpresa = $this->generaPropiedadesPlantilla(array("strPlantilla"       => "adendumMegaDatos",
                                                                                               "arrayCertificado"   => $arrayCertificado[0],
                                                                                               "arrayCertificadoSd" => $arrayCertificadoMd[0],
                                                                                               "strCertificado64"   => $strCertificado64));

                    $arrayData = array('async'   => $booleanAsync, 
                                       'cedula'  => ($arrayDatos['strTipoTributario'] == 'NAT' 
                                                        ? $arrayDatosPersona['cedula'] 
                                                        : $arrayDatos['strRuc']), 
                                       'rubrica' => $this->codificarDocumentos($strRutaRubricaTMP),
                                       'list'    => array(
                                           $this->crearPlantillaDocumentoNew(array("arrayValores"       => $arrayAdendumAdicional,
                                                                                   "strNombrePlantilla" => "adendumMegaDatos",
                                                                                   "arrayCertificado"   => $arrayCertificadoContratoEmpresa)),
                                           $this->crearPlantillaDocumentoNew(array("arrayValores"       => $arrayContratoEntidadEmisora,
                                                                                   "strNombrePlantilla" => "contratoSecurityData",
                                                                                   "arrayCertificado"   => $arrayCertificadoContratoSd)),
                                           $this->crearPlantillaDocumentoNew(array("arrayValores"       => $arrayFormularioEntidadEmisora,
                                                                                   "strNombrePlantilla" => "formularioSecurityData",
                                                                                   "arrayCertificado"   => $arrayCertificadoFormulario)),
                                           $this->crearPlantillaDocumentoNew(array("arrayValores"       => $arrayPagare,
                                                                                   "strNombrePlantilla" => "pagareMegadatos",
                                                                                   "arrayCertificado"   => $arrayCertificadoPagare))
                                       ));
                } 
                else
                {

                    $arrayCertificadoContratoEmpresa =
                        $this->generaPropiedadesPlantilla(array("strPlantilla"       => "contratoMegadatos",
                                                                "arrayCertificado"   => $arrayCertificado[0],
                                                                "arrayCertificadoSd" => $arrayCertificadoMd[0],
                                                                "strCertificado64"   => $strCertificado64));


                    $arrayData = array('async'  => $booleanAsync,
                                       'cedula' => ($arrayDatos['strTipoTributario'] == 'NAT' 
                                                       ? $arrayDatosPersona['cedula'] 
                                                       : $arrayDatos['strRuc']),
                                       'rubrica' => $this->codificarDocumentos($strRutaRubricaTMP),
                                       'list'    => array(
                                           $this->crearPlantillaDocumentoNew(array("arrayValores"       => $arrayContratoEmpresa,
                                                                                   "strNombrePlantilla" => "contratoMegadatos",
                                                                                   "arrayCertificado"   => $arrayCertificadoContratoEmpresa)),
                                           $this->crearPlantillaDocumentoNew(array("arrayValores"       => $arrayContratoEntidadEmisora,
                                                                                   "strNombrePlantilla" => "contratoSecurityData",
                                                                                   "arrayCertificado"   => $arrayCertificadoContratoSd)),
                                           $this->crearPlantillaDocumentoNew(array("arrayValores"       => $arrayFormularioEntidadEmisora,
                                                                                   "strNombrePlantilla" => "formularioSecurityData",
                                                                                   "arrayCertificado"   => $arrayCertificadoFormulario)),
                                           $this->crearPlantillaDocumentoNew(array("arrayValores"       => $arrayDebito,
                                                                                   "strNombrePlantilla" => "debitoMegadatos",
                                                                                   "arrayCertificado"   => $arrayCertificadoDebito)),
                                           $this->crearPlantillaDocumentoNew(array("arrayValores"       => $arrayPagare,
                                                                                   "strNombrePlantilla" => "pagareMegadatos",
                                                                                   "arrayCertificado"   => $arrayCertificadoPagare))));

                }
                
                $arrayDataFirmaDocumentos   = json_encode(array('op' => $strOpcionFuncion, 'data' => $arrayData));
                $arrayRest                  = array();
                $arrayRest[CURLOPT_TIMEOUT] = $this->intWsContratoDigitalTimeOut;

                $arrayResponseWS = $this->serviceRestClient->postJSON($this->strWsFirmaDigital, $arrayDataFirmaDocumentos, $arrayRest);

                $arrayRetorno['salida']  = 0;
                $arrayRetorno['mensaje'] = "No fue posible firmar documentos, se presentó un error inesperado";

                if($arrayResponseWS['status'] == $this->strStatusOk)
                {
                    $arrayStatus          = (array)json_decode($arrayResponseWS['result']);
                    $arrayStatus          = (array)json_decode($arrayStatus[0]);
                    $arrayEnviaDocumentos = $arrayStatus['enviaMails'];

                    if($arrayStatus['cod'] == $this->strStatusOk && empty($arrayStatus['err']))
                    {
                        $this->emComercial->getConnection()->beginTransaction();

                        try
                        {
                            if($arrayDataRequest['strTipo'] == "C")
                            {
                                $objContrato->setEstado('Pendiente');
                                $this->emComercial->persist($objContrato);
                                $this->emComercial->flush();
                            }

                            $this->emComercial->getConnection()->commit();

                            $arrayRetorno['salida']      = 1;
                            $arrayRetorno['arrDatos']    = $arrayDatos;
                            $arrayRetorno['arrInfo']     = $arrayDatosPersona;
                            $arrayRetorno['objContrato'] = $objContrato;
                            $arrayRetorno['documentos']  = $arrayStatus['documentos'];
                            $arrayRetorno['mensaje']     = $arrayStatus['resp'];
                            $arrayRetorno['enviaMails']  = $arrayEnviaDocumentos;
                        } catch(\Exception $e)
                        {
                            if($this->emComercial->getConnection()->isTransactionActive())
                            {
                                $this->emComercial->getConnection()->rollback();
                                $this->emComercial->getConnection()->close();
                            }

                            $arrayRetorno['mensaje']                = "Error en proceso de firmar documentos, no se pudo cambiar estado al contrato";
                            $arrayParametrosLog                     = array();
                            $arrayParametrosLog['enterpriseCode']   = $arrayDataRequest["codEmpresa"];
                            $arrayParametrosLog['logType']          = "1";
                            $arrayParametrosLog['logOrigin']        = "TELCOS";
                            $arrayParametrosLog['application']      = basename(__FILE__);
                            $arrayParametrosLog['appClass']         = basename(__CLASS__);
                            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                            $arrayParametrosLog['status']           = "Fallido";
                            $arrayParametrosLog['messageUser']      = $arrayRetorno['mensaje'];
                            $arrayParametrosLog['descriptionError'] = $e->getMessage();
                            $arrayParametrosLog['inParameters']     = json_encode($arrayDataRequest);
                            $arrayParametrosLog['creationUser']     = $arrayDataRequest['user'];

                            $this->serviceUtil->insertLog($arrayParametrosLog);
                        }
                    } else
                    {
                        if($arrayStatus['cod'] == 412)
                        {
                            $arrayRetorno['mensaje'] = $arrayStatus['err'];
                        }

                        $arrayParametrosLog                     = array();
                        $arrayParametrosLog['enterpriseCode']   = $arrayDataRequest["codEmpresa"];
                        $arrayParametrosLog['logType']          = "1";
                        $arrayParametrosLog['logOrigin']        = "ms-core-com-firma-docs";
                        $arrayParametrosLog['application']      = $arrayStatus['origin'];
                        $arrayParametrosLog['appClass']         = $arrayStatus['className'];
                        $arrayParametrosLog['appMethod']        = $arrayStatus['methodName'];
                        $arrayParametrosLog['appAction']        = $arrayStatus['methodName'];
                        $arrayParametrosLog['status']           = "Fallido";
                        $arrayParametrosLog['messageUser']      = $arrayRetorno['mensaje'];
                        $arrayParametrosLog['descriptionError'] = $arrayStatus['err'];
                        $arrayParametrosLog['inParameters']     = json_encode($arrayDataFirmaDocumentos);
                        $arrayParametrosLog['creationUser']     = $arrayDataRequest['user'];

                        $this->serviceUtil->insertLog($arrayParametrosLog);
                    }

                    if($this->emComercial->getConnection()->isTransactionActive())
                    {
                        $this->emComercial->getConnection()->close();
                    }
                } else
                {
                    $arrayParametrosLog                     = array();
                    $arrayParametrosLog['enterpriseCode']   = $arrayDataRequest["codEmpresa"];
                    $arrayParametrosLog['logType']          = "1";
                    $arrayParametrosLog['logOrigin']        = "TELCOS";
                    $arrayParametrosLog['application']      = basename(__FILE__);
                    $arrayParametrosLog['appClass']         = basename(__CLASS__);
                    $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                    $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                    $arrayParametrosLog['status']           = "Fallido";
                    $arrayParametrosLog['messageUser']      = $arrayRetorno['mensaje'];
                    $arrayParametrosLog['descriptionError'] =
                        "Error al enviar petición a firmar documentos (ms-core-com-firma-docs)" . "\n\n" . json_encode($arrayResponseWS);
                    $arrayParametrosLog['inParameters']     = json_encode($arrayDataFirmaDocumentos);
                    $arrayParametrosLog['creationUser']     = $arrayDataRequest['user'];

                    $this->serviceUtil->insertLog($arrayParametrosLog);
                }
            } 
            else
            {
                $arrayRetorno['mensaje'] = "No se ha encontrado un contrato por autorizar o activo (para adendum)";
                $arrayRetorno['salida']  = 0;
            }
        }
        catch(\Exception $objException)
        {
            $arrayParametrosLog['enterpriseCode']   = $arrayDataRequest["codEmpresa"];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $objException->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayDataRequest, 128);
            $arrayParametrosLog['creationUser']     = $arrayDataRequest['user'];
            $this->serviceUtil->insertLog($arrayParametrosLog);
            
            throw $objException;
        }
        
        return $arrayRetorno;
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
     * @version 1.0 21-05-2019
     *
     * @author  Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.1 11-04-2020 Generación de lista html de servicios adicionales a ser enviado para la
     *                         creación del documento digital adendum.
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
                                                <td class="line-height textCenter">$!' . $arrayProdParametro['valor2'] . 'SubTotal</td>
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
     * Genera las propiedades necesarias sobra la ubicación de la firma en el documento
     * @param type $arrayParametros [strPlantilla, strCertificado64, arrayCertificado]
     * @return type $arrayCertificadoContratoEmpresa [cerftificadoPfx, plantilla, enviaMail, certificado, propiedades, codigo, firmas] 
     * 
     * @author  Edgar Pin Villavicencio <epin@telconet.ec>          
     * @version 1.0 21-05-2019
     *
     * @author  Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.1 12-04-2020 Validación adicional al consultar plantillas
     *
     * @author  Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.2 21-11-2020 - Validación de la firma digital.
     * 
     * @author  Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.3 26-04-2021 - Se consulta el archivo digital de MD y SD desde el nfs
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
        $arrayValorParametros =  $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                 ->getOne('PARAMETROS_TM_COMERCIAL',
                                          'COMERCIAL',
                                            '',
                                            'LEER_CERTIFICADO_NFS',
                                            '',
                                            '',
                                            '',
                                            '',
                                            '',
                                            '');

        if ($arrayValorParametros &&$arrayValorParametros['valor1'] == "S")
        {
            $arrayDataBusqueda = array();
            $arrayDataBusqueda["data"] = array();
            $arrayDataBusqueda["op"] = "buscarArchivo";
            $arrayDataBusqueda["user"] = "epin";
    
            $objAdmiParametroCabPlan  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                        ->findOneBy( array('nombreParametro' => 'CERTIFICADO_DIGITAL', 
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
                
                        if ($objAdmiParametroDet->getValor1() == $arrayParametros['arrayCertificadoSd']['numCedula'])
                        {
                            $arrayFeBusca = array("inicio" => $objAdmiParametroDet->getValor4(),
                                                  "fin"    => $objAdmiParametroDet->getValor4());
                            $arrayData = array('codigoApp'     => $objAdmiParametroDet->getValor2(),
                                               'codigoPath'    => $objAdmiParametroDet->getValor3(),
                                               'fecha'         => $arrayFeBusca,
                                               'pathAdicional' => array(array("key" => $objAdmiParametroDet->getValor5())));

                            $arrayDataBusqueda['data'] = array($arrayData);
                            break;
                        }
    
                    }
                }   
            }
            
    
            $arrayResponseWs = $this->serviceUtil->buscarArchivosNfs( 
                                   $arrayDataBusqueda);     
            if ($arrayResponseWs && $arrayResponseWs['intStatus'] == "200")
            {
                foreach($arrayResponseWs['arrayDatosArchivos'] as $arrayArchivo)
                {
                    if ($arrayArchivo["nombreFile"] == $arrayParametros['arrayCertificadoSd']['numCedula'] .  ".pfx")
                    {
                        $strCertificado = $arrayArchivo["pathFile"]; 
                        break;   
                    }
                }    
            }
            else
            {
                throw new \Exception("No fue posible obtener el certificado " . $arrayParametros['arrayCertificadoSd']['numCedula'] .  ".pfx" , 1);
            }                           
        }
        else
        {
            $strCertificado = $this->strPathTelcos . $this->strRutaCertificado . 
            $arrayParametros['arrayCertificadoSd']['numCedula'] .  ".pfx";
        }
                                                                  

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
    
    
     /*
     * Retorna una cadena truncada según la cantidad de caracteres que se desee obtener.
     * llenar una plantilla para los documentos necesarios al firmar un contrato
     * @param $strCadena - recibe una cadena para truncar según la cantidad de caracteres.
     * @param $intLimite - límite que se desea dejar en ola cadena.
     * @param $strCortar - en que caracter se desea dejar el corte.
     * @param $strCaracter - concatenar caracter como por ejemplo (...)
     * @return $strCadena
     * 
     * @author Katherine Yager <kyager@telconet.ec>          
     * @version 1.0 28-10-2019
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
     * Método que retorna los servicios ademdum
     *
     * @version 1.0 Initial
     *
     * @author Walther Joao Gaibor <mailto:wgaibor@telconet.ec>
     * @version 1.1 - 03/04/2020 - Al momento de obtener las caracteristicas del plan no se estaba enviando correctamente el detalle del plan id,
     *                             se coloca un throw exception en el caso de caerse por alguna excepción.
     */
    public function retornaServiciosAdendum($arrayParametros)
    {
        $strCodEmpresa            = $arrayParametros['intCodEmpresa'];  
        $objPunto                 = $arrayParametros['objPunto'];

        $strTipo                  = $arrayParametros['strTipo'];
        $strNumeroAdendum         = $arrayParametros['strNumeroAdendum'];
        $intContratoId            = $arrayParametros['contratoId'];
        $arrayServ                = $arrayParametros['servicios'];            
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
                                                            "numero" => $strNumeroAdendum,
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

                $arrayServicios['registros'] = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                    ->findBy(array("id"     => $arrayServAdendum));
    
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
            $arrayServiciosContratados = array();
            
            $arrayProdParametros = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                ->get("PRODUCTOS_TM_COMERCIAL", "COMERCIAL", "", "", "", "", "", "", "", $strCodEmpresa);
            
            if(!empty($arrayProdParametros))
            {
                $arrayNuevoProdParametros = array();
    
                foreach ($arrayProdParametros as $intKey => $arrayProdParametro)
                {
                    $arrayNuevoProdParametros[$arrayProdParametro['valor1']] = $arrayProdParametros[$intKey];
                    $arrayServiciosContratados[$arrayProdParametro['valor2']] = array('precio' => '', 'cantidad' => '', 
                                                                                      'instalacion' => '', 'subtotal' => '');
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
                        
                                 $arrayParametrosIns = array(
                                    'intIdPunto'               => $objPunto->getId(),
                                    'intIdServicio'            => $objValue->getId(),
                                    'strCodigoGrupoPromocion'  => $strPromIns,
                                    'intCodEmpresa'            => $strCodEmpresa
                                    ); 
                    
                                 $arrayParametrosMens = array(
                                    'intIdPunto'               => $objPunto->getId(),
                                    'intIdServicio'            => $objValue->getId(),
                                    'strCodigoGrupoPromocion'  => $strPromMens,
                                    'intCodEmpresa'            => $strCodEmpresa
                                    ); 
    
                                $arrayContratoPromoIns[]  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->getPromocionesContrato($arrayParametrosIns);
    
    
                                $arrayContratoPromoMens[]  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->getPromocionesContrato($arrayParametrosMens);
    
    
    
                                $strNombrePlan= strtoupper($objValue->getPlanId()->getNombrePlan()." ");
                                
                                $arrayServicio["nombrePlan"] = $strNombrePlan;
                                $strObservaInstalacion       = $arrayContratoPromoIns[0]['strObservacion'];
                                $strObservaMens              = $arrayContratoPromoMens[0]['strObservacion'];
                                $strAplicaCondiciones        = 'Aplica Condiciones';
                                $strObservaMens              = $this->truncarPalabrasObservacion($strObservaMens,440,' ','');
                                $strObservacionContrato      = "{$strNombrePlan}<br>{$strObservaInstalacion}<br>"
                                                             . "{$strObservaMens}<br> {$strAplicaCondiciones}";
    
                                $strPeriodosMens        = $arrayContratoPromoMens[0]['intCantPeriodo'];
    
                                $strDescuentoMens       = $arrayContratoPromoMens[0]['intDescuento'];
    
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
                        
                        $floatPrecio      = round($this->evaluarFuncionPrecio($objProducto->getFuncionPrecio(), $arrayProductoCaracteristica),2);

    
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
                        
                        if ($objValue->getFrecuenciaProducto() == 0 && $strTipo == 'AS')
                        {
                            $arrayServiciosContratados[$strNombreTecnico]["instalacion"]   = 
                            $arrayServiciosContratados[$strNombreTecnico]["instalacion"]
                            +$floatPrecio;
                        } 
                        else
                        {
                            $arrayServiciosContratados[$strNombreTecnico]["precio"]   = $arrayServiciosContratados[$strNombreTecnico]["precio"]
                            +$floatPrecio;
                        }
                        $arrayServiciosContratados[$strNombreTecnico]["cantidad"] = 
                        $arrayServiciosContratados[$strNombreTecnico]["cantidad"] + $objValue->getCantidad();
                        $arrayServiciosContratados[$strNombreTecnico]["subtotal"] = 
                        $arrayServiciosContratados[$strNombreTecnico]["subtotal"] + ($floatPrecio * $objValue->getCantidad()) ;
                        $floatSubtotal = $floatSubtotal + ($floatPrecio * $objValue->getCantidad()); 
                                                         
                        $arrayServicio["impuestos"] = $arrayServicio["impuestos"]+(($floatPrecio * $objValue->getCantidad()) * $intImpuesto/100);
    
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
     * Reversa un adendum que no se haya podido autorizar
     * @param arrayParametros['strTipo'   => tipo de Adendum AP o AS
     *                        'strNumero' => numero del adendum
     *                            ]
     * @return 
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>          
     * @version 1.0 06-05-2020
     * @date    
     */

    public function reversarAdendum($arrayParametros)
    {
        $strTipo   = $arrayParametros['strTipo'];
        $strNumero = $arrayParametros['strNumero'];
        $this->emComercial->getConnection()->beginTransaction();                       
        $this->emComunicacion->getConnection()->beginTransaction();                       
        try
        {
            $objAdendums = $this->emComercial->getRepository('schemaBundle:InfoAdendum')
                                             ->findBy(array("tipo"   => $strTipo,
                                                            "numero" => $strNumero));
            if ($objAdendums)
            {
                foreach ($objAdendums as $entityAdendum)
                {
                    $entityAdendum->setTipo(null);
                    $entityAdendum->setNumero(null);
                    $entityAdendum->setContratoId(null);
                    $entityAdendum->setEstado("Pendiente");
                    $this->emComercial->persist($entityAdendum);
                }
                $this->emComercial->flush();
            }    
            
            $objImagenes = $this->emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')
                                                 ->findBy(array("numeroAdendum" => $strNumero));
            if ($objImagenes)
            {
                foreach ($objImagenes as $entityImagen)
                {
                    $entityImagen->setNumeroAdendum(null);
                    $entityImagen->setEstado('Eliminado');
                    $this->emComunicacion->persist($entityImagen);
                }
                $this->emComunicacion->flush();
            }    
                                      
            $this->emComercial->getConnection()->commit();
            $this->emComunicacion->getConnection()->commit();
        }
        catch (\Exception $ex)
        {
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
            }
            $arrayParametrosLog['enterpriseCode']   = $arrayContrato['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = "InfoContratoDigitalService";
            $arrayParametrosLog['appClass']         = "InfoContratoDigitalService";
            $arrayParametrosLog['appMethod']        = "reversarAdendum";
            $arrayParametrosLog['appAction']        = "reversarAdendum";
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $ex->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayParametros);
            $arrayParametrosLog['creationUser']     = "TELCOS_MOVIL";

            $this->serviceUtil->insertLog($arrayParametrosLog);

            throw $ex;
        }
    }

    /**
     * Método que realiza la transferencia de documentos digitales a un servidor remoto
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 16-07-2020
     *
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.1 24-09-2020 Se cambia parámetro serverName para el envio de documentos.
     *
     * @version 1.2 25-09-2020 Se obtiene el nombre completo de la carpeta donde se almacena los documentos digitales
     *                          desde el array $arrayParametros.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.3 20-11-2020 Si el archivo esta almacenado en el NFS se debe resolver dicho archivo.
     * @return array
     */
    public function transferirDocumentosContratoDigital($arrayParametros)
    {
        $arrayResponse = array('mensaje' => 'ERROR', 'status' => $this->strStatusError);
        
        $this->emComunicacion->getConnection()->beginTransaction();
        $this->emGeneral->getConnection()->beginTransaction();

        try
        {
            $arrayRespuestaDoc = $this->emComunicacion->getRepository('schemaBundle:InfoDocumento')
                ->getDocumentosDigitalesATransferir(array('intIdContrato'    => $arrayParametros['intIdContrato'],
                                                          'strNumeroAdendum' => $arrayParametros['strNumeroAdendum']));

            if(is_array($arrayRespuestaDoc['resultado']) && count($arrayRespuestaDoc['resultado']) > 0)
            {

                $arrayParamsOpSubida = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('CONFIGURACION_WS_SD', 'COMERCIAL', '', 'OPERACION_SUBIDA_DOCUMENTOS', '', '', '', '', '', '18');

                $arrayParamsUrlSubida = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('CONFIGURACION_WS_SD', 'COMERCIAL', '', 'RUTA_SUBIDA_DOCUMENTOS', '', '', '', '', '', '18');

                $arrayParamsServerName = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('CONFIGURACION_WS_SD', 'COMERCIAL', '', 'MEDIO_DESCARGA_CERTIFICADO', '', '', '', '', '', '18');

                $arraySourcePathList = array();
                
                foreach($arrayRespuestaDoc['resultado'] as $arrayDoc)
                {
                    if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'])
                    {
                        array_push($arraySourcePathList,
                            array('filename' => $arrayDoc['nombreDocumento'],
                                  'content'  => base64_encode(file_get_contents($arrayDoc['ubiacionFisicaDocumento']))));
                    }
                    else if(file_exists($this->strPathTelcos . $arrayDoc['ubiacionFisicaDocumento']))
                    {
                        array_push($arraySourcePathList,
                            array('filename' => $arrayDoc['nombreDocumento'],
                                'content' => base64_encode(file_get_contents($this->strPathTelcos . $arrayDoc['ubiacionFisicaDocumento']))));
                    }
                    else
                    {
                        $arrayParametrosLog                     = array();
                        $arrayParametrosLog['enterpriseCode']   = $arrayParametros['strCodEmpresa'];
                        $arrayParametrosLog['logType']          = "1";
                        $arrayParametrosLog['logOrigin']        = "TELCOS";
                        $arrayParametrosLog['application']      = basename(__FILE__);
                        $arrayParametrosLog['appClass']         = basename(__CLASS__);
                        $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                        $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                        $arrayParametrosLog['status']           = "Fallido";
                        $arrayParametrosLog['descriptionError'] = "No se ha encontrado el archivo a transferir (" .
                            $arrayDoc['ubiacionFisicaDocumento'] .")" ;
                        $arrayParametrosLog['inParameters']     = json_encode($arrayParametros, 128);
                        $arrayParametrosLog['creationUser']     = $arrayParametros['strUsrCreacion'];
                        $this->serviceUtil->insertLog($arrayParametrosLog);
                    }
                }

                $arrayDataTransferir = array('fileList'   => array(array('fileSourcePathList'  => $arraySourcePathList,
                                                                         'fileDestinationPath' => $arrayParamsUrlSubida['valor1'] .
                                                                                                  $arrayParametros['strNombreCarpeta'])),
                                             'serverName' => $arrayParamsServerName['valor3'],
                                             'op'         => $arrayParamsOpSubida['valor1']);
                
                $arrayRestConfig = array(CURLOPT_TIMEOUT => $this->intWsContratoDigitalTimeOut);

                $arrayResponseWS = $this->serviceRestClient->postJSON($this->strWsContratoDigitalSftpProcesar, 
                                       json_encode($arrayDataTransferir), $arrayRestConfig);

                if($arrayResponseWS['status'] == $this->strStatusOk)
                {
                    $arrayResponseWS = (array) json_decode($arrayResponseWS['result']);
                    $arrayResponseWS = (array) json_decode($arrayResponseWS[0]);

                    if ($arrayResponseWS['cod'] == $this->strStatusOk && empty($arrayResponseWS['err']))
                    {
                        $arrayResponse['mensaje'] = 'OK';
                        $arrayResponse['Status']   = $this->strStatusOk;
                    }
                    else
                    {
                        $arrayParametrosLog = array();
                        $arrayParametrosLog['enterpriseCode']   = $arrayParametros['strCodEmpresa'];
                        $arrayParametrosLog['logType']          = "1";
                        $arrayParametrosLog['logOrigin']        = "ms-comp-contrato-digital";
                        $arrayParametrosLog['application']      = $arrayResponseWS['origin'];
                        $arrayParametrosLog['appClass']         = $arrayResponseWS['className'];
                        $arrayParametrosLog['appMethod']        = $arrayResponseWS['methodName'];
                        $arrayParametrosLog['appAction']        = $arrayResponseWS['methodName'];
                        $arrayParametrosLog['status']           = "Fallido";
                        $arrayParametrosLog['messageUser']      = "No fue posible transferir los documentos digitales";
                        $arrayParametrosLog['descriptionError'] = $arrayResponseWS['err'];
                        $arrayParametrosLog['inParameters']     = json_encode($arrayDataTransferir, 128);
                        $arrayParametrosLog['creationUser']     = $arrayParametros['strUsrCreacion'];

                        $this->serviceUtil->insertLog($arrayParametrosLog);
                    }
                } 
                else
                {
                    throw new \Exception("No fue posible transferir los documentos digitales", 1);
                }
            }
        } 
        catch(\Exception $objException)
        {
            $arrayParametrosLog                     = array();
            $arrayParametrosLog['enterpriseCode']   = $arrayParametros['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['messageUser']      = "No fue posible transferir los documentos digitales";
            $arrayParametrosLog['descriptionError'] = ($objException->getCode() == 1) 
                                                          ? "Error al enviar petición a transferir documentos (ms-comp-contrato-digital)" . "\n\n" .
                                                          json_encode($arrayResponseWS, 128) : $objException->getMessage();
            $arrayParametrosLog['inParameters']     = ($objException->getCode() == 1) 
                                                          ? json_encode($arrayDataTransferir, 128)
                                                          : json_encode($arrayParametros, 128);
            $arrayParametrosLog['creationUser']     = $arrayParametros['strUsrCreacion'];
            $this->serviceUtil->insertLog($arrayParametrosLog);
        }

        if ($this->emGeneral->getConnection()->isTransactionActive())
        {
            $this->emGeneral->close();
        }
        if ($this->emComunicacion->getConnection()->isTransactionActive())
        {
            $this->emComunicacion->close();
        }
        
        return $arrayResponse;
    }
     /*
     * Reversa un adendum que tenga un certificado caducado
     * @param arrayParametros['strTipo'   => tipo de Adendum AP o AS
     *                        'strNumero' => numero del adendum
     *                            ]
     * @return 
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>          
     * @version 1.0 26-11-2020
     * @date    
     */

    public function reversarAdendumCertificadoCaducado($arrayParametros)
    {
        $strTipo   = $arrayParametros['strTipo'];
        $strNumero = $arrayParametros['strNumero'];
        $this->emComercial->getConnection()->beginTransaction();                       
        $this->emComunicacion->getConnection()->beginTransaction();                       
        try
        {
            $objAdendums = $this->emComercial->getRepository('schemaBundle:InfoAdendum')
                                             ->findBy(array("tipo"   => $strTipo,
                                                            "numero" => $strNumero));
            if ($objAdendums)
            {
                foreach ($objAdendums as $entityAdendum)
                {
                    $entityAdendum->setTipo(null);
                    $entityAdendum->setNumero(null);
                    $entityAdendum->setContratoId(null);
                    $entityAdendum->setEstado("Pendiente");
                    $this->emComercial->persist($entityAdendum);
                }
                $this->emComercial->flush();
            }    
            
            $objImagenes = $this->emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')
                                                 ->findBy(array("numeroAdendum" => $strNumero));
            if ($objImagenes)
            {
                foreach ($objImagenes as $entityImagen)
                {
                    $entityImagen->setNumeroAdendum(null);
                    $entityImagen->setEstado('Eliminado');
                    $this->emComunicacion->persist($entityImagen);
                }
                $this->emComunicacion->flush();
            }    
                                      
            $this->emComercial->getConnection()->commit();
            $this->emComunicacion->getConnection()->commit();
            $this->serviceInfoServicio->eliminarServicioInternetFactible($arrayParametros); 
        }
        catch (\Exception $ex)
        {
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
            }
            $arrayParametrosLog['enterpriseCode']   = $arrayContrato['strCodEmpresa'];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = "InfoContratoDigitalService";
            $arrayParametrosLog['appClass']         = "InfoContratoDigitalService";
            $arrayParametrosLog['appMethod']        = "reversarAdendumCertificadoCaducado";
            $arrayParametrosLog['appAction']        = "reversarAdendumCertificadoCaducado";
            $arrayParametrosLog['messageUser']      = "ERROR";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $ex->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayParametros);
            $arrayParametrosLog['creationUser']     = "TELCOS_MOVIL";

            $this->serviceUtil->insertLog($arrayParametrosLog);

            throw $ex;
        }
    }    
}
