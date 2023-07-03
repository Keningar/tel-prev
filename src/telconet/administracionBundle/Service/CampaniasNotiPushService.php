<?php

namespace telconet\administracionBundle\Service;

/**
 * Clase CampaniasNotiPushService
 *
 * Clase que maneja funcionales necesarias para el bundle de administración de campanias
 *
 * @author Andrea Orellana <adorellana@telconet.ec>
 * @version 1.0 21-01-2023
 */    
class CampaniasNotiPushService 
{

     /**
     *
     * @var \telconet\schemaBundle\Service\RestClientService
     */
    private $serviceRestClient;
    private $utilService;
    private $strUrlCampaingMs;
    private $strUrlRedirigeMs;
    private $strUrlPropiedadesMs;
    private $strUrlEliminarCampaniaMs;
    private $strUrlEditarCampaniaMs;
    private $strUrlClonarCampaniaMs;
    private $strUrlCrearCampaniaMs;
    private $serviceTokenCas;
    
/**
     * Documentación para el método 'setDependencies'.
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $objContainer 
     * 
     *
     * @author Andrea Orellana  <adorellana@telconet.ec>
     * @version 1.0 21-01-2023
     *
     * @since 1.0
     */
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer) 
    {
        
        $this->utilService                     = $objContainer->get('schema.Util');
        $this->serviceRestClient               = $objContainer->get('schema.RestClient');
        $this->strUrlCampaingMs                = $objContainer->getParameter('ws_ms_consultarCampania_url');
        $this->strUrlRedirigeMs                = $objContainer->getParameter('ws_ms_Pantallas_url');
        $this->serviceTokenCas                 = $objContainer->get('seguridad.TokenCas');
        $this->strUrlPropiedadesMs             = $objContainer->getParameter('ws_ms_Propiedades_url');
        $this->strUrlEditarCampaniaMs          = $objContainer->getParameter('ws_ms_EditarCampania_url');
        $this->strUrlEliminarCampaniaMs        = $objContainer->getParameter('ws_ms_eliminarCampania_url');
        $this->strUrlClonarCampaniaMs          = $objContainer->getParameter('ws_ms_clonarCampania_url');
        $this->strUrlCrearCampaniaMs           = $objContainer->getParameter('ws_ms_crearCampania_url');
        
    } 
    /** 
     * Función que consulta campañas de microservicio
     * 
     * @author Andrea Orellana  <adorellana@telconet.ec>
     * @version 1.0 21-01-2023
     * 
     * @param 
     *         array $arrayParametros
     * @throws Exception
     * @return $arrayResponseJson
     * 
     */
    public function consultarCampaniasMs($arrayParametros)
    {      
        try
        {
             $arrayTokenCas= $this->serviceTokenCas->generarTokenCas();                                
             $arrayParametros['token'] = $arrayTokenCas['strToken'];

            $objOptions         = array(CURLOPT_SSL_VERIFYPEER => false,
                                        CURLOPT_HTTPHEADER     => array('tokencas: ' . $arrayParametros['token'])); 

            $objArrayReq=json_encode(array('creationUser'=>$arrayParametros['strUsrCreacion'],
                                                'companyCode'=>$arrayParametros['intIdEmpresa']));                                                    

            error_log("\n");
            error_log("========= Inicio - comunicacionWsRestClient ===========");
            error_log('===============    Request    ===================');
            error_log('Service: consultarCampaniasMs');
            error_log('Url: '.$this->strUrlCampaingMs);
            error_log('Json Request: '.$objArrayReq );
            error_log("\n");

            $arrayResponseJson  = $this->serviceRestClient->postJSON($this->strUrlCampaingMs,$objArrayReq , $objOptions );
            
            error_log('===============    Response    ===================');
            error_log('Json Response: '.json_encode($arrayResponseJson,true));
            error_log("\n");
            error_log("======== Fin - comunicacionWsRestClient ===========");
            error_log("\n");

            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);
          
            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 && isset($strJsonRespuesta['status'])
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
                    $arrayResultado['strMensaje']  = "";
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
            $strRespuesta   = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('strMensaje'     =>$strRespuesta);
            $this->utilService->insertError('Telcos+',
                                            'CampaniasNotiPushService.consultarCampaniasMs',
                                            'Error CampaniasNotiPushService.consultarCampaniasMs:'.$e->getMessage(),
                                            $arrayParametros['strUsrCreacion'],
                                            $arrayParametros['strIpCreacion']); 
            return $arrayResultado;
        }
    }

    /** 
     * Función que consulta las propiedades de una campaña
     * 
     * @author Andrea Orellana  <adorellana@telconet.ec>
     * @version 1.0 21-01-2023
     * 
     * @param 
     *         array $arrayParametros
     * @throws Exception
     * @return $arrayResponseJson
     * 
     */
    public function consultarPropiedadesMs($arrayParametros)
    {      
        try
        {
            $arrayTokenCas= $this->serviceTokenCas->generarTokenCas();                                
            $arrayParametros['token'] = $arrayTokenCas['strToken'];

           $objOptions         = array(CURLOPT_SSL_VERIFYPEER => false,
                                       CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                                                       'tokencas: ' . $arrayParametros['token'])); 

            $objJson            = array('status'=> $arrayParametros['status']);

            $objArrayReq    = json_encode($objJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);          

            error_log("\n");
            error_log("========= Inicio - comunicacionWsRestClient ===========");
            error_log('===============    Request    ===================');
            error_log('Url: '.$this->strUrlPropiedadesMs  );
            error_log('Json Request: '.$objArrayReq);
            error_log("\n");
           
            $arrayResponseJson  = $this->serviceRestClient->postJSON($this->strUrlPropiedadesMs ,$objArrayReq ,$objOptions);

            error_log('===============    Response    ===================');
            error_log('Json Response: '.json_encode($arrayResponseJson,true));
            error_log("\n");
            error_log("======== Fin - comunicacionWsRestClient ===========");
            error_log("\n");

            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);
          
            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 && isset($strJsonRespuesta['status'])
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
                    $arrayResultado['strMensaje']  = "Estimado usuario ha ocurrido un error durante el proceso, por favor comunicarse con sistemas.";
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
            $strRespuesta   = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('strMensaje'     =>$strRespuesta);
            $this->utilService->insertError('Telcos+',
                                            'CampaniasNotiPushService.consultarPropiedadesMs',
                                            'Error CampaniasNotiPushService.consultarPropiedadesMs:'.$e->getMessage(),
                                            $arrayParametros['strUsrCreacion'],
                                            $arrayParametros['strIpCreacion']); 
            return $arrayResultado;
        }
    }
    /** 
     * Función que permite la creacion de una campaña para notificaciones
     * 
     * @author Andrea Orellana  <adorellana@telconet.ec>
     * @version 1.0 24-01-2023
     * 
     * @param 
     *         array $arrayParametros
     * @throws Exception
     * @return $arrayResponseJson
     * 
     */
    public function crearCampaniaNotiPushMs($arrayParametros)
    {
        try 
        {
            $objCampAudito = array(
                'creationUser' => $arrayParametros["strUsrCreacion"],
                'companyCode' => $arrayParametros["intIdEmpresa"]
            );

            $objJson = array(
                'nameCampaing' => $arrayParametros["nameCampaing"],
                'status' => $arrayParametros["status"],
                'propertyVal' => array_values($arrayParametros["propertyVal"]),
                'dataSession' => $objCampAudito
            );

            $arrayTokenCas = $this->serviceTokenCas->generarTokenCas();
            $arrayParametros['token'] = $arrayTokenCas['strToken'];

            $objOptions = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $arrayParametros['token']
                )
            );

            $objArrayReq = json_encode($objJson, JSON_PRETTY_PRINT);

            error_log("\n");
            error_log("========= Inicio - comunicacionWsRestClient ===========");
            error_log('===============    Request    ===================');
            error_log('Service: crearCampaniaNotiPushMs');
            error_log('Url: '.$this->strUrlCrearCampaniaMs);
            error_log('Json Request: '.$objArrayReq );
            error_log("\n");

            $arrayResponseJson  = $this->serviceRestClient->postJSON($this->strUrlCrearCampaniaMs,$objArrayReq ,$objOptions);

            error_log('===============    Response    ===================');
            error_log('Json Response: '.json_encode($arrayResponseJson,true));
            error_log("\n");
            error_log("======== Fin - comunicacionWsRestClient ===========");
            error_log("\n");
           
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);
            error_log("code->".$strJsonRespuesta['code']);
            error_log("status->".$strJsonRespuesta['status']);

            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 && isset($strJsonRespuesta['status'])
                                                                                && isset($strJsonRespuesta['message']) )
            {   
                $arrayResponse = array('strStatus' => $strJsonRespuesta['status'],
                                       'strMensaje'=> $strJsonRespuesta['message'],
                                       'objData'=> $strJsonRespuesta['data']);
                $arrayResultado = $arrayResponse;
            }
            else
            {
                $arrayResultado['strStatus']  = "ERROR";
                if(empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['strMensaje']  = "Estimado usuario ha ocurrido un error durante el proceso, por favor comunicarse con sistemas.";
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
            error_log($e->getMessage());
            $strRespuesta   = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('strMensaje'     =>$strRespuesta);
            $this->utilService->insertError('Telcos+',
                                            'CampaniasNotiPushService.crearCampaniaNotiPushMs',
                                            'Error CampaniasNotiPushService.crearCampaniaNotiPushMs:'.$e->getMessage(),
                                            $arrayParametros['strUsrCreacion'],
                                            $arrayParametros['strIpCreacion']); 
            return $arrayResultado;
        }
    }

        /** 
     * Función que consulta 
     * 
     * @author Andrea Orellana  <adorellana@telconet.ec>
     * @version 1.0 24-01-2023
     * 
     * @param array $arrayParametros
     * 
     * @throws Exception
     * @return $arrayResponseJson
     * 
     */
    public function consultarPantallasAppMs($arrayParametros)
    {      
        try
        {
            $objJson = ['nameParameter'=> $arrayParametros['nombreParamCab'],
                        'status'=>$arrayParametros['estado']];
            $objArrayReq    = json_encode($objJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            $arrayTokenCas = $this->serviceTokenCas->generarTokenCas();
            $arrayParametros['token'] = $arrayTokenCas['strToken'];

            $objOptions = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $arrayParametros['token']
                )
            );

            error_log("\n");
            error_log("========= Inicio - comunicacionWsRestClient ===========");
            error_log('===============    Request    ===================');
            error_log('Service: consultarPantallasAppMs');
            error_log('Url: '.$this->strUrlRedirigeMs  );
            error_log('Json Request: '.$objArrayReq);
            error_log("\n");

           
            $arrayResponseJson  = $this->serviceRestClient->postJSON($this->strUrlRedirigeMs,$objArrayReq ,$objOptions);

            error_log('===============    Response    ===================');
            error_log('Json Response: '.json_encode($arrayResponseJson,true));
            error_log("\n");
            error_log("======== Fin - comunicacionWsRestClient ===========");
            error_log("\n");
                    
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);
          
            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 && isset($strJsonRespuesta['status'])
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
                    $arrayResultado['strMensaje']  = "Estimado usuario ha ocurrido un error durante el proceso, por favor comunicarse con sistemas.";
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
            $strRespuesta   = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('strMensaje'     =>$strRespuesta);
            $this->utilService->insertError('Telcos+',
                                            'CampaniasNotiPushService.consultarPantallasAppMs',
                                            'Error CampaniasNotiPushService.consultarPantallasAppMs:'.$e->getMessage(),
                                            $arrayParametros['strUsrCreacion'],
                                            $arrayParametros['strIpCreacion']); 
            return $arrayResultado;
        }
    }

    public function deleteCampaniasMs($arrayParametros)
    {      
        try
        {
            $objDataAuditoria = array( 'creationUser'=>$arrayParametros['strUsrCreacion'],
                                       'companyCode'=>$arrayParametros['strCodEmpresa']);
            $objJson = ['idsCampaing'=> $arrayParametros['arrayIdsCampanias'],
                        'status'=>$arrayParametros['strEstado'], 
                        'dataSession' => $objDataAuditoria];

            $objArrayReq    = json_encode($objJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $arrayTokenCas = $this->serviceTokenCas->generarTokenCas();
            $arrayParametros['token'] = $arrayTokenCas['strToken'];

            $objOptions = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $arrayParametros['token']
                )
            );

            error_log("\n");
            error_log("========= Inicio - comunicacionWsRestClient ===========");
            error_log('===============    Request    ===================');
            error_log('Service: deleteCampaniasMs');
            error_log('Url: '.$this->strUrlEliminarCampaniaMs );
            error_log('Json Request: '.$objArrayReq);
            error_log("\n");

            $arrayResponseJson  = $this->serviceRestClient->postJSON($this->strUrlEliminarCampaniaMs,$objArrayReq,$objOptions);

            error_log('===============    Response    ===================');
            error_log('Json Response: '.json_encode($arrayResponseJson,true));
            error_log("\n");
            error_log("======== Fin - comunicacionWsRestClient ===========");
            error_log("\n");
                    
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);
          
            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 && isset($strJsonRespuesta['status'])
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
                    $arrayResultado['strMensaje']  = "Estimado usuario ha ocurrido un error durante el proceso, por favor comunicarse con sistemas.";
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
            $strRespuesta   = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('strMensaje'     =>$strRespuesta);
            $this->utilService->insertError('Telcos+',
                                            'CampaniasNotiPushService.deleteCampaniasMs',
                                            'Error CampaniasNotiPushService.deleteCampaniasMs:'.$e->getMessage(),
                                            $arrayParametros['strUsrCreacion'],
                                            $arrayParametros['strIpCreacion']); 
            return $arrayResultado;
        }
    }

    public function clonarCampaniasMs($arrayParametros)
    {      
        try
        {
            $objDataAuditoria = array( 'creationUser'=>$arrayParametros['strUsrCreacion'],
                                       'companyCode'=>$arrayParametros['strCodEmpresa']);
            $objJson = ['idsCampaing'=> $arrayParametros['arrayIdsCampanias'],
                        'status'=>$arrayParametros['strEstado'], 
                        'dataSession' => $objDataAuditoria];

            $objArrayReq    = json_encode($objJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $arrayTokenCas = $this->serviceTokenCas->generarTokenCas();
            $arrayParametros['token'] = $arrayTokenCas['strToken'];

            $objOptions = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $arrayParametros['token']
                )
            );

            error_log("\n");
            error_log("========= Inicio - comunicacionWsRestClient ===========");
            error_log('===============    Request    ===================');
            error_log('Service: clonarCampaniasMs');
            error_log('Url: '.$this->strUrlClonarCampaniaMs );
            error_log('Json Request: '.$objArrayReq);
            error_log("\n");

            $arrayResponseJson  = $this->serviceRestClient->postJSON($this->strUrlClonarCampaniaMs,$objArrayReq ,$objOptions);

            error_log('===============    Response    ===================');
            error_log('Json Response: '.json_encode($arrayResponseJson,true));
            error_log("\n");
            error_log("======== Fin - comunicacionWsRestClient ===========");
            error_log("\n");
                    
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);
          
            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 && isset($strJsonRespuesta['status'])
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
                    $arrayResultado['strMensaje']  = "Estimado usuario ha ocurrido un error durante el proceso, por favor comunicarse con sistemas.";
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
            $strRespuesta   = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('strMensaje'     =>$strRespuesta);
            $this->utilService->insertError('Telcos+',
                                            'CampaniasNotiPushService.clonarCampaniasMs',
                                            'Error CampaniasNotiPushService.clonarCampaniasMs:'.$e->getMessage(),
                                            $arrayParametros['strUsrCreacion'],
                                            $arrayParametros['strIpCreacion']); 
            return $arrayResultado;
        }
    }

    public function editarCampaniaNotiPushMs($arrayParametros)
    {
        try
        {
            $objCampAudito = array('creationUser'=> $arrayParametros["strUsrCreacion"],
                                 'companyCode' => $arrayParametros["strPrefijoEmpresa"]);

            $objJson = array('idCampaing' => $arrayParametros["idCampaing"],
                            'nameCampaing'=> $arrayParametros["nameCampaing"],
                            'status'       => $arrayParametros["status"], 
                            'propertyVal'  => array_values($arrayParametros["propertyVal"]),
                            'dataSession'  => $objCampAudito);

            $arrayTokenCas = $this->serviceTokenCas->generarTokenCas();
            $arrayParametros['token'] = $arrayTokenCas['strToken'];

            $objOptions = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $arrayParametros['token']
                )
            );

            $objArrayReq = json_encode($objJson);

            error_log("\n");
            error_log("========= Inicio - comunicacionWsRestClient ===========");
            error_log('===============    Request    ===================');
            error_log('Url: '.$this->strUrlEditarCampaniaMs );
            error_log('Json Request: '.$objArrayReq);
            error_log("\n");

            $arrayResponseJson  = $this->serviceRestClient->postJSON($this->strUrlEditarCampaniaMs,$objArrayReq ,$objOptions);

            error_log('===============    Response    ===================');
            error_log('Json Response: '.json_encode($arrayResponseJson,true));
            error_log("\n");
            error_log("======== Fin - comunicacionWsRestClient ===========");
            error_log("\n");

            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);
          
            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 && isset($strJsonRespuesta['status'])
                                                                                && isset($strJsonRespuesta['message']) )
            {   
                $arrayResponse = array('strStatus' => $strJsonRespuesta['status'],
                                       'strMensaje'=> $strJsonRespuesta['message'],
                                       'objData'=> $strJsonRespuesta['data']);
                $arrayResultado = $arrayResponse;
            }
            else
            {
                $arrayResultado['strStatus']  = "ERROR";
                if(empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['strMensaje']  = "Estimado usuario ha ocurrido un error durante el proceso, por favor comunicarse con sistemas.";
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
            $strRespuesta   = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('strMensaje' =>$strRespuesta);
            $this->utilService->insertError('Telcos+',
                                            'CampaniasNotiPushService.editarCampaniaNotiPushMs',
                                            'Error CampaniasNotiPushService.editarCampaniaNotiPushMs:'.$e->getMessage(),
                                            $arrayParametros['strUsrCreacion'],
                                            $arrayParametros['strIpCreacion']); 
            return $arrayResultado;
        }
    }
     
}
