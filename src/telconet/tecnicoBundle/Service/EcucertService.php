<?php

/* 
* To change this license header, choose License Headers in Project Properties.
* To change this template file, choose Tools | Templates
* and open the template in the editor.
*/

namespace telconet\tecnicoBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
* Clase RedAccesoCertService
*
* Clase que maneja las Transacciones realizadas a los WS de CERT y RDA en el módulo de Soporte.
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.0 25-03-2019
*/
class EcucertService
{
    /**
    *
    * @var \telconet\schemaBundle\Service\RestClientService
    */
    private $objRestClient;
    
    //Variable para CERT
    private $strUrlCertValidate;
    private $strUrlCertBlock;
    
    //Variable para Token
    public static $intStatusOk = 200; 
    public static $intStatusTokenError = 500; 
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emSoporte;
    
    public function setDependencies(Container $objContainer)
    {
        $this->strUrlCertBlock              = $objContainer->getParameter('ws_cert_ecucert_block_url');
        $this->strUrlCertValidate           = $objContainer->getParameter('ws_cert_ecucert_validate_url');
        $this->objRestClient                = $objContainer->get('schema.RestClient');
        $this->emSoporte                    = $objContainer->get('doctrine.orm.telconet_soporte_entity_manager');
        $this->objWSTokenGeneratorURL       = $objContainer->getParameter('seguridad.token_generate_url');
        $this->serviceUtil                  = $objContainer->get('schema.Util');
    }
    
    /**
     * estadoVulnerabilidadCert
     * Función que sirve para llamar y ejecutar el ws de estado de vulnerabilidad de CERT
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 25-03-2019
     * 
     * @param array $arrayDatosCert
     * [
     *      op              - Operación definida por CERT,
     *      token           - Token que valida CERT,
     *      data [  categoria       - Categoria de la incidencia
     *              subcategoria    - Sub categoria de la incidencia
     *              ip_address      - ip causante de la ]
     * ]
     * 
     * @return array $arrayResultado [
     *      strStatus  - Estado de la consulta
     *      strEstado  - Estado de la incidencia si sigue o no vulnerable
     *      strMensaje - Mensaje que indica si se realizó o no la consulta
     * ]
     */
    public function estadoVulnerabilidadCert($arrayDatosCert)
    {   
        $objOptions         = array(CURLOPT_SSL_VERIFYPEER => false);
        $strJsonData        = json_encode($arrayDatosCert);
        $arrayResponseJson  = $this->objRestClient->postJSON($this->strUrlCertValidate, $strJsonData , $objOptions);
        $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);
        if(isset($strJsonRespuesta['success']) && !empty($strJsonRespuesta['success']) && $strJsonRespuesta['success'] == "true")
        {   
            $arrayResponse = array('strStatus' => 'OK',
                                   'strEstado' => '',
                                   'strMensaje'=> $strJsonRespuesta['msg']);
            
            if(isset($strJsonRespuesta['vulnerable']) && !empty($strJsonRespuesta['vulnerable']) && $strJsonRespuesta['vulnerable']=='N')
            {
                $arrayResponse['strEstado'] = 'No Vulnerable';
            }
            else
            {
                $arrayResponse['strEstado'] = 'Vulnerable';
            }
            $arrayResultado = $arrayResponse;
            
        }
        else
        {
            $arrayResultado['strStatus']      = "ERROR";
            if($arrayResponseJson['status'] == "0")
            {
                $arrayResultado['strMensaje']  = "No Existe Conectividad con el WS CERT.";
            }
            else
            {
                $strMensajeError = 'ERROR';
                if(isset($strJsonRespuesta['errors']['msg']) && !empty($strJsonRespuesta['errors']['msg']))
                {
                    $strMensajeError = $strJsonRespuesta['errors']['msg'];
                }

                $arrayResultado['strMensaje']  = "ERROR:".$strMensajeError;
            }
        }

