<?php

namespace telconet\comercialBundle\Service;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use telconet\schemaBundle\Entity\AdmiProdCaracComp;

/**
 * Documentación de la clase InfoLogService
 * 
 * @author Edgar Holguín <eholguinn@telconet.ec>
 * @version 1.0
 *  
 */
class InfoLogService
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emGeneral;
    private $emComercial;
    private $emFinanciero;
    private $serviceUtil;
    private $strUrlGetLogs;
    private $serviceRestClient;
    private $strPostLogs;
    /**
     * setDependencies
     *
     * Función encargada de setear los entities manager de los esquemas de base de datos
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 05-01-2022
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $objContainer - objeto contenedor
     *
     */
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer )
    {
        $this->emComercial       = $objContainer->get('doctrine.orm.telconet_entity_manager');
        $this->emGeneral         = $objContainer->get('doctrine.orm.telconet_general_entity_manager');
        $this->emFinanciero      = $objContainer->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->strUrlGetLogs     = $objContainer->getParameter('ws_ms_listar_log_cliente');
        $this->strPostLogs       = $objContainer->getParameter('ws_ms_registrar_log_cliente');
        $this->serviceUtil       = $objContainer->get('schema.Util');
        $this->serviceRestClient = $objContainer->get('schema.RestClient');
        
    }  

    /** 
     * Función encargada de consultar logs .
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 05-01-2022
     * 
     * @param  array $arrayParametros
     * @throws Exception
     * @return $arrayResultado
     * 
     */
    public function consultarLogsMs($arrayParametros)
    {
        try
        {
            $objOptions         = array(CURLOPT_SSL_VERIFYPEER => false,
                                        CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                                                        'tokenCas: '.$arrayParametros['token'])
                                       ); 
            $strJsonData        = json_encode($arrayParametros);
            error_log($strJsonData);
            $arrayResponseJson  = $this->serviceRestClient->postJSON($this->strUrlGetLogs ,$strJsonData, $objOptions);           
            
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);
            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 
            && isset($strJsonRespuesta['status'])
            && isset($strJsonRespuesta['message']) )
            {   
                $arrayResponse = array('strStatus' => $strJsonRespuesta['code'],
                                       'strMensaje'=> $strJsonRespuesta['message'],
                                       'objData'=> $strJsonRespuesta['data']);
                $arrayResultado = $arrayResponse;
            }
            else
            {
                $arrayResultado['strStatus']      = "ERROR";
                if(empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['strMensaje']  = "No Existe Conectividad con el WS MS CORE LOGS.";
                }
                else
                {
                    $arrayResultado['strMensaje']  = $strJsonRespuesta['message'];
                }
            }

            return $arrayResultado ;
        }
        catch(\Exception $e)
        {
            $strRespuesta   = "Error al consultar logs. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('strMensaje'     =>$strRespuesta);
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoLogService.consultarLogsMs',
                                            'Error InfoLogService.consultarLogsMs:'.$e->getMessage(),
                                            $arrayParametros['strUser'],
                                            $arrayParametros['strIp']); 
            return $arrayResultado;
        }
    }
    
    
    /**
     * Funcion encargada de registrar logs
     * @author Christian Yunga <cyungat@telconet.ec>
     * @version 1.0 05-01-2022
     * 
     * @param array $arrayParametros
     * @throws Exception
     * @return $arrayResultado
     * 
     */

    public function registrarLogsMs($arrayParametros)
    {

        $arrayRepuesta     = array();
        $arrayValidaciones = array();
        $strJsonRespuesta  = '';
        $arrayDatosLog     = array();
        $arrayDatosLog['origen']        = $arrayParametros['strOrigen'];
        $arrayDatosLog['metodo']        = $arrayParametros['strMetodo'];
        $arrayDatosLog['request']       = $arrayParametros['request'];
        $arrayDatosLog['tipoEvento']    = $arrayParametros['strTipoEvento'];
        $arrayDatosLog['ipEvento']      = $arrayParametros['strIpUltMod'];
        $arrayDatosLog['usuarioEvento'] = $arrayParametros['strUsrUltMod'];
        $arrayDatosLog['fechaEvento']   = $arrayParametros['dateFechaEvento'];
        $arrayDatosLog['idKafka']       = $arrayParametros['strIdKafka'];
        $arrayDatosLog['response']      = $arrayParametros['response'];

        try 
        {

            $objOptions = array(CURLOPT_SSL_VERIFYPEER => false,
                                CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                                                'tokenCas: '.$arrayParametros
                                                                ['token']));
            
            $strJsonData        = json_encode($arrayDatosLog);
            $arrayResponseJson  = $this->serviceRestClient->postJSON($this->strPostLogs, $strJsonData, $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'], true);

            if (isset($strJsonRespuesta['code']) && ($strJsonRespuesta['code'] == 0)
            &&  isset($strJsonRespuesta['status']) && isset($strJsonRespuesta['message']))
            {
                $arrayResponse     = array('strStatus'  => $strJsonRespuesta['code'],
                                           'strMensaje' => $strJsonRespuesta['message']);
                $arrayRespuesta = $arrayResponse;
            }
            else 
            {
                $arrayValidaciones['strStatus'] = "ERROR";
                if (empty($strJsonRespuesta['message'])) 
                {
                    $arrayRepuesta[] = array('mensaje_validaciones' => "No existe conectividad con el MS CORE GEN LOG.");   
                } 
                else 
                {
                    $arrayRepuesta[] = array('mensaje_validaciones' => $strJsonRespuesta['message']);
                    
                }
            }
            return $arrayRespuesta;   
        } 
        catch (\Exception $e) 
        {
            $arrayRespuesta[] = array('mensaje_validaciones' => $e->getMessage());
            return $arrayRespuesta;
            
        }

    }    
    
}
