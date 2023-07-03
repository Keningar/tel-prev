<?php

namespace telconet\schemaBundle\Service;

use telconet\schemaBundle\Entity\ReturnResponse;
use telconet\schemaBundle\Entity\InfoError;
use telconet\schemaBundle\Entity\InfoLog;
use telconet\soporteBundle\Service\SoporteService;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * UtilService clase que contiene metodos genericos.
 *
 *
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 24-07-2016
 * @since 1.0
 * 
 */
class UtilService
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emGeneral;

    /**
     * @var String 
     */
    private $pathErrorLog;

    /**
     * @var String 
     */
    private $strWriteLog;

    /**
     * @var String 
     */
    private $strPathTelcos;
    /**
     * constante 0 default dada por php
     */
    const CODE_ZERO = 0;
    
    /**
     * constante 1 definida para errores no controlados
     */
    const CODE_DEFAULT = 1;
    
    /**
     * constante 2 definida para errores de validacion de objetos
     */
    const CODE_OBJECT = 2;
    
    /**
     * string definido para ser reemplazado con mensaje de control de validación de objetos
     */
    const TOKEN_MENSAJE = "MSG";
    
    private $strUsuarioFtp;
    
    private $strPassFtp;
    
    private $strHostFtp;
    
    private $strPortFtp; 
    
    private $strDirRemoto;
    
    private $strRutaCertificado;

    private $serviceRestClient;

    private $intWsContratoDigitalTimeOut;

    private $strUrlWsSftp;

    private $emComercial;

    private $serviceSoporte;

    private $strMSnfs;

    private $serviceTokenCas;

    private $strMSBitacora;

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emGeneral                   = $container->get('doctrine.orm.telconet_general_entity_manager');
        $this->emComercial                 = $container->get('doctrine')->getManager('telconet');
        $this->serviceSoporte              = $container->get('doctrine')->getManager('telconet_soporte');
        $this->serviceRestClient           = $container->get('schema.RestClient');
        $this->pathErrorLog                = $container->getParameter('general.path.errorLog');
        $this->strPathTelcos               = $container->getParameter('path_telcos');
        $this->strWriteLog                 = $container->getParameter('strWriteLog');
        $this->strUsuarioFtp               = $container->getParameter('ftp_user_certificado');
        $this->strPassFtp                  = $container->getParameter('ftp_pass_certificado');
        $this->strHostFtp                  = $container->getParameter('ftp_host_certificado');
        $this->strPortFtp                  = $container->getParameter('ftp_port_certificado');
        $this->strDirRemoto                = $container->getParameter('dir_remoto_certificados');
        $this->strRutaCertificado          = $container->getParameter('ruta_certificados_digital');
        $this->intWsContratoDigitalTimeOut = $container->getParameter('ws_contrato_digital_timeout');
        $this->strUrlWsSftp                = $container->getParameter('ws_contrato_digital_sftp_descargar');
        $this->strMSnfs                    = $container->getParameter('ms_nfs');
        $this->serviceTokenCas             = $container->get('seguridad.TokenCas');
        $this->strMSBitacora               = $container->getParameter('url_web_service_bitacora');

    }
    /**
     * Documentación para 'getSegmentacionFecha'
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 19-06-2017
     * 
     * @param array $arrayParametros['strFecha' => 'Fecha la cual se va a segmentar' ]
     * 
     * @return array $arrayResultado['intTrimestre'  => 'Número del trimestre en la cual está ubicado la fecha enviada',
     *                               'intAnioActual' => 'Año actual de la fecha consultada']
     */
    public function getSegmentacionFecha($arrayParametros)
    {
        $arrayResultado = array('intTrimestre' => 0);
        $strUsrCreacion = ( isset($arrayParametros['strUsrCreacion']) && !empty($arrayParametros['strUsrCreacion']) ) 
                           ? $arrayParametros['strUsrCreacion'] : 'telcos';
        $strIpCreacion  = ( isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']) ) 
                           ? $arrayParametros['strIpCreacion'] : '127.0.0.1';

        try
        {
            if( isset($arrayParametros['strFecha']) && !empty($arrayParametros['strFecha']) )
            {
                $strFecha       = $arrayParametros['strFecha'];
                $intMes        = date("m",strtotime($strFecha));
                $intMes        = is_null($intMes) ? date('m') : $intMes;
                $intTrimestre  = floor(($intMes-1) / 3)+1;

                $arrayResultado['intTrimestre']  = $intTrimestre;
                $arrayResultado['intAnioActual'] = date("Y",strtotime($strFecha));
            }//( isset($arrayParametros['datetimeFecha']) && !empty($arrayParametros['datetimeFecha']) )
            else
            {
                $this->insertError('TELCOS+', 
                                   'schemaBundle.UtilService.getSegmentacionFecha', 
                                   'No se ha enviado el parámetro de fecha correspondiente para ser segmentada.',
                                   $strUsrCreacion,
                                   $strIpCreacion);
            }
        }
        catch( \Exception $e)
        {
            $this->insertError('TELCOS+', 
                               'schemaBundle.UtilService.getSegmentacionFecha', 
                               $e->getMessage(),
                               $strUsrCreacion,
                               $strIpCreacion);
        }

        return $arrayResultado;
    }

    /**
     * Documentación para la función empresaAplicaProceso
     * Función que devuelve 'S' si una empresa aplica a un determinado proceso y 'N' si no aplica.
     * El proceso debe estar definido en el parámetro 'EMPRESA_APLICA_PROCESO'
     * Donde VALOR1 = El proceso que aplica la empresa.
     *       VALOR2 = S/N según corresponda.
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0 
     * @since 30-08-2018
     * @param string $arrayParametros
     */
    public function empresaAplicaProceso($arrayParametros)
    {
        $strMensaje = "Ha ocurrido un error inesperado al realizar la consulta. ";
        try
        {
            if (! isset($arrayParametros['strProcesoAccion']))
            {
                throw new \Exception("No se ha definido el proceso o acción a validar.");
            }
            if (! isset($arrayParametros['strEmpresaCod']))
            {
                throw new \Exception("No se ha definido la empresa a validar para el proceso " . $arrayParametros['strProcesoAccion']);
            }
            $objDQL            = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getDql('EMPRESA_APLICA_PROCESO',
                                                                                                          'GENERAL',
                                                                                                          'TELCOS',
                                                                                                          null,
                                                                                                          $arrayParametros['strProcesoAccion'],
                                                                                                          null,
                                                                                                          null,
                                                                                                          null,
                                                                                                          null,
                                                                                                          strval($arrayParametros['strEmpresaCod']));
            $arrayParamtroDet  = $objDQL->getOneOrNullResult();
            $strRespuesta      = isset($arrayParamtroDet['valor2']) ? $arrayParamtroDet['valor2'] : 'N';
        }
        catch(\Exception $objException)
        {
            $this->insertError('Telcos', 'UtilService.empresaAplicaProceso', $strMensaje . $objException->getMessage(), 'telcos', '127.0.0.1');
            $strRespuesta = 'N';
        }
        return $strRespuesta;
    }

    /**
     * insertError, Inserta un registro en la estructura DB_GENERAL.INFO_ERROR
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 27-07-2016
     * @since 1.0
     * 
     * @param String $strAplicacion     Recibe el nombre de la aplicacion
     * @param String $strProceso        Recibe el nombre del proceso
     * @param String $strDetalleError   Recibe el detalle del error
     * @param String $strUsrCreacion    Recibe el usuario creacion
     * @param String $strIpCreacion     Recibe la ip de creacion
     */
    public function insertError($strAplicacion, $strProceso, $strDetalleError, $strUsrCreacion, $strIpCreacion)
    {
        $objReturnResponse             = new ReturnResponse();
        $arrayDirectorioLog['strPath'] = $this->strPathTelcos . $this->pathErrorLog;
        $this->emGeneral->getConnection()->beginTransaction();
        try
        {
            $objInfoError = new InfoError();
            $objInfoError->setAplicacion($strAplicacion);
            $objInfoError->setProceso($strProceso);
            $objInfoError->setDetalleError($strDetalleError);
            $objInfoError->setUsrCreacion($strUsrCreacion);
            $objInfoError->setFeCreacion(new \DateTime('now'));
            $objInfoError->setIpCreacion($strIpCreacion);
            $this->emGeneral->persist($objInfoError);
            $this->emGeneral->flush();
            $this->emGeneral->commit();
        }
        catch(\Exception $ex)
        {
            $this->emGeneral->getConnection()->rollback();
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($ex->getMessage());
            $this->loggerInfo($arrayDirectorioLog, (new \DateTime())->format('d-m-Y') . '.log', 'insertError', (array) $objReturnResponse);
        }
        $this->emGeneral->getConnection()->close();
    } //insertError

    /**
     * logger, permite crear y escribir en un archivo usando la clase Logger
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 27-07-2016
     * @since 1.0
     * 
     * @param array  $arrayDirectorioLog Recibe ruta de escritura del log
     * @param String $strNombreLog       Recibe el nombre del log
     * @param String $strTipo            Recibe un tipo de traza
     * @param String $strMensaje         Recibe un mensaje identificador para la traza
     * @param String $arrayInformacion   Recibe un array con el contenido a escribir en el log
     */
    private function logger($arrayDirectorioLog, $strNombreLog, $strTipo, $strMensaje, $arrayInformacion)
    {
        $objReturnResponse = new ReturnResponse();
        if('N' === $this->strWriteLog)
        {
            return false;
        }
        $objLog = new Logger('Logger');
        //Pregunta si el directorio existe
        if($objReturnResponse::PROCESS_SUCCESS === $this->creaDirectorio($arrayDirectorioLog)->getStrStatus())
        {
            $objLog->pushHandler(new StreamHandler($arrayDirectorioLog['strPath'] . $strNombreLog, Logger::INFO));
            if('ERROR' === $strTipo)
            {
                $objLog->addError($strMensaje, $arrayInformacion);
            }
            else if('INFO' === $strTipo)
            {
                $objLog->addInfo($strMensaje, $arrayInformacion);
            }
        }
    } //logger

    /**
     * loggerInfo, escribe una traza de tipo Info
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 27-07-2016
     * @since 1.0
     * 
     * @param array  $arrayDirectorioLog Recibe ruta de escritura del log
     * @param String $strNombreLog       Recibe el nombre del log
     * @param String $strMensaje         Recibe un mensaje identificador para la traza
     * @param String $arrayInformacion   Recibe un array con el contenido a escribir en el log
     */
    public function loggerInfo($arrayDirectorioLog, $strNombreLog, $strMensaje, $arrayInformacion)
    {
        $this->logger($arrayDirectorioLog, $strNombreLog, 'INFO', $strMensaje, $arrayInformacion);
    } //loggerInfo

    /**
     * loggerError, escribe una traza de tipo Error
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 27-07-2016
     * @since 1.0
     * 
     * @param array  $arrayDirectorioLog Recibe ruta de escritura del log
     * @param String $strNombreLog       Recibe el nombre del log
     * @param String $strMensaje         Recibe un mensaje identificador para la traza
     * @param String $arrayInformacion   Recibe un array con el contenido a escribir en el log
     */
    public function loggerError($arrayDirectorioLog, $strNombreLog, $strMensaje, $arrayInformacion)
    {
        $this->logger($arrayDirectorioLog, $strNombreLog, 'ERROR', $strMensaje, $arrayInformacion);
    } //loggerError

    /**
     * creaDirectorio, crea directorio segun la ruta enviada
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 27-07-2016
     * @since 1.0
     * 
     * @param array $arrayParametros ['strPath' => Recibe la ruta donde se desea crear la carpeta]
     * @return \telconet\schemaBundle\Entity\ReturnResponse Retorna 100 cuando se creo correctamente, caso contrario 001
     */
    public function creaDirectorio($arrayParametros)
    {

        $objReturnResponse = new ReturnResponse();
        //Pregunta si $arrayParametros['strPath'] esta vacia
        if($objReturnResponse->emptyArray($arrayParametros['strPath']))
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . 'No esta enviando directorio');
            return $objReturnResponse;
        }
        try
        {
            //Pregunta si no existe el directorio y envia a crear el directorio
            if(!file_exists($arrayParametros['strPath']) && !is_dir($arrayParametros['strPath']))
            {
                mkdir($arrayParametros['strPath'], 0777, true);
            }
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus("Se creó el directorio correctamente");
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($ex->getMessage());
        }
        return $objReturnResponse;
    } //validateDirExistCreate
    
    /**
     * validaObjeto, valida que un objeto exista y no sea nulo
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 14-09-2016
     * @since 1.0
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 14-11-2016
     * @since 1.1 - Se envía codigo relacionado a excepcion de validacion de Objeto ( Se define 2 como valor de codigo para Objetos, informacion
     *              guardada en la Base de datos de esa manera )
     * 
     * @param object $objVariable         ->  Parametro de entrada a validar
     * @param string $strMensajeRespuesta ->  Mensaje de respuesta a retornar en caso de que 
     *                                        el obtejo enviado por parametro no sea valido
     * 
     * @return \Exception  Se retorna una excepcion en caso de que el objeto enviado por parametro no sea valido
     */
    public function validaObjeto($objVariable, $strMensajeRespuesta)
    {
        if(!$objVariable)
        {
            throw new \Exception($strMensajeRespuesta,self::CODE_DEFAULT);
        }
        if(!is_object($objVariable))
        {
            if (is_string($objVariable))
            {
                if ("" == $objVariable)
                {
                    throw new \Exception($strMensajeRespuesta,self::CODE_DEFAULT);
                }
            }
        }
    }
    
     /**
     * 
     * Método que se encarga de validar una variable si existe sea esta objeto u otro tipo de dato y devolver la excepcion con el codigo 
     * personalizado
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 24-11-2016
     * @since 1.0
     * 
     * @param 
     *            $mixedVariable        Variable generica enviada a ser validada
     *            $strMensajeError      Mensaje personalizado de excepcion
     *            $strTipoEsperado      Tipo de dato que se requiere validar respecto al tipo de dato de la variable $mixedVariable
     *                                  
     * @throws Exception - Se devuleve mensaje personalizado y código determinado por tipo de excepcion
     */
    public function validarVariable($mixedVariable , $strMensajeError , $strTipoEsperado = 'object')
    {
        $strTipoVariable = gettype($mixedVariable);
        
        if ($strTipoVariable != $strTipoEsperado)
        {
            $intCodigoError = ($strTipoEsperado == 'object' ? self::CODE_OBJECT : self::CODE_DEFAULT);
            throw new \Exception($strMensajeError, $intCodigoError);
        }
    }
    
    /**
     * 
     * Metodo que relanza la Excepcion con una codigo personalizado para poder ser controlada en bloques de codigos predecesores
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 24-11-2016
     * 
     * @param Exception $objException
     * @throws Exception
     */
    public function relanzarExcepcion($objException)
    {
        if($objException->getCode()==self::CODE_ZERO)
        {
            $this->lanzarExcepcion('DEFAULT',$objException->getMessage());                            
        }
        else
        {                
            throw ($objException);
        }      
    }
    
    /**
     * 
     * Metodo que devuelve el mensaje personalizado para una excepcion controlada
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 24-11-2016
     * 
     * @param Exception $objException
     * @return String strMensaje Mensaje personalizado de acuerdo a la excepcion manejada
     */
    public function getMensajeException($objException)
    {
        if($objException->getCode()==self::CODE_ZERO)
        {
            $objResponseExcepcion = $this->controlarExcepcion('DEFAULT',$objException->getMessage());                
            $strMensaje           = $objResponseExcepcion->getStrMessageStatus();
        }
        else
        {                
            $strMensaje           = $objException->getMessage();
        }
        
        return $strMensaje;
    }
    
    /**
     * 
     * Método que se encarga de realizar el control de excepciones lanzado un Objeto Excepción con el código y Mensaje personalizado obtenido
     * de la base de datos dado una constante enviada como parametro
     * Los códigos serán creados en secuencias a partit del 2 ya que el codigo 0 generado por default por PHP,el 1 establecido para error general
     * 
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 14-11-2016
     * @since 1.0
     * 
     * @param 
     *            string      strConstante             Constante que determina el tipo de excepcion lanzada ( OBJETO , NETWORKING , ... )
     *                                                 se creará una constante nueva por tipo de excepción general que pueda ser 
     *                                                 controlada y gestionada
     *            string      strMsgPersonalizado      Mensaje de excepción para fallos puntuales que requieren un tratamiento especial
     *                                  
     * @throws Exception - Se devuleve mensaje personalizado y código determinado por tipo de excepcion
     */
    public function controlarExcepcion( $strConstante , $strMsgPersonalizado )    
    {                                
        $arrayResponse = $this->getMensajeExcepcionByConstante($strConstante, $strMsgPersonalizado);
                        
        $objReturnResponse  = new ReturnResponse();
        $objReturnResponse->setStrStatus("ERROR");
        $objReturnResponse->setStrMessageStatus($arrayResponse['strMensajeExcepcion']);
        return $objReturnResponse;                
    }     
    
    /**
     * 
     * Método que se encarga de lanzar la excepcion con un codigo y mensaje personalizado obtenido
     * de la base de datos dado una constante enviada como parametro
     * Los códigos serán creados en secuencias a partit del 2 ya que el codigo 0 generado por default por PHP,el 1 establecido para error general
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 24-11-2016
     * @since 1.0
     * 
     * @param 
     *            string      strConstante             Constante que determina el tipo de excepcion lanzada ( OBJETO , NETWORKING , ... )
     *                                                 se creará una constante nueva por tipo de excepción general que pueda ser 
     *                                                 controlada y gestionada
     *            string      strMsgPersonalizado      Mensaje de excepción para fallos puntuales que requieren un tratamiento especial
     *                                  
     * @throws Exception - Se devuleve mensaje personalizado y código determinado por tipo de excepcion
     */
    public function lanzarExcepcion($strConstante , $strMsgPersonalizado)
    {
        $arrayResponse = $this->getMensajeExcepcionByConstante($strConstante, $strMsgPersonalizado);        
        throw new \Exception($arrayResponse['strMensajeExcepcion'], $arrayResponse['intCodeExcepcion']);
    }
    
    /**
     * 
     * Método que se encarga de obtener el mensaje de excepcion personalizado dado una constante de busqueda y el mensaje personalizado que se
     * requiere devolver o editar de acuerdo al tipo de constante enviado
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 24-11-2016
     * @since 1.0
     * 
     * @param 
     *            string      strConstante             Constante que determina el tipo de excepcion lanzada ( OBJETO , NETWORKING , ... )
     *                                                 se creará una constante nueva por tipo de excepción general que pueda ser 
     *                                                 controlada y gestionada
     *            string      strMsgPersonalizado      Mensaje de excepción para fallos puntuales que requieren un tratamiento especial
     *                                  
     * @throws Exception - Se devuleve mensaje personalizado y código determinado por tipo de excepcion
     */
    private function getMensajeExcepcionByConstante($strConstante , $strMsgPersonalizado)
    {
        $arrayResponse = array();
        
        //El mansaje definido por default para validación de objetos está definido por "No existe informacion de (MSG) , por favor verificar"
        //La cadena (MSG) será reemplazada por la cadena determinada por el objeto que se requiera validar
        $arrayResultado = $this->emGeneral->getRepository("schemaBundle:AdmiParametroDet")->getOne('MANEJO_EXCEPCIONES','','','',$strConstante,
                                                                                                   '','','','','',null);
        
        $strMensajeExcepcion = "Error en lógica de Negocio, por favor Notificar a Sistemas.";
        $intCodeExcepcion    = self::CODE_DEFAULT; //Definido por 1 cuando se trata de una excepcion que no puede ser controlada
       
        if(isset($arrayResultado))
        {                
            $strMensajeExcepcion = isset($arrayResultado['valor2'])?
                                   str_replace(self::TOKEN_MENSAJE,$strMsgPersonalizado,$arrayResultado['valor2']):$strMsgPersonalizado;
            $intCodeExcepcion    = isset($arrayResultado['valor3'])?intval($arrayResultado['valor3']):$intCodeExcepcion;
        }
        
        $arrayResponse['strMensajeExcepcion'] = $strMensajeExcepcion;
        $arrayResponse['intCodeExcepcion']    = $intCodeExcepcion;
        
        return $arrayResponse;
    }
    
    
    /**
     * Función que convierte una cadena en decimal según el formato enviado como parámetro
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 27-10-2017
     * @since 1.0
     * 
     * @param String $strFormato       Recibe el formato E|D expresado en cantidad de digitos que representa la parte entera y decimal del valor
     *                                 enviado como parámetro. 
     * @param String $strValor         Recibe el valor a transformar en forma de cadena de texto.
     * @param String $strDelimitador   Recibe el caracter delimitador.
     * @param Float  $floatResultado   Retorna el valor correspondiente en decimales.
     */
    public function getStringToFloat($strFormato, $strValor, $strDelimitador)
    {
        $floatResultado = 0;
                            
        if(strpos($strFormato,$strDelimitador))
        {
            $arrayFormatoValor = explode($strDelimitador,$strFormato);

            $intDigitosParteEntera  = (int) $arrayFormatoValor[0];

            $intDigitosParteDecimal = (int) $arrayFormatoValor[1];

            $intParteEntera         = (int) substr(preg_replace("[^0-9]", "", $strValor), 0, $intDigitosParteEntera); 

            $intParteDecimal        = (int) substr(preg_replace("[^0-9]", "", $strValor), -$intDigitosParteDecimal);
            
            $floatResultado         = floatval($intParteEntera.'.'.$intParteDecimal);
        }           
        return $floatResultado;
    }
    
    /**
     * Función que convierte una cadena que representa una fecha en el formato enviado como parámetro al fromato DD/MM/YYYY
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 30-10-2017
     * @since 1.0
     * 
     * @param String $strFormato       Recibe el formato de la fecha . 
     * @param String $strFecha         Recibe el valor de la cadena de texto que representa una fecha.
     * @param Float  $floatResultado   Retorna el valor correspondiente en decimales.
     */
    public function getStringFechaConFormato($strFormato, $strFecha)
    {
        $strFechaResultante = '';
        $strAnio = '';
        $strMes  = '';
        $strDia  = '';
                            
        if('AAAAMMDD' === $strFormato) 
        {
            $strAnio = substr(preg_replace("[^0-9]", "", $strFecha), 0, 4);
            $strMes  = substr(preg_replace("[^0-9]", "", $strFecha), 4, 2);
            $strDia  = substr(preg_replace("[^0-9]", "", $strFecha), 6, 2);
        }
        else if('DDMMAAAA' === $strFormato) 
        {
            $strDia  = substr(preg_replace("[^0-9]", "", $strFecha), 0, 2);
            $strMes  = substr(preg_replace("[^0-9]", "", $strFecha), 2, 2);
            $strAnio = substr(preg_replace("[^0-9]", "", $strFecha), 4, 2);
        }
        else
        {
            $strFechaResultante = '';
        }
        
        $strFechaResultante = $strDia.'/'.$strMes.'/'.$strAnio;
        
        return $strFechaResultante;
    }  

    /**
     * Función que permite insertar un registro en el Log
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 01-08-2018
     * @since 1.0
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.1 19-01-2020  Corrección de sintaxis al importar clase Exception.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.2 06-04-2020 En el catch de la presente función se realiza un insert a la tabla
     *                         info_log en caso de que llegase irse por ese flujo.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.2 15-07-2020 Obtención del directorio base de la aplicación, clase, método y acción.
     *                         Valores por defecto para $strEmpresaCod y $strUsrCreacion si llegasen vacíos.
     *
     * @param array $arrayParametros  Recibe los parametros para guardar el Log . 
     */    
    public function insertLog($arrayParametros)
    {
        $strEmpresaCod       = !empty($arrayParametros['enterpriseCode'])
                                   ? $arrayParametros['enterpriseCode']
                                   : '10';
        $strTipoLog          = $arrayParametros['logType'];
        $strOrigenLog        = $arrayParametros['logOrigin'];
        $strLatitud          = ($arrayParametros['latitude']) ? $arrayParametros['latitude'] : "";
        $strLongitud         = ($arrayParametros['longitude']) ? $arrayParametros['longitude']: "";
        $strAplicacion       = ($arrayParametros['application']) ? $this->basename($arrayParametros['application']) : "";
        $strClase            = ($arrayParametros['appClass']) ? $this->basename($arrayParametros['appClass']) : "";
        $strMetodo           = ($arrayParametros['appMethod']) ? $this->basename($arrayParametros['appMethod']) : "" ;
        $strAccion           = ($arrayParametros['appAction']) ? $this->basename($arrayParametros['appAction']) : "";
        $strMensaje          = $arrayParametros['messageUser'];
        $strEstado           = $arrayParametros['status'];
        $strDescripcion      = $arrayParametros['descriptionError'];
        $strImei             = ($arrayParametros['deviceImei']) ? $arrayParametros['deviceImei'] : "";
        $strModelo           = ($arrayParametros['deviceModel']) ? $arrayParametros['deviceModel'] : "";
        $strVersionApk       = ($arrayParametros['appVersion']) ? $arrayParametros['appVersion'] : "";
        $strVersionSo        = ($arrayParametros['softwareVersion']) ? $arrayParametros['softwareVersion'] : "";
        $strTipoConexion     = ($arrayParametros['conectionType']) ? $arrayParametros['conectionType'] : "";
        $strIntensidadSenal  = ($arrayParametros['signalStrength']) ? $arrayParametros['signalStrength'] : "" ;
        $strParametroEntrada = $arrayParametros['inParameters'];
        $strUsrCreacion      = !empty($arrayParametros['creationUser'])
                                   ? $arrayParametros['creationUser']
                                   : 'TELCOS';
        $objReturnResponse = new ReturnResponse();
        try
        {
            $entityLog = new InfoLog();
            $entityLog->setEmpresaCod($strEmpresaCod);
            $entityLog->setTipoLog($strTipoLog);
            $entityLog->setOrigenLog($strOrigenLog);
            $entityLog->setLatitud($strLatitud);
            $entityLog->setLongitud($strLongitud);
            $entityLog->setAplicacion($strAplicacion);
            $entityLog->setClase($strClase);
            $entityLog->setMetodo($strMetodo);
            $entityLog->setAccion($strAccion);
            $entityLog->setMensaje($strMensaje);
            $entityLog->setEstado($strEstado);
            $entityLog->setDescripcion($strDescripcion);
            $entityLog->setImei($strImei);
            $entityLog->setModelo($strModelo);
            $entityLog->setVersionApk($strVersionApk);
            $entityLog->setVersionSo($strVersionSo);
            $entityLog->setTipoConexion($strTipoConexion);
            $entityLog->setIntensidadSenal($strIntensidadSenal);
            $entityLog->setParametroEntrada($strParametroEntrada);
            $entityLog->setUsrCreacion($strUsrCreacion);
            $entityLog->setFeCreacion(new \DateTime('now'));
            
            $this->emGeneral->persist($entityLog);
            $this->emGeneral->flush(); 
            $objReturnResponse->setStrStatus("200");
            $objReturnResponse->setStrMessageStatus("OK");
            
        } 
        catch (\Exception $ex) 
        {
            error_log("InfoLog: " . $ex->getMessage());
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR_TRANSACTION);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR_TRANSACTION);

        }
        return $objReturnResponse;                
    }    

    /**
     * Función que permite validar los datos que se van a guardar en el log
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 01-08-2018
     * @since 1.0
     * 
     * @param array $arrayParametros  Recibe los parametros que se van a validar . 
     */       
    public function validarParametrosLog($arrayParametros)
    {
        $boolReturn = true;
        if (!isset($arrayParametros['enterpriseCode']))
        {
            $boolReturn = false;
        }
        return $boolReturn;
    }

    /**
     * Método encargado de obtener la información de la info_error.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 14-09-2018
     *
     * @param $arrayParametros [
     *                              intIdError         => Id del error,
     *                              strAplicacion      => Aplicacion,
     *                              strProceso         => Proceso,
     *                              strDetalleError    => Detalle del error,
     *                              strUsuarioCreacion => Usuario de creación,
     *                              strIpCreacion      => Ip de creación,
     *                              strFeCreaIni       => Fecha Inicio de creación,
     *                              strFeCreaFin       => Fecha Fin de creación
     * @return $arrayRespuesta [status, message]
     */
    public function getInfoError($arrayParametros)
    {
        $arrayRespuesta = $this->emGeneral->getRepository('schemaBundle:InfoError')
            ->getInfoError($arrayParametros);

        if (!empty($arrayRespuesta) && $arrayRespuesta['status'] === 'ok' && empty($arrayRespuesta['result']))
        {
            $arrayRespuesta            = array();
            $arrayRespuesta['status']  = 'fail';
            $arrayRespuesta['message'] = 'La consulta no retornó valores con los parámetros ingresados.';
        }

        return $arrayRespuesta;
    }
    
    /**
     * Función que retorna una contraseña de la longitud recibida en el parametro
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 01-03-2019
     * @since 1.0
     * 
     * @param array $arrayParametros  [intLongitud]
     * @return string strRetorno 
     */       
    public function generarPassword($arrayParametros)
    {
        $strNumeros    = "0123456789";
        $strMayusculas = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $strMinusculas = "abcdefghijklmnopqrstuvwxyz";
        $strEspeciales = "./*+-=?%&#!";

        $intLogitud   = $arrayParametros['intLongitud'];
        $strCadena    = $strNumeros . $strMayusculas . $strMinusculas . $strEspeciales;
        $strRetorno   = "";
        
        for ($intCont = 1; $intCont <= $intLogitud; $intCont++)
        {
            $intPosicion = rand(1, strlen($strCadena));
            $strRetorno .= substr($strCadena, $intPosicion, 1);
        }
        return $strRetorno;
    }

    /**
     * Función que retorna una contraseña de la longitud recibida en el parametro
     * sin caracteres especiales
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 14-01-2021
     * @since 1.0
     * 
     * @param array $arrayParametros  [intLongitud]
     * @return string strRetorno 
     */       
    public function generarPasswordSinCharEspecial($arrayParametros)
    {
        $strNumeros    = "0123456789";
        $strMayusculas = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $strMinusculas = "abcdefghijklmnopqrstuvwxyz";

        $intLogitud   = $arrayParametros['intLongitud'];
        $strCadena    = $strNumeros . $strMayusculas . $strMinusculas;
        $strRetorno   = "";
        
        for ($intCont = 1; $intCont <= $intLogitud; $intCont++)
        {
            $intPosicion = rand(1, strlen($strCadena));
            $strRetorno .= substr($strCadena, $intPosicion, 1);
        }
        return $strRetorno;
    }

    /**
     * Función que trae un archivo de un servidor sftp
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 01-03-2019
     * @since 1.0
     * 
     * @author Eddgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1. 21-04-2020 se modifica para que acepte valores del sftp por parametros
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.2 18-06-2020 - Obtención del certificado por medio de un servicio web
     *
     * @param array $arrayParametros  [strArchivoRemoto, strDirDia]
     * @return bool $boolRetorno
     * 
     */        
    public function hasRecibirArchivoSftp($arrayParametros)
    {  
        $strPathCertificado = $this->strPathTelcos . $this->strRutaCertificado;
        $arrayParametrosMK['strPath'] = $this->strPathTelcos . $this->strRutaCertificado;
        $strPathCertificado          .= $arrayParametros['strDirDia'] . "/";
        $arrayParametrosMK['strPath'] = $strPathCertificado;
        $strDirectorio                = ($arrayParametros['strDirRemoto']) ? $arrayParametros['strDirRemoto'] : $this->strDirRemoto;
        $strHost                      = ($arrayParametros['strHost']) ? $arrayParametros['strHost'] : $this->strHostFtp;
        $strUsuario                   = ($arrayParametros['strUsuario']) ? $arrayParametros['strUsuario'] : $this->strUsuarioFtp;
        $strPass                      = ($arrayParametros['strPassword']) ? $arrayParametros['strPassword'] : $this->strPassFtp;
        
        $strArchivoRemoto  =  $strDirectorio . "/" . $arrayParametros['strArchivoRemoto'];
        $strArchivoDestino = $strPathCertificado . $arrayParametros['strArchivoRemoto'];
        $boolRetorno = true;

        try
        {

            $this->creaDirectorio($arrayParametrosMK);

            //valido que se haya creado el directorio
            if(!file_exists($strPathCertificado) && !is_dir($strPathCertificado))
            {
                throw new \Exception("No se pudo crear el directorio o el archivo " . $strPathCertificado);
            }

            if($arrayParametros['strMedioDescarga'] == 'SFTP_WS')
            {
                //Llamada al microservicio sftp para descarga de certificado
                $strPathTemporal = $arrayParametros['strIdentificacion'] . '/' . $arrayParametros['strDirDia'] . '/';

                $arrayDataSftp = array('fileList'   => array(array('fileSourcePathList'  => array(array("filename" => $strArchivoRemoto)),
                                                                   'fileDestinationPath' => $strPathTemporal)),
                                       'serverName' => $arrayParametros['strServidorRemoto']);

                $arrayRest[CURLOPT_TIMEOUT] = $this->intWsContratoDigitalTimeOut;

                $arrayResponse = $this->serviceRestClient->postJSON($this->strUrlWsSftp, json_encode($arrayDataSftp), $arrayRest);

                if($arrayResponse['result'])
                {
                    $arrayResponseSftp = json_decode($arrayResponse['result'], true);

                    if($arrayResponseSftp['code'] == 200 && count($arrayResponseSftp['fileContentList']) > 0)
                    {
                        $arrayFileContent = $arrayResponseSftp['fileContentList'][0];

                        if(count($arrayFileContent['content']) > 0)
                        {
                            file_put_contents($strPathCertificado . '/' . $arrayParametros['strArchivoRemoto'],
                                base64_decode($arrayFileContent['content']));

                            if(filesize($strPathCertificado . '/' . $arrayParametros['strArchivoRemoto']) <= 0)
                            {
                                $boolRetorno = false;
                            }
                        }
                        else
                        {
                            $boolRetorno = false;
                        }
                    }
                    else
                    {
                        $arrayParametrosLog['enterpriseCode']   = $arrayParametros["strCodEmpresa"];
                        $arrayParametrosLog['logType']          = "1";
                        $arrayParametrosLog['logOrigin']        = "ms-core-gen-sftp";
                        $arrayParametrosLog['application']      = $arrayResponseSftp['origin'];
                        $arrayParametrosLog['appClass']         = $arrayResponseSftp['className'];
                        $arrayParametrosLog['appMethod']        = $arrayResponseSftp['methodName'];
                        $arrayParametrosLog['appAction']        = $arrayResponseSftp['methodName'];
                        $arrayParametrosLog['status']           = "Fallido";
                        $arrayParametrosLog['messageUser']      = $arrayResponseSftp['status'];
                        $arrayParametrosLog['descriptionError'] = $arrayResponseSftp['message'] . '\n\n' . json_encode($arrayResponse, 128);
                        $arrayParametrosLog['inParameters']     = json_encode($arrayDataSftp, 128);
                        $arrayParametrosLog['creationUser']     = $arrayParametros['strUsrCreacion'];

                        $this->insertLog($arrayParametrosLog);
                        $boolRetorno = false;
                    }
                }
                else
                {
                    $arrayParametrosLog['enterpriseCode']   = $arrayParametros["strCodEmpresa"];
                    $arrayParametrosLog['logType']          = "1";
                    $arrayParametrosLog['logOrigin']        = "TELCOS";
                    $arrayParametrosLog['application']      = basename(__FILE__);
                    $arrayParametrosLog['appClass']         = basename(__CLASS__);
                    $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                    $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                    $arrayParametrosLog['status']           = "Fallido";
                    $arrayParametrosLog['messageUser']      = "Error";
                    $arrayParametrosLog['descriptionError'] = json_encode($arrayResponseSftp, 128);
                    $arrayParametrosLog['inParameters']     = json_encode($arrayDataSftp, 128);
                    $arrayParametrosLog['creationUser']     = $arrayParametros['strUsrCreacion'];

                    $this->insertLog($arrayParametrosLog);
                    $boolRetorno = false;
                }
            }
            else if($arrayParametros['strMedioDescarga'] == 'SFTP_DIRECTO')
            {

                $objConexion = ssh2_connect($strHost, $this->strPortFtp);
                if (!$objConexion)
                {
                    throw new \Exception("No se pudo establecer conexión sFtp");
                }
                if (!ssh2_auth_password($objConexion, $strUsuario, $strPass))
                {
                    throw new \Exception("Password sFtp Incorrecto");
                }
                $objSftp = ssh2_sftp($objConexion);
                if (!$objSftp)
                {
                    throw new \Exception("error al inicializar ftp");
                }
                $objStream = fopen("ssh2.sftp://" . $objSftp . $strArchivoRemoto, 'r');
                if (!$objStream)
                {
                    throw new \Exception("No se pudo abrir el archivo " . $strArchivoRemoto);
                }
                $objContents = stream_get_contents($objStream);
                file_put_contents ($strArchivoDestino, $objContents);
                fclose($objStream);
                ssh2_exec($objConexion, 'exit');
                $objConexion = null;
            }
        }
        catch (\Exception $ex)
        {
            $arrayParametrosLog['enterpriseCode']   = $arrayParametros["strCodEmpresa"];
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['messageUser']      = "Error";
            $arrayParametrosLog['descriptionError'] = $ex->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayDataSftp, 128);
            $arrayParametrosLog['creationUser']     = $arrayParametros['strUsrCreacion'];
            $boolRetorno = false;
        }
        return $boolRetorno;
    }

    /**
     * Función que trae un archivo de un servidor sftp y lo almacena en el NFS
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 23-09-2020
     * @since 1.0
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.1 23-11-2020 - Se agrega lógica de tipo de descarga.
     * @param array $arrayParametros
     * @return array $arrayRespuesta
     */
    public function hasNfsRecibirArchivoSftp($arrayParametros)
    {
        $arrayRespuesta    = array();
        $strDirectorio     = ($arrayParametros['strDirRemoto']) ? $arrayParametros['strDirRemoto'] : $this->strDirRemoto;
        $strHost           = ($arrayParametros['strHost']) ? $arrayParametros['strHost'] : $this->strHostFtp;
        $strUsuario        = ($arrayParametros['strUsuario']) ? $arrayParametros['strUsuario'] : $this->strUsuarioFtp;
        $strPass           = ($arrayParametros['strPassword']) ? $arrayParametros['strPassword'] : $this->strPassFtp;

        $strArchivoRemoto  =  $strDirectorio . "/" . $arrayParametros['strArchivoRemoto'];
        try
        {
            if($arrayParametros['strMedioDescarga'] == 'SFTP_WS')
            {
                //Llamada al microservicio sftp para descarga de certificado
                $strPathTemporal = $arrayParametros['strIdentificacion'] . '/' . $arrayParametros['strDirDia'] . '/';

                $arrayDataSftp = array('fileList'   => array(array('fileSourcePathList'  => array(array("filename" => $strArchivoRemoto)),
                                                                   'fileDestinationPath' => $strPathTemporal)),
                                       'serverName' => $arrayParametros['strServidorRemoto']);

                $arrayRest[CURLOPT_TIMEOUT] = $this->intWsContratoDigitalTimeOut;

                $arrayResponse = $this->serviceRestClient->postJSON($this->strUrlWsSftp, json_encode($arrayDataSftp), $arrayRest);

                if($arrayResponse['result'])
                {
                    $arrayResponseSftp = json_decode($arrayResponse['result'], true);

                    if($arrayResponseSftp['code'] == 200 && count($arrayResponseSftp['fileContentList']) > 0)
                    {
                        $arrayFileContent = $arrayResponseSftp['fileContentList'][0];

                        if(count($arrayFileContent['content']) > 0)
                        {
                            $strCertificadoBase64 = $arrayFileContent['content'];
                        }
                    }
                    else
                    {
                        $arrayParametrosLog['enterpriseCode']   = $arrayParametros["strCodEmpresa"];
                        $arrayParametrosLog['logType']          = "1";
                        $arrayParametrosLog['logOrigin']        = "ms-core-gen-sftp";
                        $arrayParametrosLog['application']      = $arrayResponseSftp['origin'];
                        $arrayParametrosLog['appClass']         = $arrayResponseSftp['className'];
                        $arrayParametrosLog['appMethod']        = $arrayResponseSftp['methodName'];
                        $arrayParametrosLog['appAction']        = $arrayResponseSftp['methodName'];
                        $arrayParametrosLog['status']           = "Fallido";
                        $arrayParametrosLog['messageUser']      = $arrayResponseSftp['status'];
                        $arrayParametrosLog['descriptionError'] = $arrayResponseSftp['message'] . '\n\n' . json_encode($arrayResponse, 128);
                        $arrayParametrosLog['inParameters']     = json_encode($arrayDataSftp, 128);
                        $arrayParametrosLog['creationUser']     = $arrayParametros['strUsrCreacion'];

                        $this->insertLog($arrayParametrosLog);
                    }
                }
                else
                {
                    $arrayParametrosLog['enterpriseCode']   = $arrayParametros["strCodEmpresa"];
                    $arrayParametrosLog['logType']          = "1";
                    $arrayParametrosLog['logOrigin']        = "TELCOS";
                    $arrayParametrosLog['application']      = basename(__FILE__);
                    $arrayParametrosLog['appClass']         = basename(__CLASS__);
                    $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
                    $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
                    $arrayParametrosLog['status']           = "Fallido";
                    $arrayParametrosLog['messageUser']      = "Error";
                    $arrayParametrosLog['descriptionError'] = json_encode($arrayResponseSftp, 128);
                    $arrayParametrosLog['inParameters']     = json_encode($arrayDataSftp, 128);
                    $arrayParametrosLog['creationUser']     = $arrayParametros['strUsrCreacion'];

                    $this->insertLog($arrayParametrosLog);
                }
            }
            else if($arrayParametros['strMedioDescarga'] == 'SFTP_DIRECTO')
            {

                $objConexion = ssh2_connect($strHost, $this->strPortFtp);
                if (!$objConexion)
                {
                    throw new \Exception("No se pudo establecer conexión sFtp");
                }
                if (!ssh2_auth_password($objConexion, $strUsuario, $strPass))
                {
                    throw new \Exception("Password sFtp Incorrecto");
                }
                $objSftp = ssh2_sftp($objConexion);
                if (!$objSftp)
                {
                    throw new \Exception("error al inicializar ftp");
                }
                $objStream = fopen("ssh2.sftp://" . $objSftp . $strArchivoRemoto, 'r');
                if (!$objStream)
                {
                    throw new \Exception("No se pudo abrir el archivo " . $strArchivoRemoto);
                }
                $objContents            = stream_get_contents($objStream);
                $strCertificadoBase64   = base64_encode($objContents);
            }

            $strPrefijoEmpresa      = isset($arrayParametros['prefijoEmpresa']) ? $arrayParametros['prefijoEmpresa'] : 'MD';
            $strNombreApp           = !empty($arrayParametros['strApp']) ? $arrayParametros['strApp'] : 'TELCOS';
            $strIdentificacion      = is_object($arrayParametros['objPerEmpRol']->getPersonaId()) ?
                                        $arrayParametros['objPerEmpRol']->getPersonaId()->getIdentificacionCliente()
                                        : 'SIN_IDENTIFICACION';
            $arrayPathAdicional     = null;
            $arrayPathAdicional[]   = array('key' => $strIdentificacion);
            $objGestionDir          = $this->emGeneral
                                           ->getRepository('schemaBundle:AdmiGestionDirectorios')
                                           ->findOneBy(array('aplicacion'  => $strNombreApp,
                                                             'empresa'     => $strPrefijoEmpresa));
            if(!is_object($objGestionDir))
            {
                throw new \Exception('Error, no existe la configuración requerida para almacenar archivos de la aplicación'.$strNombreApp);
            }
            $arrayParamData[]       = array('codigoApp'     => $objGestionDir->getCodigoApp(),
                                            'codigoPath'    => $objGestionDir->getCodigoPath(),
                                            'fileBase64'    => $strCertificadoBase64,
                                            'nombreArchivo' => $arrayParametros['strArchivoRemoto'],
                                            'pathAdicional' => $arrayPathAdicional);
            $arrayParamDirectorio   = array('data'    => $arrayParamData,
                                            'op'      => 'guardarArchivo',
                                            'user'    => $arrayParametros['strUsrCreacion']);

            //Llamar al webService
            $arrayOptions      = array(CURLOPT_SSL_VERIFYPEER => false);
            $arrayResponse     = $this->serviceRestClient->postJSON($this->strMSnfs,
                                                                    json_encode($arrayParamDirectorio),
                                                                    $arrayOptions);

            if(isset($arrayResponse))
            {
                $arrayNFSResp = json_decode($arrayResponse['result'], 1);
                if($arrayNFSResp['code'] == 200)
                {
                    $arrayRespuesta    = array('intStatus'      => 200,
                                               'strMensaje'     => 'OK',
                                               'strUrlArchivo'  => $arrayNFSResp['data'][0]['pathFile']);
                }
                else
                {
                    throw new \Exception('No se pudo crear la firma en el servidor NFS');
                }
            }
            else
            {
                throw new \Exception('Error, no hay respuesta del WS para almacenar el documento');
            }
            fclose($objStream);
            ssh2_exec($objConexion, 'exit');
            $objConexion = null;
        }
        catch (Exception $ex)
        {
            $arrayRespuesta    = array('intStatus'  => 500,
                                       'strMensaje' => $ex->getMessage());
        }
        return $arrayRespuesta;
    }

    /**
     * Método para almacenar un archivo en el servidor NFS
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0
     * @since 23/09/2020
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.1 26-01-2021
     * Se agrega lógica para guardar log
     * 
     * @param array $arrayParametros['prefijoEmpresa'       String  Prefijo de empresa.
     *                               'strApp'               String  Nombre de la app.
     *                               'arrayPathAdicional'   Array   Array que arma el pathAdicional.
     *                               'strBase64'            String  Base64 del archivo.
     *                               'strNombreArchivo'     String  Nombre del archivo.
     *                               'strUsrCreacion'       String  Usuario de creación.
     *                              ]
     *
     * @return array $arrayRespuesta[
     *                               'intStatus'        String  Código de respuesta
     *                               'strMensaje'       String  Mensaje de respuesta
     *                               'strUrlArchivo'    String  Url donde se almaceno el archivo
     *                              ]
     */
    public function guardarArchivosNfs($arrayParametros)
    {
        $strPrefijoEmpresa      = isset($arrayParametros['prefijoEmpresa']) ? $arrayParametros['prefijoEmpresa'] : 'MD';
        $strNombreApp           = !empty($arrayParametros['strApp']) ? $arrayParametros['strApp'] : 'TELCOS';
        $strSubModulo           = !empty($arrayParametros['strSubModulo']) ? $arrayParametros['strSubModulo'] : 'TELCOS';
        $arrayTokenCas          = $this->serviceTokenCas->generarTokenCas();
        $arrayParametros['token'] = $arrayTokenCas['strToken'];

        $objGestionDir          = $this->emGeneral
                                        ->getRepository('schemaBundle:AdmiGestionDirectorios')
                                        ->findOneBy(array('aplicacion'  => $strNombreApp,
                                                          'subModulo'   => $strSubModulo,
                                                          'empresa'     => $strPrefijoEmpresa));
        if(!is_object($objGestionDir))
        {
            throw new \Exception('Error, no existe la configuración requerida para almacenar archivos de la aplicación'.$strNombreApp);
        }
        $arrayParamData[]       = array('codigoApp'     => $objGestionDir->getCodigoApp(),
                                        'codigoPath'    => $objGestionDir->getCodigoPath(),
                                        'fileBase64'    => $arrayParametros['strBase64'],
                                        'nombreArchivo' => $arrayParametros['strNombreArchivo'],
                                        'pathAdicional' => $arrayParametros['arrayPathAdicional']);
        $arrayParamDirectorio   = array('data'    => $arrayParamData,
                                        'op'      => 'guardarArchivo',
                                        'user'    => $arrayParametros['strUsrCreacion']);

        //Llamar al webService
        $arrayOptions      = array(CURLOPT_SSL_VERIFYPEER => false,
                                   CURLOPT_HTTPHEADER     => array(
                                        'Content-Type: application/json',
                                        'tokencas: ' . $arrayParametros['token']
                                    ));
        $arrayResponse     = $this->serviceRestClient->postJSON($this->strMSnfs,
                                                                json_encode($arrayParamDirectorio),
                                                                $arrayOptions);
        
        if(isset($arrayResponse))
        {
            $arrayNFSResp = json_decode($arrayResponse['result'], 1);
            if($arrayNFSResp['code'] == 200)
            {
                $arrayRespuesta    = array('intStatus'      => 200,
                                           'strMensaje'     => 'OK',
                                           'strUrlArchivo'  => $arrayNFSResp['data'][0]['pathFile'],
                                           'strFileName'    => $arrayNFSResp['data'][0]['nombreFile']);
            }
            else
            {
                $arrayParametrosLog['enterpriseCode']   = "10"; 
                $arrayParametrosLog['logType']          = "1";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['application']      = "TELCOS";
                $arrayParametrosLog['appClass']         = "UtilService";
                $arrayParametrosLog['appMethod']        = "guardarArchivosNfs";
                $arrayParametrosLog['messageUser']      = "No aplica.";
                $arrayParametrosLog['status']           = "Fallido";
                $arrayParametrosLog['descriptionError'] = json_encode($arrayNFSResp);
                $arrayParametrosLog['inParameters']     = json_encode($arrayParametros);
                $arrayParametrosLog['creationUser']     = "TELCOS";
    
                $this->insertLog($arrayParametrosLog);

                $arrayRespuesta    = array('intStatus'      => 500,
                                           'strMensaje'     => 'Error al momento de crear el archivo');
            }
        }
        else
        {
            $arrayRespuesta    = array('intStatus'      => 500,
                                       'strMensaje'     => 'Error, no hay respuesta del WS para almacenar el documento');
        }
        return $arrayRespuesta;
    }

    /**
     * Función que obtiene los valores de una estructura key->value configurada DB
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 26-05-2020
     * 
     * @param array $arrayParametros  [strStructure, strKey]
     * @return string $strValue
     * 
     */    
    public function getValueByStructure($arrayParametros)
    {
        $strStructure   = $arrayParametros['strStructure'];
        $strValue       = "";
        $strKey         = $arrayParametros['strKey'];

        $arrayValue = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
        ->getOne($strStructure, 
                 '', 
                 '', 
                 '', 
                 $strKey, 
                 '', 
                 '', 
                 ''
                 );

        if(is_array($arrayValue))
        {
            $strValue = !empty($arrayValue['valor2']) ? $arrayValue['valor2'] : "";
        }

        return $strValue;
    } 
    
    /**
     * Función que crea la ruta fisica donde se va a guardar un archivo
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 27-05-2020
     * 
     * @param array $arrayParametros
     * @return string $strRutaFisicaCompleta
     * 
     */    
    public function createNewFilePath($arrayParametros)
    {
        $strRutaBase               = $this->getValueByStructure(array('strStructure'  => 'PARAMETROS_ESTRUCTURA_RUTAS_TELCOS', 
                                                                   'strKey'        => 'RUTA_BASE'));
        $strRutaFisicaCompleta  =   $strRutaBase . 
                                    $arrayParametros['strCodigoPostal'] .'/'.
                                    $arrayParametros['strPrefijoEmpresa'] .'/'.
                                    date("Y") .'/'.
                                    date("m") .'/'.
                                    date("d") .'/'.
                                    $arrayParametros['strFolderApplication'] .'/';
        
        if($arrayParametros['strFolderApplication'] == 
            $this->getValueByStructure(array('strStructure'  => 'PARAMETROS_ESTRUCTURA_RUTAS_TELCOS', 
                                             'strKey'        => 'ec.telconet.mobile.telcos.operaciones')))
        {
            $strRutaFisicaCompleta  =   $strRutaFisicaCompleta . 
                                        $arrayParametros['strController'] .'/'.
                                        $arrayParametros['strOrigenAccion'] .'/';
        }

        if(strtoupper($arrayParametros['strExt']) === "JPG" || 
            strtoupper($arrayParametros['strExt']) === "JPEG" || 
            strtoupper($arrayParametros['strExt']) === "PNG")
        {
            $strRutaFisicaCompleta = $strRutaFisicaCompleta . 'imagenes';  
        }
        else
        {
            $strRutaFisicaCompleta = $strRutaFisicaCompleta . 'documentos';
        }

        return $strRutaFisicaCompleta;
    }

    /**
     * Función que retorna el nombre del directorio o archivo base del path enviado
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 25-07-2020
     *
     * @param string $strPath
     * @return string $strPath
     *
     */
    public function basename($strPath)
    {
        return !is_null($strPath) ? basename(str_replace("\\", "/", $strPath)) : "";
    }
    
   /**
     * Función que sirve para obtener el error de usuario que se debe devolver en base a errores parametrizados.
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.0 31-10-2020
     *  
     * @param Array $arrayData[  
     *              - mensaje               Mensaje de error original
     *              - mensajeDefault        Mensaje a devolver en caso de no mapear error original
     *              - nombreParametro       Nombre del parámetro que contine el mapeo de los errores 
     *              - empresaId             Id de la empresa 
     *              - user                  Usuario de la transacción
     *                        ]
     * @return array $arrayResultado
     */
    public function getMapeoMensajeError($arrayData)
    {   
        $strMensajeError                = $arrayData['mensaje'];
        $strMensajeDefault              = $arrayData['mensajeDefault'];
        $strNombreParametro             = $arrayData['nombreParametro'];
        $intEmpresaId                   = $arrayData['empresaId'];
        $strUsrCreacion                 = $arrayData['user'];
        $strMensajeRespuesta            = $strMensajeDefault;
        $strAccion                      = "Mapeo de mensajes de error";
        
        try
        {
            $arrayResultado                 = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getResultadoDetallesParametro($strNombreParametro,"","");
            
            $arrayAdmiParametrosDet         = $arrayResultado['registros'];
            if( isset($arrayAdmiParametrosDet)  && count($arrayAdmiParametrosDet) > 0)
            {
                
                foreach( $arrayAdmiParametrosDet as $arrayParametroDet )
                {
                    
                    if(strpos($strMensajeError, $arrayParametroDet["valor1"]) !== false
                       && $arrayParametroDet["valor3"] === 'SI')
                    {
                        $strMensajeRespuesta = $arrayParametroDet["valor2"];
                    }
                    if(strpos($strMensajeError, $arrayParametroDet["valor1"]) !== false
                       && $arrayParametroDet["valor3"] === 'NO')
                    {
                        $strMensajeRespuesta = $strMensajeError;
                    }        
                }
            }
        }
        catch(\Exception $e)
        {
            $this->insertLog(array(
                'enterpriseCode'   => $intEmpresaId,
                'logType'          => 1,
                'logOrigin'        => 'TELCOS',
                'application'      => 'TELCOS',
                'appClass'         => basename(__CLASS__),
                'appMethod'        => basename(__FUNCTION__),
                'appAction'        => $strAccion,
                'descriptionError' => "Error al intentar mapear errores para el parámetro ".$strNombreParametro." ".$e->getMessage(),
                'status'           => 'Fallido',
                'inParameters'     => json_encode($arrayData),
                'creationUser'     => $strUsrCreacion));

            return $arrayResultado;
        }
        
        return $strMensajeRespuesta;
    }






    /**
     * Función que  sirve como interceptor para evaluar el estado de una tarea y
     * permite verificar si lo deja avanzar o no ,usado para el TM OPERACIONES para
     * verificar si una tarea no ha sido finalizado por la web
     * 
     * @author Carlos Caguana Tenezaca <ccaguana@telconet.ec>
     * @version 1.0 30-10-2020
     *
     * @param Array $arrayData[  
     *              - idDetalle             id de la Tarea
     *              - op                    Nombre del servicio a Consumir              
     *              - user                  Usuario de la transacción
     * @return array $$arrayRespuesta
     *
     */
    public function estadoTarea($arrayData)
    {

        $strIdDetalle=$arrayData['data']['idDetalle'];
        $strUser= $arrayData['user'];
        $strOp=$arrayData['op'];


        $arrayTareas=null; 
        $boolRetornoOps=true;
        $arrayOpsByPass =$this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
        ->getOne('PARAMETROS_GENERALES_MOVIL', 
                 '', 
                 '', 
                 '', 
                 'OP_RESTRINGIDO_AL_VALIDAR_ESTADO_TAREA', 
                 '', 
                 '', 
                 ''
                );

        if(is_array($arrayOpsByPass))
        {
         $arrayOps= !empty($arrayOpsByPass['valor2']) ? $arrayOpsByPass['valor2'] : "";
        }      
        
        
        $arrayOps = explode(",",$arrayOps);
        foreach ($arrayOps as $strOps):
            if($strOps===$strOp)
            {

                $boolRetornoOps=false;
            }
        endforeach; 


        if(is_numeric($strIdDetalle) && $boolRetornoOps)
        {
            $arrayObtenerResultado = array("intIdTarea" =>$strIdDetalle);
            $arrayResultado = $this->serviceSoporte->getRepository('schemaBundle:InfoDetalleHistorial')->getUltimoDetHist($arrayObtenerResultado);
            $strEstado = $arrayResultado["estadoTarea"];
            $arrayUsuario = $this->serviceSoporte->getRepository('schemaBundle:InfoDetalleHistorial')->getUltimoDetUsuario($arrayObtenerResultado);
            $strEstado=strtoupper($strEstado);
            $strUsuario=$arrayUsuario['usuario'];
            $boolRetorno = false; 
    
            $arrayEstadosTareas =$this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne('PARAMETROS_GENERALES_MOVIL', 
                                                                     '', 
                                                                     '', 
                                                                     '', 
                                                                     'VALIDACION_ESTADO_TAREA', 
                                                                     '', 
                                                                     '', 
                                                                     ''
                                                                    );
             if(is_array($arrayEstadosTareas))
            {
             $arrayTareas= !empty($arrayEstadosTareas['valor2']) ? $arrayEstadosTareas['valor2'] : "";
            }
            $arrayTareas = explode(",",$arrayTareas);
            foreach ($arrayTareas as $strTarea):
                if($strTarea===$strEstado)
                {
                    $boolRetorno=true;
                }
            endforeach; 
            $arrayRespuesta['valorTarea']=$strEstado;
    
            if($strUsuario===$strUser)
            {
                $boolRetorno=false;
            }
    
    
            if(!empty($strEstado))
            {
              $arrayRespuesta['estadoTarea']=$boolRetorno;
            }else
            {
              $arrayRespuesta['estadoTarea']=false;
            }

        }
        else
        {
            $arrayRespuesta['valorTarea']="";
            $arrayRespuesta['estadoTarea']=false;

        }
       
        return  $arrayRespuesta;
    }



     /**
     * Función que  sirve para traer los parametros
     * 
     * @author Carlos Caguana Tenezaca <ccaguana@telconet.ec>
     * @version 1.0 08-08-2021
     *
     * @return array $valor
     *
     */
    public function getAdminParametroDet($strValor,$strDefecto)
    {

        $strRespuesta=$strDefecto;
        $arrayParametros =$this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
        ->getOne('PARAMETROS_GENERALES_MOVIL', 
                 '', 
                 '', 
                 '', 
                 $strValor, 
                 '', 
                 '', 
                 ''
                );

        if(is_array($arrayParametros))
        {
         $strRespuesta= !empty($arrayParametros['valor2']) ? $arrayParametros['valor2'] : "";
        } 
        
        
        if(empty( $strRespuesta))
        {
            $strRespuesta=$strDefecto;
        }
        

        return $strRespuesta;
    }      
    
    
     
    /**
    * Método para buscar archivos en el servidor NFS
    *
    * @author Carlos Caguana <ccaguana@telconet.ec>
    * @version 1.0
    * @since 19/03/2021
    *
    * 
    * @param array $arrayParametros['prefijoEmpresa'       String  Prefijo de empresa.
    *                               'strApp'               String  Nombre de la app.
    *                               'arrayPathAdicional'   Array   Array que arma el pathAdicional.
    *                               'strUsrCreacion'       String  Usuario de creación.
    *                              ]
    *
    * @return array $arrayRespuesta[
    *                               'intStatus'        String  Código de respuesta
    *                               'strMensaje'       String  Mensaje de respuesta
    *                               'arrayDatosArchivos'    String  Url donde se almaceno el archivo
    *                              ]
    */
    public function buscarArchivosReporteCarteraNfs($arrayParametros)
    {
        $strPrefijoEmpresa      = isset($arrayParametros['prefijoEmpresa']) ? $arrayParametros['prefijoEmpresa'] : 'MD';
        $strNombreApp           = !empty($arrayParametros['strApp']) ? $arrayParametros['strApp'] : 'TELCOS';
        $strSubModulo           = !empty($arrayParametros['strSubModulo']) ? $arrayParametros['strSubModulo'] : 'TELCOS';
 
        $objGestionDir          = $this->emGeneral
                                        ->getRepository('schemaBundle:AdmiGestionDirectorios')
                                        ->findOneBy(array('aplicacion'  => $strNombreApp,
                                                          'subModulo'   => $strSubModulo,
                                                          'empresa'     => $strPrefijoEmpresa));
        if(!is_object($objGestionDir))
        {
            throw new \Exception('Error, no existe la configuración requerida para consultar archivos de la aplicación'.$strNombreApp);
        }
        
        $arrayResultadoPath = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
        ->gePathReporteCartera($arrayParametros);

        if(!empty($arrayResultadoPath))
        {
            foreach($arrayResultadoPath  as $arrayPath)
            {
                
                $arrayPathAdicional[] = array('key' =>$arrayPath['path']);
    
                $arrayParamData[]       = array('codigoApp'     => $objGestionDir->getCodigoApp(),
                'codigoPath'    => $objGestionDir->getCodigoPath(),
                'fecha'         => $arrayParametros['strFecha'],
                'pathAdicional' => $arrayPathAdicional);
                $arrayPathAdicional=array();
            }  
        }
        else
        {
            $arrayPathAdicional=array();
            $arrayParamData[]       = array('codigoApp'     => $objGestionDir->getCodigoApp(),
            'codigoPath'    => $objGestionDir->getCodigoPath(),
            'fecha'         => $arrayParametros['strFecha'],
            'pathAdicional' => $arrayPathAdicional);
        }


       

        $arrayParamDirectorio   = array('data'    => $arrayParamData,
                                        'op'      => 'buscarArchivo',
                                        'user'    => $arrayParametros['strUsrCreacion']);
 
        //Llamar al webService
        $arrayOptions      = array(CURLOPT_SSL_VERIFYPEER => false);

        $arrayResponse     = $this->serviceRestClient->postJSON($this->strMSnfs,
                                                                json_encode($arrayParamDirectorio),
                                                                $arrayOptions);

        if(isset($arrayResponse))
        {
            $arrayNFSResp = json_decode($arrayResponse['result'], 1);
            if($arrayNFSResp['code'] == 200)
            {
                $arrayRespuesta    = array('intStatus'      => 200,
                                           'strMensaje'     => 'OK',
                                           'arrayDatosArchivos'  => $arrayNFSResp['data']);
            }
            else
            {
                $arrayParametrosLog['enterpriseCode']   = "10"; 
                $arrayParametrosLog['logType']          = "1";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['application']      = "TELCOS";
                $arrayParametrosLog['appClass']         = "UtilService";
                $arrayParametrosLog['appMethod']        = "buscarArchivosNfs";
                $arrayParametrosLog['messageUser']      = "No aplica.";
                $arrayParametrosLog['status']           = "Fallido";
                $arrayParametrosLog['descriptionError'] = json_encode($arrayNFSResp);
                $arrayParametrosLog['inParameters']     = json_encode($arrayParametros);
                $arrayParametrosLog['creationUser']     = "TELCOS";
    
                $this->insertLog($arrayParametrosLog);
 
                $arrayRespuesta    = array('intStatus'      => 500,
                                           'strMensaje'     => 'Error al momento de buscar el archivo');
            }
        }
        else
        {
            $arrayRespuesta    = array('intStatus'      => 500,
                                       'strMensaje'     => 'Error, no hay respuesta del WS para buscar los documento');
        }
          
        return $arrayRespuesta;
    }

    /**
    * Método para buscar archivos en el servidor NFS
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>
    * @version 1.0
    * @since 27/04/2021
    *
    * 
    * @param array $arrayParametros['prefijoEmpresa'       String  Prefijo de empresa.
    *                               'strApp'               String  Nombre de la app.
    *                               'arrayPathAdicional'   Array   Array que arma el pathAdicional.
    *                               'strUsrCreacion'       String  Usuario de creación.
    *                              ]
    *
    * @return array $arrayRespuesta[
    *                               'intStatus'        String  Código de respuesta
    *                               'strMensaje'       String  Mensaje de respuesta
    *                               'arrayDatosArchivos'    String  Url donde se almaceno el archivo
    *                              ]
    */
    public function buscarArchivosNfs($arrayParametros)
    {
        //Llamar al webService
        $arrayOptions      = array(CURLOPT_SSL_VERIFYPEER => false);

        $arrayResponse     = $this->serviceRestClient->postJSON($this->strMSnfs,
                                                                json_encode($arrayParametros),
                                                                $arrayOptions);

        if(isset($arrayResponse))
        {
            $arrayNFSResp = json_decode($arrayResponse['result'], 1);
            if($arrayNFSResp['code'] == 200)
            {
                $arrayRespuesta    = array('intStatus'      => 200,
                                           'strMensaje'     => 'OK',
                                           'arrayDatosArchivos'  => $arrayNFSResp['data']);
            }
            else
            {
                $arrayParametrosLog['enterpriseCode']   = "18"; 
                $arrayParametrosLog['logType']          = "1";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['application']      = "TELCOS";
                $arrayParametrosLog['appClass']         = "UtilService";
                $arrayParametrosLog['appMethod']        = "buscarArchivosNfs";
                $arrayParametrosLog['messageUser']      = "No aplica.";
                $arrayParametrosLog['status']           = "Fallido";
                $arrayParametrosLog['descriptionError'] = json_encode($arrayNFSResp);
                $arrayParametrosLog['inParameters']     = json_encode($arrayParametros);
                $arrayParametrosLog['creationUser']     = "TELCOS";
    
                $this->insertLog($arrayParametrosLog);
 
                $arrayRespuesta    = array('intStatus'      => 500,
                                           'strMensaje'     => 'Error al momento de buscar el archivo');
            }
        }
        else
        {
            $arrayRespuesta    = array('intStatus'      => 500,
                                       'strMensaje'     => 'Error, no hay respuesta del WS para buscar los documento');
        }
          
        return $arrayRespuesta;
    }

    /**
     * Función privada que recorre todos los registros obtenidos de la detalle parametros y devolvera la
     * lista depurada solo con los datos de sus campos valor[n], cuando se requiere una parametrizacion masiva
     *
     * @author Daniel Reyes Peñafiel <djreyes@telconet.ec>
     * @version 1.0 06-05-2021
     * 
     * @param array $arrayParametrosDet - Arreglo de registros de los detalles de parametros
     * 
     * @return $arrayValores - Listado depurado de datos parametrizados.
     *
     */
    public function obtenerValoresParametro($arrayParametrosDet)
    {
        $arrayValores = array();
        foreach($arrayParametrosDet as $parametroValor)
        {
            if ($parametroValor['valor1'] != null )
            {
                array_push($arrayValores, $parametroValor['valor1']);
            }
            if ($parametroValor['valor2'] != null )
            {
                array_push($arrayValores, $parametroValor['valor2']);
            }
            if ($parametroValor['valor3'] != null )
            {
                array_push($arrayValores, $parametroValor['valor3']);
            }
            if ($parametroValor['valor4'] != null )
            {
                array_push($arrayValores, $parametroValor['valor4']);
            }
            if ($parametroValor['valor5'] != null )
            {
                array_push($arrayValores, $parametroValor['valor5']);
            }
            if ($parametroValor['valor6'] != null )
            {
                array_push($arrayValores, $parametroValor['valor6']);
            }
            if ($parametroValor['valor7'] != null )
            {
                array_push($arrayValores, $parametroValor['valor7']);
            }
        }
        return $arrayValores;
    }

    /**
     * Método para almacenar bitacora de los cambios realizados en los datos del cliente.
     * Proyecto derechos del titular
     *
     * 
     * @author Jessenia Piloso <jpiloso@telconet.ec>
     * @version 1.0
     * @since 14/11/2022
     * 
     * @param array $arrayParamBitacora['strTipoIdentificacion'      String Tipo de identificacion del cliente
     *                                  'strIdentificacion'          String Identificacion del cliente
     *                                   'strNombres'                String Nombre del cliente
     *                                   'strApellidos'              String Apellidos del cliente
     *                                   'strGenero'                 String Genero del cliente
     *                                   'strOrigenIngresos'         String Origenes de ingreso del cliente
     *                                   'strRepresentanteLegal'     String Representante Legar del cliente
     *                                   'arrayDatosContactoPersona'   array Datos de Contacto del cliente
     *                                   'arrayDatosContactoPunto'     array Datos de Contacto del Punto del cliente
     *                                   'strFormaDePago'            String Forma de Pago del cliente
     *                                   'strUsuario'                String Usuario que realizo el cambio
     *                                   'strfechaHoraActualizacion' String Fecha y hora en la que se realizo el cambio
     *                                  ]
     *
     * @return array $arrayRespuesta[
     *                               'intStatus'        String  Código de respuesta
     *                               'strMensaje'       String  Mensaje de respuesta
     *                              ]
     */
    public function guardarBitacora($arrayParamBitacora)
    {
        
        $arrayTokenCas               = $this->serviceTokenCas->generarTokenCas();
        $strMetodo                   = '';
        $arrayParametrosMS           = array();
        $strIpCreacion               = ( isset($arrayParamBitacora['strIP']) && 
                                        !empty($arrayParamBitacora['strIP']) ) 
                                        ? $arrayParamBitacora['strIP'] : '127.0.0.1';

        if (isset($arrayParamBitacora['strMetodo']) && !empty($arrayParamBitacora['strMetodo']))
        {
            $strMetodo = $arrayParamBitacora['strMetodo'];
        }
        $arrayParametrosMS   = array('origen'       => 'Derechos del Titular',
                                'metodo'        => $strMetodo,
                                'tipoEvento'    => 'INFO',
                                'request'       => $arrayParamBitacora,
                                'ipEvento'      => $strIpCreacion,
                                'usuarioEvento'  => $arrayParamBitacora['strUsuario'],
                                'fechaEvento'    => $arrayParamBitacora['strfechaHoraActualizacion']);

        //Llamar al webService
        $arrayOptions      = array(CURLOPT_SSL_VERIFYPEER => false,
                                   CURLOPT_HTTPHEADER     => array(
                                        'Content-Type: application/json',
                                        'tokencas: ' . $arrayTokenCas['strToken']
                                    ));
       
        $arrayResponse     = $this->serviceRestClient->postJSON($this->strMSBitacora,
                                                                json_encode($arrayParametrosMS),
                                                                $arrayOptions);
        
      
        if(isset($arrayResponse))
        {
            $arrayMSBitacoraResp = json_decode($arrayResponse['result'], 1);
            if($arrayMSBitacoraResp['code'] === 0)
            {
                $arrayRespuesta    = array('intStatus'      => 0,
                                           'strMensaje'     => 'OK');
                  
            }
            else
            {
                $arrayParametrosLog['enterpriseCode']   = "18"; 
                $arrayParametrosLog['logType']          = "1";
                $arrayParametrosLog['logOrigin']        = "TELCOS";
                $arrayParametrosLog['application']      = "TELCOS";
                $arrayParametrosLog['appClass']         = "UtilService";
                $arrayParametrosLog['appMethod']        = "guardarMSBitacora";
                $arrayParametrosLog['messageUser']      = "No aplica.";
                $arrayParametrosLog['status']           = "Fallido";
                $arrayParametrosLog['descriptionError'] = $arrayResponse['error'];
                $arrayParametrosLog['inParameters']     = json_encode($arrayParametrosMS);
                $arrayParametrosLog['creationUser']     = "TELCOS";
    
                $this->insertLog($arrayParametrosLog);

                $arrayRespuesta    = array('intStatus'      => 500,
                                           'strMensaje'     => $arrayResponse['error']);
            }
        }
        else
        {
            $arrayRespuesta    = array('intStatus'      => 500,
                                       'strMensaje'     => 'Error, no hay respuesta del WS para guardar bitacora');
        }
        return $arrayRespuesta;
    }

    /**
     * obtenerTextoEntreCaracteres Función que obtiene el texto entre dos caracteres dados
     *
     * @author Diego Ernesto Guamán Eras <deguaman@telconet.ec>
     * @version 1.0 31/03/2023
     * 
     * @param array  $arrayParamTextoEntreCaracteres Array con parámetros de busqueda
     * 
     * @return string strResultado - Texto buscado
     *
     */
    public function obtenerTextoEntreCaracteres($arrayParamTextoEntreCaracteres)
    {

        $arrayDatos = $arrayParamTextoEntreCaracteres['arrayDatos'];
        $strValorBuscar = $arrayParamTextoEntreCaracteres['strValorBuscar'];
        $strCaracterInicio = $arrayParamTextoEntreCaracteres['strCaracterInicio'];
        $strCaracterFin = $arrayParamTextoEntreCaracteres['strCaracterFin'];

        $strResultado      = "";
        $strLineaAEvaluar  = "";
        for ($intIteration = 0; $intIteration < count($arrayDatos); $intIteration++)
        {
            if(strpos($arrayDatos[$intIteration], $strValorBuscar))
            {
                $strLineaAEvaluar = $arrayDatos[$intIteration];
            }
        }
        if(!empty($strLineaAEvaluar))
        {
            $strTexto = ' ' . $strLineaAEvaluar;
            $intInicio = strpos($strTexto, $strCaracterInicio);
            if ($intInicio == 0)
            {
              return '';
            }
            $intInicio += strlen($strCaracterInicio);
            $intTamanio = strpos($strTexto, $strCaracterFin, $intInicio) - $intInicio;
            $strResultado = substr($strTexto, $intInicio, $intTamanio);
        }

        return $strResultado;
    }


    /**
     * obtenerHtmlSeguimientoRegistroContactos Función que obtiene el texto HTML para ingreso de Registro de Contacto de Cliente
     *
     * @author Diego Ernesto Guamán Eras <deguaman@telconet.ec>
     * @version 1.0 31/03/2023
     * 
     * @param string $strSeguimiento - Se espera String en formato JSON con los registros de contacto de cliente
     * 
     * @return $strRespuesta - texto en html.
     *
     */
    public function obtenerHtmlSeguimientoRegistroContactos($strSeguimiento)
    {
        $strRespuesta = null;
        $objInfoBase = null;
        $objInfoTemp = null;
        $arrayInfoSeguimiento = json_decode($strSeguimiento, true);
        $strValorNoData = "NA";
        $strInfoBaseNombre = $strInfoBaseCelular = $strInfoBaseCargo = $strInfoBaseCorreo = $strInfoBaseConvencional = $strValorNoData;
        $strInfoTmpNombre = $strInfoTmpCelular = $strInfoTmpCargo = $strInfoTmpCorreo = $strInfoTmpConvencional = $strValorNoData;
        for ($intIteration = 0; $intIteration < count($arrayInfoSeguimiento); $intIteration++)
        {
            $objInfoSeguimientoActual = $arrayInfoSeguimiento[$intIteration];
            if (isset($objInfoSeguimientoActual['estado']) && !empty($objInfoSeguimientoActual['estado']) )
            {
                if(($objInfoSeguimientoActual["estado"] == "Base"))
                {
                    $objInfoBase = $objInfoSeguimientoActual;
                }else if(($objInfoSeguimientoActual["estado"] == "Temporal"))
                {
                    $objInfoTemp = $objInfoSeguimientoActual;
                }
            }
        }

        if (isset($objInfoTemp['observacion'])&&!empty($objInfoTemp['observacion'])) 
        {
            $strRespuesta = '<b>Datos Registros Contactos - Sin Registro</b><br>
                    <b>Observación: </b>' . $objInfoTemp['observacion'] . '<br>';
        } else if (!isset($objInfoBase) && isset($objInfoTemp))
        {
            $strInfoTmpNombre = $objInfoTemp['nombre'] == "" ? $strValorNoData : $objInfoTemp['nombre'];
            $strInfoTmpCelular = $objInfoTemp['celular'] == "" ? $strValorNoData : $objInfoTemp['celular'];
            $strInfoTmpCargo = $objInfoTemp['cargo'] == "" ? $strValorNoData : $objInfoTemp['cargo'];
            $strInfoTmpCorreo = $objInfoTemp['correo'] == "" ? $strValorNoData : $objInfoTemp['correo'];
            $strInfoTmpConvencional = ($objInfoTemp['convencional'] == "" || strlen($objInfoTemp['convencional']) < 9)
                                        ? $strValorNoData : $objInfoTemp['convencional'];
            $strRespuesta = '<b>Datos Registros Contactos</b><br>
                            <b>Nombre y Apellido: </b>' .         $strInfoTmpNombre .         '<br>
                            <b>Celular: </b>' .         $strInfoTmpCelular .        '<br>
                            <b>Cargo/Área: </b>' .    $strInfoTmpCargo .          '<br>
                            <b>Correo: </b>' .          $strInfoTmpCorreo .         '<br>
                            <b>Convencional: </b>' .    $strInfoTmpConvencional .   '<br>';
        } else if (isset($objInfoBase) && isset($objInfoTemp))
        {

            $strInfoBaseNombre          = $objInfoBase['nombre']        == "" ? $strValorNoData : $objInfoBase['nombre'];
            $strInfoBaseCelular         = $objInfoBase['celular']       == "" ? $strValorNoData : $objInfoBase['celular'];
            $strInfoBaseCargo           = $objInfoBase['cargo']         == "" ? $strValorNoData : $objInfoBase['cargo'];
            $strInfoBaseCorreo          = $objInfoBase['correo']        == "" ? $strValorNoData : $objInfoBase['correo'];
            $strInfoBaseConvencional    = $objInfoBase['convencional']  == "" ? $strValorNoData : $objInfoBase['convencional'];
            $strInfoBaseObservacion     = $objInfoBase['observacion']   == "" ? $strValorNoData : $objInfoBase['observacion'];
            $strInfoTmpNombre           = $objInfoTemp['nombre']        == "" ? $strValorNoData : $objInfoTemp['nombre'];
            $strInfoTmpCelular          = $objInfoTemp['celular']       == "" ? $strValorNoData : $objInfoTemp['celular'];
            $strInfoTmpCargo            = $objInfoTemp['cargo']         == "" ? $strValorNoData : $objInfoTemp['cargo'];
            $strInfoTmpCorreo           = $objInfoTemp['correo']        == "" ? $strValorNoData : $objInfoTemp['correo'];
            $strInfoTmpConvencional     = ($objInfoTemp['convencional']  == "" || strlen($objInfoTemp['convencional']) < 9) 
                                            ? $strValorNoData : $objInfoTemp['convencional'];

            if ($strValorNoData == $strInfoTmpNombre && $strValorNoData == $strInfoTmpCelular
                && $strValorNoData == $strInfoTmpCorreo && $strValorNoData == $strInfoTmpCargo
                && $strValorNoData == $strInfoTmpConvencional && $strInfoBaseObservacion == $strValorNoData)
            {
                $strRespuesta = '<b>Datos Registros Contactos - Sin Registro</b><br>
                                 <b>Observación: </b>'.$strValorNoData.' | Telcos<br>';
            } else if (
                ($strInfoBaseNombre != $strInfoTmpNombre || $strInfoBaseCelular != $strInfoTmpCelular
                || $strInfoBaseCorreo != $strInfoTmpCorreo || $strInfoBaseCargo != $strInfoTmpCargo
                || $strInfoBaseConvencional != $strInfoTmpConvencional) && $strInfoBaseObservacion != $strValorNoData.' | Telcos')
            {
                $strRespuesta = '<b>Datos Registros Contactos - Actualizados</b><br>
                                <b>Nombre y Apellido: </b>' .         $strInfoTmpNombre .         '<br>
                                <b>Celular: </b>' .         $strInfoTmpCelular .        '<br>
                                <b>Cargo/Área: </b>' .    $strInfoTmpCargo .          '<br>
                                <b>Correo: </b>' .          $strInfoTmpCorreo .         '<br>
                                <b>Convencional: </b>' .    $strInfoTmpConvencional .   '<br>';
            } else if (
                ($strInfoBaseNombre == $strInfoTmpNombre && $strInfoBaseCelular == $strInfoTmpCelular
                && $strInfoBaseCorreo == $strInfoTmpCorreo && $strInfoBaseCargo == $strInfoTmpCargo
                && $strInfoBaseConvencional == $strInfoTmpConvencional) && $strInfoBaseObservacion != $strValorNoData.' | Telcos')
            {
                $strRespuesta = 'IGUAL';
            }

        }
        return $strRespuesta;
    }

}