        return $arrayResultado ;
    }
    
    /**
     * contencionVulnerabilidadCert
     * Función que sirve para llamar y ejecutar el ws de contención de IP de CERT
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 25-03-2019
     * 
     * @param array $arrayDatosCert
     * [
     *      op              - Operación definida por CERT,
     *      token           - Token que valida CERT,
     *      data [  categoria       - Categoria de la incidencia
     *              subcategoria    - Sub categoria de la incidencia
     *              ip_address      - ip causante de la ]
     * ]
     * 
     * @return array $arrayResultado [
     *      strStatus  - Estado de la consulta
     *      strEstado  - Estado de la incidencia si se la bloqueó
     *      strMensaje - Mensaje que indica si se realizó o no la consulta
     * ]
     */
    public function contencionVulnerabilidadCert($arrayDatosCert)
    {
        $objOptions         = array(CURLOPT_SSL_VERIFYPEER => false);
        $strJsonData        = json_encode($arrayDatosCert);
        $arrayResponseJson  = $this->objRestClient->postJSON($this->strUrlCertBlock, $strJsonData , $objOptions);
        $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);
        if(isset($arrayResponseJson['status']) && !empty($arrayResponseJson['status']) && $arrayResponseJson['status'] == '200'
            && isset($strJsonRespuesta['success']) && !empty($strJsonRespuesta['success']) && $strJsonRespuesta['success'] == "true")
        {   
            $arrayResponse = array('strStatus' => 'OK',
                                   'strEstado' => 'Procesado',
                                   'strMensaje'=> $strJsonRespuesta['msg']);
            $arrayResultado = $arrayResponse;
        }
        else
        {
            $arrayResultado['strStatus']      = "ERROR";
            if($arrayResponseJson['status'] == "0")
            {
                $arrayResultado['strMensaje']  = "No Existe Conectividad con el WS CERT.";
            }
            else
            {
                $strMensajeError = 'ERROR';
                if(isset($strJsonRespuesta['errors']['msg']) && !empty($strJsonRespuesta['errors']['msg']))
                {
                    $strMensajeError = $strJsonRespuesta['errors']['msg'];
                }

                $arrayResultado['strMensaje']  = "ERROR:".$strMensajeError;
            }
        }

        return $arrayResultado ;
    }
    
    /**
     * reprocesarIncidenciaEcucert
     * Función que reprocesar la ip enviada por ECUCERT
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.0 18-06-2018
     * 
     * @param array $arrayParametros
     * [
     *     intIdIncidencia      => Id del detalle de la incidencia 
     *     strIpMod             => Usuario que modifica el estado de notificación del cliente
     *     strUserMod           => estado del ticket (creada, procesada)
     * ]
     * 
     * @return array $arrayResultado
     * [
     *     strMensaje           => Mensaje si se realizó o no el proceso
     *     strStatus            => Estado del proceso
     * ]
     */
    public function reprocesarIncidenciaEcucert($arrayParametros)
    {
        $intIdIncidencia        = $arrayParametros['intIdIncidencia'];
        $strIpMod               = $arrayParametros['strIpMod'];
        $strUserMod             = $arrayParametros['strUserMod'];
        $strMsjError            = str_repeat('a',  30*1024);

        try
        {
            $strSql = " BEGIN 
                            DB_SOPORTE.SPKG_INCIDENCIA_ECUCERT.P_REPROCESAR_CLIENTE(
                                                                                    :Pn_IncidenciaDetId,
                                                                                    :Pv_ipCreacion     ,
                                                                                    :Pv_user           ,
                                                                                    :Pv_MensajeError    ); 
                        COMMIT;
                        END;";
           
            $objStmt = $this->emSoporte->getConnection()->prepare($strSql);

            $objStmt->bindParam('Pn_IncidenciaDetId'    , $intIdIncidencia);
            $objStmt->bindParam('Pv_ipCreacion'         , $strIpMod);
            $objStmt->bindParam('Pv_user'               , $strUserMod);
            $objStmt->bindParam('Pv_MensajeError'       , $strMsjError);
            
            $objStmt->execute();
            
            if (strpos($strMsjError, 'ERROR') !== false)
            {
                $arrayRespuesta = array ('strMensaje'           =>"ERROR AL PROCESAR",
                                         'strStatus'            =>"ERROR");
            }
            else
            {
                $arrayRespuesta = array ('strMensaje'           =>$strMsjError,
                                         'strStatus'            =>"OK");
            }
           
        }
        catch(\Exception $e)
        {
            $strRespuesta   = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas".$e->getMessage();
            $arrayRespuesta = array ('strMensaje'     =>$strRespuesta);
            $this->serviceUtil->insertError('Telcos+',
                                            'EcucertService.ReprocesarIncidenciaEcucert',
                                            'Error SoporteService.ReprocesarIncidenciaEcucert:'.$e->getMessage(),
                                            $strUserMod,
                                            $strIpMod);
            return $arrayRespuesta;
        }
        return $arrayRespuesta;
    }
    
    /**
     * generateToken
     * Método encargado de obtener la información del token de seguridad de acuerdo a la APP.CERT registrada
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.0 - 15-07-2019
     * 
     * @param array $arrayJson
     * [
     *      user     => Usuario que solicita el token
     *      gateway  => Ruta para la generación de token
     *      service  => Servicio para generar el token
     *      method   => Método que genera el token
     *      source[
     *               name         => Nombre del aplicativo 
     *               originID     => Ip del usuario que ejecuta el Token
     *               tipoOriginID => Identidificar si una IP que se esta enviando en originID
     *            ]
     * ]
     * 
     * @return Array $arrayParametros [
     *      token   => token generado
     *      status  => status]
     */
    public function generateToken($arrayJson)
    {
        $strDataString = json_encode($arrayJson);
        $arrayOptions  = array(CURLOPT_SSL_VERIFYPEER => false);
        $arrayResponse = $this->objRestClient->postJSON($this->objWSTokenGeneratorURL, $strDataString, $arrayOptions);
        
        if ($arrayResponse['status'] == static::$intStatusOk)
        {
            // HTTP Status 200 OK - comunicacion correcta con servidor de tokens
            $arrayResult = json_decode($arrayResponse['result'], true);
            return $arrayResult;
        }
        else
        {           
            return array(
                'token' => null,'status' => static::$intStatusTokenError
            );
        }
    }
    
}
